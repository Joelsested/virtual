-- Migracao: alunos unificados no provao_base (cadastro unico).
-- Requer permissoes de CREATE/ALTER/DROP/VIEW em provao_base, virtual_base e sestedcursos_base.
-- Antes de rodar, faca backup dos 3 bancos.

SET SESSION group_concat_max_len = 100000;

-- 1) Tabela de responsaveis por sistema/compra
CREATE TABLE IF NOT EXISTS provao_base.alunos_responsaveis (
  id INT NOT NULL AUTO_INCREMENT,
  aluno_id INT NOT NULL,
  sistema_id INT NOT NULL,
  usuario_id INT NOT NULL,
  matricula_id INT DEFAULT NULL,
  data DATE NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY idx_aluno_sistema (aluno_id, sistema_id),
  KEY idx_usuario (usuario_id),
  KEY idx_matricula (matricula_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Staging com chave (CPF ou email)
DROP TABLE IF EXISTS provao_base.alunos_staging;
CREATE TABLE provao_base.alunos_staging LIKE provao_base.alunos;
ALTER TABLE provao_base.alunos_staging
  ADD COLUMN origem VARCHAR(20) NOT NULL,
  ADD COLUMN origem_id INT NOT NULL,
  ADD COLUMN chave VARCHAR(80) NOT NULL,
  ADD KEY idx_chave (chave),
  ADD KEY idx_origem (origem, origem_id);

-- 2.1) Garantir coluna orgao_expedidor nas bases satelites
SET @has_orgao_virtual := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'virtual_base'
    AND TABLE_NAME = 'alunos'
    AND COLUMN_NAME = 'orgao_expedidor'
);
SET @sql_orgao_virtual := IF(
  @has_orgao_virtual = 0,
  'ALTER TABLE virtual_base.alunos ADD COLUMN orgao_expedidor VARCHAR(60) DEFAULT ""',
  'SELECT 1'
);
PREPARE stmt_orgao_virtual FROM @sql_orgao_virtual;
EXECUTE stmt_orgao_virtual;
DEALLOCATE PREPARE stmt_orgao_virtual;

SET @has_orgao_sested := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'sestedcursos_base'
    AND TABLE_NAME = 'alunos'
    AND COLUMN_NAME = 'orgao_expedidor'
);
SET @sql_orgao_sested := IF(
  @has_orgao_sested = 0,
  'ALTER TABLE sestedcursos_base.alunos ADD COLUMN orgao_expedidor VARCHAR(60) DEFAULT ""',
  'SELECT 1'
);
PREPARE stmt_orgao_sested FROM @sql_orgao_sested;
EXECUTE stmt_orgao_sested;
DEALLOCATE PREPARE stmt_orgao_sested;



-- Helper de chave: CPF sem pontuacao, senao email
-- Prov ao
INSERT INTO provao_base.alunos_staging
  (id, nome, cpf, email, telefone, rg, expedicao, nascimento, cep, sexo, endereco, numero, bairro,
   cidade, estado, mae, pai, naturalidade, foto, data, cartao, ativo, arquivo, usuario, orgao_expedidor,
   origem, origem_id, chave)
SELECT a.id, a.nome, a.cpf, a.email, a.telefone, a.rg, a.expedicao, a.nascimento, a.cep, a.sexo, a.endereco,
       a.numero, a.bairro, a.cidade, a.estado, a.mae, a.pai, a.naturalidade, a.foto, a.data, a.cartao,
       a.ativo, a.arquivo, a.usuario, a.orgao_expedidor,
       'provao', a.id,
       LOWER(CASE
         WHEN a.cpf IS NOT NULL AND REPLACE(REPLACE(REPLACE(a.cpf, '.', ''), '-', ''), ' ', '') <> ''
           THEN REPLACE(REPLACE(REPLACE(a.cpf, '.', ''), '-', ''), ' ', '')
         ELSE a.email
       END) AS chave
FROM provao_base.alunos a;

-- Virtual
INSERT INTO provao_base.alunos_staging
  (id, nome, cpf, email, telefone, rg, expedicao, nascimento, cep, sexo, endereco, numero, bairro,
   cidade, estado, mae, pai, naturalidade, foto, data, cartao, ativo, arquivo, usuario, orgao_expedidor,
   origem, origem_id, chave)
SELECT NULL AS id, a.nome, a.cpf, a.email, a.telefone, a.rg, a.expedicao, a.nascimento, a.cep, a.sexo, a.endereco,
       a.numero, a.bairro, a.cidade, a.estado, a.mae, a.pai, a.naturalidade, a.foto, a.data, a.cartao,
       a.ativo, a.arquivo, a.usuario, a.orgao_expedidor,
       'virtual', a.id,
       LOWER(CASE
         WHEN a.cpf IS NOT NULL AND REPLACE(REPLACE(REPLACE(a.cpf, '.', ''), '-', ''), ' ', '') <> ''
           THEN REPLACE(REPLACE(REPLACE(a.cpf, '.', ''), '-', ''), ' ', '')
         ELSE a.email
       END) AS chave
FROM virtual_base.alunos a;

-- Sestedcursos
INSERT INTO provao_base.alunos_staging
  (id, nome, cpf, email, telefone, rg, expedicao, nascimento, cep, sexo, endereco, numero, bairro,
   cidade, estado, mae, pai, naturalidade, foto, data, cartao, ativo, arquivo, usuario, orgao_expedidor,
   origem, origem_id, chave)
SELECT NULL AS id, a.nome, a.cpf, a.email, a.telefone, a.rg, a.expedicao, a.nascimento, a.cep, a.sexo, a.endereco,
       a.numero, a.bairro, a.cidade, a.estado, a.mae, a.pai, a.naturalidade, a.foto, a.data, a.cartao,
       a.ativo, a.arquivo, a.usuario, a.orgao_expedidor,
       'sestedcursos', a.id,
       LOWER(CASE
         WHEN a.cpf IS NOT NULL AND REPLACE(REPLACE(REPLACE(a.cpf, '.', ''), '-', ''), ' ', '') <> ''
           THEN REPLACE(REPLACE(REPLACE(a.cpf, '.', ''), '-', ''), ' ', '')
         ELSE a.email
       END) AS chave
FROM sestedcursos_base.alunos a;

-- 3) Gerar alunos centralizados (mais antigo + complemento)
DROP TABLE IF EXISTS provao_base.alunos_central;
CREATE TABLE provao_base.alunos_central LIKE provao_base.alunos;
ALTER TABLE provao_base.alunos_central
  ADD COLUMN chave VARCHAR(80) NOT NULL,
  ADD KEY idx_chave (chave);

INSERT INTO provao_base.alunos_central
  (nome, cpf, email, telefone, rg, expedicao, nascimento, cep, sexo, endereco, numero, bairro,
   cidade, estado, mae, pai, naturalidade, foto, data, cartao, ativo, arquivo, usuario, orgao_expedidor, chave)
SELECT
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(nome, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS nome,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(cpf, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS cpf,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(email, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS email,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(telefone, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS telefone,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(rg, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS rg,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(expedicao, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS expedicao,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(nascimento, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS nascimento,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(cep, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS cep,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(sexo, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS sexo,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(endereco, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS endereco,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(numero, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS numero,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(bairro, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS bairro,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(cidade, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS cidade,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(estado, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS estado,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(mae, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS mae,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(pai, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS pai,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(naturalidade, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS naturalidade,
  COALESCE(SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(foto, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1), 'sem-perfil.jpg') AS foto,
  MIN(data_ordem) AS data,
  COALESCE(SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(cartao, 0) ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1), 0) AS cartao,
  COALESCE(SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(ativo, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1), 'Sim') AS ativo,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(arquivo, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS arquivo,
  COALESCE(SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(usuario, 0) ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1), 0) AS usuario,
  SUBSTRING_INDEX(GROUP_CONCAT(NULLIF(orgao_expedidor, '') ORDER BY data_ordem ASC SEPARATOR '||'), '||', 1) AS orgao_expedidor,
  chave
FROM (
  SELECT *, COALESCE(data, '1970-01-01') AS data_ordem
  FROM provao_base.alunos_staging
) s
GROUP BY chave;

-- 4) Mapear IDs antigos para o novo aluno_id
DROP TABLE IF EXISTS provao_base.alunos_map;
CREATE TABLE provao_base.alunos_map (
  origem VARCHAR(20) NOT NULL,
  origem_id INT NOT NULL,
  aluno_id INT NOT NULL,
  chave VARCHAR(80) NOT NULL,
  PRIMARY KEY (origem, origem_id),
  KEY idx_aluno (aluno_id),
  KEY idx_chave (chave)
);

INSERT INTO provao_base.alunos_map (origem, origem_id, aluno_id, chave)
SELECT s.origem, s.origem_id, c.id, s.chave
FROM provao_base.alunos_staging s
JOIN provao_base.alunos_central c ON c.chave = s.chave;

-- 5) Atualizar usuarios.id_pessoa para alunos
UPDATE provao_base.usuarios u
JOIN provao_base.alunos_central a
  ON LOWER(CASE
    WHEN u.cpf IS NOT NULL AND REPLACE(REPLACE(REPLACE(u.cpf, '.', ''), '-', ''), ' ', '') <> ''
      THEN REPLACE(REPLACE(REPLACE(u.cpf, '.', ''), '-', ''), ' ', '')
    ELSE u.usuario
  END) = a.chave
SET u.id_pessoa = a.id
WHERE u.nivel = 'Aluno';

-- 6) Atualizar arquivos_alunos com novo aluno_id
UPDATE provao_base.arquivos_alunos aa
JOIN provao_base.alunos_map map ON map.origem = 'provao' AND map.origem_id = aa.aluno
SET aa.aluno = map.aluno_id;

UPDATE virtual_base.arquivos_alunos aa
JOIN provao_base.alunos_map map ON map.origem = 'virtual' AND map.origem_id = aa.aluno
SET aa.aluno = map.aluno_id;

UPDATE sestedcursos_base.arquivos_alunos aa
JOIN provao_base.alunos_map map ON map.origem = 'sestedcursos' AND map.origem_id = aa.aluno
SET aa.aluno = map.aluno_id;

-- 7) Criar responsaveis por sistema (a partir do cadastro antigo)
INSERT INTO provao_base.alunos_responsaveis
  (aluno_id, sistema_id, usuario_id, matricula_id, data, ativo)
SELECT map.aluno_id,
       CASE s.origem
         WHEN 'virtual' THEN 1
         WHEN 'provao' THEN 2
         WHEN 'sestedcursos' THEN 3
       END AS sistema_id,
       s.usuario,
       NULL,
       COALESCE(s.data, CURDATE()),
       1
FROM provao_base.alunos_staging s
JOIN provao_base.alunos_map map ON map.origem = s.origem AND map.origem_id = s.origem_id
WHERE s.usuario IS NOT NULL AND s.usuario <> 0;

-- 8) Criar acessos dos alunos por sistema (baseado no cadastro)
SET @has_acesso := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'provao_base'
    AND TABLE_NAME = 'usuarios_acessos'
    AND COLUMN_NAME = 'perfil'
);
SET @sql_perfil := IF(
  @has_acesso = 0,
  'ALTER TABLE provao_base.usuarios_acessos ADD COLUMN perfil VARCHAR(30) NOT NULL DEFAULT \"Aluno\" AFTER sistema_id',
  'SELECT 1'
);
PREPARE stmt_perfil FROM @sql_perfil;
EXECUTE stmt_perfil;
DEALLOCATE PREPARE stmt_perfil;

INSERT INTO provao_base.usuarios_acessos (usuario_id, sistema_id, perfil, ativo)
SELECT u.id,
       s.sistema_id,
       'Aluno',
       1
FROM provao_base.usuarios u
JOIN provao_base.alunos a ON a.id = u.id_pessoa
JOIN (
  SELECT DISTINCT map.aluno_id,
    CASE map.origem
      WHEN 'virtual' THEN 1
      WHEN 'provao' THEN 2
      WHEN 'sestedcursos' THEN 3
    END AS sistema_id
  FROM provao_base.alunos_map map
) s ON s.aluno_id = a.id
WHERE u.nivel = 'Aluno'
ON DUPLICATE KEY UPDATE perfil = VALUES(perfil), ativo = VALUES(ativo);

-- 9) Substituir alunos por VIEW nas bases satelites
DROP TABLE IF EXISTS virtual_base.alunos;
DROP VIEW IF EXISTS virtual_base.alunos;
CREATE VIEW virtual_base.alunos AS SELECT * FROM provao_base.alunos;

DROP TABLE IF EXISTS sestedcursos_base.alunos;
DROP VIEW IF EXISTS sestedcursos_base.alunos;
CREATE VIEW sestedcursos_base.alunos AS SELECT * FROM provao_base.alunos;

DROP TABLE IF EXISTS virtual_base.alunos_responsaveis;
DROP VIEW IF EXISTS virtual_base.alunos_responsaveis;
CREATE VIEW virtual_base.alunos_responsaveis AS SELECT * FROM provao_base.alunos_responsaveis;

DROP TABLE IF EXISTS sestedcursos_base.alunos_responsaveis;
DROP VIEW IF EXISTS sestedcursos_base.alunos_responsaveis;
CREATE VIEW sestedcursos_base.alunos_responsaveis AS SELECT * FROM provao_base.alunos_responsaveis;

-- 10) Trocar tabela central (mantem backup simples)
ALTER TABLE provao_base.alunos_central DROP COLUMN chave;
DROP TABLE IF EXISTS provao_base.alunos_backup;
RENAME TABLE provao_base.alunos TO provao_base.alunos_backup,
             provao_base.alunos_central TO provao_base.alunos;

-- Limpeza opcional
DROP TABLE IF EXISTS provao_base.alunos_staging;
DROP TABLE IF EXISTS provao_base.alunos_map;

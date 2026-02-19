-- Atualiza bancos reais: usuarios_comissoes + acessos/comissoes de vendedores.
-- Ajuste os IDs dos sistemas conforme seu padrao.

-- 1) Base principal: tabela usuarios_comissoes
CREATE TABLE IF NOT EXISTS provao_base.usuarios_comissoes (
  id INT NOT NULL AUTO_INCREMENT,
  usuario_id INT NOT NULL,
  sistema_id INT NOT NULL,
  nivel VARCHAR(30) NOT NULL,
  percentual DECIMAL(10,2) NOT NULL DEFAULT 0,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  data DATE NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_usuario_sistema_nivel (usuario_id, sistema_id, nivel),
  KEY idx_sistema_nivel (sistema_id, nivel),
  KEY idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Views para bases satelites (dados centralizados em provao_base)
DROP TABLE IF EXISTS virtual_base.usuarios_comissoes;
DROP VIEW IF EXISTS virtual_base.usuarios_comissoes;
CREATE VIEW virtual_base.usuarios_comissoes AS
  SELECT * FROM provao_base.usuarios_comissoes;

DROP TABLE IF EXISTS sestedcursos_base.usuarios_comissoes;
DROP VIEW IF EXISTS sestedcursos_base.usuarios_comissoes;
CREATE VIEW sestedcursos_base.usuarios_comissoes AS
  SELECT * FROM provao_base.usuarios_comissoes;

-- 2.1) Garantir tabela/coluna de acessos
CREATE TABLE IF NOT EXISTS provao_base.usuarios_acessos (
  usuario_id INT NOT NULL,
  sistema_id INT NOT NULL,
  perfil VARCHAR(30) NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (usuario_id, sistema_id),
  KEY idx_usuarios_acessos_sistema (sistema_id)
);

SET @has_perfil := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'provao_base'
    AND TABLE_NAME = 'usuarios_acessos'
    AND COLUMN_NAME = 'perfil'
);
SET @sql_perfil := IF(
  @has_perfil = 0,
  'ALTER TABLE provao_base.usuarios_acessos ADD COLUMN perfil VARCHAR(30) NOT NULL DEFAULT \"Vendedor\" AFTER sistema_id',
  'SELECT 1'
);
PREPARE stmt_perfil FROM @sql_perfil;
EXECUTE stmt_perfil;
DEALLOCATE PREPARE stmt_perfil;

-- Remover duplicados antigos (se a tabela ja existia sem chave unica e tem coluna id)
SET @has_id := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'provao_base'
    AND TABLE_NAME = 'usuarios_acessos'
    AND COLUMN_NAME = 'id'
);
SET @sql_dedupe := IF(
  @has_id = 1,
  'DELETE a FROM provao_base.usuarios_acessos a JOIN provao_base.usuarios_acessos b ON a.usuario_id = b.usuario_id AND a.sistema_id = b.sistema_id AND a.id > b.id',
  'SELECT 1'
);
PREPARE stmt_dedupe FROM @sql_dedupe;
EXECUTE stmt_dedupe;
DEALLOCATE PREPARE stmt_dedupe;

-- Garantir chave unica (usuario_id, sistema_id) em tabelas antigas
SET @has_unique := (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = 'provao_base'
    AND TABLE_NAME = 'usuarios_acessos'
    AND INDEX_NAME = 'uniq_usuario_sistema'
);
SET @sql_unique := IF(
  @has_unique = 0,
  'ALTER TABLE provao_base.usuarios_acessos ADD UNIQUE KEY uniq_usuario_sistema (usuario_id, sistema_id)',
  'SELECT 1'
);
PREPARE stmt_unique FROM @sql_unique;
EXECUTE stmt_unique;
DEALLOCATE PREPARE stmt_unique;

-- 2.2) Responsaveis por sistema (alunos)
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

DROP TABLE IF EXISTS virtual_base.alunos_responsaveis;
DROP VIEW IF EXISTS virtual_base.alunos_responsaveis;
CREATE VIEW virtual_base.alunos_responsaveis AS
  SELECT * FROM provao_base.alunos_responsaveis;

DROP TABLE IF EXISTS sestedcursos_base.alunos_responsaveis;
DROP VIEW IF EXISTS sestedcursos_base.alunos_responsaveis;
CREATE VIEW sestedcursos_base.alunos_responsaveis AS
  SELECT * FROM provao_base.alunos_responsaveis;

-- 3) Acessos: todos os vendedores ativos para os sistemas 1, 2 e 3
INSERT INTO provao_base.usuarios_acessos (usuario_id, sistema_id, perfil, ativo)
SELECT u.id, s.id, 'Vendedor', 1
FROM provao_base.usuarios u
JOIN provao_base.sistemas s ON s.id IN (1, 2, 3) AND s.ativo = 1
WHERE u.nivel = 'Vendedor' AND u.ativo = 'Sim'
ON DUPLICATE KEY UPDATE perfil = VALUES(perfil), ativo = VALUES(ativo);

-- 4) Comissoes por sistema
-- Opcao A (padrao): copiar vendedores.comissao para todos os sistemas
INSERT INTO provao_base.usuarios_comissoes
  (usuario_id, sistema_id, nivel, percentual, ativo, data)
SELECT u.id,
       s.id,
       'Vendedor',
       CAST(REPLACE(v.comissao, ',', '.') AS DECIMAL(10,2)),
       1,
       CURDATE()
FROM provao_base.usuarios u
JOIN provao_base.vendedores v ON v.id = u.id_pessoa
JOIN provao_base.sistemas s ON s.id IN (1, 2, 3) AND s.ativo = 1
WHERE u.nivel = 'Vendedor'
  AND u.ativo = 'Sim'
  AND v.ativo = 'Sim'
ON DUPLICATE KEY UPDATE percentual = VALUES(percentual), ativo = VALUES(ativo);

-- Opcao B: zerar comissao para o admin ajustar depois
-- INSERT INTO provao_base.usuarios_comissoes
--   (usuario_id, sistema_id, nivel, percentual, ativo, data)
-- SELECT u.id,
--        s.id,
--        'Vendedor',
--        0,
--        1,
--        CURDATE()
-- FROM provao_base.usuarios u
-- JOIN provao_base.vendedores v ON v.id = u.id_pessoa
-- JOIN provao_base.sistemas s ON s.id IN (1, 2, 3) AND s.ativo = 1
-- WHERE u.nivel = 'Vendedor'
--   AND u.ativo = 'Sim'
--   AND v.ativo = 'Sim'
-- ON DUPLICATE KEY UPDATE percentual = VALUES(percentual), ativo = VALUES(ativo);

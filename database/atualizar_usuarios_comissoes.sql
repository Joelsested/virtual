-- Atualiza usuarios_comissoes nos bancos reais (provao_base, virtual_base, sestedcursos_base).
-- Opcao padrao: cria a tabela em provao_base e cria views nos demais bancos.
-- Execute com um usuario que tenha permissao para CREATE TABLE/VIEW.

-- 1) Base principal
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

-- 2) Views para bases satelites (mantem dados centralizados em provao_base)
DROP TABLE IF EXISTS virtual_base.usuarios_comissoes;
DROP VIEW IF EXISTS virtual_base.usuarios_comissoes;
CREATE VIEW virtual_base.usuarios_comissoes AS SELECT * FROM provao_base.usuarios_comissoes;

DROP TABLE IF EXISTS sestedcursos_base.usuarios_comissoes;
DROP VIEW IF EXISTS sestedcursos_base.usuarios_comissoes;
CREATE VIEW sestedcursos_base.usuarios_comissoes AS SELECT * FROM provao_base.usuarios_comissoes;

-- Caso prefira tabela fisica em cada base, comente os CREATE VIEW acima e use:
-- CREATE TABLE IF NOT EXISTS virtual_base.usuarios_comissoes LIKE provao_base.usuarios_comissoes;
-- CREATE TABLE IF NOT EXISTS sestedcursos_base.usuarios_comissoes LIKE provao_base.usuarios_comissoes;

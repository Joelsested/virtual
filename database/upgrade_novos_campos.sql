-- Upgrade: novos campos/tabelas para alinhar com provao
-- Baseado em: database/banco antigo.sql
-- Execute no banco virtual (mysql).

SET @db := DATABASE();

-- Tabela nova para registrar comissoes recebidas
CREATE TABLE IF NOT EXISTS comissoes_recebimentos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  gateway VARCHAR(30) NOT NULL,
  pagamento_id VARCHAR(80) NOT NULL,
  id_matricula INT UNSIGNED NULL,
  wallet_id VARCHAR(80) NULL,
  usuario_id INT UNSIGNED NULL,
  valor DECIMAL(10,2) NOT NULL DEFAULT 0,
  status VARCHAR(20) NOT NULL DEFAULT 'PENDENTE',
  data_pagamento DATE NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_gateway_pag_wallet (gateway, pagamento_id, wallet_id),
  KEY idx_matricula (id_matricula),
  KEY idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Funcoes auxiliares via SQL dinamico

-- alunos.orgao_expedidor
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='alunos' AND COLUMN_NAME='orgao_expedidor');
SET @sql := IF(@col=0,'ALTER TABLE alunos ADD COLUMN orgao_expedidor VARCHAR(60) NULL','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- parcelas_geradas_por_boleto.data_pagamento
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='parcelas_geradas_por_boleto' AND COLUMN_NAME='data_pagamento');
SET @sql := IF(@col=0,'ALTER TABLE parcelas_geradas_por_boleto ADD COLUMN data_pagamento DATETIME NULL','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- nascimento em tabelas de responsaveis
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='parceiros' AND COLUMN_NAME='nascimento');
SET @sql := IF(@col=0,'ALTER TABLE parceiros ADD COLUMN nascimento VARCHAR(12) NULL','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='professores' AND COLUMN_NAME='nascimento');
SET @sql := IF(@col=0,'ALTER TABLE professores ADD COLUMN nascimento VARCHAR(12) NULL','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='secretarios' AND COLUMN_NAME='nascimento');
SET @sql := IF(@col=0,'ALTER TABLE secretarios ADD COLUMN nascimento VARCHAR(12) NULL','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='tesoureiros' AND COLUMN_NAME='nascimento');
SET @sql := IF(@col=0,'ALTER TABLE tesoureiros ADD COLUMN nascimento VARCHAR(12) NULL','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='tutores' AND COLUMN_NAME='nascimento');
SET @sql := IF(@col=0,'ALTER TABLE tutores ADD COLUMN nascimento VARCHAR(12) NULL','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='vendedores' AND COLUMN_NAME='nascimento');
SET @sql := IF(@col=0,'ALTER TABLE vendedores ADD COLUMN nascimento VARCHAR(12) NULL','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='vendedores' AND COLUMN_NAME='tutor_id');
SET @sql := IF(@col=0,'ALTER TABLE vendedores ADD COLUMN tutor_id INT NULL','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- config.acrescimo_cartao_credito
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='config' AND COLUMN_NAME='acrescimo_cartao_credito');
SET @sql := IF(@col=0,'ALTER TABLE config ADD COLUMN acrescimo_cartao_credito DECIMAL(10,2) NOT NULL DEFAULT 0','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- config.mp_key
SET @col := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='config' AND COLUMN_NAME='mp_key');
SET @sql := IF(@col=0,'ALTER TABLE config ADD COLUMN mp_key VARCHAR(120) NULL','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- usuarios.senha_crip maior para hashes modernos
SET @len := (SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='usuarios' AND COLUMN_NAME='senha_crip');
SET @sql := IF(@len IS NULL OR @len >= 255,'SELECT 1','ALTER TABLE usuarios MODIFY COLUMN senha_crip VARCHAR(255) NOT NULL');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- indice para wallet_id (melhor lookup em webhook)
SET @idx := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=@db AND TABLE_NAME='usuarios' AND INDEX_NAME='idx_wallet_id');
SET @sql := IF(@idx=0,'ALTER TABLE usuarios ADD INDEX idx_wallet_id (wallet_id(80))','SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

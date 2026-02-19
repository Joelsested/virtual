-- Preparacao para aplicar mudancas do provao no virtual
-- Execute manualmente no banco do sistema virtual.
-- Se algum ALTER falhar por coluna existente, ignore o erro.

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

ALTER TABLE usuarios
  ADD COLUMN wallet_id VARCHAR(80) NULL,
  ADD INDEX idx_wallet_id (wallet_id);

ALTER TABLE usuarios
  MODIFY COLUMN senha_crip VARCHAR(255);

ALTER TABLE alunos
  ADD COLUMN orgao_expedidor VARCHAR(60) NULL;

ALTER TABLE config
  ADD COLUMN acrescimo_cartao_credito DECIMAL(10,2) NOT NULL DEFAULT 0,
  ADD COLUMN chave_asaas VARCHAR(120) NULL,
  ADD COLUMN mp_key VARCHAR(120) NULL;

ALTER TABLE matriculas
  ADD COLUMN id_pacote INT NULL;

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

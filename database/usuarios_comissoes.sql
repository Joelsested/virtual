CREATE TABLE IF NOT EXISTS usuarios_comissoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    sistema_id INT NOT NULL,
    nivel VARCHAR(30) NOT NULL,
    percentual DECIMAL(10,2) NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    data DATE NULL,
    UNIQUE KEY uniq_usuario_sistema_nivel (usuario_id, sistema_id, nivel),
    KEY idx_sistema_nivel (sistema_id, nivel),
    KEY idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

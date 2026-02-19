-- Adiciona acessos para todos os usuarios ativos nos sistemas 1, 2 e 3.
-- Execute com permissao nas bases provao_base, virtual_base e sestedcursos_base.

INSERT INTO provao_base.usuarios_acessos (usuario_id, sistema_id, perfil, ativo)
SELECT u.id,
       s.id,
       u.nivel,
       1
FROM provao_base.usuarios u
JOIN provao_base.sistemas s ON s.id IN (1, 2, 3) AND s.ativo = 1
WHERE u.ativo = 'Sim'
ON DUPLICATE KEY UPDATE perfil = VALUES(perfil), ativo = VALUES(ativo);

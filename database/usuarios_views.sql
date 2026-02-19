-- Views to keep auth tables in provao_base while using separate data DBs.
-- Run as a user with CREATE VIEW permissions.

-- Virtual
DROP TABLE IF EXISTS virtual_base.usuarios;
DROP VIEW IF EXISTS virtual_base.usuarios;
CREATE VIEW virtual_base.usuarios AS SELECT * FROM provao_base.usuarios;

DROP TABLE IF EXISTS virtual_base.alunos;
DROP VIEW IF EXISTS virtual_base.alunos;
CREATE VIEW virtual_base.alunos AS SELECT * FROM provao_base.alunos;

DROP TABLE IF EXISTS virtual_base.alunos_responsaveis;
DROP VIEW IF EXISTS virtual_base.alunos_responsaveis;
CREATE VIEW virtual_base.alunos_responsaveis AS SELECT * FROM provao_base.alunos_responsaveis;

DROP TABLE IF EXISTS virtual_base.usuarios_acessos;
DROP VIEW IF EXISTS virtual_base.usuarios_acessos;
CREATE VIEW virtual_base.usuarios_acessos AS SELECT * FROM provao_base.usuarios_acessos;

DROP TABLE IF EXISTS virtual_base.sistemas;
DROP VIEW IF EXISTS virtual_base.sistemas;
CREATE VIEW virtual_base.sistemas AS SELECT * FROM provao_base.sistemas;

DROP TABLE IF EXISTS virtual_base.usuarios_comissoes;
DROP VIEW IF EXISTS virtual_base.usuarios_comissoes;
CREATE VIEW virtual_base.usuarios_comissoes AS SELECT * FROM provao_base.usuarios_comissoes;

-- Sestedcursos
DROP TABLE IF EXISTS sestedcursos_base.usuarios;
DROP VIEW IF EXISTS sestedcursos_base.usuarios;
CREATE VIEW sestedcursos_base.usuarios AS SELECT * FROM provao_base.usuarios;

DROP TABLE IF EXISTS sestedcursos_base.alunos;
DROP VIEW IF EXISTS sestedcursos_base.alunos;
CREATE VIEW sestedcursos_base.alunos AS SELECT * FROM provao_base.alunos;

DROP TABLE IF EXISTS sestedcursos_base.alunos_responsaveis;
DROP VIEW IF EXISTS sestedcursos_base.alunos_responsaveis;
CREATE VIEW sestedcursos_base.alunos_responsaveis AS SELECT * FROM provao_base.alunos_responsaveis;

DROP TABLE IF EXISTS sestedcursos_base.usuarios_acessos;
DROP VIEW IF EXISTS sestedcursos_base.usuarios_acessos;
CREATE VIEW sestedcursos_base.usuarios_acessos AS SELECT * FROM provao_base.usuarios_acessos;

DROP TABLE IF EXISTS sestedcursos_base.sistemas;
DROP VIEW IF EXISTS sestedcursos_base.sistemas;
CREATE VIEW sestedcursos_base.sistemas AS SELECT * FROM provao_base.sistemas;

DROP TABLE IF EXISTS sestedcursos_base.usuarios_comissoes;
DROP VIEW IF EXISTS sestedcursos_base.usuarios_comissoes;
CREATE VIEW sestedcursos_base.usuarios_comissoes AS SELECT * FROM provao_base.usuarios_comissoes;

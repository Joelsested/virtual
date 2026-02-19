-- Garante coluna tutor_id em vendedores para virtual e sestedcursos.
-- Execute com permissao nas bases virtual_base e sestedcursos_base.

SET @has_tutor_virtual := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'virtual_base'
    AND TABLE_NAME = 'vendedores'
    AND COLUMN_NAME = 'tutor_id'
);
SET @sql_tutor_virtual := IF(
  @has_tutor_virtual = 0,
  'ALTER TABLE virtual_base.vendedores ADD COLUMN tutor_id INT(11) NULL AFTER professor',
  'SELECT 1'
);
PREPARE stmt_tutor_virtual FROM @sql_tutor_virtual;
EXECUTE stmt_tutor_virtual;
DEALLOCATE PREPARE stmt_tutor_virtual;

SET @has_tutor_sested := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'sestedcursos_base'
    AND TABLE_NAME = 'vendedores'
    AND COLUMN_NAME = 'tutor_id'
);
SET @sql_tutor_sested := IF(
  @has_tutor_sested = 0,
  'ALTER TABLE sestedcursos_base.vendedores ADD COLUMN tutor_id INT(11) NULL AFTER professor',
  'SELECT 1'
);
PREPARE stmt_tutor_sested FROM @sql_tutor_sested;
EXECUTE stmt_tutor_sested;
DEALLOCATE PREPARE stmt_tutor_sested;

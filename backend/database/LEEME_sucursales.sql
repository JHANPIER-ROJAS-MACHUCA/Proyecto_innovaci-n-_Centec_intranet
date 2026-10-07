-- Módulo SUCURSALES (traído de CENTECPC) — migración aplicada 2026-10-07
-- BD destino local: credisoportecom_credisopo
--
-- 1) Estructuras reales (18 tablas, mysqldump --no-data desde cent3cpcom_crediinversion):
--    ver archivo: database/estructuras_reales_origen.sql
--    (sucursales, empresas, bank_accounts, sucursal_metodo_pago, tipos_credito,
--     tipos_ahorro, tipos_seguro, pago_comprobantes, usuario_sucursal, rol,
--      eval_actividades, eval_activos, eval_config, eval_costos, eval_credito,
--      eval_deudas, eval_gastos, eval_indicadores, eval_ingresos)
--    NOTA: este archivo antiguo (extraído del .sql con DDL inferido para
--    sucursal_metodo_pago/pago_comprobantes) quedó SUPERSEDEDO por
--    estructuras_reales_origen.sql. No ejecutar ambos.
--
-- 2) bank_accounts local era versión vieja (6 cols): se agregaron:
ALTER TABLE `bank_accounts`
  ADD COLUMN IF NOT EXISTS `empresa_id` int(11) NULL DEFAULT NULL AFTER `destination_id`,
  ADD COLUMN IF NOT EXISTS `cci` varchar(100) NULL DEFAULT NULL AFTER `empresa_id`,
  ADD COLUMN IF NOT EXISTS `qr` varchar(255) NULL DEFAULT NULL AFTER `cci`,
  ADD COLUMN IF NOT EXISTS `icono` varchar(255) NULL DEFAULT NULL AFTER `qr`,
  ADD COLUMN IF NOT EXISTS `estado` tinyint(1) NOT NULL DEFAULT 1 AFTER `icono`,
  ADD COLUMN IF NOT EXISTS `orden` int(11) NOT NULL DEFAULT 0 AFTER `estado`;
-- (MariaDB <10.2 no soporta IF NOT EXISTS en ADD COLUMN: borrar la cláusula si falla)
--
-- 3) Datos maestros copiados (tablas vacías en destino, sin conflicto):
--    empresas (1), sucursales (3), tipos_credito (5), tipos_ahorro (3),
--    tipos_seguro (2), rol (8). NO se copiaron: bank_accounts (4 filas propias),
--    usuario_sucursal, sucursal_metodo_pago, pago_comprobantes, eval_* (operativos).

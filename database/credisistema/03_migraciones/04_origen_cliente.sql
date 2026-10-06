-- Migración: columna "origen" en tclie_general para marcar clientes migrados
-- Ejecutar en la base de datos de cPanel (y en local)
ALTER TABLE `tclie_general`
  ADD COLUMN `origen` VARCHAR(20) NOT NULL DEFAULT 'sistema' AFTER `status`;
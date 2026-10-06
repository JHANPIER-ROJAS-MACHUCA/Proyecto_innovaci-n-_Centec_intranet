-- Migración: campo "dirigido_a" en tipos de crédito y ahorros + imagen principal en ahorros
-- Fecha: 2026-09-23

ALTER TABLE tipos_credito ADD COLUMN IF NOT EXISTS dirigido_a TEXT NULL AFTER descripcion;

ALTER TABLE tipos_ahorro ADD COLUMN IF NOT EXISTS imagen VARCHAR(255) NULL AFTER descripcion;
ALTER TABLE tipos_ahorro ADD COLUMN IF NOT EXISTS dirigido_a TEXT NULL AFTER descripcion;

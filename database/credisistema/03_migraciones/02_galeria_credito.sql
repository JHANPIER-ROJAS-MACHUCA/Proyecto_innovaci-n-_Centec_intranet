-- Migracion: Galeria de Tipos de Credito + Plazos en dias y semanas
-- Re-ejecutable (idempotente): los bloques protegen con IF NOT EXISTS / INFORMATION_SCHEMA

-- 1) Nuevas columnas de plazo en tipos_credito (dias y semanas)
ALTER TABLE tipos_credito
    ADD COLUMN IF NOT EXISTS plazo_min_dias   INT NULL DEFAULT 0 AFTER plazo_max,
    ADD COLUMN IF NOT EXISTS plazo_max_dias   INT NULL DEFAULT 0 AFTER plazo_min_dias,
    ADD COLUMN IF NOT EXISTS plazo_min_semanas INT NULL DEFAULT 0 AFTER plazo_max_dias,
    ADD COLUMN IF NOT EXISTS plazo_max_semanas INT NULL DEFAULT 0 AFTER plazo_min_semanas;

-- 2) Tabla de galeria por tipo de credito
CREATE TABLE IF NOT EXISTS galeria_tipos_credito (
    idGTC INT NOT NULL,
    idTC INT NOT NULL,
    titulo VARCHAR(100) DEFAULT NULL,
    descripcion TEXT DEFAULT NULL,
    imagen VARCHAR(255) NOT NULL,
    tipo_imagen ENUM('principal','promocional','informativo','otro') NOT NULL DEFAULT 'otro',
    es_principal TINYINT(1) DEFAULT 0,
    orden INT DEFAULT 0,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idGTC),
    KEY idx_galeria_tc (idTC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
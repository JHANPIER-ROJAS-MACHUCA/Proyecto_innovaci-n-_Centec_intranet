-- ============================================================
-- Migracion pendiente para PRODUCCION
-- Cambios de esquema aplicados en desarrollo y no presentes en prod.
-- Ejecutar una sola vez.
-- ============================================================

-- 1) NUEVA TABLA: eval_form_data
--    Guarda el snapshot completo del formulario de evaluacion para
--    poder restaurarlo al editar. NO EXISTE EN PRODUCCION.
CREATE TABLE IF NOT EXISTS `eval_form_data` (
  `grupo` bigint(20) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `data` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`grupo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) NUEVA TABLA: home_necesitas (si no existe en producción)
CREATE TABLE IF NOT EXISTS `home_necesitas` (
  `idNecesita` int(11) NOT NULL AUTO_INCREMENT,
  `icono` varchar(30) NOT NULL DEFAULT 'default',
  `etiqueta` varchar(80) NOT NULL,
  `url` varchar(200) NOT NULL DEFAULT '',
  `orden` int(11) NOT NULL DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idNecesita`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3) eval_indicadores: columnas de borrado (ocultar) de evaluaciones
ALTER TABLE `eval_indicadores` ADD COLUMN IF NOT EXISTS `eliminado` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `eval_indicadores` ADD COLUMN IF NOT EXISTS `eliminado_por` int(11) DEFAULT NULL;
ALTER TABLE `eval_indicadores` ADD COLUMN IF NOT EXISTS `eliminado_at` datetime DEFAULT NULL;

-- 4) Fecha de evaluación con HORA (era DATE)
ALTER TABLE `eval_credito` MODIFY `fecha` datetime NOT NULL;
ALTER TABLE `eval_indicadores` MODIFY `fecha` datetime NOT NULL;

-- 5) tipos_credito: campo "Dirigido a"
ALTER TABLE `tipos_credito` ADD COLUMN IF NOT EXISTS `dirigido_a` text DEFAULT NULL AFTER `descripcion`;

-- 6) tipos_ahorro: imagen principal + "Dirigido a"
ALTER TABLE `tipos_ahorro` ADD COLUMN IF NOT EXISTS `imagen` varchar(255) DEFAULT NULL AFTER `descripcion`;
ALTER TABLE `tipos_ahorro` ADD COLUMN IF NOT EXISTS `dirigido_a` text DEFAULT NULL AFTER `descripcion`;

-- 7) libro_reclamaciones: evidencias (agregado antes; incluir si falta)
ALTER TABLE `libro_reclamaciones` ADD COLUMN IF NOT EXISTS `evidencias` text DEFAULT NULL;

-- 8) Permiso para "Quienes Somos" en el home (dato, no esquema)
--    Si el id 234 está ocupado en producción, ajustar el id.
INSERT INTO `permisos` (`id`, `modulo`, `accion`, `estado`)
SELECT 234, 'home', 'quienes_somos', 1
WHERE NOT EXISTS (SELECT 1 FROM (SELECT * FROM `permisos`) p WHERE p.modulo='home' AND p.accion='quienes_somos');
-- Otorgar el permiso al rol que corresponda (ejemplo rol 1 = admin, alcance 'todas'):
-- INSERT INTO rol_permisos (idRol, idPermiso, alcance) SELECT 1, id, 'todas' FROM permisos WHERE modulo='home' AND accion='quienes_somos';

-- ============================================
-- Migración: STATS BAR del Home administrable
-- Ejecutar en la BD del sistema
-- (también esta registrada en public/_migrate.php)
-- ============================================

CREATE TABLE IF NOT EXISTS `home_stats` (
  `idStat` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(50) NOT NULL DEFAULT '',
  `sufijo` varchar(10) NOT NULL DEFAULT '',
  `etiqueta` varchar(255) NOT NULL DEFAULT '',
  `orden` int(11) NOT NULL DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idStat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `home_stats` (`numero`,`sufijo`,`etiqueta`,`orden`,`estado`) VALUES
('+15','','Años de experiencia',1,1),
('+5,000','','Clientes atendidos',2,1),
('+5','','Sucursales',3,1),
('88','%','Satisfacción',4,1);

-- Permiso para administrar estadísticas del home
INSERT INTO `permisos` (`id`, `nombre`, `accion`, `estado`, `descripcion`, `modulo`)
SELECT COALESCE(MAX(id), 0) + 1, 'home.stats', 'stats', 1, 'Administrar estadísticas del home', 'home'
FROM `permisos`
WHERE NOT EXISTS (SELECT 1 FROM `permisos` WHERE `accion` = 'stats' AND `modulo` = 'home');

-- Asignar el permiso al rol dueño (idRol = 1)
INSERT INTO `rol_permisos` (`idRol`, `idPermiso`, `alcance`)
SELECT 1, `id`, 'todas'
FROM `permisos`
WHERE `accion` = 'stats' AND `modulo` = 'home'
  AND NOT EXISTS (
    SELECT 1 FROM `rol_permisos`
    WHERE `idRol` = 1 AND `idPermiso` = (SELECT `id` FROM `permisos` WHERE `accion` = 'stats' AND `modulo` = 'home' LIMIT 1)
  )
LIMIT 1;

-- Color de fondo de la barra de estadisticas (tema)
INSERT IGNORE INTO `theme_settings` (`setting_key`, `setting_value`, `label`, `type`)
SELECT 'color_stats_bg', setting_value, 'Color de fondo de estadisticas', 'color'
FROM `theme_settings`
WHERE `setting_key` = 'color_primary'
  AND NOT EXISTS (SELECT 1 FROM `theme_settings` WHERE `setting_key` = 'color_stats_bg');
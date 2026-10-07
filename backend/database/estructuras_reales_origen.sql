/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sucursales` (
  `idS` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('principal','sucursal') NOT NULL DEFAULT 'sucursal',
  `codigo` varchar(20) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL COMMENT 'ruta: assets/img/sucursales/logo.png',
  `banner` varchar(255) DEFAULT NULL COMMENT 'ruta: assets/img/sucursales/banner.jpg',
  `responsable` varchar(100) DEFAULT NULL,
  `tipos_credito` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tipos_credito`)),
  `tipos_ahorro` longtext DEFAULT NULL,
  `tipos_seguro` longtext DEFAULT NULL,
  `servicios` longtext DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `distrito` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `celular` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `horario_pago` varchar(500) DEFAULT NULL,
  `latitud` varchar(30) DEFAULT NULL,
  `longitud` varchar(30) DEFAULT NULL,
  `mapa_embed` text DEFAULT NULL COMMENT 'iframe del mapa de Google Maps',
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`idS`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empresas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `razon_social` varchar(200) NOT NULL DEFAULT '',
  `grupo` varchar(50) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_credito` (
  `idTC` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL COMMENT 'identificador url friendly',
  `descripcion` text DEFAULT NULL,
  `dirigido_a` text DEFAULT NULL,
  `categoria` enum('personal','hipotecario','agropecuario','empresarial') DEFAULT 'personal',
  `tasa_anual` decimal(10,2) DEFAULT NULL COMMENT 'Tasa Efectiva Anual (TEA %)',
  `tasa_mensual` decimal(10,2) DEFAULT NULL COMMENT 'Tasa Efectiva Mensual (TEM %)',
  `monto_minimo` decimal(12,2) DEFAULT NULL COMMENT 'Monto minimo en soles',
  `monto_maximo` decimal(12,2) DEFAULT NULL COMMENT 'Monto maximo en soles',
  `plazo_min` int(11) DEFAULT NULL COMMENT 'Plazo minimo en meses',
  `plazo_max` int(11) DEFAULT NULL COMMENT 'Plazo maximo en meses',
  `plazo_min_dias` int(11) DEFAULT 0,
  `plazo_max_dias` int(11) DEFAULT 0,
  `plazo_min_semanas` int(11) DEFAULT 0,
  `plazo_max_semanas` int(11) DEFAULT 0,
  `cuota_inicial` decimal(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de cuota inicial (%)',
  `comision` decimal(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de comision (%)',
  `seguro_desgravamen` decimal(5,2) DEFAULT 0.00 COMMENT 'Porcentaje seguro (%)',
  `requisitos` text DEFAULT NULL COMMENT 'Requisitos en formato JSON o texto separado por saltos de linea',
  `beneficios` text DEFAULT NULL COMMENT 'Beneficios en formato JSON o texto',
  `caracteristicas` text DEFAULT NULL,
  `icono` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL COMMENT 'ruta: assets/img/sucursales/credito.jpg',
  `orden` int(11) DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`idTC`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_ahorro` (
  `idAhorro` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `descripcion` text DEFAULT NULL,
  `dirigido_a` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `requisitos` text DEFAULT NULL,
  `beneficios` text DEFAULT NULL,
  `icono` varchar(100) NOT NULL DEFAULT '',
  `orden` int(11) NOT NULL DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idAhorro`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_seguro` (
  `idSeguro` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `descripcion` text DEFAULT NULL,
  `cobertura` decimal(12,2) DEFAULT NULL,
  `requisitos` text DEFAULT NULL,
  `beneficios` text DEFAULT NULL,
  `icono` varchar(100) NOT NULL DEFAULT '',
  `orden` int(11) NOT NULL DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`idSeguro`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario_sucursal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idS` int(11) NOT NULL COMMENT 'id de la sucursal (sucursales.idS)',
  `idU` int(11) NOT NULL COMMENT 'id del asesor (tusuarios.idU, idRol=2)',
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `tipo_asignacion` enum('principal','secundaria') NOT NULL DEFAULT 'principal',
  `fecha_inicio` date DEFAULT NULL COMMENT 'fecha de inicio del contrato del asesor',
  `fecha_fin` date DEFAULT NULL COMMENT 'fecha de fin del contrato del asesor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sucursal_asesor` (`idS`,`idU`),
  KEY `idx_usuario_sucursal_idU` (`idU`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Asesores asignados a cada sucursal';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(15) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sucursal_metodo_pago` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idS` int(11) NOT NULL,
  `banco_id` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_sucursal_banco` (`idS`,`banco_id`),
  KEY `idx_banco` (`banco_id`),
  KEY `idx_sucursal` (`idS`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pago_comprobantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idU` int(11) DEFAULT NULL,
  `idCG` int(11) DEFAULT NULL,
  `deuda_id` int(11) DEFAULT NULL,
  `deuda_empresa_id` int(11) DEFAULT NULL,
  `receptor_empresa_id` int(11) DEFAULT NULL,
  `sucursal_id` int(11) DEFAULT NULL,
  `banco_id` int(11) DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fecha_pago` date DEFAULT NULL,
  `nro_operacion` varchar(50) DEFAULT NULL,
  `comprobante` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `validado_por` int(11) DEFAULT NULL,
  `fecha_validacion` datetime DEFAULT NULL,
  `monto_aplicado` decimal(12,2) DEFAULT NULL,
  `sobrante` decimal(12,2) DEFAULT NULL,
  `aplicado_detalle` text DEFAULT NULL,
  `motivo_rechazo` varchar(255) DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_idcg` (`idCG`),
  KEY `idx_sucursal` (`sucursal_id`),
  KEY `idx_deuda` (`deuda_id`),
  KEY `idx_banco` (`banco_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eval_actividades` (
  `id_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `grupo` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `numero` int(11) NOT NULL DEFAULT 1,
  `descripcion` varchar(300) DEFAULT NULL,
  `frecuencia` varchar(50) DEFAULT NULL,
  `num_dias` int(11) DEFAULT 1,
  `venta_baja` decimal(14,2) DEFAULT 0.00,
  `peso_baja` int(11) DEFAULT 2,
  `venta_alta` decimal(14,2) DEFAULT 0.00,
  `peso_alta` int(11) DEFAULT 1,
  `venta_media` decimal(14,2) DEFAULT 0.00,
  `peso_media` int(11) DEFAULT 2,
  `promedio_ponderado` decimal(14,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_actividad`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_grupo` (`grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=2402 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eval_activos` (
  `id_activo` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `grupo` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `categoria` varchar(50) NOT NULL COMMENT 'inventario|muebles|inmuebles|equipos|vehiculos|otros',
  `descripcion` varchar(300) NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  `monto` decimal(14,2) DEFAULT 0.00,
  `marca` varchar(100) DEFAULT '',
  `modelo` varchar(100) DEFAULT '',
  `serie` varchar(100) DEFAULT '',
  `estado` varchar(50) DEFAULT '',
  `ruta_imagen` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_activo`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_grupo` (`grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=8311 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eval_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL,
  `valor` varchar(255) NOT NULL DEFAULT '',
  `descripcion` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eval_costos` (
  `id_costo` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `grupo` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` varchar(300) NOT NULL,
  `frecuencia` varchar(50) DEFAULT NULL,
  `monto_mensual` decimal(14,2) DEFAULT 0.00,
  `monto_diario` decimal(14,4) DEFAULT 0.0000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_costo`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_grupo` (`grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=4853 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eval_credito` (
  `id_credito` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `grupo` bigint(20) NOT NULL,
  `fecha` datetime NOT NULL,
  `monto_solicitado` decimal(14,2) NOT NULL DEFAULT 0.00,
  `tem` decimal(6,4) DEFAULT 0.0000,
  `tipo_credito` varchar(100) DEFAULT NULL,
  `frecuencia_pago` varchar(50) DEFAULT NULL,
  `numero_cuotas` int(11) DEFAULT 0,
  `cuota_estimada` decimal(14,2) DEFAULT 0.00,
  `periodo_cuotas` varchar(50) DEFAULT NULL,
  `condicion` varchar(50) DEFAULT NULL,
  `monto_propuesto` decimal(14,2) DEFAULT 0.00,
  `fecha_caducidad` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_credito`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_grupo` (`grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=2416 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eval_deudas` (
  `id_deuda` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `grupo` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `entidad` varchar(200) DEFAULT NULL,
  `frecuencia_pago` varchar(50) DEFAULT NULL,
  `monto` decimal(14,2) DEFAULT 0.00,
  `periodo` int(11) DEFAULT 0,
  `tasa_promedio` decimal(6,4) DEFAULT 0.0000,
  `condicion` varchar(50) DEFAULT NULL,
  `cuota` decimal(14,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_deuda`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_grupo` (`grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=4822 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eval_gastos` (
  `id_gasto` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `grupo` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` varchar(300) DEFAULT NULL,
  `num_integrantes` int(11) DEFAULT 0,
  `monto_mensual` decimal(14,2) DEFAULT 0.00,
  `monto_diario` decimal(14,4) DEFAULT 0.0000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_gasto`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_grupo` (`grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=12634 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eval_indicadores` (
  `id_indicador` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `grupo` bigint(20) NOT NULL,
  `fecha` datetime NOT NULL,
  `excedente` decimal(14,2) DEFAULT 0.00,
  `capacidad_pago` decimal(14,4) DEFAULT 0.0000,
  `endeudamiento_patrimonial` decimal(14,4) DEFAULT 0.0000,
  `capital_trabajo` decimal(14,2) DEFAULT 0.00,
  `califica_credito` varchar(20) DEFAULT NULL,
  `monto_atender` decimal(14,2) DEFAULT 0.00,
  `mora_diaria` decimal(10,2) DEFAULT 0.00,
  `ingresos_simple` decimal(14,2) DEFAULT NULL,
  `egresos_simple` decimal(14,2) DEFAULT NULL,
  `estado` enum('borrador','completado') DEFAULT 'borrador',
  `editar_permiso` tinyint(1) DEFAULT 0,
  `editado_una_vez` tinyint(1) DEFAULT 0,
  `resultado` enum('aprobado','rechazado','riesgo_medio','pendiente') DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `eliminado_por` int(11) DEFAULT NULL,
  `eliminado_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_indicador`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_grupo` (`grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=2416 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eval_ingresos` (
  `id_ingreso` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `grupo` bigint(20) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` varchar(300) DEFAULT NULL,
  `frecuencia` varchar(50) DEFAULT NULL,
  `num_dias` int(11) DEFAULT 26,
  `monto_mensual` decimal(14,2) DEFAULT 0.00,
  `monto_diario` decimal(14,4) DEFAULT 0.0000,
  `ruta_imagen` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_ingreso`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_grupo` (`grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=4640 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

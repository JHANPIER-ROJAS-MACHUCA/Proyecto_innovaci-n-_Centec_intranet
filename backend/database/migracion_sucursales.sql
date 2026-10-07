-- Migracion sucursales traida de CENTECPC
-- Origen dump: cent3cpcom_crediinversion (5).sql

CREATE TABLE `sucursales` (
  `idS` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('principal','sucursal') NOT NULL DEFAULT 'sucursal',
  `codigo` varchar(20) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL COMMENT 'ruta: assets/img/sucursales/logo.png',
  `banner` varchar(255) DEFAULT NULL COMMENT 'ruta: assets/img/sucursales/banner.jpg',
  `responsable` varchar(100) DEFAULT NULL,
  `tipos_credito` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tipos_credito`)),
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
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`idS`, `nombre`, `tipo`, `codigo`, `empresa_id`, `logo`, `banner`, `responsable`, `tipos_credito`, `descripcion`, `direccion`, `departamento`, `provincia`, `distrito`, `telefono`, `celular`, `email`, `horario`, `horario_pago`, `latitud`, `longitud`, `mapa_embed`, `estado`, `fecha_creacion`) VALUES
(1, 'Oficina Principal', 'principal', 'FCN', 1, 'logo_6a9ae2886267f.jpg', 'banner_6a9ae28862bb3.jpg', 'Roberto. E. Cosme', '[\"ahorro-mas\"]', 'Oficina Central de CENTECP - Sede principal de operaciones y atencion al cliente.', 'Jr. Ruben Callegari, Satipo 12261, Per', 'Junin', 'Satipo', 'Satipo', '(01) 123-4567', '987654321', 'rcosme@centecp.com', 'Lun-Vie 8:00am - 6:00pm y Sab de 9:00am - 19:00pm', NULL, '-11.25929735581484', '-74.63943329589151', '', 1, '2026-05-11 09:40:57'),
(6, 'Oficina Megan', 'sucursal', 'FCN2', 1, 'logo_6a9ae3ac6bc64.jpg', 'banner_6a9ae3ac6c27f.jpg', 'Megan', '[]', '', 'JR. RUBEN CALLEGARI 312', 'Junin', 'Satipo', 'Satipo', '965869011', '', 'creditos@centecp.com', '', NULL, '', '', '', 1, '2026-05-22 13:19:55');



CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `razon_social` varchar(200) NOT NULL DEFAULT '',
  `grupo` varchar(50) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id`, `nombre`, `razon_social`, `grupo`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'CENTECP', 'CENTECP', NULL, 1, '2026-09-11 14:26:01', '2026-09-11 14:26:01');



CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL,
  `type` enum('bank_account','digital_wallet') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `name` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `number` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `headline_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `destination_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `cci` varchar(100) DEFAULT NULL,
  `qr` varchar(255) DEFAULT NULL,
  `icono` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `type`, `name`, `number`, `headline_name`, `destination_id`, `empresa_id`, `cci`, `qr`, `icono`, `estado`, `orden`) VALUES
(1, 'bank_account', 'BCP', '5252592819018', 'CENTRO DE TECNOLOGÃA Y CRÃ‰DITOS DEL PERÃš', NULL, 1, NULL, NULL, NULL, 1, 0),
(2, 'bank_account', 'BANCO DE LA NACIÃ“N', '04487178204', 'ROBERTO EDGARDO COSME RAMOS', NULL, 1, NULL, NULL, NULL, 1, 0),
(3, 'digital_wallet', 'YAPE CENTECP', '947641447', 'CENTECP', NULL, 1, NULL, NULL, NULL, 1, 0),
(4, 'digital_wallet', 'YAPE ROBERTO COSME', '926486048', 'Roberto Cosme Ramos', NULL, 1, NULL, NULL, NULL, 1, 0);



CREATE TABLE `tipos_credito` (
  `idTC` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL COMMENT 'identificador url friendly',
  `descripcion` text DEFAULT NULL,
  `categoria` enum('personal','hipotecario','agropecuario','empresarial') DEFAULT 'personal',
  `tasa_anual` decimal(10,2) DEFAULT NULL COMMENT 'Tasa Efectiva Anual (TEA %)',
  `tasa_mensual` decimal(10,2) DEFAULT NULL COMMENT 'Tasa Efectiva Mensual (TEM %)',
  `monto_minimo` decimal(12,2) DEFAULT NULL COMMENT 'Monto minimo en soles',
  `monto_maximo` decimal(12,2) DEFAULT NULL COMMENT 'Monto maximo en soles',
  `plazo_min` int(11) DEFAULT NULL COMMENT 'Plazo minimo en meses',
  `plazo_max` int(11) DEFAULT NULL COMMENT 'Plazo maximo en meses',
  `cuota_inicial` decimal(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de cuota inicial (%)',
  `comision` decimal(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de comision (%)',
  `seguro_desgravamen` decimal(5,2) DEFAULT 0.00 COMMENT 'Porcentaje seguro (%)',
  `requisitos` text DEFAULT NULL COMMENT 'Requisitos en formato JSON o texto separado por saltos de linea',
  `beneficios` text DEFAULT NULL COMMENT 'Beneficios en formato JSON o texto',
  `icono` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL COMMENT 'ruta: assets/img/sucursales/credito.jpg',
  `orden` int(11) DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `tipos_credito`
--

INSERT INTO `tipos_credito` (`idTC`, `nombre`, `slug`, `descripcion`, `categoria`, `tasa_anual`, `tasa_mensual`, `monto_minimo`, `monto_maximo`, `plazo_min`, `plazo_max`, `cuota_inicial`, `comision`, `seguro_desgravamen`, `requisitos`, `beneficios`, `icono`, `imagen`, `orden`, `estado`, `fecha_creacion`) VALUES
(1, 'CrediDiario', 'crediDiario', 'Credito personal para trabajadores dependientes con ingreso diario.', 'personal', 45.00, 3.10, 500.00, 30000.00, 6, 60, 0.00, 2.00, 0.50, 'DNI vigente\r\nBoletas de pago (ultimos 3 meses)\r\nCertificado de trabajo\r\nNo tener deudas en el sistema financiero', 'Aprobacion en 24 horas\r\nDescuento por planilla\r\nSin garantes', '', '', 1, 1, '2026-05-11 09:40:57'),
(2, 'CrediAhorro', 'crediahorro', 'Credito de libre disponibilidad con proceso agil y sencillo.', 'personal', 52.00, 3.50, 1000.00, 50000.00, 3, 48, 0.00, 2.50, 0.60, 'DNI vigente\r\nComprobantes de ingresos\r\nRecibos de servicios a nombre del solicitante\r\nReporte positivo en SBS', 'Proceso 100% digital\r\nDesembolso inmediato\r\nFlexibilidad de uso', '', '', 2, 1, '2026-05-11 09:40:57'),
(6, 'AhorroMas', 'ahorro-mas', 'Deposito a plazo fijo con la mejor rentabilidad del mercado.', 'personal', 0.00, 0.00, 500.00, 1000000.00, 30, 365, 0.00, 0.00, 0.00, '??? DNI vigente\r\n??? Monto minimo S/ 500\r\n??? Persona natural o juridica', '??? Hasta 9% TEA\r\n??? Capital garantizado\r\n??? Renovacion automatica', '', '', 6, 1, '2026-05-11 09:40:57');



CREATE TABLE `tipos_ahorro` (
  `idAhorro` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `descripcion` text DEFAULT NULL,
  `icono` varchar(100) NOT NULL DEFAULT '',
  `orden` int(11) NOT NULL DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_ahorro`
--

INSERT INTO `tipos_ahorro` (`idAhorro`, `nombre`, `slug`, `descripcion`, `icono`, `orden`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Ahorro Corriente', 'ahorro-corriente', NULL, '????', 1, 1, '2026-06-01 16:00:53', '2026-06-01 16:00:53'),
(2, 'FlexiTotal', 'flexitotal', NULL, '????', 2, 1, '2026-06-01 16:00:53', '2026-06-01 16:00:53'),
(3, 'Cuenta Sueldo', 'cuenta-sueldo', NULL, '????', 3, 1, '2026-06-01 16:00:53', '2026-06-01 16:00:53');



CREATE TABLE `tipos_seguro` (
  `idSeguro` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `descripcion` text DEFAULT NULL,
  `icono` varchar(100) NOT NULL DEFAULT '',
  `orden` int(11) NOT NULL DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_seguro`
--

INSERT INTO `tipos_seguro` (`idSeguro`, `nombre`, `slug`, `descripcion`, `icono`, `orden`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Seguro de Vida', 'seguro-vida', '', '', 1, 1, '2026-06-01 16:00:53', '2026-06-01 21:10:42'),
(3, 'Seguro Vehicular', 'seguro-vehicular', '', '', 3, 1, '2026-06-01 16:00:53', '2026-06-01 21:10:47');



CREATE TABLE `usuario_sucursal` (
  `id` int(11) NOT NULL,
  `idS` int(11) NOT NULL COMMENT 'id de la sucursal (sucursales.idS)',
  `idU` int(11) NOT NULL COMMENT 'id del asesor (tusuarios.idU, idRol=2)',
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `tipo_asignacion` enum('principal','secundaria') NOT NULL DEFAULT 'principal',
  `fecha_inicio` date DEFAULT NULL COMMENT 'fecha de inicio del contrato del asesor',
  `fecha_fin` date DEFAULT NULL COMMENT 'fecha de fin del contrato del asesor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Asesores asignados a cada sucursal';

--
-- Volcado de datos para la tabla `usuario_sucursal`
--

INSERT INTO `usuario_sucursal` (`id`, `idS`, `idU`, `estado`, `tipo_asignacion`, `fecha_inicio`, `fecha_fin`, `created_at`, `updated_at`) VALUES
(4, 6, 35, 0, 'principal', NULL, NULL, '2026-09-11 14:26:05', '2026-09-11 14:26:05'),
(6, 6, 8, 1, 'principal', NULL, NULL, '2026-09-11 14:26:05', '2026-09-11 14:26:05'),
(12, 1, 35, 1, 'principal', NULL, NULL, '2026-09-11 16:45:18', '2026-09-11 16:45:18'),
(13, 1, 32, 1, 'principal', NULL, NULL, '2026-09-11 16:45:18', '2026-09-11 16:45:18'),
(14, 1, 8, 1, 'principal', NULL, NULL, '2026-09-11 16:45:18', '2026-09-11 16:45:18'),
(15, 1, 34, 1, 'principal', NULL, NULL, '2026-09-11 16:45:18', '2026-09-11 16:45:18');



CREATE TABLE `rol` (
  `id` int(11) NOT NULL,
  `tipo` varchar(15) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `tipo`, `descripcion`, `estado`) VALUES
(1, 'Super Admin', 'Acceso total al sistema', 1),
(2, 'Asesor', 'Asesor crediticio', 1),
(3, 'Cliente', 'Cliente', 1),
(4, 'Postulante', 'Postulante a convocatoria', 1),
(5, 'admin_personal', 'Personal administrativo', 1),
(6, 'Soporte Web', 'Soporte de contenido web', 1),
(7, 'Plataforma', 'Validacion de pagos', 1),
(8, 'Gerente', 'Gestiona clientes, creditos, evaluaciones y reportes (alcance propio)', 1);



-- â”€â”€ Tablas NO encontradas en el dump: DDL inferido del codigo que las usa â”€â”€
-- sucursal_metodo_pago: usada en SucursalesModel::getMetodosPagoDeSucursal (join bank_accounts b / sucursal_metodo_pago sm por banco_id; columnas sm.idS, sm.estado, sm.orden)
CREATE TABLE `sucursal_metodo_pago` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idS` int NOT NULL,
  `banco_id` int NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_smp_suc` (`idS`),
  KEY `idx_smp_banco` (`banco_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- pago_comprobantes: usada en PagoCliente::comprobantes/registrarPago y ClienteController::pagarDeuda (columnas idU, idCG, deuda_id, deuda_empresa_id, receptor_empresa_id, sucursal_id, banco_id, monto, fecha_pago, nro_operacion, comprobante, estado, creado_en)
CREATE TABLE `pago_comprobantes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idU` int DEFAULT NULL,
  `idCG` int NOT NULL,
  `deuda_id` int NOT NULL,
  `deuda_empresa_id` int DEFAULT NULL,
  `receptor_empresa_id` int DEFAULT NULL,
  `sucursal_id` int DEFAULT NULL,
  `banco_id` int DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fecha_pago` date DEFAULT NULL,
  `nro_operacion` varchar(100) DEFAULT NULL,
  `comprobante` varchar(255) DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `creado_en` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pc_clie` (`idCG`),
  KEY `idx_pc_deuda` (`deuda_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


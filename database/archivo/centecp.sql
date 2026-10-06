-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-10-2026 a las 20:33:46
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `centecp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad_negocio`
--

CREATE TABLE `actividad_negocio` (
  `id_actividad_negocio` int(11) NOT NULL,
  `id_datos_negocio` int(11) NOT NULL,
  `ruc` char(11) NOT NULL DEFAULT '00000000000',
  `razon_social` varchar(150) NOT NULL DEFAULT 'NO_APLICA',
  `nombre_establecimiento` varchar(200) NOT NULL DEFAULT 'NO_APLICA',
  `actividad` text NOT NULL DEFAULT 'NO_APLICA',
  `reside_desde` date NOT NULL,
  `antiguedad_anios` int(11) NOT NULL DEFAULT 0,
  `antiguedad_meses` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_credito`
--

CREATE TABLE `cliente_credito` (
  `id_cliente_credito` int(11) NOT NULL,
  `id_persona_cliente` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `id_credito` int(11) NOT NULL,
  `codigo_cliente` char(8) NOT NULL,
  `tipo_persona` enum('PERSONA NATURAL','PERSONA JURIDICA') NOT NULL DEFAULT 'PERSONA NATURAL',
  `tipo_documento_momento` enum('DNI','CE') NOT NULL DEFAULT 'DNI',
  `numero_documento_momento` varchar(15) NOT NULL,
  `ruc_momento` varchar(11) DEFAULT 'NO_APLICA',
  `nombres_momento` varchar(100) NOT NULL,
  `apellido_paterno_momento` varchar(100) NOT NULL,
  `apellido_materno_momento` varchar(100) NOT NULL,
  `sexo_momento` enum('M','F') NOT NULL DEFAULT 'M',
  `tipo_cliente_momento` enum('NUEVO','RECURRENTE') NOT NULL DEFAULT 'NUEVO',
  `correo_momento` varchar(255) NOT NULL DEFAULT 'NO_APLICA',
  `telefono_primero_momento` varchar(15) NOT NULL,
  `telefono_segundo_momento` varchar(15) NOT NULL DEFAULT 'NO_APLICA',
  `ocupacion_momento` varchar(100) NOT NULL,
  `fecha_nacimiento_momento` date NOT NULL,
  `lugar_nacimiento_momento` varchar(200) NOT NULL,
  `id_situacion_civil_momento` int(11) NOT NULL,
  `id_grado_estudio_momento` int(11) NOT NULL,
  `numero_hijos_momento` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `numero_dependientes_momento` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `latitud_momento` varchar(30) DEFAULT NULL,
  `longitud_momento` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_foto`
--

CREATE TABLE `cliente_foto` (
  `id_foto` int(11) NOT NULL,
  `id_persona_cliente` int(11) NOT NULL,
  `tipo_foto` enum('PERFIL_CLIENTE','DNI_FRONTAL','DNI_REVERSA','CLIENTE_CON_ASESOR','RECIBO_LUZ','CROQUIS_NEGOCIO','NEGOCIO_FRONTAL','NEGOCIO_IZQUIERDO','NEGOCIO_DERECHO','DOMICILIO_FRONTAL','DOMICILIO_IZQUIERDO','DOMICILIO_DERECHO','VIVIENDA_NEGOCIO_FRONTAL','VIVIENDA_NEGOCIO_IZQUIERDO','VIVIENDA_NEGOCIO_DERECHO','VIVIENDA_NEGOCIO_RECIBO_LUZ') NOT NULL,
  `url_foto` varchar(255) NOT NULL,
  `nombre_foto` varchar(100) NOT NULL,
  `fecha_subida` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_vinculo`
--

CREATE TABLE `cliente_vinculo` (
  `id_vinculo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_persona_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_mora`
--

CREATE TABLE `configuracion_mora` (
  `id_config_mora` int(11) NOT NULL,
  `monto_min` decimal(10,2) NOT NULL,
  `monto_max` decimal(10,2) DEFAULT NULL,
  `valor_mora` decimal(5,2) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_tasa_interes`
--

CREATE TABLE `configuracion_tasa_interes` (
  `id_config_interes` int(11) NOT NULL,
  `id_pago` int(11) NOT NULL,
  `cuotas_min` int(11) NOT NULL,
  `cuotas_max` int(11) NOT NULL,
  `dias_limite` int(11) DEFAULT NULL,
  `porcentaje_interes` decimal(5,2) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `credito`
--

CREATE TABLE `credito` (
  `id_credito` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `credito`
--

INSERT INTO `credito` (`id_credito`, `tipo`) VALUES
(1, 'CREDI MICRO'),
(2, 'CREDI VEHICULO'),
(3, 'CREDI PRENDARIO'),
(4, 'CREDI SERVICIO'),
(5, 'CREDI PRODUCCIÓN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_negocio`
--

CREATE TABLE `datos_negocio` (
  `id_datos_negocio` int(11) NOT NULL,
  `id_persona_cliente` int(11) NOT NULL,
  `id_tipo_estructura` int(11) NOT NULL,
  `material` enum('MADERA','CEMENTO','ADOBE','OTROS') NOT NULL,
  `propiedad` enum('PROPIO','FAMILIAR','ALQUILADA','PREVENTA','EN USO') NOT NULL,
  `reside_desde` date NOT NULL,
  `nombre_arrendatario` varchar(100) NOT NULL DEFAULT 'NO_APLICA',
  `telefono_arrendatario` varchar(15) NOT NULL DEFAULT 'NO_APLICA',
  `direccion` varchar(150) NOT NULL,
  `urbanizacion` varchar(100) NOT NULL,
  `id_ubigeo` char(6) NOT NULL,
  `referencia` text NOT NULL,
  `descripcion` text NOT NULL,
  `numero_pisos` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `codigo_luz` varchar(20) NOT NULL DEFAULT '0000000000',
  `zona` enum('URBANIZACIÓN','PUEBLO JOVEN','ASOCIACIÓN','AA.HH','OTRO') NOT NULL,
  `zona_otro` varchar(100) NOT NULL DEFAULT 'NO_APLICA',
  `establecimiento` enum('MERCADO','LOCAL','GALERIA','ASOCIACIÓN','AMBULANTE','OTRO') NOT NULL,
  `establecimiento_otro` varchar(100) NOT NULL DEFAULT 'NO_APLICA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_negocio_servicio`
--

CREATE TABLE `datos_negocio_servicio` (
  `id_negocio_servicio` int(11) NOT NULL,
  `id_datos_negocio` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_simulacion`
--

CREATE TABLE `datos_simulacion` (
  `id_simulacion` int(11) NOT NULL,
  `id_persona_cliente` int(11) DEFAULT NULL,
  `dni` char(8) NOT NULL,
  `ruc` varchar(11) DEFAULT 'NO_APLICA',
  `razon_social` varchar(255) NOT NULL DEFAULT 'NO_APLICA',
  `nombres` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `celular` char(9) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `id_ubigeo` char(6) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_simulacion`
--

CREATE TABLE `detalle_simulacion` (
  `id_detalle` int(11) NOT NULL,
  `id_simulacion` int(11) NOT NULL,
  `id_negocio` int(11) DEFAULT NULL,
  `id_pago` int(11) NOT NULL,
  `id_config_interes` int(11) DEFAULT NULL,
  `id_config_mora` int(11) DEFAULT NULL,
  `monto_solicitado` decimal(10,2) NOT NULL,
  `cuotas` int(11) NOT NULL,
  `monto_cuota` decimal(10,2) NOT NULL,
  `total_pagar` decimal(10,2) NOT NULL,
  `tasa_interes` decimal(5,2) NOT NULL,
  `mora` decimal(5,2) NOT NULL,
  `estado` enum('PENDIENTE','APROBADO','RECHAZADO') DEFAULT 'PENDIENTE',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dia_laboral`
--

CREATE TABLE `dia_laboral` (
  `id_laboral` int(11) NOT NULL,
  `dia` varchar(100) NOT NULL,
  `estado` enum('SI LABORA','NO LABORA') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `dia_laboral`
--

INSERT INTO `dia_laboral` (`id_laboral`, `dia`, `estado`) VALUES
(1, 'LUNES', 'SI LABORA'),
(2, 'MARTES', 'SI LABORA'),
(3, 'MIÉRCOLES', 'SI LABORA'),
(4, 'JUEVES', 'SI LABORA'),
(5, 'VIERNES', 'SI LABORA'),
(6, 'SÁBADO', 'SI LABORA'),
(7, 'DOMINGO', 'NO LABORA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `id_estado` int(11) NOT NULL,
  `nombre_estado` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`id_estado`, `nombre_estado`) VALUES
(1, 'ACTIVO'),
(2, 'INACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `feriados`
--

CREATE TABLE `feriados` (
  `id_feriado` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `feriados`
--

INSERT INTO `feriados` (`id_feriado`, `fecha`, `descripcion`) VALUES
(1, '2026-01-01', 'Año Nuevo'),
(2, '2026-04-02', 'Jueves Santo'),
(3, '2026-04-03', 'Viernes Santo'),
(4, '2026-05-01', 'Día del Trabajo'),
(5, '2026-06-29', 'Día de San Pedro y San Pablo'),
(6, '2026-07-28', 'Fiestas Patrias'),
(7, '2026-07-29', 'Fiestas Patrias'),
(8, '2026-08-06', 'Batalla de Junín'),
(9, '2026-08-30', 'Santa Rosa de Lima'),
(10, '2026-10-08', 'Combate de Angamos'),
(11, '2026-11-01', 'Día de Todos los Santos'),
(12, '2026-12-08', 'Inmaculada Concepción'),
(13, '2026-12-09', 'Batalla de Ayacucho'),
(14, '2026-12-25', 'Navidad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grado_estudio`
--

CREATE TABLE `grado_estudio` (
  `id_grado_estudio` int(11) NOT NULL,
  `nombre_grado` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grado_estudio`
--

INSERT INTO `grado_estudio` (`id_grado_estudio`, `nombre_grado`) VALUES
(1, 'PRIMARIA'),
(2, 'SECUNDARIA'),
(3, 'TÉCNICO'),
(4, 'UNIVERSIDAD'),
(5, 'SIN INSTRUCCIÓN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona_cliente`
--

CREATE TABLE `persona_cliente` (
  `id_persona_cliente` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `tipo_documento` enum('DNI','CE') NOT NULL DEFAULT 'DNI',
  `numero_documento` varchar(15) NOT NULL,
  `ruc` varchar(11) DEFAULT 'NO_APLICA',
  `nombres` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) NOT NULL,
  `sexo` enum('M','F') NOT NULL DEFAULT 'M',
  `tipo_cliente` enum('NUEVO','RECURRENTE') NOT NULL DEFAULT 'NUEVO',
  `correo` varchar(255) NOT NULL DEFAULT 'NO_APLICA',
  `telefono_primero` varchar(15) NOT NULL,
  `telefono_segundo` varchar(15) NOT NULL DEFAULT 'NO_APLICA',
  `ocupacion` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `lugar_nacimiento` varchar(200) NOT NULL,
  `id_situacion_civil` int(11) NOT NULL,
  `id_grado_estudio` int(11) NOT NULL,
  `numero_hijos` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `numero_dependientes` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `latitud` varchar(30) DEFAULT NULL,
  `longitud` varchar(30) DEFAULT NULL,
  `estado_registro` enum('BORRADOR','INCOMPLETO','COMPLETADO') NOT NULL DEFAULT 'BORRADOR',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona_vinculo`
--

CREATE TABLE `persona_vinculo` (
  `id_vinculo` int(11) NOT NULL,
  `id_cliente_titular` int(11) NOT NULL,
  `id_cliente_vinculado` int(11) NOT NULL,
  `tipo_vinculo` enum('AVAL','CONYUGUE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre_rol`) VALUES
(1, 'ADMINISTRADOR'),
(2, 'ASESOR'),
(3, 'CLIENTE'),
(4, 'SOPORTE'),
(5, 'POSTULANTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `id_servicio` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`id_servicio`, `nombre_servicio`) VALUES
(1, 'LUZ'),
(2, 'AGUA POTABLE'),
(3, 'TELÉFONO'),
(4, 'CABLE'),
(5, 'INTERNET'),
(6, 'OTROS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `situacion_civil`
--

CREATE TABLE `situacion_civil` (
  `id_situacion_civil` int(11) NOT NULL,
  `nombre_estado` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `situacion_civil`
--

INSERT INTO `situacion_civil` (`id_situacion_civil`, `nombre_estado`) VALUES
(1, 'SOLTERO'),
(2, 'CASADO'),
(3, 'DIVORCIADO'),
(4, 'CONVIVIENTE'),
(5, 'VIUDO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `id_sucursal` int(11) NOT NULL,
  `codigo` char(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` text DEFAULT NULL,
  `id_ubigeo` char(6) NOT NULL,
  `latitud` varchar(30) DEFAULT NULL,
  `longitud` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_estructura`
--

CREATE TABLE `tipo_estructura` (
  `id_tipo_estructura` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_estructura`
--

INSERT INTO `tipo_estructura` (`id_tipo_estructura`, `tipo`) VALUES
(1, 'CASA-DEPARTAMENTO'),
(2, 'DEPARTAMENTO'),
(3, 'LOCAL COMERCIAL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_negocio`
--

CREATE TABLE `tipo_negocio` (
  `id_negocio` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_negocio`
--

INSERT INTO `tipo_negocio` (`id_negocio`, `nombre`) VALUES
(1, 'HOSPEDAJE'),
(2, 'TIENDA DE ROPAS'),
(3, 'LIBRERIA'),
(4, 'BODEGA / ABARROTES'),
(5, 'RESTAURANTE'),
(6, 'FERRETERÍA'),
(7, 'OTROS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_pago`
--

CREATE TABLE `tipo_pago` (
  `id_pago` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_pago`
--

INSERT INTO `tipo_pago` (`id_pago`, `tipo`) VALUES
(1, 'DIARIO'),
(2, 'SEMANAL'),
(3, 'QUINCENAL'),
(4, 'MENSUAL'),
(5, 'PAGO_UNICO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubigeo`
--

CREATE TABLE `ubigeo` (
  `id_ubigeo` char(6) NOT NULL,
  `departamento` varchar(50) NOT NULL,
  `provincia` varchar(50) NOT NULL,
  `distrito` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubigeo`
--

INSERT INTO `ubigeo` (`id_ubigeo`, `departamento`, `provincia`, `distrito`) VALUES
('100101', 'HUANUCO', 'HUANUCO', 'HUANUCO'),
('100102', 'HUANUCO', 'HUANUCO', 'AMARILIS'),
('100103', 'HUANUCO', 'HUANUCO', 'PILLCO MARCA'),
('120101', 'JUNIN', 'HUANCAYO', 'HUANCAYO'),
('120104', 'JUNIN', 'HUANCAYO', 'EL TAMBO'),
('120105', 'JUNIN', 'HUANCAYO', 'CHILCA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `dni` char(8) NOT NULL,
  `username` varchar(100) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_sucursal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `id_rol`, `id_estado`, `dni`, `username`, `correo`, `password`, `created_at`, `updated_at`, `id_sucursal`) VALUES
(1, 1, 1, '77328424', 'JESSICA MADELID', 'admin@centecp.com', '$2y$10$hW7UgkUEN.tX46NB2MlbgOWGiRbB1OI20bXC1qtQ2nXvW1CY0Hb8y', '2026-09-15 17:18:23', '2026-09-15 17:18:23', NULL),
(2, 2, 1, '12345678', 'TONY CALEB', 'asesor@asesor.com', '$2y$10$gAGf0KC9q0A9hdo8mBJPoO2E7NRZOTd3aD2xYl2Ri1L3RzTojjR1G', '2026-09-15 17:18:33', '2026-09-15 17:18:33', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vinculo_simulacion`
--

CREATE TABLE `vinculo_simulacion` (
  `id_vinculo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `id_detalle_simulacion` int(11) NOT NULL,
  `estado_gestion` enum('ASIGNADO','EN_REVISION','COMPLETADO','RECHAZADO') DEFAULT 'ASIGNADO',
  `observaciones` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividad_negocio`
--
ALTER TABLE `actividad_negocio`
  ADD PRIMARY KEY (`id_actividad_negocio`),
  ADD KEY `id_datos_negocio` (`id_datos_negocio`);

--
-- Indices de la tabla `cliente_credito`
--
ALTER TABLE `cliente_credito`
  ADD PRIMARY KEY (`id_cliente_credito`),
  ADD UNIQUE KEY `codigo_cliente` (`codigo_cliente`),
  ADD KEY `id_persona_cliente` (`id_persona_cliente`),
  ADD KEY `id_credito` (`id_credito`),
  ADD KEY `id_sucursal` (`id_sucursal`),
  ADD KEY `id_situacion_civil_momento` (`id_situacion_civil_momento`),
  ADD KEY `id_grado_estudio_momento` (`id_grado_estudio_momento`);

--
-- Indices de la tabla `cliente_foto`
--
ALTER TABLE `cliente_foto`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `id_persona_cliente` (`id_persona_cliente`);

--
-- Indices de la tabla `cliente_vinculo`
--
ALTER TABLE `cliente_vinculo`
  ADD PRIMARY KEY (`id_vinculo`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_persona_cliente` (`id_persona_cliente`);

--
-- Indices de la tabla `configuracion_mora`
--
ALTER TABLE `configuracion_mora`
  ADD PRIMARY KEY (`id_config_mora`);

--
-- Indices de la tabla `configuracion_tasa_interes`
--
ALTER TABLE `configuracion_tasa_interes`
  ADD PRIMARY KEY (`id_config_interes`),
  ADD KEY `fk_config_pago` (`id_pago`);

--
-- Indices de la tabla `credito`
--
ALTER TABLE `credito`
  ADD PRIMARY KEY (`id_credito`);

--
-- Indices de la tabla `datos_negocio`
--
ALTER TABLE `datos_negocio`
  ADD PRIMARY KEY (`id_datos_negocio`),
  ADD KEY `id_persona_cliente` (`id_persona_cliente`),
  ADD KEY `id_tipo_estructura` (`id_tipo_estructura`),
  ADD KEY `id_ubigeo` (`id_ubigeo`);

--
-- Indices de la tabla `datos_negocio_servicio`
--
ALTER TABLE `datos_negocio_servicio`
  ADD PRIMARY KEY (`id_negocio_servicio`),
  ADD KEY `id_datos_negocio` (`id_datos_negocio`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `datos_simulacion`
--
ALTER TABLE `datos_simulacion`
  ADD PRIMARY KEY (`id_simulacion`),
  ADD KEY `id_ubigeo` (`id_ubigeo`),
  ADD KEY `id_persona_cliente` (`id_persona_cliente`);

--
-- Indices de la tabla `detalle_simulacion`
--
ALTER TABLE `detalle_simulacion`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `fk_detalle_simulacion` (`id_simulacion`),
  ADD KEY `fk_detalle_negocio` (`id_negocio`),
  ADD KEY `fk_detalle_pago` (`id_pago`),
  ADD KEY `fk_detalle_config_interes` (`id_config_interes`),
  ADD KEY `fk_detalle_config_mora` (`id_config_mora`);

--
-- Indices de la tabla `dia_laboral`
--
ALTER TABLE `dia_laboral`
  ADD PRIMARY KEY (`id_laboral`),
  ADD UNIQUE KEY `dia` (`dia`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `feriados`
--
ALTER TABLE `feriados`
  ADD PRIMARY KEY (`id_feriado`),
  ADD UNIQUE KEY `fecha` (`fecha`);

--
-- Indices de la tabla `grado_estudio`
--
ALTER TABLE `grado_estudio`
  ADD PRIMARY KEY (`id_grado_estudio`);

--
-- Indices de la tabla `persona_cliente`
--
ALTER TABLE `persona_cliente`
  ADD PRIMARY KEY (`id_persona_cliente`),
  ADD UNIQUE KEY `numero_documento` (`numero_documento`),
  ADD KEY `id_situacion_civil` (`id_situacion_civil`),
  ADD KEY `id_grado_estudio` (`id_grado_estudio`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indices de la tabla `persona_vinculo`
--
ALTER TABLE `persona_vinculo`
  ADD PRIMARY KEY (`id_vinculo`),
  ADD KEY `id_cliente_titular` (`id_cliente_titular`),
  ADD KEY `id_cliente_vinculado` (`id_cliente_vinculado`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `situacion_civil`
--
ALTER TABLE `situacion_civil`
  ADD PRIMARY KEY (`id_situacion_civil`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`id_sucursal`),
  ADD KEY `id_ubigeo` (`id_ubigeo`);

--
-- Indices de la tabla `tipo_estructura`
--
ALTER TABLE `tipo_estructura`
  ADD PRIMARY KEY (`id_tipo_estructura`);

--
-- Indices de la tabla `tipo_negocio`
--
ALTER TABLE `tipo_negocio`
  ADD PRIMARY KEY (`id_negocio`);

--
-- Indices de la tabla `tipo_pago`
--
ALTER TABLE `tipo_pago`
  ADD PRIMARY KEY (`id_pago`);

--
-- Indices de la tabla `ubigeo`
--
ALTER TABLE `ubigeo`
  ADD PRIMARY KEY (`id_ubigeo`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_estado` (`id_estado`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indices de la tabla `vinculo_simulacion`
--
ALTER TABLE `vinculo_simulacion`
  ADD PRIMARY KEY (`id_vinculo`),
  ADD KEY `fk_vinculo_usuario` (`id_usuario`),
  ADD KEY `fk_vinculo_sucursal` (`id_sucursal`),
  ADD KEY `fk_vinculo_detalle_simulacion` (`id_detalle_simulacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividad_negocio`
--
ALTER TABLE `actividad_negocio`
  MODIFY `id_actividad_negocio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente_credito`
--
ALTER TABLE `cliente_credito`
  MODIFY `id_cliente_credito` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente_foto`
--
ALTER TABLE `cliente_foto`
  MODIFY `id_foto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente_vinculo`
--
ALTER TABLE `cliente_vinculo`
  MODIFY `id_vinculo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_mora`
--
ALTER TABLE `configuracion_mora`
  MODIFY `id_config_mora` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion_tasa_interes`
--
ALTER TABLE `configuracion_tasa_interes`
  MODIFY `id_config_interes` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `credito`
--
ALTER TABLE `credito`
  MODIFY `id_credito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `datos_negocio`
--
ALTER TABLE `datos_negocio`
  MODIFY `id_datos_negocio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `datos_negocio_servicio`
--
ALTER TABLE `datos_negocio_servicio`
  MODIFY `id_negocio_servicio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `datos_simulacion`
--
ALTER TABLE `datos_simulacion`
  MODIFY `id_simulacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_simulacion`
--
ALTER TABLE `detalle_simulacion`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `dia_laboral`
--
ALTER TABLE `dia_laboral`
  MODIFY `id_laboral` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `feriados`
--
ALTER TABLE `feriados`
  MODIFY `id_feriado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `grado_estudio`
--
ALTER TABLE `grado_estudio`
  MODIFY `id_grado_estudio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `persona_cliente`
--
ALTER TABLE `persona_cliente`
  MODIFY `id_persona_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona_vinculo`
--
ALTER TABLE `persona_vinculo`
  MODIFY `id_vinculo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `situacion_civil`
--
ALTER TABLE `situacion_civil`
  MODIFY `id_situacion_civil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `id_sucursal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_estructura`
--
ALTER TABLE `tipo_estructura`
  MODIFY `id_tipo_estructura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipo_negocio`
--
ALTER TABLE `tipo_negocio`
  MODIFY `id_negocio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tipo_pago`
--
ALTER TABLE `tipo_pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `vinculo_simulacion`
--
ALTER TABLE `vinculo_simulacion`
  MODIFY `id_vinculo` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividad_negocio`
--
ALTER TABLE `actividad_negocio`
  ADD CONSTRAINT `actividad_negocio_ibfk_1` FOREIGN KEY (`id_datos_negocio`) REFERENCES `datos_negocio` (`id_datos_negocio`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cliente_credito`
--
ALTER TABLE `cliente_credito`
  ADD CONSTRAINT `cliente_credito_ibfk_1` FOREIGN KEY (`id_persona_cliente`) REFERENCES `persona_cliente` (`id_persona_cliente`),
  ADD CONSTRAINT `cliente_credito_ibfk_2` FOREIGN KEY (`id_credito`) REFERENCES `credito` (`id_credito`),
  ADD CONSTRAINT `cliente_credito_ibfk_3` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id_sucursal`),
  ADD CONSTRAINT `cliente_credito_ibfk_4` FOREIGN KEY (`id_situacion_civil_momento`) REFERENCES `situacion_civil` (`id_situacion_civil`),
  ADD CONSTRAINT `cliente_credito_ibfk_5` FOREIGN KEY (`id_grado_estudio_momento`) REFERENCES `grado_estudio` (`id_grado_estudio`);

--
-- Filtros para la tabla `cliente_foto`
--
ALTER TABLE `cliente_foto`
  ADD CONSTRAINT `cliente_foto_ibfk_1` FOREIGN KEY (`id_persona_cliente`) REFERENCES `persona_cliente` (`id_persona_cliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cliente_vinculo`
--
ALTER TABLE `cliente_vinculo`
  ADD CONSTRAINT `cliente_vinculo_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `cliente_vinculo_ibfk_2` FOREIGN KEY (`id_persona_cliente`) REFERENCES `persona_cliente` (`id_persona_cliente`);

--
-- Filtros para la tabla `configuracion_tasa_interes`
--
ALTER TABLE `configuracion_tasa_interes`
  ADD CONSTRAINT `fk_config_pago` FOREIGN KEY (`id_pago`) REFERENCES `tipo_pago` (`id_pago`);

--
-- Filtros para la tabla `datos_negocio`
--
ALTER TABLE `datos_negocio`
  ADD CONSTRAINT `datos_negocio_ibfk_1` FOREIGN KEY (`id_persona_cliente`) REFERENCES `persona_cliente` (`id_persona_cliente`),
  ADD CONSTRAINT `datos_negocio_ibfk_2` FOREIGN KEY (`id_tipo_estructura`) REFERENCES `tipo_estructura` (`id_tipo_estructura`),
  ADD CONSTRAINT `datos_negocio_ibfk_3` FOREIGN KEY (`id_ubigeo`) REFERENCES `ubigeo` (`id_ubigeo`);

--
-- Filtros para la tabla `datos_negocio_servicio`
--
ALTER TABLE `datos_negocio_servicio`
  ADD CONSTRAINT `datos_negocio_servicio_ibfk_1` FOREIGN KEY (`id_datos_negocio`) REFERENCES `datos_negocio` (`id_datos_negocio`) ON DELETE CASCADE,
  ADD CONSTRAINT `datos_negocio_servicio_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`);

--
-- Filtros para la tabla `datos_simulacion`
--
ALTER TABLE `datos_simulacion`
  ADD CONSTRAINT `datos_simulacion_ibfk_1` FOREIGN KEY (`id_ubigeo`) REFERENCES `ubigeo` (`id_ubigeo`),
  ADD CONSTRAINT `datos_simulacion_ibfk_2` FOREIGN KEY (`id_persona_cliente`) REFERENCES `persona_cliente` (`id_persona_cliente`) ON DELETE SET NULL;

--
-- Filtros para la tabla `detalle_simulacion`
--
ALTER TABLE `detalle_simulacion`
  ADD CONSTRAINT `fk_detalle_config_interes` FOREIGN KEY (`id_config_interes`) REFERENCES `configuracion_tasa_interes` (`id_config_interes`),
  ADD CONSTRAINT `fk_detalle_config_mora` FOREIGN KEY (`id_config_mora`) REFERENCES `configuracion_mora` (`id_config_mora`),
  ADD CONSTRAINT `fk_detalle_negocio` FOREIGN KEY (`id_negocio`) REFERENCES `tipo_negocio` (`id_negocio`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_detalle_pago` FOREIGN KEY (`id_pago`) REFERENCES `tipo_pago` (`id_pago`),
  ADD CONSTRAINT `fk_detalle_simulacion` FOREIGN KEY (`id_simulacion`) REFERENCES `datos_simulacion` (`id_simulacion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `persona_cliente`
--
ALTER TABLE `persona_cliente`
  ADD CONSTRAINT `persona_cliente_ibfk_1` FOREIGN KEY (`id_situacion_civil`) REFERENCES `situacion_civil` (`id_situacion_civil`),
  ADD CONSTRAINT `persona_cliente_ibfk_2` FOREIGN KEY (`id_grado_estudio`) REFERENCES `grado_estudio` (`id_grado_estudio`),
  ADD CONSTRAINT `persona_cliente_ibfk_3` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id_sucursal`);

--
-- Filtros para la tabla `persona_vinculo`
--
ALTER TABLE `persona_vinculo`
  ADD CONSTRAINT `persona_vinculo_ibfk_1` FOREIGN KEY (`id_cliente_titular`) REFERENCES `persona_cliente` (`id_persona_cliente`),
  ADD CONSTRAINT `persona_vinculo_ibfk_2` FOREIGN KEY (`id_cliente_vinculado`) REFERENCES `persona_cliente` (`id_persona_cliente`);

--
-- Filtros para la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD CONSTRAINT `sucursal_ibfk_1` FOREIGN KEY (`id_ubigeo`) REFERENCES `ubigeo` (`id_ubigeo`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`),
  ADD CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`),
  ADD CONSTRAINT `usuario_ibfk_3` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id_sucursal`);

--
-- Filtros para la tabla `vinculo_simulacion`
--
ALTER TABLE `vinculo_simulacion`
  ADD CONSTRAINT `fk_vinculo_detalle_simulacion` FOREIGN KEY (`id_detalle_simulacion`) REFERENCES `detalle_simulacion` (`id_detalle`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vinculo_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id_sucursal`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vinculo_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

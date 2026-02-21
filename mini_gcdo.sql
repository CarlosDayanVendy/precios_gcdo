-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-02-2026 a las 16:02:09
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
-- Base de datos: `mini_gcdo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acuerdos_precio`
--

CREATE TABLE `acuerdos_precio` (
  `id` bigint(20) NOT NULL,
  `cliente_id` bigint(20) NOT NULL,
  `producto_id` bigint(20) NOT NULL,
  `precio_acordado` decimal(14,4) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `precio_minimo_referencia` decimal(14,4) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aplicaciones_detalle`
--

CREATE TABLE `aplicaciones_detalle` (
  `id` bigint(20) NOT NULL,
  `detalle_id` bigint(20) NOT NULL,
  `tipo_aplicacion` varchar(50) NOT NULL,
  `referencia_id` bigint(20) DEFAULT NULL,
  `cantidad_aplicada` decimal(14,3) NOT NULL,
  `precio_aplicado` decimal(14,4) NOT NULL,
  `monto_aplicado` decimal(14,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bolsas_producto`
--

CREATE TABLE `bolsas_producto` (
  `id` bigint(20) NOT NULL,
  `cliente_id` bigint(20) NOT NULL,
  `producto_id` bigint(20) NOT NULL,
  `cantidad_inicial` decimal(14,3) NOT NULL,
  `cantidad_disponible` decimal(14,3) NOT NULL,
  `precio_unitario_congelado` decimal(14,4) NOT NULL,
  `folio_sae_origen` varchar(100) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL,
  `estatus` varchar(50) DEFAULT 'activa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) NOT NULL,
  `nombre_comercial` varchar(255) NOT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `rfc` varchar(20) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_saldo`
--

CREATE TABLE `cuentas_saldo` (
  `id` bigint(20) NOT NULL,
  `cliente_id` bigint(20) NOT NULL,
  `saldo_actual` decimal(14,2) DEFAULT 0.00,
  `permite_negativo` tinyint(1) DEFAULT 1,
  `limite_credito` decimal(14,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_precios`
--

CREATE TABLE `lista_precios` (
  `id` bigint(20) NOT NULL,
  `producto_id` bigint(20) NOT NULL,
  `precio_publico` decimal(14,4) NOT NULL,
  `precio_minimo` decimal(14,4) NOT NULL,
  `precio_materialista` decimal(14,4) DEFAULT NULL,
  `precio_tiendas` decimal(14,4) DEFAULT NULL,
  `fecha_vigencia` datetime DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` bigint(20) NOT NULL,
  `cliente_id` bigint(20) NOT NULL,
  `folio_sae` varchar(100) NOT NULL,
  `tipo_movimiento` varchar(50) NOT NULL,
  `fecha_movimiento` datetime NOT NULL,
  `total_sae` decimal(14,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `tipo_documento_sae` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_saldo`
--

CREATE TABLE `movimientos_saldo` (
  `id` bigint(20) NOT NULL,
  `cliente_id` bigint(20) NOT NULL,
  `movimiento_id` bigint(20) DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `monto` decimal(14,2) NOT NULL,
  `fecha` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_detalle`
--

CREATE TABLE `movimiento_detalle` (
  `id` bigint(20) NOT NULL,
  `movimiento_id` bigint(20) NOT NULL,
  `producto_id` bigint(20) DEFAULT NULL,
  `descripcion_sae` varchar(255) NOT NULL,
  `cantidad` decimal(14,3) NOT NULL,
  `precio_unitario_sae` decimal(14,4) NOT NULL,
  `importe_sae` decimal(14,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento_evidencias`
--

CREATE TABLE `movimiento_evidencias` (
  `id` bigint(20) NOT NULL,
  `movimiento_id` bigint(20) NOT NULL,
  `archivo_url` text NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` bigint(20) NOT NULL,
  `codigo_sae` varchar(50) NOT NULL,
  `sku_interno` varchar(50) NOT NULL,
  `nombre_comercial` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `unidad` varchar(50) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acuerdos_precio`
--
ALTER TABLE `acuerdos_precio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_acuerdo_producto` (`producto_id`),
  ADD KEY `idx_acuerdo_cliente_prod` (`cliente_id`,`producto_id`,`activo`,`fecha_inicio`,`fecha_fin`);

--
-- Indices de la tabla `aplicaciones_detalle`
--
ALTER TABLE `aplicaciones_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_app_detalle_detalle` (`detalle_id`);

--
-- Indices de la tabla `bolsas_producto`
--
ALTER TABLE `bolsas_producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bolsa_producto` (`producto_id`),
  ADD KEY `idx_bolsa_fifo` (`cliente_id`,`producto_id`,`fecha_creacion`),
  ADD KEY `idx_bolsa_cliente_prod` (`cliente_id`,`producto_id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_clientes_activo` (`activo`);

--
-- Indices de la tabla `cuentas_saldo`
--
ALTER TABLE `cuentas_saldo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cliente_id` (`cliente_id`),
  ADD KEY `idx_saldo_cliente` (`cliente_id`);

--
-- Indices de la tabla `lista_precios`
--
ALTER TABLE `lista_precios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lp_producto_activo` (`producto_id`,`activo`,`fecha_vigencia`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mov_cliente` (`cliente_id`),
  ADD KEY `idx_mov_folio` (`folio_sae`),
  ADD KEY `idx_mov_tipo_fecha` (`tipo_movimiento`,`fecha_movimiento`);

--
-- Indices de la tabla `movimientos_saldo`
--
ALTER TABLE `movimientos_saldo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mov_saldo_cliente` (`cliente_id`),
  ADD KEY `idx_mov_saldo_mov` (`movimiento_id`);

--
-- Indices de la tabla `movimiento_detalle`
--
ALTER TABLE `movimiento_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_det_movimiento` (`movimiento_id`),
  ADD KEY `idx_det_producto` (`producto_id`),
  ADD KEY `idx_det_mov_prod` (`movimiento_id`,`producto_id`);

--
-- Indices de la tabla `movimiento_evidencias`
--
ALTER TABLE `movimiento_evidencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_evidencia_mov` (`movimiento_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_sae` (`codigo_sae`),
  ADD UNIQUE KEY `sku_interno` (`sku_interno`),
  ADD KEY `idx_productos_codigo_sae` (`codigo_sae`),
  ADD KEY `idx_productos_activo` (`activo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acuerdos_precio`
--
ALTER TABLE `acuerdos_precio`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aplicaciones_detalle`
--
ALTER TABLE `aplicaciones_detalle`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bolsas_producto`
--
ALTER TABLE `bolsas_producto`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cuentas_saldo`
--
ALTER TABLE `cuentas_saldo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lista_precios`
--
ALTER TABLE `lista_precios`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos_saldo`
--
ALTER TABLE `movimientos_saldo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimiento_detalle`
--
ALTER TABLE `movimiento_detalle`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimiento_evidencias`
--
ALTER TABLE `movimiento_evidencias`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `acuerdos_precio`
--
ALTER TABLE `acuerdos_precio`
  ADD CONSTRAINT `fk_acuerdo_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_acuerdo_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `aplicaciones_detalle`
--
ALTER TABLE `aplicaciones_detalle`
  ADD CONSTRAINT `fk_app_detalle` FOREIGN KEY (`detalle_id`) REFERENCES `movimiento_detalle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `bolsas_producto`
--
ALTER TABLE `bolsas_producto`
  ADD CONSTRAINT `fk_bolsa_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bolsa_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `cuentas_saldo`
--
ALTER TABLE `cuentas_saldo`
  ADD CONSTRAINT `fk_saldo_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `lista_precios`
--
ALTER TABLE `lista_precios`
  ADD CONSTRAINT `fk_lp_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `fk_mov_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos_saldo`
--
ALTER TABLE `movimientos_saldo`
  ADD CONSTRAINT `fk_mov_saldo_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mov_saldo_mov` FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimiento_detalle`
--
ALTER TABLE `movimiento_detalle`
  ADD CONSTRAINT `fk_det_mov` FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_det_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimiento_evidencias`
--
ALTER TABLE `movimiento_evidencias`
  ADD CONSTRAINT `fk_evidencia_mov` FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

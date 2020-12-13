-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 13-12-2020 a las 19:10:26
-- Versión del servidor: 5.7.31
-- Versión de PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dssd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

DROP TABLE IF EXISTS `actividades`;
CREATE TABLE IF NOT EXISTS `actividades` (
  `id_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `id_protocolo` int(11) NOT NULL,
  `estado` varchar(255) NOT NULL DEFAULT 'config',
  PRIMARY KEY (`id_actividad`),
  KEY `restric` (`id_protocolo`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id_actividad`, `nombre`, `id_protocolo`, `estado`) VALUES
(116, 'ac1', 93, 'config');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades_protocolos`
--

DROP TABLE IF EXISTS `actividades_protocolos`;
CREATE TABLE IF NOT EXISTS `actividades_protocolos` (
  `id_actividad` int(11) NOT NULL,
  `id_protocolo` int(11) NOT NULL,
  PRIMARY KEY (`id_protocolo`,`id_actividad`),
  KEY `fk_actividad` (`id_actividad`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `app_users`
--

DROP TABLE IF EXISTS `app_users`;
CREATE TABLE IF NOT EXISTS `app_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C2502824F85E0677` (`username`),
  UNIQUE KEY `UNIQ_C2502824E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `miembros_proyectos`
--

DROP TABLE IF EXISTS `miembros_proyectos`;
CREATE TABLE IF NOT EXISTS `miembros_proyectos` (
  `id_miembro` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_miembro`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `protocolos`
--

DROP TABLE IF EXISTS `protocolos`;
CREATE TABLE IF NOT EXISTS `protocolos` (
  `id_protocolo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `id_responsable` int(11) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `puntaje` int(11) NOT NULL,
  `id_proyecto` int(11) NOT NULL,
  `estado` varchar(255) NOT NULL,
  `es_local` int(11) DEFAULT NULL,
  `borrado` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_protocolo`),
  KEY `protocolo_responsable` (`id_responsable`),
  KEY `protocolo_proyecto` (`id_proyecto`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `protocolos`
--

INSERT INTO `protocolos` (`id_protocolo`, `nombre`, `id_responsable`, `fecha_inicio`, `fecha_fin`, `orden`, `puntaje`, `id_proyecto`, `estado`, `es_local`, `borrado`) VALUES
(1, 'protocolo_a', 4, '2020-12-01', '2020-12-02', 1, 8, 5, 'ejecutado', 0, 0),
(2, 'protocolo_b', 2, '2020-12-02', '2020-12-03', 2, 6, 5, 'completado', 0, 0),
(4, 'protocolo_d', 2, '2020-12-04', '2020-12-05', 1, 0, 5, 'pendiente', 1, 0),
(5, 'protocolo_e', 2, '2020-12-04', '2020-12-05', 1, 0, 5, 'pendiente', 1, 0),
(9, 'protocolo_d', 4, '2020-12-09', '2020-12-12', 1, 0, 7, 'pendiente', 1, 0),
(10, 'protocolo_a', 4, '2020-12-24', '2020-12-10', 1, 7, 8, 'completado', 1, 0),
(11, 'protocolo_b', 4, '2020-12-11', '2020-12-11', 1, 0, 8, 'pendiente', 1, 0),
(93, 'protocolo_a', 2, '2020-12-21', '2020-12-24', 1, 4, 93, 'completado', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `protocolos_ejecutados`
--

DROP TABLE IF EXISTS `protocolos_ejecutados`;
CREATE TABLE IF NOT EXISTS `protocolos_ejecutados` (
  `id_protocolo_ejecutado` int(11) NOT NULL AUTO_INCREMENT,
  `id_protocolo_ejecutado_relacion` int(11) NOT NULL,
  PRIMARY KEY (`id_protocolo_ejecutado`),
  KEY `protocolo_remoto` (`id_protocolo_ejecutado_relacion`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `protocolos_ejecutados`
--

INSERT INTO `protocolos_ejecutados` (`id_protocolo_ejecutado`, `id_protocolo_ejecutado_relacion`) VALUES
(23, 93);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

DROP TABLE IF EXISTS `proyectos`;
CREATE TABLE IF NOT EXISTS `proyectos` (
  `id_proyecto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `id_responsable` int(11) NOT NULL,
  `case_id` int(11) DEFAULT NULL,
  `borrado` int(2) NOT NULL DEFAULT '0',
  `estado` varchar(255) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_proyecto`),
  KEY `proyecto_responsable` (`id_responsable`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id_proyecto`, `nombre`, `fecha_inicio`, `fecha_fin`, `id_responsable`, `case_id`, `borrado`, `estado`, `orden`) VALUES
(5, 'vacuna_covid', '2020-12-01', '2020-12-05', 5, NULL, 0, 'configuracion', 1),
(7, 'vacuna_sarampion', '2020-12-06', '2020-12-12', 1, NULL, 0, '', 1),
(8, 'vacuna_ebola', '2020-12-13', '2020-12-19', 5, NULL, 0, 'configuracion', 1),
(93, 'vacuna_gg', '2020-12-16', '2020-12-26', 5, 15028, 0, 'tomar_decision', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `roles` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_2265B05DF85E0677` (`username`),
  UNIQUE KEY `UNIQ_2265B05DE7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `username`, `password`, `email`, `is_active`, `roles`) VALUES
(1, 'test', '123456', 'test', 1, 'jefe'),
(2, 'lucas', '123456', 'lucas@gmail.com', 1, 'responsable'),
(3, 'franco', '123456', 'franco@gmail.com', 1, 'responsable'),
(4, 'nahuel', '123456', 'nahuel@gmail.com', 1, 'responsable'),
(5, 'walter.bates', 'bpm', 'walter@gmail.com', 1, 'jefe');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `restric` FOREIGN KEY (`id_protocolo`) REFERENCES `protocolos` (`id_protocolo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `actividades_protocolos`
--
ALTER TABLE `actividades_protocolos`
  ADD CONSTRAINT `fk_actividad` FOREIGN KEY (`id_actividad`) REFERENCES `actividades` (`id_actividad`),
  ADD CONSTRAINT `fk_protocolo` FOREIGN KEY (`id_protocolo`) REFERENCES `protocolos` (`id_protocolo`);

--
-- Filtros para la tabla `protocolos`
--
ALTER TABLE `protocolos`
  ADD CONSTRAINT `protocolo_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id_proyecto`),
  ADD CONSTRAINT `protocolo_responsable` FOREIGN KEY (`id_responsable`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `protocolos_ejecutados`
--
ALTER TABLE `protocolos_ejecutados`
  ADD CONSTRAINT `protocolo_remoto` FOREIGN KEY (`id_protocolo_ejecutado_relacion`) REFERENCES `protocolos` (`id_protocolo`);

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `proyecto_responsable` FOREIGN KEY (`id_responsable`) REFERENCES `usuario` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-02-2016 a las 20:20:54
-- Versión del servidor: 5.6.17
-- Versión de PHP: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `gpswarehouse-poi`
--
CREATE DATABASE IF NOT EXISTS `gpswarehouse-poi` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `gpswarehouse-poi`;

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `markEndTrajectory`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `markEndTrajectory`(idTrajectory INTEGER)
BEGIN
DECLARE endTrajectory timestamp;
SET endTrajectory = (
	SELECT sf.timestamp FROM smartphonefixes sf
	WHERE sf.idTrajectory = 22
	ORDER BY sf.timestamp DESC
    );

UPDATE gpswarehouse_sp.trajectories t
SET t.endTime = endTrajectory
WHERE t.id = idTrajectory;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pois`
--

DROP TABLE IF EXISTS `pois`;
CREATE TABLE IF NOT EXISTS `pois` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idTrajectory` int(11) NOT NULL,
  `latitude` decimal(18,15) NOT NULL,
  `longitude` decimal(18,15) NOT NULL,
  `arrivalTime` datetime NOT NULL,
  `departureTime` datetime NOT NULL,
  `fixesInvolved` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=125 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `smartphonefixes`
--

DROP TABLE IF EXISTS `smartphonefixes`;
CREATE TABLE IF NOT EXISTS `smartphonefixes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idTrajectory` int(5) NOT NULL,
  `latitude` decimal(18,15) NOT NULL,
  `longitude` decimal(18,15) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_PerTrajectories` (`idTrajectory`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1855 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `smartphonefixes_temp`
--

DROP TABLE IF EXISTS `smartphonefixes_temp`;
CREATE TABLE IF NOT EXISTS `smartphonefixes_temp` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idTrajectory` int(5) NOT NULL,
  `latitude` decimal(18,15) NOT NULL,
  `longitude` decimal(18,15) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_PerTrajectories` (`idTrajectory`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2627 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trajectories`
--

DROP TABLE IF EXISTS `trajectories`;
CREATE TABLE IF NOT EXISTS `trajectories` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `startTime` datetime NOT NULL,
  `endTime` datetime NOT NULL,
  `minDistance` int(11) NOT NULL,
  `minTime` int(11) NOT NULL,
  `maxTime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `smartphonefixes`
--
ALTER TABLE `smartphonefixes`
  ADD CONSTRAINT `fk_PerTrajectories` FOREIGN KEY (`idTrajectory`) REFERENCES `trajectories` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

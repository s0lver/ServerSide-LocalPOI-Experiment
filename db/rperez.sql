-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 02-03-2016 a las 18:01:27
-- Versión del servidor: 5.5.40
-- Versión de PHP: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `rperez`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pois`
--
-- Creación: 16-02-2016 a las 16:01:57
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=388 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `smartphonefixes`
--
-- Creación: 02-03-2016 a las 23:38:24
--

DROP TABLE IF EXISTS `smartphonefixes`;
CREATE TABLE IF NOT EXISTS `smartphonefixes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idTrajectory` int(5) NOT NULL,
  `latitude` decimal(18,15) NOT NULL,
  `longitude` decimal(18,15) NOT NULL,
  `accuracy` decimal(18,15) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_PerTrajectories` (`idTrajectory`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7215 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `smartphonefixes_temp`
--
-- Creación: 02-03-2016 a las 23:38:57
--

DROP TABLE IF EXISTS `smartphonefixes_temp`;
CREATE TABLE IF NOT EXISTS `smartphonefixes_temp` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idTrajectory` int(5) NOT NULL,
  `latitude` decimal(18,15) NOT NULL,
  `longitude` decimal(18,15) NOT NULL,
  `accuracy` decimal(18,15) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_PerTrajectories` (`idTrajectory`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=615 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trajectories`
--
-- Creación: 16-02-2016 a las 16:01:57
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=70 ;

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

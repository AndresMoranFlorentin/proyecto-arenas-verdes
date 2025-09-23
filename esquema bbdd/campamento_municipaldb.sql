-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-09-2025 a las 18:49:28
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
-- Base de datos: `campamento_municipaldb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_tareas`
--

CREATE TABLE `config_tareas` (
  `id` int(11) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `config_tareas`
--

INSERT INTO `config_tareas` (`id`, `clave`, `valor`) VALUES
(1, 'ultima_ejecucion', '2025-09-23 18:32:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_pendientes`
--

CREATE TABLE `notificaciones_pendientes` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fecha_notificacion` datetime NOT NULL,
  `enviado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones_pendientes`
--

INSERT INTO `notificaciones_pendientes` (`id`, `nombre_completo`, `email`, `fecha_notificacion`, `enviado`) VALUES
(82, 'Andres Moran', 'moranandres729@gmail.com', '2025-07-05 00:00:00', 0),
(83, 'Andres Moran', 'moranandres729@gmail.com', '2025-07-05 00:00:00', 0),
(84, 'Andres Moran', 'moranandres729@gmail.com', '2025-07-05 00:00:00', 0),
(85, 'Andres Moran', 'moranandres729@gmail.com', '2025-07-05 00:00:00', 0),
(86, 'Andres Moran', 'moranandres729@gmail.com', '2025-07-05 00:00:00', 0),
(87, 'Andres Moran', 'moranandres729@gmail.com', '2025-07-17 00:00:00', 0),
(88, 'Andres Moran', 'moranandres729@gmail.com', '2025-07-17 00:00:00', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parcela`
--

CREATE TABLE `parcela` (
  `id` bigint(20) NOT NULL,
  `tipo_de_vehiculo` varchar(255) DEFAULT NULL,
  `imagen` text DEFAULT NULL,
  `id_servicio` bigint(20) NOT NULL,
  `sector` enum('Familiar','Carpa Fam','Joven','Motorhome') NOT NULL,
  `sanitarios_distancia` varchar(30) NOT NULL,
  `uso_espacio` varchar(35) NOT NULL,
  `cant_personas` int(11) NOT NULL DEFAULT 1,
  `disponible` enum('disponible','no disponible') NOT NULL DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `parcela`
--

INSERT INTO `parcela` (`id`, `tipo_de_vehiculo`, `imagen`, `id_servicio`, `sector`, `sanitarios_distancia`, `uso_espacio`, `cant_personas`, `disponible`) VALUES
(1, 'Casilla, Auto/Camioneta', NULL, 62, 'Familiar', 'Al lado', 'Casilla', 6, 'disponible'),
(2, 'Casilla, Auto/Camioneta', NULL, 62, 'Familiar', 'Al lado', 'Casilla', 6, 'disponible'),
(3, 'Casilla, Auto/Camioneta', NULL, 70, 'Familiar', 'Al lado', 'Casilla', 6, 'disponible'),
(4, 'Carpa, Auto/Camioneta', NULL, 68, 'Carpa Fam', 'Si', 'Carpa', 4, 'disponible'),
(5, 'Carpa, Auto/Camioneta', NULL, 62, 'Carpa Fam', 'Cerca', 'Carpa', 6, 'disponible'),
(6, 'Carpa, Auto/Camioneta', NULL, 62, 'Carpa Fam', 'Cerca', 'Carpa', 6, 'disponible'),
(7, 'Carpa/Casilla, Auto/Camioneta', NULL, 27, 'Carpa Fam', 'Cerca', 'Carpa/Casilla', 6, 'disponible'),
(8, 'Carpa, Auto/Camioneta', NULL, 60, 'Carpa Fam', 'Cerca', 'Carpa', 6, 'disponible'),
(9, 'Carpa, Auto/Camioneta', NULL, 60, 'Carpa Fam', 'Cerca', 'Carpa', 6, 'disponible'),
(10, 'Carpa, Auto/Camioneta', NULL, 60, 'Carpa Fam', 'Cerca', 'Carpa', 6, 'disponible'),
(11, 'Carpa/Casilla, Auto/Camioneta', NULL, 62, 'Carpa Fam', 'Cerca', 'Carpa/Casilla', 6, 'disponible'),
(12, 'Carpa, Auto/Camioneta', NULL, 62, 'Carpa Fam', 'Cerca', 'Carpa', 6, 'disponible'),
(13, 'Carpa, Camioneta', NULL, 43, 'Carpa Fam', 'Cerca', 'Carpa', 10, 'disponible'),
(14, 'Carpa, Camioneta', NULL, 45, 'Carpa Fam', 'Cerca', 'Carpa', 10, 'disponible'),
(15, 'Carpa, Camioneta', NULL, 45, 'Carpa Fam', 'Cerca', 'Carpa', 10, 'disponible'),
(16, 'Carpa, Moto/Camioneta', NULL, 43, 'Carpa Fam', 'Cerca', 'Carpa', 4, 'disponible'),
(17, 'Carpa, Moto/Camioneta', NULL, 19, 'Carpa Fam', 'Cerca', 'Carpa', 8, 'disponible'),
(18, 'Carpa, Moto/Camioneta', NULL, 19, 'Carpa Fam', 'Cerca', 'Carpa', 8, 'disponible'),
(19, 'Carpa, Auto/Camioneta', NULL, 19, 'Carpa Fam', 'Cerca', 'Carpa', 10, 'disponible'),
(20, 'Carpa, Auto/Camioneta', NULL, 70, 'Carpa Fam', 'Cerca', 'Carpa', 4, 'disponible'),
(21, 'Carpa, Auto/Camioneta', NULL, 70, 'Carpa Fam', 'Cerca', 'Carpa', 10, 'disponible'),
(22, 'Carpa, Auto/Camioneta', NULL, 70, 'Carpa Fam', 'Cerca', 'Carpa', 10, 'disponible'),
(23, 'Carpa, Auto/Camioneta', NULL, 62, 'Familiar', 'Cerca', 'Carpa', 8, 'no disponible'),
(24, 'Carpa, Auto/Camioneta', NULL, 21, 'Familiar', 'Cerca', 'Carpa', 4, 'disponible'),
(25, 'Casilla, Auto/Camioneta', NULL, 70, 'Familiar', 'Cerca', 'Casilla', 6, 'disponible'),
(26, 'Casilla, Auto', NULL, 70, 'Familiar', 'Cerca', 'Casilla', 4, 'disponible'),
(27, 'Casilla, Camioneta', NULL, 70, 'Familiar', 'Cerca', 'Casilla', 4, 'disponible'),
(28, 'Casilla, Camioneta', NULL, 66, 'Familiar', 'Cerca', 'Casilla', 6, 'disponible'),
(29, 'Casilla, Camioneta', NULL, 70, 'Familiar', 'Cerca', 'Casilla', 8, 'disponible'),
(30, 'Casilla, Camioneta', NULL, 70, 'Familiar', 'Cerca', 'Casilla', 6, 'disponible'),
(31, 'Casilla, Camioneta', NULL, 70, 'Familiar', 'Cerca', 'Casilla', 10, 'disponible'),
(32, 'Casilla, Camioneta', NULL, 70, 'Familiar', 'Cerca', 'Casilla', 6, 'disponible'),
(33, 'Casilla, Camioneta', NULL, 67, 'Familiar', 'Cerca', 'Casilla', 13, 'disponible'),
(34, 'Casilla, Auto', NULL, 70, 'Familiar', 'Cerca', 'Casilla', 6, 'disponible'),
(35, 'Casilla, Camioneta', NULL, 70, 'Familiar', 'Cerca', 'Casilla', 10, 'disponible'),
(36, 'Casilla, Camioneta', NULL, 70, 'Familiar', 'Cerca', 'Casilla', 4, 'disponible'),
(37, 'Casilla, Camioneta', NULL, 70, 'Familiar', 'Cerca', 'Casilla', 6, 'disponible'),
(38, 'Casilla, Camioneta', NULL, 66, 'Familiar', 'Cerca', 'Casilla', 6, 'disponible'),
(39, 'Casilla, Camioneta', NULL, 66, 'Familiar', 'Cerca', 'Casilla', 2, 'disponible'),
(40, 'Carpa, Camioneta', NULL, 66, 'Joven', 'Cerca', 'Carpa', 10, 'disponible'),
(41, 'Carpa, Auto/Camioneta', NULL, 70, 'Joven', 'Cerca', 'Carpa', 10, 'disponible'),
(42, 'Carpa, Auto', NULL, 62, 'Joven', 'Cerca', 'Carpa', 8, 'disponible'),
(43, 'Carpa, Auto/Camioneta', NULL, 70, 'Joven', 'Cerca', 'Carpa', 10, 'disponible'),
(44, 'Carpa, Auto/Camioneta', NULL, 70, 'Joven', 'Cerca', 'Carpa', 6, 'disponible'),
(45, 'Carpa, Auto', NULL, 14, 'Joven', 'Cerca', 'Carpa', 6, 'disponible'),
(46, 'Carpa, Auto', NULL, 14, 'Joven', 'Cerca', 'Carpa', 6, 'disponible'),
(47, 'Carpa, Auto/Camioneta', NULL, 14, 'Joven', 'Cerca', 'Carpa', 15, 'disponible'),
(48, 'Carpa, Auto', NULL, 14, 'Joven', 'Cerca', 'Carpa', 2, 'disponible'),
(49, 'Carpa, Auto', NULL, 14, 'Joven', 'Cerca', 'Carpa', 4, 'disponible'),
(50, 'Carpa, Auto', NULL, 26, 'Joven', 'Cerca', 'Carpa', 2, 'disponible'),
(51, 'Carpa, Auto', NULL, 6, 'Joven', 'Cerca', 'Carpa', 4, 'disponible'),
(52, 'Carpa, Auto/Camioneta', NULL, 6, 'Joven', 'Cerca', 'Carpa', 6, 'disponible'),
(53, 'Carpa, Auto/Camioneta', NULL, 14, 'Joven', 'Cerca', 'Carpa', 10, 'disponible'),
(54, 'Carpa/Casilla, Auto/Camioneta', NULL, 14, 'Joven', 'Cerca', 'Carpa/Casilla', 10, 'disponible'),
(55, 'Carpa/Casilla, Auto/Camioneta', NULL, 14, 'Joven', 'Cerca', 'Carpa/Casilla', 10, 'disponible'),
(56, 'Carpa, Auto', NULL, 6, 'Joven', 'Cerca', 'Carpa', 4, 'disponible'),
(57, 'Carpa, Auto', NULL, 6, 'Joven', 'Cerca', 'Carpa', 4, 'disponible'),
(58, 'Carpa, Auto', NULL, 26, 'Joven', 'Cerca', 'Carpa', 6, 'disponible'),
(59, 'Carpa, Auto', NULL, 6, 'Joven', 'Cerca', 'Carpa', 4, 'disponible'),
(60, 'Carpa, Auto', NULL, 26, 'Joven', 'Cerca', 'Carpa', 4, 'disponible'),
(61, 'Carpa, Auto/Camioneta', NULL, 26, 'Joven', 'Cerca', 'Carpa', 12, 'disponible'),
(62, 'Carpa, Auto', NULL, 6, 'Joven', 'Cerca', 'Carpa', 4, 'disponible'),
(63, 'Carpa, Auto', NULL, 26, 'Joven', 'Cerca', 'Carpa', 4, 'disponible'),
(64, 'Carpa, Auto/Camioneta', NULL, 70, 'Joven', 'Cerca', 'Carpa', 8, 'disponible'),
(65, 'Carpa, Auto/Camioneta', NULL, 70, 'Joven', 'Cerca', 'Carpa', 6, 'disponible'),
(66, 'Carpa, Auto/Camioneta', NULL, 70, 'Joven', 'Cerca', 'Carpa', 8, 'disponible'),
(67, 'Casilla, Colectivo', NULL, 66, 'Joven', 'Cerca', 'Casilla', 8, 'disponible'),
(68, 'Casilla, Colectivo/Motorhome', NULL, 66, 'Joven', 'Cerca', 'Casilla', 8, 'disponible'),
(69, 'Casilla, Colectivo/Motorhome', NULL, 66, 'Joven', 'Cerca', 'Casilla', 8, 'disponible'),
(70, 'Casilla rural, Colectivo/Motorhome', NULL, 40, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible'),
(71, 'Casilla rural, Colectivo/Motorhome', NULL, 55, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible'),
(72, 'Casilla rural, Colectivo/Motorhome', NULL, 55, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible'),
(73, 'Casilla rural, Colectivo/Motorhome', NULL, 55, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible'),
(74, 'Casilla rural, Colectivo/Motorhome', NULL, 55, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible'),
(75, 'Casilla rural, Colectivo/Motorhome', NULL, 55, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible'),
(76, 'Casilla rural, Colectivo/Motorhome', NULL, 55, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible'),
(77, 'Casilla rural, Colectivo/Motorhome', NULL, 55, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible'),
(78, 'Casilla rural, Colectivo/Motorhome', NULL, 55, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible'),
(79, 'Casilla rural, Colectivo/Motorhome', NULL, 55, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible'),
(80, 'Casilla rural, Colectivo/Motorhome', NULL, 55, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible'),
(81, 'Casilla rural, Colectivo/Motorhome', NULL, 55, 'Motorhome', 'Cerca', 'Casilla', 10, 'disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires`) VALUES
(1, 4, '0dd3ded04f112ef72bd91fc9dbdc10733585f228fad35f695b10af84a88dd05b', '2025-05-23 19:44:28'),
(2, 4, '9df3e182897ffea9ba4cffa2adce6f5921f55f9436d5475921845c3b42f88624', '2025-06-18 20:57:13'),
(3, 4, '71d8f6bafcd10f563c2ff6d495140218d80f42b5784381a1ea3109d3df513c1f', '2025-06-30 19:22:56'),
(4, 4, 'fafec25e40a23deac62d7549f0d10ae7aa68c2e59e790e92ed881d0a4f4bd283', '2025-06-30 19:27:27'),
(5, 4, '17b0e90de156a1444a0bbc990ce6601b20bc06ed4f9c130d85f7bddf0d8076ea', '2025-07-01 00:21:17'),
(6, 4, 'f394c0d1a2d401e52f99eb6e2b0b233aaee216eaaa503769ac88e8ffcbedeeac', '2025-07-01 00:31:45'),
(7, 4, '73ae06c7672321f03ca78da44b2de064dd290ca6cb61dd63b0a1e2b1fcb7854d', '2025-07-01 00:33:16'),
(8, 4, '16c5817c85031a6d7af4df77c3ccea8d0498bdba084829255908ae339728f0bc', '2025-07-01 00:34:35'),
(9, 4, '731eb0c544944e9e9ddee5e82f9cfe0227faaada370809640dc575d8f994bfa1', '2025-07-01 00:37:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios`
--

CREATE TABLE `precios` (
  `id` bigint(20) NOT NULL,
  `edad_ninos4` float NOT NULL,
  `edad_ninos12` float NOT NULL,
  `edad_ninos20` float NOT NULL,
  `costo_estancia_xdia` float NOT NULL,
  `costo_ducha` float NOT NULL,
  `costo_sanitario` float NOT NULL,
  `costoxvehiculoxdia` float NOT NULL,
  `costoxcasillaxdia` float NOT NULL,
  `costoxmescasilla` float NOT NULL,
  `residente_local` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios`
--

INSERT INTO `precios` (`id`, `edad_ninos4`, `edad_ninos12`, `edad_ninos20`, `costo_estancia_xdia`, `costo_ducha`, `costo_sanitario`, `costoxvehiculoxdia`, `costoxcasillaxdia`, `costoxmescasilla`, `residente_local`) VALUES
(1, 0, 8500, 15000, 2500, 2000, 350, 2000, 5500, 110000, 0),
(2, 0, 1000, 2000, 850, 500, 200, 700, 2500, 70000, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `id` bigint(20) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `menores_de_4` int(20) NOT NULL DEFAULT 0,
  `menores_de_12` int(20) NOT NULL DEFAULT 0,
  `mayores_de_12` int(20) NOT NULL DEFAULT 1,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `tipo_vehiculo` varchar(50) DEFAULT NULL,
  `id_servicio` bigint(20) NOT NULL,
  `precio_total` float NOT NULL DEFAULT 0,
  `estado` enum('pendiente','confirmada','cancelada') DEFAULT 'pendiente',
  `identificador` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reserva`
--

INSERT INTO `reserva` (`id`, `id_usuario`, `menores_de_4`, `menores_de_12`, `mayores_de_12`, `fecha_inicio`, `fecha_fin`, `tipo_vehiculo`, `id_servicio`, `precio_total`, `estado`, `identificador`) VALUES
(228, 4, 4, 2, 3, '2025-07-01', '2025-07-06', 'Camioneta', 54, 47000, 'confirmada', 'D5062E28CAE29C9C9B87'),
(230, 4, 2, 2, 1, '2025-07-07', '2025-07-18', 'Auto', 5, 53900, 'confirmada', '9F8936C771B013AD3648'),
(232, 4, 2, 4, 2, '2025-08-11', '2025-08-17', 'Auto', 8, 46200, 'pendiente', 'E659A90DAA2212DD6F2A'),
(233, 4, 2, 4, 2, '2025-08-11', '2025-08-17', 'Auto', 14, 46200, 'pendiente', 'A39B695BF86B9A7BF44B'),
(234, 4, 3, 5, 3, '2025-08-11', '2025-08-17', 'Casilla', 19, 71400, 'pendiente', '5C007F4F8449DF0CFBA1');

--
-- Disparadores `reserva`
--
DELIMITER $$
CREATE TRIGGER `notificacion_fin_estancia_reservacion` AFTER INSERT ON `reserva` FOR EACH ROW BEGIN
    DECLARE email VARCHAR(255);
    DECLARE nombre VARCHAR(255); 
    DECLARE apellido VARCHAR(255);
    DECLARE nombre_completo VARCHAR(255);

    -- Obtener el email del usuario 
    SELECT u.email INTO email 
    FROM users AS u 
    WHERE u.id = NEW.id_usuario;

    -- Obtener el nombre y el apellido del usuario 
    SELECT u.nombre, u.apellido INTO nombre, apellido 
    FROM users AS u 
    WHERE u.id = NEW.id_usuario;

    -- Verificar que el email, nombre y apellido no sean NULL
    IF email IS NOT NULL AND nombre IS NOT NULL AND apellido IS NOT NULL THEN
        -- Combinar nombre y apellido 
        SET nombre_completo = CONCAT(nombre, ' ', apellido);

        -- Insertar en la tabla de notificaciones pendientes
        INSERT INTO notificaciones_pendientes (nombre_completo, email, fecha_notificacion)
        VALUES (nombre_completo, email, DATE_SUB(NEW.fecha_fin, INTERVAL 1 DAY));
    ELSE
        -- Lanzar un error si faltan datos
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El email, nombre o apellido no puede ser NULL';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva_parcela`
--

CREATE TABLE `reserva_parcela` (
  `id_reserva` bigint(20) NOT NULL,
  `id_parcela` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reserva_parcela`
--

INSERT INTO `reserva_parcela` (`id_reserva`, `id_parcela`) VALUES
(228, 22),
(230, 9),
(232, 70),
(233, 42),
(234, 33);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicioreserva`
--

CREATE TABLE `servicioreserva` (
  `id_servicio` bigint(20) NOT NULL,
  `con_fogon` tinyint(1) DEFAULT 0,
  `con_toma_electrica` tinyint(1) DEFAULT 0,
  `sombra` tinyint(1) DEFAULT 0,
  `agua` tinyint(1) DEFAULT NULL,
  `poder_estacionar` tinyint(1) NOT NULL,
  `con_ducha` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicioreserva`
--

INSERT INTO `servicioreserva` (`id_servicio`, `con_fogon`, `con_toma_electrica`, `sombra`, `agua`, `poder_estacionar`, `con_ducha`) VALUES
(1, 1, 1, 1, 0, 0, 1),
(2, 1, 0, 0, 1, 1, 0),
(3, 1, 1, 1, 1, 0, 0),
(5, 1, 0, 1, 0, 0, 0),
(6, 1, 1, 1, 1, 1, 0),
(7, 0, 0, 0, 0, 0, 0),
(8, 1, 0, 0, 0, 0, 0),
(9, 0, 1, 0, 0, 0, 0),
(10, 1, 1, 0, 0, 0, 0),
(11, 0, 0, 1, 0, 0, 0),
(12, 1, 0, 1, 0, 0, 0),
(13, 0, 1, 1, 0, 0, 0),
(14, 1, 1, 1, 0, 0, 0),
(15, 0, 0, 0, 1, 0, 0),
(16, 1, 0, 0, 1, 0, 0),
(17, 0, 1, 0, 1, 0, 0),
(18, 1, 1, 0, 1, 0, 0),
(19, 0, 0, 1, 1, 0, 0),
(20, 1, 0, 1, 1, 0, 0),
(21, 0, 1, 1, 1, 0, 0),
(22, 1, 1, 1, 1, 0, 0),
(23, 0, 0, 0, 0, 1, 0),
(24, 1, 0, 0, 0, 1, 0),
(25, 0, 1, 0, 0, 1, 0),
(26, 1, 1, 0, 0, 1, 0),
(27, 0, 0, 1, 0, 1, 0),
(28, 1, 0, 1, 0, 1, 0),
(29, 0, 1, 1, 0, 1, 0),
(30, 1, 1, 1, 0, 1, 0),
(31, 0, 0, 0, 1, 1, 0),
(32, 1, 0, 0, 1, 1, 0),
(33, 0, 1, 0, 1, 1, 0),
(34, 1, 1, 0, 1, 1, 0),
(35, 0, 0, 1, 1, 1, 0),
(36, 1, 0, 1, 1, 1, 0),
(37, 0, 1, 1, 1, 1, 0),
(38, 1, 1, 1, 1, 1, 0),
(39, 0, 0, 0, 0, 0, 1),
(40, 1, 0, 0, 0, 0, 1),
(41, 0, 1, 0, 0, 0, 1),
(42, 1, 1, 0, 0, 0, 1),
(43, 0, 0, 1, 0, 0, 1),
(44, 1, 0, 1, 0, 0, 1),
(45, 0, 1, 1, 0, 0, 1),
(46, 1, 1, 1, 0, 0, 1),
(47, 0, 0, 0, 1, 0, 1),
(48, 1, 0, 0, 1, 0, 1),
(49, 0, 1, 0, 1, 0, 1),
(50, 1, 1, 0, 1, 0, 1),
(51, 0, 0, 1, 1, 0, 1),
(52, 1, 0, 1, 1, 0, 1),
(53, 0, 1, 1, 1, 0, 1),
(54, 1, 1, 1, 1, 0, 1),
(55, 0, 0, 0, 0, 1, 1),
(56, 1, 0, 0, 0, 1, 1),
(57, 0, 1, 0, 0, 1, 1),
(58, 1, 1, 0, 0, 1, 1),
(59, 0, 0, 1, 0, 1, 1),
(60, 1, 0, 1, 0, 1, 1),
(61, 0, 1, 1, 0, 1, 1),
(62, 1, 1, 1, 0, 1, 1),
(63, 0, 0, 0, 1, 1, 1),
(64, 1, 0, 0, 1, 1, 1),
(65, 0, 1, 0, 1, 1, 1),
(66, 1, 1, 0, 1, 1, 1),
(67, 0, 0, 1, 1, 1, 1),
(68, 1, 0, 1, 1, 1, 1),
(69, 0, 1, 1, 1, 1, 1),
(70, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `rol` varchar(50) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `apellido` varchar(200) NOT NULL,
  `dni` int(11) NOT NULL,
  `phone` int(11) NOT NULL,
  `email` varchar(500) NOT NULL,
  `localidad` varchar(200) NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `rol`, `nombre`, `apellido`, `dni`, `phone`, `email`, `localidad`, `password`) VALUES
(1, 'super', 'Administrador', 'BaseCamp', 0, 0, 'base@gmail.com', 'Loberia', '$2a$12$sQbpXaU3IruUNF.BU7iKj.YeXS.DEJuUB1Jqx3VzaHQglC4i6L/Ee'),
(4, 'super', 'Andres', 'Moran', 44830876, 226289765, 'moranandres729@gmail.com', 'Loberia', '$2y$10$gwZ55KYjReMRPeOCREdAH.EpROzh00Vwlq/MVk97Q6eEwUDWaqFDm'),
(5, 'user', 'Mateo', 'Foglionni', 44888777, 2147483647, 'mateofoglionni@gmail.com', 'Necochea', 'Maqueta'),
(7, 'admin', 'lautaro', 'Lamenza', 2132312321, 34779043, 'flakolobizon@gmail.com', 'Loberia', '$2y$10$srQTkYfI4BLfXAN0uwTHIOuD5OXl4MyKqWUwr5zuGRPfqi/jGhHoC');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `config_tareas`
--
ALTER TABLE `config_tareas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `notificaciones_pendientes`
--
ALTER TABLE `notificaciones_pendientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `parcela`
--
ALTER TABLE `parcela`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `precios`
--
ALTER TABLE `precios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identificador` (`identificador`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `FK_Reserva_ServicioReserva` (`id_servicio`);

--
-- Indices de la tabla `reserva_parcela`
--
ALTER TABLE `reserva_parcela`
  ADD PRIMARY KEY (`id_reserva`,`id_parcela`),
  ADD KEY `id_parcela` (`id_parcela`);

--
-- Indices de la tabla `servicioreserva`
--
ALTER TABLE `servicioreserva`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `config_tareas`
--
ALTER TABLE `config_tareas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `notificaciones_pendientes`
--
ALTER TABLE `notificaciones_pendientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT de la tabla `parcela`
--
ALTER TABLE `parcela`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `precios`
--
ALTER TABLE `precios`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;

--
-- AUTO_INCREMENT de la tabla `servicioreserva`
--
ALTER TABLE `servicioreserva`
  MODIFY `id_servicio` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `parcela`
--
ALTER TABLE `parcela`
  ADD CONSTRAINT `parcela_ibfk_1` FOREIGN KEY (`id_servicio`) REFERENCES `servicioreserva` (`id_servicio`);

--
-- Filtros para la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `FK_Reserva_ServicioReserva` FOREIGN KEY (`id_servicio`) REFERENCES `servicioreserva` (`id_servicio`),
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reserva_parcela`
--
ALTER TABLE `reserva_parcela`
  ADD CONSTRAINT `reserva_parcela_ibfk_1` FOREIGN KEY (`id_reserva`) REFERENCES `reserva` (`id`),
  ADD CONSTRAINT `reserva_parcela_ibfk_2` FOREIGN KEY (`id_parcela`) REFERENCES `parcela` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

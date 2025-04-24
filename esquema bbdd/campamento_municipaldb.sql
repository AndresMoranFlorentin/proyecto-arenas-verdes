-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-03-2025 a las 20:33:08
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
(47, 'Andres Moran', 'moranandres729@gmail.com', '2025-03-14 00:00:00', 0),
(48, 'Andres Moran', 'moranandres729@gmail.com', '2025-03-17 00:00:00', 0),
(49, 'Andres Moran', 'moranandres729@gmail.com', '2025-04-04 00:00:00', 0),
(50, 'Andres Moran', 'moranandres729@gmail.com', '2025-03-21 00:00:00', 0),
(51, 'Mateo Foglionni', 'mateofoglionni@gmail.com', '2025-03-25 00:00:00', 0),
(52, 'Laula sofirio', 'sofini56j@gmail.com', '2025-04-04 00:00:00', 0),
(53, 'Laula sofirio', 'sofini56j@gmail.com', '2025-04-04 00:00:00', 0),
(54, 'Andres Moran', 'moranandres729@gmail.com', '2025-04-04 00:00:00', 0),
(56, 'Andres Moran', 'moranandres729@gmail.com', '2025-03-27 00:00:00', 0),
(57, 'Andres Moran', 'moranandres729@gmail.com', '2025-03-28 00:00:00', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parcela`
--

CREATE TABLE `parcela` (
  `id` bigint(20) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
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

INSERT INTO `parcela` (`id`, `descripcion`, `imagen`, `id_servicio`, `sector`, `sanitarios_distancia`, `uso_espacio`, `cant_personas`, `disponible`) VALUES
(1, 'parcela 1', 'https//imagencampamento1', 1, 'Joven', 'cerca', 'casilla', 6, 'disponible'),
(2, 'parcela 2', 'https//:url.com.servercampverdes', 6, 'Motorhome', 'lejos', 'camping', 8, 'disponible'),
(3, 'parcela 3', 'https://img.com/campamento_verde45.png', 5, 'Familiar', 'lejos', 'casilla', 10, 'disponible');

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
(1, 0, 1000, 1400, 700, 500, 100, 500, 1600, 32000, 0),
(2, 0, 300, 500, 250, 150, 50, 200, 700, 12000, 1);

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
  `estado` enum('pendiente','confirmada','cancelada') DEFAULT 'pendiente',
  `identificador` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reserva`
--

INSERT INTO `reserva` (`id`, `id_usuario`, `menores_de_4`, `menores_de_12`, `mayores_de_12`, `fecha_inicio`, `fecha_fin`, `tipo_vehiculo`, `id_servicio`, `estado`, `identificador`) VALUES
(199, 4, 1, 2, 3, '2025-03-21', '2025-03-29', 'auto', 1, 'pendiente', '63F8DF93A1B498E12107');

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
(199, 1);

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
(6, 1, 1, 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifa`
--

CREATE TABLE `tarifa` (
  `id_tarifa` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `precio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Admin', 'Administrador', 'BaseCamp', 0, 0, 'base@gmail.com', 'Loberia', '$2a$12$sQbpXaU3IruUNF.BU7iKj.YeXS.DEJuUB1Jqx3VzaHQglC4i6L/Ee'),
(4, 'user', 'Andres', 'Moran', 44830876, 2147483647, 'moranandres729@gmail.com', 'Loberia', '$2y$10$ka.UHO2FdC4wudt81ve.ruTT/bDKB/jzzg5XJXEh4Yq2J8DOVcrAy'),
(5, 'user', 'Mateo', 'Foglionni', 44888777, 2147483647, 'mateofoglionni@gmail.com', 'Necochea', 'Maqueta'),
(6, 'user', 'Laula', 'sofirio', 22333444, 2147483647, 'sofini56j@gmail.com', 'Balcarce', 'Nona');

--
-- Índices para tablas volcadas
--

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
-- Indices de la tabla `tarifa`
--
ALTER TABLE `tarifa`
  ADD PRIMARY KEY (`id_tarifa`);

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
-- AUTO_INCREMENT de la tabla `notificaciones_pendientes`
--
ALTER TABLE `notificaciones_pendientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `parcela`
--
ALTER TABLE `parcela`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `precios`
--
ALTER TABLE `precios`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT de la tabla `servicioreserva`
--
ALTER TABLE `servicioreserva`
  MODIFY `id_servicio` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tarifa`
--
ALTER TABLE `tarifa`
  MODIFY `id_tarifa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `borrar_reservaciones_vencidas` ON SCHEDULE EVERY 1 DAY STARTS '2025-03-10 14:52:46' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
  -- Eliminar primero las relaciones en reserva_parcela
  DELETE FROM reserva_parcela
  WHERE id_reserva IN (SELECT id FROM reserva WHERE fecha_fin < NOW());

  -- Luego eliminar las reservas vencidas
  DELETE FROM reserva WHERE fecha_fin < NOW();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

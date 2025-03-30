-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-03-2025 a las 02:31:39
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `taller_moto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contraseña` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin`
--

INSERT INTO `admin` (`id`, `usuario`, `contraseña`) VALUES
(1, 'admin', '$2y$10$iea226b.FqkNnk85OuWMU.vbryuiLfFqq3/qUlONDoCg3kypBz./S');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id` int(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `dni` int(11) NOT NULL,
  `telefono` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id`, `nombre`, `dni`, `telefono`) VALUES
(52, 'vicky esa vanesa', 4356288, 2494511655),
(55, 'Niquito Shcherbyna', 44335336, 2494678950),
(56, 'agustin pedersen', 35544332, 234567988),
(57, 'Myriam Buena', 25334335, 2494670046),
(58, 'agustin pedersen', 42944838, 2494511633);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `moto`
--

CREATE TABLE `moto` (
  `id` int(255) NOT NULL,
  `modelo` varchar(200) NOT NULL,
  `patente` varchar(40) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `descripcion` varchar(300) DEFAULT NULL,
  `observaciones` varchar(300) DEFAULT NULL,
  `kilometros` int(110) DEFAULT NULL,
  `dni` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `moto`
--

INSERT INTO `moto` (`id`, `modelo`, `patente`, `estado`, `descripcion`, `observaciones`, `kilometros`, `dni`) VALUES
(94, 'keller 1500', '3456773', 'en_reparacion', 'muy linda', '8392', 1111000, 4356288),
(97, 'tornadoasas', '344566', 'Entregada', 'vino rayada', 'linda', 30000, 44335336),
(98, 'wave rojo', '4664738', 'en_reparacion', 'sdhnsdbh', 'bshdbs', 89898, 35544332),
(99, 'Gilera', '12345678', 'en_reparacion', 'buena moto', 'esta sucia', 1111111111, 25334335),
(100, 'zanella hot', '4489ASD', 'Entregada', '', '', 0, 42944838);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turno`
--

CREATE TABLE `turno` (
  `id` int(100) NOT NULL,
  `ingreso` date NOT NULL,
  `entrega` date NOT NULL,
  `patente` varchar(30) NOT NULL,
  `hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turno`
--

INSERT INTO `turno` (`id`, `ingreso`, `entrega`, `patente`, `hora`) VALUES
(33, '2025-03-20', '2025-03-20', '12345678', '2025-03-20 14:00:00'),
(34, '2025-03-21', '2222-12-12', '222222', '2025-03-21 13:30:00'),
(35, '2025-03-19', '2222-12-12', '3456773', '2025-03-19 13:00:00'),
(36, '2025-03-20', '0012-12-12', '233455', '2025-03-20 13:00:00'),
(37, '2025-03-21', '2222-02-12', '3456322', '2025-03-21 12:00:00'),
(38, '2025-03-20', '2222-12-12', '344566', '2025-03-20 15:00:00'),
(39, '2025-03-18', '3332-02-23', '4664738', '2025-03-18 13:30:00'),
(40, '2025-03-20', '2211-12-12', '12345678', '2025-03-20 14:00:00'),
(41, '2025-03-21', '0000-00-00', '4489ASD', '2025-03-21 12:30:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- Indices de la tabla `moto`
--
ALTER TABLE `moto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patente` (`patente`),
  ADD KEY `fk_dni` (`dni`);

--
-- Indices de la tabla `turno`
--
ALTER TABLE `turno`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `moto`
--
ALTER TABLE `moto`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `turno`
--
ALTER TABLE `turno`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `moto`
--
ALTER TABLE `moto`
  ADD CONSTRAINT `moto_ibfk_1` FOREIGN KEY (`dni`) REFERENCES `cliente` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

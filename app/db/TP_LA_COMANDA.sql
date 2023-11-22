-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 21-11-2023 a las 21:46:37
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `TP_LA_COMANDA`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Comanda`
--

CREATE TABLE `Comanda` (
  `ID` int(11) NOT NULL,
  `Fecha` varchar(10) NOT NULL,
  `Hora` varchar(10) NOT NULL,
  `ID_Mesa` int(11) NOT NULL,
  `ID_Empleado` int(11) NOT NULL,
  `ID_Pedido` int(11) NOT NULL,
  `Pedidos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`Pedidos`)),
  `NombreCliente` varchar(255) DEFAULT NULL,
  `FotoMesa` varchar(255) DEFAULT NULL,
  `Estado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Empleado`
--

CREATE TABLE `Empleado` (
  `ID` int(11) NOT NULL,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `Sector` varchar(50) DEFAULT NULL,
  `Clave` varchar(8) NOT NULL,
  `Estado` varchar(50) NOT NULL DEFAULT 'Activo',
  `Operaciones` int(11) NOT NULL DEFAULT 0,
  `FechaAlta` varchar(10) NOT NULL,
  `FechaBaja` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Empleado`
--

INSERT INTO `Empleado` (`ID`, `Nombre`, `Apellido`, `Sector`, `Clave`, `Estado`, `Operaciones`, `FechaAlta`, `FechaBaja`) VALUES
(4, 'Moe', 'Howard', 'Socio', 'pass1234', 'Activo', 0, '', NULL),
(5, 'Curly', 'Howard', 'Socio', 'pass1234', 'Activo', 0, '', NULL),
(6, 'Larry', 'Fine', 'Socio', 'pass1234', 'Activo', 0, '', NULL),
(8, 'Bruce', 'Wayne', 'Cocinero', '515', 'Activo', 1, '2023-11-21', NULL),
(9, 'Clint', 'Eastwood', 'Mozo', '515', 'Activo', 2, '2023-11-21', NULL),
(11, 'Chuck', 'Norris', 'Mozo', '515', 'Activo', 0, '2023-11-21', NULL),
(12, 'Steve', 'Vai', 'Mozo', '515', 'Activo', 0, '2023-11-21', NULL),
(13, 'Bugs', 'Bunny', 'Bartender', '515', 'Activo', 0, '2023-11-21', NULL),
(14, 'Emmet', 'Brown', 'Cocinero', '515', 'Activo', 1, '2023-11-21', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Encuesta`
--

CREATE TABLE `Encuesta` (
  `ID` int(11) NOT NULL,
  `ID_Comanda` int(11) NOT NULL,
  `PuntuacionMesa` int(11) NOT NULL,
  `PuntuacionMozo` int(11) NOT NULL,
  `PuntuacionCocinero` int(11) NOT NULL,
  `PuntuacionRestaurante` int(11) NOT NULL,
  `Fecha` varchar(10) NOT NULL,
  `Comentarios` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Encuesta`
--

INSERT INTO `Encuesta` (`ID`, `ID_Comanda`, `PuntuacionMesa`, `PuntuacionMozo`, `PuntuacionCocinero`, `PuntuacionRestaurante`, `Fecha`, `Comentarios`) VALUES
(3, 10, 7, 9, 10, 8, '2023-11-21', 'Re caro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Mesa`
--

CREATE TABLE `Mesa` (
  `ID` int(11) NOT NULL,
  `ID_Pedido` int(11) DEFAULT NULL,
  `Estado` varchar(50) NOT NULL,
  `ID_Empleado` int(11) DEFAULT NULL,
  `Cliente` varchar(50) DEFAULT NULL,
  `CodigoUnico` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Pedido`
--

CREATE TABLE `Pedido` (
  `ID` int(11) NOT NULL,
  `Productos` text NOT NULL,
  `ID_Mesa` int(11) NOT NULL,
  `CodigoUnico` varchar(5) NOT NULL,
  `Estado` varchar(50) NOT NULL DEFAULT 'Pedido',
  `TiempoEstimado` varchar(5) NOT NULL,
  `ValorTotal` float(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Producto`
--

CREATE TABLE `Producto` (
  `ID` int(11) NOT NULL,
  `Nombre` varchar(255) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Precio` float(10,2) NOT NULL,
  `Tiempo` varchar(5) NOT NULL,
  `Sector` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Producto`
--

INSERT INTO `Producto` (`ID`, `Nombre`, `Cantidad`, `Precio`, `Tiempo`, `Sector`) VALUES
(10, 'Hamburguesa', 284, 950.00, '00:15', 'Cocinero'),
(11, 'Pizza Margarita', 2054, 1200.00, '00:25', 'Cocinero'),
(12, 'Ensalada César', 802, 800.00, '00:10', 'Cocinero'),
(13, 'Pasta Alfredo', 180, 1000.00, '00:20', 'Cocinero'),
(14, 'Sushi Variado', 120, 1500.00, '00:30', 'Cocinero'),
(15, 'Refresco de Cola', 1912, 150.00, '00:00', 'Bartender'),
(16, 'Cerveza Artesanal', 2940, 300.00, '00:00', 'Cervecero'),
(17, 'Agua Mineral', 600, 50.00, '00:00', 'Bartender'),
(18, 'Polenta con salsa', 624, 350.00, '00:30', 'Cocinero'),
(19, 'Milanesa a caballo', 249, 1300.00, '00:40', 'Cocinero'),
(20, 'Hamburguesa de garbanzo', 522, 800.00, '00:10', 'Cocinero'),
(21, 'Daikiri', 303, 600.00, '00:05', 'Bartender'),
(22, 'Cerveza Corona', 1386, 700.00, '00:00', 'Cervecero');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Comanda`
--
ALTER TABLE `Comanda`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `Empleado`
--
ALTER TABLE `Empleado`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `Encuesta`
--
ALTER TABLE `Encuesta`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `Mesa`
--
ALTER TABLE `Mesa`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `Pedido`
--
ALTER TABLE `Pedido`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `Producto`
--
ALTER TABLE `Producto`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Comanda`
--
ALTER TABLE `Comanda`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `Empleado`
--
ALTER TABLE `Empleado`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `Encuesta`
--
ALTER TABLE `Encuesta`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `Mesa`
--
ALTER TABLE `Mesa`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `Pedido`
--
ALTER TABLE `Pedido`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `Producto`
--
ALTER TABLE `Producto`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

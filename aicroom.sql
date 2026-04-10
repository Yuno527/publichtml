-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 16-12-2025 a las 23:15:10
-- Versión del servidor: 11.8.3-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u158860862_aicroom`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_historial`
--

CREATE TABLE `tbl_historial` (
  `Id_historial` bigint(20) NOT NULL,
  `Id_UsuarioFK` bigint(20) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` enum('Completado','Cancelado','Abandonado') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbl_historial`
--

INSERT INTO `tbl_historial` (`Id_historial`, `Id_UsuarioFK`, `fecha`, `estado`) VALUES
(9, 6, '2025-11-29', 'Completado'),
(10, 9, '2025-11-29', 'Completado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_respuestas`
--

CREATE TABLE `tbl_respuestas` (
  `Id_respuesta` bigint(20) NOT NULL,
  `Id_historial` bigint(20) DEFAULT NULL,
  `pregunta` text DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  `puntaje` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbl_respuestas`
--

INSERT INTO `tbl_respuestas` (`Id_respuesta`, `Id_historial`, `pregunta`, `respuesta`, `puntaje`) VALUES
(31, 9, 'Si un compañero atraviesa un momento personal difícil, ¿cómo sueles responder?', 'Le ofrezco apoyo si me lo pide.', 2),
(32, 9, 'Cuando un grupo necesita dirección para avanzar, ¿cómo sueles contribuir?', 'Espero instrucciones claras antes de actuar.', 1),
(33, 9, 'Si debes trabajar con alguien con quien tuviste conflictos previos, ¿cómo procedes?', 'Manejo la relación con respeto y distancia.', 2),
(34, 9, 'Cuando te piden entregar algo con mucha prisa, comprometiendo calidad, ¿qué haces?', 'Intento balancear velocidad y calidad.', 2),
(35, 9, 'Si notas tensión o desacuerdo entre miembros del equipo, ¿qué acostumbras hacer?', 'Intento mediar si veo espacio para ello.', 2),
(36, 9, 'Cuando necesitas aprender una habilidad rápidamente, ¿qué haces?', 'Busco ayuda y practico cuando puedo.', 2),
(37, 9, 'Cuando alguien cuestiona tu idea en una reunión, ¿cómo respondes?', 'Aclaro mi punto para evitar malentendidos.', 2),
(38, 9, 'Si debes delegar trabajo, ¿cómo lo haces?', 'Asigno tareas de forma clara y doy seguimiento oportuno.', 3),
(39, 9, 'En una reunión con poca participación, ¿cómo contribuyes?', 'Me mantengo en silencio esperando instrucciones.', 1),
(40, 9, 'Cuando debes organizar varias tareas simultáneamente, ¿cómo gestionas tu tiempo?', 'Organizo parcialmente y ajusto sobre la marcha.', 2),
(41, 10, 'En ambientes de alta presión o cambios frecuentes, ¿cómo respondes?', 'Me ajusto rápido y busco soluciones prácticas.', 3),
(42, 10, 'Si notas que el equipo se apresura y podría cometer un error importante, ¿qué haces?', 'Presento los riesgos y propongo revisar antes de avanzar.', 3),
(43, 10, 'Si recibes una tarea nueva que requiere habilidades desconocidas, ¿qué haces primero?', 'Investigo, practico y pido retroalimentación para aprender rápido.', 3),
(44, 10, 'Cuando recibes tareas repetitivas, ¿cómo reaccionas?', 'Propongo mejoras o formas de hacerlas más eficientes.', 3),
(45, 10, 'Cuando hay un cambio inesperado de última hora, ¿cómo reaccionas?', 'Reorganizo mis planes rápidamente y sigo adelante.', 3),
(46, 10, 'Si un compañero atraviesa un momento personal difícil, ¿cómo sueles responder?', 'Me acerco, escucho y me muestro disponible.', 3),
(47, 10, 'Cuando un grupo necesita dirección para avanzar, ¿cómo sueles contribuir?', 'Tomo iniciativa y ayudo a organizar las tareas del grupo.', 3),
(48, 10, 'Cuando te piden entregar algo con mucha prisa, comprometiendo calidad, ¿qué haces?', 'Explico las limitaciones y propongo alternativas viables.', 3),
(49, 10, 'Si debes delegar trabajo, ¿cómo lo haces?', 'Asigno tareas de forma clara y doy seguimiento oportuno.', 3),
(50, 10, 'Cuando un cliente o usuario se comunica con tono tenso, ¿cómo manejas la situación?', 'Mantengo la calma, escucho y busco comprender la causa.', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_resultados`
--

CREATE TABLE `tbl_resultados` (
  `Id_resultado` bigint(20) NOT NULL,
  `Id_historiaLFK` bigint(20) DEFAULT NULL,
  `puntaje_total` int(100) DEFAULT NULL,
  `resultado_final` text DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbl_resultados`
--

INSERT INTO `tbl_resultados` (`Id_resultado`, `Id_historiaLFK`, `puntaje_total`, `resultado_final`, `fecha_registro`) VALUES
(4, 9, 19, 'Habilidad más fuerte: Espíritu colaborativo\nHabilidad más débil: Equilibrio personal\n\nAnálisis: Se observa un perfil con potencial y fortalezas en espíritu colaborativo. Las respuestas reflejan una actitud colaborativa y disposición para trabajar en conjunto. El área de equilibrio personal requiere atención para fortalecer el perfil profesional general. \n\nRecomendación: Tomar cursos de inteligencia emocional, practicar meditación y desarrollar habilidades de autorregulación.', '2025-11-29 02:29:00'),
(5, 10, 30, 'Habilidad más fuerte: Trabajo en equipo\nHabilidad más débil: Originalidad\n\nAnálisis: El análisis de las respuestas revela un perfil destacado, con fortalezas especialmente notables en trabajo en equipo. El perfil muestra habilidades para influir positivamente y generar resultados de alto impacto. Se recomienda enfocar esfuerzos en fortalecer originalidad para lograr un perfil aún más completo. \n\nRecomendación: Involucrarse en proyectos innovadores, experimentar con nuevas ideas y desarrollar capacidad de pensamiento original.', '2025-11-29 03:20:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tbl_usuario`
--

CREATE TABLE `tbl_usuario` (
  `Id_Usuario` bigint(20) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `contraseña` varchar(255) DEFAULT NULL,
  `correo` varchar(50) DEFAULT NULL,
  `empresa_donde_labora` varchar(100) DEFAULT NULL,
  `puesto` varchar(100) DEFAULT NULL,
  `fecha_registro` date DEFAULT NULL,
  `rol` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tbl_usuario`
--

INSERT INTO `tbl_usuario` (`Id_Usuario`, `nombre`, `contraseña`, `correo`, `empresa_donde_labora`, `puesto`, `fecha_registro`, `rol`) VALUES
(4, 'Breiner', '$2y$10$50YPy1munDAzdmnd9TZ2nu4LnPyhfVMA9aa7I5yAxUqJiTERvfCR2', 'breinerduran91@gmail.com', 'Aicroom', 'Ing Sistemas', '2025-10-30', 'usuario'),
(5, 'Geovani número 2', '$2y$10$mCwGbvtq6YHBe/UIswl/X.P6Rzrr.80.cn4ARTcs3s..rVEsUA9A.', 'instadhl542@gmail.com', 'Aicroom', 'Líder', '2025-10-31', 'admin'),
(6, 'Prueba', '$2y$10$pTzi1btY/nx5US5eIijwkeUYB28esynB29N45UlHYetYZag.iwgya', 'prueba@gmail.com', 'Prueba', 'Prueba', '2025-11-17', 'admin'),
(7, 'Daniela', '$2y$10$5n0KSpatiFEGW8Ru1JII2eBLXWNfEpXvtNVnWRgF0bZpb3Qf7UnpC', 'daniosorior15@gmail.com', 'Banco de occidente', 'Auxiliar operativo', '2025-11-28', 'usuario'),
(8, 'Juan', '$2y$10$Q1N6hokaDsW9GQClfltn1e5bD93Zb7BFc3eCsIHlxsa4iosC69.tW', 'breinerdanielduran12@gmail.com', 'Na', 'Na', '2025-11-29', 'usuario'),
(9, 'Saray', '$2y$10$A6SSFK3n1fsjPsyXE6vM3.Xrbu.Cej6ccaP/W5RzFvLmP/ANHhcI6', 'pemma9962@gmail.com', 'Aicroom', 'No', '2025-11-29', 'usuario');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tbl_historial`
--
ALTER TABLE `tbl_historial`
  ADD PRIMARY KEY (`Id_historial`),
  ADD KEY `Id_UsuarioFK` (`Id_UsuarioFK`);

--
-- Indices de la tabla `tbl_respuestas`
--
ALTER TABLE `tbl_respuestas`
  ADD PRIMARY KEY (`Id_respuesta`),
  ADD KEY `Id_historial` (`Id_historial`);

--
-- Indices de la tabla `tbl_resultados`
--
ALTER TABLE `tbl_resultados`
  ADD PRIMARY KEY (`Id_resultado`),
  ADD KEY `Id_historiaLFK` (`Id_historiaLFK`);

--
-- Indices de la tabla `tbl_usuario`
--
ALTER TABLE `tbl_usuario`
  ADD PRIMARY KEY (`Id_Usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tbl_historial`
--
ALTER TABLE `tbl_historial`
  MODIFY `Id_historial` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tbl_respuestas`
--
ALTER TABLE `tbl_respuestas`
  MODIFY `Id_respuesta` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `tbl_resultados`
--
ALTER TABLE `tbl_resultados`
  MODIFY `Id_resultado` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tbl_usuario`
--
ALTER TABLE `tbl_usuario`
  MODIFY `Id_Usuario` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tbl_historial`
--
ALTER TABLE `tbl_historial`
  ADD CONSTRAINT `tbl_historial_ibfk_1` FOREIGN KEY (`Id_UsuarioFK`) REFERENCES `tbl_usuario` (`Id_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tbl_respuestas`
--
ALTER TABLE `tbl_respuestas`
  ADD CONSTRAINT `tbl_respuestas_ibfk_1` FOREIGN KEY (`Id_historial`) REFERENCES `tbl_historial` (`Id_historial`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tbl_resultados`
--
ALTER TABLE `tbl_resultados`
  ADD CONSTRAINT `tbl_resultados_ibfk_1` FOREIGN KEY (`Id_historiaLFK`) REFERENCES `tbl_historial` (`Id_historial`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

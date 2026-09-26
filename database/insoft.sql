-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-09-2026 a las 01:04:36
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
-- Base de datos: `insoft`
--
CREATE DATABASE IF NOT EXISTS `insoft` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `insoft`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoriacurso`
--

CREATE TABLE `categoriacurso` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoriacurso`
--

INSERT INTO `categoriacurso` (`id_categoria`, `nombre`, `descripcion`) VALUES
(1, 'Diseño gráfico', 'Explora ideas, recursos y trabajos relacionados con el diseño gráfico. Encuentra inspiración para crear logos, afiches, ilustraciones y diferentes proyectos visuales.'),
(2, 'Programación', 'Un espacio para aprender, crear y compartir proyectos de programación. Aquí encontrarás actividades y recursos sobre Python, desarrollo web y creación de videojuegos con GameMaker, incluyendo proyectos realizados durante el curso.'),
(3, 'Contabilidad', 'Cursos orientados al aprendizaje de herramientas y conocimientos necesarios para la gestión contable, administrativa y financiera, utilizando herramientas informáticas.'),
(4, 'Ofimática', 'Cursos enfocados en el uso de herramientas informáticas para tareas de oficina, como procesadores de texto, hojas de cálculo, presentaciones, correo electrónico y gestión de archivos.'),
(5, 'Robótica', 'Cursos orientados al aprendizaje de robótica, electrónica y programación de dispositivos.'),
(6, 'Inteligencia Artificial', 'Cursos enfocados en el aprendizaje y aplicación de herramientas de inteligencia artificial.'),
(7, 'Bases de Datos', 'Cursos orientados al diseño, gestión y consulta de bases de datos.'),
(8, 'Programación Web', 'Cursos enfocados en el desarrollo de sitios y aplicaciones web.'),
(9, 'Marketing Digital', 'Cursos orientados al uso de herramientas y estrategias para la comunicación digital.'),
(10, 'Videojuegos', 'Cursos enfocados en el diseño, programación y desarrollo de videojuegos.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clase`
--

CREATE TABLE `clase` (
  `id_clase` int(11) NOT NULL,
  `titulo` varchar(20) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` date NOT NULL,
  `id_curso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clase`
--

INSERT INTO `clase` (`id_clase`, `titulo`, `descripcion`, `fecha`, `id_curso`) VALUES
(1, 'Introducción', 'Presentación del curso y conceptos iniciales.', '2026-09-01', 1),
(2, 'Primeros pasos', 'Introducción al desarrollo de videojuegos y sus elementos principales.', '2026-09-02', 2),
(3, 'Variables Python', 'Introducción a variables y tipos de datos en Python.', '2026-09-03', 3),
(4, 'Diseño básico', 'Principios básicos de composición y diseño visual.', '2026-09-04', 4),
(5, 'Conceptos contables', 'Introducción a los conceptos fundamentales de contabilidad.', '2026-09-05', 5),
(6, 'Herramientas Office', 'Uso de herramientas avanzadas de ofimática.', '2026-09-06', 6),
(7, 'Procesador de texto', 'Introducción al uso de procesadores de texto.', '2026-09-07', 7),
(8, 'Informática básica', 'Primeros pasos en el uso del ordenador y sus aplicaciones.', '2026-09-08', 8),
(9, 'Micro:bit', 'Introducción a la programación de la placa micro:bit.', '2026-09-09', 9),
(10, 'Introducción web', 'Conceptos básicos para la creación de sitios web.', '2026-09-10', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `id_curso` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `modalidad` varchar(50) NOT NULL,
  `duracion` varchar(50) NOT NULL,
  `descripcion` text NOT NULL,
  `costo` decimal(10,0) NOT NULL,
  `cupo` int(11) NOT NULL,
  `estado` varchar(30) NOT NULL,
  `img` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`id_curso`, `id_categoria`, `nombre`, `modalidad`, `duracion`, `descripcion`, `costo`, `cupo`, `estado`, `img`) VALUES
(1, 9, 'Marketing Digital', 'Presencial', '6 meses', 'Espacio dedicado a ideas, estrategias y recursos para promocionar proyectos en internet. Incluye redes sociales, creación de contenido, publicidad digital y herramientas para llegar a nuevas personas.\r\n', 8000, 10, 'En espera', 'marketing.jpg'),
(2, 10, 'Desarrollo de Videojuegos', 'Presencial', '6 meses', 'Un espacio para aprender a crear videojuegos desde cero. Encontrarás proyectos, actividades y recursos sobre programación, mecánicas, personajes, escenarios y desarrollo de juegos con herramientas como GameMaker.', 13800, 10, 'En espera', 'desarrollovideojuego.jpg'),
(3, 2, 'Programación Python Básico', 'Presencial', '6 meses', 'Un espacio para dar los primeros pasos en programación con Python. Aquí encontrarás ejercicios y recursos sobre variables, print, input, condicionales y otros conceptos fundamentales para comenzar a programar.', 9000, 10, 'En espera', 'python.jpg'),
(4, 1, 'Diseño Gráfico', 'Presencial', '6 meses', 'Un espacio para aprender y crear diseños visuales. Aquí encontrarás recursos y proyectos sobre logos, afiches, ilustraciones, edición de imágenes y composición gráfica.', 6000, 10, 'En espera', 'diseñografico.jpg'),
(5, 3, 'Operador PC Contable', 'Presencial', '9 meses', 'Se preparará al alumno para el área contable de una empresa, relacionando la administración con la contabilidad, preparándolo en la documentación comercial e insertándolo en el tema de registros contables por medios manuales; simultáneamente se ira incorporando una exploración general del software contable para introducir a los alumnos en el programa  a los efectos de familiarizarlos con las planillas de ingreso de datos y presentación de informes.', 9000, 15, 'Activo', 'OP Contable.png'),
(6, 4, 'Operador PC Avanzado', 'Presencial', '6 meses', 'El egresado tendrá conocimientos avanzados en el manejo de paquetes de ofimática, bases de datos y un amplio desempeño en el mantenimiento del PC, instalación de software, y trabajo con redes.', 9000, 10, 'Activo', 'OP Avanzado.png'),
(7, 4, 'Operador PC', 'Presencial', '4 meses', 'Preparar al alumno con conocimientos en el manejo del ordenador, capaz de desempeñar tareas mediante el uso de procesadores de textos, planilla de cálculo, presentación de diapositivas, así como también el manejo de correo electrónico y navegadores de Internet.', 6000, 10, 'Activo', 'OP.png'),
(8, 4, 'Operador PC Junior', 'Presencial', '6 meses', 'El alumno obtiene conocimientos básicos en el manejo del ordenador, en un nivel escolar tratando de enriquecer los conocimientos del niño en el desempeño de tareas con procesadores de texto, planilla de cálculo y presentaciones digitales, así como también el uso de correo electrónico y navegadores de Internet.', 8000, 15, 'Activo', 'OP Junior.png'),
(9, 5, 'Programación y Robótica con Micro:bit', 'Presencial', '6 meses', 'La finalidad de este plan es proporcionar los conocimientos necesarios para la programación de placas micro:bit, promoviendo su aplicación en proyectos de robótica básica. Asimismo, busca responder a la necesidad del instituto de contar con un curso de estas características.', 9000, 15, 'En espera', 'Prog Microbit.png'),
(10, 8, 'Programación Web', 'Presencial', '6 meses', 'Iniciar al estudiante en el lenguaje de programación PHP (Hypertext Pre-Processor), lo que permite crear sitios web con contenido dinámico.', 9000, 15, 'Activo', 'Prog Web.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `material`
--

CREATE TABLE `material` (
  `id_material` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `url_archivo` varchar(255) NOT NULL,
  `id_clase` int(11) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `material`
--

INSERT INTO `material` (`id_material`, `nombre`, `tipo`, `url_archivo`, `id_clase`, `descripcion`) VALUES
(1, 'Guía Marketing Digital', 'PDF', 'materiales/marketing-guia.pdf', 1, 'Material introductorio sobre marketing digital.'),
(2, 'Introducción Videojuegos', 'PDF', 'materiales/videojuegos-introduccion.pdf', 2, 'Material sobre los conceptos básicos del desarrollo de videojuegos.'),
(3, 'Variables Python', 'PDF', 'materiales/variables-python.pdf', 3, 'Guía práctica sobre variables y tipos de datos en Python.'),
(4, 'Principios del Diseño', 'PDF', 'materiales/principios-diseno.pdf', 4, 'Material sobre composición y principios básicos del diseño gráfico.'),
(5, 'Introducción Contabilidad', 'PDF', 'materiales/introduccion-contabilidad.pdf', 5, 'Material introductorio de conceptos contables.'),
(6, 'Guía Office Avanzado', 'PDF', 'materiales/office-avanzado.pdf', 6, 'Guía de herramientas avanzadas de ofimática.'),
(7, 'Procesador de Texto', 'PDF', 'materiales/procesador-texto.pdf', 7, 'Material para aprender a utilizar procesadores de texto.'),
(8, 'Informática Básica', 'PDF', 'materiales/informatica-basica.pdf', 8, 'Material introductorio sobre el uso del ordenador.'),
(9, 'Guía Micro:bit', 'PDF', 'materiales/guia-microbit.pdf', 9, 'Material introductorio para programación con micro:bit.'),
(10, 'Introducción PHP', 'PDF', 'materiales/introduccion-php.pdf', 10, 'Material introductorio sobre programación web con PHP.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula`
--

CREATE TABLE `matricula` (
  `id_matricula` int(11) NOT NULL,
  `fechamatricula` date NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `matricula`
--

INSERT INTO `matricula` (`id_matricula`, `fechamatricula`, `id_usuario`, `id_curso`) VALUES
(1, '2026-08-01', 7, 1),
(2, '2026-08-02', 8, 2),
(3, '2026-08-03', 9, 3),
(4, '2026-08-04', 10, 4),
(5, '2026-08-05', 11, 5),
(6, '2026-08-06', 12, 6),
(7, '2026-08-07', 13, 7),
(8, '2026-08-08', 14, 8),
(9, '2026-08-09', 15, 9),
(10, '2026-08-10', 16, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `monto` decimal(10,2) NOT NULL,
  `metodoPago` varchar(50) NOT NULL,
  `refPasarela` varchar(100) NOT NULL,
  `estado` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pago`
--

INSERT INTO `pago` (`id_pago`, `fecha`, `monto`, `metodoPago`, `refPasarela`, `estado`) VALUES
(1, '2026-08-01 13:15:00', 8000.00, 'Tarjeta', 'PAY-2026-0001', 'Aprobado'),
(2, '2026-08-02 14:20:00', 13800.00, 'Transferencia', 'PAY-2026-0002', 'Aprobado'),
(3, '2026-08-03 12:45:00', 9000.00, 'Tarjeta', 'PAY-2026-0003', 'Aprobado'),
(4, '2026-08-04 17:30:00', 6000.00, 'Efectivo', 'PAY-2026-0004', 'Aprobado'),
(5, '2026-08-05 19:10:00', 9000.00, 'Transferencia', 'PAY-2026-0005', 'Aprobado'),
(6, '2026-08-06 13:05:00', 9000.00, 'Tarjeta', 'PAY-2026-0006', 'Aprobado'),
(7, '2026-08-07 15:40:00', 6000.00, 'Efectivo', 'PAY-2026-0007', 'Pendiente'),
(8, '2026-08-08 18:25:00', 8000.00, 'Transferencia', 'PAY-2026-0008', 'Aprobado'),
(9, '2026-08-09 20:00:00', 9000.00, 'Tarjeta', 'PAY-2026-0009', 'Aprobado'),
(10, '2026-08-10 21:15:00', 9000.00, 'Transferencia', 'PAY-2026-0010', 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representantes_estudiantes`
--

CREATE TABLE `representantes_estudiantes` (
  `id_relacion` int(11) NOT NULL,
  `id_representante` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `parentesco` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `representantes_estudiantes`
--

INSERT INTO `representantes_estudiantes` (`id_relacion`, `id_representante`, `id_estudiante`, `parentesco`) VALUES
(1, 7, 8, 'Madre'),
(2, 8, 9, 'Padre'),
(3, 9, 10, 'Madre'),
(4, 10, 11, 'Padre'),
(5, 11, 12, 'Madre'),
(6, 12, 13, 'Padre'),
(7, 13, 14, 'Madre'),
(8, 14, 15, 'Padre'),
(9, 15, 16, 'Madre'),
(10, 16, 7, 'Padre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre`) VALUES
(1, 'Administrador'),
(4, 'Docente'),
(5, 'Estudiante'),
(6, 'Padre/Representante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` int(20) NOT NULL,
  `cedula_iv` binary(16) NOT NULL,
  `cedula_tag` binary(16) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `correo_iv` binary(16) NOT NULL,
  `correo_tag` binary(16) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `telefono_iv` binary(16) NOT NULL,
  `telefono_tag` binary(16) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha` date NOT NULL,
  `fecha_iv` binary(16) NOT NULL,
  `fecha_tag` binary(16) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `nombre`, `apellido`, `cedula`, `cedula_iv`, `cedula_tag`, `correo`, `correo_iv`, `correo_tag`, `telefono`, `telefono_iv`, `telefono_tag`, `password`, `fecha`, `fecha_iv`, `fecha_tag`, `id_rol`) VALUES
(7, 'DianaL2026', 'Diana', 'Leuchuk', 45830228, 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, 'dleuchuk@gmail.com', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, '', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, '123456789', '1993-03-04', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, 5),
(8, 'Yhadhira2026', 'Yhadhira', 'Buzzo', 58141545, 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, 'yhadhirabuzzo@gmail.com', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, '', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, '987654321', '2006-04-26', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, 5),
(9, 'JorgetallerInformatica', 'Jorge', 'Ferreira', 78374568, 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, 'jorgeferreira2026@gmail.com', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, '', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, 'jorge2345', '1985-03-09', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, 5),
(10, 'R2637', 'Felipe', 'Gomez', 56789145, 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, 'feliper2637@gmail.com', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, '', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, '$2y$10$iCXo64FHGerTLNK.R.kSyOIQ8DjhlBvTeUZopGrBOZroxglqV.Ne6', '1986-06-01', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, 5),
(11, 'JuanPerez', 'Juan', 'Perez', 78901067, 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, 'juancitoperez@gmail.com', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, '', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, '$2y$10$Gys3HI.e/x0PnYVx8sbi3eRY/I5gFtwLNo6D9ym4g6exON6aWYdra', '1993-09-08', 0x00000000000000000000000000000000, 0x00000000000000000000000000000000, 5),
(12, 'Roberto123', 'Roberto', 'Carlos', 0, 0xa00dce7e67ae356301d0d58200000000, 0x43f46a86ba4770285232f6b9d6a7ce56, '???	j???#@}GN?(??b\Z??', 0xc8f640bf3b3a8d768cf33d9600000000, 0x1aed27155be8c5114b2dbbd6b3b25e09, '%?3\\?x1dn', 0x5dc8b71d416a8be0bfc4cf6200000000, 0xe0994a92c855b0066641702863f26dca, '$argon2id$v=19$m=65536,t=4,p=1$Ni9xNktpQlpBOEdac3dwTQ$hkiYOcxUsQNHkZvySOikFc87IaPaX0oFkqSY+tWwwW4', '0000-00-00', 0xd416a92db3da9abcaa7bdcb800000000, 0x40cdc29f75e34741def0e8253a837095, 5),
(13, 'Roberto', 'Juan', 'Francisco', 0, 0x0da602ba4d931240628cdd2600000000, 0xa927428fcbab0fb0bd21df678f770f39, '?$?1??ee?*n???OZŇ', 0x1046036fab574ad7f759747600000000, 0xdf4e44719264fc47ad70e78af3d0f3d1, '????%f	?d', 0xcbd68df2fcdaf7e0ed35eed200000000, 0xcc7a20ed5f731fa666a8b28d07d694ba, '$argon2id$v=19$m=65536,t=4,p=1$ZFBsT1VjR3VDNWVJc0pBYw$b45plOPDUO/W5F9BHDHbRTVgvi1ihJ0AxNmQoePvojQ', '0000-00-00', 0x7d2a74d8196680a82a064f2000000000, 0xb3e0f46917beafd7c2b9d197ba8bea1b, 5),
(14, 'Lucas2026', 'Lucas', 'Rangel', 0, 0xd7dd7ba665d505782345e78400000000, 0x44741fa4c1e7d8c09565fdba29efd845, 'APT:[)*????\n}[?ރ???J', 0xd6d3da5dfb182ca87be6e8f000000000, 0x08d130c1b558615caa59415320c23c9b, '??)N?A?<?', 0x9774ac66d2c4715e0a81555900000000, 0x9ea1fb8bdbe5988bf95d0607a3172f10, '$argon2id$v=19$m=65536,t=4,p=1$emp6azQ5dVVwMjN4SEF4aA$TITmyzFybAycEiNE5xJ4OF3d3etcgFtyC8uvrFSVXQ8', '0000-00-00', 0x0f1e7fb0faeb02bbae6cca2800000000, 0x04b52a4f7bf693e9cf06fbb93ba116d6, 5),
(15, 'AlbertCole123', 'Alberto', 'Cole', 0, 0xe6e2b26e1b0680a1064e102400000000, 0xa1ded2efe75fab29e15f1a1c62763e6b, 'D??KY?K???????k$c', 0x6045d2274a60021e31313e4d00000000, 0xfedd10ee354e8c86ad9d3851c829af0d, 'w℠R[u?', 0x1696e51827e7e4e40740b5aa00000000, 0x108243d2cd88d1eebfd5bc6e2801c806, '$argon2id$v=19$m=65536,t=4,p=1$QVJwOVM1M1pmbk54enRRVw$dOZinWXPsfZO25HGFw6A2O7HAcPxKRBukF1w5Je39a8', '0000-00-00', 0xe0ec5f3e7a6b197b1a37ba1800000000, 0x8622e7be67fe83cb5dfd632fae234617, 5),
(16, 'Ernesto332', 'Ernesto', 'Jujui', 0, 0x799e7fcb7d32a011d335ca0000000000, 0xc62cea1521812c879199b212c25bb2c8, '?`]?.k?gC3!9W????5???h\\?', 0x0eb9d9f6bd06b4951529e9f700000000, 0x3a9111a38f8b61fbaace61f624beb84c, '7?ŗj???f', 0x4a8c230fd7eb14297994468500000000, 0xef749f5cea59c9a08fd8c06f74319f54, '$argon2id$v=19$m=65536,t=4,p=1$UVVudVVWV1NmemxrcU1MNQ$tTnXa/jASqy9NLCcQke8Aa8+ffforTowgEZsE2UX9L8', '0000-00-00', 0xbdd59b90a1fe4a9b9716630d00000000, 0xb3e8d165a0c8155ee8a7594f8307d09a, 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoriacurso`
--
ALTER TABLE `categoriacurso`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `clase`
--
ALTER TABLE `clase`
  ADD PRIMARY KEY (`id_clase`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id_curso`);

--
-- Indices de la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD PRIMARY KEY (`id_matricula`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id_pago`);

--
-- Indices de la tabla `representantes_estudiantes`
--
ALTER TABLE `representantes_estudiantes`
  ADD PRIMARY KEY (`id_relacion`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoriacurso`
--
ALTER TABLE `categoriacurso`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `clase`
--
ALTER TABLE `clase`
  MODIFY `id_clase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `matricula`
--
ALTER TABLE `matricula`
  MODIFY `id_matricula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `representantes_estudiantes`
--
ALTER TABLE `representantes_estudiantes`
  MODIFY `id_relacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

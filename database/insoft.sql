-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-10-2026 a las 19:00:56
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
-- Estructura de tabla para la tabla `administrativos`
--

CREATE TABLE `administrativos` (
  `nombre_usuario` varchar(50) NOT NULL,
  `nivel_acceso` varchar(30) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrativos`
--

INSERT INTO `administrativos` (`nombre_usuario`, `nivel_acceso`, `fecha_ingreso`, `cargo`, `area`) VALUES
('Admin', 'Parcial', '2026-10-14', 'Docente encargado de administrar información sobre la base de datos.', 'Programación de Páginas Web.'),
('CarlosVillaruel', 'Total', '2026-10-25', 'Director', 'Administración');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banner`
--

CREATE TABLE `banner` (
  `id_banner` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `orden` int(2) NOT NULL,
  `estado` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `banner`
--

INSERT INTO `banner` (`id_banner`, `id_curso`, `orden`, `estado`) VALUES
(1, 7, 0, 'Activo'),
(2, 3, 0, 'Activo'),
(3, 4, 0, 'Activo'),
(4, 9, 0, 'Activo'),
(5, 10, 0, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_cursos`
--

CREATE TABLE `categoria_cursos` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria_cursos`
--

INSERT INTO `categoria_cursos` (`id_categoria`, `nombre`, `descripcion`) VALUES
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
-- Estructura de tabla para la tabla `certificados`
--

CREATE TABLE `certificados` (
  `id_certificado` int(11) NOT NULL,
  `archivo` varchar(50) NOT NULL,
  `fecha_emision` date NOT NULL,
  `id_matricula` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `nombre` varchar(150) NOT NULL,
  `modalidad` varchar(50) NOT NULL,
  `duracion` varchar(50) NOT NULL,
  `descripcion` text NOT NULL,
  `costo` decimal(10,0) NOT NULL,
  `cupo` int(11) NOT NULL,
  `estado` varchar(30) NOT NULL,
  `img` varchar(150) NOT NULL,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`id_curso`, `nombre`, `modalidad`, `duracion`, `descripcion`, `costo`, `cupo`, `estado`, `img`, `id_categoria`) VALUES
(1, 'Marketing Digital', 'Presencial', '6 meses', 'Espacio dedicado a ideas, estrategias y recursos para promocionar proyectos en internet. Incluye redes sociales, creación de contenido, publicidad digital y herramientas para llegar a nuevas personas.\r\n', 8000, 10, 'Activo', 'marketing.jpg', 9),
(2, 'Desarrollo de Videojuegos', 'Presencial', '6 meses', 'Un espacio para aprender a crear videojuegos desde cero. Encontrarás proyectos, actividades y recursos sobre programación, mecánicas, personajes, escenarios y desarrollo de juegos con herramientas como GameMaker.', 13800, 10, 'Activo', 'desarrollovideojuego.jpg', 10),
(3, 'Programación Python Básico', 'Presencial', '6 meses', 'Un espacio para dar los primeros pasos en programación con Python. Aquí encontrarás ejercicios y recursos sobre variables, print, input, condicionales y otros conceptos fundamentales para comenzar a programar.', 9000, 10, 'En espera', 'python.jpg', 2),
(4, 'Diseño Gráfico', 'Presencial', '6 meses', 'Un espacio para aprender y crear diseños visuales. Aquí encontrarás recursos y proyectos sobre logos, afiches, ilustraciones, edición de imágenes y composición gráfica.', 6000, 10, 'En espera', 'diseñografico.jpg', 1),
(5, 'Operador PC Contable', 'Presencial', '9 meses', 'Se preparará al alumno para el área contable de una empresa, relacionando la administración con la contabilidad, preparándolo en la documentación comercial e insertándolo en el tema de registros contables por medios manuales; simultáneamente se ira incorporando una exploración general del software contable para introducir a los alumnos en el programa  a los efectos de familiarizarlos con las planillas de ingreso de datos y presentación de informes.', 9000, 15, 'Activo', 'OP Contable.png', 3),
(6, 'Operador PC Avanzado', 'Presencial', '6 meses', 'El egresado tendrá conocimientos avanzados en el manejo de paquetes de ofimática, bases de datos y un amplio desempeño en el mantenimiento del PC, instalación de software, y trabajo con redes.', 9000, 10, 'Activo', 'OP Avanzado.png', 4),
(7, 'Operador PC', 'Presencial', '4 meses', 'Preparar al alumno con conocimientos en el manejo del ordenador, capaz de desempeñar tareas mediante el uso de procesadores de textos, planilla de cálculo, presentación de diapositivas, así como también el manejo de correo electrónico y navegadores de Internet.', 6000, 10, 'Activo', 'OP.png', 4),
(8, 'Operador PC Junior', 'Presencial', '6 meses', 'El alumno obtiene conocimientos básicos en el manejo del ordenador, en un nivel escolar tratando de enriquecer los conocimientos del niño en el desempeño de tareas con procesadores de texto, planilla de cálculo y presentaciones digitales, así como también el uso de correo electrónico y navegadores de Internet.', 8000, 15, 'Activo', 'OP Junior.png', 4),
(9, 'Programación y Robótica con Micro:bit', 'Presencial', '6 meses', 'La finalidad de este plan es proporcionar los conocimientos necesarios para la programación de placas micro:bit, promoviendo su aplicación en proyectos de robótica básica. Asimismo, busca responder a la necesidad del instituto de contar con un curso de estas características.', 9000, 15, 'En espera', 'Prog Microbit.png', 5),
(10, 'Programación Web', 'Presencial', '6 meses', 'Iniciar al estudiante en el lenguaje de programación PHP (Hypertext Pre-Processor), lo que permite crear sitios web con contenido dinámico.', 9000, 15, 'Activo', 'Prog Web.png', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `nombre_usuario` varchar(50) NOT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `curriculum_vitae` varchar(255) DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `nombre_usuario` varchar(50) NOT NULL,
  `telefono` varbinary(32) DEFAULT NULL,
  `telefono_iv` varbinary(12) DEFAULT NULL,
  `telefono_tag` varbinary(16) DEFAULT NULL,
  `fecha_nacimiento` varbinary(32) DEFAULT NULL,
  `fecha_nacimiento_iv` varbinary(12) DEFAULT NULL,
  `fecha_nacimiento_tag` varbinary(16) DEFAULT NULL,
  `foto` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`nombre_usuario`, `telefono`, `telefono_iv`, `telefono_tag`, `fecha_nacimiento`, `fecha_nacimiento_iv`, `fecha_nacimiento_tag`, `foto`) VALUES
('Dleuchuk', 0xb7df86164725b5f495, 0x07bcf50229a91c5aa0d8e2a7, 0x704c9ff60cac81d6bf56484824c1b714, 0xa1fc11c6f066f52910c4, 0xf262dec9157f03ea9d9fe928, 0x96989d38255d2f51d39adf7996439694, NULL),
('Lucas123', 0x9eb1e9d097b3131849, 0x76ae49e02a839b6d1a16c4a8, 0x12999b61be9961838e0d00c238d6445b, 0xc53c6dea842f918da7e9, 0xcb30bbbc930ee0f6c3e9af6c, 0xf34842c237b40d86dca7ce6029482954, 'FotoPerfil_Lucas123.png'),
('LucasLS', 0x43a69b248a2cc3adc5, 0x964d269e75da10f0283c89cb, 0xa973bfe7d125ba832b0d7276d0913314, 0x2659d86318b9ceabf514, 0x881bd47de3c12e4068dc1d62, 0x5b09f7e82b3516ba9986ec9b53f86dbc, 'FotoPerfil_LucasLS.jpg'),
('Luis123', 0x62e2948dd0e075ad9a, 0x202d1fa6ccdf8e5b7057814a, 0x06803e6073fa915da162581e51675b63, 0xa9f85b2d659a74f6b7d9, 0x85e86d1ee57d59c61ad209e0, 0xe0c3e9234ec3248ab642335e50c593ae, 'FotoPerfil_Luis123.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria`
--

CREATE TABLE `galeria` (
  `id_imagen` int(11) NOT NULL,
  `titulo` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `imagen` varchar(50) NOT NULL,
  `nombre_usuario_admin` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `galeria`
--

INSERT INTO `galeria` (`id_imagen`, `titulo`, `fecha`, `imagen`, `nombre_usuario_admin`) VALUES
(1, 'Frente de la Institución', '2016-09-07', '1.jpg', 'Admin'),
(2, 'Chicos estudiando', '2026-09-03', '10.jpg', 'Admin'),
(3, 'Entrada de la Institución', '2016-09-08', '2.jpg', 'Admin'),
(4, 'Entrada a dirección', '2026-09-16', '3.jpg', 'Admin'),
(5, 'Salida de la Institución', '2016-09-08', '4.jpg', 'Admin'),
(6, 'Frente de INADI con Bandera', '2019-09-04', '5.jpg', 'Admin'),
(7, 'Entrada a dirección de INADI', '2016-09-15', '6.jpg', 'Admin'),
(8, 'Salón de clases', '2026-09-03', '7.jpg', 'Admin'),
(9, 'Salón de clases 2', '2026-09-17', '8.jpg', 'Admin'),
(10, 'Clase virtual 1', '2026-09-10', '9.jpg', 'Admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id_material` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `url_archivo` varchar(255) NOT NULL,
  `fecha` date DEFAULT NULL,
  `id_clase` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id_material`, `nombre`, `descripcion`, `tipo`, `url_archivo`, `fecha`, `id_clase`) VALUES
(1, 'Guía Marketing Digital', 'Material introductorio sobre marketing digital.', 'PDF', 'materiales/marketing-guia.pdf', NULL, 1),
(2, 'Introducción Videojuegos', 'Material sobre los conceptos básicos del desarrollo de videojuegos.', 'PDF', 'videojuegos-introduccion.pdf', NULL, 2),
(3, 'Variables Python', 'Guía práctica sobre variables y tipos de datos en Python.', 'PDF', 'materiales/variables-python.pdf', NULL, 3),
(4, 'Principios del Diseño', 'Material sobre composición y principios básicos del diseño gráfico.', 'PDF', 'materiales/principios-diseno.pdf', NULL, 4),
(5, 'Introducción Contabilidad', 'Material introductorio de conceptos contables.', 'PDF', 'materiales/introduccion-contabilidad.pdf', NULL, 5),
(6, 'Guía Office Avanzado', 'Guía de herramientas avanzadas de ofimática.', 'PDF', 'materiales/office-avanzado.pdf', NULL, 6),
(7, 'Procesador de Texto', 'Material para aprender a utilizar procesadores de texto.', 'PDF', 'materiales/procesador-texto.pdf', NULL, 7),
(8, 'Informática Básica', 'Material introductorio sobre el uso del ordenador.', 'PDF', 'materiales/informatica-basica.pdf', NULL, 8),
(9, 'Guía Micro:bit', 'Material introductorio para programación con micro:bit.', 'PDF', 'materiales/guia-microbit.pdf', NULL, 9),
(10, 'Introducción PHP', 'Material introductorio sobre programación web con PHP.', 'PDF', 'materiales/introduccion-php.pdf', NULL, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matricula`
--

CREATE TABLE `matricula` (
  `id_matricula` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `estado` varchar(50) NOT NULL,
  `nombre_usuario_estudiante` varchar(50) NOT NULL,
  `id_curso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `matricula`
--

INSERT INTO `matricula` (`id_matricula`, `fecha`, `estado`, `nombre_usuario_estudiante`, `id_curso`) VALUES
(1, '2026-09-09', 'activa', 'Luis123', 2),
(2, '2026-10-26', 'Pendiente de pago', 'LucasLS', 2),
(3, '2026-10-26', 'Activa', 'Lucas123', 1),
(4, '2026-10-26', 'Pendiente de pago', 'Dleuchuk', 5),
(7, '2026-10-26', 'Pendiente de pago', 'Dleuchuk', 1),
(8, '2026-10-26', 'Pendiente de pago', 'Dleuchuk', 2),
(9, '2026-10-27', 'Activa', 'Lucas123', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `id_noticia` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `titulo` varchar(50) NOT NULL,
  `contenido` varchar(100) NOT NULL,
  `imagen` varchar(100) NOT NULL,
  `enlace` varchar(2048) NOT NULL,
  `nombre_usuario_admin` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `fecha_pago` date NOT NULL DEFAULT current_timestamp(),
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `ref_pasarela` varchar(100) NOT NULL,
  `estado` varchar(30) NOT NULL,
  `id_matricula` int(11) DEFAULT NULL,
  `referencia_externa` varchar(100) DEFAULT NULL,
  `moneda` varchar(3) NOT NULL DEFAULT 'UYU'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `fecha_pago`, `monto`, `metodo_pago`, `ref_pasarela`, `estado`, `id_matricula`, `referencia_externa`, `moneda`) VALUES
(18, '2026-10-27', 8000.00, 'Mercado Pago (prueba)', '185322443285', 'confirmado', 3, 'INSOFT-747d733abd453db3c5ee6429:3', 'UYU');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representante`
--

CREATE TABLE `representante` (
  `id_representante` int(11) NOT NULL,
  `telefono` varbinary(32) DEFAULT NULL,
  `telefono_iv` varbinary(12) DEFAULT NULL,
  `telefono_tag` varbinary(16) DEFAULT NULL,
  `direccion` varbinary(32) DEFAULT NULL,
  `direccion_iv` varbinary(12) DEFAULT NULL,
  `direccion_tag` varbinary(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representantes_estudiantes`
--

CREATE TABLE `representantes_estudiantes` (
  `id_relacion` int(11) NOT NULL,
  `id_representante` int(11) DEFAULT NULL,
  `nombre_usuario_estudiante` varchar(50) DEFAULT NULL,
  `parentesco` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id_tarea` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_clase` int(11) NOT NULL,
  `fecha_tarea` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_limite` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id_tarea`, `titulo`, `descripcion`, `id_clase`, `fecha_tarea`, `fecha_limite`) VALUES
(1, 'Prueba 1', 'Realizada desde BD', 2, '2026-10-22 09:44:50', '2026-10-29 11:13:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas_estudiantes`
--

CREATE TABLE `tareas_estudiantes` (
  `id_tarea` int(11) NOT NULL,
  `nombre_usuario_estudiante` varchar(50) NOT NULL,
  `calificacion` decimal(5,2) DEFAULT NULL,
  `fecha_entrega` datetime NOT NULL DEFAULT current_timestamp(),
  `texto_entrega` text DEFAULT NULL,
  `archivo_entrega` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `nombre_usuario` varchar(50) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cedula` varbinary(16) NOT NULL,
  `cedula_iv` varbinary(12) NOT NULL,
  `cedula_tag` varbinary(16) NOT NULL,
  `correo` varbinary(255) NOT NULL,
  `correo_iv` varbinary(12) NOT NULL,
  `correo_tag` varbinary(16) NOT NULL,
  `password` varchar(255) NOT NULL,
  `estado` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`nombre_usuario`, `nombre`, `apellido`, `cedula`, `cedula_iv`, `cedula_tag`, `correo`, `correo_iv`, `correo_tag`, `password`, `estado`) VALUES
('Admin', 'Lucas', 'Rangel', 0x30, 0xd7dd7ba665d505782345e784, 0x44741fa4c1e7d8c09565fdba29efd845, 0x4150543a5b292a3f3f3f3f0a7d5b3fde83153f073f3f034a0f, 0xd6d3da5dfb182ca87be6e8f0, 0x08d130c1b558615caa59415320c23c9b, '$argon2id$v=19$m=65536,t=4,p=1$emp6azQ5dVVwMjN4SEF4aA$TITmyzFybAycEiNE5xJ4OF3d3etcgFtyC8uvrFSVXQ8', 'Activo'),
('CarlosVillaruel', 'Carlos', 'Villaruel', 0x8cca3541f9fc815fc1, 0xdb4d25a4c7e4ea7c9be00fc3, 0xa0320a66a50de81f7dbd9429bf484aee, 0x99782a3bb41e265b67460549f14f5e9ed58a4cfc8916ada04a, 0x52fb097b72703cf41b9ea89c, 0xe5a4f6a2f8c10f63601152c04d33a346, '$argon2id$v=19$m=65536,t=4,p=1$ZzNienFNWHFBZndKZ0YzVQ$6tWKbfG+rK0xJXejVejZlpFp8U+XCuCzqhO3/0W+N1k', 'Activo'),
('Dleuchuk', 'Diana', 'Leuchuk', 0xd963b8d4fa1db8c5786dc7, 0x32cc91b381ac40cef62378d6, 0xaa74995e501663c0a499f7db893e2334, 0xb13b558a3e08e0f49e793bc0941c9607e98bc6685ae3, 0xfb234e7543ae604aab65f25b, 0xa9ccdab736ddeaf317e7772268f8c91f, '$argon2id$v=19$m=65536,t=4,p=1$YTlIN0taSUY3cng4dGhtcQ$9cbulFIBbwAdz8fyXfofXRxEh1/v9FyzalojmsDn0WU', 'Activo'),
('Lucas123', 'Lucas', 'Rangel', 0x2dc9ed09412a31776dc0ff, 0xd2c29a0ac4fb08baee511c4b, 0x29386b04296be7e772732f0e171e13eb, 0x1ce8c4b8d3a11a509e06c1517eddd341a07aec148087e37b30, 0x5258ebfdcbcebed9fe86ba9e, 0xb2c68c3420a7dee2e4c3ca762ea9fe99, '$argon2id$v=19$m=65536,t=4,p=1$Yi5IYVJXNEVOQU5sMTB2Rg$gHcVkZhCWDoZuXD4laUFM9wjuQkHaB1N4+PxO5E3mWY', 'Activo'),
('LucasLS', 'Lucas', 'LS', 0x6df58efe4343699222f5a6, 0x6b8695d3d34c359401ea67ca, 0xbc3ea20cf61360259c1356b947917802, 0x10c63544d6664845a298864647c3caec221e88068be73b1336255aad3800f0, 0x527c8873a4a2ec7287afb84c, 0xabe6498cd9987149293920738e92f7f6, '$argon2id$v=19$m=65536,t=4,p=1$Si8uL0hYSE9YN2pSREs5Vw$06tMpsR5Sl3eXf+S4Q088DpjtMuBuxNNmNuFk9USwlI', 'Activo'),
('Luis123', 'Luisito', 'Comunica', 0x6daf82897624e111cfb201, 0x2393fe9e774044cfff950112, 0xf18bddeaeef0617ff9d928399ea3bb3f, 0x93ee8f2d46d43728ee4597f6c05a370138, 0x9a0bbfcad15fb86c7ddb6cf2, 0x0086adff92cecaf9f0aa57e6651c16c4, '$argon2id$v=19$m=65536,t=4,p=1$czMvUXlXQURrMmxqUy5XUg$N6tmi6Spd6VeRWBvnKbs1QirrC9T2LLtgu0lggQ48Jw', 'Activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrativos`
--
ALTER TABLE `administrativos`
  ADD PRIMARY KEY (`nombre_usuario`);

--
-- Indices de la tabla `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id_banner`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `categoria_cursos`
--
ALTER TABLE `categoria_cursos`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `certificados`
--
ALTER TABLE `certificados`
  ADD PRIMARY KEY (`id_certificado`),
  ADD KEY `id_matricula` (`id_matricula`);

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
-- Indices de la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD PRIMARY KEY (`nombre_usuario`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`nombre_usuario`);

--
-- Indices de la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD PRIMARY KEY (`id_imagen`),
  ADD KEY `nombre_usuario_admin` (`nombre_usuario_admin`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id_material`),
  ADD KEY `id_clase` (`id_clase`);

--
-- Indices de la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD PRIMARY KEY (`id_matricula`),
  ADD UNIQUE KEY `uq_matricula_estudiante_curso` (`nombre_usuario_estudiante`,`id_curso`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `nombre_usuario_estudiante` (`nombre_usuario_estudiante`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id_noticia`),
  ADD KEY `fk_noticias_admin` (`nombre_usuario_admin`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `fk_pagos_matricula` (`id_matricula`);

--
-- Indices de la tabla `representante`
--
ALTER TABLE `representante`
  ADD PRIMARY KEY (`id_representante`);

--
-- Indices de la tabla `representantes_estudiantes`
--
ALTER TABLE `representantes_estudiantes`
  ADD PRIMARY KEY (`id_relacion`),
  ADD KEY `id_representante` (`id_representante`),
  ADD KEY `nombre_usuario_estudiante` (`nombre_usuario_estudiante`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id_tarea`),
  ADD KEY `id_clase` (`id_clase`);

--
-- Indices de la tabla `tareas_estudiantes`
--
ALTER TABLE `tareas_estudiantes`
  ADD PRIMARY KEY (`id_tarea`,`nombre_usuario_estudiante`),
  ADD KEY `id_tarea` (`id_tarea`),
  ADD KEY `nombre_usuario_estudiante` (`nombre_usuario_estudiante`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`nombre_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `banner`
--
ALTER TABLE `banner`
  MODIFY `id_banner` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `categoria_cursos`
--
ALTER TABLE `categoria_cursos`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `certificados`
--
ALTER TABLE `certificados`
  MODIFY `id_certificado` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT de la tabla `galeria`
--
ALTER TABLE `galeria`
  MODIFY `id_imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `matricula`
--
ALTER TABLE `matricula`
  MODIFY `id_matricula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id_noticia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `representante`
--
ALTER TABLE `representante`
  MODIFY `id_representante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `representantes_estudiantes`
--
ALTER TABLE `representantes_estudiantes`
  MODIFY `id_relacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administrativos`
--
ALTER TABLE `administrativos`
  ADD CONSTRAINT `administrativos_ibfk_1` FOREIGN KEY (`nombre_usuario`) REFERENCES `usuarios` (`nombre_usuario`);

--
-- Filtros para la tabla `banner`
--
ALTER TABLE `banner`
  ADD CONSTRAINT `banner_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`);

--
-- Filtros para la tabla `certificados`
--
ALTER TABLE `certificados`
  ADD CONSTRAINT `certificados_ibfk_1` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`);

--
-- Filtros para la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD CONSTRAINT `docentes_ibfk_1` FOREIGN KEY (`nombre_usuario`) REFERENCES `usuarios` (`nombre_usuario`);

--
-- Filtros para la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD CONSTRAINT `estudiantes_ibfk_1` FOREIGN KEY (`nombre_usuario`) REFERENCES `usuarios` (`nombre_usuario`);

--
-- Filtros para la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD CONSTRAINT `nombre_usuario_admin` FOREIGN KEY (`nombre_usuario_admin`) REFERENCES `administrativos` (`nombre_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `materiales_ibfk_1` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`);

--
-- Filtros para la tabla `matricula`
--
ALTER TABLE `matricula`
  ADD CONSTRAINT `id_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`) ON UPDATE CASCADE,
  ADD CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`nombre_usuario_estudiante`) REFERENCES `estudiantes` (`nombre_usuario`);

--
-- Filtros para la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `fk_noticias_admin` FOREIGN KEY (`nombre_usuario_admin`) REFERENCES `administrativos` (`nombre_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_pagos_matricula` FOREIGN KEY (`id_matricula`) REFERENCES `matricula` (`id_matricula`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `representantes_estudiantes`
--
ALTER TABLE `representantes_estudiantes`
  ADD CONSTRAINT `representantes_estudiantes_ibfk_1` FOREIGN KEY (`id_representante`) REFERENCES `representante` (`id_representante`),
  ADD CONSTRAINT `representantes_estudiantes_ibfk_2` FOREIGN KEY (`nombre_usuario_estudiante`) REFERENCES `estudiantes` (`nombre_usuario`);

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`id_clase`) REFERENCES `clase` (`id_clase`);

--
-- Filtros para la tabla `tareas_estudiantes`
--
ALTER TABLE `tareas_estudiantes`
  ADD CONSTRAINT `tareas_estudiantes_ibfk_1` FOREIGN KEY (`id_tarea`) REFERENCES `tareas` (`id_tarea`),
  ADD CONSTRAINT `tareas_estudiantes_ibfk_2` FOREIGN KEY (`nombre_usuario_estudiante`) REFERENCES `estudiantes` (`nombre_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

<?php
session_start();

if (!isset($_SESSION['nombre_usuario']))
    header("Location: ../Bienvenido/bienvenidoindex.html");
// CONEXIÓN

require_once __DIR__ . '/../database/consultas/conexion.php';

$anioActual = date("Y");


// OBTENER ID DEL CURSO

$idCurso = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);


// Si no recibimos ID, volvemos a Cursos

if (!$idCurso) {

    header("Location: cursosindex.php");

    exit;
}


// BUSCAR CURSO EN LA BASE DE DATOS

$sql = "
    SELECT
        curso.id_curso,
        curso.nombre,
        curso.modalidad,
        curso.duracion,
        curso.descripcion,
        curso.costo,
        curso.cupo,
        curso.estado,
        curso.img,
        categoriacurso.nombre AS categoria

    FROM curso

    INNER JOIN categoriacurso
        ON curso.id_categoria = categoriacurso.id_categoria

    WHERE curso.id_curso = :idCurso

    LIMIT 1
";


$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':idCurso' => $idCurso
]);


$curso = $stmt->fetch(PDO::FETCH_ASSOC);


// SI EL CURSO NO EXISTE


if (!$curso) {

    header("Location: cursosindex.php");

    exit;
}


// DATOS DEL CURSO

$nombre = htmlspecialchars(
    $curso['nombre'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);

$categoria = htmlspecialchars(
    $curso['categoria'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);

$modalidad = htmlspecialchars(
    $curso['modalidad'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);

$duracion = htmlspecialchars(
    $curso['duracion'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);

$descripcion = htmlspecialchars(
    $curso['descripcion'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);

$costo = htmlspecialchars(
    $curso['costo'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);

$cupo = htmlspecialchars(
    $curso['cupo'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);

$estado = htmlspecialchars(
    $curso['estado'] ?? '',
    ENT_QUOTES,
    'UTF-8'
);

$imagen = trim(
    $curso['img'] ?? ''
);



// IMAGEN DE PYTHON


if ($imagen === '' && $idCurso === 3) {

    $imagen = 'phyton.jpeg';

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        INADI | <?php echo $nombre; ?>
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="curso.css"
    >

</head>


<body>

<div class="page">


    <!-- BARRA SUPERIOR -->

    <header class="topbar">


        <div class="topbar-left">


            <!-- LOGO -->

            <div class="top-logo">

                <img
                    src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                    alt="Logo INADI"
                >

                <span>
                    INADI
                </span>

            </div>


            <!-- NOTICIAS -->

            <div class="news">

                <i class="fa-solid fa-triangle-exclamation"></i>

                NOTICIAS

            </div>


        </div>



        <!-- DERECHA -->

        <div class="topbar-right">


            <span>

                <i class="fa-solid fa-globe"></i>

                ES

            </span>


            <a href="../login/inicio.php"
                class="user-icon"
                title="Iniciar sesión">

                <img src="../database/consultas/estudiantes/fotosEst/FotoPerfil_Lucas123.png" alt="Imagen de Lucas.">

            </a>


        </div>


    </header>



    <!-- SIDEBAR -->

    <aside class="sidebar">


        <ul class="menu">

            <!-- INICIO -->

            <li>

                <a href="../Inicio/inicioindex.php">

                    <i class="fa-solid fa-house"></i>

                    <span>
                        Inicio
                    </span>

                </a>

            </li>


            <!-- NOSOTROS -->

            <li>

                <a href="../Nosotros/nosotros.php">

                    <i class="fa-solid fa-building-columns"></i>

                    <span>
                        Nosotros
                    </span>

                </a>

            </li>


            <!-- CURSOS -->

            <li class="active">

                <a href="cursosindex.php">

                    <i class="fa-solid fa-book"></i>

                    <span>
                        Cursos
                    </span>

                </a>

            </li>


            <!-- MATRÍCULA -->

            <li>

                <a href="../Matricula/matricula.php">

                    <i class="fa-solid fa-file-signature"></i>

                    <span>
                        Matrícula
                    </span>

                </a>

            </li>


            <!-- CONTACTO -->

            <li>

                <a href="#">

                    <i class="fa-solid fa-envelope"></i>

                    <span>
                        Contacto
                    </span>

                </a>

            </li>


            <!-- GALERÍA -->

            <li>

                <a href="#">

                    <i class="fa-solid fa-image"></i>

                    <span>
                        Galería
                    </span>

                </a>

            </li>


            <!-- CONVENIOS -->

            <li>

                <a href="#">

                    <i class="fa-solid fa-handshake"></i>

                    <span>
                        Convenios
                    </span>

                </a>

            </li>


            <!-- EMPRESAS -->

            <li>

                <a href="#">

                    <i class="fa-solid fa-city"></i>

                    <span>
                        Empresas
                    </span>

                </a>

            </li>


        </ul>


        <!-- INFORMACIÓN -->

        <div class="sidebar-bottom">

            © INADI <?php echo $anioActual; ?><br>

            Instituto de Informática<br>

            Salto - Uruguay

        </div>


    </aside>



    <!-- CONTENIDO PRINCIPAL -->

    <div class="content">


        <main class="main">


            <!-- VOLVER -->

            <a
                href="cursosindex.php"
                class="back-button"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Volver a cursos

            </a>



            <!-- INFORMACIÓN DEL CURSO -->

            <section class="course-detail">


                <!-- IMAGEN -->

                <div class="course-image">


                    <?php if ($imagen !== ''): ?>

                        <img
                            src="imagenes/<?php echo htmlspecialchars(
                                $imagen,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            alt="<?php echo $nombre; ?>"
                        >

                    <?php else: ?>

                        <div class="course-no-image">

                            <i class="fa-solid fa-graduation-cap"></i>

                        </div>

                    <?php endif; ?>


                </div>



                <!-- INFORMACIÓN -->

                <div class="course-information">


                    <!-- CATEGORÍA -->

                    <span class="course-category">

                        <?php echo $categoria; ?>

                    </span>



                    <!-- NOMBRE -->

                    <h1>

                        <?php echo $nombre; ?>

                    </h1>



                    <!-- DESCRIPCIÓN -->

                    <?php if ($descripcion !== ''): ?>

                        <p class="course-description">

                            <?php echo nl2br($descripcion); ?>

                        </p>

                    <?php endif; ?>



                    <!-- DATOS -->

                    <div class="course-data">


                        <!-- MODALIDAD -->

                        <?php if ($modalidad !== ''): ?>

                            <div class="data-item">

                                <i class="fa-solid fa-display"></i>

                                <div>

                                    <small>
                                        Modalidad
                                    </small>

                                    <strong>
                                        <?php echo $modalidad; ?>
                                    </strong>

                                </div>

                            </div>

                        <?php endif; ?>



                        <!-- DURACIÓN -->

                        <?php if ($duracion !== ''): ?>

                            <div class="data-item">

                                <i class="fa-regular fa-clock"></i>

                                <div>

                                    <small>
                                        Duración
                                    </small>

                                    <strong>
                                        <?php echo $duracion; ?>
                                    </strong>

                                </div>

                            </div>

                        <?php endif; ?>



                        <!-- COSTO -->

                        <?php if ($costo !== ''): ?>

                            <div class="data-item">

                                <i class="fa-solid fa-dollar-sign"></i>

                                <div>

                                    <small>
                                        Costo
                                    </small>

                                    <strong>
                                        $<?php echo $costo; ?>
                                    </strong>

                                </div>

                            </div>

                        <?php endif; ?>



                        <!-- CUPO -->

                        <?php if ($cupo !== ''): ?>

                            <div class="data-item">

                                <i class="fa-solid fa-users"></i>

                                <div>

                                    <small>
                                        Cupos disponibles
                                    </small>

                                    <strong>
                                        <?php echo $cupo; ?>
                                    </strong>

                                </div>

                            </div>

                        <?php endif; ?>


                    </div>



                    <!-- ESTADO -->

                    <?php if ($estado !== ''): ?>

                        <div class="course-status">

                            <i class="fa-solid fa-circle"></i>

                            <?php echo $estado; ?>

                        </div>

                    <?php endif; ?>


                </div>


            </section>


        </main>

    </div>


</div>

</body>

</html>

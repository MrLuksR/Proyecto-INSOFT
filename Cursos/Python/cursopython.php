<?php


    // INADI - DETALLE DEL CURSO
    


    
    // CONEXIÓN
    

    require_once __DIR__ . '/../database/consultas/conexion.php';


    
    // OBTENER ID DEL CURSO
    

    $idCurso = isset($_GET['id'])
        ? (int) $_GET['id']
        : 0;


    
    // VERIFICAR ID
    

    if ($idCurso <= 0) {

        header('Location: cursosindex.php');

        exit;
    }


    
    // CONSULTAR CURSO
    

    $sql = "SELECT
                curso.id_curso,
                curso.nombre,
                curso.modalidad,
                curso.duracion,
                curso.descripcion,
                curso.costo,
                curso.cupo,
                curso.estado,
                curso.img,
                categoriacurso.nombre AS categoria,
                categoriacurso.descripcion AS descripcion_categoria

            FROM curso

            INNER JOIN categoriacurso
                ON curso.id_categoria = categoriacurso.id_categoria

            WHERE curso.id_curso = :id_curso";


    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id_curso' => $idCurso
    ]);


    $curso = $stmt->fetch(PDO::FETCH_ASSOC);


    
    // SI NO EXISTE EL CURSO
    

    if (!$curso) {

        header('Location: cursosindex.php');

        exit;
    }


    
    // DATOS DEL CURSO

    $nombre = htmlspecialchars(
        $curso['nombre'],
        ENT_QUOTES,
        'UTF-8'
    );


    $categoria = htmlspecialchars(
        $curso['categoria'],
        ENT_QUOTES,
        'UTF-8'
    );


    $modalidad = htmlspecialchars(
        $curso['modalidad'],
        ENT_QUOTES,
        'UTF-8'
    );


    $duracion = htmlspecialchars(
        $curso['duracion'],
        ENT_QUOTES,
        'UTF-8'
    );


    $descripcion = nl2br(
        htmlspecialchars(
            $curso['descripcion'],
            ENT_QUOTES,
            'UTF-8'
        )
    );


    $costo = number_format(
        $curso['costo'],
        0,
        ',',
        '.'
    );


    $cupo = (int) $curso['cupo'];


    $estado = htmlspecialchars(
        $curso['estado'],
        ENT_QUOTES,
        'UTF-8'
    );


    $imagen = htmlspecialchars(
        $curso['img'],
        ENT_QUOTES,
        'UTF-8'
    );


    
    // OBTENER CLASES DEL CURSO
    

    $sqlClases = "SELECT
                        id_clase,
                        titulo,
                        descripcion,
                        fecha

                  FROM clase

                  WHERE id_curso = :id_curso

                  ORDER BY fecha ASC";


    $stmtClases = $pdo->prepare($sqlClases);

    $stmtClases->execute([
        ':id_curso' => $idCurso
    ]);


    $clases = $stmtClases->fetchAll(PDO::FETCH_ASSOC);


    
    // CONTAR MATERIALES DEL CURSO
    

    $sqlMateriales = "SELECT
                            COUNT(*) AS cantidad

                      FROM material

                      INNER JOIN clase
                          ON material.id_clase = clase.id_clase

                      WHERE clase.id_curso = :id_curso";


    $stmtMateriales = $pdo->prepare($sqlMateriales);

    $stmtMateriales->execute([
        ':id_curso' => $idCurso
    ]);


    $cantidadMateriales = (int) $stmtMateriales
        ->fetchColumn();


    
    // USUARIO / AÑO
    

    $nombreUsuario = "Usuario";

    $anioActual = date("Y");

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


    <link
        rel="icon"
        href="../Elementos Gráficos/Logo Inadi sin Brillo.png"
    >


    <link
        rel="stylesheet"
        href="cursopython.css"
    >


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body>


    <!-- BARRA SUPERIOR -->

    <header class="topbar">


        <div class="topbar-left">

            <img
                src="../Elementos Gráficos/Logo Inadi con Brillo.png"
                alt="INADI"
                class="topbar-logo"
            >

            <span class="topbar-title">

                INADI

            </span>

        </div>


        <div class="topbar-right">


            <span class="topbar-news">

                NOTICIAS

            </span>


            <span class="topbar-language">

                ES

            </span>


            <a
                href="../Bienvenido/bienvenidoindex.html"
                class="topbar-user"
                title="Usuario"
            >

                <i class="fa-solid fa-user"></i>

            </a>


        </div>


    </header>



    <!-- BARRA LATERAL -->

    <aside class="sidebar">


        <nav class="sidebar-nav">


            <!-- NOSOTROS -->

            <a
                href="../Nosotros/nosotros.php"
                class="sidebar-link"
            >

                <i class="fa-solid fa-building"></i>

                <span>

                    Nosotros

                </span>

            </a>



            <!-- CURSOS -->

            <a
                href="cursosindex.php"
                class="sidebar-link active"
            >

                <i class="fa-solid fa-graduation-cap"></i>

                <span>

                    Cursos

                </span>

            </a>



            <!-- MATRÍCULA -->

            <a
                href="../Matricula/matricula.php"
                class="sidebar-link"
            >

                <i class="fa-solid fa-file-signature"></i>

                <span>

                    Matrícula

                </span>

            </a>



            <!-- CONTACTO -->

            <a
                href="#"
                class="sidebar-link"
            >

                <i class="fa-solid fa-envelope"></i>

                <span>

                    Contacto

                </span>

            </a>



            <!-- GALERÍA -->

            <a
                href="#"
                class="sidebar-link"
            >

                <i class="fa-solid fa-images"></i>

                <span>

                    Galería

                </span>

            </a>



            <!-- CONVENIOS -->

            <a
                href="#"
                class="sidebar-link"
            >

                <i class="fa-solid fa-handshake"></i>

                <span>

                    Convenios

                </span>

            </a>



            <!-- EMPRESAS -->

            <a
                href="#"
                class="sidebar-link"
            >

                <i class="fa-solid fa-building-user"></i>

                <span>

                    Empresas

                </span>

            </a>


        </nav>



        <!-- PIE SIDEBAR -->

        <div class="sidebar-footer">

            <p>

                © INADI <?php echo $anioActual; ?>

            </p>

            <span>

                Instituto de Informática

            </span>

            <span>

                Salto - Uruguay

            </span>

        </div>


    </aside>



    <!-- CONTENIDO PRINCIPAL -->

    <main class="content">


        <div class="main">


            <!-- VOLVER -->

            <a
                href="cursosindex.php"
                class="back-link"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Volver a cursos

            </a>



            <!-- CABECERA DEL CURSO -->

            <section class="course-hero">


                <div class="course-image-container">


                    <img
                        src="imagenes/<?php echo $imagen; ?>"
                        alt="<?php echo $nombre; ?>"
                        class="course-image"
                    >


                    <span class="course-category">

                        <?php echo $categoria; ?>

                    </span>


                </div>



                <div class="course-info">


                    <span class="course-label">

                        CURSO INADI

                    </span>


                    <h1>

                        <?php echo $nombre; ?>

                    </h1>


                    <p class="course-description">

                        <?php echo $descripcion; ?>

                    </p>


                    <div class="course-details">


                        <div class="detail-item">

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



                        <div class="detail-item">

                            <i class="fa-solid fa-laptop"></i>

                            <div>

                                <small>

                                    Modalidad

                                </small>

                                <strong>

                                    <?php echo $modalidad; ?>

                                </strong>

                            </div>

                        </div>



                        <div class="detail-item">

                            <i class="fa-solid fa-users"></i>

                            <div>

                                <small>

                                    Cupos

                                </small>

                                <strong>

                                    <?php echo $cupo; ?> lugares

                                </strong>

                            </div>

                        </div>


                    </div>


                </div>


            </section>



            <!-- INFORMACIÓN DE INSCRIPCIÓN -->

            <section class="enrollment-card">


                <div class="price-box">


                    <span>

                        Inversión del curso

                    </span>


                    <strong>

                        $ <?php echo $costo; ?>

                    </strong>


                    <small>

                        Precio informado por INADI

                    </small>


                </div>



                <div class="payment-box">


                    <h3>

                        <i class="fa-solid fa-credit-card"></i>

                        Pagos virtuales

                    </h3>


                    <p>

                        La plataforma contempla pagos mediante
                        medios electrónicos, como tarjeta y
                        transferencia.

                    </p>


                    <div class="payment-methods">


                        <span>

                            <i class="fa-solid fa-credit-card"></i>

                            Tarjeta

                        </span>


                        <span>

                            <i class="fa-solid fa-building-columns"></i>

                            Transferencia

                        </span>


                    </div>


                </div>



                <div class="enrollment-action">


                    <span class="status">

                        <?php echo $estado; ?>

                    </span>


                    <a
                        href="../Matricula/matricula.php"
                        class="enroll-button"
                    >

                        <i class="fa-solid fa-file-signature"></i>

                        Inscribirme

                    </a>


                </div>


            </section>



            <!-- CARACTERÍSTICAS -->

            <section class="information-section">


                <h2>

                    Sobre este curso

                </h2>


                <div class="feature-grid">


                    <article class="feature-card">


                        <i class="fa-solid fa-laptop"></i>


                        <h3>

                            Modalidad

                        </h3>


                        <p>

                            <?php echo $modalidad; ?>,
                            con recursos y materiales de apoyo
                            para acompañar el proceso de aprendizaje.

                        </p>


                    </article>



                    <article class="feature-card">


                        <i class="fa-solid fa-book-open"></i>


                        <h3>

                            Material de estudio

                        </h3>


                        <p>

                            El curso cuenta actualmente con
                            <?php echo $cantidadMateriales; ?>
                            material(es) registrado(s) en la plataforma.

                        </p>


                    </article>



                    <article class="feature-card">


                        <i class="fa-solid fa-certificate"></i>


                        <h3>

                            Certificación

                        </h3>


                        <p>

                            Al completar los requisitos
                            establecidos por el instituto,
                            el estudiante podrá acceder a la
                            certificación correspondiente.

                        </p>


                    </article>



                    <article class="feature-card">


                        <i class="fa-solid fa-user-graduate"></i>


                        <h3>

                            Acompañamiento

                        </h3>


                        <p>

                            Acceso a contenidos organizados
                            para facilitar el aprendizaje y
                            seguimiento del curso.

                        </p>


                    </article>


                </div>


            </section>



            <!-- PROGRAMA DEL CURSO -->

            <section class="information-section">


                <div class="section-heading">


                    <div>

                        <span class="section-label">

                            CONTENIDOS

                        </span>


                        <h2>

                            Programa del curso

                        </h2>

                    </div>


                    <span class="class-count">

                        <?php echo count($clases); ?>

                        clase(s)

                    </span>


                </div>



                <?php if (count($clases) > 0): ?>


                    <div class="classes-list">


                        <?php foreach ($clases as $clase): ?>


                            <article class="class-item">


                                <div class="class-number">

                                    <?php
                                        echo (int) $clase['id_clase'];
                                    ?>

                                </div>


                                <div class="class-content">


                                    <h3>

                                        <?php
                                            echo htmlspecialchars(
                                                $clase['titulo'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>

                                    </h3>


                                    <p>

                                        <?php
                                            echo htmlspecialchars(
                                                $clase['descripcion'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                        ?>

                                    </p>


                                </div>


                                <div class="class-date">

                                    <i class="fa-regular fa-calendar"></i>


                                    <?php
                                        echo date(
                                            'd/m/Y',
                                            strtotime($clase['fecha'])
                                        );
                                    ?>

                                </div>


                            </article>


                        <?php endforeach; ?>


                    </div>


                <?php else: ?>


                    <div class="empty-message">

                        Todavía no hay clases registradas
                        para este curso.

                    </div>


                <?php endif; ?>


            </section>



            <!-- CTA FINAL -->

            <section class="final-cta">


                <div>

                    <span>

                        ¿Querés formar parte de INADI?

                    </span>


                    <h2>

                        Comenzá tu inscripción

                    </h2>


                    <p>

                        Elegí tu curso y comenzá a desarrollar
                        nuevas habilidades.

                    </p>

                </div>


                <a
                    href="../Matricula/matricula.php"
                    class="cta-button"
                >

                    Inscribirme ahora

                    <i class="fa-solid fa-arrow-right"></i>

                </a>


            </section>



        </div>


    </main>



    <!-- BOTÓN FLOTANTE -->

    <a
        href="#"
        class="floating-contact"
        title="Contacto"
    >

        <i class="fa-solid fa-phone"></i>

    </a>


</body>

</html>
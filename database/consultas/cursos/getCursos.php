<?php



// CONEXIÓN A LA BASE DE DATOS

require_once __DIR__ . '/../conexion.php';


// OBTENER TODOS LOS CURSOS

function obtenerCursos($pdo)
{
    $sql = "
        SELECT
            curso.id_curso,
            categoriacurso.nombre AS categoria,
            curso.nombre,
            curso.modalidad,
            curso.duracion,
            curso.img,
            curso.estado,
            curso.cupo
        FROM curso

        INNER JOIN categoriacurso
            ON curso.id_categoria = categoriacurso.id_categoria

        WHERE curso.estado = 'Activo'
            
        ORDER BY curso.id_curso ASC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// MOSTRAR UNA CARD

function mostrarCurso($curso)
{
    // ID DEL CURSO
    $idCurso = (int) $curso['id_curso'];


    // CATEGORÍA
    $categoria = htmlspecialchars(
        $curso['categoria'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );


    // NOMBRE
    $nombre = htmlspecialchars(
        $curso['nombre'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );


    // DURACIÓN
    $duracion = htmlspecialchars(
        $curso['duracion'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );


    // ESTADO
    $estado = htmlspecialchars(
        $curso['estado'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );


    // IMAGEN
    $imagen = trim(
        $curso['img'] ?? ''
    );


    /*
     * PYTHON
     *
     * Si el curso tiene ID 3 y no tiene imagen
     * registrada en la BD, usamos phyton.jpeg.
     */

    if ($imagen === '' && $idCurso === 3) {

        $imagen = 'phyton.jpeg';

    }


    // URL DEL DETALLE DEL CURSO
    $urlCurso = 'curso.php?id=' . $idCurso;


    // CARD

    echo '

        <article class="course-card">
    ';


    
    // IMAGEN

    if ($imagen !== '') {

        echo '

            <img
                src="imagenes/' .
                htmlspecialchars(
                    $imagen,
                    ENT_QUOTES,
                    'UTF-8'
                ) .
                '"
                alt="' . $nombre . '"
            >

        ';

    } else {

        echo '

            <div class="course-no-image">

                <i class="fa-solid fa-graduation-cap"></i>

            </div>

        ';

    }


    $cupo = $curso['cupo'];
    // OVERLAY

    echo '

            <div class="course-overlay">


                <!-- DURACIÓN -->

                <span class="course-duration">

                    ' . $duracion . '

                </span>


                <!-- FLECHA -->

                <span class="course-arrow">

                    <i class="fa-solid fa-arrow-right"></i>

                </span>


                <!-- CONTENIDO -->

                <div class="course-content">


                    <!-- CATEGORÍA -->

                    <small>

                        ' . $categoria . '

                    </small>


                    <!-- NOMBRE -->

                    <h3>

                        ' . $nombre . '

                    </h3>


                    <!-- PARTE INFERIOR -->

                    <div class="course-bottom">


                        <!-- CUPOS DISPONIBLES -->

                        <span>

                            Cupo: ' . $cupo . '

                        </span>


                        <!-- BOTÓN -->

                        <a
                            href="' . $urlCurso . '"
                            class="enroll-button"
                        >

                            Ver curso

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>


                    </div>


                </div>


            </div>


        </article>

    ';
}


// MOSTRAR TODOS LOS CURSOS


function mostrarCursos($pdo)
{
    // Obtener cursos
    $cursos = obtenerCursos($pdo);


    // Si no existen cursos
    if (count($cursos) === 0) {

        echo '

            <div class="no-courses">

                <i class="fa-solid fa-book-open"></i>

                <p>
                    Nada para mostrar por aquí.
                </p>

            </div>

        ';

        return;
    }


    // Mostrar cada curso
    foreach ($cursos as $curso) {

        mostrarCurso($curso);

    }
}

?>
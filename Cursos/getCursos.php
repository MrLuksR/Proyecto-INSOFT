<?php
    /*NOTA:
        Este script puede ser reutilizado dentro de otros apartados
        que necesiten mostrar los cursos de forma gráfica (ej.:
            el panel administrativo).
    */
    // Obtener conexión
    require_once '../database/consultas/conexion.php';


    function obtenerCursos($pdo){
        $sql = "SELECT 
                    categoriacurso.nombre AS categoria,
                    curso.nombre,
                    modalidad,
                    duracion,
                    img
                FROM curso
                INNER JOIN categoriacurso
                ON curso.id_categoria = categoriacurso.id_categoria";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function mostrarCurso($curso){
        $cat = $curso['categoria'];
        $nombre = $curso['nombre'];
        $modal = $curso['modalidad'];
        $duracion = $curso['duracion'];
        $img = $curso['img'];

        echo '
            <article class="course-card">

                <img
                    src="imagenes/' . $img . '"
                    alt="' . $nombre . '"
                >

                <div class="course-overlay">

                    <span class="course-duration">
                        ' . $duracion . '
                    </span>

                    <span class="course-arrow">
                        ›
                    </span>

                    <div class="course-content">

                        <small>
                            ' . $cat . '
                        </small>

                        <h3>
                            ' . $nombre . '
                        </h3>

                        <div class="course-bottom">

                            <span>
                                Modalidad: ' . $modal . '
                            </span>

                            <a
                                href="../Matricula/matricula.php"
                                class="enroll-button"
                            >
                                Inscribirme
                            </a>

                        </div>

                    </div>

                </div>

            </article>
        ';
    }


    function mostrarCursos($pdo){
        $cursos = obtenerCursos($pdo);

        if (count($cursos) == 0) {
            echo "<div>Nada para mostrar por aquí</div>";
            return;
        }

        foreach ($cursos as $curso) {
            mostrarCurso($curso);
        }
    }
?>
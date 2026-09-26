<?php

    /*INADI - OBTENER CURSOS
          Este archivo obtiene los cursos desde la base de datos
        y genera las tarjetas visuales de la página Cursos. */


    
    // CONEXIÓN A LA BASE DE DATOS
    

    require_once __DIR__ . '/../conexion.php';


    
    // OBTENER CURSOS
    

    function obtenerCursos($pdo)
    {
        $sql = "SELECT 
                    curso.id_curso,
                    categoriacurso.nombre AS categoria,
                    curso.nombre,
                    curso.modalidad,
                    curso.duracion,
                    curso.img,
                    curso.estado
                FROM curso

                INNER JOIN categoriacurso
                    ON curso.id_categoria = categoriacurso.id_categoria

                ORDER BY curso.id_curso ASC";


        $stmt = $pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    
    // MOSTRAR UN CURSO
    

    function mostrarCurso($curso)
    {
        $idCurso = (int) $curso['id_curso'];

        $cat = htmlspecialchars(
            $curso['categoria'],
            ENT_QUOTES,
            'UTF-8'
        );

        $nombre = htmlspecialchars(
            $curso['nombre'],
            ENT_QUOTES,
            'UTF-8'
        );

        $duracion = htmlspecialchars(
            $curso['duracion'],
            ENT_QUOTES,
            'UTF-8'
        );

        $img = htmlspecialchars(
            $curso['img'],
            ENT_QUOTES,
            'UTF-8'
        );

        $estado = htmlspecialchars(
            $curso['estado'],
            ENT_QUOTES,
            'UTF-8'
        );


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

                        <i class="fa-solid fa-arrow-right"></i>

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

                                ' . $estado . '

                            </span>


                            <a
                                href="curso.php?id=' . $idCurso . '"
                                class="enroll-button"
                            >

                                Ver curso

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
        $cursos = obtenerCursos($pdo);


        if (count($cursos) == 0) {

            echo '

                <div class="no-courses">

                    Nada para mostrar por aquí.

                </div>

            ';

            return;
        }


        foreach ($cursos as $curso) {

            mostrarCurso($curso);

        }
    }

?>
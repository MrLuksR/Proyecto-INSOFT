<?php
    // Obtener conexión
    require_once '../database/consultas/conexion.php';

    function setCategoria($numCat){
        switch ($numCat){
            case 1:
                return "Diseño Gráfico";
                break;
            case 2:
                return "Programación";
                break;
        }
    }

    $sql = "SELECT id_categoria, nombre, modalidad, duracion, costo, cupo, estado FROM curso;";
    $stmt = $pdo->prepare($sql);

    $stmt->execute();

    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $count = count($res);

    if ($count == 0){
        echo "<div>Nada para mostrar por aquí</div>";
    }else{
        for ($i=0; $i<$count; $i++){
            $cat = $res[$i]['id_categoria'];
            $nombre = $res[$i]['nombre'];
            $modal = $res[$i]['modalidad'];
            $duracion = $res[$i]['duracion'];
            $costo = $res[$i]['costo'];
            $cupo = $res[$i]['cupo'];
            $estado = $res[$i]['estado'];
            echo '
                <article class="course-card">

                    <img src="img/ia.jpg" alt="Inteligencia Artificial">

                    <div class="course-overlay">

                        <span class="course-duration">
                            '. $duracion .'
                        </span>

                        <span class="course-arrow">
                            ›
                        </span>

                        <div class="course-content">

                            <small>
                                '. setCategoria($cat) .'
                            </small>

                            <h3>
                                '.$nombre.'
                            </h3>

                            <div class="course-bottom">

                                <span>
                                    Inscripciones Abiertas
                                </span>

                                <a href="#" class="enroll-button">
                                    Inscribirme
                                </a>

                            </div>

                        </div>

                    </div>

                </article>
        ';}
    }
?>
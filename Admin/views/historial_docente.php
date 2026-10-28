<?php

/* ==========================================================
   1. OBTENER DATOS
========================================================== */

$datosListado = adminListado(
    $pdo ?? null,
    $page
);

$columnas = $datosListado['columnas'] ?? [];

$filas = $datosListado['filas'] ?? [];

/* ==========================================================
   OBTENER MENSAJE DE ERROR
========================================================== */

$errorDescarga = $_SESSION['error_descarga_cv'] ?? null;


// ELIMINAR MENSAJE DESPUÉS DE LEERLO

unset($_SESSION['error_descarga_cv']);


/* ==========================================================
   2. CONFIGURAR COLUMNAS
========================================================== */

// OCULTAR ID DEL CURSO

unset(
    $columnas['id_curso']
);


// OCULTAR RUTA DEL CURRÍCULUM

unset(
    $columnas['curriculum_vitae']
);


// AGREGAR COLUMNA DE DESCARGA

$columnas['descargar_cv'] = 'Currículum vitae';

?>

<?php if ($errorDescarga): ?>

    <div
        style="
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        "
    >

        <i class="fa-solid fa-circle-exclamation"></i>

        <?= e($errorDescarga) ?>

    </div>

<?php endif; ?>


<!-- ========================================================
     3. ENCABEZADO
======================================================== -->

<div class="welcome-row">

    <div>

        <div class="eyebrow">
            Registros de INSOFT
        </div>

        <h1 class="heading">
            <?= e($pageTitle) ?>
        </h1>

        <p class="subtitle">
            Información consultada directamente
            desde la base de datos.
        </p>

    </div>

    <span class="status-pill pill-blue">

        <?= count($filas) ?> registros

    </span>

</div>


<!-- ========================================================
     4. PANEL PRINCIPAL
======================================================== -->

<section class="panel">

    <div class="panel-head">

        <div>

            <h2 class="panel-title">

                Listado de
                <?= e(mb_strtolower($pageTitle, 'UTF-8')) ?>

            </h2>

            <div class="panel-sub">
                Historial de docentes registrados
            </div>

        </div>

    </div>


    <!-- ====================================================
         5. BUSCADOR
    ==================================================== -->

    <div
        class="toolbar"
        style="margin-bottom: 18px;"
    >

        <div class="search">

            <input
                type="search"
                id="listadoBusqueda"
                placeholder="Buscar docentes..."
                aria-label="Buscar docentes"
            >

        </div>

    </div>


    <!-- ====================================================
         6. TABLA
    ==================================================== -->

    <div class="table-wrap">

        <table id="listadoTabla">

            <thead>

                <tr>

                    <?php foreach ($columnas as $etiqueta): ?>

                        <th>
                            <?= e($etiqueta) ?>
                        </th>

                    <?php endforeach; ?>

                </tr>

            </thead>


            <tbody>

                <?php foreach ($filas as $fila): ?>

                    <tr>

                        <?php foreach ($columnas as $columna => $etiqueta): ?>

                            <td>

                                <?php if ($columna === 'descargar_cv'): ?>

                                    <?php

                                    /* ==============================
                                       DATOS DEL CURRÍCULUM
                                    ============================== */

                                    /* ==========================================================
                                    OBTENER CURRÍCULUM DEL DOCENTE
                                    ========================================================== */

                                    // OBTENER NOMBRE DE USUARIO

                                    $nombreUsuario = trim(
                                        (string) ($fila['nombre_usuario'] ?? '')
                                    );


                                    // CARPETA DE CURRÍCULUMS

                                    $directorio = __DIR__ . '/../../curriculums';


                                    // INICIALIZAR RUTA

                                    $rutaCV = '';


                                    // BUSCAR EL CURRÍCULUM DEL DOCENTE

                                    if (
                                        $nombreUsuario !== '' &&
                                        is_dir($directorio)
                                    ) {

                                        $archivos = glob(
                                            $directorio .
                                            DIRECTORY_SEPARATOR .
                                            $nombreUsuario .
                                            '_*.pdf'
                                        );

                                        foreach ($archivos ?: [] as $archivo) {

                                            if (is_file($archivo)) {

                                                $rutaCV = $archivo;

                                                break;

                                            }

                                        }

                                    }

                                    $nombreUsuario = trim(
                                        (string) (
                                            $fila['nombre_usuario'] ?? ''
                                        )
                                    );

                                    ?>


                                    <?php if (
                                        $rutaCV !== '' &&
                                        $nombreUsuario !== ''
                                    ): ?>

                                        <!-- DESCARGAR CURRÍCULUM -->

                                        <a
                                            class="btn btn-light"
                                            href="?page=descargar_cv&amp;usuario=<?= rawurlencode($nombreUsuario) ?>"
                                            title="Descargar currículum de <?= e($nombreUsuario) ?>"
                                        >

                                            <i class="fa-solid fa-download"></i>

                                            Descargar CV

                                        </a>


                                    <?php else: ?>

                                        <!-- SIN CURRÍCULUM -->

                                        <span
                                            style="
                                                color: #94a3b8;
                                                font-size: 13px;
                                            "
                                        >

                                            No disponible

                                        </span>

                                    <?php endif; ?>


                                <?php else: ?>

                                    <!-- MOSTRAR DATO NORMAL -->

                                    <?= e(
                                        $fila[$columna] ?? '—'
                                    ) ?>

                                <?php endif; ?>

                            </td>

                        <?php endforeach; ?>

                    </tr>

                <?php endforeach; ?>


                <!-- SIN REGISTROS -->

                <?php if (empty($filas)): ?>

                    <tr>

                        <td
                            colspan="<?= max(1, count($columnas)) ?>"
                            style="
                                text-align: center;
                                padding: 25px;
                            "
                        >

                            No hay docentes registrados.

                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>


<!-- ========================================================
     7. BUSCADOR DINÁMICO
======================================================== -->

<script>

    document.getElementById(
        'listadoBusqueda'
    )?.addEventListener(
        'input',
        function() {

            const busqueda = this.value
                .trim()
                .toLocaleLowerCase('es');

            const filas = document.querySelectorAll(
                '#listadoTabla tbody tr'
            );

            filas.forEach(
                function(fila) {

                    const contenido = fila.innerText
                        .toLocaleLowerCase('es');

                    fila.hidden = !contenido.includes(
                        busqueda
                    );

                }
            );

        }
    );

</script>

<?php

/* 1. CARGA DE FUNCIONES COMUNES */

require_once __DIR__ . '/portal_common.php';


/* 2. INICIO DEL DISEÑO DE LA PÁGINA */

portalLayoutInicio(
    'Credencial estudiantil',
    'credencial'
);

?>


<!-- 3. ENCABEZADO Y BOTÓN DE IMPRESIÓN -->

<section class="page-heading no-print">

    <div>

        <p class="eyebrow">
            IDENTIFICACIÓN
        </p>

        <h1>
            Credencial estudiantil
        </h1>

        <p>
            Vista imprimible con los datos confirmados disponibles.
        </p>

    </div>

    <button
        class="btn btn-primary"
        type="button"
        onclick="window.print()"
    >

        <i class="fa-solid fa-print"></i>

        Imprimir

    </button>

</section>


<!-- 4. CONTENEDOR PRINCIPAL DE LA CREDENCIAL -->

<div class="student-credential">


    <!-- 4.1. ENCABEZADO DE LA CREDENCIAL -->

    <div class="credential-header">

        <div class="credential-logo">
            INADI
        </div>

        <div>

            <strong>
                INSTITUTO DE CAPACITACIÓN
            </strong>

            <small>
                Credencial estudiantil · Vista informativa
            </small>

        </div>

    </div>


    <!-- 4.2. DATOS DEL ESTUDIANTE -->

    <div class="credential-body">


        <!-- ICONO REPRESENTATIVO DEL ESTUDIANTE -->

        <div class="credential-photo">

            <i class="fa-solid fa-user"></i>

        </div>


        <!-- INFORMACIÓN DEL USUARIO -->

        <div>

            <span>
                Usuario
            </span>

            <h2>
                <?= e($nombreUsuario) ?>
            </h2>

            <span>
                Estado de identificación
            </span>

            <p>
                No oficial · Requiere validación administrativa
            </p>

        </div>

    </div>


    <!-- 4.3. PIE DE LA CREDENCIAL -->

    <div class="credential-footer">

        Esta vista no sustituye una credencial oficial
        emitida por INADI.

    </div>


</div>


<!-- 5. CIERRE DEL DISEÑO DE LA PÁGINA -->

<?php

portalLayoutFin();

?>

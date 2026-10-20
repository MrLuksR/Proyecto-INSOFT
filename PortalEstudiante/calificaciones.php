
<?php

/* 1. CARGA DE FUNCIONES COMUNES */

require_once __DIR__ . '/portal_common.php';


/* 2. INICIO DEL DISEÑO DE LA PÁGINA */

portalLayoutInicio(
    'Calificaciones',
    'calificaciones'
);

?>


<!-- 3. ENCABEZADO PRINCIPAL -->

<section class="page-heading">

    <div>

        <p class="eyebrow">
            RENDIMIENTO
        </p>

        <h1>
            Calificaciones
        </h1>

        <p>
            Consulta de resultados académicos.
        </p>

    </div>

</section>


<!-- 4. ESTADO DE LAS CALIFICACIONES -->

<?php

portalMostrarVacio(
    'Calificaciones no disponibles todavía',

    'No se muestran notas porque aún no se ha confirmado la tabla de calificaciones ni su relación con el estudiante y el curso.',

    'fa-graduation-cap'
);

?>


<!-- 5. CIERRE DEL DISEÑO DE LA PÁGINA -->

<?php

portalLayoutFin();

?>

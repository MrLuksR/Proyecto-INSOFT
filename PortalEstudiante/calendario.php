
<?php

/* 1. CARGA DE FUNCIONES COMUNES */

require_once __DIR__ . '/portal_common.php';


/* 2. INICIO DEL DISEÑO DE LA PÁGINA */

portalLayoutInicio(
    'Calendario académico',
    'calendario'
);

?>


<!-- 3. ENCABEZADO PRINCIPAL -->

<section class="page-heading">

    <div>

        <p class="eyebrow">
            ORGANIZACIÓN
        </p>

        <h1>
            Calendario académico
        </h1>

        <p>
            Fechas y actividades de tu trayectoria formativa.
        </p>

    </div>

</section>


<!-- 4. ESTADO VACÍO DEL CALENDARIO -->

<?php

portalMostrarVacio(
    'No hay eventos cargados',

    'No se inventan fechas de clases, evaluaciones o actividades. Este módulo podrá conectarse cuando se confirme la tabla de eventos y sus campos.',

    'fa-calendar-days'
);

?>


<!-- 5. CIERRE DEL DISEÑO DE LA PÁGINA -->

<?php

portalLayoutFin();

?>

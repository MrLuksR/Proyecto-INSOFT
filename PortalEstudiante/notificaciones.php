
<?php

/* 1. CARGA DE FUNCIONES COMUNES */

require_once __DIR__ . '/portal_common.php';


/* 2. INICIO DEL DISEÑO DE LA PÁGINA */

portalLayoutInicio(
    'Notificaciones',
    'notificaciones'
);

?>


<!-- 3. ENCABEZADO PRINCIPAL -->

<section class="page-heading">

    <div>

        <p class="eyebrow">
            AVISOS
        </p>

        <h1>
            Notificaciones
        </h1>

        <p>
            Comunicaciones relevantes para tu cuenta.
        </p>

    </div>

</section>


<!-- 4. ESTADO VACÍO DE LAS NOTIFICACIONES -->

<?php

portalMostrarVacio(
    'No hay notificaciones conectadas',

    'No se muestran avisos de ejemplo. Este módulo queda preparado para conectarse cuando se confirme la tabla de notificaciones, sus columnas y sus permisos.',

    'fa-bell'
);

?>


<!-- 5. CIERRE DEL DISEÑO DE LA PÁGINA -->

<?php

portalLayoutFin();

?>

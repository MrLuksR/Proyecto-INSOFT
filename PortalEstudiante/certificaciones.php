<?php

/* 1. CARGA DE FUNCIONES COMUNES */

require_once __DIR__ . '/portal_common.php';


/* 2. INICIO DEL DISEÑO DE LA PÁGINA */

portalLayoutInicio(
    'Certificaciones',
    'certificaciones'
);

?>

<!-- 3. ENCABEZADO PRINCIPAL -->

<section class="page-heading">

    <div>

        <p class="eyebrow">FORMACIÓN</p>

        <h1>Mis certificaciones</h1>

        <p>
            Consultá las certificaciones obtenidas durante tu formación en INADI.
        </p>

    </div>

</section>

<!-- 4. ESTADO DE LAS CERTIFICACIONES -->

<?php

portalMostrarVacio(
    'Todavía no hay certificaciones disponibles',
    'Cuando el instituto emita una certificación a tu nombre, podrás consultarla desde esta sección.',
    'fa-certificate'
);

?>

<!-- 5. CIERRE DEL DISEÑO DE LA PÁGINA -->

<?php

portalLayoutFin();

?>

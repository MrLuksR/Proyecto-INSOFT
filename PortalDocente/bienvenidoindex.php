<?php

require_once __DIR__ . '/portal_common.php';


/* 1. CONFIGURACIÓN INICIAL */

[$titulo, $icono, $kicker] = portal_page_info();


/* 2. MOSTRAR ENCABEZADO DEL PORTAL */

portal_header(
    $titulo,
    $icono,
    $kicker
);


/* 3. INICIALIZAR CONTADORES */

$key = portal_user_key();

$eventCount = 0;

$inboxCount = 0;

$resourceCount = 0;


/* 4. OBTENER DATOS PARA EL RESUMEN DOCENTE */

try {

    if ($key !== null) {

        /* 4.1. CONTAR ACTIVIDADES PRÓXIMAS */

        $query = $pdo->prepare(
            'SELECT COUNT(*)
             FROM portal_eventos
             WHERE owner_key = ?
             AND event_date >= CURDATE()'
        );


        $query->execute([
            $key
        ]);


        $eventCount = (int) $query->fetchColumn();


        /* 4.2. CONTAR MENSAJES SIN LEER */

        $query = $pdo->prepare(
            'SELECT COUNT(*)
             FROM portal_mensajes
             WHERE recipient_key = ?
             AND is_read = 0
             AND recipient_deleted = 0'
        );


        $query->execute([
            $key
        ]);


        $inboxCount = (int) $query->fetchColumn();


        /* 4.3. CONTAR RECURSOS DISPONIBLES */

        $resourceCount = (int) $pdo
            ->query(
                'SELECT COUNT(*)
                 FROM portal_recursos'
            )
            ->fetchColumn();

    }


} catch (Throwable $e) {

    error_log(
        'Portal Docente resumen: ' . $e->getMessage()
    );
}


/* 5. BIENVENIDA AL DOCENTE */

?>

<section class="dashboard-section">

    <div class="information-panel">


        <!-- Icono de bienvenida -->

        <div class="information-icon">

            <i class="fa-solid fa-chalkboard-user"></i>

        </div>


        <!-- Texto de bienvenida -->

        <div>

            <h3>

                ¡Bienvenido/a,
                <?= h(portal_user_name()) ?>!

            </h3>


            <p>

                Desde este espacio podés organizar tus
                actividades académicas, consultar mensajes
                y acceder a materiales de apoyo para la enseñanza.

            </p>

        </div>


    </div>

</section>


<!-- 6. RESUMEN DE LA ACTIVIDAD DOCENTE -->

<section class="dashboard-section">


    <!-- Encabezado de la sección -->

    <div class="section-heading">

        <div>

            <span class="section-kicker">
                RESUMEN
            </span>


            <h2>
                Tu actividad docente
            </h2>

        </div>

    </div>


    <!-- Tarjetas de acceso rápido -->

    <div class="quick-grid">


        <!-- 6.1. CALENDARIO DOCENTE -->

        <a
            class="quick-card"
            href="calendarioindex.php"
        >


            <!-- Icono -->

            <span class="quick-icon blue">

                <i class="fa-solid fa-calendar-days"></i>

            </span>


            <!-- Título -->

            <h3>
                Calendario docente
            </h3>


            <!-- Descripción -->

            <p>

                Clases, evaluaciones y entregas próximas.

            </p>


            <!-- Contador -->

            <strong class="metric-number">

                <?= (int) $eventCount ?>

                actividades próximas

            </strong>


            <!-- Enlace -->

            <span class="card-link">

                Abrir agenda

                <i class="fa-solid fa-arrow-right"></i>

            </span>


        </a>


        <!-- 6.2. MENSAJES -->

        <a
            class="quick-card"
            href="mensajesindex.php"
        >


            <!-- Icono -->

            <span class="quick-icon purple">

                <i class="fa-solid fa-envelope"></i>

            </span>


            <!-- Título -->

            <h3>
                Mensajes
            </h3>


            <!-- Descripción -->

            <p>

                Comunicación con usuarios registrados.

            </p>


            <!-- Contador -->

            <strong class="metric-number">

                <?= (int) $inboxCount ?>

                mensajes sin leer

            </strong>


            <!-- Enlace -->

            <span class="card-link">

                Ver mensajes

                <i class="fa-solid fa-arrow-right"></i>

            </span>


        </a>


        <!-- 6.3. RECURSOS DOCENTES -->

        <a
            class="quick-card"
            href="recursosindex.php"
        >


            <!-- Icono -->

            <span class="quick-icon green">

                <i class="fa-solid fa-lightbulb"></i>

            </span>


            <!-- Título -->

            <h3>
                Recursos docentes
            </h3>


            <!-- Descripción -->

            <p>

                Materiales y enlaces compartidos.

            </p>


            <!-- Contador -->

            <strong class="metric-number">

                <?= (int) $resourceCount ?>

                recursos disponibles

            </strong>


            <!-- Enlace -->

            <span class="card-link">

                Explorar recursos

                <i class="fa-solid fa-arrow-right"></i>

            </span>


        </a>


    </div>

</section>


<!-- 7. INFORMACIÓN Y PRIVACIDAD -->

<section class="dashboard-section">

    <div class="information-panel">


        <!-- Icono de seguridad -->

        <div class="information-icon">

            <i class="fa-solid fa-shield-halved"></i>

        </div>


        <!-- Información -->

        <div>

            <h3>
                Información y privacidad
            </h3>


            <p>

                Las actividades del calendario se guardan para
                tu cuenta. Los mensajes se entregan a usuarios
                registrados y la biblioteca de recursos es
                compartida. No se envían correos electrónicos:
                la comunicación ocurre dentro del portal.

            </p>

        </div>


    </div>

</section>


<!-- 8. PIE DEL PORTAL -->

<?php

portal_footer();

?>

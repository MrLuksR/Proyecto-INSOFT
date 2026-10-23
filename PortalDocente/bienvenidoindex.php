
<?php

require_once __DIR__ . '/portal_common.php';

/* 1. CONFIGURACIÓN INICIAL */

[$titulo, $icono, $kicker] = portal_page_info();

/* 2. INICIALIZAR CONTADORES */

$key = portal_user_key();

$eventCount = 0;
$inboxCount = 0;
$resourceCount = 0;

/* 3. CONSULTAR DATOS DEL PORTAL */

try {

    // Actividades futuras del docente.
    if ($key !== null) {
        $query = $pdo->prepare(
            'SELECT COUNT(*)
             FROM portal_eventos
             WHERE owner_key = ?
               AND event_date >= CURDATE()'
        );

        $query->execute([$key]);
        $eventCount = (int) $query->fetchColumn();

        // Mensajes no leídos recibidos por el docente.
        $query = $pdo->prepare(
            'SELECT COUNT(*)
             FROM portal_mensajes
             WHERE recipient_key = ?
               AND is_read = 0
               AND recipient_deleted = 0'
        );

        $query->execute([$key]);
        $inboxCount = (int) $query->fetchColumn();
    }

    // Recursos compartidos disponibles.
    $resourceCount = (int) $pdo
        ->query('SELECT COUNT(*) FROM portal_recursos')
        ->fetchColumn();

} catch (Throwable $e) {
    error_log(
        'Portal Docente - Resumen: ' . $e->getMessage()
    );
}

/* 4. MOSTRAR CABECERA */

portal_header($titulo, $icono, $kicker);

?>

<!-- 5. BIENVENIDA -->

<section class="dashboard-section">
    <div class="information-panel">

        <div class="information-icon">
            <i class="fa-solid fa-chalkboard-user"
               aria-hidden="true"></i>
        </div>

        <div>
            <h3>
                ¡Bienvenido/a, <?= h(portal_user_name()) ?>!
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

    <div class="section-heading">
        <div>
            <span class="section-kicker">RESUMEN</span>
            <h2>Tu actividad docente</h2>
            <p>Accedé rápidamente a las herramientas de tu portal.</p>
        </div>
    </div>

    <div class="quick-grid">

        <!-- CALENDARIO -->

        <a class="quick-card" href="calendarioindex.php">

            <span class="quick-icon blue">
                <i class="fa-solid fa-calendar-days"
                   aria-hidden="true"></i>
            </span>

            <h3>Calendario docente</h3>

            <p>
                Consultá y organizá clases, evaluaciones
                y entregas próximas.
            </p>

            <strong class="metric-number">
                <?= $eventCount ?>
                <?= $eventCount === 1
                    ? 'actividad próxima'
                    : 'actividades próximas' ?>
            </strong>

            <span class="card-link">
                Abrir agenda
                <i class="fa-solid fa-arrow-right"
                   aria-hidden="true"></i>
            </span>

        </a>

        <!-- MENSAJES -->

        <a class="quick-card" href="mensajesindex.php">

            <span class="quick-icon purple">
                <i class="fa-solid fa-envelope"
                   aria-hidden="true"></i>
            </span>

            <h3>Mensajes</h3>

            <p>
                Consultá la comunicación recibida
                dentro del portal.
            </p>

            <strong class="metric-number">
                <?= $inboxCount ?>
                <?= $inboxCount === 1
                    ? 'mensaje sin leer'
                    : 'mensajes sin leer' ?>
            </strong>

            <span class="card-link">
                Ver mensajes
                <i class="fa-solid fa-arrow-right"
                   aria-hidden="true"></i>
            </span>

        </a>

        <!-- RECURSOS DOCENTES -->

        <a class="quick-card" href="recursosindex.php">

            <span class="quick-icon green">
                <i class="fa-solid fa-lightbulb"
                   aria-hidden="true"></i>
            </span>

            <h3>Recursos docentes</h3>

            <p>
                Encontrá materiales y enlaces de apoyo
                para tus actividades de enseñanza.
            </p>

            <strong class="metric-number">
                <?= $resourceCount ?>
                <?= $resourceCount === 1
                    ? 'recurso disponible'
                    : 'recursos disponibles' ?>
            </strong>

            <span class="card-link">
                Explorar recursos
                <i class="fa-solid fa-arrow-right"
                   aria-hidden="true"></i>
            </span>

        </a>

    </div>
</section>

<!-- 7. INFORMACIÓN Y PRIVACIDAD -->

<section class="dashboard-section">
    <div class="information-panel">

        <div class="information-icon">
            <i class="fa-solid fa-shield-halved"
               aria-hidden="true"></i>
        </div>

        <div>
            <h3>Información y privacidad</h3>

            <p>
                Las actividades del calendario se guardan para
                tu cuenta. Los mensajes se entregan a usuarios
                registrados y la biblioteca de recursos es
                compartida. La comunicación ocurre dentro
                del portal.
            </p>
        </div>

    </div>
</section>

<?php

/* 8. PIE DEL PORTAL */

portal_footer();

?>

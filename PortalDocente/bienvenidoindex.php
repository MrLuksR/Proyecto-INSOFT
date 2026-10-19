<?php
require_once __DIR__ . '/portal_common.php';
[$titulo, $icono, $kicker] = portal_page_info();
portal_header($titulo, $icono, $kicker);
try {
    $key = portal_user_key();
    $eventCount = 0; $inboxCount = 0; $resourceCount = 0;
    if ($key !== null) {
        $q = $pdo->prepare('SELECT COUNT(*) FROM portal_eventos WHERE owner_key = ? AND event_date >= CURDATE()'); $q->execute([$key]); $eventCount = (int)$q->fetchColumn();
        $q = $pdo->prepare('SELECT COUNT(*) FROM portal_mensajes WHERE recipient_key = ? AND is_read = 0'); $q->execute([$key]); $inboxCount = (int)$q->fetchColumn();
        $resourceCount = (int)$pdo->query('SELECT COUNT(*) FROM portal_recursos')->fetchColumn();
    }
} catch (Throwable $e) { error_log('Portal Docente resumen: '.$e->getMessage()); }
?>
<section class="dashboard-section"><div class="information-panel"><div class="information-icon"><i class="fa-solid fa-chalkboard-user"></i></div><div><h3>¡Bienvenido/a, <?= h(portal_user_name()) ?>!</h3><p>Desde este espacio podés organizar tus actividades académicas, consultar mensajes y acceder a materiales de apoyo para la enseñanza.</p></div></div></section>
<section class="dashboard-section"><div class="section-heading"><div><span class="section-kicker">RESUMEN</span><h2>Tu actividad docente</h2></div></div><div class="quick-grid">
<a class="quick-card" href="calendarioindex.php"><span class="quick-icon blue"><i class="fa-solid fa-calendar-days"></i></span><h3>Calendario docente</h3><p>Clases, evaluaciones y entregas próximas.</p><strong class="metric-number"><?= (int)$eventCount ?> actividades próximas</strong><span class="card-link">Abrir agenda <i class="fa-solid fa-arrow-right"></i></span></a>
<a class="quick-card" href="mensajesindex.php"><span class="quick-icon purple"><i class="fa-solid fa-envelope"></i></span><h3>Mensajes</h3><p>Comunicación con usuarios registrados.</p><strong class="metric-number"><?= (int)$inboxCount ?> mensajes sin leer</strong><span class="card-link">Ver mensajes <i class="fa-solid fa-arrow-right"></i></span></a>
<a class="quick-card" href="recursosindex.php"><span class="quick-icon green"><i class="fa-solid fa-lightbulb"></i></span><h3>Recursos docentes</h3><p>Materiales y enlaces compartidos.</p><strong class="metric-number"><?= (int)$resourceCount ?> recursos disponibles</strong><span class="card-link">Explorar recursos <i class="fa-solid fa-arrow-right"></i></span></a>
</div></section>
<section class="dashboard-section"><div class="information-panel"><div class="information-icon"><i class="fa-solid fa-shield-halved"></i></div><div><h3>Información y privacidad</h3><p>Las actividades del calendario se guardan para tu cuenta. Los mensajes se entregan a usuarios registrados y la biblioteca de recursos es compartida. No se envían correos electrónicos: la comunicación ocurre dentro del portal.</p></div></div></section>
<?php portal_footer(); ?>

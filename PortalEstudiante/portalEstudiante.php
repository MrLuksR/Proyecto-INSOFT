<?php
require_once __DIR__ . '/portal_common.php';

/* Datos del portal estudiantil */
$matriculas = [];
$errorDatos = false;

if (isset($pdo) && $pdo instanceof PDO && !$errorConexion && $haySesionEstudiante) {
    try {
        $matriculas = portalObtenerMatriculas($pdo, $nombreUsuario);
    } catch (Throwable $e) {
        $matriculas = [];
        $errorDatos = true;
    }
}

$activas = array_filter($matriculas, static function ($m) {
    return isset($m['estado'])
        && mb_strtolower(trim((string) $m['estado']), 'UTF-8') === 'activa';
});

portalLayoutInicio('Inicio', 'portal');
?>

<section class="welcome-panel">
    <div>
        <p class="eyebrow">TU ESPACIO ACADÉMICO</p>
        <h1>¡Hola, <?= e($nombreUsuario) ?>!</h1>
        <p>Desde aquí podés consultar tus cursos, información académica y servicios estudiantiles.</p>
        <a class="btn btn-light" href="misCursos.php">Ver mis cursos</a>
    </div>
    <div class="welcome-art" aria-hidden="true">
        <i class="fa-solid fa-shield-halved"></i>
    </div>
</section>

<?php if ($errorDatos): ?>
    <div class="alert alert-warning">
        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
        <span>No se pudieron cargar las matrículas en este momento.</span>
    </div>
<?php endif; ?>

<section class="stats-grid">
    <article class="stat-card">
        <span class="stat-icon purple"><i class="fa-solid fa-book-open"></i></span>
        <div>
            <p>Mis matrículas</p>
            <strong><?= count($matriculas) ?></strong>
        </div>
    </article>

    <article class="stat-card">
        <span class="stat-icon orange"><i class="fa-solid fa-circle-check"></i></span>
        <div>
            <p>Matrículas activas</p>
            <strong><?= count($activas) ?></strong>
        </div>
    </article>

    <article class="stat-card">
        <span class="stat-icon blue"><i class="fa-solid fa-calendar-days"></i></span>
        <div>
            <p>Calendario</p>
            <strong>—</strong>
            <small>Sin eventos conectados</small>
        </div>
    </article>
</section>

<section class="section-block">
    <div class="section-heading">
        <div>
            <p class="eyebrow">ACCESOS RÁPIDOS</p>
            <h2>¿Qué necesitás consultar?</h2>
        </div>
    </div>

    <div class="module-grid">
        <a class="module-card" href="misCursos.php">
            <span class="module-icon"><i class="fa-solid fa-book-open"></i></span>
            <h3>Mis cursos</h3>
            <p>Consultá las matrículas asociadas a tu usuario.</p>
            <span class="module-link">Ingresar <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a class="module-card" href="aulaVirtual.php">
            <span class="module-icon"><i class="fa-solid fa-chalkboard-user"></i></span>
            <h3>Aula virtual</h3>
            <p>Acceso a aulas y recursos disponibles.</p>
            <span class="module-link">Ingresar <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a class="module-card" href="legajo.php">
            <span class="module-icon"><i class="fa-solid fa-folder-open"></i></span>
            <h3>Mi legajo</h3>
            <p>Historial de matrículas registradas.</p>
            <span class="module-link">Ingresar <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a class="module-card" href="calificaciones.php">
            <span class="module-icon"><i class="fa-solid fa-graduation-cap"></i></span>
            <h3>Calificaciones</h3>
            <p>Consultá tus calificaciones disponibles.</p>
            <span class="module-link">Ingresar <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a class="module-card" href="perfil.php">
            <span class="module-icon"><i class="fa-solid fa-user"></i></span>
            <h3>Mi perfil</h3>
            <p>Datos básicos vinculados a tu sesión.</p>
            <span class="module-link">Ingresar <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a class="module-card" href="calendario.php">
            <span class="module-icon"><i class="fa-solid fa-calendar-days"></i></span>
            <h3>Calendario</h3>
            <p>Espacio para eventos académicos.</p>
            <span class="module-link">Ingresar <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a class="module-card" href="credencial.php">
            <span class="module-icon"><i class="fa-solid fa-id-card"></i></span>
            <h3>Credencial</h3>
            <p>Vista imprimible con los datos disponibles.</p>
            <span class="module-link">Ingresar <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a class="module-card" href="misPagos.php">
            <span class="module-icon"><i class="fa-solid fa-credit-card"></i></span>
            <h3>Mis pagos</h3>
            <p>Consultá referencias asociadas a tus matrículas.</p>
            <span class="module-link">Ingresar <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a class="module-card" href="notificaciones.php">
            <span class="module-icon"><i class="fa-solid fa-bell"></i></span>
            <h3>Notificaciones</h3>
            <p>Consultá los avisos disponibles en el sistema.</p>
            <span class="module-link">Ingresar <i class="fa-solid fa-arrow-right"></i></span>
        </a>
    </div>
</section>

<?php portalLayoutFin(); ?>

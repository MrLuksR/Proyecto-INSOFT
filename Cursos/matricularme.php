<?php
include("../auth/config.php");
include("../database/Clases/Estudiante.php");
require_once("../database/consultas/cursos/getCursos.php");
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require_once __DIR__ . '/../database/consultas/conexion.php';
$anioActual = date('Y');
$idCurso = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idCurso || $idCurso <= 0) { header('Location: cursosindex.php'); exit; }
$sql = "SELECT curso.id_curso, curso.nombre, curso.modalidad, curso.duracion, curso.costo, curso.img, curso.estado, categoria_cursos.nombre AS categoria FROM curso INNER JOIN categoria_cursos ON curso.id_categoria = categoria_cursos.id_categoria WHERE curso.id_curso = :idCurso LIMIT 1";
$stmt = $pdo->prepare($sql); $stmt->execute([':idCurso' => $idCurso]); $curso = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$curso) { header('Location: cursosindex.php'); exit; }
function escMatricula($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
$nombreCurso = escMatricula($curso['nombre']);
$errores = [];
$enviado = false;
$datos = ['nombre' => '', 'apellido' => '', 'email' => '', 'telefono' => '', 'modalidad' => (string)($curso['modalidad'] ?? '')];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos as $campo => $valor) { $datos[$campo] = trim((string)($_POST[$campo] ?? $valor)); }
    if ($datos['nombre'] === '') $errores[] = 'Ingresá tu nombre.';
    if ($datos['apellido'] === '') $errores[] = 'Ingresá tu apellido.';
    if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) $errores[] = 'Ingresá un correo electrónico válido.';
    if ($datos['telefono'] === '') $errores[] = 'Ingresá un teléfono de contacto.';
    if (!isset($_POST['acepto'])) $errores[] = 'Debés confirmar que los datos ingresados son correctos.';
    $modalidadesPermitidas = ['Presencial', 'Virtual', 'Semipresencial', 'A confirmar'];
    if (!in_array($datos['modalidad'], $modalidadesPermitidas, true)) {
        $errores[] = 'Seleccioná una modalidad válida.';
    }

    // Conservamos los datos validados en sesión para no depender de campos ocultos
    // ni aceptar el precio enviado por el navegador.
    if (!$errores) {
        session_regenerate_id(true);
        $_SESSION['inscripcion_pendiente'] = [
            'id_curso' => (int)$idCurso,
            'nombre' => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'],
            'modalidad' => $datos['modalidad'],
            'creada_en' => date('c')
        ];
        header('Location: pago.php', true, 303);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>INADI | Matriculación</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="../Inicio/CSS/stylesinicio.css?v=8">
<link rel="stylesheet" href="cursostyles.css?v=6">
<link rel="stylesheet" href="matricularme.css?v=1">
<link rel="stylesheet" href="/Proyecto-INSOFT/Estructura/topbar-controls.css?v=1">
</head><body><div class="page">
<header class="topbar"><div class="topbar-left"><a href="../Inicio/inicioindex.php" class="top-logo"><img src="../Elementos Gráficos/Logo Inadi con Brillo.png" alt="Logo INADI"><span>INADI</span></a><div class="news"><i class="fa-solid fa-triangle-exclamation"></i><span>NOTICIAS</span></div></div>
<div class="top-search"><form action="cursosindex.php" method="GET"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="buscar" placeholder="Buscar información..." aria-label="Buscar información"><button type="submit">Buscar</button></form></div>
<div class="topbar-right"><?php setPortalBtn(); inadiLanguageControl(); setLogin(); ?></div></header>
<aside class="sidebar" aria-label="Menú principal"><button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Contraer menú" aria-expanded="true" title="Contraer menú"><i class="fa-solid fa-angles-left"></i></button>
<ul class="menu"><li><a href="../Inicio/inicioindex.php"><i class="fa-solid fa-house"></i><span>Inicio</span></a></li><li><a href="../Nosotros/nosotros.php"><i class="fa-solid fa-building-columns"></i><span>Nosotros</span></a></li><li class="active"><a href="cursosindex.php" aria-current="page"><i class="fa-solid fa-book"></i><span>Cursos</span></a></li><li><a href="../Contacto/contacto.php"><i class="fa-solid fa-envelope"></i><span>Contacto</span></a></li><li><a href="../Galeria/galeria.php"><i class="fa-solid fa-image"></i><span>Galería</span></a></li><li><a href="../Convenios/convenios.php"><i class="fa-solid fa-handshake"></i><span>Convenios</span></a></li><li><a href="../Empresas/empresas.php"><i class="fa-solid fa-city"></i><span>Empresas</span></a></li></ul>
<div class="sidebar-bottom">© INADI <?php echo escMatricula($anioActual); ?><br>Instituto de Informática<br>Salto - Uruguay</div></aside>
<div class="content"><main class="main matricula-main"><a href="curso.php?id=<?php echo (int)$idCurso; ?>" class="back-button"><i class="fa-solid fa-arrow-left"></i> Volver al curso</a>
<div class="matricula-heading"><span class="matricula-kicker"><i class="fa-solid fa-graduation-cap"></i> INSCRIPCIONES INADI</span><h1>Matricularme a un curso</h1><p>Completá tus datos para continuar con la inscripción.</p></div>
<section class="matricula-layout"><aside class="matricula-resumen"><div class="resumen-icon"><i class="fa-solid fa-laptop-code"></i></div><span class="resumen-label">CURSO SELECCIONADO</span><h2><?php echo $nombreCurso; ?></h2><div class="resumen-dato"><i class="fa-solid fa-layer-group"></i><span><?php echo escMatricula($curso['categoria'] ?? 'Informática'); ?></span></div><div class="resumen-dato"><i class="fa-solid fa-desktop"></i><span><?php echo escMatricula($curso['modalidad'] ?? 'A confirmar'); ?></span></div><div class="resumen-dato"><i class="fa-regular fa-clock"></i><span><?php echo escMatricula($curso['duracion'] ?? 'Duración a confirmar'); ?></span></div><?php if (($curso['costo'] ?? '') !== ''): ?><div class="resumen-precio">$<?php echo escMatricula($curso['costo']); ?></div><?php endif; ?><a href="curso.php?id=<?php echo (int)$idCurso; ?>" class="resumen-link">Ver detalles del curso <i class="fa-solid fa-arrow-up-right-from-square"></i></a></aside>
<div class="matricula-form-card">

<?php if ($errores): ?><div class="matricula-aviso aviso-error"><i class="fa-solid fa-circle-exclamation"></i><div><strong>Revisá los siguientes datos:</strong><ul><?php foreach ($errores as $error): ?><li><?php echo escMatricula($error); ?></li><?php endforeach; ?></ul></div></div><?php endif; ?>
<h2>Tus datos</h2><p class="form-intro">Usaremos esta información para contactarte sobre la inscripción.</p>
<form method="POST" action="matricularme.php?id=<?php echo (int)$idCurso; ?>" class="form-matricula" novalidate>
<div class="form-row"><label>Nombre <span>*</span><input type="text" name="nombre" value="<?php echo escMatricula($datos['nombre']); ?>" autocomplete="given-name" required></label><label>Apellido <span>*</span><input type="text" name="apellido" value="<?php echo escMatricula($datos['apellido']); ?>" autocomplete="family-name" required></label></div>
<div class="form-row"><label>Correo electrónico <span>*</span><input type="email" name="email" value="<?php echo escMatricula($datos['email']); ?>" placeholder="tu@correo.com" autocomplete="email" required></label><label>Teléfono <span>*</span><input type="tel" name="telefono" value="<?php echo escMatricula($datos['telefono']); ?>" placeholder="099 123 456" autocomplete="tel" required></label></div>
<label>Modalidad de cursado<select name="modalidad"><option value="Presencial" <?php echo $datos['modalidad']==='Presencial'?'selected':''; ?>>Presencial</option><option value="Virtual" <?php echo $datos['modalidad']==='Virtual'?'selected':''; ?>>Virtual</option><option value="Semipresencial" <?php echo $datos['modalidad']==='Semipresencial'?'selected':''; ?>>Semipresencial</option><option value="A confirmar" <?php echo $datos['modalidad']==='A confirmar'?'selected':''; ?>>A confirmar</option></select></label>
<label class="acepto-label"><input type="checkbox" name="acepto" value="1" <?php echo isset($_POST['acepto'])?'checked':''; ?> required><span>Confirmo que los datos ingresados son correctos.</span></label>
<button type="submit" class="btn-confirmar"><i class="fa-solid fa-paper-plane"></i> Continuar con mi inscripción</button><p class="form-privacidad"><i class="fa-solid fa-lock"></i> Tus datos deben tratarse de forma responsable y segura.</p>
</form></div></section></main></div>
<footer><div class="footer-content"><div class="footer-column"><div class="footer-brand-title"><i class="fa-solid fa-graduation-cap"></i><span>INADI</span></div><p class="footer-description">Instituto de Informática comprometido con la formación tecnológica y el desarrollo educativo.</p><p class="footer-description">© INADI <?php echo escMatricula($anioActual); ?>. Todos los derechos reservados.</p></div><div class="footer-column"><h3>CONTACTO</h3><p><i class="fa-solid fa-location-dot"></i><span>Artigas 827, Salto, Uruguay</span></p><p><i class="fa-solid fa-building"></i><span>Instituto de Informática</span></p></div><div class="footer-column"><h3>FORMACIÓN</h3><p><i class="fa-solid fa-book"></i><span>Cursos de informática</span></p><p><i class="fa-solid fa-laptop"></i><span>Formación tecnológica</span></p></div><div class="footer-column"><h3>NAVEGACIÓN</h3><p><i class="fa-solid fa-house"></i><a href="../Inicio/inicioindex.php">Inicio</a></p><p><i class="fa-solid fa-building-columns"></i><a href="../Nosotros/nosotros.php">Nosotros</a></p><p><i class="fa-solid fa-envelope"></i><a href="../Contacto/contacto.php">Contacto</a></p></div></div></footer></div>
<script>document.addEventListener('DOMContentLoaded',function(){const sidebar=document.querySelector('.sidebar'),page=document.querySelector('.page'),toggle=document.getElementById('sidebarToggle');if(!sidebar||!page||!toggle)return;toggle.addEventListener('click',function(){const collapsed=sidebar.classList.toggle('sidebar-collapsed');page.classList.toggle('sidebar-collapsed',collapsed);toggle.setAttribute('aria-expanded',String(!collapsed));toggle.setAttribute('aria-label',collapsed?'Mostrar menú':'Contraer menú');toggle.title=collapsed?'Mostrar menú':'Contraer menú';const icon=toggle.querySelector('i');if(icon){icon.classList.toggle('fa-angles-left',!collapsed);icon.classList.toggle('fa-angles-right',collapsed);}});});</script>
</body></html>

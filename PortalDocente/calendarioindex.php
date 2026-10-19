<?php
require_once __DIR__ . '/portal_common.php';
[$titulo, $icono, $kicker] = portal_page_info();
$key = portal_user_key();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    portal_verify_csrf();
    $key = portal_require_user_key();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'save') {
            $id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT) ?: null;
            $title = trim((string)($_POST['title'] ?? ''));
            $type = trim((string)($_POST['event_type'] ?? 'Clase'));
            $date = trim((string)($_POST['event_date'] ?? ''));
            $time = trim((string)($_POST['event_time'] ?? ''));
            $details = trim((string)($_POST['details'] ?? ''));
            $allowed = ['Clase','Evaluación','Entrega','Reunión','Otro'];
            $d = DateTime::createFromFormat('!Y-m-d', $date);
            if ($title === '' || mb_strlen($title) > 120 || !$d || $d->format('Y-m-d') !== $date || !in_array($type, $allowed, true) || mb_strlen($details) > 1500) {
                throw new InvalidArgumentException('Revisá el título, tipo, fecha y detalles de la actividad.');
            }
            if ($time !== '' && !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) throw new InvalidArgumentException('La hora no tiene un formato válido.');
            if ($id) {
                $q = $pdo->prepare('UPDATE portal_eventos SET title=?, event_type=?, event_date=?, event_time=?, details=?, updated_at=NOW() WHERE id=? AND owner_key=?');
                $q->execute([$title,$type,$date,$time !== '' ? $time : null,$details ?: null,$id,$key]);
                if (!$q->rowCount()) {
                    $check=$pdo->prepare('SELECT id FROM portal_eventos WHERE id=? AND owner_key=?'); $check->execute([$id,$key]);
                    if (!$check->fetch()) throw new RuntimeException('No se encontró esa actividad o no tenés permiso para modificarla.');
                }
                portal_flash('success','La actividad se actualizó correctamente.');
            } else {
                $q = $pdo->prepare('INSERT INTO portal_eventos (owner_key, owner_name, title, event_type, event_date, event_time, details, created_at, updated_at) VALUES (?,?,?,?,?,?,?,NOW(),NOW())');
                $q->execute([$key, portal_user_name(), $title, $type, $date, $time !== '' ? $time : null, $details ?: null]);
                portal_flash('success','La actividad se agregó al calendario.');
            }
            header('Location: calendarioindex.php'); exit;
        } elseif ($action === 'delete') {
            $id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
            if (!$id) throw new InvalidArgumentException('La actividad seleccionada no es válida.');
            $q=$pdo->prepare('DELETE FROM portal_eventos WHERE id=? AND owner_key=?'); $q->execute([$id,$key]);
            portal_flash($q->rowCount() ? 'success' : 'error', $q->rowCount() ? 'La actividad se eliminó.' : 'No se encontró la actividad o no tenés permiso para eliminarla.');
            header('Location: calendarioindex.php'); exit;
        }
    } catch (InvalidArgumentException $e) { portal_flash('error',$e->getMessage()); header('Location: calendarioindex.php' . (isset($id) && $id ? '?edit='.(int)$id : '')); exit; }
      catch (Throwable $e) { portal_db_error($e,'No se pudo guardar la actividad. Verificá que ejecutaste instalar_portal.sql.'); header('Location: calendarioindex.php'); exit; }
}
$editEvent = null;
if (isset($_GET['edit']) && $key !== null) {
    $q=$pdo->prepare('SELECT * FROM portal_eventos WHERE id=? AND owner_key=?'); $q->execute([(int)$_GET['edit'],$key]); $editEvent=$q->fetch() ?: null;
}
$events=[];
$monthLabels = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
try { if ($key !== null) { $q=$pdo->prepare('SELECT * FROM portal_eventos WHERE owner_key=? ORDER BY event_date ASC, event_time ASC, id DESC'); $q->execute([$key]); $events=$q->fetchAll(); } }
catch (Throwable $e) { error_log('Portal calendario: '.$e->getMessage()); portal_flash('error','No se pudieron cargar las actividades. Ejecutá instalar_portal.sql.'); }
portal_header($titulo,$icono,$kicker);
?>
<section class="dashboard-section"><div class="section-heading"><div><span class="section-kicker">PLANIFICACIÓN</span><h2><?= $editEvent ? 'Editar actividad' : 'Agregar actividad' ?></h2></div></div>
<form class="form-panel" method="post" action="calendarioindex.php" autocomplete="off"><input type="hidden" name="csrf_token" value="<?= h(portal_csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="event_id" value="<?= h($editEvent['id'] ?? '') ?>">
<div class="form-grid"><label>Actividad<input type="text" name="title" required maxlength="120" value="<?= h($editEvent['title'] ?? '') ?>" placeholder="Ej.: Clase de programación"></label>
<label>Tipo<select name="event_type" required><?php foreach(['Clase','Evaluación','Entrega','Reunión','Otro'] as $type): ?><option value="<?= h($type) ?>" <?= ($editEvent['event_type'] ?? 'Clase')===$type?'selected':'' ?>><?= h($type) ?></option><?php endforeach; ?></select></label>
<label>Fecha<input type="date" name="event_date" required value="<?= h($editEvent['event_date'] ?? '') ?>"></label><label>Hora<input type="time" name="event_time" value="<?= h(isset($editEvent['event_time']) ? substr((string)$editEvent['event_time'],0,5) : '') ?>"></label></div>
<label>Detalles<textarea name="details" rows="3" maxlength="1500" placeholder="Aula, tema o recordatorios (opcional)"><?= h($editEvent['details'] ?? '') ?></textarea></label>
<div class="form-actions"><button class="primary-button" type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= $editEvent ? 'Guardar cambios' : 'Agregar al calendario' ?></button><?php if($editEvent): ?><a class="secondary-button" href="calendarioindex.php">Cancelar edición</a><?php endif; ?></div>
<p class="form-note">Las actividades se guardan en MySQL y son visibles para la cuenta que las creó.</p></form></section>
<section class="dashboard-section"><div class="section-heading"><div><span class="section-kicker">AGENDA</span><h2>Tus actividades</h2></div><span class="count-pill"><?= count($events) ?> en total</span></div>
<div class="event-list">
<?php if (!$key): ?><div class="empty-state"><i class="fa-solid fa-user-lock"></i><h3>Iniciá sesión para usar tu agenda</h3><p>La agenda necesita identificar tu cuenta para guardar actividades privadas.</p></div>
<?php elseif (!$events): ?><div class="empty-state"><i class="fa-regular fa-calendar"></i><h3>Tu agenda está vacía</h3><p>Agregá una actividad usando el formulario.</p></div>
<?php else: foreach($events as $event): $timestamp=strtotime($event['event_date']); ?>
<article class="event-item"><div class="event-datebox"><strong><?= date('d',$timestamp) ?></strong><span><?= h($monthLabels[(int)date('n',$timestamp)-1]) ?></span></div><div class="event-info"><div class="event-title-line"><h3><?= h($event['title']) ?></h3><span class="type-pill"><?= h($event['event_type']) ?></span></div><p><?= h(date('d/m/Y',$timestamp)) ?><?= !empty($event['event_time']) ? ' · '.h(substr($event['event_time'],0,5)) : '' ?></p><?php if(!empty($event['details'])): ?><p><?= nl2br(h($event['details'])) ?></p><?php endif; ?><div class="event-meta">Creada por vos · <?= h($event['owner_name'] ?? portal_user_name()) ?></div></div><div class="event-actions"><a class="icon-action" href="calendarioindex.php?edit=<?= (int)$event['id'] ?>" aria-label="Editar actividad"><i class="fa-solid fa-pen"></i></a><form method="post" onsubmit="return confirm('¿Querés eliminar esta actividad?');"><input type="hidden" name="csrf_token" value="<?= h(portal_csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>"><button class="icon-action danger" type="submit" aria-label="Eliminar actividad"><i class="fa-regular fa-trash-can"></i></button></form></div></article>
<?php endforeach; endif; ?></div></section>
<?php portal_footer(); ?>

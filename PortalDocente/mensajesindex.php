<?php
require_once __DIR__ . '/portal_common.php';
[$titulo, $icono, $kicker] = portal_page_info();
$key = portal_user_key();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    portal_verify_csrf(); $key = portal_require_user_key();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'send') {
            $recipientKey=trim((string)($_POST['recipient_key'] ?? ''));
            $subject=trim((string)($_POST['subject'] ?? ''));
            $body=trim((string)($_POST['body'] ?? ''));
            if ($recipientKey==='' || $subject==='' || $body==='') throw new InvalidArgumentException('Completá destinatario, asunto y mensaje.');
            if (mb_strlen($subject)>160 || mb_strlen($body)>5000) throw new InvalidArgumentException('El asunto o el mensaje supera la longitud permitida.');
            if ($recipientKey===$key) throw new InvalidArgumentException('No podés enviarte un mensaje a vos mismo.');
            $recipientName=null;
            $users=$pdo->query('SELECT cedula, nombre, apellido, nombre_usuario, correo FROM usuarios');
            foreach($users as $u) {
                $uKey = !empty($u['cedula']) ? 'cedula:'.mb_strtolower(trim((string)$u['cedula']),'UTF-8') : (!empty($u['correo']) ? 'correo:'.mb_strtolower(trim((string)$u['correo']),'UTF-8') : (!empty($u['nombre_usuario']) ? 'usuario:'.mb_strtolower(trim((string)$u['nombre_usuario']),'UTF-8') : null));
                if ($uKey === $recipientKey) { $recipientName=trim(($u['nombre'] ?? '').' '.($u['apellido'] ?? '')); if($recipientName==='') $recipientName=$u['nombre_usuario'] ?? $u['correo'] ?? 'Usuario'; break; }
            }
            if ($recipientName===null) throw new InvalidArgumentException('El destinatario ya no está disponible. Actualizá la página y elegí un usuario registrado.');
            $q=$pdo->prepare('INSERT INTO portal_mensajes (sender_key,sender_name,recipient_key,recipient_name,subject,body,is_read,created_at) VALUES (?,?,?,?,?,?,0,NOW())');
            $q->execute([$key,portal_user_name(),$recipientKey,$recipientName,$subject,$body]);
            portal_flash('success','Mensaje enviado dentro del Portal Docente.'); header('Location: mensajesindex.php?view=sent'); exit;
        } elseif ($action === 'read') {
            $id=filter_input(INPUT_POST,'message_id',FILTER_VALIDATE_INT);
            if($id){$q=$pdo->prepare('UPDATE portal_mensajes SET is_read=1 WHERE id=? AND recipient_key=?');$q->execute([$id,$key]);}
            header('Location: mensajesindex.php?view=inbox'); exit;
        } elseif ($action === 'delete') {
            $id=filter_input(INPUT_POST,'message_id',FILTER_VALIDATE_INT);
            if(!$id) throw new InvalidArgumentException('Mensaje no válido.');
            $check=$pdo->prepare('SELECT sender_key,recipient_key FROM portal_mensajes WHERE id=? AND (sender_key=? OR recipient_key=?)'); $check->execute([$id,$key,$key]); $owned=$check->fetch();
            if (!$owned) throw new InvalidArgumentException('No se encontró ese mensaje.');
            if ($owned['sender_key'] === $key) { $q=$pdo->prepare('UPDATE portal_mensajes SET sender_deleted=1 WHERE id=? AND sender_key=?'); $q->execute([$id,$key]); }
            else { $q=$pdo->prepare('UPDATE portal_mensajes SET recipient_deleted=1 WHERE id=? AND recipient_key=?'); $q->execute([$id,$key]); }
            $cleanup=$pdo->prepare('DELETE FROM portal_mensajes WHERE id=? AND sender_deleted=1 AND recipient_deleted=1'); $cleanup->execute([$id]);
            portal_flash('success','El mensaje se eliminó de tu vista.'); header('Location: mensajesindex.php?view='.urlencode($_GET['view'] ?? 'inbox')); exit;
        }
    } catch (InvalidArgumentException $e) { portal_flash('error',$e->getMessage()); header('Location: mensajesindex.php'); exit; }
      catch (Throwable $e) { portal_db_error($e,'No se pudo procesar el mensaje. Verificá la tabla usuarios y ejecutá instalar_portal.sql.'); header('Location: mensajesindex.php'); exit; }
}
$view = ($_GET['view'] ?? 'inbox') === 'sent' ? 'sent' : 'inbox';
$recipients=[]; $messages=[]; $unread=0;
try {
    if ($key !== null) {
        $stmt=$pdo->query('SELECT cedula,nombre,apellido,nombre_usuario,correo FROM usuarios ORDER BY nombre,apellido');
        foreach($stmt as $u) {
            $uKey = !empty($u['cedula']) ? 'cedula:'.mb_strtolower(trim((string)$u['cedula']),'UTF-8') : (!empty($u['correo']) ? 'correo:'.mb_strtolower(trim((string)$u['correo']),'UTF-8') : (!empty($u['nombre_usuario']) ? 'usuario:'.mb_strtolower(trim((string)$u['nombre_usuario']),'UTF-8') : null));
            if (!$uKey || $uKey === $key) continue;
            $label=trim(($u['nombre'] ?? '').' '.($u['apellido'] ?? ''));
            if($label==='') $label=$u['nombre_usuario'] ?? $u['correo'] ?? 'Usuario';
            if(!empty($u['correo'])) $label .= ' · '.$u['correo'];
            $recipients[$uKey]=$label;
        }
        $q=$pdo->prepare('SELECT COUNT(*) FROM portal_mensajes WHERE recipient_key=? AND is_read=0');$q->execute([$key]);$unread=(int)$q->fetchColumn();
        if($view==='sent'){$q=$pdo->prepare('SELECT * FROM portal_mensajes WHERE sender_key=? AND sender_deleted=0 ORDER BY created_at DESC,id DESC');}
        else {$q=$pdo->prepare('SELECT * FROM portal_mensajes WHERE recipient_key=? AND recipient_deleted=0 ORDER BY created_at DESC,id DESC');}
        $q->execute([$key]);$messages=$q->fetchAll();
    }
} catch(Throwable $e) { error_log('Portal mensajes: '.$e->getMessage()); portal_flash('error','No se pudieron cargar los mensajes. Ejecutá instalar_portal.sql y verificá la tabla usuarios.'); }
portal_header($titulo,$icono,$kicker);
?>
<section class="dashboard-section"><div class="information-panel"><div class="information-icon purple-bg"><i class="fa-solid fa-comments"></i></div><div><h3>Comunicación educativa</h3><p>Los mensajes se guardan en el portal para que el destinatario pueda consultarlos al iniciar sesión. Esta función no envía correos electrónicos.</p></div></div></section>
<section class="dashboard-section"><div class="section-heading"><div><span class="section-kicker">REDACTAR</span><h2>Nuevo mensaje</h2></div></div>
<form class="form-panel" method="post" action="mensajesindex.php"><input type="hidden" name="csrf_token" value="<?= h(portal_csrf_token()) ?>"><input type="hidden" name="action" value="send">
<label>Destinatario<select name="recipient_key" required><option value="">Seleccioná un usuario registrado</option><?php foreach($recipients as $recipientKey=>$label): ?><option value="<?= h($recipientKey) ?>"><?= h($label) ?></option><?php endforeach; ?></select></label>
<label>Asunto<input name="subject" required maxlength="160" placeholder="Asunto del mensaje"></label><label>Mensaje<textarea name="body" required rows="5" maxlength="5000" placeholder="Escribí aquí tu mensaje..."></textarea></label>
<button class="primary-button" type="submit"><i class="fa-solid fa-paper-plane"></i> Enviar mensaje</button><p class="form-note">Si no aparecen destinatarios, verificá que existan usuarios en la tabla usuarios.</p></form></section>
<section class="dashboard-section"><div class="section-heading"><div><span class="section-kicker">BANDEJA DE MENSAJES</span><h2><?= $view==='sent'?'Enviados':'Recibidos' ?></h2></div><span class="count-pill"><?= $view==='inbox' ? $unread.' sin leer' : count($messages).' enviados' ?></span></div>
<div class="mail-tabs"><a class="<?= $view==='inbox'?'selected':'' ?>" href="mensajesindex.php?view=inbox"><i class="fa-solid fa-inbox"></i> Recibidos</a><a class="<?= $view==='sent'?'selected':'' ?>" href="mensajesindex.php?view=sent"><i class="fa-solid fa-paper-plane"></i> Enviados</a></div>
<div class="message-list">
<?php if(!$key): ?><div class="empty-state"><i class="fa-solid fa-user-lock"></i><h3>Iniciá sesión para ver tus mensajes</h3><p>Los mensajes se vinculan con tu cuenta.</p></div>
<?php elseif(!$messages): ?><div class="empty-state"><i class="fa-regular fa-envelope-open"></i><h3><?= $view==='sent'?'Todavía no enviaste mensajes':'Tu bandeja está vacía' ?></h3><p>Cuando haya mensajes disponibles, aparecerán aquí.</p></div>
<?php else: foreach($messages as $message): ?><article class="message-card <?= $view==='inbox' && !(int)$message['is_read']?'unread':'' ?>"><div class="message-icon"><i class="fa-solid fa-envelope<?= (int)$message['is_read']?'':'-open-text' ?>"></i></div><div class="message-content"><div class="message-heading"><h3><?= h($message['subject']) ?></h3><?php if($view==='inbox' && !(int)$message['is_read']): ?><span class="unread-pill">Nuevo</span><?php endif; ?></div><p class="message-meta"><?= $view==='sent'?'Para: '.h($message['recipient_name']):'De: '.h($message['sender_name']) ?> · <?= h(date('d/m/Y H:i',strtotime($message['created_at']))) ?></p><p class="message-body"><?= nl2br(h($message['body'])) ?></p><div class="message-actions"><?php if($view==='inbox' && !(int)$message['is_read']): ?><form method="post"><input type="hidden" name="csrf_token" value="<?= h(portal_csrf_token()) ?>"><input type="hidden" name="action" value="read"><input type="hidden" name="message_id" value="<?= (int)$message['id'] ?>"><button class="text-button" type="submit"><i class="fa-solid fa-check"></i> Marcar como leído</button></form><?php endif; ?><form method="post" onsubmit="return confirm('¿Querés eliminar este mensaje de tu vista?');"><input type="hidden" name="csrf_token" value="<?= h(portal_csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="message_id" value="<?= (int)$message['id'] ?>"><button class="text-button danger-text" type="submit"><i class="fa-regular fa-trash-can"></i> Eliminar</button></form></div></div></article><?php endforeach; endif; ?></div></section>
<?php portal_footer(); ?>

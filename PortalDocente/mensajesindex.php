
<?php

require_once __DIR__ . '/portal_common.php';

/* =========================================================
   1. CONFIGURACIÓN INICIAL
========================================================= */

if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log('Portal mensajes: conexión PDO no disponible.');
    http_response_code(500);
    exit('No se pudo establecer la conexión con la base de datos.');
}

[$titulo, $icono, $kicker] = portal_page_info();

$key = portal_user_key();

/*
 * Genera la clave de identificación con el mismo criterio
 * utilizado en el resto del portal.
 */
$makeUserKey = static function (array $user): ?string {

    if (!empty($user['cedula'])) {
        return 'cedula:' . mb_strtolower(
            trim((string) $user['cedula']),
            'UTF-8'
        );
    }

    if (!empty($user['correo'])) {
        return 'correo:' . mb_strtolower(
            trim((string) $user['correo']),
            'UTF-8'
        );
    }

    if (!empty($user['nombre_usuario'])) {
        return 'usuario:' . mb_strtolower(
            trim((string) $user['nombre_usuario']),
            'UTF-8'
        );
    }

    return null;
};

/* =========================================================
   2. PROCESAMIENTO DE FORMULARIOS
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    portal_verify_csrf();

    $key = portal_require_user_key();

    $action = (string) ($_POST['action'] ?? '');

    try {

        /* -------------------------------------------------
           2.1. ENVIAR MENSAJE
        ------------------------------------------------- */

        if ($action === 'send') {

            $recipientKey = trim(
                (string) ($_POST['recipient_key'] ?? '')
            );

            $subject = trim(
                (string) ($_POST['subject'] ?? '')
            );

            $body = trim(
                (string) ($_POST['body'] ?? '')
            );

            if (
                $recipientKey === ''
                || $subject === ''
                || $body === ''
            ) {
                throw new InvalidArgumentException(
                    'Completá destinatario, asunto y mensaje.'
                );
            }

            if (
                mb_strlen($subject) > 160
                || mb_strlen($body) > 5000
            ) {
                throw new InvalidArgumentException(
                    'El asunto o el mensaje supera la longitud permitida.'
                );
            }

            if ($recipientKey === $key) {
                throw new InvalidArgumentException(
                    'No podés enviarte un mensaje a vos mismo.'
                );
            }

            /*
             * Buscar el destinatario.
             * La clave recibida debe corresponder a un usuario real.
             */
            $recipientName = null;

            $stmt = $pdo->query(
                'SELECT cedula, nombre, apellido, nombre_usuario, correo
                 FROM usuarios'
            );

            while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $userKey = $makeUserKey($user);

                if ($userKey !== $recipientKey) {
                    continue;
                }

                $recipientName = trim(
                    (string) ($user['nombre'] ?? '') . ' ' .
                    (string) ($user['apellido'] ?? '')
                );

                if ($recipientName === '') {
                    $recipientName =
                        $user['nombre_usuario']
                        ?? $user['correo']
                        ?? 'Usuario';
                }

                break;
            }

            if ($recipientName === null) {
                throw new InvalidArgumentException(
                    'El destinatario no está disponible. Actualizá la página y elegí un usuario registrado.'
                );
            }

            $stmt = $pdo->prepare(
                'INSERT INTO portal_mensajes (
                    sender_key,
                    sender_name,
                    recipient_key,
                    recipient_name,
                    subject,
                    body,
                    is_read,
                    sender_deleted,
                    recipient_deleted,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, 0, 0, 0, NOW())'
            );

            $stmt->execute([
                $key,
                portal_user_name(),
                $recipientKey,
                $recipientName,
                $subject,
                $body
            ]);

            portal_flash(
                'success',
                'Mensaje enviado dentro del Portal Docente.'
            );

            header('Location: mensajesindex.php?view=sent');
            exit;
        }

        /* -------------------------------------------------
           2.2. MARCAR MENSAJE COMO LEÍDO
        ------------------------------------------------- */

        elseif ($action === 'read') {

            $id = filter_input(
                INPUT_POST,
                'message_id',
                FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                throw new InvalidArgumentException(
                    'El mensaje seleccionado no es válido.'
                );
            }

            $stmt = $pdo->prepare(
                'UPDATE portal_mensajes
                 SET is_read = 1
                 WHERE id = ?
                   AND recipient_key = ?
                   AND recipient_deleted = 0'
            );

            $stmt->execute([$id, $key]);

            portal_flash(
                'success',
                'El mensaje se marcó como leído.'
            );

            header('Location: mensajesindex.php?view=inbox');
            exit;
        }

        /* -------------------------------------------------
           2.3. ELIMINAR MENSAJE DE LA VISTA DEL USUARIO
        ------------------------------------------------- */

        elseif ($action === 'delete') {

            $id = filter_input(
                INPUT_POST,
                'message_id',
                FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                throw new InvalidArgumentException(
                    'El mensaje seleccionado no es válido.'
                );
            }

            /*
             * Confirmar que el mensaje pertenece al usuario
             * y determinar qué copia debe eliminarse.
             */
            $stmt = $pdo->prepare(
                'SELECT sender_key, recipient_key
                 FROM portal_mensajes
                 WHERE id = ?
                   AND (sender_key = ? OR recipient_key = ?)
                 LIMIT 1'
            );

            $stmt->execute([$id, $key, $key]);

            $message = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$message) {
                throw new InvalidArgumentException(
                    'No se encontró ese mensaje o no tenés permiso para eliminarlo.'
                );
            }

            if ($message['sender_key'] === $key) {

                $stmt = $pdo->prepare(
                    'UPDATE portal_mensajes
                     SET sender_deleted = 1
                     WHERE id = ?
                       AND sender_key = ?'
                );

                $stmt->execute([$id, $key]);

            } elseif ($message['recipient_key'] === $key) {

                $stmt = $pdo->prepare(
                    'UPDATE portal_mensajes
                     SET recipient_deleted = 1
                     WHERE id = ?
                       AND recipient_key = ?'
                );

                $stmt->execute([$id, $key]);
            }

            /*
             * Eliminar definitivamente solo cuando ambas partes
             * hayan eliminado su copia.
             */
            $stmt = $pdo->prepare(
                'DELETE FROM portal_mensajes
                 WHERE id = ?
                   AND sender_deleted = 1
                   AND recipient_deleted = 1'
            );

            $stmt->execute([$id]);

            portal_flash(
                'success',
                'El mensaje se eliminó de tu vista.'
            );

            $returnView = ($_POST['view'] ?? 'inbox') === 'sent'
                ? 'sent'
                : 'inbox';

            header(
                'Location: mensajesindex.php?view=' .
                $returnView
            );

            exit;
        }

        else {
            throw new InvalidArgumentException(
                'La operación solicitada no es válida.'
            );
        }

    } catch (InvalidArgumentException $e) {

        portal_flash('error', $e->getMessage());

        $returnView = ($_POST['view'] ?? '') === 'sent'
            ? 'sent'
            : 'inbox';

        header(
            'Location: mensajesindex.php?view=' .
            $returnView
        );

        exit;

    } catch (Throwable $e) {

        error_log('Portal mensajes: ' . $e->getMessage());

        portal_flash(
            'error',
            'No se pudo procesar la operación. Verificá la tabla usuarios y la estructura de portal_mensajes.'
        );

        header('Location: mensajesindex.php');
        exit;
    }
}

/* =========================================================
   3. CONFIGURAR LA BANDEJA
========================================================= */

$view = ($_GET['view'] ?? 'inbox') === 'sent'
    ? 'sent'
    : 'inbox';

$recipients = [];
$messages = [];
$unread = 0;

/* =========================================================
   4. CARGAR DESTINATARIOS Y MENSAJES
========================================================= */

try {

    if ($key !== null && $key !== '') {

        /* 4.1. DESTINATARIOS */

        $stmt = $pdo->query(
            'SELECT cedula, nombre, apellido, nombre_usuario, correo
             FROM usuarios
             ORDER BY nombre, apellido'
        );

        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $userKey = $makeUserKey($user);

            if (!$userKey || $userKey === $key) {
                continue;
            }

            $label = trim(
                (string) ($user['nombre'] ?? '') . ' ' .
                (string) ($user['apellido'] ?? '')
            );

            if ($label === '') {
                $label =
                    $user['nombre_usuario']
                    ?? $user['correo']
                    ?? 'Usuario';
            }

            if (!empty($user['correo'])) {
                $label .= ' · ' . $user['correo'];
            }

            $recipients[$userKey] = $label;
        }

        /* 4.2. MENSAJES SIN LEER */

        $stmt = $pdo->prepare(
            'SELECT COUNT(*)
             FROM portal_mensajes
             WHERE recipient_key = ?
               AND is_read = 0
               AND recipient_deleted = 0'
        );

        $stmt->execute([$key]);

        $unread = (int) $stmt->fetchColumn();

        /* 4.3. MENSAJES RECIBIDOS O ENVIADOS */

        if ($view === 'sent') {

            $stmt = $pdo->prepare(
                'SELECT *
                 FROM portal_mensajes
                 WHERE sender_key = ?
                   AND sender_deleted = 0
                 ORDER BY created_at DESC, id DESC'
            );

        } else {

            $stmt = $pdo->prepare(
                'SELECT *
                 FROM portal_mensajes
                 WHERE recipient_key = ?
                   AND recipient_deleted = 0
                 ORDER BY created_at DESC, id DESC'
            );
        }

        $stmt->execute([$key]);

        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (Throwable $e) {

    error_log('Portal mensajes, carga: ' . $e->getMessage());

    portal_flash(
        'error',
        'No se pudieron cargar los mensajes. Verificá la tabla usuarios y la estructura de portal_mensajes.'
    );
}

/* =========================================================
   5. ENCABEZADO
========================================================= */

portal_header($titulo, $icono, $kicker);

?>

<!-- =====================================================
     6. INFORMACIÓN GENERAL
====================================================== -->

<section class="dashboard-section">

    <div class="information-panel">

        <div class="information-icon purple-bg">
            <i class="fa-solid fa-comments"></i>
        </div>

        <div>
            <h3>Comunicación educativa</h3>

            <p>
                Los mensajes se guardan en el portal para que
                el destinatario pueda consultarlos al iniciar
                sesión. Esta función no envía correos electrónicos.
            </p>
        </div>

    </div>

</section>

<!-- =====================================================
     7. FORMULARIO PARA REDACTAR
====================================================== -->

<section class="dashboard-section">

    <div class="section-heading">
        <div>
            <span class="section-kicker">REDACTAR</span>
            <h2>Nuevo mensaje</h2>
        </div>
    </div>

    <form
        class="form-panel"
        method="post"
        action="mensajesindex.php"
    >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= h(portal_csrf_token()) ?>"
        >

        <input type="hidden" name="action" value="send">

        <label>
            Destinatario

            <select name="recipient_key" required>
                <option value="">
                    Seleccioná un usuario registrado
                </option>

                <?php foreach ($recipients as $recipientKey => $label): ?>
                    <option value="<?= h($recipientKey) ?>">
                        <?= h($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Asunto

            <input
                type="text"
                name="subject"
                required
                maxlength="160"
                placeholder="Asunto del mensaje"
            >
        </label>

        <label>
            Mensaje

            <textarea
                name="body"
                required
                rows="5"
                maxlength="5000"
                placeholder="Escribí aquí tu mensaje..."
            ></textarea>
        </label>

        <button class="primary-button" type="submit">
            <i class="fa-solid fa-paper-plane"></i>
            Enviar mensaje
        </button>

        <p class="form-note">
            Si no aparecen destinatarios, verificá que existan
            usuarios registrados en la tabla usuarios.
        </p>

    </form>

</section>

<!-- =====================================================
     8. BANDEJA DE MENSAJES
====================================================== -->

<section class="dashboard-section">

    <div class="section-heading">
        <div>
            <span class="section-kicker">BANDEJA DE MENSAJES</span>
            <h2>
                <?= $view === 'sent' ? 'Enviados' : 'Recibidos' ?>
            </h2>
        </div>

        <span class="count-pill">
            <?php if ($view === 'inbox'): ?>
                <?= $unread ?> sin leer
            <?php else: ?>
                <?= count($messages) ?> enviados
            <?php endif; ?>
        </span>
    </div>

    <div class="mail-tabs">

        <a
            class="<?= $view === 'inbox' ? 'selected' : '' ?>"
            href="mensajesindex.php?view=inbox"
        >
            <i class="fa-solid fa-inbox"></i>
            Recibidos
        </a>

        <a
            class="<?= $view === 'sent' ? 'selected' : '' ?>"
            href="mensajesindex.php?view=sent"
        >
            <i class="fa-solid fa-paper-plane"></i>
            Enviados
        </a>

    </div>

    <div class="message-list">

        <?php if ($key === null || $key === ''): ?>

            <div class="empty-state">
                <i class="fa-solid fa-user-lock"></i>

                <h3>Iniciá sesión para ver tus mensajes</h3>

                <p>Los mensajes se vinculan con tu cuenta.</p>
            </div>

        <?php elseif (!$messages): ?>

            <div class="empty-state">
                <i class="fa-regular fa-envelope-open"></i>

                <h3>
                    <?= $view === 'sent'
                        ? 'Todavía no enviaste mensajes'
                        : 'Tu bandeja está vacía'
                    ?>
                </h3>

                <p>
                    Cuando haya mensajes disponibles,
                    aparecerán aquí.
                </p>
            </div>

        <?php else: ?>

            <?php foreach ($messages as $message): ?>

                <?php
                $isUnread = (
                    $view === 'inbox'
                    && (int) ($message['is_read'] ?? 0) === 0
                );

                $dateTimestamp = strtotime(
                    (string) ($message['created_at'] ?? '')
                );
                ?>

                <article class="message-card <?= $isUnread ? 'unread' : '' ?>">

                    <div class="message-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>

                    <div class="message-content">

                        <div class="message-heading">
                            <h3><?= h($message['subject'] ?? '') ?></h3>

                            <?php if ($isUnread): ?>
                                <span class="unread-pill">Nuevo</span>
                            <?php endif; ?>
                        </div>

                        <p class="message-meta">

                            <?php if ($view === 'sent'): ?>
                                Para: <?= h($message['recipient_name'] ?? 'Usuario') ?>
                            <?php else: ?>
                                De: <?= h($message['sender_name'] ?? 'Usuario') ?>
                            <?php endif; ?>

                            <?php if ($dateTimestamp !== false): ?>
                                · <?= h(date('d/m/Y H:i', $dateTimestamp)) ?>
                            <?php endif; ?>

                        </p>

                        <p class="message-body">
                            <?= nl2br(h($message['body'] ?? '')) ?>
                        </p>

                        <div class="message-actions">

                            <?php if ($isUnread): ?>

                                <form
                                    method="post"
                                    action="mensajesindex.php?view=inbox"
                                >
                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= h(portal_csrf_token()) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="read"
                                    >

                                    <input
                                        type="hidden"
                                        name="message_id"
                                        value="<?= (int) $message['id'] ?>"
                                    >

                                    <button class="text-button" type="submit">
                                        <i class="fa-solid fa-check"></i>
                                        Marcar como leído
                                    </button>
                                </form>

                            <?php endif; ?>

                            <form
                                method="post"
                                action="mensajesindex.php"
                                onsubmit="return confirm('¿Querés eliminar este mensaje de tu vista?');"
                            >
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= h(portal_csrf_token()) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
                                >

                                <input
                                    type="hidden"
                                    name="message_id"
                                    value="<?= (int) $message['id'] ?>"
                                >

                                <input
                                    type="hidden"
                                    name="view"
                                    value="<?= h($view) ?>"
                                >

                                <button
                                    class="text-button danger-text"
                                    type="submit"
                                >
                                    <i class="fa-regular fa-trash-can"></i>
                                    Eliminar
                                </button>
                            </form>

                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</section>

<!-- =====================================================
     9. PIE DEL PORTAL
====================================================== -->

<?php
portal_footer();
?>

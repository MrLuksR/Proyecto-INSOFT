
<?php

require_once __DIR__ . '/portal_common.php';

/* 1. CONFIGURACIÓN INICIAL */

[$titulo, $icono, $kicker] = portal_page_info();

$key = portal_user_key();


/* 2. PROCESAMIENTO DE FORMULARIOS */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    portal_verify_csrf();

    $key = portal_require_user_key();

    $action = $_POST['action'] ?? '';

    try {

        /* 2.1. ENVIAR MENSAJE */

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


            // Validar campos obligatorios.

            if (
                $recipientKey === '' ||
                $subject === '' ||
                $body === ''
            ) {
                throw new InvalidArgumentException(
                    'Completá destinatario, asunto y mensaje.'
                );
            }


            // Validar longitudes máximas.

            if (
                mb_strlen($subject) > 160 ||
                mb_strlen($body) > 5000
            ) {
                throw new InvalidArgumentException(
                    'El asunto o el mensaje supera la longitud permitida.'
                );
            }


            // Evitar enviarse mensajes a uno mismo.

            if ($recipientKey === $key) {

                throw new InvalidArgumentException(
                    'No podés enviarte un mensaje a vos mismo.'
                );
            }


            /* Buscar al destinatario en la base de datos. */

            $recipientName = null;

            $users = $pdo->query(
                'SELECT cedula, nombre, apellido, nombre_usuario, correo
                 FROM usuarios'
            );


            foreach ($users as $user) {

                $userKey = null;

                if (!empty($user['cedula'])) {

                    $userKey = 'cedula:' . mb_strtolower(
                        trim((string) $user['cedula']),
                        'UTF-8'
                    );

                } elseif (!empty($user['correo'])) {

                    $userKey = 'correo:' . mb_strtolower(
                        trim((string) $user['correo']),
                        'UTF-8'
                    );

                } elseif (!empty($user['nombre_usuario'])) {

                    $userKey = 'usuario:' . mb_strtolower(
                        trim((string) $user['nombre_usuario']),
                        'UTF-8'
                    );
                }


                if ($userKey === $recipientKey) {

                    $recipientName = trim(
                        ($user['nombre'] ?? '') . ' ' .
                        ($user['apellido'] ?? '')
                    );

                    if ($recipientName === '') {

                        $recipientName =
                            $user['nombre_usuario'] ??
                            $user['correo'] ??
                            'Usuario';
                    }

                    break;
                }
            }


            // Comprobar que el destinatario exista.

            if ($recipientName === null) {

                throw new InvalidArgumentException(
                    'El destinatario ya no está disponible. ' .
                    'Actualizá la página y elegí un usuario registrado.'
                );
            }


            /* Guardar el mensaje en la base de datos. */

            $query = $pdo->prepare(
                'INSERT INTO portal_mensajes (
                    sender_key,
                    sender_name,
                    recipient_key,
                    recipient_name,
                    subject,
                    body,
                    is_read,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, 0, NOW())'
            );

            $query->execute([
                $key,
                portal_user_name(),
                $recipientKey,
                $recipientName,
                $subject,
                $body
            ]);


            // Informar el resultado y redirigir.

            portal_flash(
                'success',
                'Mensaje enviado dentro del Portal Docente.'
            );

            header(
                'Location: mensajesindex.php?view=sent'
            );

            exit;
        }


        /* 2.2. MARCAR MENSAJE COMO LEÍDO */

        elseif ($action === 'read') {

            $id = filter_input(
                INPUT_POST,
                'message_id',
                FILTER_VALIDATE_INT
            );


            if ($id) {

                $query = $pdo->prepare(
                    'UPDATE portal_mensajes
                     SET is_read = 1
                     WHERE id = ?
                     AND recipient_key = ?'
                );

                $query->execute([
                    $id,
                    $key
                ]);
            }


            header(
                'Location: mensajesindex.php?view=inbox'
            );

            exit;
        }


        /* 2.3. ELIMINAR MENSAJE */

        elseif ($action === 'delete') {

            $id = filter_input(
                INPUT_POST,
                'message_id',
                FILTER_VALIDATE_INT
            );


            if (!$id) {

                throw new InvalidArgumentException(
                    'Mensaje no válido.'
                );
            }


            // Verificar que el mensaje pertenezca al usuario.

            $check = $pdo->prepare(
                'SELECT sender_key, recipient_key
                 FROM portal_mensajes
                 WHERE id = ?
                 AND (sender_key = ? OR recipient_key = ?)'
            );

            $check->execute([
                $id,
                $key,
                $key
            ]);

            $owned = $check->fetch(PDO::FETCH_ASSOC);


            if (!$owned) {

                throw new InvalidArgumentException(
                    'No se encontró ese mensaje.'
                );
            }


            // El remitente elimina su propia copia.

            if ($owned['sender_key'] === $key) {

                $query = $pdo->prepare(
                    'UPDATE portal_mensajes
                     SET sender_deleted = 1
                     WHERE id = ?
                     AND sender_key = ?'
                );

                $query->execute([
                    $id,
                    $key
                ]);

            } else {

                // El destinatario elimina su propia copia.

                $query = $pdo->prepare(
                    'UPDATE portal_mensajes
                     SET recipient_deleted = 1
                     WHERE id = ?
                     AND recipient_key = ?'
                );

                $query->execute([
                    $id,
                    $key
                ]);
            }


            // Eliminar definitivamente si ambas partes lo borraron.

            $cleanup = $pdo->prepare(
                'DELETE FROM portal_mensajes
                 WHERE id = ?
                 AND sender_deleted = 1
                 AND recipient_deleted = 1'
            );

            $cleanup->execute([$id]);


            portal_flash(
                'success',
                'El mensaje se eliminó de tu vista.'
            );


            $currentView = $_GET['view'] ?? 'inbox';

            $currentView = $currentView === 'sent'
                ? 'sent'
                : 'inbox';


            header(
                'Location: mensajesindex.php?view=' .
                urlencode($currentView)
            );

            exit;
        }

    } catch (InvalidArgumentException $e) {

        portal_flash(
            'error',
            $e->getMessage()
        );

        header('Location: mensajesindex.php');

        exit;

    } catch (Throwable $e) {

        portal_db_error(
            $e,
            'No se pudo procesar el mensaje. ' .
            'Verificá la tabla usuarios y ejecutá instalar_portal.sql.'
        );

        header('Location: mensajesindex.php');

        exit;
    }
}


/* 3. CONFIGURAR LA BANDEJA */

// Determinar la vista seleccionada.

$view = ($_GET['view'] ?? 'inbox') === 'sent'
    ? 'sent'
    : 'inbox';


// Inicializar variables.

$recipients = [];

$messages = [];

$unread = 0;


/* 4. CARGAR DESTINATARIOS Y MENSAJES */

try {

    if ($key !== null) {

        /* 4.1. OBTENER DESTINATARIOS */

        $stmt = $pdo->query(
            'SELECT cedula, nombre, apellido, nombre_usuario, correo
             FROM usuarios
             ORDER BY nombre, apellido'
        );


        foreach ($stmt as $user) {

            $userKey = null;


            // Generar identificador del usuario.

            if (!empty($user['cedula'])) {

                $userKey = 'cedula:' . mb_strtolower(
                    trim((string) $user['cedula']),
                    'UTF-8'
                );

            } elseif (!empty($user['correo'])) {

                $userKey = 'correo:' . mb_strtolower(
                    trim((string) $user['correo']),
                    'UTF-8'
                );

            } elseif (!empty($user['nombre_usuario'])) {

                $userKey = 'usuario:' . mb_strtolower(
                    trim((string) $user['nombre_usuario']),
                    'UTF-8'
                );
            }


            // Excluir usuarios sin identificador y al usuario actual.

            if (!$userKey || $userKey === $key) {
                continue;
            }


            // Construir el nombre que aparecerá en el selector.

            $label = trim(
                ($user['nombre'] ?? '') . ' ' .
                ($user['apellido'] ?? '')
            );


            if ($label === '') {

                $label =
                    $user['nombre_usuario'] ??
                    $user['correo'] ??
                    'Usuario';
            }


            if (!empty($user['correo'])) {

                $label .= ' · ' . $user['correo'];
            }


            $recipients[$userKey] = $label;
        }


        /* 4.2. CONTAR MENSAJES SIN LEER */

        $query = $pdo->prepare(
            'SELECT COUNT(*)
             FROM portal_mensajes
             WHERE recipient_key = ?
             AND is_read = 0
             AND recipient_deleted = 0'
        );

        $query->execute([$key]);

        $unread = (int) $query->fetchColumn();


        /* 4.3. OBTENER MENSAJES DE LA VISTA SELECCIONADA */

        if ($view === 'sent') {

            $query = $pdo->prepare(
                'SELECT *
                 FROM portal_mensajes
                 WHERE sender_key = ?
                 AND sender_deleted = 0
                 ORDER BY created_at DESC, id DESC'
            );

        } else {

            $query = $pdo->prepare(
                'SELECT *
                 FROM portal_mensajes
                 WHERE recipient_key = ?
                 AND recipient_deleted = 0
                 ORDER BY created_at DESC, id DESC'
            );
        }


        $query->execute([$key]);

        $messages = $query->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (Throwable $e) {

    error_log(
        'Portal mensajes: ' . $e->getMessage()
    );

    portal_flash(
        'error',
        'No se pudieron cargar los mensajes. ' .
        'Ejecutá instalar_portal.sql y verificá la tabla usuarios.'
    );
}


/* 5. ENCABEZADO DEL PORTAL */

portal_header(
    $titulo,
    $icono,
    $kicker
);

?>

<!-- 6. INFORMACIÓN GENERAL -->

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


<!-- 7. FORMULARIO PARA REDACTAR MENSAJES -->

<section class="dashboard-section">

    <div class="section-heading">

        <div>

            <span class="section-kicker">
                REDACTAR
            </span>

            <h2>Nuevo mensaje</h2>

        </div>

    </div>


    <form
        class="form-panel"
        method="post"
        action="mensajesindex.php"
    >

        <!-- Token de seguridad -->

        <input
            type="hidden"
            name="csrf_token"
            value="<?= h(portal_csrf_token()) ?>"
        >

        <input
            type="hidden"
            name="action"
            value="send"
        >


        <!-- Destinatario -->

        <label>

            Destinatario

            <select
                name="recipient_key"
                required
            >

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


        <!-- Asunto -->

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


        <!-- Contenido -->

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


        <!-- Botón de envío -->

        <button
            class="primary-button"
            type="submit"
        >

            <i class="fa-solid fa-paper-plane"></i>

            Enviar mensaje

        </button>


        <p class="form-note">

            Si no aparecen destinatarios, verificá que existan
            usuarios en la tabla usuarios.

        </p>

    </form>

</section>


<!-- 8. BANDEJA DE MENSAJES -->

<section class="dashboard-section">

    <div class="section-heading">

        <div>

            <span class="section-kicker">
                BANDEJA DE MENSAJES
            </span>

            <h2>
                <?= $view === 'sent' ? 'Enviados' : 'Recibidos' ?>
            </h2>

        </div>


        <!-- Contador de mensajes -->

        <span class="count-pill">

            <?php if ($view === 'inbox'): ?>

                <?= $unread ?> sin leer

            <?php else: ?>

                <?= count($messages) ?> enviados

            <?php endif; ?>

        </span>

    </div>


    <!-- Pestañas de navegación -->

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


    <!-- Lista de mensajes -->

    <div class="message-list">


        <!-- Caso 1: usuario sin sesión -->

        <?php if (!$key): ?>

            <div class="empty-state">

                <i class="fa-solid fa-user-lock"></i>

                <h3>
                    Iniciá sesión para ver tus mensajes
                </h3>

                <p>
                    Los mensajes se vinculan con tu cuenta.
                </p>

            </div>


        <!-- Caso 2: bandeja vacía -->

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


        <!-- Caso 3: mostrar mensajes -->

        <?php else: ?>

            <?php foreach ($messages as $message): ?>

                <?php

                $isUnread =
                    $view === 'inbox' &&
                    !(int) $message['is_read'];

                ?>

                <article
                    class="message-card <?= $isUnread ? 'unread' : '' ?>"
                >

                    <!-- Icono del mensaje -->

                    <div class="message-icon">

                        <i class="fa-solid fa-envelope<?= 
                            (int) $message['is_read']
                                ? ''
                                : '-open-text'
                        ?>"></i>

                    </div>


                    <!-- Contenido del mensaje -->

                    <div class="message-content">


                        <!-- Asunto y estado -->

                        <div class="message-heading">

                            <h3>
                                <?= h($message['subject']) ?>
                            </h3>


                            <?php if ($isUnread): ?>

                                <span class="unread-pill">
                                    Nuevo
                                </span>

                            <?php endif; ?>

                        </div>


                        <!-- Remitente o destinatario y fecha -->

                        <p class="message-meta">

                            <?php if ($view === 'sent'): ?>

                                Para:
                                <?= h($message['recipient_name']) ?>

                            <?php else: ?>

                                De:
                                <?= h($message['sender_name']) ?>

                            <?php endif; ?>

                            ·

                            <?= h(
                                date(
                                    'd/m/Y H:i',
                                    strtotime($message['created_at'])
                                )
                            ) ?>

                        </p>


                        <!-- Texto del mensaje -->

                        <p class="message-body">

                            <?= nl2br(h($message['body'])) ?>

                        </p>


                        <!-- Acciones disponibles -->

                        <div class="message-actions">


                            <!-- Marcar como leído -->

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


                                    <button
                                        class="text-button"
                                        type="submit"
                                    >

                                        <i class="fa-solid fa-check"></i>

                                        Marcar como leído

                                    </button>

                                </form>

                            <?php endif; ?>


                            <!-- Eliminar mensaje -->

                            <form
                                method="post"
                                action="mensajesindex.php?view=<?= h($view) ?>"
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


<!-- 9. PIE DEL PORTAL -->

<?php

portal_footer();

?>

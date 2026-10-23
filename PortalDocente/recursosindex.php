
<?php

require_once __DIR__ . '/portal_common.php';

/* =========================================================
   1. CONFIGURACIÓN INICIAL
========================================================= */

if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log('Portal recursos: conexión PDO no disponible.');
    http_response_code(500);
    exit('No se pudo establecer la conexión con la base de datos.');
}

[$titulo, $icono, $kicker] = portal_page_info();

$key = portal_user_key();

$uploadDir = __DIR__ . '/uploads/recursos';

if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
        error_log('No se pudo crear la carpeta uploads/recursos.');
    }
}

$categories = [
    'Guías y documentos',
    'Herramientas digitales',
    'Propuestas didácticas',
    'Presentaciones',
    'Otros'
];

$allowedExtensions = [
    'pdf', 'doc', 'docx', 'ppt', 'pptx',
    'xls', 'xlsx', 'odt', 'ods', 'odp',
    'txt', 'csv', 'png', 'jpg', 'jpeg',
    'webp', 'zip'
];

/* =========================================================
   2. PROCESAR FORMULARIOS
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    portal_verify_csrf();

    $key = portal_require_user_key();

    $action = (string) ($_POST['action'] ?? '');

    try {

        /* -------------------------------------------------
           2.1. AGREGAR UN RECURSO
        ------------------------------------------------- */

        if ($action === 'save') {

            $title = trim((string) ($_POST['title'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $category = trim(
                (string) ($_POST['category'] ?? 'Guías y documentos')
            );
            $url = trim((string) ($_POST['resource_url'] ?? ''));

            if (
                $title === ''
                || mb_strlen($title) > 150
                || mb_strlen($description) > 2000
                || !in_array($category, $categories, true)
            ) {
                throw new InvalidArgumentException(
                    'Revisá el título, la categoría y la descripción del recurso.'
                );
            }

            /* Validar URL externa. */

            if ($url !== '') {

                if (
                    mb_strlen($url) > 1000
                    || !filter_var($url, FILTER_VALIDATE_URL)
                ) {
                    throw new InvalidArgumentException(
                        'Ingresá un enlace completo válido, por ejemplo https://...'
                    );
                }

                $scheme = strtolower(
                    (string) parse_url($url, PHP_URL_SCHEME)
                );

                if (!in_array($scheme, ['http', 'https'], true)) {
                    throw new InvalidArgumentException(
                        'Solo se permiten enlaces http o https.'
                    );
                }
            }

            $filePath = null;
            $originalName = null;
            $mimeType = null;
            $destination = null;

            /* -------------------------------------------------
               2.1.1. VALIDAR Y GUARDAR ARCHIVO
            ------------------------------------------------- */

            if (
                isset($_FILES['resource_file'])
                && $_FILES['resource_file']['error'] !== UPLOAD_ERR_NO_FILE
            ) {

                $file = $_FILES['resource_file'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new InvalidArgumentException(
                        'Hubo un error al subir el archivo. Probá nuevamente.'
                    );
                }

                if (
                    !isset($file['tmp_name'])
                    || !is_uploaded_file($file['tmp_name'])
                ) {
                    throw new InvalidArgumentException(
                        'El archivo recibido no es válido.'
                    );
                }

                if ((int) $file['size'] > 10 * 1024 * 1024) {
                    throw new InvalidArgumentException(
                        'El archivo supera el máximo permitido de 10 MB.'
                    );
                }

                $originalName = basename((string) $file['name']);

                // Evitar nombres de archivo excesivamente largos.
                if (mb_strlen($originalName) > 255) {
                    throw new InvalidArgumentException(
                        'El nombre del archivo es demasiado largo.'
                    );
                }

                $ext = strtolower(
                    pathinfo($originalName, PATHINFO_EXTENSION)
                );

                if (!in_array($ext, $allowedExtensions, true)) {
                    throw new InvalidArgumentException(
                        'Tipo de archivo no permitido. Usá PDF, Office, OpenDocument, TXT, CSV, imágenes o ZIP.'
                    );
                }

                if (!is_dir($uploadDir)) {
                    if (
                        !mkdir($uploadDir, 0755, true)
                        && !is_dir($uploadDir)
                    ) {
                        throw new RuntimeException(
                            'No se pudo crear la carpeta de archivos.'
                        );
                    }
                }

                if (!is_writable($uploadDir)) {
                    throw new RuntimeException(
                        'La carpeta uploads/recursos no tiene permisos de escritura.'
                    );
                }

                $finfo = new finfo(FILEINFO_MIME_TYPE);

                $mimeType = $finfo->file($file['tmp_name'])
                    ?: 'application/octet-stream';

                /*
                 * Validaciones básicas del contenido.
                 * No se confía únicamente en la extensión del archivo.
                 */

                if (
                    in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)
                    && !@getimagesize($file['tmp_name'])
                ) {
                    throw new InvalidArgumentException(
                        'El contenido del archivo no parece ser una imagen válida.'
                    );
                }

                if (
                    $ext === 'pdf'
                    && $mimeType !== 'application/pdf'
                ) {
                    throw new InvalidArgumentException(
                        'El archivo no parece ser un PDF válido.'
                    );
                }

                if (
                    in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)
                    && !str_starts_with($mimeType, 'image/')
                ) {
                    throw new InvalidArgumentException(
                        'El tipo de imagen no es válido.'
                    );
                }

                $stored = bin2hex(random_bytes(18)) . '.' . $ext;

                $destination = $uploadDir . DIRECTORY_SEPARATOR . $stored;

                if (!move_uploaded_file($file['tmp_name'], $destination)) {
                    throw new RuntimeException(
                        'No se pudo guardar el archivo en uploads/recursos.'
                    );
                }

                $filePath = 'uploads/recursos/' . $stored;
            }

            /* Exigir un enlace o un archivo. */

            if ($url === '' && $filePath === null) {
                throw new InvalidArgumentException(
                    'Agregá un enlace o adjuntá un archivo.'
                );
            }

            /* -------------------------------------------------
               2.1.2. GUARDAR EN LA BASE DE DATOS
            ------------------------------------------------- */

            try {

                $sql = '
                    INSERT INTO portal_recursos (
                        owner_key,
                        owner_name,
                        title,
                        description,
                        category,
                        resource_url,
                        file_path,
                        original_filename,
                        mime_type,
                        created_at
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ';

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    $key,
                    portal_user_name(),
                    $title,
                    $description !== '' ? $description : null,
                    $category,
                    $url !== '' ? $url : null,
                    $filePath,
                    $originalName,
                    $mimeType
                ]);

            } catch (Throwable $e) {

                // Si falla el INSERT, quitar el archivo recién guardado.
                if (
                    $destination !== null
                    && is_file($destination)
                ) {
                    @unlink($destination);
                }

                throw $e;
            }

            portal_flash(
                'success',
                'El recurso se agregó a la biblioteca compartida.'
            );

            header('Location: recursosindex.php');
            exit;
        }

        /* -------------------------------------------------
           2.2. ELIMINAR UN RECURSO PROPIO
        ------------------------------------------------- */

        elseif ($action === 'delete') {

            $id = filter_input(
                INPUT_POST,
                'resource_id',
                FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                throw new InvalidArgumentException(
                    'El recurso seleccionado no es válido.'
                );
            }

            $stmt = $pdo->prepare(
                'SELECT id, owner_key, file_path
                 FROM portal_recursos
                 WHERE id = ?
                   AND owner_key = ?
                 LIMIT 1'
            );

            $stmt->execute([$id, $key]);

            $resource = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resource) {
                throw new InvalidArgumentException(
                    'Solo podés eliminar los recursos que agregaste.'
                );
            }

            /*
             * Eliminar primero el registro.
             * Si la consulta falla, el archivo no se toca.
             */
            $stmt = $pdo->prepare(
                'DELETE FROM portal_recursos
                 WHERE id = ?
                   AND owner_key = ?'
            );

            $stmt->execute([$id, $key]);

            if ($stmt->rowCount() > 0 && !empty($resource['file_path'])) {

                $rootPath = realpath($uploadDir);
                $relativePath = (string) $resource['file_path'];

                /*
                 * Solo permitir eliminar archivos guardados
                 * dentro de uploads/recursos.
                 */
                if (
                    $rootPath !== false
                    && strpos($relativePath, 'uploads/recursos/') === 0
                ) {

                    $fullPath = realpath(
                        __DIR__ . DIRECTORY_SEPARATOR .
                        str_replace(
                            '/',
                            DIRECTORY_SEPARATOR,
                            $relativePath
                        )
                    );

                    if (
                        $fullPath !== false
                        && strpos(
                            $fullPath,
                            $rootPath . DIRECTORY_SEPARATOR
                        ) === 0
                        && is_file($fullPath)
                    ) {
                        if (!@unlink($fullPath)) {
                            error_log(
                                'No se pudo eliminar el archivo: ' . $fullPath
                            );
                        }
                    }
                }
            }

            portal_flash(
                'success',
                'El recurso se eliminó de la biblioteca.'
            );

            header('Location: recursosindex.php');
            exit;
        }

        else {
            throw new InvalidArgumentException(
                'La operación solicitada no es válida.'
            );
        }

    } catch (InvalidArgumentException $e) {

        portal_flash('error', $e->getMessage());

        header('Location: recursosindex.php');
        exit;

    } catch (Throwable $e) {

        error_log('Portal recursos: ' . $e->getMessage());

        portal_flash(
            'error',
            'No se pudo procesar el recurso. Verificá la tabla portal_recursos y los permisos de uploads/recursos.'
        );

        header('Location: recursosindex.php');
        exit;
    }
}

/* =========================================================
   3. OBTENER LOS RECURSOS
========================================================= */

$resources = [];

$filter = trim((string) ($_GET['category'] ?? ''));

if (!in_array($filter, $categories, true)) {
    $filter = '';
}

try {

    $sql = 'SELECT * FROM portal_recursos';
    $params = [];

    if ($filter !== '') {
        $sql .= ' WHERE category = ?';
        $params[] = $filter;
    }

    $sql .= ' ORDER BY created_at DESC, id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {

    error_log('Portal recursos, listado: ' . $e->getMessage());

    portal_flash(
        'error',
        'No se pudieron cargar los recursos. Verificá la tabla portal_recursos.'
    );
}

/* =========================================================
   4. ENCABEZADO
========================================================= */

portal_header($titulo, $icono, $kicker);

?>

<!-- =====================================================
     5. INFORMACIÓN DE LA BIBLIOTECA
====================================================== -->

<section class="dashboard-section">

    <div class="information-panel">

        <div class="information-icon green-bg">
            <i class="fa-solid fa-book-open"></i>
        </div>

        <div>
            <h3>Biblioteca docente compartida</h3>

            <p>
                Agregá enlaces o archivos de apoyo para que otros
                usuarios del portal puedan consultarlos.
                Podés eliminar únicamente los recursos que agregaste.
            </p>
        </div>

    </div>

</section>

<!-- =====================================================
     6. FORMULARIO PARA AGREGAR RECURSOS
====================================================== -->

<section class="dashboard-section">

    <div class="section-heading">
        <div>
            <span class="section-kicker">BIBLIOTECA DOCENTE</span>
            <h2>Agregar recurso</h2>
        </div>
    </div>

    <form
        class="form-panel"
        method="post"
        action="recursosindex.php"
        enctype="multipart/form-data"
    >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= h(portal_csrf_token()) ?>"
        >

        <input type="hidden" name="action" value="save">

        <div class="form-grid">

            <label>
                Nombre del recurso

                <input
                    type="text"
                    name="title"
                    required
                    maxlength="150"
                    placeholder="Ej.: Guía de programación"
                >
            </label>

            <label>
                Categoría

                <select name="category" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= h($cat) ?>">
                            <?= h($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

        </div>

        <label>
            Descripción

            <textarea
                name="description"
                rows="3"
                maxlength="2000"
                placeholder="Tema, asignatura o indicaciones para utilizar el recurso"
            ></textarea>
        </label>

        <label>
            Enlace externo (opcional)

            <input
                type="url"
                name="resource_url"
                maxlength="1000"
                placeholder="https://..."
            >
        </label>

        <label>
            Archivo (opcional)

            <input
                type="file"
                name="resource_file"
                accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.odt,.ods,.odp,.txt,.csv,.png,.jpg,.jpeg,.webp,.zip"
            >

            <small class="field-help">
                Máximo 10 MB. Tipos permitidos: PDF, Office,
                OpenDocument, TXT, CSV, imágenes y ZIP.
            </small>
        </label>

        <button class="primary-button" type="submit">
            <i class="fa-solid fa-plus"></i>
            Agregar a la biblioteca
        </button>

        <p class="form-note">
            Tenés que proporcionar al menos un enlace o un archivo.
        </p>

    </form>

</section>

<!-- =====================================================
     7. LISTADO DE RECURSOS
====================================================== -->

<section class="dashboard-section">

    <div class="section-heading">
        <div>
            <span class="section-kicker">RECURSOS DISPONIBLES</span>
            <h2>Materiales compartidos</h2>
        </div>

        <span class="count-pill">
            <?= count($resources) ?> recursos
        </span>
    </div>

    <!-- FILTRO -->

    <form class="filter-row" method="get" action="recursosindex.php">

        <label for="categoryFilter">
            Filtrar por categoría
        </label>

        <select id="categoryFilter" name="category">
            <option value="">Todas las categorías</option>

            <?php foreach ($categories as $cat): ?>
                <option
                    value="<?= h($cat) ?>"
                    <?= $filter === $cat ? 'selected' : '' ?>
                >
                    <?= h($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="secondary-button" type="submit">
            Filtrar
        </button>

        <?php if ($filter !== ''): ?>
            <a class="text-button" href="recursosindex.php">
                Limpiar
            </a>
        <?php endif; ?>

    </form>

    <!-- TARJETAS -->

    <div class="resource-grid">

        <?php if (!$resources): ?>

            <div class="empty-state resource-empty">
                <i class="fa-solid fa-folder-open"></i>

                <h3>No hay recursos para mostrar</h3>

                <p>
                    Agregá el primero con el formulario de arriba.
                </p>
            </div>

        <?php else: ?>

            <?php foreach ($resources as $r): ?>

                <?php
                $resourceDate = strtotime(
                    (string) ($r['created_at'] ?? '')
                );

                $hasFile = !empty($r['file_path']);
                $hasUrl = !empty($r['resource_url']);
                $isOwner = (
                    $key !== null
                    && $key !== ''
                    && $key === ($r['owner_key'] ?? null)
                );
                ?>

                <article class="resource-card">

                    <div class="resource-card-top">

                        <span class="quick-icon green">
                            <i class="fa-solid <?= $hasFile
                                ? 'fa-file-arrow-down'
                                : 'fa-link'
                            ?>"></i>
                        </span>

                        <span class="category-pill">
                            <?= h($r['category'] ?? 'Otros') ?>
                        </span>

                    </div>

                    <h3><?= h($r['title'] ?? '') ?></h3>

                    <?php if (!empty($r['description'])): ?>
                        <p><?= nl2br(h($r['description'])) ?></p>
                    <?php endif; ?>

                    <div class="resource-meta">

                        Compartido por
                        <?= h($r['owner_name'] ?? 'Usuario') ?>

                        <?php if ($resourceDate !== false): ?>
                            · <?= h(date('d/m/Y', $resourceDate)) ?>
                        <?php endif; ?>

                    </div>

                    <div class="resource-actions">

                        <?php if ($hasUrl): ?>
                            <a
                                class="primary-button"
                                href="<?= h($r['resource_url']) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                Abrir enlace
                            </a>
                        <?php endif; ?>

                        <?php if ($hasFile): ?>
                            <a
                                class="secondary-button"
                                href="<?= h($r['file_path']) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <i class="fa-solid fa-download"></i>
                                Ver archivo
                            </a>
                        <?php endif; ?>

                        <?php if ($isOwner): ?>

                            <form
                                method="post"
                                action="recursosindex.php"
                                onsubmit="return confirm('¿Querés eliminar este recurso?');"
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
                                    name="resource_id"
                                    value="<?= (int) $r['id'] ?>"
                                >

                                <button
                                    class="icon-action danger"
                                    type="submit"
                                    aria-label="Eliminar recurso"
                                    title="Eliminar recurso"
                                >
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</section>

<!-- =====================================================
     8. PIE DEL PORTAL
====================================================== -->

<?php
portal_footer();
?>

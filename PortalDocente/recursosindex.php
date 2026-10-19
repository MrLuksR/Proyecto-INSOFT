<?php
require_once __DIR__ . '/portal_common.php';
[$titulo, $icono, $kicker] = portal_page_info();
$key = portal_user_key();
$uploadDir = __DIR__ . '/uploads/recursos';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    portal_verify_csrf(); $key = portal_require_user_key();
    $action=$_POST['action'] ?? '';
    try {
        if($action==='save') {
            $title=trim((string)($_POST['title'] ?? '')); $description=trim((string)($_POST['description'] ?? ''));
            $category=trim((string)($_POST['category'] ?? 'Guías y documentos')); $url=trim((string)($_POST['resource_url'] ?? ''));
            $categories=['Guías y documentos','Herramientas digitales','Propuestas didácticas','Presentaciones','Otros'];
            if($title==='' || mb_strlen($title)>150 || mb_strlen($description)>2000 || !in_array($category,$categories,true)) throw new InvalidArgumentException('Revisá el título, categoría y descripción del recurso.');
            if($url!=='' && !filter_var($url,FILTER_VALIDATE_URL)) throw new InvalidArgumentException('Ingresá un enlace completo válido, por ejemplo https://...');
            if($url!=='' && !in_array(strtolower((string)parse_url($url,PHP_URL_SCHEME)),['http','https'],true)) throw new InvalidArgumentException('Solo se permiten enlaces http o https.');
            $filePath=null; $originalName=null; $mimeType=null;
            if(isset($_FILES['resource_file']) && $_FILES['resource_file']['error']!==UPLOAD_ERR_NO_FILE) {
                $file=$_FILES['resource_file'];
                if($file['error']!==UPLOAD_ERR_OK) throw new InvalidArgumentException('Hubo un error al subir el archivo. Probá nuevamente.');
                if($file['size']>10*1024*1024) throw new InvalidArgumentException('El archivo supera el máximo de 10 MB.');
                $originalName=basename((string)$file['name']); $ext=strtolower(pathinfo($originalName,PATHINFO_EXTENSION));
                $allowed=['pdf','doc','docx','ppt','pptx','xls','xlsx','odt','ods','odp','txt','csv','png','jpg','jpeg','webp','zip'];
                if(!in_array($ext,$allowed,true)) throw new InvalidArgumentException('Tipo de archivo no permitido. Usá PDF, Office, OpenDocument, TXT, CSV, imágenes o ZIP.');
                $finfo=new finfo(FILEINFO_MIME_TYPE); $mimeType=$finfo->file($file['tmp_name']) ?: 'application/octet-stream';
                if(in_array($ext,['jpg','jpeg','png','webp'],true) && !str_starts_with($mimeType,'image/')) throw new InvalidArgumentException('El contenido del archivo no parece ser una imagen válida.');
                if($ext==='pdf' && $mimeType!=='application/pdf') throw new InvalidArgumentException('El archivo no parece ser un PDF válido.');
                $stored=bin2hex(random_bytes(18)).'.'.$ext; $destination=$uploadDir.'/'.$stored;
                if(!move_uploaded_file($file['tmp_name'],$destination)) throw new RuntimeException('No se pudo guardar el archivo en uploads/recursos.');
                $filePath='uploads/recursos/'.$stored;
            }
            if($url==='' && $filePath===null) throw new InvalidArgumentException('Agregá un enlace o adjuntá un archivo.');
            try {
                $q=$pdo->prepare('INSERT INTO portal_recursos (owner_key,owner_name,title,description,category,resource_url,file_path,original_filename,mime_type,created_at) VALUES (?,?,?,?,?,?,?,?,?,NOW())');
                $q->execute([$key,portal_user_name(),$title,$description ?: null,$category,$url ?: null,$filePath,$originalName,$mimeType]);
            } catch(Throwable $e) { if($filePath && is_file(__DIR__.'/'.$filePath)) @unlink(__DIR__.'/'.$filePath); throw $e; }
            portal_flash('success','El recurso se agregó a la biblioteca compartida.'); header('Location: recursosindex.php'); exit;
        } elseif($action==='delete') {
            $id=filter_input(INPUT_POST,'resource_id',FILTER_VALIDATE_INT); if(!$id) throw new InvalidArgumentException('Recurso no válido.');
            $q=$pdo->prepare('SELECT file_path FROM portal_recursos WHERE id=? AND owner_key=?');$q->execute([$id,$key]);$resource=$q->fetch();
            if(!$resource) throw new InvalidArgumentException('Solo podés eliminar los recursos que agregaste.');
            $q=$pdo->prepare('DELETE FROM portal_recursos WHERE id=? AND owner_key=?');$q->execute([$id,$key]);
            if(!empty($resource['file_path'])) { $full=realpath(__DIR__.'/'.$resource['file_path']); $root=realpath($uploadDir); if($full && $root && str_starts_with($full,$root.DIRECTORY_SEPARATOR) && is_file($full)) @unlink($full); }
            portal_flash('success','El recurso se eliminó de la biblioteca.');header('Location: recursosindex.php');exit;
        }
    } catch(InvalidArgumentException $e) { portal_flash('error',$e->getMessage());header('Location: recursosindex.php');exit; }
      catch(Throwable $e) { portal_db_error($e,'No se pudo guardar el recurso. Verificá la tabla portal_recursos y los permisos de uploads/recursos.');header('Location: recursosindex.php');exit; }
}
$resources=[]; $filter=trim((string)($_GET['category'] ?? ''));
try { $sql='SELECT * FROM portal_recursos'; $params=[]; if(in_array($filter,['Guías y documentos','Herramientas digitales','Propuestas didácticas','Presentaciones','Otros'],true)){$sql.=' WHERE category=?';$params[]=$filter;} $sql.=' ORDER BY created_at DESC,id DESC';$q=$pdo->prepare($sql);$q->execute($params);$resources=$q->fetchAll(); }
catch(Throwable $e) { error_log('Portal recursos: '.$e->getMessage()); portal_flash('error','No se pudieron cargar los recursos. Ejecutá instalar_portal.sql.'); }
portal_header($titulo,$icono,$kicker);
?>
<section class="dashboard-section"><div class="information-panel"><div class="information-icon green-bg"><i class="fa-solid fa-book-open"></i></div><div><h3>Biblioteca docente compartida</h3><p>Agregá enlaces o archivos de apoyo para que otros usuarios del portal puedan consultarlos. Podés eliminar únicamente los recursos que agregaste.</p></div></div></section>
<section class="dashboard-section"><div class="section-heading"><div><span class="section-kicker">BIBLIOTECA DOCENTE</span><h2>Agregar recurso</h2></div></div>
<form class="form-panel" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= h(portal_csrf_token()) ?>"><input type="hidden" name="action" value="save">
<div class="form-grid"><label>Nombre del recurso<input name="title" required maxlength="150" placeholder="Ej.: Guía de programación"></label><label>Categoría<select name="category" required><?php foreach(['Guías y documentos','Herramientas digitales','Propuestas didácticas','Presentaciones','Otros'] as $cat): ?><option value="<?= h($cat) ?>"><?= h($cat) ?></option><?php endforeach; ?></select></label></div>
<label>Descripción<textarea name="description" rows="3" maxlength="2000" placeholder="Tema, asignatura o indicaciones para utilizar el recurso"></textarea></label>
<label>Enlace externo (opcional)<input type="url" name="resource_url" maxlength="1000" placeholder="https://..." inputmode="url"></label>
<label>Archivo (opcional)<input type="file" name="resource_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.odt,.ods,.odp,.txt,.csv,.png,.jpg,.jpeg,.webp,.zip"><small class="field-help">Máximo 10 MB. Tipos permitidos: PDF, Office, OpenDocument, TXT, CSV, imágenes y ZIP.</small></label>
<button class="primary-button" type="submit"><i class="fa-solid fa-plus"></i> Agregar a la biblioteca</button><p class="form-note">Tenés que proporcionar al menos un enlace o un archivo.</p></form></section>
<section class="dashboard-section"><div class="section-heading"><div><span class="section-kicker">RECURSOS DISPONIBLES</span><h2>Materiales compartidos</h2></div><span class="count-pill"><?= count($resources) ?> recursos</span></div>
<form class="filter-row" method="get"><label for="categoryFilter">Filtrar por categoría</label><select id="categoryFilter" name="category"><option value="">Todas las categorías</option><?php foreach(['Guías y documentos','Herramientas digitales','Propuestas didácticas','Presentaciones','Otros'] as $cat): ?><option value="<?= h($cat) ?>" <?= $filter===$cat?'selected':'' ?>><?= h($cat) ?></option><?php endforeach; ?></select><button class="secondary-button" type="submit">Filtrar</button><?php if($filter!==''): ?><a class="text-button" href="recursosindex.php">Limpiar</a><?php endif; ?></form>
<div class="resource-grid"><?php if(!$resources): ?><div class="empty-state resource-empty"><i class="fa-solid fa-folder-open"></i><h3>No hay recursos para mostrar</h3><p>Agregá el primero con el formulario de arriba.</p></div><?php else: foreach($resources as $r): ?><article class="resource-card"><div class="resource-card-top"><span class="quick-icon green"><i class="fa-solid fa-<?= !empty($r['file_path'])?'file-arrow-down':'link' ?>"></i></span><span class="category-pill"><?= h($r['category']) ?></span></div><h3><?= h($r['title']) ?></h3><?php if(!empty($r['description'])): ?><p><?= nl2br(h($r['description'])) ?></p><?php endif; ?><div class="resource-meta">Compartido por <?= h($r['owner_name']) ?> · <?= h(date('d/m/Y',strtotime($r['created_at']))) ?></div><div class="resource-actions"><?php if(!empty($r['resource_url'])): ?><a class="primary-button" href="<?= h($r['resource_url']) ?>" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square"></i> Abrir enlace</a><?php endif; ?><?php if(!empty($r['file_path'])): ?><a class="secondary-button" href="<?= h($r['file_path']) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-download"></i> Ver archivo</a><?php endif; ?><?php if($key!==null && $key===$r['owner_key']): ?><form method="post" onsubmit="return confirm('¿Querés eliminar este recurso?');"><input type="hidden" name="csrf_token" value="<?= h(portal_csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="resource_id" value="<?= (int)$r['id'] ?>"><button class="icon-action danger" type="submit" aria-label="Eliminar recurso"><i class="fa-regular fa-trash-can"></i></button></form><?php endif; ?></div></article><?php endforeach; endif; ?></div></section>
<?php portal_footer(); ?>

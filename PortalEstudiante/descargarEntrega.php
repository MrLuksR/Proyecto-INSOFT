<?php
/** Descarga protegida de la entrega del propio estudiante. */
require_once __DIR__ . '/portal_common.php';
if (!$haySesionEstudiante || $errorConexion) { http_response_code(403); exit('Acceso denegado.'); }
$idTarea = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idTarea) { http_response_code(400); exit('Solicitud inválida.'); }
try {
    $stmt = $pdo->prepare("SELECT te.archivo_entrega FROM tareas_estudiantes te
        INNER JOIN tareas t ON t.id_tarea = te.id_tarea
        INNER JOIN clase c ON c.id_clase = t.id_clase
        INNER JOIN matricula m ON m.id_curso = c.id_curso
        WHERE te.id_tarea = :tarea AND te.nombre_usuario_estudiante = :usuario
          AND m.nombre_usuario_estudiante = :usuario AND LOWER(TRIM(m.estado)) = 'activa'
        LIMIT 1");
    $stmt->execute([':tarea'=>$idTarea, ':usuario'=>$nombreUsuario]);
    $archivo = $stmt->fetchColumn();
    if (!$archivo || !preg_match('/^[a-f0-9]{40}\.pdf$/', $archivo)) { http_response_code(404); exit('Entrega no disponible.'); }
    $base = realpath(dirname(rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/\\')) . '/insoft_entregas_privadas');
    $ruta = $base ? realpath($base . '/' . $archivo) : false;
    if (!$ruta || !str_starts_with($ruta, $base . DIRECTORY_SEPARATOR) || !is_file($ruta)) { http_response_code(404); exit('Archivo no encontrado.'); }
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="mi-entrega-' . $idTarea . '.pdf"');
    header('Content-Length: ' . filesize($ruta));
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');
    readfile($ruta);
} catch (Throwable $e) {
    error_log('[INSOFT] Descargar entrega: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo descargar la entrega.');
}

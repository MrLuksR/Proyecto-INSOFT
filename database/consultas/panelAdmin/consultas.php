<?php
/**
 * INSOFT — Consultas del panel administrativo.
 * Ubicación: database/consultas/panelAdmin/consultas.php
 * SOLO LECTURA: no se realizan INSERT, UPDATE ni DELETE.
 * Las vistas no contienen sentencias SQL.
 */

declare(strict_types=1);

function adminConsultar(?PDO $pdo, string $sql, array $parametros = []): array
{
    if (!$pdo instanceof PDO) {
        return [];
    }
    try {
        $sentencia = $pdo->prepare($sql);
        $sentencia->execute($parametros);
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('[INSOFT admin] Error de consulta: ' . $e->getMessage());
        return [];
    }
}

/** El identificador procede de una lista fija: los placeholders no aceptan tablas. */
function adminContar(?PDO $pdo, string $tabla): ?int
{
    $permitidas = ['curso', 'docentes', 'usuarios', 'estudiantes', 'pagos', 'noticias', 'galeria', 'matricula'];
    if (!$pdo instanceof PDO || !in_array($tabla, $permitidas, true)) {
        return null;
    }
    try {
        $sentencia = $pdo->prepare("SELECT COUNT(*) FROM `$tabla`");
        $sentencia->execute();
        return (int) $sentencia->fetchColumn();
    } catch (PDOException $e) {
        error_log('[INSOFT admin] Error de conteo: ' . $e->getMessage());
        return null;
    }
}

function adminResumen(?PDO $pdo): array
{
    return [
        'cursos' => adminContar($pdo, 'curso'),
        'docentes' => adminContar($pdo, 'docentes'),
        'mensajes' => null, // El esquema no incluye una tabla de mensajes.
        'usuarios' => adminContar($pdo, 'usuarios'),
    ];
}

/** Devuelve las columnas y filas de una vista de lectura autorizada. */
function adminListado(?PDO $pdo, string $modulo): array
{
    $configuracion = [
        'cursos' => [
            'sql' => <<<'SQL'
SELECT c.id_curso, c.nombre, c.modalidad, c.duracion, c.costo, c.cupo, c.estado, COALESCE(NULLIF(TRIM(CONCAT(u.nombre, ' ', u.apellido)), ''), 'Sin asignar') AS docente FROM curso c LEFT JOIN docentes d ON d.nombre_usuario = c.nombre_usuario_docente LEFT JOIN usuarios u ON u.nombre_usuario = d.nombre_usuario ORDER BY c.id_curso DESC
SQL,
            'columnas' => [
                'id_curso' => 'ID',
                'nombre' => 'Curso',
                'modalidad' => 'Modalidad',
                'duracion' => 'Duración',
                'costo' => 'Costo (UYU)',
                'cupo' => 'Cupo',
                'estado' => 'Estado',
                'docente' => 'Docente',
            ],
        ],
        'estudiantes' => [
            'sql' => <<<'SQL'
SELECT e.nombre_usuario, u.nombre, u.apellido, u.estado, COUNT(m.id_matricula) AS matriculas FROM estudiantes e INNER JOIN usuarios u ON u.nombre_usuario = e.nombre_usuario LEFT JOIN matricula m ON m.nombre_usuario_estudiante = e.nombre_usuario GROUP BY e.nombre_usuario, u.nombre, u.apellido, u.estado ORDER BY u.apellido, u.nombre
SQL,
            'columnas' => [
                'nombre_usuario' => 'Usuario',
                'nombre' => 'Nombre',
                'apellido' => 'Apellido',
                'estado' => 'Estado',
                'matriculas' => 'Matrículas',
            ],
        ],
        'pagos' => [
            'sql' => <<<'SQL'
SELECT p.id_pago, p.fecha_pago, p.monto, p.moneda, p.metodo_pago, p.estado, p.ref_pasarela, m.nombre_usuario_estudiante AS estudiante, c.nombre AS curso FROM pagos p LEFT JOIN matricula m ON m.id_matricula = p.id_matricula LEFT JOIN curso c ON c.id_curso = m.id_curso ORDER BY p.id_pago DESC
SQL,
            'columnas' => [
                'id_pago' => 'ID',
                'fecha_pago' => 'Fecha',
                'monto' => 'Monto',
                'moneda' => 'Moneda',
                'metodo_pago' => 'Método',
                'estado' => 'Estado',
                'ref_pasarela' => 'Referencia',
                'estudiante' => 'Estudiante',
                'curso' => 'Curso',
            ],
        ],
        'usuarios' => [
            'sql' => <<<'SQL'
SELECT nombre_usuario, nombre, apellido, estado FROM usuarios ORDER BY apellido, nombre
SQL,
            'columnas' => [
                'nombre_usuario' => 'Usuario',
                'nombre' => 'Nombre',
                'apellido' => 'Apellido',
                'estado' => 'Estado',
            ],
        ],
        'noticias' => [
            'sql' => <<<'SQL'
SELECT id_noticia, fecha, titulo, contenido, nombre_usuario_admin FROM noticias ORDER BY fecha DESC
SQL,
            'columnas' => [
                'id_noticia' => 'ID',
                'fecha' => 'Fecha',
                'titulo' => 'Título',
                'contenido' => 'Contenido',
                'nombre_usuario_admin' => 'Publicado por',
            ],
        ],
        'banners' => [
            'sql' => <<<'SQL'
SELECT b.id_banner, c.nombre AS curso, b.orden, b.estado FROM banner b INNER JOIN curso c ON c.id_curso = b.id_curso ORDER BY b.orden, b.id_banner
SQL,
            'columnas' => [
                'id_banner' => 'ID',
                'curso' => 'Curso',
                'orden' => 'Orden',
                'estado' => 'Estado',
            ],
        ],
        'galeria' => [
            'sql' => <<<'SQL'
SELECT id_imagen, titulo, fecha, imagen, nombre_usuario_admin FROM galeria ORDER BY fecha DESC
SQL,
            'columnas' => [
                'id_imagen' => 'ID',
                'titulo' => 'Título',
                'fecha' => 'Fecha',
                'imagen' => 'Archivo',
                'nombre_usuario_admin' => 'Publicado por',
            ],
        ],
        'historial_docente' => [
            'sql' => <<<'SQL'
SELECT c.id_curso, c.nombre AS curso, d.nombre_usuario, u.nombre, u.apellido, d.especialidad FROM curso c INNER JOIN docentes d ON d.nombre_usuario = c.nombre_usuario_docente INNER JOIN usuarios u ON u.nombre_usuario = d.nombre_usuario ORDER BY u.apellido, c.nombre
SQL,
            'columnas' => [
                'id_curso' => 'ID curso',
                'curso' => 'Curso asignado',
                'nombre_usuario' => 'Usuario docente',
                'nombre' => 'Nombre',
                'apellido' => 'Apellido',
                'especialidad' => 'Especialidad',
            ],
        ],
    ];

    if (!isset($configuracion[$modulo])) {
        return ['columnas' => [], 'filas' => []];
    }
    return [
        'columnas' => $configuracion[$modulo]['columnas'],
        'filas' => adminConsultar($pdo, $configuracion[$modulo]['sql']),
    ];
}

/** Docentes con datos públicos de la cuenta y sus cursos actuales. */
function adminObtenerDocentes(?PDO $pdo): array
{
    if (!$pdo instanceof PDO) {
        return [];
    }

    $sql = "
        SELECT
            d.nombre_usuario,
            u.nombre,
            u.apellido,
            u.estado,
            d.titulo,
            d.especialidad,
            d.horario,
            c.nombre AS nombre_curso
        FROM docentes d
        INNER JOIN usuarios u
            ON u.nombre_usuario = d.nombre_usuario
        LEFT JOIN curso c
            ON c.nombre_usuario_docente = d.nombre_usuario
        ORDER BY u.nombre, u.apellido
    ";

    $consulta = $pdo->prepare($sql);
    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

/** Cursos para mostrar en la futura asignación docente. */
/**
 * OBTENER CURSOS SIN DOCENTE ASIGNADO
 */
function adminObtenerCursosParaDocentes(?PDO $pdo): array
{
    return adminConsultar($pdo, <<<'SQL'
        SELECT
            id_curso,
            nombre,
            estado
        FROM curso
        WHERE nombre_usuario_docente IS NULL
           OR TRIM(nombre_usuario_docente) = ''
        ORDER BY nombre ASC
    SQL);
}

/** Consulta preparada filtrada por docente para próximas vistas de detalle. */
function adminCursosDeDocente(?PDO $pdo, string $nombreUsuario): array
{
    return adminConsultar($pdo, <<<'SQL'
SELECT id_curso, nombre, modalidad, duracion, estado
FROM curso
WHERE nombre_usuario_docente = :docente
ORDER BY nombre
SQL, ['docente' => $nombreUsuario]);
}

/* ==========================================================
   OBTENER CURSOS
========================================================== */

function adminObtenerCursos(PDO $pdo): array
{
    $consulta = $pdo->prepare("
        SELECT
            c.id_curso,
            c.nombre,
            c.modalidad,
            c.duracion,
            c.descripcion,
            c.costo,
            c.cupo,
            c.estado,
            c.img,
            c.id_categoria,
            cat.nombre AS categoria,
            c.nombre_usuario_docente

        FROM curso c

        LEFT JOIN categoria_cursos cat
            ON cat.id_categoria = c.id_categoria

        ORDER BY c.id_curso DESC
    ");

    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

/* ==========================================================
   OBTENER CATEGORÍAS DE CURSOS
========================================================== */

function adminCategoriasCursos(?PDO $pdo): array
{
    if (!$pdo instanceof PDO) {
        return [];
    }

    $consulta = $pdo->prepare("
        SELECT
            id_categoria,
            nombre

        FROM categoria_cursos

        ORDER BY nombre ASC
    ");

    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}

/* ==========================================================
   OBTENER CURSO POR IDENTIFICADOR
========================================================== */

function adminObtenerCursoPorId(PDO $pdo, int $idCurso): ?array
{
    $consulta = $pdo->prepare("
        SELECT
            id_curso,
            nombre,
            modalidad,
            duracion,
            descripcion,
            costo,
            cupo,
            estado,
            img,
            id_categoria

        FROM curso

        WHERE id_curso = :id

        LIMIT 1
    ");

    $consulta->execute([
        ':id' => $idCurso
    ]);

    $curso = $consulta->fetch(PDO::FETCH_ASSOC);

    return $curso ?: null;
}

/* ==========================================================
   OBTENER NOTICIAS
========================================================== */

function adminObtenerNoticias(PDO $pdo): array
{
    $consulta = $pdo->prepare("
        SELECT
            id_noticia,
            fecha,
            titulo,
            contenido,
            imagen,
            enlace,
            nombre_usuario_admin,
            estado
        FROM noticias
        ORDER BY fecha DESC, id_noticia DESC
    ");

    $consulta->execute();

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}


/* ==========================================================
   OBTENER NOTICIA POR ID
========================================================== */

function adminObtenerNoticiaPorId(PDO $pdo, int $idNoticia): ?array
{
    $consulta = $pdo->prepare("
        SELECT
            id_noticia,
            fecha,
            titulo,
            contenido,
            imagen,
            enlace,
            nombre_usuario_admin,
            estado
        FROM noticias
        WHERE id_noticia = :id
        LIMIT 1
    ");

    $consulta->execute([
        ':id' => $idNoticia
    ]);

    $noticia = $consulta->fetch(PDO::FETCH_ASSOC);

    return $noticia ?: null;
}
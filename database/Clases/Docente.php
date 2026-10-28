<?php

require_once __DIR__ . '/Usuario.php';

require_once __DIR__ . '/../consultas/encriptacion.php';

require_once __DIR__ . '/../../Mailer/enviarCorreo.php';

class Docente extends Usuario
{

    /* ==========================================================
       1. ATRIBUTOS
    ========================================================== */

    private ?string $titulo;

    private ?string $curriculum_vitae;

    private ?string $horario;

    private ?string $especialidad;


    /* ==========================================================
       2. CONSTRUCTOR
    ========================================================== */

    public function __construct(
        $nombre_usuario,
        $nombre,
        $apellido,
        $cedula,
        $cedula_iv,
        $cedula_tag,
        $correo,
        $correo_iv,
        $correo_tag,
        $password,
        $estado,
        $titulo = null,
        $curriculum_vitae = null,
        $horario = null,
        $especialidad = null
    ) {

        parent::__construct(
            $nombre_usuario,
            $nombre,
            $apellido,
            $cedula,
            $cedula_iv,
            $cedula_tag,
            $correo,
            $correo_iv,
            $correo_tag,
            $password,
            $estado
        );

        $this->titulo = $titulo;

        $this->curriculum_vitae = $curriculum_vitae;

        $this->horario = $horario;

        $this->especialidad = $especialidad;

    }


    /* ==========================================================
       3. GETTERS
    ========================================================== */

    public function getTitulo(): ?string
    {

        return $this->titulo;

    }


    public function getCurriculumVitae(): ?string
    {

        return $this->curriculum_vitae;

    }


    public function getHorario(): ?string
    {

        return $this->horario;

    }


    public function getEspecialidad(): ?string
    {

        return $this->especialidad;

    }


    /* ==========================================================
       4. COMPROBAR EXISTENCIA DEL USUARIO
    ========================================================== */

    private static function existeUsuario(
        PDO $pdo,
        string $nombreUsuario
    ): bool {

        $consulta = $pdo->prepare("

            SELECT
                nombre_usuario

            FROM usuarios

            WHERE
                nombre_usuario = :nombre_usuario

            LIMIT 1

        ");


        $consulta->execute([

            ':nombre_usuario' => $nombreUsuario

        ]);


        return $consulta->fetchColumn() !== false;

    }


    /* ==========================================================
       5. REGISTRAR DOCENTE
    ========================================================== */

    public static function registrar(
        PDO $pdo,
        array $datos,
        array $archivo,
        string $key
    ): void {

        /* ======================================================
           6. OBTENER DATOS
        ====================================================== */

        $usuario = trim(
            (string) ($datos['nombre_usuario'] ?? '')
        );

        $nombre = trim(
            (string) ($datos['nombre'] ?? '')
        );

        $apellido = trim(
            (string) ($datos['apellido'] ?? '')
        );

        $cedula = preg_replace(
            '/\D+/',
            '',
            (string) ($datos['cedula'] ?? '')
        );

        $correo = trim(
            (string) ($datos['correo'] ?? '')
        );

        $password = (string) (
            $datos['password'] ?? ''
        );

        $confirmarPassword = (string) (
            $datos['confirmar_password'] ?? ''
        );

        $titulo = trim(
            (string) ($datos['titulo'] ?? '')
        );

        $especialidad = trim(
            (string) ($datos['especialidad'] ?? '')
        );

        $horario = trim(
            (string) ($datos['horario'] ?? '')
        );

        $cursos = $datos['cursos'] ?? [];


        /* ======================================================
           7. VALIDAR DATOS PERSONALES
        ====================================================== */

        if (
            !preg_match(
                '/\A[a-zA-Z0-9_.-]{3,50}\z/D',
                $usuario
            )
        ) {

            throw new InvalidArgumentException(
                'El nombre de usuario debe tener entre 3 y 50 caracteres.'
            );

        }


        if (
            $nombre === '' ||
            mb_strlen($nombre) > 20 ||
            $apellido === '' ||
            mb_strlen($apellido) > 50
        ) {

            throw new InvalidArgumentException(
                'Ingresá un nombre y apellido válidos.'
            );

        }


        if (
            !preg_match(
                '/\A\d{6,8}\z/D',
                $cedula
            )
        ) {

            throw new InvalidArgumentException(
                'Ingresá una cédula válida.'
            );

        }


        if (
            !filter_var(
                $correo,
                FILTER_VALIDATE_EMAIL
            ) ||
            strlen($correo) > 200
        ) {

            throw new InvalidArgumentException(
                'Ingresá un correo electrónico válido.'
            );

        }


        /* ======================================================
           8. VALIDAR CONTRASEÑA
        ====================================================== */

        if (
            strlen($password) < 8
        ) {

            throw new InvalidArgumentException(
                'La contraseña debe tener al menos 8 caracteres.'
            );

        }


        if (
            $password !== $confirmarPassword
        ) {

            throw new InvalidArgumentException(
                'Las contraseñas no coinciden.'
            );

        }


        /* ======================================================
           9. VALIDAR DATOS PROFESIONALES
        ====================================================== */

        if (
            mb_strlen($titulo) > 100 ||
            mb_strlen($especialidad) > 100 ||
            mb_strlen($horario) > 100
        ) {

            throw new InvalidArgumentException(
                'Los datos profesionales superan la longitud permitida.'
            );

        }


        /* ======================================================
           10. VALIDAR CURSOS
        ====================================================== */

        if (
            !is_array($cursos) ||
            empty($cursos) ||
            count($cursos) > 100
        ) {

            throw new InvalidArgumentException(
                'Seleccioná al menos un curso válido.'
            );

        }


        $idsCursos = [];


        foreach ($cursos as $id) {

            $idCurso = filter_var(
                $id,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1
                    ]
                ]
            );


            if (
                $idCurso === false
            ) {

                throw new InvalidArgumentException(
                    'Se recibió un curso inválido.'
                );

            }


            $idsCursos[] = $idCurso;

        }


        $idsCursos = array_values(
            array_unique($idsCursos)
        );


        /* ======================================================
           11. VALIDAR CLAVE AES
        ====================================================== */

        if (
            $key === ''
        ) {

            throw new RuntimeException(
                'No se encontró la clave de cifrado.'
            );

        }


        /* ======================================================
           12. VALIDAR CURRÍCULUM
        ====================================================== */

        if (
            ($archivo['error'] ?? UPLOAD_ERR_NO_FILE)
                !== UPLOAD_ERR_OK ||
            !isset($archivo['tmp_name']) ||
            !is_uploaded_file($archivo['tmp_name'])
        ) {

            throw new InvalidArgumentException(
                'Adjuntá un currículum vitae válido.'
            );

        }


        if (
            ($archivo['size'] ?? 0) < 1 ||
            $archivo['size'] > 5 * 1024 * 1024
        ) {

            throw new InvalidArgumentException(
                'El currículum no puede superar los 5 MB.'
            );

        }


        $finfo = new finfo(
            FILEINFO_MIME_TYPE
        );

        $mime = $finfo->file(
            $archivo['tmp_name']
        );

        $extension = strtolower(
            pathinfo(
                (string) ($archivo['name'] ?? ''),
                PATHINFO_EXTENSION
            )
        );


        if (
            $mime !== 'application/pdf' ||
            $extension !== 'pdf'
        ) {

            throw new InvalidArgumentException(
                'El currículum debe estar en formato PDF.'
            );

        }


        /* ======================================================
           13. CIFRAR INFORMACIÓN PERSONAL
        ====================================================== */

        if (
            !function_exists('encrypt_aes_256_gcm')
        ) {

            throw new RuntimeException(
                'No se encontró la función de cifrado.'
            );

        }


        $cedulaCifrada = encrypt_aes_256_gcm(
            $cedula,
            $key
        );


        $correoCifrado = encrypt_aes_256_gcm(
            $correo,
            $key
        );


        foreach (
            ['ciphertext', 'iv', 'tag'] as $campo
        ) {

            if (
                !isset(
                    $cedulaCifrada[$campo],
                    $correoCifrado[$campo]
                )
            ) {

                throw new RuntimeException(
                    'El resultado del cifrado es incompatible.'
                );

            }

        }


        if (
            strlen($cedulaCifrada['ciphertext']) > 16 ||
            strlen($correoCifrado['ciphertext']) > 255
        ) {

            throw new RuntimeException(
                'Los datos cifrados superan el tamaño permitido.'
            );

        }


        /* ======================================================
        14. CONFIGURAR ARCHIVO PDF
        ====================================================== */

        // OBTENER RAÍZ DEL PROYECTO

        $raizProyecto = dirname(
            __DIR__,
            2
        );


        // CARPETA DE CURRÍCULUMS

        $directorio = $raizProyecto .
            DIRECTORY_SEPARATOR .
            'curriculums';


        // OBTENER NOMBRE ORIGINAL DEL ARCHIVO

        $nombreOriginal = pathinfo(
            (string) ($archivo['name'] ?? ''),
            PATHINFO_FILENAME
        );


        // NORMALIZAR NOMBRE DEL ARCHIVO

        $nombreOriginal = preg_replace(
            '/[^a-zA-Z0-9_-]/',
            '_',
            $nombreOriginal
        );


        // EVITAR NOMBRES VACÍOS

        if (
            $nombreOriginal === ''
        ) {

            $nombreOriginal = 'Curriculum';

        }


        // LIMITAR LONGITUD DEL NOMBRE

        $nombreOriginal = substr(
            $nombreOriginal,
            0,
            100
        );


        // GENERAR NOMBRE DEL CURRÍCULUM

        $nombreArchivo = $usuario .
            '_' .
            $nombreOriginal .
            '.pdf';


        // RUTA FÍSICA DEL ARCHIVO

        $rutaFinal = $directorio .
            DIRECTORY_SEPARATOR .
            $nombreArchivo;


        // RUTA PARA LA BASE DE DATOS

        $rutaRelativa = 'curriculums/' .
            $nombreArchivo;


        // CONTROLAR SI EL ARCHIVO FUE GUARDADO

        $archivoMovido = false;


        /* ======================================================
           15. INICIAR TRANSACCIÓN
        ====================================================== */

        $pdo->beginTransaction();


        try {

            /* ==================================================
               16. COMPROBAR NOMBRE DE USUARIO
            ================================================== */

            if (
                self::existeUsuario(
                    $pdo,
                    $usuario
                )
            ) {

                throw new InvalidArgumentException(
                    'El nombre de usuario "' .
                    $usuario .
                    '" ya se encuentra registrado.'
                );

            }


            /* ==================================================
               17. REGISTRAR USUARIO
            ================================================== */

            $consulta = $pdo->prepare("

                INSERT INTO usuarios (

                    nombre_usuario,
                    nombre,
                    apellido,
                    cedula,
                    cedula_iv,
                    cedula_tag,
                    correo,
                    correo_iv,
                    correo_tag,
                    password,
                    estado

                ) VALUES (

                    :usuario,
                    :nombre,
                    :apellido,
                    :cedula,
                    :cedula_iv,
                    :cedula_tag,
                    :correo,
                    :correo_iv,
                    :correo_tag,
                    :password,
                    :estado

                )

            ");


            $consulta->execute([

                ':usuario' => $usuario,

                ':nombre' => $nombre,

                ':apellido' => $apellido,

                ':cedula' => $cedulaCifrada['ciphertext'],

                ':cedula_iv' => $cedulaCifrada['iv'],

                ':cedula_tag' => $cedulaCifrada['tag'],

                ':correo' => $correoCifrado['ciphertext'],

                ':correo_iv' => $correoCifrado['iv'],

                ':correo_tag' => $correoCifrado['tag'],

                ':password' => password_hash(
                    $password,
                    PASSWORD_ARGON2ID
                ),

                ':estado' => 'Activo'

            ]);


            /* ==================================================
               18. CREAR DIRECTORIO DE CURRÍCULUMS
            ================================================== */

            if (
                !is_dir($directorio) &&
                !mkdir(
                    $directorio,
                    0700,
                    true
                ) &&
                !is_dir($directorio)
            ) {

                throw new RuntimeException(
                    'No se pudo crear la carpeta de currículums.'
                );

            }


            /* ==================================================
               19. GUARDAR CURRÍCULUM
            ================================================== */

            if (
                !move_uploaded_file(
                    $archivo['tmp_name'],
                    $rutaFinal
                )
            ) {

                throw new RuntimeException(
                    'No se pudo guardar el currículum vitae.'
                );

            }


            $archivoMovido = true;


            /* ==================================================
               20. REGISTRAR DOCENTE
            ================================================== */

            $consulta = $pdo->prepare("

                INSERT INTO docentes (

                    nombre_usuario,
                    titulo,
                    curriculum_vitae,
                    horario,
                    especialidad

                ) VALUES (

                    :usuario,
                    :titulo,
                    :cv,
                    :horario,
                    :especialidad

                )

            ");


            $consulta->execute([

                ':usuario' => $usuario,

                ':titulo' => $titulo !== ''
                    ? $titulo
                    : null,

                ':cv' => $rutaRelativa,

                ':horario' => $horario !== ''
                    ? $horario
                    : null,

                ':especialidad' => $especialidad !== ''
                    ? $especialidad
                    : null

            ]);


            /* ==================================================
               21. ASIGNAR CURSOS
            ================================================== */

            $consulta = $pdo->prepare("

                UPDATE curso

                SET
                    nombre_usuario_docente = :usuario

                WHERE
                    id_curso = :curso

                    AND (

                        nombre_usuario_docente IS NULL

                        OR TRIM(nombre_usuario_docente) = ''

                    )

            ");


            foreach ($idsCursos as $idCurso) {

                $consulta->execute([

                    ':usuario' => $usuario,

                    ':curso' => $idCurso

                ]);


                if (
                    $consulta->rowCount() !== 1
                ) {

                    throw new InvalidArgumentException(
                        'Uno de los cursos ya tiene un docente asignado o no existe.'
                    );

                }

            }


            /* ==================================================
               22. CONFIRMAR REGISTRO
            ================================================== */

            $pdo->commit();


        } catch (Throwable $e) {

            /* ==================================================
               23. REVERTIR CAMBIOS
            ================================================== */

            if (
                $pdo->inTransaction()
            ) {

                $pdo->rollBack();

            }


            /* ELIMINAR CV SI FALLÓ EL REGISTRO */

            if (
                $archivoMovido &&
                is_file($rutaFinal)
            ) {

                unlink($rutaFinal);

            }


            throw $e;

        }

    }

    /* ==========================================================
    DAR DE BAJA A UN DOCENTE
    ========================================================== */

    public static function darDeBaja(PDO $pdo, string $nombreUsuario): bool
    {
        // COMPROBAR NOMBRE DE USUARIO

        $nombreUsuario = trim($nombreUsuario);

        if ($nombreUsuario === '') {
            throw new InvalidArgumentException('No se indicó el docente.');
        }


        /* ======================================================
        1. OBTENER INFORMACIÓN DEL DOCENTE
        ====================================================== */

        $consulta = $pdo->prepare("
            SELECT
                u.nombre,
                u.apellido,
                u.correo,
                u.correo_iv,
                u.correo_tag

            FROM usuarios u

            INNER JOIN docentes d
                ON d.nombre_usuario = u.nombre_usuario

            WHERE u.nombre_usuario = :usuario

            LIMIT 1
        ");

        $consulta->execute([
            ':usuario' => $nombreUsuario
        ]);

        $datosDocente = $consulta->fetch(PDO::FETCH_ASSOC);

        if (!$datosDocente) {
            throw new RuntimeException('No se encontró el docente.');
        }


        /* ======================================================
        2. ACTUALIZAR ESTADO
        ====================================================== */

        $consulta = $pdo->prepare("
            UPDATE usuarios

            SET estado = 'Inactivo'

            WHERE nombre_usuario = :usuario

            AND estado <> 'Inactivo'

            AND EXISTS (
                SELECT 1
                FROM docentes
                WHERE docentes.nombre_usuario = usuarios.nombre_usuario
            )
        ");

        $consulta->execute([
            ':usuario' => $nombreUsuario
        ]);

        $resultado = $consulta->rowCount() > 0;

        if (!$resultado) {
            return false;
        }


        /* ======================================================
        3. ENVIAR CORREO DE BAJA
        ====================================================== */

        try {

            // OBTENER CLAVE DE CIFRADO

            $key = $GLOBALS['key'] ?? null;

            if (!is_string($key) || $key === '') {
                throw new RuntimeException(
                    'No está disponible la clave de cifrado.'
                );
            }


            // DESCIFRAR CORREO

            $correo = decrypt_aes_256_gcm(
                $datosDocente['correo'],
                $key,
                $datosDocente['correo_iv'],
                $datosDocente['correo_tag']
            );

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException(
                    'El correo electrónico del docente no es válido.'
                );
            }


            // OBTENER NOMBRE COMPLETO

            $nombre = trim(
                $datosDocente['nombre'] . ' ' .
                $datosDocente['apellido']
            );


            // PROTEGER CONTENIDO

            $nombreSeguro = htmlspecialchars(
                $nombre,
                ENT_QUOTES,
                'UTF-8'
            );

            $usuarioSeguro = htmlspecialchars(
                $nombreUsuario,
                ENT_QUOTES,
                'UTF-8'
            );


            /* ==================================================
            4. PREPARAR NOTIFICACIÓN
            ================================================== */

            $asunto = 'Notificación de baja docente - INADI';

            $mensaje = '
                Estimado/a <strong>' . $nombreSeguro . '</strong>:<br><br>

                Por medio del presente, te informamos que tu cuenta
                docente en el sistema institucional
                <strong>INSOFT</strong> ha sido desactivada.<br><br>

                Como consecuencia de esta modificación,
                <strong>ya no dispondrás de acceso a la plataforma
                docente de INADI</strong>, incluyendo las herramientas
                y funcionalidades asociadas a tu rol.<br><br>

                Agradecemos tu participación y contribución
                a las actividades académicas de nuestra institución.
            ';


            // AVISO

            $aviso = '
                <strong>Información de tu cuenta</strong><br><br>

                <strong>Usuario:</strong> ' . $usuarioSeguro . '<br>

                <strong>Rol:</strong> Docente<br>

                <strong>Estado:</strong> Inactivo<br><br>

                <strong>Importante:</strong> Tu acceso a la plataforma
                docente ha sido suspendido.<br><br>

                Si considerás que esta modificación se realizó
                por error o necesitás más información,
                comunicate con la administración de INADI.
            ';


            // GENERAR PLANTILLA

            $contenido = plantillaCorreo(
                'Notificación de baja docente',
                $mensaje,
                $aviso
            );


            /* ==================================================
            5. ENVIAR NOTIFICACIÓN
            ================================================== */

            if (!enviarCorreo($correo, $asunto, $contenido)) {
                throw new RuntimeException(
                    'No se pudo enviar el correo de baja.'
                );
            }

        } catch (Throwable $e) {

            // LA BAJA SE MANTIENE AUNQUE FALLE EL CORREO

            error_log(
                '[INSOFT correo baja docente] ' .
                $e->getMessage()
            );

            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['aviso_docentes'] =
                    'El docente fue dado de baja, pero no se pudo enviar la notificación por correo.';
            }

        }


        /* ======================================================
        6. CONFIRMAR OPERACIÓN
        ====================================================== */

        return true;
    }

    /* ==========================================================
    DAR DE ALTA A UN DOCENTE
    ========================================================== */

    public static function darDeAlta(PDO $pdo, string $nombreUsuario): bool
    {
        $consulta = $pdo->prepare("
            UPDATE usuarios
            SET estado = 'Activo'
            WHERE nombre_usuario = :usuario
            AND estado = 'Inactivo'
            AND EXISTS (
                SELECT 1
                FROM docentes
                WHERE docentes.nombre_usuario = usuarios.nombre_usuario
            )
        ");

        $consulta->execute([
            ':usuario' => $nombreUsuario
        ]);

        return $consulta->rowCount() > 0;
    }

}
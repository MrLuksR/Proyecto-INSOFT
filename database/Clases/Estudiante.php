
<?php
    include_once(__DIR__ . "/Usuario.php");
    
    class Estudiante extends Usuario{
        private $telefono;
        private $telefono_iv;
        private $telefono_tag;

        private $fecha_nacimiento;
        private $fecha_nacimiento_iv;
        private $fecha_nacimiento_tag;

        private $foto;

        // Constructor
        function __construct(
            $nombre_usuario, $nombre, $apellido, 
            $cedula, $cedula_iv, $cedula_tag, 
            $correo, $correo_iv, $correo_tag, 
            $password, $estado,

            $telefono, $telefono_iv, $telefono_tag,
            $fecha_nacimiento, $fecha_nacimiento_iv, $fecha_nacimiento_tag,
            $foto
        ){
            parent::__construct(
                $nombre_usuario, $nombre, $apellido,
                
                $cedula, $cedula_iv, $cedula_tag,
                
                $correo, $correo_iv, $correo_tag,
                
                $password, $estado);

                $this->telefono = $telefono;
                $this->telefono_iv = $telefono_iv;
                $this->telefono_tag = $telefono_tag;

                $this->fecha_nacimiento = $fecha_nacimiento;
                $this->fecha_nacimiento_iv = $fecha_nacimiento_iv;
                $this->fecha_nacimiento_tag = $fecha_nacimiento_tag;
                $this->foto = $foto;
        }

        // Registrar nuevo usuario
        function registrar($pdo){
            try {
                // INSERTAR EL USUARIO Y ESTUDIANTE MEDIANTE TRANSACCIÓN
                $pdo->beginTransaction();

                // 1. INSERTAR USUARIO
                $sql1 = "INSERT INTO usuarios (
                            nombre_usuario, nombre, apellido,
                            cedula, cedula_iv, cedula_tag,
                            correo, correo_iv, correo_tag,
                            password, estado
                        ) VALUES (
                            :nombre_usuario, :nombre, :apellido,
                            :cedula, :cedula_iv, :cedula_tag,
                            :correo, :correo_iv, :correo_tag,
                            :password, :estado
                        )";

                $consulta = $pdo->prepare($sql1);

                $consulta->execute([
                    ':nombre_usuario' => $this->nombre_usuario,
                    ':nombre'         => $this->nombre,
                    ':apellido'       => $this->apellido,

                    ':cedula'         => $this->cedula,
                    ':cedula_iv'      => $this->cedula_iv,
                    ':cedula_tag'     => $this->cedula_tag,

                    ':correo'         => $this->correo,
                    ':correo_iv'      => $this->correo_iv,
                    ':correo_tag'     => $this->correo_tag,

                    ':password'       => $this->password,
                    ':estado'         => $this->estado
                ]);

                // 2. INSERTAR ESTUDIANTE
                $sql2 = "INSERT INTO estudiantes (
                            nombre_usuario,
                            telefono, telefono_iv, telefono_tag,
                            fecha_nacimiento, fecha_nacimiento_iv,
                            fecha_nacimiento_tag,
                            foto
                        ) VALUES (
                            :nombre_usuario,
                            :telefono, :telefono_iv, :telefono_tag,
                            :fecha_nacimiento, :fecha_nacimiento_iv,
                            :fecha_nacimiento_tag,
                            :foto
                        )";

                $consulta = $pdo->prepare($sql2);

                $consulta->execute([
                    ':nombre_usuario'       => $this->nombre_usuario,

                    ':telefono'             => $this->telefono,
                    ':telefono_iv'          => $this->telefono_iv,
                    ':telefono_tag'         => $this->telefono_tag,

                    ':fecha_nacimiento'     => $this->fecha_nacimiento,
                    ':fecha_nacimiento_iv'  => $this->fecha_nacimiento_iv,
                    ':fecha_nacimiento_tag' => $this->fecha_nacimiento_tag,

                    ':foto'                 => $this->foto
                ]);

                // 3. CONFIRMAR LA TRANSACCIÓN
                $pdo->commit();

                return true;

            } catch (PDOException $e) {

                // REVERTIR LOS CAMBIOS SI OCURRIÓ UN ERROR
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                error_log("Error al registrar estudiante: " . $e->getMessage());

                return false;
            }
        }

        // --- TELEFONO ---
        function getTelefono($key){
            $telefono = decrypt_aes_256_gcm($this->telefono, $key, $this->telefono_iv, $this->telefono_tag);
            return $telefono;
        }

        // ACTUALIZAR TELÉFONO
        public function updateTelefono(PDO $pdo, string $telefono, string $key){
            $telefono = trim($telefono);

            // VALIDAR TELÉFONO
            if (
                $telefono !== '' &&
                !preg_match('/^[0-9+\s()-]{6,20}$/', $telefono)
            ) {
                throw new Exception('El formato del teléfono no es válido.');
            }

            // CIFRAR TELÉFONO
            $telefonoCifrado = encrypt_aes_256_gcm($telefono, $key);

            // ACTUALIZAR BASE DE DATOS
            $sql = "UPDATE estudiantes
                    SET telefono = :telefono,
                        telefono_iv = :telefono_iv,
                        telefono_tag = :telefono_tag
                    WHERE nombre_usuario = :nombre_usuario";

            $consulta = $pdo->prepare($sql);

            $consulta->execute([
                ':telefono' => $telefonoCifrado['ciphertext'],
                ':telefono_iv' => $telefonoCifrado['iv'],
                ':telefono_tag' => $telefonoCifrado['tag'],
                ':nombre_usuario' => $this->getNombreUsuario()
            ]);

            // ACTUALIZAR OBJETO
            $this->setTelefonoCifrado(
                $telefonoCifrado['ciphertext'],
                $telefonoCifrado['iv'],
                $telefonoCifrado['tag']
            );

            return true;
        }

        // --- FECHA NACIMIENTO ---
        function getFechaNacimiento($key){
            $fecha_nacimiento = decrypt_aes_256_gcm($this->fecha_nacimiento, $key, $this->fecha_nacimiento_iv, $this->fecha_nacimiento_tag);
            return $fecha_nacimiento;
        }

        // ACTUALIZAR FECHA DE NACIMIENTO
        public function updateFechaNacimiento(PDO $pdo, string $fecha, string $key){
            $fecha = trim($fecha);

            // VALIDAR FECHA (DD/MM/AAAA)
            $fechaObjeto = DateTime::createFromFormat('!d/m/Y', $fecha);

            if (
                !$fechaObjeto ||
                $fechaObjeto->format('d/m/Y') !== $fecha
            ) {
                throw new Exception('La fecha de nacimiento no es válida.');
            }

            // CIFRAR FECHA
            $fechaCifrada = encrypt_aes_256_gcm($fecha, $key);

            // ACTUALIZAR BASE DE DATOS
            $sql = "UPDATE estudiantes
                    SET fecha_nacimiento = :fecha,
                        fecha_nacimiento_iv = :fecha_iv,
                        fecha_nacimiento_tag = :fecha_tag
                    WHERE nombre_usuario = :nombre_usuario";

            $consulta = $pdo->prepare($sql);

            $consulta->execute([
                ':fecha' => $fechaCifrada['ciphertext'],
                ':fecha_iv' => $fechaCifrada['iv'],
                ':fecha_tag' => $fechaCifrada['tag'],
                ':nombre_usuario' => $this->getNombreUsuario()
            ]);

            // ACTUALIZAR OBJETO
            $this->fecha_nacimiento = $fechaCifrada['ciphertext'];
            $this->fecha_nacimiento_iv = $fechaCifrada['iv'];
            $this->fecha_nacimiento_tag = $fechaCifrada['tag'];

            return true;
        }

        // --- FOTO ---
        function getFoto(){
            return $this->foto;
        }

        // ACTUALIZAR FOTOGRAFÍA DEL ESTUDIANTE
        public function updateFoto(PDO $pdo){
            // COMPROBAR SI SE ENVIÓ UNA FOTOGRAFÍA
            if (
                !isset($_FILES['foto']) ||
                $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE
            ) {
                return false;
            }

            // COMPROBAR ERRORES
            if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Error al subir la fotografía.');
            }

            // VALIDAR TAMAÑO MÁXIMO (5 MB)
            if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
                throw new Exception('La fotografía no puede superar los 5 MB.');
            }

            // OBTENER ARCHIVO TEMPORAL
            $archivoTemporal = $_FILES['foto']['tmp_name'];

            if (!is_uploaded_file($archivoTemporal)) {
                throw new Exception('El archivo recibido no es válido.');
            }

            // VALIDAR FORMATO REAL
            $tipoMime = mime_content_type($archivoTemporal);

            $formatosPermitidos = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($formatosPermitidos[$tipoMime])) {
                throw new Exception('La fotografía debe ser JPG, PNG o WEBP.');
            }

            // COMPROBAR QUE SEA UNA IMAGEN
            if (getimagesize($archivoTemporal) === false) {
                throw new Exception('El archivo no es una imagen válida.');
            }

            // OBTENER EXTENSIÓN REAL
            $extension = $formatosPermitidos[$tipoMime];

            // OBTENER NOMBRE DE USUARIO
            $nombreUsuario = $this->getNombreUsuario();

            // VALIDAR NOMBRE DE USUARIO PARA UTILIZARLO EN EL ARCHIVO
            if (
                $nombreUsuario === '' ||
                !preg_match('/^[a-zA-Z0-9_-]+$/', $nombreUsuario)
            ) {
                throw new Exception('El nombre de usuario no es válido.');
            }

            // GENERAR NOMBRE DE FOTOGRAFÍA
            $nombreFoto = 'FotoPerfil_' . $nombreUsuario . '.' . $extension;

            // OBTENER FOTOGRAFÍA ANTERIOR
            $fotoAnterior = $this->getFoto();

            // VALIDAR NOMBRE ANTERIOR
            if (
                !empty($fotoAnterior) &&
                basename($fotoAnterior) !== $fotoAnterior
            ) {
                throw new Exception('El nombre de la fotografía anterior no es válido.');
            }

            // COMPROBAR SI CAMBIÓ EL NOMBRE
            $cambioNombre = ($fotoAnterior !== $nombreFoto);

            // DEFINIR DIRECTORIO
            $directorio = __DIR__ . '/../consultas/estudiantes/fotosEst/';

            if (!is_dir($directorio) || !is_writable($directorio)) {
                throw new Exception('El directorio de fotografías no está disponible.');
            }

            // DEFINIR RUTAS
            $rutaNueva = $directorio . $nombreFoto;

            $rutaAnterior = !empty($fotoAnterior)
                ? $directorio . $fotoAnterior
                : null;

            // CREAR ARCHIVO TEMPORAL
            $rutaTemporal = tempnam($directorio, 'foto_');

            if ($rutaTemporal === false) {
                throw new Exception('No se pudo preparar la fotografía.');
            }

            // VARIABLES DE CONTROL
            $rutaRespaldo = null;
            $archivoReemplazado = false;
            $actualizacionBDRealizada = false;

            try {

                // MOVER FOTOGRAFÍA AL ARCHIVO TEMPORAL
                if (!move_uploaded_file($archivoTemporal, $rutaTemporal)) {
                    throw new Exception('No se pudo guardar la fotografía.');
                }

                // RESPALDAR ARCHIVO EXISTENTE SI TIENE EL MISMO NOMBRE
                if (is_file($rutaNueva)) {

                    $rutaRespaldo = tempnam($directorio, 'respaldo_');

                    if (
                        $rutaRespaldo === false ||
                        !copy($rutaNueva, $rutaRespaldo)
                    ) {
                        throw new Exception('No se pudo respaldar la fotografía anterior.');
                    }
                }

                // REEMPLAZAR O GUARDAR FOTOGRAFÍA
                if (!rename($rutaTemporal, $rutaNueva)) {
                    throw new Exception('No se pudo reemplazar la fotografía.');
                }

                $archivoReemplazado = true;

                // ACTUALIZAR BASE DE DATOS SOLO SI CAMBIÓ EL NOMBRE
                if ($cambioNombre) {

                    $sql = "UPDATE estudiantes
                            SET foto = :foto
                            WHERE nombre_usuario = :nombre_usuario";

                    $consulta = $pdo->prepare($sql);

                    $consulta->execute([
                        ':foto' => $nombreFoto,
                        ':nombre_usuario' => $nombreUsuario
                    ]);

                    // VERIFICAR QUE EL ESTUDIANTE EXISTA
                    if ($consulta->rowCount() === 0) {
                        throw new Exception('No se pudo actualizar la fotografía en la base de datos.');
                    }

                    $actualizacionBDRealizada = true;
                }

                // ACTUALIZAR ATRIBUTO DEL OBJETO
                $this->foto = $nombreFoto;

                // ELIMINAR FOTOGRAFÍA ANTERIOR SI CAMBIÓ LA EXTENSIÓN
                if (
                    $cambioNombre &&
                    $rutaAnterior !== null &&
                    is_file($rutaAnterior)
                ) {

                    if (!unlink($rutaAnterior)) {
                        error_log(
                            'No se pudo eliminar la fotografía anterior: '
                            . $rutaAnterior
                        );
                    }
                }

                // LIMPIAR CACHÉ DE ESTADO DEL ARCHIVO
                clearstatcache(true, $rutaNueva);

                return true;

            } catch (Throwable $e) {

                // RESTAURAR FOTOGRAFÍA ANTERIOR SI FALLÓ
                if ($archivoReemplazado) {

                    if (
                        $rutaRespaldo !== null &&
                        is_file($rutaRespaldo)
                    ) {
                        copy($rutaRespaldo, $rutaNueva);

                    } elseif (is_file($rutaNueva)) {
                        unlink($rutaNueva);
                    }
                }

                // RESTAURAR EL ATRIBUTO DEL OBJETO
                $this->foto = $fotoAnterior;

                // REGISTRAR EL ERROR
                error_log(
                    'Error al actualizar fotografía: ' . $e->getMessage()
                );

                throw $e;

            } finally {

                // ELIMINAR ARCHIVO TEMPORAL
                if (is_file($rutaTemporal)) {
                    unlink($rutaTemporal);
                }

                // ELIMINAR RESPALDO
                if (
                    $rutaRespaldo !== null &&
                    is_file($rutaRespaldo)
                ) {
                    unlink($rutaRespaldo);
                }
            }
        }
}
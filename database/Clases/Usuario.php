<?php
    require_once(__DIR__ . "/../consultas/conexion.php");
    include_once(__DIR__ . "/../consultas/encriptacion.php");
    abstract class Usuario {
        protected $nombre_usuario;
        protected $nombre;
        protected $apellido;

        protected $cedula;
        protected $cedula_iv;
        protected $cedula_tag;

        protected $correo;
        protected $correo_iv;
        protected $correo_tag;

        protected $password;
        protected $estado;

        // Constructor
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
            $estado
        ) {
            $this->nombre_usuario = $nombre_usuario;
            $this->nombre = $nombre;
            $this->apellido = $apellido;

            $this->cedula = $cedula;
            $this->cedula_iv = $cedula_iv;
            $this->cedula_tag = $cedula_tag;

            $this->correo = $correo;
            $this->correo_iv = $correo_iv;
            $this->correo_tag = $correo_tag;

            $this->password = $password;
            $this->estado = $estado;
        }

        // --- NOMBRE USUARIO ---
        public function getNombreUsuario(){
            return $this->nombre_usuario;
        }

        public function setNombreUsuario($nombre_usuario){
            $this->nombre_usuario = $nombre_usuario;
        }

        // --- NOMBRE ---
        public function getNombre(){
            return $this->nombre;
        }

        // ACTUALIZAR NOMBRE
        public function updateNombre(PDO $pdo, string $nombre){
            $nombre = trim($nombre);

            if ($nombre === '') {
                throw new Exception('El nombre no puede estar vacío.');
            }

            $sql = "UPDATE usuarios
                    SET nombre = :nombre
                    WHERE nombre_usuario = :nombre_usuario";

            $consulta = $pdo->prepare($sql);

            $consulta->execute([
                ':nombre' => $nombre,
                ':nombre_usuario' => $this->getNombreUsuario()
            ]);

            // ACTUALIZAR OBJETO
            $this->nombre = $nombre;

            return true;
        }

        // --- APELLIDO ---
        public function getApellido(){
            return $this->apellido;
        }

        // ACTUALIZAR APELLIDO
        public function updateApellido(PDO $pdo, string $apellido){
            $apellido = trim($apellido);

            if ($apellido === '') {
                throw new Exception('El apellido no puede estar vacío.');
            }

            $sql = "UPDATE usuarios
                    SET apellido = :apellido
                    WHERE nombre_usuario = :nombre_usuario";

            $consulta = $pdo->prepare($sql);

            $consulta->execute([
                ':apellido' => $apellido,
                ':nombre_usuario' => $this->getNombreUsuario()
            ]);

            // ACTUALIZAR OBJETO
            $this->apellido = $apellido;

            return true;
        }

        // --- CÉDULA ---
        public function getCedula($key){
            $cedula = decrypt_aes_256_gcm($this->cedula, $key, $this->cedula_iv, $this->cedula_tag);
            return $cedula;
        }

        public function setCedula($cedula){
            $this->cedula = $cedula;
        }

        // --- CORREO ---
        public function getCorreo($key){
            $correo = decrypt_aes_256_gcm($this->correo, $key, $this->correo_iv, $this->correo_tag);
            return $correo;
        }

        // ACTUALIZAR CORREO
        public function updateCorreo(PDO $pdo, string $correo, string $key){
            $correo = trim($correo);

            // VALIDAR CORREO
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El correo electrónico no es válido.');
            }

            // CIFRAR CORREO
            $correoCifrado = encrypt_aes_256_gcm($correo, $key);

            // ACTUALIZAR BASE DE DATOS
            $sql = "UPDATE usuarios
                    SET correo = :correo,
                        correo_iv = :correo_iv,
                        correo_tag = :correo_tag
                    WHERE nombre_usuario = :nombre_usuario";

            $consulta = $pdo->prepare($sql);

            $consulta->execute([
                ':correo' => $correoCifrado['ciphertext'],
                ':correo_iv' => $correoCifrado['iv'],
                ':correo_tag' => $correoCifrado['tag'],
                ':nombre_usuario' => $this->getNombreUsuario()
            ]);

            // ACTUALIZAR OBJETO
            $this->correo = $correoCifrado['ciphertext'];
            $this->correo_iv = $correoCifrado['iv'];
            $this->correo_tag = $correoCifrado['tag'];

            return true;
}
        // --- PASSWORD ---
        public function getPassword(){
            return $this->password;
        }

        // ACTUALIZAR CONTRASEÑA
        public function updatePassword(
            PDO $pdo,
            string $passwordActual,
            string $passwordNueva
        ) {
            // COMPROBAR CONTRASEÑA ACTUAL
            if (!password_verify($passwordActual, $this->password)) {
                throw new Exception('La contraseña actual es incorrecta.');
            }

            // VALIDAR NUEVA CONTRASEÑA
            if (strlen($passwordNueva) < 8) {
                throw new Exception('La nueva contraseña debe tener al menos 8 caracteres.');
            }

            // COMPROBAR QUE SEA DIFERENTE
            if (password_verify($passwordNueva, $this->password)) {
                throw new Exception('La nueva contraseña debe ser diferente a la actual.');
            }

            // CIFRAR CONTRASEÑA CON ARGON2ID
            $passwordHash = password_hash(
                $passwordNueva,
                PASSWORD_ARGON2ID
            );

            // ACTUALIZAR BASE DE DATOS
            $sql = "UPDATE usuarios
                    SET password = :password
                    WHERE nombre_usuario = :nombre_usuario
                    AND password = :password_anterior";

            $consulta = $pdo->prepare($sql);

            $consulta->execute([
                ':password' => $passwordHash,
                ':nombre_usuario' => $this->getNombreUsuario(),
                ':password_anterior' => $this->password
            ]);

            // COMPROBAR ACTUALIZACIÓN
            if ($consulta->rowCount() !== 1) {
                throw new Exception(
                    'No se pudo actualizar la contraseña. Intentá nuevamente.'
                );
            }

            // ACTUALIZAR OBJETO
            $this->password = $passwordHash;

            return true;
        }

        // --- ESTADO ---
        public function getEstado(){
            return $this->estado;
        }

        public function setEstado($estado){
            $this->estado = $estado;
        }
}
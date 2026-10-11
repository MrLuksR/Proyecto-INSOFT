<?php
    class Usuario{
        private $nombre_usuario;
        private $nombre;
        private $apellido;

        private $cedula;
        private $cedula_iv;
        private $cedula_tag;

        private $correo;
        private $correo_iv;
        private $correo_tag;

        private $password;

        private $estado;

        // Constructor
        function __construct(
            $nombre_usuario, $nombre, $apellido, 
            $cedula, $cedula_iv, $cedula_tag, 
            $correo, $correo_iv, $correo_tag, 
            $password, $estado
        ){
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

        // Registrar nuevo usuario
        function registrar($pdo){
            try {
                // INSERTAR EL ESTUDIANTE
                $sql = "INSERT INTO usuarios
                        (
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
                        )
                        VALUES
                        (
                            :nombre_usuario,
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
                        )";

                // PREPARAR LA CONSULTA
                $consulta = $pdo->prepare($sql);

                // EJECUTAR
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


                // REGISTRO EXITOSO

                echo "<!DOCTYPE html>";
                echo "<html lang='es'>";
                echo "<head>";
                echo "<meta charset='UTF-8'>";
                echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
                echo "<title>Registro exitoso - INADI</title>";
                echo "</head>";

                echo "<body>";

                echo "<h1>¡Registro exitoso!</h1>";

                echo "<p>El estudiante fue registrado correctamente.</p>";

                // Espera 5 segundos antes de redirigir
                header("Refresh: 3; URL=../../../login/loginn.php");
                echo "Serás redirigido en unos segundos...";
                exit;

                echo "</body>";
                echo "</html>";

            } catch (PDOException $e) {

                echo "<h1>Error al registrar</h1>";

                echo "<p>No se pudo registrar el estudiante.</p>";

                echo "<p>" . $e->getMessage() . "</p>";
            }
        }


        // Getters y setters

        // --- NOMBRE USUARIO ---
        public function getNombreUsuario(): string {
            return $this->nombre_usuario;
        }

        public function setNombreUsuario(string $nombre_usuario): void {
            $this->nombre_usuario = $nombre_usuario;
        }

        // --- NOMBRE ---
        public function getNombre(): string {
            return $this->nombre;
        }

        public function setNombre(string $nombre): void {
            $this->nombre = $nombre;
        }

        // --- APELLIDO ---
        public function getApellido(): string {
            return $this->apellido;
        }

        public function setApellido(string $apellido): void {
            $this->apellido = $apellido;
        }

        // --- CÉDULA ---
        public function getCedula(): string {
            return $this->cedula;
        }

        public function setCedula(string $cedula): void {
            $this->cedula = $cedula;
        }

        // --- CORREO ---
        public function getCorreo(): string {
            return $this->correo;
        }

        public function setCorreo(string $correo): void {
            $this->correo = $correo;
        }

        // --- PASSWORD ---
        public function getPassword(): string {
            return $this->password;
        }

        public function setPassword(string $password): void {
            $this->password = $password;
        }

        // --- ESTADO ---
        public function getEstado(): string {
            return $this->estado;
        }

        public function setEstado(string $estado): void {
            $this->estado = $estado;
        }
    }
?>
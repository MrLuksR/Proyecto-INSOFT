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

        private $telefono;
        private $telefono_iv;
        private $telefono_tag;

        private $password;

        private $fecha;
        private $fecha_iv;
        private $fecha_tag;

        private $foto;
        private $rol;

        // Constructor
        function __construct(
            $nombre_usuario, $nombre, $apellido, 
            $cedula, $cedula_iv, $cedula_tag, 
            $correo, $correo_iv, $correo_tag, 
            $telefono, $telefono_iv, $telefono_tag, 
            $password, 
            $fecha, $fecha_iv, $fecha_tag, 
            $foto, $rol
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
            
            $this->telefono = $telefono;
            $this->telefono_iv = $telefono_iv;
            $this->telefono_tag = $telefono_tag;
            
            $this->password = $password;
            
            $this->fecha = $fecha;
            $this->fecha_iv = $fecha_iv;
            $this->fecha_tag = $fecha_tag;
            
            $this->foto = $foto;
            $this->rol = $rol;
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
                            telefono,
                            telefono_iv,
                            telefono_tag,
                            password,
                            fecha,
                            fecha_iv,
                            fecha_tag,
                            foto,
                            id_rol
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
                            :telefono,
                            :telefono_iv,
                            :telefono_tag,
                            :password,
                            :fecha,
                            :fecha_iv,
                            :fecha_tag,
                            :foto,
                            :id_rol
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

                    ':telefono'       => $this->telefono,
                    ':telefono_iv'    => $this->telefono_iv,
                    ':telefono_tag'   => $this->telefono_tag,

                    ':password'       => $this->password,

                    ':fecha'          => $this->fecha,
                    ':fecha_iv'       => $this->fecha_iv,
                    ':fecha_tag'      => $this->fecha_tag,

                    ':foto'           => $this->foto,
                    ':id_rol'         => $this->rol
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
                header("Refresh: 5; URL=../../../Nosotros/nosotros.php");
                echo "Serás redirigido en 5 segundos...";
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

        // --- TELÉFONO ---
        public function getTelefono(): string {
            return $this->telefono;
        }

        public function setTelefono(string $telefono): void {
            $this->telefono = $telefono;
        }

        // --- PASSWORD ---
        public function getPassword(): string {
            return $this->password;
        }

        public function setPassword(string $password): void {
            $this->password = $password;
        }

        // --- FECHA ---
        public function getFecha(): string {
            return $this->fecha;
        }

        public function setFecha(string $fecha): void {
            $this->fecha = $fecha;
        }

        // --- FOTO ---
        public function getFoto(): ?string {
            return $this->foto;
        }

        public function setFoto(?string $foto): void {
            $this->foto = $foto;
        }

        // --- ROL ---
        public function getRol(): int {
            return $this->rol;
        }

        public function setRol(int $rol): void {
            $this->rol = $rol;
        }

    }
?>
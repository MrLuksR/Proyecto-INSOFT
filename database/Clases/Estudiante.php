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


        // Getters y setters

        // --- TELEFONO ---
        function getTelefono($key){
            $telefono = decrypt_aes_256_gcm($this->telefono, $key, $this->telefono_iv, $this->telefono_tag);
            return $telefono;
        }

        function setTelefono($telefono){
            $this->telefono = $telefono;
            return $this;
        }

        // --- FECHA NACIMIENTO ---
        function getFechaNacimiento($key){
            $fecha_nacimiento = decrypt_aes_256_gcm($this->fecha_nacimiento, $key, $this->fecha_nacimiento_iv, $this->fecha_nacimiento_tag);
            return $fecha_nacimiento;
        }

        function setFechaNacimiento($fecha_nacimiento){
            $this->fecha_nacimiento = $fecha_nacimiento;
            return $this;
        }

        // --- FOTO ---
        function getFoto(){
            return $this->foto;
        }

        function setFoto($foto){
            $this->foto = $foto;
            return $this;
        }
    }
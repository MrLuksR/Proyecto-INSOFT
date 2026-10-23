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

        public function setNombre($nombre){
            $this->nombre = $nombre;
        }

        // --- APELLIDO ---
        public function getApellido(){
            return $this->apellido;
        }

        public function setApellido($apellido){
            $this->apellido = $apellido;
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

        public function setCorreo($correo){
            $this->correo = $correo;
        }

        // --- PASSWORD ---
        public function getPassword(){
            return $this->password;
        }

        public function setPassword($password){
            $this->password = $password;
        }

        // --- ESTADO ---
        public function getEstado(){
            return $this->estado;
        }

        public function setEstado($estado){
            $this->estado = $estado;
        }
}
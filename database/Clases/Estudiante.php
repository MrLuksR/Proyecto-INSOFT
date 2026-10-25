
<?php

/**
 * Clase Estudiante
 * Representa a un estudiante del sistema INSOFT.
 */

class Estudiante
{
    private $id;
    private $nombre;
    private $apellido;
    private $cedula;
    private $correo;
    private $nombreUsuario;
    private $tipoUsuario;

    /**
     * Constructor de la clase.
     */
    public function __construct(
        $id = null,
        $nombre = '',
        $apellido = '',
        $cedula = '',
        $correo = '',
        $nombreUsuario = '',
        $tipoUsuario = 'estudiante'
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->cedula = $cedula;
        $this->correo = $correo;
        $this->nombreUsuario = $nombreUsuario;
        $this->tipoUsuario = $tipoUsuario;
    }

    // Obtener el ID
    public function getId()
    {
        return $this->id;
    }

    // Establecer el ID
    public function setId($id)
    {
        $this->id = $id;
    }

    // Obtener el nombre
    public function getNombre()
    {
        return $this->nombre;
    }

    // Establecer el nombre
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    // Obtener el apellido
    public function getApellido()
    {
        return $this->apellido;
    }

    // Establecer el apellido
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    // Obtener la cédula
    public function getCedula()
    {
        return $this->cedula;
    }

    // Establecer la cédula
    public function setCedula($cedula)
    {
        $this->cedula = $cedula;
    }

    // Obtener el correo
    public function getCorreo()
    {
        return $this->correo;
    }

    // Establecer el correo
    public function setCorreo($correo)
    {
        $this->correo = $correo;
    }

    // Obtener el nombre de usuario
    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    // Establecer el nombre de usuario
    public function setNombreUsuario($nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;
    }

    // Obtener el tipo de usuario
    public function getTipoUsuario()
    {
        return $this->tipoUsuario;
    }

    // Establecer el tipo de usuario
    public function setTipoUsuario($tipoUsuario)
    {
        $this->tipoUsuario = $tipoUsuario;
    }

    // Obtener el nombre completo
    public function getNombreCompleto()
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }
}

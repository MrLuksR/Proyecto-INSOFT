<?php

require_once(__DIR__ . "/Usuario.php");

class Administrativo extends Usuario
{
    // ATRIBUTOS
    private $nivel_acceso;
    private $fecha_ingreso;
    private $cargo;
    private $area;

    // CONSTRUCTOR
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
        $nivel_acceso,
        $fecha_ingreso,
        $cargo,
        $area
    ) {
        // CONSTRUCTOR DE USUARIO
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

        // ATRIBUTOS DEL ADMINISTRATIVO
        $this->nivel_acceso = $nivel_acceso;
        $this->fecha_ingreso = $fecha_ingreso;
        $this->cargo = $cargo;
        $this->area = $area;
    }

    // --- NIVEL DE ACCESO ---

    public function getNivelAcceso()
    {
        return $this->nivel_acceso;
    }

    // ACTUALIZAR NIVEL DE ACCESO
    public function updateNivelAcceso(PDO $pdo, string $nivel_acceso)
    {
        $nivel_acceso = trim($nivel_acceso);

        if ($nivel_acceso === '') {
            throw new Exception('El nivel de acceso no puede estar vacío.');
        }

        $sql = "UPDATE administrativos
                SET nivel_acceso = :nivel_acceso
                WHERE nombre_usuario = :nombre_usuario";

        $consulta = $pdo->prepare($sql);

        $consulta->execute([
            ':nivel_acceso' => $nivel_acceso,
            ':nombre_usuario' => $this->getNombreUsuario()
        ]);

        // ACTUALIZAR OBJETO
        $this->nivel_acceso = $nivel_acceso;

        return true;
    }

    // --- FECHA DE INGRESO ---

    public function getFechaIngreso()
    {
        return $this->fecha_ingreso;
    }

    // ACTUALIZAR FECHA DE INGRESO
    public function updateFechaIngreso(PDO $pdo, string $fecha_ingreso)
    {
        // VALIDAR FORMATO DE FECHA
        $fecha = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $fecha_ingreso
        );

        if (
            !$fecha ||
            $fecha->format('Y-m-d') !== $fecha_ingreso
        ) {
            throw new Exception('La fecha de ingreso no es válida.');
        }

        $sql = "UPDATE administrativos
                SET fecha_ingreso = :fecha_ingreso
                WHERE nombre_usuario = :nombre_usuario";

        $consulta = $pdo->prepare($sql);

        $consulta->execute([
            ':fecha_ingreso' => $fecha_ingreso,
            ':nombre_usuario' => $this->getNombreUsuario()
        ]);

        // ACTUALIZAR OBJETO
        $this->fecha_ingreso = $fecha_ingreso;

        return true;
    }

    // --- CARGO ---

    public function getCargo()
    {
        return $this->cargo;
    }

    // ACTUALIZAR CARGO
    public function updateCargo(PDO $pdo, string $cargo)
    {
        $cargo = trim($cargo);

        if ($cargo === '') {
            throw new Exception('El cargo no puede estar vacío.');
        }

        $sql = "UPDATE administrativos
                SET cargo = :cargo
                WHERE nombre_usuario = :nombre_usuario";

        $consulta = $pdo->prepare($sql);

        $consulta->execute([
            ':cargo' => $cargo,
            ':nombre_usuario' => $this->getNombreUsuario()
        ]);

        // ACTUALIZAR OBJETO
        $this->cargo = $cargo;

        return true;
    }

    // --- ÁREA ---

    public function getArea()
    {
        return $this->area;
    }

    // ACTUALIZAR ÁREA
    public function updateArea(PDO $pdo, string $area)
    {
        $area = trim($area);

        if ($area === '') {
            throw new Exception('El área no puede estar vacía.');
        }

        $sql = "UPDATE administrativos
                SET area = :area
                WHERE nombre_usuario = :nombre_usuario";

        $consulta = $pdo->prepare($sql);

        $consulta->execute([
            ':area' => $area,
            ':nombre_usuario' => $this->getNombreUsuario()
        ]);

        // ACTUALIZAR OBJETO
        $this->area = $area;

        return true;
    }
}
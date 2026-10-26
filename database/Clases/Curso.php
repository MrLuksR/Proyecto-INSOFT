<?php
require_once __DIR__ . '/Gestionable.php';

class Curso implements Gestionable
{
    private ?int $id_curso;
    private string $nombre;
    private string $modalidad;
    private string $duracion;
    private string $descripcion;
    private float $costo;
    private int $cupo;
    private string $estado;
    private string $img;
    private int $id_categoria;

    public function __construct(
        string $nombre,
        string $modalidad,
        string $duracion,
        string $descripcion,
        float $costo,
        int $cupo,
        string $estado,
        string $img,
        int $id_categoria,
        ?int $id_curso = null
    ) {
        $this->nombre = $nombre;
        $this->modalidad = $modalidad;
        $this->duracion = $duracion;
        $this->descripcion = $descripcion;
        $this->costo = $costo;
        $this->cupo = $cupo;
        $this->estado = $estado;
        $this->img = $img;
        $this->id_categoria = $id_categoria;
        $this->id_curso = $id_curso;
    }

    public function getId(): ?int { return $this->id_curso; }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getModalidad(): string
    {
        return $this->modalidad;
    }

    public function setModalidad(string $modalidad): void
    {
        $this->modalidad = $modalidad;
    }

    public function getDuracion(): string
    {
        return $this->duracion;
    }

    public function setDuracion(string $duracion): void
    {
        $this->duracion = $duracion;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getCosto(): float
    {
        return $this->costo;
    }

    public function setCosto(float $costo): void
    {
        $this->costo = $costo;
    }

    public function getCupo(): int
    {
        return $this->cupo;
    }

    public function setCupo(int $cupo): void
    {
        $this->cupo = $cupo;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    public function getImg(): string
    {
        return $this->img;
    }

    public function setImg(string $img): void
    {
        $this->img = $img;
    }

    public function getIdCategoria(): int
    {
        return $this->id_categoria;
    }

    public function setIdCategoria(int $id_categoria): void
    {
        $this->id_categoria = $id_categoria;
    }

    // INSERTAR REGISTRO
    public function crear(PDO $pdo): bool
    {
        if ($this->id_curso !== null) {
            throw new LogicException("El registro ya tiene un identificador.");
        }

        $sql = "INSERT INTO `curso` (`nombre`, `modalidad`, `duracion`, `descripcion`, `costo`, `cupo`, `estado`, `img`, `id_categoria`)
                VALUES (:nombre, :modalidad, :duracion, :descripcion, :costo, :cupo, :estado, :img, :id_categoria)";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':nombre' => $this->nombre,
            ':modalidad' => $this->modalidad,
            ':duracion' => $this->duracion,
            ':descripcion' => $this->descripcion,
            ':costo' => $this->costo,
            ':cupo' => $this->cupo,
            ':estado' => $this->estado,
            ':img' => $this->img,
            ':id_categoria' => $this->id_categoria,
        ]);

        $this->id_curso = (int) $pdo->lastInsertId();
        return true;
    }

    // MODIFICAR REGISTRO
    public function modificar(PDO $pdo): bool
    {
        $this->exigirId();
        $sql = "UPDATE `curso`
                SET `nombre` = :nombre, `modalidad` = :modalidad, `duracion` = :duracion, `descripcion` = :descripcion, `costo` = :costo, `cupo` = :cupo, `estado` = :estado, `img` = :img, `id_categoria` = :id_categoria
                WHERE `id_curso` = :id";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':nombre' => $this->nombre,
            ':modalidad' => $this->modalidad,
            ':duracion' => $this->duracion,
            ':descripcion' => $this->descripcion,
            ':costo' => $this->costo,
            ':cupo' => $this->cupo,
            ':estado' => $this->estado,
            ':img' => $this->img,
            ':id_categoria' => $this->id_categoria,
            ':id' => $this->id_curso,
        ]);
        return $consulta->rowCount() > 0;
    }

    // BAJA LÓGICA: CONSERVA EL REGISTRO
    public function darDeBaja(PDO $pdo): bool
    {
        return $this->cambiarEstado($pdo, 'Inactivo');
    }

    // VOLVER A HABILITAR EL REGISTRO
    public function reactivar(PDO $pdo): bool
    {
        return $this->cambiarEstado($pdo, 'Activo');
    }

    private function cambiarEstado(PDO $pdo, string $estado): bool
    {
        $this->exigirId();
        $sql = "UPDATE `curso` SET `estado` = :estado WHERE `id_curso` = :id";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':estado' => $estado,
            ':id' => $this->id_curso,
        ]);
        if ($consulta->rowCount() > 0) {
            $this->estado = $estado;
            return true;
        }
        return false;
    }

    private function exigirId(): void
    {
        if ($this->id_curso === null) {
            throw new LogicException("Primero debés crear o cargar el registro con su ID.");
        }
    }
}
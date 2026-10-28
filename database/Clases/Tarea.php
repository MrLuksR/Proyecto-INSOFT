<?php
require_once __DIR__ . '/Gestionable.php';

class Tarea implements Gestionable
{
    private ?int $id_tarea;
    private string $titulo;
    private ?string $descripcion;
    private int $id_clase;
    private string $fecha_tarea;
    private ?string $fecha_limite;
    private string $estado;

    public function __construct(
        string $titulo,
        ?string $descripcion,
        int $id_clase,
        string $fecha_tarea,
        ?string $fecha_limite,
        string $estado = 'Activo',
        ?int $id_tarea = null
    ) {
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->id_clase = $id_clase;
        $this->fecha_tarea = $fecha_tarea;
        $this->fecha_limite = $fecha_limite;
        $this->estado = $estado;
        $this->id_tarea = $id_tarea;
    }

    public function getId(): ?int { return $this->id_tarea; }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getIdClase(): int
    {
        return $this->id_clase;
    }

    public function setIdClase(int $id_clase): void
    {
        $this->id_clase = $id_clase;
    }

    public function getFechaTarea(): string
    {
        return $this->fecha_tarea;
    }

    public function setFechaTarea(string $fecha_tarea): void
    {
        $this->fecha_tarea = $fecha_tarea;
    }

    public function getFechaLimite(): ?string
    {
        return $this->fecha_limite;
    }

    public function setFechaLimite(?string $fecha_limite): void
    {
        $this->fecha_limite = $fecha_limite;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }

    // INSERTAR REGISTRO
    public function crear(PDO $pdo): bool
    {
        if ($this->id_tarea !== null) {
            throw new LogicException("El registro ya tiene un identificador.");
        }

        $sql = "INSERT INTO `tareas` (`titulo`, `descripcion`, `id_clase`, `fecha_tarea`, `fecha_limite`, `estado`)
                VALUES (:titulo, :descripcion, :id_clase, :fecha_tarea, :fecha_limite, :estado)";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':titulo' => $this->titulo,
            ':descripcion' => $this->descripcion,
            ':id_clase' => $this->id_clase,
            ':fecha_tarea' => $this->fecha_tarea,
            ':fecha_limite' => $this->fecha_limite,
            ':estado' => $this->estado,
        ]);

        $this->id_tarea = (int) $pdo->lastInsertId();
        return true;
    }

    // MODIFICAR REGISTRO
    public function modificar(PDO $pdo): bool
    {
        $this->exigirId();
        $sql = "UPDATE `tareas`
                SET `titulo` = :titulo, `descripcion` = :descripcion, `id_clase` = :id_clase, `fecha_tarea` = :fecha_tarea, `fecha_limite` = :fecha_limite
                WHERE `id_tarea` = :id";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':titulo' => $this->titulo,
            ':descripcion' => $this->descripcion,
            ':id_clase' => $this->id_clase,
            ':fecha_tarea' => $this->fecha_tarea,
            ':fecha_limite' => $this->fecha_limite,
            ':id' => $this->id_tarea,
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
        $sql = "UPDATE `tareas` SET `estado` = :estado WHERE `id_tarea` = :id";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':estado' => $estado,
            ':id' => $this->id_tarea,
        ]);
        if ($consulta->rowCount() > 0) {
            $this->estado = $estado;
            return true;
        }
        return false;
    }

    private function exigirId(): void
    {
        if ($this->id_tarea === null) {
            throw new LogicException("Primero debés crear o cargar el registro con su ID.");
        }
    }
}

<?php
require_once __DIR__ . '/Gestionable.php';

class Galeria implements Gestionable
{
    private ?int $id_imagen;
    private string $titulo;
    private string $fecha;
    private string $imagen;
    private string $nombre_usuario_admin;
    private string $estado;

    public function __construct(
        string $titulo,
        string $fecha,
        string $imagen,
        string $nombre_usuario_admin,
        string $estado = 'Activo',
        ?int $id_imagen = null
    ) {
        $this->titulo = $titulo;
        $this->fecha = $fecha;
        $this->imagen = $imagen;
        $this->nombre_usuario_admin = $nombre_usuario_admin;
        $this->estado = $estado;
        $this->id_imagen = $id_imagen;
    }

    public function getId(): ?int { return $this->id_imagen; }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function setFecha(string $fecha): void
    {
        $this->fecha = $fecha;
    }

    public function getImagen(): string
    {
        return $this->imagen;
    }

    public function setImagen(string $imagen): void
    {
        $this->imagen = $imagen;
    }

    public function getNombreUsuarioAdmin(): string
    {
        return $this->nombre_usuario_admin;
    }

    public function setNombreUsuarioAdmin(string $nombre_usuario_admin): void
    {
        $this->nombre_usuario_admin = $nombre_usuario_admin;
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
        if ($this->id_imagen !== null) {
            throw new LogicException("El registro ya tiene un identificador.");
        }

        $sql = "INSERT INTO `galeria` (`titulo`, `fecha`, `imagen`, `nombre_usuario_admin`, `estado`)
                VALUES (:titulo, :fecha, :imagen, :nombre_usuario_admin, :estado)";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':titulo' => $this->titulo,
            ':fecha' => $this->fecha,
            ':imagen' => $this->imagen,
            ':nombre_usuario_admin' => $this->nombre_usuario_admin,
            ':estado' => $this->estado,
        ]);

        $this->id_imagen = (int) $pdo->lastInsertId();
        return true;
    }

    // MODIFICAR REGISTRO
    public function modificar(PDO $pdo): bool
    {
        $this->exigirId();
        $sql = "UPDATE `galeria`
                SET `titulo` = :titulo, `fecha` = :fecha, `imagen` = :imagen, `nombre_usuario_admin` = :nombre_usuario_admin
                WHERE `id_imagen` = :id";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':titulo' => $this->titulo,
            ':fecha' => $this->fecha,
            ':imagen' => $this->imagen,
            ':nombre_usuario_admin' => $this->nombre_usuario_admin,
            ':id' => $this->id_imagen,
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
        $sql = "UPDATE `galeria` SET `estado` = :estado WHERE `id_imagen` = :id";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':estado' => $estado,
            ':id' => $this->id_imagen,
        ]);
        if ($consulta->rowCount() > 0) {
            $this->estado = $estado;
            return true;
        }
        return false;
    }

    private function exigirId(): void
    {
        if ($this->id_imagen === null) {
            throw new LogicException("Primero debés crear o cargar el registro con su ID.");
        }
    }
}

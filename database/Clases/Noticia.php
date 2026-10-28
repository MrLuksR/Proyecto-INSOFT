<?php
require_once __DIR__ . '/Gestionable.php';

class Noticia implements Gestionable
{
    private ?int $id_noticia;
    private string $fecha;
    private string $titulo;
    private string $contenido;
    private string $imagen;
    private string $enlace;
    private string $nombre_usuario_admin;
    private string $estado;

    public function __construct(
        string $fecha,
        string $titulo,
        string $contenido,
        string $imagen,
        string $enlace,
        string $nombre_usuario_admin,
        string $estado = 'Activo',
        ?int $id_noticia = null
    ) {
        $this->fecha = $fecha;
        $this->titulo = $titulo;
        $this->contenido = $contenido;
        $this->imagen = $imagen;
        $this->enlace = $enlace;
        $this->nombre_usuario_admin = $nombre_usuario_admin;
        $this->estado = $estado;
        $this->id_noticia = $id_noticia;
    }

    public function getId(): ?int { return $this->id_noticia; }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function setFecha(string $fecha): void
    {
        $this->fecha = $fecha;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function getContenido(): string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): void
    {
        $this->contenido = $contenido;
    }

    public function getImagen(): string
    {
        return $this->imagen;
    }

    public function setImagen(string $imagen): void
    {
        $this->imagen = $imagen;
    }

    public function getEnlace(): string
    {
        return $this->enlace;
    }

    public function setEnlace(string $enlace): void
    {
        $this->enlace = $enlace;
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
        if ($this->id_noticia !== null) {
            throw new LogicException("El registro ya tiene un identificador.");
        }

        $sql = "INSERT INTO `noticias` (`fecha`, `titulo`, `contenido`, `imagen`, `enlace`, `nombre_usuario_admin`, `estado`)
                VALUES (:fecha, :titulo, :contenido, :imagen, :enlace, :nombre_usuario_admin, :estado)";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':fecha' => $this->fecha,
            ':titulo' => $this->titulo,
            ':contenido' => $this->contenido,
            ':imagen' => $this->imagen,
            ':enlace' => $this->enlace,
            ':nombre_usuario_admin' => $this->nombre_usuario_admin,
            ':estado' => $this->estado,
        ]);

        $this->id_noticia = (int) $pdo->lastInsertId();
        return true;
    }

    // MODIFICAR REGISTRO
    public function modificar(PDO $pdo): bool
    {
        $this->exigirId();
        $sql = "UPDATE `noticias`
                SET `fecha` = :fecha, `titulo` = :titulo, `contenido` = :contenido, `imagen` = :imagen, `enlace` = :enlace, `nombre_usuario_admin` = :nombre_usuario_admin
                WHERE `id_noticia` = :id";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':fecha' => $this->fecha,
            ':titulo' => $this->titulo,
            ':contenido' => $this->contenido,
            ':imagen' => $this->imagen,
            ':enlace' => $this->enlace,
            ':nombre_usuario_admin' => $this->nombre_usuario_admin,
            ':id' => $this->id_noticia,
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
        $sql = "UPDATE `noticias` SET `estado` = :estado WHERE `id_noticia` = :id";
        $consulta = $pdo->prepare($sql);
        $consulta->execute([
            ':estado' => $estado,
            ':id' => $this->id_noticia,
        ]);
        if ($consulta->rowCount() > 0) {
            $this->estado = $estado;
            return true;
        }
        return false;
    }

    private function exigirId(): void
    {
        if ($this->id_noticia === null) {
            throw new LogicException("Primero debés crear o cargar el registro con su ID.");
        }
    }
}

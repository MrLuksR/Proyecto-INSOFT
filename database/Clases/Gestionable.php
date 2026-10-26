<?php
interface Gestionable
{
    public function crear(PDO $pdo): bool;

    public function modificar(PDO $pdo): bool;

    public function darDeBaja(PDO $pdo): bool;

    public function reactivar(PDO $pdo): bool;
}
<?php

require_once __DIR__ . '/../config.php';

class AuthModel
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    public function login($usuario, $contrasenaIngresada)
    {
        $sql = "SELECT 
    u.id_ AS id_real,
    u.usuario,
    u.contrasena,
    u.rol,
    u.estado,
    u.fecha_creacion,
    u.nombre,
    u.correo,
    u.telefono,
    u.dni,
    u.saldo
    FROM Usuarios  u
    WHERE u.usuario = :usuario
    LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario' => $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $hash = $user['contrasena'];
        $isHashed = preg_match('/^\$2y\$/', $hash);

        if (
            (!$isHashed && $hash === $contrasenaIngresada) ||
            ($isHashed && password_verify($contrasenaIngresada, $hash))
        ) {
            return $user;
        }

        return false;
    }
}

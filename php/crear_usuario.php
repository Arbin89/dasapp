<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';
require_once 'crypto.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $rol = trim($_POST['rol'] ?? 'arquitecto');

    if (empty($nombre) || empty($email) || empty($password)) {
        header('Location: ../admin_usuario_nuevo.php?error=1');
        exit;
    }

    $passwordEncriptada = encriptar($password);

    try {
        $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nombre, $email, $passwordEncriptada, $rol]);

        header('Location: ../admin_usuarios.php?creado=1');
        exit;
    } catch (PDOException $e) {
        header('Location: ../admin_usuario_nuevo.php?error=1');
        exit;
    }

} else {
    header('Location: ../admin_usuarios.php');
    exit;
}
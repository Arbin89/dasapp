<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';
require_once 'crypto.php';

$id = $_POST['id'] ?? null;
if (!$id) {
    header('Location: ../admin_usuarios.php');
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$rol = trim($_POST['rol'] ?? 'arquitecto');
$password = trim($_POST['password'] ?? '');

if ($password !== '') {
    $passwordEncriptada = encriptar($password);
    $stmt = $pdo->prepare('UPDATE usuarios SET nombre = ?, email = ?, rol = ?, password = ? WHERE id = ?');
    $stmt->execute([$nombre, $email, $rol, $passwordEncriptada, $id]);
} else {
    $stmt = $pdo->prepare('UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?');
    $stmt->execute([$nombre, $email, $rol, $id]);
}

header('Location: ../admin_usuario_detalle.php?id=' . $id . '&actualizado=1');
exit;
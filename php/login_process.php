<?php
session_start();
require_once 'config.php';
require_once 'crypto.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        header('Location: ../index.php?error=vacio');
        exit;
    }

    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        $passwordGuardada = desencriptar($usuario['password']);

        if ($password === $passwordGuardada) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];


            header('Location: ../dashboard.php');
            exit;
        } else {
            header('Location: ../index.php?error=credenciales');
            exit;
        }
    } else {
        header('Location: ../index.php?error=credenciales');
        exit;
    }

} else {
    header('Location: ../index.php');
    exit;
}
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $empresa_id = $_POST['empresa_id'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado = 'En proceso';
    $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');

    if (!$empresa_id || empty($nombre) || empty($fecha_inicio)) {
        header('Location: ../proyecto_nuevo.php?empresa_id=' . $empresa_id . '&error=1');
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO proyectos (empresa_id, nombre, descripcion, estado, fecha_inicio) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$empresa_id, $nombre, $descripcion, $estado, $fecha_inicio]);

    header('Location: ../proyectos.php?empresa_id=' . $empresa_id . '&creado=1');
    exit;

} else {
    header('Location: ../dashboard.php');
    exit;
}
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$proyecto_id = $_POST['proyecto_id'] ?? null;
$nombre = trim($_POST['nombre_categoria'] ?? '');

if (!$proyecto_id || empty($nombre)) {
    header('Location: ../proyecto_planos.php?proyecto_id=' . $proyecto_id);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO categorias_planos (proyecto_id, nombre) VALUES (?, ?)');
$stmt->execute([$proyecto_id, $nombre]);
$nuevaId = $pdo->lastInsertId();

header('Location: ../proyecto_planos.php?proyecto_id=' . $proyecto_id . '&categoria_id=' . $nuevaId);
exit;
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_POST['id'] ?? null;
$proyecto_id = $_POST['proyecto_id'] ?? null;
$categoria_id = $_POST['categoria_id'] ?? null;
$pestana = $_POST['pestana'] ?? '';

if (!$id || !$proyecto_id || !$categoria_id) {
    header('Location: ../dashboard.php');
    exit;
}

// La categoría de destino debe existir y ser de este proyecto
$stmt = $pdo->prepare('SELECT id FROM categorias_planos WHERE id = ? AND proyecto_id = ?');
$stmt->execute([$categoria_id, $proyecto_id]);

if ($stmt->fetch()) {
    $stmt = $pdo->prepare('UPDATE planos SET categoria_id = ? WHERE id = ? AND proyecto_id = ?');
    $stmt->execute([$categoria_id, $id, $proyecto_id]);
}

// Te quedas en la pestaña donde estabas, para seguir moviendo planos
$destino = '../proyecto_planos.php?proyecto_id=' . $proyecto_id;
if ($pestana !== '') {
    $destino .= '&categoria_id=' . urlencode($pestana);
}

header('Location: ' . $destino . '&movido=1');
exit;
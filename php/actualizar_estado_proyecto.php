<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_GET['id'] ?? null;
$accion = $_GET['accion'] ?? null;

if (!$id || !$accion) {
    header('Location: ../dashboard.php');
    exit;
}

if ($accion === 'completar') {
    $stmt = $pdo->prepare('UPDATE proyectos SET estado = ?, fecha_completado = CURDATE() WHERE id = ?');
    $stmt->execute(['Completado', $id]);
} elseif ($accion === 'descompletar') {
    $stmt = $pdo->prepare('UPDATE proyectos SET estado = ?, fecha_completado = NULL WHERE id = ?');
    $stmt->execute(['En proceso', $id]);
}

header('Location: ../proyecto_detalle.php?id=' . $id);
exit;
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_GET['id'] ?? null;
$proyecto_id = $_GET['proyecto_id'] ?? null;
$accion = $_GET['accion'] ?? 'completar';

if (!$id || !$proyecto_id) {
    header('Location: ../dashboard.php');
    exit;
}

if ($accion === 'completar') {
    $stmt = $pdo->prepare('UPDATE tareas SET estado = ?, fecha_finalizacion = COALESCE(fecha_finalizacion, NOW()) WHERE id = ?');
    $stmt->execute(['Finalizado', $id]);
} else {
    $stmt = $pdo->prepare('UPDATE tareas SET estado = ?, fecha_finalizacion = NULL WHERE id = ?');
    $stmt->execute(['Pendiente', $id]);
}

header('Location: ../proyecto_tarea_detalle.php?id=' . $id . '&proyecto_id=' . $proyecto_id);
exit;
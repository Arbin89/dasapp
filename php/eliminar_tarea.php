<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_GET['id'] ?? null;
$proyecto_id = $_GET['proyecto_id'] ?? null;

if (!$id || !$proyecto_id) {
    header('Location: ../dashboard.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM tarea_contratistas WHERE tarea_id = ?');
$stmt->execute([$id]);

$stmt = $pdo->prepare('DELETE FROM tareas WHERE id = ?');
$stmt->execute([$id]);

header('Location: ../proyecto_tablero.php?proyecto_id=' . $proyecto_id);
exit;
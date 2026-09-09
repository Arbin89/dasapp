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

$stmt = $pdo->prepare('SELECT ruta_archivo FROM planos WHERE id = ?');
$stmt->execute([$id]);
$plano = $stmt->fetch();

if ($plano && file_exists('../' . $plano['ruta_archivo'])) {
    unlink('../' . $plano['ruta_archivo']);
}

$stmt = $pdo->prepare('DELETE FROM planos WHERE id = ?');
$stmt->execute([$id]);

header('Location: ../proyecto_planos.php?proyecto_id=' . $proyecto_id);
exit;
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

$stmt = $pdo->prepare('SELECT ruta_archivo FROM fotos WHERE id = ?');
$stmt->execute([$id]);
$foto = $stmt->fetch();

if ($foto && file_exists('../' . $foto['ruta_archivo'])) {
    unlink('../' . $foto['ruta_archivo']);
}

$stmt = $pdo->prepare('DELETE FROM fotos WHERE id = ?');
$stmt->execute([$id]);

header('Location: ../proyecto_fotos.php?proyecto_id=' . $proyecto_id);
exit;
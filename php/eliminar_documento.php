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

$stmt = $pdo->prepare('SELECT ruta_archivo FROM documentos WHERE id = ?');
$stmt->execute([$id]);
$documento = $stmt->fetch();

if ($documento && file_exists('../' . $documento['ruta_archivo'])) {
    unlink('../' . $documento['ruta_archivo']);
}

$stmt = $pdo->prepare('DELETE FROM documentos WHERE id = ?');
$stmt->execute([$id]);

header('Location: ../proyecto_documentos.php?proyecto_id=' . $proyecto_id);
exit;
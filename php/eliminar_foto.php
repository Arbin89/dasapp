<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_GET['id'] ?? null;
$proyecto_id = $_GET['proyecto_id'] ?? null;
$album_id = $_GET['album_id'] ?? '';

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

$destino = '../proyecto_fotos.php?proyecto_id=' . $proyecto_id;
if ($album_id !== '') {
    $destino .= '&album_id=' . urlencode($album_id);
}

header('Location: ' . $destino);
exit;
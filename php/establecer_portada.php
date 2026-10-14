<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$album_id = $_GET['album_id'] ?? null;
$foto_id = $_GET['foto_id'] ?? null;
$proyecto_id = $_GET['proyecto_id'] ?? null;

if (!$album_id || !$foto_id || !$proyecto_id) {
    header('Location: ../dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM fotos WHERE id = ? AND album_id = ?');
$stmt->execute([$foto_id, $album_id]);

if ($stmt->fetch()) {
    $stmt = $pdo->prepare('UPDATE albumes_fotos SET portada_foto_id = ? WHERE id = ? AND proyecto_id = ?');
    $stmt->execute([$foto_id, $album_id, $proyecto_id]);
}

header('Location: ../proyecto_fotos.php?proyecto_id=' . $proyecto_id . '&album_id=' . urlencode($album_id) . '&portada=1');
exit;
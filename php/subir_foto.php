<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$proyecto_id = $_POST['proyecto_id'] ?? null;
$album_id = $_POST['album_id'] ?? '';
$album_id = ($album_id === '') ? null : $album_id;
$pestana = ($album_id === null) ? 'sin' : $album_id;

$destino = '../proyecto_fotos.php?proyecto_id=' . $proyecto_id . '&album_id=' . urlencode($pestana);

if (!$proyecto_id || !isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ' . $destino . '&error=1');
    exit;
}

$archivo = $_FILES['foto'];
$tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

if (!in_array($archivo['type'], $tiposPermitidos)) {
    header('Location: ' . $destino . '&error=1');
    exit;
}

$nombreOriginal = basename($archivo['name']);
$extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
$nombreUnico = uniqid('foto_') . '.' . $extension;
$rutaDestino = '../uploads/fotos/' . $nombreUnico;

if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
    $rutaGuardada = 'uploads/fotos/' . $nombreUnico;

    $stmt = $pdo->prepare('INSERT INTO fotos (proyecto_id, album_id, nombre_archivo, ruta_archivo) VALUES (?, ?, ?, ?)');
    $stmt->execute([$proyecto_id, $album_id, $nombreOriginal, $rutaGuardada]);

    header('Location: ' . $destino . '&subido=1');
    exit;
} else {
    header('Location: ' . $destino . '&error=1');
    exit;
}
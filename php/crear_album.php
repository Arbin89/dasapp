<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$proyecto_id = $_POST['proyecto_id'] ?? null;
$nombre = trim($_POST['nombre_album'] ?? '');

if (!$proyecto_id || $nombre === '') {
    header('Location: ../proyecto_fotos.php?proyecto_id=' . $proyecto_id);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO albumes_fotos (proyecto_id, nombre) VALUES (?, ?)');
$stmt->execute([$proyecto_id, $nombre]);
$nuevoId = $pdo->lastInsertId();

header('Location: ../proyecto_fotos.php?proyecto_id=' . $proyecto_id . '&album_id=' . $nuevoId);
exit;
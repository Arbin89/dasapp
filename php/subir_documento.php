<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$proyecto_id = $_POST['proyecto_id'] ?? null;
$categoria_id = $_POST['categoria_id'] ?? null;

if (!$proyecto_id || !$categoria_id || !isset($_FILES['documento']) || $_FILES['documento']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../proyecto_documentos.php?proyecto_id=' . $proyecto_id . '&error=1');
    exit;
}

$archivo = $_FILES['documento'];
$nombreOriginal = basename($archivo['name']);
$extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
$nombreUnico = uniqid('doc_') . '.' . $extension;
$rutaDestino = '../uploads/documentos/' . $nombreUnico;

if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
    $rutaGuardada = 'uploads/documentos/' . $nombreUnico;

    $stmt = $pdo->prepare('INSERT INTO documentos (proyecto_id, categoria_id, nombre_archivo, ruta_archivo) VALUES (?, ?, ?, ?)');
    $stmt->execute([$proyecto_id, $categoria_id, $nombreOriginal, $rutaGuardada]);

    header('Location: ../proyecto_documentos.php?proyecto_id=' . $proyecto_id . '&subido=1');
    exit;
} else {
    header('Location: ../proyecto_documentos.php?proyecto_id=' . $proyecto_id . '&error=1');
    exit;
}
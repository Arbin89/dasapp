<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$proyecto_id = $_POST['proyecto_id'] ?? null;
$categoria_id = $_POST['categoria_id'] ?? '';
$categoria_id = ($categoria_id === '') ? null : $categoria_id;
$pestana = ($categoria_id === null) ? 'sin' : $categoria_id;

$destino = '../proyecto_planos.php?proyecto_id=' . $proyecto_id . '&categoria_id=' . urlencode($pestana);

if (!$proyecto_id || !isset($_FILES['plano']) || $_FILES['plano']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ' . $destino . '&error=1');
    exit;
}

$archivo = $_FILES['plano'];

if ($archivo['type'] !== 'application/pdf') {
    header('Location: ' . $destino . '&error=1');
    exit;
}

$nombreOriginal = basename($archivo['name']);
$nombreUnico = uniqid('plano_') . '.pdf';
$rutaDestino = '../uploads/planos/' . $nombreUnico;

if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
    $rutaGuardada = 'uploads/planos/' . $nombreUnico;

    $stmt = $pdo->prepare('INSERT INTO planos (proyecto_id, categoria_id, nombre_archivo, ruta_archivo) VALUES (?, ?, ?, ?)');
    $stmt->execute([$proyecto_id, $categoria_id, $nombreOriginal, $rutaGuardada]);

    header('Location: ' . $destino . '&subido=1');
    exit;
} else {
    header('Location: ' . $destino . '&error=1');
    exit;
}
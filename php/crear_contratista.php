<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$cedula = trim($_POST['cedula'] ?? '');
$proyecto_id = $_POST['proyecto_id'] ?? null;

if (empty($nombre)) {
    header('Location: ../admin_contratistas.php');
    exit;
}

$rutaFoto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nombreUnico = uniqid('contratista_') . '.' . $extension;
    if (move_uploaded_file($_FILES['foto']['tmp_name'], '../uploads/contratistas/' . $nombreUnico)) {
        $rutaFoto = 'uploads/contratistas/' . $nombreUnico;
    }
}

$stmt = $pdo->prepare('INSERT INTO contratistas (nombre, telefono, cedula, foto) VALUES (?, ?, ?, ?)');
$stmt->execute([$nombre, $telefono, $cedula, $rutaFoto]);
$nuevoId = $pdo->lastInsertId();

if ($proyecto_id) {
    $stmt = $pdo->prepare('INSERT INTO proyecto_contratistas (proyecto_id, contratista_id) VALUES (?, ?)');
    $stmt->execute([$proyecto_id, $nuevoId]);
    header('Location: ../proyecto_contratistas.php?proyecto_id=' . $proyecto_id . '&creado=1');
    exit;
}

header('Location: ../admin_contratistas.php?creado=1');
exit;
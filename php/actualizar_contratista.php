<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_POST['id'] ?? null;
if (!$id) {
    header('Location: ../admin_contratistas.php');
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$cedula = trim($_POST['cedula'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$latitud = $_POST['latitud'] ?: null;
$longitud = $_POST['longitud'] ?: null;
$ref1_nombre = trim($_POST['ref1_nombre'] ?? '');
$ref1_telefono = trim($_POST['ref1_telefono'] ?? '');
$ref2_nombre = trim($_POST['ref2_nombre'] ?? '');
$ref2_telefono = trim($_POST['ref2_telefono'] ?? '');
$rutaFoto = $_POST['foto_actual'] ?? null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    if ($rutaFoto && file_exists('../' . $rutaFoto)) {
        unlink('../' . $rutaFoto);
    }
    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nombreUnico = uniqid('contratista_') . '.' . $extension;
    if (move_uploaded_file($_FILES['foto']['tmp_name'], '../uploads/contratistas/' . $nombreUnico)) {
        $rutaFoto = 'uploads/contratistas/' . $nombreUnico;
    }
}

$stmt = $pdo->prepare('UPDATE contratistas SET nombre = ?, telefono = ?, cedula = ?, direccion = ?, latitud = ?, longitud = ?, foto = ?, ref1_nombre = ?, ref1_telefono = ?, ref2_nombre = ?, ref2_telefono = ? WHERE id = ?');
$stmt->execute([$nombre, $telefono, $cedula, $direccion, $latitud, $longitud, $rutaFoto, $ref1_nombre, $ref1_telefono, $ref2_nombre, $ref2_telefono, $id]);

header('Location: ../admin_contratista_detalle.php?id=' . $id . '&actualizado=1');
exit;
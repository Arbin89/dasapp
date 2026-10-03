<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$nombre = trim($_POST['nombre'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$latitud = $_POST['latitud'] ?: null;
$longitud = $_POST['longitud'] ?: null;
$rnc_cedula = trim($_POST['rnc_cedula'] ?? '');
$telefono1 = trim($_POST['telefono1'] ?? '');
$telefono2 = trim($_POST['telefono2'] ?? '');
$rubro = trim($_POST['rubro'] ?? '');
$referencia_nombre = trim($_POST['referencia_nombre'] ?? '');
$referencia_telefono = trim($_POST['referencia_telefono'] ?? '');
$proyecto_id = $_POST['proyecto_id'] ?? null;

if (empty($nombre)) {
    header('Location: ../admin_proveedores.php');
    exit;
}

$stmt = $pdo->prepare('INSERT INTO proveedores (nombre, direccion, rnc_cedula, telefono1, telefono2, rubro, referencia_nombre, referencia_telefono, latitud, longitud) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([$nombre, $direccion, $rnc_cedula, $telefono1, $telefono2, $rubro, $referencia_nombre, $referencia_telefono, $latitud, $longitud]);
$nuevoId = $pdo->lastInsertId();

if ($proyecto_id) {
    $stmt = $pdo->prepare('INSERT INTO proyecto_proveedores (proyecto_id, proveedor_id) VALUES (?, ?)');
    $stmt->execute([$proyecto_id, $nuevoId]);
    header('Location: ../proyecto_proveedores.php?proyecto_id=' . $proyecto_id . '&creado=1');
    exit;
}

header('Location: ../admin_proveedores.php?creado=1');
exit;
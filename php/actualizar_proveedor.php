<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_POST['id'] ?? null;
if (!$id) {
    header('Location: ../admin_proveedores.php');
    exit;
}

$volver = $_POST['volver'] ?? 'admin_proveedores.php';

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

$stmt = $pdo->prepare('UPDATE proveedores SET nombre = ?, direccion = ?, rnc_cedula = ?, telefono1 = ?, telefono2 = ?, rubro = ?, referencia_nombre = ?, referencia_telefono = ?, latitud = ?, longitud = ? WHERE id = ?');
$stmt->execute([$nombre, $direccion, $rnc_cedula, $telefono1, $telefono2, $rubro, $referencia_nombre, $referencia_telefono, $latitud, $longitud, $id]);

header('Location: ../admin_proveedor_detalle.php?id=' . $id . '&actualizado=1&volver=' . urlencode($volver));
exit;
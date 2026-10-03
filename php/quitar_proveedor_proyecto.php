<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$proveedor_id = $_GET['proveedor_id'] ?? null;
$proyecto_id = $_GET['proyecto_id'] ?? null;

if (!$proveedor_id || !$proyecto_id) {
    header('Location: ../dashboard.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM proyecto_proveedores WHERE proveedor_id = ? AND proyecto_id = ?');
$stmt->execute([$proveedor_id, $proyecto_id]);

header('Location: ../proyecto_proveedores.php?proyecto_id=' . $proyecto_id);
exit;
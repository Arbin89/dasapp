<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$proyecto_id = $_POST['proyecto_id'] ?? null;
$proveedor_id = $_POST['proveedor_id'] ?? null;

if (!$proyecto_id || !$proveedor_id) {
    header('Location: ../dashboard.php');
    exit;
}

$stmt = $pdo->prepare('INSERT INTO proyecto_proveedores (proyecto_id, proveedor_id) VALUES (?, ?)');
$stmt->execute([$proyecto_id, $proveedor_id]);

header('Location: ../proyecto_proveedores.php?proyecto_id=' . $proyecto_id . '&agregado=1');
exit;
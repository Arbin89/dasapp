<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: ../admin_proveedores.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM proyecto_proveedores WHERE proveedor_id = ?');
$stmt->execute([$id]);

$stmt = $pdo->prepare('DELETE FROM proveedores WHERE id = ?');
$stmt->execute([$id]);

header('Location: ../admin_proveedores.php?eliminado=1');
exit;
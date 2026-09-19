<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$contratista_id = $_GET['contratista_id'] ?? null;
$proyecto_id = $_GET['proyecto_id'] ?? null;

if (!$contratista_id || !$proyecto_id) {
    header('Location: ../dashboard.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM proyecto_contratistas WHERE contratista_id = ? AND proyecto_id = ?');
$stmt->execute([$contratista_id, $proyecto_id]);

header('Location: ../proyecto_contratistas.php?proyecto_id=' . $proyecto_id);
exit;
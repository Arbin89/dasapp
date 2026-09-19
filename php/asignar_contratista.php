<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$proyecto_id = $_POST['proyecto_id'] ?? null;
$contratista_id = $_POST['contratista_id'] ?? null;

if (!$proyecto_id || !$contratista_id) {
    header('Location: ../dashboard.php');
    exit;
}

$stmt = $pdo->prepare('INSERT INTO proyecto_contratistas (proyecto_id, contratista_id) VALUES (?, ?)');
$stmt->execute([$proyecto_id, $contratista_id]);

header('Location: ../proyecto_contratistas.php?proyecto_id=' . $proyecto_id . '&agregado=1');
exit;
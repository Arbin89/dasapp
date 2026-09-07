<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_GET ['id'] ?? null;
$empresa_id = $_GET ['empresa_id'] ?? null;

if (!$id || !$empresa_id) {
    header('Location: ../dashboard.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM proyectos WHERE id = ?');
$stmt->execute([$id]);

header('Location: ../proyectos.php?empresa_id=' . $empresa_id);
exit;
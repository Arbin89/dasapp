<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: ../admin_usuarios.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
$stmt->execute([$id]);

header('Location: ../admin_usuarios.php?eliminado=1');
exit;
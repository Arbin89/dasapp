<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: ../admin_contratistas.php');
    exit;
}

$stmt = $pdo->prepare('SELECT foto FROM contratistas WHERE id = ?');
$stmt->execute([$id]);
$contratista = $stmt->fetch();

if ($contratista && $contratista['foto'] && file_exists('../' . $contratista['foto'])) {
    unlink('../' . $contratista['foto']);
}

$stmt = $pdo->prepare('DELETE FROM proyecto_contratistas WHERE contratista_id = ?');
$stmt->execute([$id]);

$stmt = $pdo->prepare('DELETE FROM contratistas WHERE id = ?');
$stmt->execute([$id]);

header('Location: ../admin_contratistas.php?eliminado=1');
exit;
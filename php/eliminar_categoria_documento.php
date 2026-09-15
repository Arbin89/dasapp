<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_GET['id'] ?? null;
$proyecto_id = $_GET['proyecto_id'] ?? null;

if (!$id || !$proyecto_id) {
    header('Location: ../dashboard.php');
    exit;
}

// Borrar archivos físicos y registros de documentos de esta categoría
$stmt = $pdo->prepare('SELECT ruta_archivo FROM documentos WHERE categoria_id = ?');
$stmt->execute([$id]);
foreach ($stmt->fetchAll() as $doc) {
    if (file_exists('../' . $doc['ruta_archivo'])) {
        unlink('../' . $doc['ruta_archivo']);
    }
}
$stmt = $pdo->prepare('DELETE FROM documentos WHERE categoria_id = ?');
$stmt->execute([$id]);

// Ahora sí, borrar la categoría
$stmt = $pdo->prepare('DELETE FROM categorias_documentos WHERE id = ?');
$stmt->execute([$id]);

header('Location: ../proyecto_documentos.php?proyecto_id=' . $proyecto_id);
exit;
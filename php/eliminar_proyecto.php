<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_GET['id'] ?? null;
$empresa_id = $_GET['empresa_id'] ?? null;

if (!$id || !$empresa_id) {
    header('Location: ../dashboard.php');
    exit;
}

// 1. Borrar archivos físicos y registros de documentos
$stmt = $pdo->prepare('SELECT ruta_archivo FROM documentos WHERE proyecto_id = ?');
$stmt->execute([$id]);
foreach ($stmt->fetchAll() as $doc) {
    if (file_exists('../' . $doc['ruta_archivo'])) {
        unlink('../' . $doc['ruta_archivo']);
    }
}
$stmt = $pdo->prepare('DELETE FROM documentos WHERE proyecto_id = ?');
$stmt->execute([$id]);

// 2. Borrar las categorías de documentos de este proyecto
$stmt = $pdo->prepare('DELETE FROM categorias_documentos WHERE proyecto_id = ?');
$stmt->execute([$id]);

// 3. Borrar archivos físicos y registros de planos
$stmt = $pdo->prepare('SELECT ruta_archivo FROM planos WHERE proyecto_id = ?');
$stmt->execute([$id]);
foreach ($stmt->fetchAll() as $plano) {
    if (file_exists('../' . $plano['ruta_archivo'])) {
        unlink('../' . $plano['ruta_archivo']);
    }
}
$stmt = $pdo->prepare('DELETE FROM planos WHERE proyecto_id = ?');
$stmt->execute([$id]);

// Borrar archivos físicos y registros de fotos
$stmt = $pdo->prepare('SELECT ruta_archivo FROM fotos WHERE proyecto_id = ?');
$stmt->execute([$id]);
foreach ($stmt->fetchAll() as $foto) {
    if (file_exists('../' . $foto['ruta_archivo'])) {
        unlink('../' . $foto['ruta_archivo']);
    }
}
$stmt = $pdo->prepare('DELETE FROM fotos WHERE proyecto_id = ?');
$stmt->execute([$id]);

// Quitar las asignaciones de contratistas de este proyecto (no borra los contratistas en sí)
$stmt = $pdo->prepare('DELETE FROM proyecto_contratistas WHERE proyecto_id = ?');
$stmt->execute([$id]);

// 4. Finalmente, borrar el proyecto
$stmt = $pdo->prepare('DELETE FROM proyectos WHERE id = ?');
$stmt->execute([$id]);

header('Location: ../proyectos.php?empresa_id=' . $empresa_id . '&eliminado=1');
exit;
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

// Confirmar que el álbum existe y pertenece a este proyecto
$stmt = $pdo->prepare('SELECT id FROM albumes_fotos WHERE id = ? AND proyecto_id = ?');
$stmt->execute([$id, $proyecto_id]);

if ($stmt->fetch()) {
    // Borrar los archivos físicos y los registros de las fotos del álbum
    $stmt = $pdo->prepare('SELECT ruta_archivo FROM fotos WHERE album_id = ?');
    $stmt->execute([$id]);
    foreach ($stmt->fetchAll() as $foto) {
        if (file_exists('../' . $foto['ruta_archivo'])) {
            unlink('../' . $foto['ruta_archivo']);
        }
    }
    $stmt = $pdo->prepare('DELETE FROM fotos WHERE album_id = ?');
    $stmt->execute([$id]);

    // Ahora sí, borrar el álbum
    $stmt = $pdo->prepare('DELETE FROM albumes_fotos WHERE id = ? AND proyecto_id = ?');
    $stmt->execute([$id, $proyecto_id]);
}

header('Location: ../proyecto_fotos.php?proyecto_id=' . $proyecto_id . '&eliminado=1');
exit;
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$id = $_POST['id'] ?? null;
$proyecto_id = $_POST['proyecto_id'] ?? null;

if (!$id || !$proyecto_id) {
    header('Location: ../dashboard.php');
    exit;
}

$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$fecha_limite = trim($_POST['fecha_limite'] ?? '') ?: null;
$categoria = trim($_POST['categoria'] ?? '') ?: null;
$estado = $_POST['estado'] ?? 'Pendiente';
$prioridad = $_POST['prioridad'] ?? 'Media';
$contratistasSeleccionados = $_POST['contratistas'] ?? [];

if (!in_array($estado, ['Pendiente', 'En progreso', 'Finalizado'], true)) {
    $estado = 'Pendiente';
}
if (!in_array($prioridad, ['Alta', 'Media', 'Baja'], true)) {
    $prioridad = 'Media';
}

$fechaFin = ($estado === 'Finalizado') ? 'COALESCE(fecha_finalizacion, NOW())' : 'NULL';

$stmt = $pdo->prepare("UPDATE tareas SET titulo = ?, descripcion = ?, estado = ?, prioridad = ?, categoria = ?, fecha_limite = ?, fecha_finalizacion = $fechaFin WHERE id = ?");
$stmt->execute([$titulo, $descripcion, $estado, $prioridad, $categoria, $fecha_limite, $id]);

$stmt = $pdo->prepare('DELETE FROM tarea_contratistas WHERE tarea_id = ?');
$stmt->execute([$id]);

foreach ($contratistasSeleccionados as $contratistaId) {
    $stmt = $pdo->prepare('INSERT INTO tarea_contratistas (tarea_id, contratista_id) VALUES (?, ?)');
    $stmt->execute([$id, $contratistaId]);
}

header('Location: ../proyecto_tarea_detalle.php?id=' . $id . '&proyecto_id=' . $proyecto_id . '&actualizado=1');
exit;
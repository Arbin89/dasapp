<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
require_once 'config.php';

$proyecto_id = $_POST['proyecto_id'] ?? null;
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

if (!$proyecto_id || empty($titulo)) {
    header('Location: ../proyecto_tablero.php?proyecto_id=' . $proyecto_id);
    exit;
}

$fechaFin = ($estado === 'Finalizado') ? date('Y-m-d H:i:s') : null;

$stmt = $pdo->prepare('INSERT INTO tareas (proyecto_id, titulo, descripcion, estado, prioridad, categoria, fecha_limite, fecha_finalizacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([$proyecto_id, $titulo, $descripcion, $estado, $prioridad, $categoria, $fecha_limite, $fechaFin]);
$tareaId = $pdo->lastInsertId();

foreach ($contratistasSeleccionados as $contratistaId) {
    $stmt = $pdo->prepare('INSERT INTO tarea_contratistas (tarea_id, contratista_id) VALUES (?, ?)');
    $stmt->execute([$tareaId, $contratistaId]);
}

header('Location: ../proyecto_tablero.php?proyecto_id=' . $proyecto_id . '&creado=1');
exit;
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    exit;
}
require_once 'config.php';

$id = $_POST['id'] ?? null;
$estado = $_POST['estado'] ?? null;
$estadosValidos = ['Pendiente', 'En progreso', 'Finalizado'];

if (!$id || !in_array($estado, $estadosValidos, true)) {
    http_response_code(400);
    exit;
}

// Si pasa a Finalizado se guarda la fecha (conservando la anterior si ya la tenía); si sale de Finalizado se borra
$fechaFin = ($estado === 'Finalizado') ? 'COALESCE(fecha_finalizacion, NOW())' : 'NULL';

$stmt = $pdo->prepare("UPDATE tareas SET estado = ?, fecha_finalizacion = $fechaFin WHERE id = ?");
$stmt->execute([$estado, $id]);

echo 'ok';
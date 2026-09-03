<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
$proyecto_id = $_GET['proyecto_id'] ?? null;
if (!$proyecto_id) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sección</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="javascript:history.back()" class="volver-link">← Volver</a>
        <h1>Sección en construcción</h1>
    </div>
</body>
</html>
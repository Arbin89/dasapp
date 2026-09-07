<?php session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

?>
<DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>OFICINA</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="dashboard.php" class="volver-link">← Volver</a>
        <h1>Oficina</h1>
        <p>Esta sección está en construcción.</p>
    </div>
</body>
</html>
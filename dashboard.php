<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel</title>
</head>
<body style="font-family: Arial; padding: 40px;">
    <h1>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h1>
    <p>Rol: <?php echo htmlspecialchars($_SESSION['usuario_rol']); ?></p>
</body>
</html>
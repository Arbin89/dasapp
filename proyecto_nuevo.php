<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
$empresa_id = $_GET['empresa_id'] ?? null;
if (!$empresa_id) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo proyecto</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="proyectos.php?empresa_id=<?php echo htmlspecialchars($empresa_id); ?>" class="volver-link">← Volver a proyectos</a>
        <h1>Nuevo proyecto</h1>

        <form action="php/crear_proyecto.php" method="POST" class="form-proyecto">
            <input type="hidden" name="empresa_id" value="<?php echo htmlspecialchars($empresa_id); ?>">

            <label for="nombre">Nombre del proyecto</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4"></textarea>

            <label for="fecha_inicio">Fecha de inicio</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" required>

            <button type="submit">Crear proyecto</button>
        </form>
    </div>
</body>
</html>


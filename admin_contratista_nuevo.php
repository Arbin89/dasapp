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
    <title>Nuevo contratista</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="admin_contratistas.php" class="volver-link">← Volver a contratistas</a>
        <h1>Nuevo contratista</h1>

        <form action="php/crear_contratista.php" method="POST" enctype="multipart/form-data" class="form-proyecto">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="telefono">Teléfono</label>
            <input type="text" id="telefono" name="telefono">

            <label for="cedula">Cédula</label>
            <input type="text" id="cedula" name="cedula">

            <label for="foto">Foto</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="submit">Crear contratista</button>
        </form>
    </div>
</body>
</html>
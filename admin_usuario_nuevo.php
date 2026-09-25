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
    <title>Nuevo usuario</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="admin_usuarios.php" class="volver-link">← Volver a usuarios</a>
        <h1>Nuevo usuario</h1>

        <form action="php/crear_usuario.php" method="POST" class="form-proyecto">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Correo</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
            
            <label for="rol">Rol</label>
            <input type="text" id="rol" name="rol" list="roles-lista" placeholder="Elige uno existente o escribe uno nuevo" required>
            <datalist id="roles-lista">
                <option value="arquitecto">
                    <option value="admin">
                    </datalist>
            </select>

            <button type="submit">Crear usuario</button>
        </form>
    </div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_usuarios.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, nombre, email, rol FROM usuarios WHERE id = ?');
$stmt->execute([$id]);
$u = $stmt->fetch();

if (!$u) {
    header('Location: admin_usuarios.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($u['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="admin_usuarios.php" class="volver-link">← Volver a usuarios</a>

        <?php if (isset($_GET['actualizado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Usuario actualizado correctamente</div>
        <?php endif; ?>

        <div class="detalle-layout">
            <div>
                <h1><?php echo htmlspecialchars($u['nombre']); ?></h1>
                <a href="admin_usuario_editar.php?id=<?php echo $u['id']; ?>" class="btn-completar">Editar usuario</a>
                <a href="php/eliminar_usuario.php?id=<?php echo $u['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar al usuario <?php echo htmlspecialchars($u['nombre']); ?>? Esta acción no se puede deshacer.');">Eliminar usuario</a>
            </div>

            <div class="contratista-datos">
                <p><strong>Correo:</strong> <?php echo htmlspecialchars($u['email']); ?></p>
                <p><strong>Rol:</strong> <?php echo htmlspecialchars($u['rol']); ?></p>
            </div>
        </div>
    </div>

    <script>
        const mensaje = document.getElementById('mensaje-exito');
        if (mensaje) { setTimeout(() => { mensaje.style.display = 'none'; }, 3000); }
    </script>
</body>
</html>
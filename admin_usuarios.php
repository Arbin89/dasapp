<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';

$stmt = $pdo->query('SELECT id, nombre, email, rol FROM usuarios ORDER BY nombre ASC');
$usuarios = $stmt->fetchAll();
$volver = $_GET['volver'] ?? 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="<?php echo htmlspecialchars($volver); ?>" class="volver-link">← Volver</a>
        <h1>Usuarios</h1>

        <?php if (isset($_GET['creado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Usuario creado correctamente</div>
        <?php endif; ?>

        <div class="acciones-contratistas">
            <a href="admin_usuario_nuevo.php" class="btn-nuevo-proyecto">+ Nuevo usuario</a>
        </div>

        <div class="contratistas-lista">
            <?php foreach ($usuarios as $u): ?>
                <div class="contratista-fila">
                    <span class="contratista-nombre-fila">
                        <?php echo htmlspecialchars($u['nombre']); ?><br>
                        <span style="font-size: 12px; color: #999; font-weight: 400;"><?php echo htmlspecialchars($u['email']); ?></span>
                    </span>
                    <span class="proyecto-estado"><?php echo htmlspecialchars($u['rol']); ?></span>
                    <a href="admin_usuario_detalle.php?id=<?php echo $u['id']; ?>" class="btn-ver">Ver</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const mensaje = document.getElementById('mensaje-exito');
        if (mensaje) { setTimeout(() => { mensaje.style.display = 'none'; }, 3000); }
    </script>
</body>
</html>
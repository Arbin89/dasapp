<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';

$stmt = $pdo->query('SELECT * FROM proveedores ORDER BY nombre ASC');
$proveedores = $stmt->fetchAll();

$volver = $_GET['volver'] ?? 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="<?php echo htmlspecialchars($volver); ?>" class="volver-link">← Volver</a>
        <h1>Proveedores</h1>

        <?php if (isset($_GET['creado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Proveedor creado correctamente</div>
        <?php endif; ?>
        <?php if (isset($_GET['eliminado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Proveedor eliminado correctamente</div>
        <?php endif; ?>

        <div class="acciones-contratistas">
            <a href="admin_proveedor_nuevo.php" class="btn-nuevo-proyecto">+ Nuevo proveedor</a>
        </div>

        <?php if (count($proveedores) === 0): ?>
            <p>Aún no hay proveedores registrados.</p>
        <?php else: ?>
            <div class="contratistas-lista">
                <?php foreach ($proveedores as $p): ?>
                    <div class="contratista-fila">
                        <span class="contratista-nombre-fila">
                            <?php echo htmlspecialchars($p['nombre']); ?><br>
                            <span style="font-size: 12px; color: #999; font-weight: 400;"><?php echo htmlspecialchars($p['rubro'] ?: 'Sin rubro'); ?></span>
                        </span>
                        <a href="admin_proveedor_detalle.php?id=<?php echo $p['id']; ?>&volver=<?php echo urlencode('admin_proveedores.php?volver=' . urlencode($volver)); ?>" class="btn-ver">Ver</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const mensaje = document.getElementById('mensaje-exito');
        if (mensaje) { setTimeout(() => { mensaje.style.display = 'none'; }, 3000); }
    </script>
</body>
</html>
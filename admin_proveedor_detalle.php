<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_proveedores.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM proveedores WHERE id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    header('Location: admin_proveedores.php');
    exit;
}

$volver = $_GET['volver'] ?? 'admin_proveedores.php';

$stmtProy = $pdo->prepare('SELECT proyectos.id, proyectos.nombre AS proyecto_nombre, empresas.nombre AS empresa_nombre FROM proyectos JOIN proyecto_proveedores ON proyectos.id = proyecto_proveedores.proyecto_id JOIN empresas ON proyectos.empresa_id = empresas.id WHERE proyecto_proveedores.proveedor_id = ?');
$stmtProy->execute([$id]);
$proyectosAsignados = $stmtProy->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($p['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="<?php echo htmlspecialchars($volver); ?>" class="volver-link">← Volver</a>

        <?php if (isset($_GET['actualizado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Datos actualizados correctamente</div>
        <?php endif; ?>

        <div class="detalle-layout">
            <div>
                <h1><?php echo htmlspecialchars($p['nombre']); ?></h1>
                <a href="admin_proveedor_editar.php?id=<?php echo $p['id']; ?>&volver=<?php echo urlencode($volver); ?>" class="btn-completar">Editar datos</a>
                <a href="php/eliminar_proveedor.php?id=<?php echo $p['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar a <?php echo htmlspecialchars($p['nombre']); ?> por completo? Se quitará de TODOS los proyectos donde esté asignado.');">Eliminar proveedor</a>
            </div>

            <div class="contratista-datos">
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($p['direccion'] ?: 'No registrada'); ?></p>
                <?php if ($p['latitud'] && $p['longitud']): ?>
                    <p><a href="https://www.google.com/maps?q=<?php echo $p['latitud']; ?>,<?php echo $p['longitud']; ?>" target="_blank">📍 Ver en Google Maps</a></p>
                    <?php endif; ?>
                <p><strong>RNC/Cédula:</strong> <?php echo htmlspecialchars($p['rnc_cedula'] ?: 'No registrado'); ?></p>
                <p><strong>Teléfono 1:</strong> <?php echo htmlspecialchars($p['telefono1'] ?: 'No registrado'); ?></p>
                <p><strong>Teléfono 2:</strong> <?php echo htmlspecialchars($p['telefono2'] ?: 'No registrado'); ?></p>
                <p><strong>Rubro:</strong> <?php echo htmlspecialchars($p['rubro'] ?: 'No registrado'); ?></p>

                <h3>Referencia</h3>
                <p><?php echo htmlspecialchars($p['referencia_nombre'] ?: 'No registrada'); ?> — <?php echo htmlspecialchars($p['referencia_telefono'] ?: 'Sin teléfono'); ?></p>

                <h3>Proyectos asignados</h3>
                <?php if (count($proyectosAsignados) === 0): ?>
                    <p>No está asignado a ningún proyecto.</p>
                <?php else: ?>
                    <?php foreach ($proyectosAsignados as $pa): ?>
                        <p>📌 <?php echo htmlspecialchars($pa['empresa_nombre'] . ' — ' . $pa['proyecto_nombre']); ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const mensaje = document.getElementById('mensaje-exito');
        if (mensaje) { setTimeout(() => { mensaje.style.display = 'none'; }, 3000); }
    </script>
</body>
</html>
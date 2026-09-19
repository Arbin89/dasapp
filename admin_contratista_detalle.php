<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_contratistas.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM contratistas WHERE id = ?');
$stmt->execute([$id]);
$c = $stmt->fetch();

if (!$c) {
    header('Location: admin_contratistas.php');
    exit;
}

$volver = $_GET['volver'] ?? 'admin_contratistas.php';

$stmtProy = $pdo->prepare('SELECT proyectos.id, proyectos.nombre AS proyecto_nombre, empresas.nombre AS empresa_nombre FROM proyectos JOIN proyecto_contratistas ON proyectos.id = proyecto_contratistas.proyecto_id JOIN empresas ON proyectos.empresa_id = empresas.id WHERE proyecto_contratistas.contratista_id = ?');
$stmtProy->execute([$id]);
$proyectosAsignados = $stmtProy->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($c['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="admin_contratistas.php?volver=<?php echo urlencode($volver); ?>" class="volver-link">← Volver a contratistas</a>

        <?php if (isset($_GET['actualizado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Datos actualizados correctamente</div>
        <?php endif; ?>

        <div class="detalle-layout">
            <div>
                <?php if ($c['foto']): ?>
                    <img src="<?php echo htmlspecialchars($c['foto']); ?>" class="contratista-foto-grande">
                <?php else: ?>
                    <div class="contratista-foto-grande contratista-foto-vacia">👤</div>
                <?php endif; ?>
                <h1><?php echo htmlspecialchars($c['nombre']); ?></h1>

                <a href="admin_contratista_editar.php?id=<?php echo $c['id']; ?>" class="btn-completar">Editar datos</a>
                <a href="php/eliminar_contratista.php?id=<?php echo $c['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar a <?php echo htmlspecialchars($c['nombre']); ?> por completo? Se quitará de TODOS los proyectos donde esté asignado.');">Eliminar contratista</a>
            </div>

            <div class="contratista-datos">
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($c['telefono'] ?: 'No registrado'); ?></p>
                <p><strong>Cédula:</strong> <?php echo htmlspecialchars($c['cedula'] ?: 'No registrada'); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($c['direccion'] ?: 'No registrada'); ?></p>

                <h3>Referencia 1</h3>
                <p><?php echo htmlspecialchars($c['ref1_nombre'] ?: 'No registrada'); ?> — <?php echo htmlspecialchars($c['ref1_telefono'] ?: 'Sin teléfono'); ?></p>

                <h3>Referencia 2</h3>
                <p><?php echo htmlspecialchars($c['ref2_nombre'] ?: 'No registrada'); ?> — <?php echo htmlspecialchars($c['ref2_telefono'] ?: 'Sin teléfono'); ?></p>

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
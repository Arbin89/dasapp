<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';

$stmt = $pdo->query('SELECT * FROM contratistas ORDER BY nombre ASC');
$contratistas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contratistas</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="javascript:history.back()" class="volver-link">← Volver</a>
        <h1>Contratistas</h1>

        <?php if (isset($_GET['creado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Contratista creado correctamente</div>
        <?php endif; ?>
        <?php if (isset($_GET['eliminado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Contratista eliminado correctamente</div>
        <?php endif; ?>

        <a href="admin_contratista_nuevo.php" class="btn-nuevo-proyecto">+ Nuevo contratista</a>

        <?php if (count($contratistas) === 0): ?>
            <p>Aún no hay contratistas registrados.</p>
        <?php else: ?>
            <div class="contratistas-grid">
                <?php foreach ($contratistas as $c): ?>
                    <div class="contratista-card">
                        <?php if ($c['foto']): ?>
                            <img src="<?php echo htmlspecialchars($c['foto']); ?>" class="contratista-foto">
                        <?php else: ?>
                            <div class="contratista-foto contratista-foto-vacia">👤</div>
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($c['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($c['telefono'] ?: 'Sin teléfono'); ?></p>
                        <p><?php echo htmlspecialchars($c['cedula'] ?: 'Sin cédula'); ?></p>
                        <a href="php/eliminar_contratista.php?id=<?php echo $c['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar a <?php echo htmlspecialchars($c['nombre']); ?> por completo? Se quitará de TODOS los proyectos donde esté asignado.');">Eliminar</a>
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
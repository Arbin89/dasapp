<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'php/config.php';

$proyecto_id = $_GET['proyecto_id'] ?? null;
if (!$proyecto_id) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM proyectos WHERE id = ?');
$stmt->execute([$proyecto_id]);
$proyecto = $stmt->fetch();

if (!$proyecto) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM planos WHERE proyecto_id = ? ORDER BY fecha_subida DESC');
$stmt->execute([$proyecto_id]);
$planos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planos - <?php echo htmlspecialchars($proyecto['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="proyecto_detalle.php?id=<?php echo $proyecto_id; ?>" class="volver-link">← Volver al proyecto</a>
        <h1>Planos de <?php echo htmlspecialchars($proyecto['nombre']); ?></h1>

        <?php if (isset($_GET['subido'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Plano subido correctamente</div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje-error">El archivo debe ser un PDF válido</div>
        <?php endif; ?>

        <form action="php/subir_plano.php" method="POST" enctype="multipart/form-data" class="form-upload">
            <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($proyecto_id); ?>">
            <input type="file" name="plano" accept="application/pdf" required>
            <button type="submit">Subir plano</button>
        </form>

        <?php if (count($planos) === 0): ?>
            <p>Aún no se han subido planos para este proyecto.</p>
        <?php else: ?>
            <div class="archivos-lista">
                <?php foreach ($planos as $plano): ?>
                    <div class="archivo-item">
                        <span class="archivo-nombre">📄 <?php echo htmlspecialchars($plano['nombre_archivo']); ?></span>
                        <span class="archivo-fecha"><?php echo date('d/m/Y', strtotime($plano['fecha_subida'])); ?></span>
                        <a href="<?php echo htmlspecialchars($plano['ruta_archivo']); ?>" target="_blank" class="btn-ver">Ver</a>
                        <a href="<?php echo htmlspecialchars($plano['ruta_archivo']); ?>" download class="btn-descargar">Descargar</a>
                        <a href="php/eliminar_plano.php?id=<?php echo $plano['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este plano? Esta acción no se puede deshacer.');">Eliminar</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const mensaje = document.getElementById('mensaje-exito');
        if (mensaje) {
            setTimeout(() => { mensaje.style.display = 'none'; }, 3000);
        }
    </script>
</body>
</html>
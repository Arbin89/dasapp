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

$stmt = $pdo->prepare('SELECT * FROM fotos WHERE proyecto_id = ? ORDER BY fecha_subida DESC');
$stmt->execute([$proyecto_id]);
$fotos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fotos - <?php echo htmlspecialchars($proyecto['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="proyecto_detalle.php?id=<?php echo $proyecto_id; ?>" class="volver-link">← Volver al proyecto</a>
        <h1>Fotos de <?php echo htmlspecialchars($proyecto['nombre']); ?></h1>

        <?php if (isset($_GET['subido'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Foto subida correctamente</div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje-error">El archivo debe ser una imagen válida (JPG, PNG, WEBP o GIF)</div>
        <?php endif; ?>

        <form action="php/subir_foto.php" method="POST" enctype="multipart/form-data" class="form-upload">
            <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($proyecto_id); ?>">
            <input type="file" name="foto" accept="image/*" required>
            <button type="submit">Subir foto</button>
        </form>

        <?php if (count($fotos) === 0): ?>
            <p>Aún no se han subido fotos para este proyecto.</p>
        <?php else: ?>
            <div class="fotos-galeria">
                <?php foreach ($fotos as $foto): ?>
                    <div class="foto-item">
                        <a href="<?php echo htmlspecialchars($foto['ruta_archivo']); ?>" target="_blank">
                            <img src="<?php echo htmlspecialchars($foto['ruta_archivo']); ?>" alt="<?php echo htmlspecialchars($foto['nombre_archivo']); ?>" class="foto-miniatura">
                        </a>
                        <div class="foto-info">
                            <span class="foto-fecha"><?php echo date('d/m/Y', strtotime($foto['fecha_subida'])); ?></span>
                            <a href="<?php echo htmlspecialchars($foto['ruta_archivo']); ?>" download class="btn-descargar">⬇</a>
                            <a href="php/eliminar_foto.php?id=<?php echo $foto['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar esta foto?');">✗</a>
                        </div>
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
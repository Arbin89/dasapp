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

$stmt = $pdo->prepare('SELECT * FROM categorias_documentos WHERE proyecto_id = ? ORDER BY nombre ASC');
$stmt->execute([$proyecto_id]);
$categorias = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM documentos WHERE proyecto_id = ? ORDER BY fecha_subida DESC');
$stmt->execute([$proyecto_id]);
$documentos = $stmt->fetchAll();

$documentosPorCategoria = [];
foreach ($categorias as $cat) {
    $documentosPorCategoria[$cat['id']] = [];
}
foreach ($documentos as $doc) {
    $documentosPorCategoria[$doc['categoria_id']][] = $doc;
}

$categoria_activa = $_GET['categoria_id'] ?? ($categorias[0]['id'] ?? null);

$catActivaNombre = '';
foreach ($categorias as $cat) {
    if ($cat['id'] == $categoria_activa) {
        $catActivaNombre = $cat['nombre'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentos - <?php echo htmlspecialchars($proyecto['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="proyecto_detalle.php?id=<?php echo $proyecto_id; ?>" class="volver-link">← Volver al proyecto</a>
        <h1>Documentos de <?php echo htmlspecialchars($proyecto['nombre']); ?></h1>

        <?php if (isset($_GET['subido'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Documento subido correctamente</div>
        <?php endif; ?>

        <form action="php/crear_categoria_documento.php" method="POST" class="form-upload">
            <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($proyecto_id); ?>">
            <input type="text" name="nombre_categoria" placeholder="Nombre de nueva categoría" required>
            <button type="submit">+ Crear categoría</button>
        </form>

        <?php if (count($categorias) === 0): ?>
            <p>Crea una categoría para poder empezar a subir documentos.</p>
        <?php else: ?>
            <form action="php/subir_documento.php" method="POST" enctype="multipart/form-data" class="form-upload">
                <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($proyecto_id); ?>">
                <select name="categoria_id" required>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($categoria_activa == $cat['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="file" name="documento" required>
                <button type="submit">Subir documento</button>
            </form>

            <div class="categorias-tabs">
                <?php foreach ($categorias as $cat): ?>
                    <a href="proyecto_documentos.php?proyecto_id=<?php echo $proyecto_id; ?>&categoria_id=<?php echo $cat['id']; ?>" class="tab-categoria <?php echo ($categoria_activa == $cat['id']) ? 'tab-activa' : ''; ?>">
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($categoria_activa): ?>
                <a href="php/eliminar_categoria_documento.php?id=<?php echo $categoria_activa; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="btn-eliminar-categoria" onclick="return confirm('¿Eliminar la categoría «<?php echo htmlspecialchars($catActivaNombre); ?>»? Esto también borrará TODOS los documentos que contiene. Esta acción no se puede deshacer.');">🗑 Eliminar categoría actual</a>
            <?php endif; ?>

            <?php if ($categoria_activa && count($documentosPorCategoria[$categoria_activa]) === 0): ?>
                <p class="categoria-vacia">Sin documentos en esta categoría.</p>
            <?php elseif ($categoria_activa): ?>
                <div class="archivos-lista">
                    <?php foreach ($documentosPorCategoria[$categoria_activa] as $doc): ?>
                        <div class="archivo-item">
                            <span class="archivo-nombre">📄 <?php echo htmlspecialchars($doc['nombre_archivo']); ?></span>
                            <span class="archivo-fecha"><?php echo date('d/m/Y', strtotime($doc['fecha_subida'])); ?></span>
                            <a href="<?php echo htmlspecialchars($doc['ruta_archivo']); ?>" target="_blank" class="btn-ver">Ver</a>
                            <a href="<?php echo htmlspecialchars($doc['ruta_archivo']); ?>" download class="btn-descargar">Descargar</a>
                            <a href="php/eliminar_documento.php?id=<?php echo $doc['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este documento?');">Eliminar</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
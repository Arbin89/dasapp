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

$stmt = $pdo->prepare('SELECT * FROM categorias_planos WHERE proyecto_id = ? ORDER BY nombre ASC');
$stmt->execute([$proyecto_id]);
$categorias = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM planos WHERE proyecto_id = ? ORDER BY fecha_subida DESC');
$stmt->execute([$proyecto_id]);
$planos = $stmt->fetchAll();

// Agrupar los planos por categoría ('sin' = planos sin categoría)
$planosPorCategoria = ['sin' => []];
foreach ($categorias as $cat) {
    $planosPorCategoria[$cat['id']] = [];
}
foreach ($planos as $pl) {
    $clave = $pl['categoria_id'] ?? 'sin';
    $planosPorCategoria[$clave][] = $pl;
}

// Pestaña activa: la de la URL, o la primera categoría, o "sin categoría"
$categoria_activa = (string)($_GET['categoria_id'] ?? ($categorias[0]['id'] ?? 'sin'));

$catActivaNombre = '';
foreach ($categorias as $cat) {
    if ((string)$cat['id'] === $categoria_activa) {
        $catActivaNombre = $cat['nombre'];
    }
}

$planosActivos = $planosPorCategoria[$categoria_activa] ?? [];
$mostrarSinCategoria = count($planosPorCategoria['sin']) > 0 || $categoria_activa === 'sin';
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

        <form action="php/crear_categoria_plano.php" method="POST" class="form-upload">
            <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($proyecto_id); ?>">
            <input type="text" name="nombre_categoria" placeholder="Nombre de nueva categoría" required>
            <button type="submit">+ Crear categoría</button>
        </form>

        <form action="php/subir_plano.php" method="POST" enctype="multipart/form-data" class="form-upload">
            <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($proyecto_id); ?>">
            <select name="categoria_id">
                <option value="" <?php echo ($categoria_activa === 'sin') ? 'selected' : ''; ?>>Sin categoría</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ((string)$cat['id'] === $categoria_activa) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="file" name="plano" accept="application/pdf" required>
            <button type="submit">Subir plano</button>
        </form>

        <div class="categorias-tabs">
            <?php foreach ($categorias as $cat): ?>
                <a href="proyecto_planos.php?proyecto_id=<?php echo $proyecto_id; ?>&categoria_id=<?php echo $cat['id']; ?>" class="tab-categoria <?php echo ((string)$cat['id'] === $categoria_activa) ? 'tab-activa' : ''; ?>">
                    <?php echo htmlspecialchars($cat['nombre']); ?>
                </a>
            <?php endforeach; ?>
            <?php if ($mostrarSinCategoria): ?>
                <a href="proyecto_planos.php?proyecto_id=<?php echo $proyecto_id; ?>&categoria_id=sin" class="tab-categoria <?php echo ($categoria_activa === 'sin') ? 'tab-activa' : ''; ?>">
                    Sin categoría
                </a>
            <?php endif; ?>
        </div>

        <?php if ($catActivaNombre !== ''): ?>
            <a href="php/eliminar_categoria_plano.php?id=<?php echo $categoria_activa; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="btn-eliminar-categoria" onclick="return confirm('¿Eliminar la categoría «<?php echo htmlspecialchars($catActivaNombre); ?>»? Esto también borrará TODOS los planos que contiene. Esta acción no se puede deshacer.');">🗑 Eliminar categoría actual</a>
        <?php endif; ?>

        <?php if (count($planosActivos) === 0): ?>
            <p class="categoria-vacia">Sin planos en esta categoría.</p>
        <?php else: ?>
            <div class="archivos-lista">
                <?php foreach ($planosActivos as $plano): ?>
                    <div class="archivo-item">
                        <span class="archivo-nombre">📄 <?php echo htmlspecialchars($plano['nombre_archivo']); ?></span>
                        <span class="archivo-fecha"><?php echo date('d/m/Y', strtotime($plano['fecha_subida'])); ?></span>
                        <a href="<?php echo htmlspecialchars($plano['ruta_archivo']); ?>" target="_blank" class="btn-ver">Ver</a>
                        <a href="<?php echo htmlspecialchars($plano['ruta_archivo']); ?>" download class="btn-descargar">Descargar</a>
                        <a href="php/eliminar_plano.php?id=<?php echo $plano['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>&categoria_id=<?php echo urlencode($categoria_activa); ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este plano? Esta acción no se puede deshacer.');">Eliminar</a>
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
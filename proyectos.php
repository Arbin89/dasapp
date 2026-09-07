<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'php/config.php';

$empresa_id = $_GET['empresa_id'] ?? null;

if (!$empresa_id) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM empresas WHERE id = ?');
$stmt->execute([$empresa_id]);
$empresa = $stmt->fetch();

if (!$empresa) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM proyectos WHERE empresa_id = ? ORDER BY fecha_inicio DESC');
$stmt->execute([$empresa_id]);
$proyectos = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proyectos - <?php echo htmlspecialchars($empresa['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css?v=1">
    <link rel="stylesheet" href="css/panel.css?v=1">
</head>

<body>
    <div class="panel-container2">
    <a href="dashboard.php" class="volver-link">← Volver a selección de empresa</a>
    <h1>Proyectos de <?php echo htmlspecialchars($empresa['nombre']); ?></h1>

    <?php if (count($proyectos) === 0): ?>
        <p>Esta empresa aun no tiene proyectos registrados</p>
        <?php else: ?>
            <div class="proyectos-lista">
                <?php foreach ($proyectos as $proyecto): ?>
                    <div class="proyecto-item">
                      <h3><?php echo htmlspecialchars($proyecto['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($proyecto['descripcion']); ?></p>
                        <span class="proyecto-estado <?php echo $proyecto['estado'] === 'En proceso' ? 'en-proceso' : ''; ?>">
                            <?php echo htmlspecialchars($proyecto['estado']); ?>
                        </span>
                         <a href="proyecto_detalle.php?id=<?php echo $proyecto['id']; ?>" class="btn-ver">Ver</a>
                         <a href="php/eliminar_proyecto.php?id=<?php echo $proyecto['id']; ?>&empresa_id=<?php echo $empresa_id; ?>" class="btn-eliminar" onclick="return confirm('¿Seguro que quieres eliminar este proyecto? Esta acción no se puede deshacer.');">Eliminar</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>  



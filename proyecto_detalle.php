<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM proyectos WHERE id = ?');
$stmt->execute([$id]);
$proyecto = $stmt->fetch();

if (!$proyecto) {
    header('Location: dashboard.php');
    exit;
}

$secciones = [
    ['nombre' => 'Planos', 'archivo' => 'proyecto_planos.php'],
    ['nombre' => 'Contratistas', 'archivo' => 'proyecto_contratistas.php'],
    ['nombre' => 'Documentos', 'archivo' => 'proyecto_documentos.php'],
    ['nombre' => 'Proveedores', 'archivo' => 'proyecto_proveedores.php'],
    ['nombre' => 'Fotos', 'archivo' => 'proyecto_fotos.php'],
    ['nombre' => 'Tablero de asignaciones', 'archivo' => 'proyecto_tablero.php'],
    ['nombre' => 'Cronograma', 'archivo' => 'proyecto_cronograma.php'],
    ['nombre' => 'Inventario de materiales', 'archivo' => 'proyecto_inventario.php'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($proyecto['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css?v=1">
    <link rel="stylesheet" href="css/panel.css?v=1">
</head>
<body>
    <div class="panel-container">
        <a href="javascript:history.back()" class="volver-link">← Volver</a>

        <div class="detalle-layout">
            <h1><?php echo htmlspecialchars($proyecto['nombre']); ?></h1>

            <div class="secciones-grid">
                <?php foreach ($secciones as $seccion): ?>
                    <a href="<?php echo $seccion['archivo']; ?>?proyecto_id=<?php echo $proyecto['id']; ?>" class="seccion-card">
                        <?php echo $seccion['nombre']; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
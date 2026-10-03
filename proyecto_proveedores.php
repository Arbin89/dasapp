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

$stmt = $pdo->prepare('SELECT proveedores.* FROM proveedores JOIN proyecto_proveedores ON proveedores.id = proyecto_proveedores.proveedor_id WHERE proyecto_proveedores.proyecto_id = ? ORDER BY proveedores.nombre ASC');
$stmt->execute([$proyecto_id]);
$asignados = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM proveedores WHERE id NOT IN (SELECT proveedor_id FROM proyecto_proveedores WHERE proyecto_id = ?) ORDER BY nombre ASC');
$stmt->execute([$proyecto_id]);
$disponibles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores - <?php echo htmlspecialchars($proyecto['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="proyecto_detalle.php?id=<?php echo $proyecto_id; ?>" class="volver-link">← Volver al proyecto</a>
        <h1>Proveedores de <?php echo htmlspecialchars($proyecto['nombre']); ?></h1>

        <?php if (isset($_GET['agregado']) || isset($_GET['creado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Proveedor agregado al proyecto</div>
        <?php endif; ?>

        <div class="acciones-contratistas">
            <a href="admin_proveedor_nuevo.php?proyecto_id=<?php echo $proyecto_id; ?>" class="btn-nuevo-proyecto">+ Crear nuevo proveedor</a>
        </div>

        <?php if (count($disponibles) > 0): ?>
            <form action="php/asignar_proveedor.php" method="POST" class="form-upload">
                <input type="hidden" name="proyecto_id" value="<?php echo $proyecto_id; ?>">
                <select name="proveedor_id" required>
                    <?php foreach ($disponibles as $d): ?>
                        <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Agregar existente</button>
            </form>
        <?php endif; ?>

        <?php if (count($asignados) === 0): ?>
            <p>Aún no hay proveedores asignados a este proyecto.</p>
        <?php else: ?>
            <div class="contratistas-lista">
                <?php foreach ($asignados as $a): ?>
                    <div class="contratista-fila">
                        <span class="contratista-nombre-fila">
                            <?php echo htmlspecialchars($a['nombre']); ?><br>
                            <span style="font-size: 12px; color: #999; font-weight: 400;"><?php echo htmlspecialchars($a['rubro'] ?: 'Sin rubro'); ?></span>
                        </span>
                        <a href="admin_proveedor_detalle.php?id=<?php echo $a['id']; ?>&volver=<?php echo urlencode('proyecto_proveedores.php?proyecto_id=' . $proyecto_id); ?>" class="btn-ver">Ver</a>
                        <a href="php/quitar_proveedor_proyecto.php?proveedor_id=<?php echo $a['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="btn-eliminar" onclick="return confirm('¿Quitar a <?php echo htmlspecialchars($a['nombre']); ?> de este proyecto? (No se borrará de la base de datos global).');">Quitar</a>
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
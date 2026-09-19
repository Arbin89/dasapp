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

$stmt = $pdo->prepare('SELECT contratistas.* FROM contratistas JOIN proyecto_contratistas ON contratistas.id = proyecto_contratistas.contratista_id WHERE proyecto_contratistas.proyecto_id = ? ORDER BY contratistas.nombre ASC');
$stmt->execute([$proyecto_id]);
$asignados = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM contratistas WHERE id NOT IN (SELECT contratista_id FROM proyecto_contratistas WHERE proyecto_id = ?) ORDER BY nombre ASC');
$stmt->execute([$proyecto_id]);
$disponibles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contratistas - <?php echo htmlspecialchars($proyecto['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="proyecto_detalle.php?id=<?php echo $proyecto_id; ?>" class="volver-link">← Volver al proyecto</a>
        <h1>Contratistas de <?php echo htmlspecialchars($proyecto['nombre']); ?></h1>

        <?php if (isset($_GET['agregado']) || isset($_GET['creado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Contratista agregado al proyecto</div>
        <?php endif; ?>

        <div class="form-upload">
            <?php if (count($disponibles) > 0): ?>
                <form action="php/asignar_contratista.php" method="POST" style="display: flex; gap: 12px;">
                    <input type="hidden" name="proyecto_id" value="<?php echo $proyecto_id; ?>">
                    <select name="contratista_id" required>
                        <?php foreach ($disponibles as $d): ?>
                            <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Agregar existente</button>
                </form>
            <?php endif; ?>
            <a href="admin_contratista_nuevo.php?proyecto_id=<?php echo $proyecto_id; ?>" class="btn-nuevo-proyecto">+ Crear nuevo contratista</a>
        </div>

        <?php if (count($asignados) === 0): ?>
            <p>Aún no hay contratistas asignados a este proyecto.</p>
        <?php else: ?>
            <div class="contratistas-lista" style="margin: 20px 20px;">
                <?php foreach ($asignados as $a): ?>
                    <div class="contratista-fila">
                        <?php if ($a['foto']): ?>
                            <img src="<?php echo htmlspecialchars($a['foto']); ?>" class="contratista-foto-chica">
                        <?php else: ?>
                            <div class="contratista-foto-chica contratista-foto-vacia">👤</div>
                        <?php endif; ?>
                        <span class="contratista-nombre-fila"><?php echo htmlspecialchars($a['nombre']); ?></span>
                        <a href="admin_contratista_detalle.php?id=<?php echo $a['id']; ?>&volver=<?php echo urlencode('proyecto_contratistas.php?proyecto_id=' . $proyecto_id); ?>" class="btn-ver">Ver</a>
                        <a href="php/quitar_contratista_proyecto.php?contratista_id=<?php echo $a['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="btn-eliminar" onclick="return confirm('¿Quitar a <?php echo htmlspecialchars($a['nombre']); ?> de este proyecto? (No se borrará de la base de datos global).');">Quitar</a>
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
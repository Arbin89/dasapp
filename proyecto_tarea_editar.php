<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';

$id = $_GET['id'] ?? null;
$proyecto_id = $_GET['proyecto_id'] ?? null;

if (!$id || !$proyecto_id) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM tareas WHERE id = ?');
$stmt->execute([$id]);
$tarea = $stmt->fetch();

if (!$tarea) {
    header('Location: proyecto_tablero.php?proyecto_id=' . $proyecto_id);
    exit;
}

$stmt = $pdo->prepare('SELECT contratistas.* FROM contratistas JOIN proyecto_contratistas ON contratistas.id = proyecto_contratistas.contratista_id WHERE proyecto_contratistas.proyecto_id = ? ORDER BY contratistas.nombre ASC');
$stmt->execute([$proyecto_id]);
$contratistas = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT contratista_id FROM tarea_contratistas WHERE tarea_id = ?');
$stmt->execute([$id]);
$asignadosIds = array_column($stmt->fetchAll(), 'contratista_id');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar tarea</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="proyecto_tarea_detalle.php?id=<?php echo $id; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="volver-link">← Volver al detalle</a>
        <h1>Editar tarea</h1>

        <form action="php/actualizar_tarea.php" method="POST" class="form-proyecto">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="proyecto_id" value="<?php echo $proyecto_id; ?>">

            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($tarea['titulo']); ?>" required>

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4"><?php echo htmlspecialchars($tarea['descripcion'] ?? ''); ?></textarea>

            <label for="estado">Columna</label>
            <select id="estado" name="estado">
                <?php foreach (['Pendiente', 'En progreso', 'Finalizado'] as $e): ?>
                    <option value="<?php echo $e; ?>" <?php echo ($tarea['estado'] === $e) ? 'selected' : ''; ?>><?php echo $e; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="prioridad">Prioridad</label>
            <select id="prioridad" name="prioridad">
                <?php foreach (['Alta', 'Media', 'Baja'] as $p): ?>
                    <option value="<?php echo $p; ?>" <?php echo ($tarea['prioridad'] === $p) ? 'selected' : ''; ?>><?php echo $p; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="categoria">Categoría</label>
            <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($tarea['categoria'] ?? ''); ?>">

            <label for="fecha_limite">Fecha de entrega</label>
            <input type="date" id="fecha_limite" name="fecha_limite" value="<?php echo htmlspecialchars($tarea['fecha_limite'] ?? ''); ?>">

            <label for="contratistas">Responsables</label>
            <?php if (count($contratistas) === 0): ?>
                <p style="font-size: 13px; color: #999;">Este proyecto aún no tiene contratistas asignados.</p>
            <?php else: ?>
                <select id="contratistas" name="contratistas[]" multiple size="6">
                    <?php foreach ($contratistas as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo in_array($c['id'], $asignadosIds) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <p style="font-size: 12px; color: #999; margin-top: 6px;">Mantén presionado Ctrl (o Cmd en Mac) para seleccionar varios. Las opciones ya resaltadas son las asignaciones actuales.</p>
            <?php endif; ?>

            <button type="submit">Guardar cambios</button>
        </form>
    </div>
</body>
</html>
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

$estadoInicial = $_GET['estado'] ?? 'Pendiente';

$stmt = $pdo->prepare('SELECT contratistas.* FROM contratistas JOIN proyecto_contratistas ON contratistas.id = proyecto_contratistas.contratista_id WHERE proyecto_contratistas.proyecto_id = ? ORDER BY contratistas.nombre ASC');
$stmt->execute([$proyecto_id]);
$contratistas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva tarea</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="proyecto_tablero.php?proyecto_id=<?php echo $proyecto_id; ?>" class="volver-link">← Volver al tablero</a>
        <h1>Nueva tarea</h1>

        <form action="php/crear_tarea.php" method="POST" class="form-proyecto">
            <input type="hidden" name="proyecto_id" value="<?php echo $proyecto_id; ?>">

            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4"></textarea>

            <label for="estado">Columna</label>
            <select id="estado" name="estado">
                <?php foreach (['Pendiente', 'En progreso', 'Finalizado'] as $e): ?>
                    <option value="<?php echo $e; ?>" <?php echo ($estadoInicial === $e) ? 'selected' : ''; ?>><?php echo $e; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="prioridad">Prioridad</label>
            <select id="prioridad" name="prioridad">
                <option value="Alta">Alta</option>
                <option value="Media" selected>Media</option>
                <option value="Baja">Baja</option>
            </select>

            <label for="categoria">Categoría</label>
            <input type="text" id="categoria" name="categoria" placeholder="Ej: Electricidad, Acabados, Permisos">

            <label for="fecha_limite">Fecha de limite de finalizacion</label>
            <input type="date" id="fecha_limite" name="fecha_limite">

            <label for="contratistas">Responsables</label>
            <?php if (count($contratistas) === 0): ?>
                <p style="font-size: 13px; color: #999;">Este proyecto aún no tiene contratistas asignados. Ve primero a la sección Contratistas.</p>
            <?php else: ?>
                <select id="contratistas" name="contratistas[]" multiple size="6">
                    <?php foreach ($contratistas as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <p style="font-size: 12px; color: #999; margin-top: 6px;">Mantén presionado Ctrl (o Cmd en Mac) y haz clic para seleccionar varios a la vez.</p>
            <?php endif; ?>

            <button type="submit">Crear tarea</button>
        </form>
    </div>
</body>
</html>
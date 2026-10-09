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

$stmt = $pdo->prepare('SELECT contratistas.* FROM contratistas JOIN tarea_contratistas ON contratistas.id = tarea_contratistas.contratista_id WHERE tarea_contratistas.tarea_id = ?');
$stmt->execute([$id]);
$asignados = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($tarea['titulo']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="proyecto_tablero.php?proyecto_id=<?php echo $proyecto_id; ?>" class="volver-link">← Volver al tablero</a>

        <?php if (isset($_GET['actualizado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Tarea actualizada correctamente</div>
        <?php endif; ?>

        <div class="detalle-layout">
            <div>
                <h1><?php echo htmlspecialchars($tarea['titulo']); ?></h1>

                <span class="proyecto-estado <?php echo $tarea['estado'] !== 'Finalizado' ? 'en-proceso' : ''; ?>">
                    <?php echo htmlspecialchars($tarea['estado']); ?>
                </span>
                <span class="prioridad prioridad-<?php echo strtolower($tarea['prioridad']); ?>">
                    <?php echo htmlspecialchars($tarea['prioridad']); ?>
                </span>

                <div style="margin-top: 15px;">
                    <?php if ($tarea['estado'] === 'Finalizado'): ?>
                        <a href="php/completar_tarea.php?id=<?php echo $tarea['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>&accion=descompletar" class="btn-completar btn-descompletar">Reabrir tarea</a>
                    <?php else: ?>
                        <a href="php/completar_tarea.php?id=<?php echo $tarea['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>&accion=completar" class="btn-completar">Completar tarea</a>
                    <?php endif; ?>
                    <a href="proyecto_tarea_editar.php?id=<?php echo $tarea['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="btn-ver">Editar tarea</a>
                    <a href="php/eliminar_tarea.php?id=<?php echo $tarea['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar esta tarea?');">Eliminar tarea</a>
                </div>
            </div>

            <div class="contratista-datos">
                <p><strong>Descripción:</strong><br><?php echo nl2br(htmlspecialchars($tarea['descripcion'] ?: 'Sin descripción')); ?></p>

                <?php if ($tarea['categoria']): ?>
                    <p><strong>Categoría:</strong> <?php echo htmlspecialchars($tarea['categoria']); ?></p>
                <?php endif; ?>

                <?php if ($tarea['fecha_limite']): ?>
                    <p><strong>Fecha de entrega:</strong> <?php echo date('d/m/Y', strtotime($tarea['fecha_limite'])); ?></p>
                <?php endif; ?>

                <p><strong>Creada el:</strong> <?php echo date('d/m/Y', strtotime($tarea['fecha_creacion'])); ?></p>
                
                <?php if ($tarea['estado'] === 'Finalizado' && $tarea['fecha_finalizacion']): ?>
                    <p><strong>Finalizada el:</strong> <?php echo date('d/m/Y', strtotime($tarea['fecha_finalizacion'])); ?></p>
                    
                    <?php endif; ?>

                <h3>Responsables</h3>
                <?php if (count($asignados) === 0): ?>
                    <p>Nadie asignado.</p>
                <?php else: ?>
                    <?php foreach ($asignados as $a): ?>
                        <p>👤 <?php echo htmlspecialchars($a['nombre']); ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const mensaje = document.getElementById('mensaje-exito');
        if (mensaje) { setTimeout(() => { mensaje.style.display = 'none'; }, 3000); }
    </script>
</body>
</html>
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

$stmt = $pdo->prepare('SELECT * FROM tareas WHERE proyecto_id = ? ORDER BY fecha_creacion DESC');
$stmt->execute([$proyecto_id]);
$tareas = $stmt->fetchAll();

$asignadosPorTarea = [];
foreach ($tareas as $t) {
    $stmt = $pdo->prepare('SELECT contratistas.nombre FROM contratistas JOIN tarea_contratistas ON contratistas.id = tarea_contratistas.contratista_id WHERE tarea_contratistas.tarea_id = ?');
    $stmt->execute([$t['id']]);
    $asignadosPorTarea[$t['id']] = $stmt->fetchAll();
}

$columnas = ['Pendiente', 'En progreso', 'Finalizado'];
$tareasPorColumna = ['Pendiente' => [], 'En progreso' => [], 'Finalizado' => []];
foreach ($tareas as $t) {
    $col = isset($tareasPorColumna[$t['estado']]) ? $t['estado'] : 'Pendiente';
    $tareasPorColumna[$col][] = $t;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tablero - <?php echo htmlspecialchars($proyecto['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="proyecto_detalle.php?id=<?php echo $proyecto_id; ?>" class="volver-link">← Volver al proyecto</a>
        <h1>Tablero de <?php echo htmlspecialchars($proyecto['nombre']); ?></h1>

        <?php if (isset($_GET['creado'])): ?>
            <div id="mensaje-exito" class="mensaje-exito">✓ Tarea creada correctamente</div>
        <?php endif; ?>

        <div class="kanban">
            <?php foreach ($columnas as $col): ?>
                <div class="kanban-columna" data-estado="<?php echo $col; ?>">
                    <div class="kanban-titulo">
                        <?php echo $col; ?>
                        <span class="kanban-contador"><?php echo count($tareasPorColumna[$col]); ?></span>
                    </div>

                    <div class="kanban-tarjetas">
                        <?php foreach ($tareasPorColumna[$col] as $t): ?>
                            <?php
                            $vencida = $t['fecha_limite'] && $t['fecha_limite'] < date('Y-m-d') && $t['estado'] !== 'Finalizado';
                            ?>
                            <div class="kanban-tarjeta" draggable="true" data-id="<?php echo $t['id']; ?>">
                                <a href="proyecto_tarea_detalle.php?id=<?php echo $t['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="tarjeta-titulo" draggable="false">
                                    <?php echo htmlspecialchars($t['titulo']); ?>
                                </a>

                                <?php if ($t['categoria']): ?>
                                    <span class="etiqueta-categoria"><?php echo htmlspecialchars($t['categoria']); ?></span>
                                <?php endif; ?>

                                <div class="tarjeta-meta">
                                    <span class="prioridad prioridad-<?php echo strtolower($t['prioridad']); ?>"><?php echo htmlspecialchars($t['prioridad']); ?></span>
                                    <?php if ($t['fecha_limite']): ?>
                                        <span class="<?php echo $vencida ? 'fecha-vencida' : ''; ?>">📅 <?php echo date('d/m/Y', strtotime($t['fecha_limite'])); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="tarjeta-fechas">
                                    Creada: <?php echo date('d/m/Y', strtotime($t['fecha_creacion'])); ?>
                                    
                                    <?php if ($t['estado'] === 'Finalizado' && $t['fecha_finalizacion']): ?>
                                        · Finalizada: <?php echo date('d/m/Y', strtotime($t['fecha_finalizacion'])); ?>
                                        <?php endif; ?>
                                    </div>

                                <div class="tarjeta-responsables">
                                    <?php foreach ($asignadosPorTarea[$t['id']] as $a): ?>
                                        <span class="avatar-mini" title="<?php echo htmlspecialchars($a['nombre']); ?>"><?php echo htmlspecialchars(mb_strtoupper(mb_substr($a['nombre'], 0, 1))); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <a href="proyecto_tarea_nueva.php?proyecto_id=<?php echo $proyecto_id; ?>&estado=<?php echo urlencode($col); ?>" class="kanban-agregar">+ Agregar tarea</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const mensaje = document.getElementById('mensaje-exito');
        if (mensaje) { setTimeout(() => { mensaje.style.display = 'none'; }, 3000); }

        let tarjetaArrastrada = null;

        function actualizarContadores() {
            document.querySelectorAll('.kanban-columna').forEach(col => {
                col.querySelector('.kanban-contador').textContent = col.querySelectorAll('.kanban-tarjeta').length;
            });
        }

        document.querySelectorAll('.kanban-tarjeta').forEach(tarjeta => {
            tarjeta.addEventListener('dragstart', () => {
                tarjetaArrastrada = tarjeta;
                tarjeta.classList.add('arrastrando');
            });
            tarjeta.addEventListener('dragend', () => {
                tarjeta.classList.remove('arrastrando');
            });
        });

        document.querySelectorAll('.kanban-columna').forEach(col => {
            col.addEventListener('dragover', e => {
                e.preventDefault();
                col.classList.add('columna-resaltada');
            });
            col.addEventListener('dragleave', e => {
                if (!col.contains(e.relatedTarget)) {
                    col.classList.remove('columna-resaltada');
                }
            });
            col.addEventListener('drop', e => {
                e.preventDefault();
                col.classList.remove('columna-resaltada');
                if (!tarjetaArrastrada) return;

                col.querySelector('.kanban-tarjetas').appendChild(tarjetaArrastrada);
                actualizarContadores();

                const datos = new URLSearchParams();
                datos.append('id', tarjetaArrastrada.dataset.id);
                datos.append('estado', col.dataset.estado);

                fetch('php/mover_tarea.php', { method: 'POST', body: datos })
                    .then(respuesta => { if (!respuesta.ok) location.reload(); });
            });
        });
    </script>
</body>
</html>
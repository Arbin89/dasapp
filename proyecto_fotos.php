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

$stmt = $pdo->prepare('SELECT * FROM albumes_fotos WHERE proyecto_id = ? ORDER BY nombre ASC');
$stmt->execute([$proyecto_id]);
$albumes = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM fotos WHERE proyecto_id = ? ORDER BY fecha_subida DESC');
$stmt->execute([$proyecto_id]);
$fotos = $stmt->fetchAll();

// Agrupar las fotos por álbum ('sin' = fotos sin álbum)
$fotosPorAlbum = ['sin' => []];
foreach ($albumes as $al) {
    $fotosPorAlbum[$al['id']] = [];
}
foreach ($fotos as $f) {
    $clave = $f['album_id'] ?? 'sin';
    $fotosPorAlbum[$clave][] = $f;
}

// Armar la información de cada álbum, incluida su portada
$albumesVista = [];
foreach ($albumes as $al) {
    $lista = $fotosPorAlbum[$al['id']];
    $portada = $lista[0] ?? null; // por defecto, la foto más reciente
    foreach ($lista as $f) {
        if ($al['portada_foto_id'] !== null && (int)$f['id'] === (int)$al['portada_foto_id']) {
            $portada = $f;
            break;
        }
    }
    $albumesVista[] = [
        'clave' => (string)$al['id'],
        'nombre' => $al['nombre'],
        'fotos' => $lista,
        'portada' => $portada,
        'es_sin' => false,
    ];
}
if (count($fotosPorAlbum['sin']) > 0) {
    $albumesVista[] = [
        'clave' => 'sin',
        'nombre' => 'Sin álbum',
        'fotos' => $fotosPorAlbum['sin'],
        'portada' => $fotosPorAlbum['sin'][0],
        'es_sin' => true,
    ];
}

$album_abrir = isset($_GET['album_id']) ? (string)$_GET['album_id'] : '';

$mensajeExito = '';
if (isset($_GET['subido'])) {
    $mensajeExito = '✓ Foto subida correctamente';
} elseif (isset($_GET['renombrado'])) {
    $mensajeExito = '✓ Álbum renombrado correctamente';
} elseif (isset($_GET['portada'])) {
    $mensajeExito = '✓ Portada actualizada';
} elseif (isset($_GET['eliminado'])) {
        $mensajeExito = '✓ Álbum eliminado correctamente';
}
$mensajeError = isset($_GET['error']) ? 'El archivo debe ser una imagen válida (JPG, PNG, WEBP o GIF)' : '';

function textoCantidad($n, $singular, $plural) {
    return $n . ' ' . ($n === 1 ? $singular : $plural);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fotos - <?php echo htmlspecialchars($proyecto['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <?php if ($mensajeExito !== ''): ?>
        <div class="toast-mensaje"><?php echo $mensajeExito; ?></div>
    <?php endif; ?>
    <?php if ($mensajeError !== ''): ?>
        <div class="toast-mensaje toast-error"><?php echo $mensajeError; ?></div>
    <?php endif; ?>

    <div class="panel-container">
        <a href="proyecto_detalle.php?id=<?php echo $proyecto_id; ?>" class="volver-link">← Volver al proyecto</a>
        <h1>Fotos de <?php echo htmlspecialchars($proyecto['nombre']); ?></h1>

        <div class="fotos-toolbar">
            <input type="search" id="buscar-album" class="buscar-albumes" placeholder="Buscar nombres de álbumes">
            <button type="button" class="btn-crear-album" onclick="document.getElementById('panel-crear-album').classList.toggle('mostrar')">+ Crear álbum</button>
        </div>

        <form id="panel-crear-album" class="crear-album-panel" action="php/crear_album.php" method="POST">
            <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($proyecto_id); ?>">
            <input type="text" name="nombre_album" placeholder="Nombre del nuevo álbum" required>
            <button type="submit">Crear</button>
        </form>

        <div class="fotos-resumen">
            <?php echo textoCantidad(count($albumes), 'álbum', 'álbumes'); ?> · <?php echo textoCantidad(count($fotos), 'foto', 'fotos'); ?>
        </div>

        <?php if (count($albumesVista) === 0): ?>
            <p class="categoria-vacia">Aún no hay álbumes. Crea uno para empezar a subir fotos.</p>
        <?php else: ?>
            <div class="albumes-grid">
                <?php foreach ($albumesVista as $av): ?>
                    <div class="album-card" data-nombre="<?php echo htmlspecialchars($av['nombre']); ?>" onclick="abrirAlbum('<?php echo $av['clave']; ?>')">
                        <div class="album-portada">
                            <?php if ($av['portada']): ?>
                                <img src="<?php echo htmlspecialchars($av['portada']['ruta_archivo']); ?>" alt="<?php echo htmlspecialchars($av['nombre']); ?>">
                            <?php else: ?>
                                📷
                            <?php endif; ?>
                        </div>
                        <div class="album-info">
                            <span class="album-nombre"><?php echo htmlspecialchars($av['nombre']); ?></span>
                            <span class="album-cantidad"><?php echo textoCantidad(count($av['fotos']), 'foto', 'fotos'); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Ventana emergente: un bloque por álbum, solo se muestra el elegido -->
    <div id="modal-fondo" class="modal-fondo" onclick="if (event.target === this) cerrarAlbum()">
        <div class="modal-ventana">
            <button type="button" class="modal-cerrar" onclick="cerrarAlbum()" aria-label="Cerrar">✕</button>

            <?php foreach ($albumesVista as $av): ?>
                <div class="album-contenido" id="album-<?php echo $av['clave']; ?>">
                    <h2 class="modal-titulo"><?php echo htmlspecialchars($av['nombre']); ?></h2>
                    <div class="modal-subtitulo"><?php echo textoCantidad(count($av['fotos']), 'foto', 'fotos'); ?></div>

                    <div class="modal-acciones">
                        <form action="php/subir_foto.php" method="POST" enctype="multipart/form-data" class="modal-subir">
                            <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($proyecto_id); ?>">
                            <input type="hidden" name="album_id" value="<?php echo $av['es_sin'] ? '' : $av['clave']; ?>">
                            <input type="file" name="foto" accept="image/*" required>
                            <button type="submit">Subir foto</button>
                        </form>

                        <?php if (!$av['es_sin']): ?>
                            <button type="button" class="btn-renombrar" onclick="alternarRenombrar('<?php echo $av['clave']; ?>')">✏ Renombrar</button>
                            <a href="php/eliminar_album.php?id=<?php echo $av['clave']; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="btn-eliminar-categoria" onclick="return confirm('¿Eliminar el álbum «<?php echo htmlspecialchars($av['nombre']); ?>»? Esto también borrará TODAS las fotos que contiene. Esta acción no se puede deshacer.');">🗑 Eliminar álbum</a>
                        <?php endif; ?>
                    </div>

                    <?php if (!$av['es_sin']): ?>
                        <form id="renombrar-<?php echo $av['clave']; ?>" class="modal-renombrar" action="php/renombrar_album.php" method="POST">
                            <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($proyecto_id); ?>">
                            <input type="hidden" name="album_id" value="<?php echo $av['clave']; ?>">
                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($av['nombre']); ?>" required>
                            <button type="submit">Guardar nombre</button>
                        </form>
                    <?php endif; ?>

                    <?php if (count($av['fotos']) === 0): ?>
                        <p class="categoria-vacia">Este álbum todavía no tiene fotos.</p>
                    <?php else: ?>
                        <div class="fotos-galeria modal-galeria">
                            <?php foreach ($av['fotos'] as $foto): ?>
                                <?php $esPortada = $av['portada'] && (int)$av['portada']['id'] === (int)$foto['id']; ?>
                                <div class="foto-item">
                                    <a href="<?php echo htmlspecialchars($foto['ruta_archivo']); ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($foto['ruta_archivo']); ?>" alt="<?php echo htmlspecialchars($foto['nombre_archivo']); ?>" class="foto-miniatura" loading="lazy">
                                    </a>
                                    <?php if ($esPortada && !$av['es_sin']): ?>
                                        <span class="insignia-portada">Portada</span>
                                    <?php endif; ?>
                                    <div class="foto-info">
                                        <span class="foto-fecha"><?php echo date('d/m/Y', strtotime($foto['fecha_subida'])); ?></span>
                                        <?php if (!$av['es_sin'] && !$esPortada): ?>
                                            <a href="php/establecer_portada.php?album_id=<?php echo $av['clave']; ?>&foto_id=<?php echo $foto['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>" class="btn-descargar" title="Usar como portada">★</a>
                                        <?php endif; ?>
                                        <a href="<?php echo htmlspecialchars($foto['ruta_archivo']); ?>" download class="btn-descargar" title="Descargar">⬇</a>
                                        <a href="php/eliminar_foto.php?id=<?php echo $foto['id']; ?>&proyecto_id=<?php echo $proyecto_id; ?>&album_id=<?php echo urlencode($av['clave']); ?>" class="btn-eliminar" title="Eliminar" onclick="return confirm('¿Eliminar esta foto?');">✗</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const proyectoId = <?php echo json_encode($proyecto_id); ?>;
        const albumAbrir = <?php echo json_encode($album_abrir); ?>;

        function abrirAlbum(clave) {
            const contenido = document.getElementById('album-' + clave);
            if (!contenido) return;

            document.querySelectorAll('.album-contenido').forEach(c => c.style.display = 'none');
            contenido.style.display = 'block';

            document.getElementById('modal-fondo').classList.add('abierto');
            document.body.style.overflow = 'hidden';
        }

        function cerrarAlbum() {
            const fondo = document.getElementById('modal-fondo');
            if (!fondo.classList.contains('abierto')) return;

            fondo.classList.remove('abierto');
            document.body.style.overflow = '';
            // Limpia la URL para que al recargar no se vuelva a abrir el álbum
            history.replaceState(null, '', 'proyecto_fotos.php?proyecto_id=' + encodeURIComponent(proyectoId));
        }

        function alternarRenombrar(clave) {
            document.getElementById('renombrar-' + clave).classList.toggle('mostrar');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') cerrarAlbum();
        });

        document.getElementById('buscar-album').addEventListener('input', function () {
            const texto = this.value.toLowerCase();
            document.querySelectorAll('.album-card').forEach(function (card) {
                card.style.display = card.dataset.nombre.toLowerCase().includes(texto) ? '' : 'none';
            });
        });

        document.querySelectorAll('.toast-mensaje').forEach(function (t) {
            setTimeout(function () { t.style.display = 'none'; }, 3500);
        });

        if (albumAbrir !== '') {
            abrirAlbum(albumAbrir);
        }
    </script>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_proveedores.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM proveedores WHERE id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    header('Location: admin_proveedores.php');
    exit;
}

$volver = $_GET['volver'] ?? 'admin_proveedores.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar <?php echo htmlspecialchars($p['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>
<body>
    <div class="panel-container">
        <a href="admin_proveedor_detalle.php?id=<?php echo $p['id']; ?>&volver=<?php echo urlencode($volver); ?>" class="volver-link">← Volver al detalle</a>
        <h1>Editar proveedor</h1>

        <form action="php/actualizar_proveedor.php" method="POST" class="form-proyecto">
            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
            <input type="hidden" name="volver" value="<?php echo htmlspecialchars($volver); ?>">

            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($p['nombre']); ?>" required>

            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($p['direccion'] ?? ''); ?>">

            <div id="mapa-proveedor" style="height: 300px; border-radius: 10px; margin-top: 10px;"></div>
            <p style="font-size: 12px; color: #999; margin-top: 8px;">Haz clic en el mapa para actualizar la ubicación.</p>

            <input type="hidden" id="latitud" name="latitud" value="<?php echo htmlspecialchars($p['latitud'] ?? ''); ?>">
            <input type="hidden" id="longitud" name="longitud" value="<?php echo htmlspecialchars($p['longitud'] ?? ''); ?>">

            <label for="rnc_cedula">RNC o Cédula</label>
            <input type="text" id="rnc_cedula" name="rnc_cedula" value="<?php echo htmlspecialchars($p['rnc_cedula'] ?? ''); ?>">

            <label for="telefono1">Teléfono 1</label>
            <input type="text" id="telefono1" name="telefono1" value="<?php echo htmlspecialchars($p['telefono1'] ?? ''); ?>">

            <label for="telefono2">Teléfono 2</label>
            <input type="text" id="telefono2" name="telefono2" value="<?php echo htmlspecialchars($p['telefono2'] ?? ''); ?>">

            <label for="rubro">Rubro</label>
            <input type="text" id="rubro" name="rubro" value="<?php echo htmlspecialchars($p['rubro'] ?? ''); ?>">

            <h3 style="margin-top: 25px; font-size: 15px; color: #1a1a2e;">Referencia</h3>
            <label for="referencia_nombre">Nombre</label>
            <input type="text" id="referencia_nombre" name="referencia_nombre" value="<?php echo htmlspecialchars($p['referencia_nombre'] ?? ''); ?>">

            <label for="referencia_telefono">Teléfono</label>
            <input type="text" id="referencia_telefono" name="referencia_telefono" value="<?php echo htmlspecialchars($p['referencia_telefono'] ?? ''); ?>">

            <button type="submit">Guardar cambios</button>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const latInicial = <?php echo $p['latitud'] ? $p['latitud'] : 19.4517; ?>;
        const lngInicial = <?php echo $p['longitud'] ? $p['longitud'] : -70.6970; ?>;

        const mapa = L.map('mapa-proveedor').setView([latInicial, lngInicial], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapa);

       let marcador = null;
       
       <?php if ($p['latitud'] && $p['longitud']): ?>
        marcador = L.marker([latInicial, lngInicial]).addTo(mapa);
        <?php endif; ?>
        
        mapa.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lng;

            if (marcador) {
                marcador.setLatLng(e.latlng);
            } else {
                marcador = L.marker(e.latlng).addTo(mapa);
            }

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data.display_name) {
                        document.getElementById('direccion').value = data.display_name;
                    }
                });
        });
    </script>
</body>
</html>
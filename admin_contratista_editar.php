<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_contratistas.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM contratistas WHERE id = ?');
$stmt->execute([$id]);
$c = $stmt->fetch();

if (!$c) {
    header('Location: admin_contratistas.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar <?php echo htmlspecialchars($c['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>
<body>
    <div class="panel-container">
        <a href="admin_contratista_detalle.php?id=<?php echo $c['id']; ?>" class="volver-link">← Volver al detalle</a>
        <h1>Editar contratista</h1>

        <form action="php/actualizar_contratista.php" method="POST" enctype="multipart/form-data" class="form-proyecto">
            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
            <input type="hidden" name="foto_actual" value="<?php echo htmlspecialchars($c['foto'] ?? ''); ?>">

            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($c['nombre']); ?>" required>

            <label for="telefono">Teléfono</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($c['telefono'] ?? ''); ?>">

            <label for="cedula">Cédula</label>
            <input type="text" id="cedula" name="cedula" value="<?php echo htmlspecialchars($c['cedula'] ?? ''); ?>">

            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($c['direccion'] ?? ''); ?>">

            <div id="mapa-contratista" style="height: 300px; border-radius: 10px; margin-top: 10px;"></div>
            <p style="font-size: 12px; color: #999; margin-top: 8px;">Haz clic en el mapa para actualizar la ubicación.</p>

            <input type="hidden" id="latitud" name="latitud" value="<?php echo htmlspecialchars($c['latitud'] ?? ''); ?>">
            <input type="hidden" id="longitud" name="longitud" value="<?php echo htmlspecialchars($c['longitud'] ?? ''); ?>">

            <h3 style="margin-top: 25px; font-size: 15px; color: #1a1a2e;">Referencia 1</h3>
            <label for="ref1_nombre">Nombre</label>
            <input type="text" id="ref1_nombre" name="ref1_nombre" value="<?php echo htmlspecialchars($c['ref1_nombre'] ?? ''); ?>">

            <label for="ref1_telefono">Teléfono</label>
            <input type="text" id="ref1_telefono" name="ref1_telefono" value="<?php echo htmlspecialchars($c['ref1_telefono'] ?? ''); ?>">

            <h3 style="margin-top: 25px; font-size: 15px; color: #1a1a2e;">Referencia 2</h3>
            <label for="ref2_nombre">Nombre</label>
            <input type="text" id="ref2_nombre" name="ref2_nombre" value="<?php echo htmlspecialchars($c['ref2_nombre'] ?? ''); ?>">

            <label for="ref2_telefono">Teléfono</label>
            <input type="text" id="ref2_telefono" name="ref2_telefono" value="<?php echo htmlspecialchars($c['ref2_telefono'] ?? ''); ?>">

            <label for="foto">Foto (deja vacío para mantener la actual)</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="submit">Guardar cambios</button>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const latInicial = <?php echo $c['latitud'] ? $c['latitud'] : 19.4517; ?>;
        const lngInicial = <?php echo $c['longitud'] ? $c['longitud'] : -70.6970; ?>;

        const mapa = L.map('mapa-contratista').setView([latInicial, lngInicial], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapa);

        let marcador = <?php echo ($c['latitud'] && $c['longitud']) ? "L.marker([$latInicial, $lngInicial]).addTo(mapa)" : 'null'; ?>;

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
<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo contratista</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>
<body>
    <div class="panel-container">
        <a href="admin_contratistas.php" class="volver-link">← Volver a contratistas</a>
        <h1>Nuevo contratista</h1>

        <form action="php/crear_contratista.php" method="POST" enctype="multipart/form-data" class="form-proyecto">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="telefono">Teléfono</label>
            <input type="text" id="telefono" name="telefono">

            <label for="cedula">Cédula o RNC</label>
            <input type="text" id="cedula" name="cedula">

            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" placeholder="Escribe la dirección o selecciónala en el mapa">

            <div id="mapa-contratista" style="height: 300px; border-radius: 10px; margin-top: 10px;"></div>
            <p style="font-size: 12px; color: #999; margin-top: 8px;">Haz clic en el mapa para marcar la ubicación exacta.</p>

            <input type="hidden" id="latitud" name="latitud">
            <input type="hidden" id="longitud" name="longitud">

            <?php if (!isset($_GET['proyecto_id'])): ?>
                <label for="proyecto_id">Enlazar a un proyecto (opcional)</label>
                <select id="proyecto_id" name="proyecto_id">
                    <option value="">-- Ninguno por ahora --</option>
                    <?php
                    $stmtP = $pdo->query('SELECT proyectos.id, proyectos.nombre, empresas.nombre AS empresa_nombre FROM proyectos JOIN empresas ON proyectos.empresa_id = empresas.id ORDER BY empresas.nombre, proyectos.nombre');
                    foreach ($stmtP->fetchAll() as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['empresa_nombre'] . ' — ' . $p['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($_GET['proyecto_id']); ?>">
            <?php endif; ?>

            <h3 style="margin-top: 25px; font-size: 15px; color: #1a1a2e;">Referencia 1</h3>
            <label for="ref1_nombre">Nombre</label>
            <input type="text" id="ref1_nombre" name="ref1_nombre">

            <label for="ref1_telefono">Teléfono</label>
            <input type="text" id="ref1_telefono" name="ref1_telefono">

            <h3 style="margin-top: 25px; font-size: 15px; color: #1a1a2e;">Referencia 2</h3>
            <label for="ref2_nombre">Nombre</label>
            <input type="text" id="ref2_nombre" name="ref2_nombre">

            <label for="ref2_telefono">Teléfono</label>
            <input type="text" id="ref2_telefono" name="ref2_telefono">

            <label for="foto">Foto</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="submit">Crear contratista</button>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const mapa = L.map('mapa-contratista').setView([19.4517, -70.6970], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapa);

        let marcador = null;

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
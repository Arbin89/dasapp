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
</body>
</html>
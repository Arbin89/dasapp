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
    <title>Nuevo proveedor</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="admin_proveedores.php" class="volver-link">← Volver a proveedores</a>
        <h1>Nuevo proveedor</h1>

        <form action="php/crear_proveedor.php" method="POST" class="form-proyecto">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion">

            <label for="rnc_cedula">RNC o Cédula</label>
            <input type="text" id="rnc_cedula" name="rnc_cedula">

            <label for="telefono1">Teléfono 1</label>
            <input type="text" id="telefono1" name="telefono1">

            <label for="telefono2">Teléfono 2</label>
            <input type="text" id="telefono2" name="telefono2">

            <label for="rubro">Rubro</label>
            <input type="text" id="rubro" name="rubro" placeholder="Ej: Materiales eléctricos, Ferretería, etc.">

            <h3 style="margin-top: 25px; font-size: 15px; color: #1a1a2e;">Referencia</h3>
            <label for="referencia_nombre">Nombre</label>
            <input type="text" id="referencia_nombre" name="referencia_nombre">

            <label for="referencia_telefono">Teléfono</label>
            <input type="text" id="referencia_telefono" name="referencia_telefono">

            <?php if (!isset($_GET['proyecto_id'])): ?>
                <label for="proyecto_id">Enlazar a un proyecto (opcional)</label>
                <select id="proyecto_id" name="proyecto_id">
                    <option value="">-- Ninguno por ahora --</option>
                    <?php
                    $stmtP = $pdo->query('SELECT proyectos.id, proyectos.nombre, empresas.nombre AS empresa_nombre FROM proyectos JOIN empresas ON proyectos.empresa_id = empresas.id ORDER BY empresas.nombre, proyectos.nombre');
                    foreach ($stmtP->fetchAll() as $pr): ?>
                        <option value="<?php echo $pr['id']; ?>"><?php echo htmlspecialchars($pr['empresa_nombre'] . ' — ' . $pr['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="proyecto_id" value="<?php echo htmlspecialchars($_GET['proyecto_id']); ?>">
            <?php endif; ?>

            <button type="submit">Crear proveedor</button>
        </form>
    </div>
</body>
</html>
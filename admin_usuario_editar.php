<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'php/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin_usuarios.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, nombre, email, rol FROM usuarios WHERE id = ?');
$stmt->execute([$id]);
$u = $stmt->fetch();

if (!$u) {
    header('Location: admin_usuarios.php');
    exit;
}

$stmtRoles = $pdo->query('SELECT DISTINCT rol FROM usuarios ORDER BY rol ASC');
$roles = $stmtRoles->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar <?php echo htmlspecialchars($u['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel.css">
</head>
<body>
    <div class="panel-container">
        <a href="admin_usuario_detalle.php?id=<?php echo $u['id']; ?>" class="volver-link">← Volver al detalle</a>
        <h1>Editar usuario</h1>

        <form action="php/actualizar_usuario.php" method="POST" class="form-proyecto">
            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">

            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($u['nombre']); ?>" required>

            <label for="email">Correo</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" required>

            <label for="password">Nueva contraseña (deja vacío para mantener la actual)</label>
            <input type="password" id="password" name="password">

            <label for="rol">Rol</label>
            <input type="text" id="rol" name="rol" list="roles-lista" value="<?php echo htmlspecialchars($u['rol']); ?>" required>
            <datalist id="roles-lista">
                <?php foreach ($roles as $r): ?>
                    <option value="<?php echo htmlspecialchars($r['rol']); ?>">
                <?php endforeach; ?>
            </datalist>

            <button type="submit">Guardar cambios</button>
        </form>
    </div>
</body>
</html>
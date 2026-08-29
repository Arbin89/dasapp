<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'php/config.php';

$stmt = $pdo->query('SELECT * FROM empresas');
$empresas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Selecciona una empresa</title>
     <link rel="icon" type="image/png" href="img/favicon.png">
     <link rel="stylesheet" href="css/style.css">
     <link rel="stylesheet" href="css/panel.css">
</head>
<body style="font-family: Arial; padding: 40px;">
    <div class="panel-container">
    <a href="php/logout.php" class="logout-link">Cerrar sesión</a>
        <h1>Selecciona una empresa</h1>
         <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>

         <div class="empresas-grid">
            <?php foreach ($empresas as $empresa): ?>
                <div class="empresa-card">
                 <img src="img/empresas/<?php echo htmlspecialchars($empresa['logo']); ?>" alt="<?php echo htmlspecialchars($empresa['nombre']); ?>" class="empresa-logo">
                    <h2><?php echo htmlspecialchars($empresa['nombre']); ?></h2>
                    <a href="proyectos.php?empresa_id=<?php echo $empresa['id']; ?>" class="btn-seleccionar">Seleccionar</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
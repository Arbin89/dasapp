<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesion</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <div class ="login-box"> 
            <h1>Bienvenido</h1>
            <?php if (isset($_GET['error'])): ?>
                <p style="color: red; margin-bottom: 15px;">
                    <?php echo $_GET['error'] === 'vacio' ? 'Completa todos los campos.' : 'Correo o contraseña incorrectos.'; ?>
                </p>
            <?php endif; ?>

            <form action="php/login_process.php" method="POST">
                <label for="email">Correo</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>


                <button type="submit">Iniciar sesion</button>
            </form>
        </div>
    </div>
</body>
</html>
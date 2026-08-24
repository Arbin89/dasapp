<?php
require_once 'crypto.php';

$password = $_GET['pw'] ?? '';

if ($password === '') {
    echo 'Usa la URL así: generar_password.php?pw=tu_contraseña';
    exit;
}

$encriptado = encriptar($password);

echo "Contraseña: " . htmlspecialchars($password) . "<br>";
echo "Valor para pegar en phpMyAdmin:<br>";
echo "<strong>" . htmlspecialchars($encriptado) . "</strong>";
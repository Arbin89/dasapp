<?php require_once 'crypto.php'; $password = '123456'; $encriptado = encriptar($password); 
$desencriptado = desencriptar($encriptado); echo "Original: $password<br>"; 
echo "Encriptado: $encriptado<br>"; echo "Desencriptado de vuelta: $desencriptado<br>";
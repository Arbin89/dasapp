<?php define('CLAVE_SECRETA', 'CambiaEstoPorUnaClaveLargaYUnica2024'); 
define('METODO_CIFRADO', 'AES-256-CBC'); 
function encriptar($texto) { $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(METODO_CIFRADO)); 
$encriptado = openssl_encrypt($texto, METODO_CIFRADO, CLAVE_SECRETA, 0, $iv); 
return base64_encode($iv) . ':' . $encriptado; } 
function desencriptar($textoEncriptado) 
{ list($iv_b64, $encriptado) = explode(':', $textoEncriptado, 2); $iv = base64_decode($iv_b64); 
return openssl_decrypt($encriptado, METODO_CIFRADO, CLAVE_SECRETA, 0, $iv); } 
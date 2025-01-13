<?php
// Establecer los parámetros de la conexión
$host = 'localhost'; // o la IP de tu servidor de base de datos
$usuario = 'root'; // Tu usuario de MySQL
$contraseña = ''; // Tu contraseña de MySQL
$nombre_base_datos = 'financiamiento'; // El nombre de tu base de datos

// Crear la conexión
$conn = new mysqli($host, $usuario, $contraseña, $nombre_base_datos);
try {
    // Crear una conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$nombre_base_datos", $usuario , $contraseña);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Esto ayuda a depurar errores
} catch (PDOException $e) {
    // Si ocurre un error, muestra el mensaje
    echo 'Error de conexión: ' . $e->getMessage();
}
// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres para evitar problemas con los caracteres especiales
$conn->set_charset("utf8");

?>

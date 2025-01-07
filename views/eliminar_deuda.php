<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    // Si no está autenticado, redirige a la página de login
    header("Location: login.php");
    exit(); // Detiene la ejecución después de la redirección
}

$email = $_SESSION['email'];

// Conectar a la base de datos
include('conexion.php'); // Suponiendo que la conexión está en este archivo

// Obtener el id_deuda desde la URL
$id_deuda = $_GET['id'] ?? ''; // Si no hay id, será una cadena vacía

// Verifica si el id_deuda está presente
if ($id_deuda != '') {
    // Consulta para obtener la deuda antes de eliminarla (opcional, por si deseas mostrarla o verificarla)
    $sql_check = "SELECT id_deuda FROM deuda WHERE id_deuda = '$id_deuda' AND id_usuario = (SELECT id_usuario FROM usuarios WHERE email = '$email')";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // Si la deuda existe, proceder a eliminarla
        $sql_delete = "DELETE FROM deuda WHERE id_deuda = '$id_deuda' AND id_usuario = (SELECT id_usuario FROM usuarios WHERE email = '$email')";
        if ($conn->query($sql_delete) === TRUE) {
            // Redirige a la página de deudas con un mensaje de éxito
            header("Location: deudas.php?success=1");
            exit();
        } else {
            // Si hubo un error al eliminar
            header("Location: deudas.php?error=1");
            exit();
        }
    } else {
        // Si no se encuentra la deuda
        header("Location: deudas.php?error=2");
        exit();
    }
} else {
    // Si no se proporciona un id_deuda
    header("Location: deudas.php?error=3");
    exit();
}

// Cerrar la conexión
$conn->close();
?>

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

// Consulta para obtener el id del presupuesto a eliminar
if (isset($_GET['id'])) {
    $id_presupuesto = $_GET['id'];

    // Verifica que el presupuesto pertenece al usuario autenticado
    $sql_check = "SELECT * FROM presupuesto WHERE id_presupuesto = '$id_presupuesto' AND id_usuario = (SELECT id_usuario FROM usuarios WHERE email = '$email')";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // Si el presupuesto existe, procede a eliminarlo
        $sql_delete = "DELETE FROM presupuesto WHERE id_presupuesto = '$id_presupuesto'";

        if ($conn->query($sql_delete) === TRUE) {
            // Redirige al usuario a la página de presupuestos después de eliminar
            header("Location: presupuestos.php");
            exit();
        } else {
            echo "<script>alert('Error al eliminar el presupuesto: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Presupuesto no encontrado o no autorizado para eliminarlo');</script>";
    }
} else {
    echo "<script>alert('ID de presupuesto no válido');</script>";
}

// Cerrar la conexión
$conn->close();
?>

<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Conectar a la base de datos
include('conexion.php');

// Obtener el ID de la inversión a eliminar
if (isset($_GET['id'])) {
    $id_inversion = $_GET['id'];

    // Eliminar la inversión
    $sqlDelete = "DELETE FROM inversion WHERE id_inversion = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $id_inversion);

    if ($stmtDelete->execute()) {
        echo "<script>alert('Inversión eliminada correctamente.'); window.location.href = 'inversiones.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar la inversión.'); window.location.href = 'inversiones.php';</script>";
    }

    $stmtDelete->close();
}

$conn->close();
?>

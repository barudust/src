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

// Obtener el id del usuario basado en su email
$sql = "SELECT id_usuario, nombre FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_usuario = $row['id_usuario'];
    $nombre = $row['nombre'];
} else {
    echo "Error: Usuario no encontrado.";
    exit();
}

// Verificar si el ID del adeudo se ha pasado como parámetro
if (isset($_GET['id'])) {
    $id_adeudo = $_GET['id'];

    // Verificar si el adeudo pertenece al usuario
    $sql_adeudo = "SELECT * FROM adeudo WHERE id_adeudo = '$id_adeudo' AND id_usuario = '$id_usuario'";
    $result_adeudo = $conn->query($sql_adeudo);

    if ($result_adeudo->num_rows > 0) {
        // Eliminar el adeudo de la base de datos
        $sql_delete = "DELETE FROM adeudo WHERE id_adeudo = '$id_adeudo'";
        if ($conn->query($sql_delete) === TRUE) {
            // Redirigir a la página de adeudos después de la eliminación
            header("Location: adeudos.php");
            exit();
        } else {
            echo "Error al eliminar el adeudo: " . $conn->error;
        }
    } else {
        echo "Error: Adeudo no encontrado o no pertenece a este usuario.";
        exit();
    }
} else {
    echo "Error: No se ha proporcionado un ID de adeudo.";
    exit();
}

?>

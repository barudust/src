<?php
include('../conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];

    // Preparar la consulta para eliminar el usuario
    $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir después de eliminar
        header("Location: index.php");
        exit();
    } else {
        echo "Error al eliminar el usuario.";
    }
} else {
    echo "ID de usuario no recibido.";
}
?>

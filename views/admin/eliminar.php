<?php
if (isset($_GET['id'])) {
    $idEliminar = $_GET['id'];

    // Conexión a la base de datos
    include('../conexion.php');

    // Consulta para eliminar el usuario
    $sqlEliminar = "DELETE FROM usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sqlEliminar);
    $stmt->bind_param("i", $idEliminar);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirige de vuelta a la página anterior
        header("Location: index.php");
        exit();
    }

    // Cierra la conexión
    $stmt->close();
    $conn->close();
} else {
    // Si no hay ID proporcionado, redirige al index.php
    header("Location: index.php");
    exit();
}
?>

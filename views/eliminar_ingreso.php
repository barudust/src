<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit(); // Detiene la ejecución después de la redirección
}

// Conectar a la base de datos
include('conexion.php'); // Incluye la conexión a la base de datos

// Verifica si se recibió un ID de transacción
if (isset($_GET['id_transaccion'])) {
    $id_transaccion = intval($_GET['id_transaccion']); // Asegúrate de que sea un número entero

    // Consulta para eliminar la transacción
    $sql_eliminar = "DELETE FROM transaccion WHERE id_transaccion = ?";
    $stmt = $conn->prepare($sql_eliminar); // Preparar la consulta para evitar inyección SQL
    $stmt->bind_param('i', $id_transaccion);

    if ($stmt->execute()) {
        // Si la consulta se ejecutó correctamente
        echo "<script>
                alert('Ingreso eliminado correctamente.');
                window.location.href = 'ingresos.php';
              </script>";
    } else {
        // Si hubo un error en la ejecución
        echo "<script>
                alert('Error al eliminar el ingreso.');
                window.location.href = 'ingresos.php';
              </script>";
    }

    $stmt->close(); // Cierra la declaración preparada
} else {
    // Si no se recibió un ID válido
    echo "<script>
            alert('ID de ingreso no válido.');
            window.location.href = 'ingresos.php';
          </script>";
}

// Cierra la conexión
$conn->close();
?>

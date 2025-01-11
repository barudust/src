<?php
// Incluir el archivo de conexión a la base de datos
include('conexion.php'); // Asegúrate de que la ruta sea correcta

// Verificar si la sesión está iniciada
session_start();
if (isset($_SESSION['email'])) {
    // Obtener el email del usuario desde la sesión
    $email = $_SESSION['email'];

    // Preparar la consulta para obtener el id_usuario y el nombre
    $stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se obtuvo el nombre y el id_usuario correctamente
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_usuario = $row['id_usuario'];
        $nombre = $row['nombre'];
    } else {
        // Si no se encuentra el usuario, asigna valores por defecto
        $nombre = "Usuario";
        $id_usuario = 0;
    }

    // Obtener la suma de los montos y la tasa de interés de la tabla deudas para el id_usuario
    $sql_deuda = "SELECT SUM(monto) AS total_deuda, AVG(tasa_interes) AS tasa_interes FROM deuda WHERE id_usuario = ?";
    $stmt_deuda = $conn->prepare($sql_deuda);
    $stmt_deuda->bind_param("i", $id_usuario); // 'i' para entero (id_usuario)
    $stmt_deuda->execute();
    $result_deuda = $stmt_deuda->get_result();

    // Verificar si se obtuvo la suma y la tasa correctamente
    if ($result_deuda->num_rows > 0) {
        $row_deuda = $result_deuda->fetch_assoc();
        $total_deuda = $row_deuda['total_deuda'];
        $tasa_interes = $row_deuda['tasa_interes'];
        
        // Si existe una tasa de interés, multiplicar el total de deuda por la tasa
        if ($tasa_interes) {
            $total_deuda_con_interes = $total_deuda * (1 + ($tasa_interes / 100)); // Asumiendo tasa en porcentaje
        } else {
            $total_deuda_con_interes = $total_deuda; // Si no hay tasa de interés, solo usamos la deuda total
        }
    } else {
        // Si no hay deudas, asigna 0
        $total_deuda = 0;
        $total_deuda_con_interes = 0; // Si no hay deudas, la deuda con interés es 0
    }
} else {
    // Si la sesión no está iniciada, asignar valores por defecto o redirigir al login
    $nombre = "Usuario";
    $id_usuario = 0;
    $total_deuda = 0; // Asignar 0 si no hay sesión activa
    $total_deuda_con_interes = 0; // Si no hay sesión, la deuda con interés es 0
    // Puedes redirigir al usuario al login si es necesario:
    // header("Location: login.php");
    // exit();
}

// Cerrar la conexión
$conn->close();
?>

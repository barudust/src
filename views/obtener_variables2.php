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

// Obtener la fecha actual
$hoy = date('Y-m-d');

// Calcular las fechas de inicio y fin de las quincenas actuales
$diaActual = date('j');
$mesActual = date('n');
$anioActual = date('Y');

if ($diaActual <= 15) {
    // Primera quincena
    $inicioQuincenaActual = date('Y-m-01');
    $finQuincenaActual = date('Y-m-15');
    $inicioQuincenaSiguiente = date('Y-m-16');
    $finQuincenaSiguiente = date('Y-m-t'); // Último día del mes
} else {
    // Segunda quincena
    $inicioQuincenaActual = date('Y-m-16');
    $finQuincenaActual = date('Y-m-t'); // Último día del mes
    $inicioQuincenaSiguiente = date('Y-m-d', strtotime('first day of next month'));
    $finQuincenaSiguiente = date('Y-m-15', strtotime('next month'));
}

// Consulta para obtener el nombre del usuario por el email
$sql = "SELECT nombre FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql);

// Verifica si se obtuvo el nombre correctamente
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre']; // Asume que el campo se llama 'nombre'
} else {
    $nombre = "Usuario"; // En caso de error, muestra "Usuario"
}

// Consulta para obtener los ingresos
$sql_ingresos = "SELECT id_transaccion, fecha, descripcion, monto, categoria FROM transaccion WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE email = '$email') AND tipo = 'Ingreso' ORDER BY fecha DESC";
$ingresos_result = $conn->query($sql_ingresos);

// Verifica si la consulta se ejecutó correctamente
if (!$ingresos_result) {
    echo "Error en la consulta: " . $conn->error;
    exit();
}

// Inicializar variables para los totales
$totalIngresoQuincenaActual = 0;
$totalIngresoTotal = 0;

// Procesar los ingresos
while ($row = $ingresos_result->fetch_assoc()) {
    $fechaIngreso = $row['fecha'];
    $montoIngreso = $row['monto'];

    // Calcular el total de ingresos en la quincena actual
    if ($fechaIngreso >= $inicioQuincenaActual && $fechaIngreso <= $finQuincenaActual) {
        $totalIngresoQuincenaActual += $montoIngreso;
    }

    // Calcular el total de ingresos en general (sin importar la quincena)
    $totalIngresoTotal += $montoIngreso;
}

// Cerrar la conexión
$conn->close();



// Mostrar los resultados
echo "Total de ingresos en la quincena actual: $totalIngresoQuincenaActual MXN<br>";
echo "Total de ingresos totales: $totalIngresoTotal MXN<br>";
?>

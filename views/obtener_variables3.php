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

// Obtener el ID del usuario
$sql = "SELECT id_usuario FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$id_usuario = $row['id_usuario'];

// Obtener la fecha actual
$hoy = date('Y-m-d');

// Calcular las fechas de inicio y fin de las quincenas actuales
$diaActual = date('j');
if ($diaActual <= 15) {
    // Primera quincena
    $inicioQuincenaActual = date('Y-m-01');
    $finQuincenaActual = date('Y-m-15');
    $inicioQuincenaSiguiente = date('Y-m-16');
    $finQuincenaSiguiente = date('Y-m-t');
} else {
    // Segunda quincena
    $inicioQuincenaActual = date('Y-m-16');
    $finQuincenaActual = date('Y-m-t');
    $inicioQuincenaSiguiente = date('Y-m-d', strtotime('first day of next month'));
    $finQuincenaSiguiente = date('Y-m-15', strtotime('next month'));
}

// Sumar los adeudos en la quincena actual y en total
$totalAdeudoQuincenaActual = 0;
$totalAdeudoTotal = 0;

$sql_adeudos = "SELECT * FROM adeudo WHERE id_usuario = '$id_usuario'";
$result_adeudos = $conn->query($sql_adeudos);

while ($row = $result_adeudos->fetch_assoc()) {
    $monto = $row['monto'];
    $fechaVencimiento = $row['fecha_vencimiento'];

    // Sumar adeudos de la quincena actual
    if ($fechaVencimiento >= $inicioQuincenaActual && $fechaVencimiento <= $finQuincenaActual) {
        $totalAdeudoQuincenaActual += $monto;
    }

    // Sumar el total de adeudos
    $totalAdeudoTotal += $monto;
}

$conn->close();

// Mostrar los resultados
echo "Total de adeudos en la quincena actual: $totalAdeudoQuincenaActual MXN<br>";
echo "Total de adeudos totales: $totalAdeudoTotal MXN<br>";
?>

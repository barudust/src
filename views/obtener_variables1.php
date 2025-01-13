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

// Obtener el ID del usuario desde la sesión
$sqlUser = "SELECT id_usuario FROM usuarios WHERE email = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $email);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

$totalMontoDeudaQuincenaActual = 0;
$totalInteresDeudaQuincenaActual = 0;
$totalMontoDeudaQuincenaFutura = 0;
$totalInteresDeudaQuincenaFutura = 0;
$totalMontoDeudaTodas = 0;
$totalInteresDeudaTodas = 0;

if ($resultUser->num_rows > 0) {
    $userRow = $resultUser->fetch_assoc();
    $userId = $userRow['id_usuario'];

    // Obtener las deudas que inician y terminan en la quincena actual
    $sqlDeudasQuincenaActual = "SELECT SUM(monto) AS total, SUM(monto * (tasa_interes / 100)) AS total_interes 
                                FROM deuda 
                                WHERE id_usuario = ? AND fecha_inicio >= ? AND fecha_inicio <= ? AND fecha_fin >= ? AND fecha_fin <= ?";
    $stmtDeudasQuincenaActual = $conn->prepare($sqlDeudasQuincenaActual);
    $stmtDeudasQuincenaActual->bind_param("issss", $userId, $inicioQuincenaActual, $finQuincenaActual, $inicioQuincenaActual, $finQuincenaActual);
    $stmtDeudasQuincenaActual->execute();
    $resultDeudasQuincenaActual = $stmtDeudasQuincenaActual->get_result();

    if ($resultDeudasQuincenaActual->num_rows > 0) {
        $rowDeudasQuincenaActual = $resultDeudasQuincenaActual->fetch_assoc();
        $totalMontoDeudaQuincenaActual = $rowDeudasQuincenaActual['total'] ?? 0;
        $totalInteresDeudaQuincenaActual = $rowDeudasQuincenaActual['total_interes'] ?? 0;
    }
    $stmtDeudasQuincenaActual->close();

    // Obtener las deudas que inician en la quincena actual y terminan en una posterior
    $sqlDeudasQuincenaFutura = "SELECT SUM(monto) AS total, SUM(monto * (tasa_interes / 100)) AS total_interes 
                                FROM deuda 
                                WHERE id_usuario = ? AND fecha_inicio >= ? AND fecha_inicio <= ? AND fecha_fin > ?";
    $stmtDeudasQuincenaFutura = $conn->prepare($sqlDeudasQuincenaFutura);
    $stmtDeudasQuincenaFutura->bind_param("isss", $userId, $inicioQuincenaActual, $finQuincenaActual, $finQuincenaActual);
    $stmtDeudasQuincenaFutura->execute();
    $resultDeudasQuincenaFutura = $stmtDeudasQuincenaFutura->get_result();

    if ($resultDeudasQuincenaFutura->num_rows > 0) {
        $rowDeudasQuincenaFutura = $resultDeudasQuincenaFutura->fetch_assoc();
        $totalMontoDeudaQuincenaFutura = $rowDeudasQuincenaFutura['total'] ?? 0;
        $totalInteresDeudaQuincenaFutura = $rowDeudasQuincenaFutura['total_interes'] ?? 0;
    }
    $stmtDeudasQuincenaFutura->close();

    // Obtener todas las deudas (pasadas, actuales y futuras)
    $sqlTodasLasDeudas = "SELECT SUM(monto) AS total, SUM(monto * (tasa_interes / 100)) AS total_interes 
                          FROM deuda WHERE id_usuario = ?";
    $stmtTodasLasDeudas = $conn->prepare($sqlTodasLasDeudas);
    $stmtTodasLasDeudas->bind_param("i", $userId);
    $stmtTodasLasDeudas->execute();
    $resultTodasLasDeudas = $stmtTodasLasDeudas->get_result();

    if ($resultTodasLasDeudas->num_rows > 0) {
        $rowTodasLasDeudas = $resultTodasLasDeudas->fetch_assoc();
        $totalMontoDeudaTodas = $rowTodasLasDeudas['total'] ?? 0;
        $totalInteresDeudaTodas = $rowTodasLasDeudas['total_interes'] ?? 0;
    }
    $stmtTodasLasDeudas->close();
}

$conn->close();

// Sumar el total con los intereses para las deudas
$totalDeudaQuincenaActualConInteres = $totalMontoDeudaQuincenaActual + $totalInteresDeudaQuincenaActual;
$totalDeudaQuincenaFuturaConInteres = $totalMontoDeudaQuincenaFutura + $totalInteresDeudaQuincenaFutura;
$totalDeudaTodasConInteres = $totalMontoDeudaTodas + $totalInteresDeudaTodas;

// Mostrar los resultados
echo "Total de las deudas que inician y terminan en la quincena actual: $totalDeudaQuincenaActualConInteres MXN (incluye intereses)<br>";
echo "Total de las deudas que inician en la quincena actual y terminan en una posterior: $totalDeudaQuincenaFuturaConInteres MXN (incluye intereses)<br>";
echo "Total de todas las deudas (pasadas, presentes y futuras): $totalDeudaTodasConInteres MXN (incluye intereses)<br>";
?>

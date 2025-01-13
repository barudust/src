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
// Consulta para obtener los ingresos
$sql_ingresos = "SELECT id_transaccion, fecha, descripcion, monto, categoria FROM transaccion WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE email = '$email') AND tipo = 'Ingreso' ORDER BY fecha DESC";
$ingresos_result = $conn->query($sql_ingresos);

$totalMontoQuincenaActualSiguiente = 0;
$totalMontoQuincenaActual = 0;
$totalMontoGeneral = 0;
$totalMontoDeudaQuincenaActual = 0;
$totalInteresDeudaQuincenaActual = 0;
$totalMontoDeudaQuincenaFutura = 0;
$totalInteresDeudaQuincenaFutura = 0;
$totalMontoDeudaTodas = 0;
$totalInteresDeudaTodas = 0;
$totalIngresoQuincenaActual = 0;
$totalIngresoTotal = 0;
// Sumar los adeudos en la quincena actual y en total
$totalAdeudoQuincenaActual = 0;
$totalAdeudoTotal = 0;




if ($resultUser->num_rows > 0) {
    $userRow = $resultUser->fetch_assoc();
    $userId = $userRow['id_usuario'];
    $sql_adeudos = "SELECT * FROM adeudo WHERE id_usuario = ' $userId'";
    $result_adeudos = $conn->query($sql_adeudos);

    // Obtener inversiones que inician en esta quincena y terminan en la siguiente
    $sqlInicianActualTerminanSiguiente = "
        SELECT * 
        FROM inversion 
        WHERE id_usuario = ? 
        AND fecha_inicio BETWEEN ? AND ? 
        AND fecha_fin BETWEEN ? AND ?";
    $stmt1 = $conn->prepare($sqlInicianActualTerminanSiguiente);
    $stmt1->bind_param("issss", $userId, $inicioQuincenaActual, $finQuincenaActual, $inicioQuincenaSiguiente, $finQuincenaSiguiente);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $inversionesInicianActualTerminanSiguiente = $result1->fetch_all(MYSQLI_ASSOC);
    $stmt1->close();

    // Sumar los montos de las inversiones que inician en esta quincena y terminan en la siguiente
    foreach ($inversionesInicianActualTerminanSiguiente as $inversion) {
        $totalMontoQuincenaActualSiguiente += $inversion['monto'] + (isset($inversion['rendimiento']) ? $inversion['rendimiento'] : 0);
    }
    

    // Obtener inversiones que inician y terminan en la misma quincena actual
    $sqlInicianYTerminanActual = "
        SELECT * 
        FROM inversion 
        WHERE id_usuario = ? 
        AND fecha_inicio BETWEEN ? AND ? 
        AND fecha_fin BETWEEN ? AND ?";
    $stmt2 = $conn->prepare($sqlInicianYTerminanActual);
    $stmt2->bind_param("issss", $userId, $inicioQuincenaActual, $finQuincenaActual, $inicioQuincenaActual, $finQuincenaActual);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $inversionesInicianYTerminanActual = $result2->fetch_all(MYSQLI_ASSOC);
    $stmt2->close();

    // Sumar los montos de las inversiones que inician y terminan en la misma quincena, incluyendo el rendimiento
    foreach ($inversionesInicianYTerminanActual as $inversion) {
        $totalMontoQuincenaActual += $inversion['monto'] + (isset($inversion['rendimiento']) ? $inversion['rendimiento'] : 0);
    }

    // Obtener todas las inversiones (anteriores, actuales y futuras)
    $sqlTodasLasInversiones = "
        SELECT * 
        FROM inversion 
        WHERE id_usuario = ?";
    $stmt3 = $conn->prepare($sqlTodasLasInversiones);
    $stmt3->bind_param("i", $userId);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    $inversionesTodas = $result3->fetch_all(MYSQLI_ASSOC);
    $stmt3->close();

    // Sumar los montos de todas las inversiones (pasadas, actuales y futuras)
    foreach ($inversionesTodas as $inversion) {
        $totalMontoGeneral += $inversion['monto'] + (isset($inversion['rendimiento']) ? $inversion['rendimiento'] : 0);
    }

// DEUDAS
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

// Sumar el total con los intereses para las deudas
$totalDeudaQuincenaActualConInteres = $totalMontoDeudaQuincenaActual + $totalInteresDeudaQuincenaActual;
$totalDeudaQuincenaFuturaConInteres = $totalMontoDeudaQuincenaFutura + $totalInteresDeudaQuincenaFutura;
$totalDeudaTodasConInteres = $totalMontoDeudaTodas + $totalInteresDeudaTodas;

// Mostrar resultados para verificar
echo "Total de las inversiones que inician en esta quincena y terminan en la siguiente: $totalMontoQuincenaActualSiguiente MXN<br>";
echo "Total de las inversiones que inician y terminan en la misma quincena (con rendimiento): $totalMontoQuincenaActual MXN<br>";
echo "Total de todas las inversiones (anteriores, actuales y futuras): $totalMontoGeneral MXN<br>";
echo "Total de las deudas que inician y terminan en la quincena actual: $totalDeudaQuincenaActualConInteres MXN (incluye intereses)<br>";
echo "Total de las deudas que inician en la quincena actual y terminan en una posterior: $totalDeudaQuincenaFuturaConInteres MXN (incluye intereses)<br>";
echo "Total de todas las deudas (pasadas, presentes y futuras): $totalDeudaTodasConInteres MXN (incluye intereses)<br>";
echo "Total de ingresos en la quincena actual: $totalIngresoQuincenaActual MXN<br>";
echo "Total de ingresos totales: $totalIngresoTotal MXN<br>";
echo "Total de adeudos en la quincena actual: $totalAdeudoQuincenaActual MXN<br>";
echo "Total de adeudos totales: $totalAdeudoTotal MXN<br>";

// Guardar todas las variables en la sesión
$_SESSION['totalMontoQuincenaActualSiguiente'] = $totalMontoQuincenaActualSiguiente;
$_SESSION['totalMontoQuincenaActual'] = $totalMontoQuincenaActual;
$_SESSION['totalMontoGeneral'] = $totalMontoGeneral;
$_SESSION['totalDeudaQuincenaActualConInteres'] = $totalDeudaQuincenaActualConInteres;
$_SESSION['totalDeudaQuincenaFuturaConInteres'] = $totalDeudaQuincenaFuturaConInteres;
$_SESSION['totalDeudaTodasConInteres'] = $totalDeudaTodasConInteres;
$_SESSION['totalIngresoQuincenaActual'] = $totalIngresoQuincenaActual;
$_SESSION['totalIngresoTotal'] = $totalIngresoTotal;
$_SESSION['totalAdeudoQuincenaActual'] = $totalAdeudoQuincenaActual;
$_SESSION['totalAdeudoTotal'] = $totalAdeudoTotal;
?>

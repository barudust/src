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

// Consulta para obtener el nombre del usuario por el email
$sql = "SELECT * FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql);

// Verifica si se obtuvo el nombre correctamente
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre']; // Asume que el campo se llama 'nombre'
} else {
    $nombre = "Usuario"; // En caso de error, muestra "Usuario"
}

// Incluye los archivos que generan los arreglos
require 'fetch_user_incomes.php';
require 'fetch_user_debts.php';
require 'fetch_user_adeudos.php';
require 'fetch_user_investments.php';

// Extrae los datos totales de los arreglos
$totalIngresos = array_sum(array_column($ingresos, 'monto'));
$totalDeudas = array_sum(array_column($deudas, 'monto'));
$totalAdeudos = array_sum(array_column($adeudos, 'monto'));
$totalInversiones = array_sum(array_column($inversiones, 'monto'));

$data = [
    'Ingresos' => $totalIngresos,
    'Deudas' => $totalDeudas,
    'Adeudos' => $totalAdeudos,
    'Inversiones' => $totalInversiones,
];

// Cerrar la conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="es"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis Financiero</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="index.php">Repaso de Cuentas</a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i> 
                <?php if ($nombre != "") echo $nombre; ?> <!-- Muestra el nombre si está logueado -->
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="cuenta.php">Cuenta</a></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>
    
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Inicio
                        </a>
                        <a class="nav-link" href="inversiones.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Inversiones
                        </a>
                        <a class="nav-link" href="adeudos.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Adeudos
                        </a>
                        <a class="nav-link" href="ingresos.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Ingresos
                        </a>
                        <a class="nav-link" href="deudas.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Deudas
                        </a>
                        <a class="nav-link" href="presupuestos.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Presupuestos
                        </a>
                        <a class="nav-link" href="analisis_financiero.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Analisis Financiero
                        </a>
                        <a class="nav-link" href="exportar_datos.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Exportar Datos Financieros
                        </a>
                        <a class="nav-link" href="informes_financieros.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Generar Informes Financieros
                        </a>
                        <a class="nav-link" href="educacion_financiera.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Educacion Financiera
                        </a>   
                        </div>
                    </div>
                    
                </nav>
            </div>

            <div id="layoutSidenav_content">
            <main>
            <div class="container-fluid px-4">
                    <h1 class="my-4 text-center">Análisis Financiero</h1>

                    <!-- Ver Análisis Financiero -->
                    <div id="ver-analisis-financiero" class="mb-4">
                        <button onclick="obtenerAnalisisFinanciero()" class="btn btn-success">
                            <i class="fas fa-chart-line"></i> Ver Análisis Financiero
                        </button>
                    </div>

                    <ul id="lista-movimientos" class="mt-4 list-group">
                        <!-- Aquí se mostrarán los movimientos o el análisis -->
                        <h1>Visualización de Datos Financieros</h1>

                        <h2>Distribución Financiera (Gráfica de Pastel)</h2>
                        <canvas id="pieChart" width="400" height="400"></canvas>

                        <h2>Comparación Financiera (Gráfica de Barras)</h2>
                        <canvas id="barChart" width="400" height="400"></canvas>
                    </ul>
                </div>
            </main>

                <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted"> &copy; 2024 Repaso de Cuentas. Todos los derechos reservados.</div>

                    </div>
                </div>
            </footer>
            </div>
        </div>    
    
        <script>
        // Datos desde PHP para JavaScript
        function obtenerAnalisisFinanciero() {
        const labels = <?php echo json_encode(array_keys($data)); ?>;
        const values = <?php echo json_encode(array_values($data)); ?>;

        // Gráfica de pastel
        const ctxPie = document.getElementById('pieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                }]
            }
        });

        // Gráfica de barras
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Montos en MXN',
                    data: values,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    </script>
        <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/scripts.js"></script>
    <script src="../controllers/financialAnalysisController.js"></script> <!-- Lógica JS -->
</body>
</html>

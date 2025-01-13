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
$sql = "SELECT nombre FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql);

// Verifica si se obtuvo el nombre correctamente
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre']; // Asume que el campo se llama 'nombre'
} else {
    $nombre = "Usuario"; // En caso de error, muestra "Usuario"
}

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


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////INGRESOS
    $queryUsuario = "SELECT id_usuario FROM usuarios WHERE email = ?";
    $stmtUsuario = $conn->prepare($queryUsuario);
    $stmtUsuario->bind_param("s", $email);
    $stmtUsuario->execute();
    $resultUsuario = $stmtUsuario->get_result();

    if ($resultUsuario->num_rows > 0) {
        $user = $resultUsuario->fetch_assoc();
        $id_usuario = $user['id_usuario'];

        // Consulta para obtener los ingresos del usuario
        $queryIngresos = "SELECT fecha, descripcion, monto, categoria FROM transaccion WHERE id_usuario = ? AND tipo = 'Ingreso'";
        $stmtIngresos = $conn->prepare($queryIngresos);
        $stmtIngresos->bind_param("i", $id_usuario);
        $stmtIngresos->execute();
        $resultIngresos = $stmtIngresos->get_result();

        // Array para almacenar los ingresos
        $ingresos = [];

        while ($row = $resultIngresos->fetch_assoc()) {
            $ingresos[] = [
                'fecha' => $row['fecha'],
                'descripcion' => $row['descripcion'],
                'monto' => $row['monto'],
                'categoria' => $row['categoria']
            ];
        }

        // Ahora el array $ingresos está listo para usarse en otro documento
        // Por ejemplo, puedes guardarlo en la sesión para uso posterior
        $_SESSION['ingresos'] = $ingresos;
        
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////INVERSIONES
        // Consulta para obtener los inversiones del usuario
        $queryInversiones = "SELECT tipo, monto, rendimiento FROM inversion WHERE id_usuario = ?";
        $stmtInversiones = $conn->prepare($queryInversiones);
        $stmtInversiones->bind_param("i", $id_usuario);
        $stmtInversiones->execute();
        $resultInversiones = $stmtInversiones->get_result();

        // Array para almacenar las inversiones
        $inversiones = [];

        while ($row = $resultInversiones->fetch_assoc()) {
            $inversiones[] = [
                'tipo' => $row['tipo'],
                'monto' => $row['monto'],
                'rendimiento' => $row['rendimiento']
            ];
        }

        // Ahora el array $inversiones está listo para usarse en otro documento
        // Por ejemplo, puedes guardarlo en la sesión para uso posterior
        $_SESSION['inversiones'] = $inversiones;
        
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////ADEUDOS
        // Consulta para obtener los adeudos del usuario
        $queryAdeudos = "SELECT fecha_vencimiento, descripcion, monto, categoria, estado FROM adeudo WHERE id_usuario = ?";
        $stmtAdeudos = $conn->prepare($queryAdeudos);
        $stmtAdeudos->bind_param("i", $id_usuario);
        $stmtAdeudos->execute();
        $resultAdeudos = $stmtAdeudos->get_result();

        // Array para almacenar los adeudos
        $adeudos = [];

        while ($row = $resultAdeudos->fetch_assoc()) {
            $adeudos[] = [
                'fecha_vencimiento' => $row['fecha_vencimiento'],
                'descripcion' => $row['descripcion'],
                'monto' => $row['monto'],
                'categoria' => $row['categoria'],
                'estado' => $row['estado']
            ];
        }

        // Ahora el array $adeudos está listo para usarse en otro documento
        // Por ejemplo, puedes guardarlo en la sesión para uso posterior
        $_SESSION['adeudos'] = $adeudos;
        
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////DEUDAS
        // Consulta para obtener las deudas del usuario
        $queryDeudas = "SELECT entidad_acreedora, descripcion, monto, tasa_interes, estatus FROM deuda WHERE id_usuario = ?";
        $stmtDeudas = $conn->prepare($queryDeudas);
        $stmtDeudas->bind_param("i", $id_usuario);
        $stmtDeudas->execute();
        $resultDeudas = $stmtDeudas->get_result();

        // Array para almacenar las deudas
        $deudas = [];

        while ($row = $resultDeudas->fetch_assoc()) {
            $deudas[] = [
                'entidad_acreedora' => $row['entidad_acreedora'],
                'descripcion' => $row['descripcion'],
                'monto' => $row['monto'],
                'tasa_interes' => $row['tasa_interes'],
                'estatus' => $row['estatus']
            ];
        }

        // Ahora el array $deudas está listo para usarse en otro documento
        // Por ejemplo, puedes guardarlo en la sesión para uso posterior
        $_SESSION['deudas'] = $deudas;

      //  echo "Ingresos obtenidos y almacenados en la sesión.";
    } else {
       // echo "Usuario no encontrado.";
    }

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Extrae los datos totales de los arreglos
$totalIngresos = array_sum(array_column($ingresos, 'monto'));
$totalDeudas = array_sum(array_column($deudas, 'monto'));
$totalAdeudos = array_sum(array_column($adeudos, 'monto'));
$totalInversiones = array_sum(array_column($inversiones, 'monto'));

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
                        <h1>Visualización de Datos</h1>

                        <h2>Gráfico de Barras</h2>
                        <canvas id="barChart" width="400" height="200"></canvas>

                        <h2>Gráfico de Pastel</h2>
                        <canvas id="pieChart" width="400" height="200"></canvas>
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
         // Datos para el gráfico de barras
         const barData = {
            labels: ['Ingresos', 'Adeudos', 'Deudas', 'Inversiones'],
            datasets: [{
                label: 'Montos Totales',
                data: [
                    <?php echo array_sum(array_column($ingresos, 'monto')); ?>,
                    <?php echo array_sum(array_column($adeudos, 'monto')); ?>,
                    <?php echo array_sum(array_column($deudas, 'monto')); ?>,
                    <?php echo array_sum(array_column($inversiones, 'monto')); ?>
                ],
                backgroundColor: ['#4caf50', '#f44336', '#2196f3', '#ff9800']
            }]
        };

        // Configuración del gráfico de barras
        const barConfig = {
            type: 'bar',
            data: barData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Comparación de Montos Totales'
                    }
                }
            }
        };

        // Datos para el gráfico de pastel
        const pieData = {
            labels: ['Ingresos', 'Adeudos', 'Deudas', 'Inversiones'],
            datasets: [{
                data: [
                    <?php echo array_sum(array_column($ingresos, 'monto')); ?>,
                    <?php echo array_sum(array_column($adeudos, 'monto')); ?>,
                    <?php echo array_sum(array_column($deudas, 'monto')); ?>,
                    <?php echo array_sum(array_column($inversiones, 'monto')); ?>
                ],
                backgroundColor: ['#4caf50', '#f44336', '#2196f3', '#ff9800']
            }]
        };

        // Configuración del gráfico de pastel
        const pieConfig = {
            type: 'pie',
            data: pieData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distribución de Montos'
                    }
                }
            }
        };

        // Renderiza los gráficos
        const barChart = new Chart(
            document.getElementById('barChart'),
            barConfig
        );

        const pieChart = new Chart(
            document.getElementById('pieChart'),
            pieConfig
        );
    </script>
        <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/scripts.js"></script>
    <script src="../controllers/financialAnalysisController.js"></script> <!-- Lógica JS -->
</body>
</html>

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


$data = [
    'Ingresos' => $totalIngresos,
    'Deudas' => $totalDeudas,
    'Adeudos' => $totalAdeudos,
    'Inversiones' => $totalInversiones,
];

require 'vendor/autoload.php'; // Asegúrate de tener instalada la librería Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

// Configuración de Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

// Genera el contenido HTML para el PDF
$html = "<html lang='es'><head><meta charset='UTF-8'><title>Reporte Financiero</title></head><body>";
$html .= "<h1>Reporte Financiero del Usuario</h1>";
$html .= "<p>Usuario: " . htmlspecialchars($_SESSION['email']) . "</p>";
$html .= "<h2>Resumen Financiero</h2>";
$html .= "<table border='1' cellspacing='0' cellpadding='5'>";
$html .= "<tr><th>Categoría</th><th>Total (MXN)</th></tr>";
foreach ($data as $categoria => $total) {
    $html .= "<tr><td>" . htmlspecialchars($categoria) . "</td><td>" . number_format($total, 2) . "</td></tr>";
}
$html .= "</table>";
$html .= "<h2>Gráficas no incluidas</h2><p>Las gráficas se pueden consultar en la versión interactiva.</p>";
$html .= "</body></html>";

// Genera el PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Descarga el PDF
$dompdf->stream("reporte_financiero.pdf", ["Attachment" => true]);
exit;
// Cerrar la conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportar Datos Financieros</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
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
                    <h1 class="my-4 text-center">Exportar Datos Financieros</h1>

                    <!-- Obtener Datos Financieros -->
                    <div id="obtener-datos-financieros" class="mb-4">
                        <button onclick="obtenerDatosFinancieros()" class="btn btn-primary">
                            <i class="fas fa-download"></i> Obtener Datos Financieros
                        </button>
                    </div>

                    <!-- Lista de Datos Financieros -->
                    <ul id="lista-datos" class="mt-4 list-group">
                        <!-- Los datos exportados se mostrarán aquí -->
                    </ul>

                    <h2 class="my-4">Eliminar Datos Exportados</h2>

                    <!-- Botones para Eliminar Datos Exportados -->
                    <div id="eliminar-datos" class="mb-4">
                        <button onclick="eliminarDatosExportados('all')" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Eliminar Todos los Datos Exportados
                        </button>
                        <button onclick="eliminarDatosExportados('incomes')" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar Ingresos Exportados
                        </button>
                        <button onclick="eliminarDatosExportados('investments')" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar Inversiones Exportadas
                        </button>
                        <button onclick="eliminarDatosExportados('debts')" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar Deudas Exportadas
                        </button>
                        <button onclick="eliminarDatosExportados('budgets')" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar Presupuestos Exportados
                        </button>
                    </div>
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

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/scripts.js"></script>
    <script src="../controllers/exportController.js"></script> <!-- Lógica JS -->
</body>
</html>

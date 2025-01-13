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

// Obtener el nombre del usuario
$sqlNombre = "SELECT nombre FROM usuarios WHERE email = ?";
$stmtNombre = $conn->prepare($sqlNombre);
$stmtNombre->bind_param("s", $email);
$stmtNombre->execute();
$resultNombre = $stmtNombre->get_result();
if ($resultNombre->num_rows > 0) {
    $user = $resultNombre->fetch_assoc();
    $nombre = $user['nombre'];
} else {
    echo "<script>alert('Usuario no encontrado.');</script>";
    exit();
}
$stmtNombre->close();

// Obtener el ID de la inversión a modificar
if (isset($_GET['id'])) {
    $id_inversion = $_GET['id'];

    // Consultar la inversión específica
    $sql = "SELECT * FROM inversion WHERE id_inversion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_inversion);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $inversion = $result->fetch_assoc();
    } else {
        echo "<script>alert('Inversión no encontrada.');</script>";
        exit();
    }
    $stmt->close();
}

// Procesar el formulario de modificación
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tipo'], $_POST['monto'], $_POST['rendimiento'])) {
    $tipo = $_POST['tipo'];
    $monto = $_POST['monto'];
    $rendimiento = $_POST['rendimiento'];

    // Actualizar la inversión en la base de datos
    $sqlUpdate = "UPDATE inversion SET tipo = ?, monto = ?, rendimiento = ? WHERE id_inversion = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("sdii", $tipo, $monto, $rendimiento, $id_inversion);

    if ($stmtUpdate->execute()) {
        echo "<script> window.location.href = 'inversiones.php';</script>";
    } else {
        echo "<script>alert('Error al modificar la inversión.');</script>";
    }
    
    $stmtUpdate->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Inversión</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">Repaso de Cuentas</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i> <?= $nombre; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
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
                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                        Inicio
                    </a>
                    <a class="nav-link" href="inversiones.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                        Inversiones
                    </a>
                    <a class="nav-link" href="adeudos.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        Adeudos
                    </a>
                    <a class="nav-link" href="ingresos.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                        Ingresos
                    </a>
                    <a class="nav-link" href="deudas.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-hand-holding-usd"></i></div>
                        Deudas
                    </a>
                    <a class="nav-link" href="presupuestos.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                        Presupuestos
                    </a>
                    <a class="nav-link" href="analisis_financiero.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                        Análisis Financiero
                    </a>
                    <a class="nav-link" href="exportar_datos.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-download"></i></div>
                        Exportar Datos Financieros
                    </a>
                    <a class="nav-link" href="informes_financieros.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                        Generar Informes Financieros
                    </a>
                    <a class="nav-link" href="educacion_financiera.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                        Educación Financiera
                    </a>   
                </div>
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="my-4 text-center">Modificar Inversión</h1> 

                <!-- Formulario de modificación -->
                <div class="mb-4">
                    <h2 class="h4 mb-3">Modificar Inversión #<?= $inversion['id_inversion']; ?></h2>
                    <form action="editar_inversion.php?id=<?= $id_inversion; ?>" method="post">
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Inversión</label>
                            <input type="text" class="form-control" id="tipo" name="tipo" value="<?= $inversion['tipo']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="number" class="form-control" id="monto" name="monto"  value="<?= $inversion['monto']; ?>" min="0" step="0.01" required>
                            <small class="text-muted">Debe ser un número positivo con máximo dos decimales.</small>
                        </div>
                        <div class="mb-3">
                            <label for="rendimiento" class="form-label">Rendimiento (%)</label>
                            <input type="number" class="form-control" id="rendimiento" name="rendimiento" value="<?= $inversion['rendimiento']; ?>"  min="0" step="0.01" required>
                            <small class="text-muted">Debe ser un número positivo con máximo dos decimales.</small>
                        </div>
                        <button type="submit" class="btn btn-success">Modificar</button>
                    </form>
                </div>
            </div>
        </main>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">&copy; 2025 Repaso de Cuentas</div>
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/scripts.js"></script>
</body>
</html>

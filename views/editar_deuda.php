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

// Obtener el id_deuda desde la URL
$id_deuda = $_GET['id'] ?? '';

// Si el id_deuda no está presente, redirige
if ($id_deuda == '') {
    header("Location: deudas.php");
    exit();
}

// Consulta para obtener la deuda a editar
$sql_deuda = "SELECT d.id_deuda, d.monto, d.entidad_acreedora, d.tasa_interes, d.descripcion, d.estatus
              FROM deuda d
              JOIN usuarios u ON d.id_usuario = u.id_usuario
              WHERE u.email = '$email' AND d.id_deuda = '$id_deuda'";

$result_deuda = $conn->query($sql_deuda);

if ($result_deuda->num_rows > 0) {
    $deuda = $result_deuda->fetch_assoc(); // Obtiene los detalles de la deuda
} else {
    // Si no se encuentra la deuda, redirige
    header("Location: deudas.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibe los datos del formulario
    $monto_deuda = $_POST['monto_deuda'] ?? '';
    $entidad = $_POST['entidad_acreedora'] ?? '';
    $tasa_interes = $_POST['tasa_interes'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $estatus = $_POST['estatus'] ?? '';

    // Actualiza la deuda en la base de datos
    $sql_update = "UPDATE deuda SET 
                    monto = '$monto_deuda', 
                    entidad_acreedora = '$entidad', 
                    tasa_interes = '$tasa_interes', 
                    descripcion = '$descripcion', 
                    estatus = '$estatus'
                    WHERE id_deuda = '$id_deuda' AND id_usuario = (SELECT id_usuario FROM usuarios WHERE email = '$email')";

    if ($conn->query($sql_update) === TRUE) {
        header("Location: deudas.php?success=1");
        exit();
    } else {
        echo "Error al actualizar la deuda: " . $conn->error;
    }
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Deuda</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">Repaso de Cuentas</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i> 
                <?php echo $nombre; ?>
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
                    <a class="nav-link" href="deudas.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Deudas
                    </a>
                    <!-- Otros enlaces del menú -->
                </div>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="my-4 text-center">Editar Deuda</h1>

                <form action="editar_deuda.php?id=<?php echo $deuda['id_deuda']; ?>" method="POST">
                    <div class="mb-3">
                        <label for="monto_deuda" class="form-label">Monto</label>
                        <input type="number" class="form-control" name="monto_deuda" value="<?php echo $deuda['monto']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="entidad" class="form-label">Entidad Acreedora</label>
                        <input type="text" class="form-control" name="entidad_acreedora" value="<?php echo $deuda['entidad_acreedora']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="tasa_interes" class="form-label">Tasa de Interés (%)</label>
                        <input type="number" class="form-control" name="tasa_interes" value="<?php echo $deuda['tasa_interes']; ?>" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3" required><?php echo $deuda['descripcion']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="estatus" class="form-label">Estatus</label>
                        <select class="form-control" name="estatus" required>
                            <option value="pendiente" <?php if ($deuda['estatus'] == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                            <option value="Pagado" <?php if ($deuda['estatus'] == 'Pagado') echo 'selected'; ?>>Pagado</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Deuda</button>
                </form>
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
</body>
</html>

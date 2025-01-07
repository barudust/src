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

// Verifica si se ha recibido un ID de transacción
if (isset($_GET['id_transaccion'])) {
    $id_transaccion = $_GET['id_transaccion'];

    // Consulta para obtener los datos del ingreso a editar
    $sql_ingreso = "SELECT * FROM transaccion WHERE id_transaccion = '$id_transaccion' AND id_usuario = (SELECT id_usuario FROM usuarios WHERE email = '$email')";
    $ingreso_result = $conn->query($sql_ingreso);

    if ($ingreso_result->num_rows > 0) {
        $ingreso = $ingreso_result->fetch_assoc();
    } else {
        echo "Ingreso no encontrado.";
        exit();
    }
}

// Procesar el formulario de actualización de ingreso
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $categoria = $_POST['categoria'];
    $fecha = $_POST['fecha'];

    // Actualizar el ingreso en la base de datos
    $sql_update = "UPDATE transaccion SET fecha = '$fecha', descripcion = '$descripcion', monto = '$monto', categoria = '$categoria' WHERE id_transaccion = '$id_transaccion'";

    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('Ingreso actualizado correctamente.'); window.location.href = 'ingresos.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ingreso</title>
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
                <?php if ($nombre != "") echo $nombre; ?>
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
            <h1 class="my-4 text-center">Editar Ingreso</h1>

            <!-- Formulario de edición de ingreso -->
            <form method="POST" id="form-editar-ingreso">
                <div class="mb-3">
                    <label for="fecha-ingreso" class="form-label">Fecha del Ingreso</label>
                    <input type="date" class="form-control" id="fecha-ingreso" name="fecha" value="<?php echo $ingreso['fecha']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion-ingreso" class="form-label">Descripción del Ingreso</label>
                    <input type="text" class="form-control" id="descripcion-ingreso" name="descripcion" value="<?php echo $ingreso['descripcion']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="monto-ingreso" class="form-label">Monto del Ingreso</label>
                    <input type="number" class="form-control" id="monto-ingreso" name="monto" value="<?php echo $ingreso['monto']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="categoria-ingreso" class="form-label">Categoría del Ingreso</label>
                    <input type="text" class="form-control" id="categoria-ingreso" name="categoria" value="<?php echo $ingreso['categoria']; ?>" required>
                </div>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </form>

        </div>
    </main>
</div>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/scripts.js"></script>
</body>
</html>

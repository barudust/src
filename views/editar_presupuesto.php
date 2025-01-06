<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit(); // Detiene la ejecución después de la redirección
}

$email = $_SESSION['email'];

// Conectar a la base de datos
include('conexion.php');

// Consulta para obtener el nombre del usuario por el email usando prepared statements
$stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Verifica si se obtuvo el nombre correctamente
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_usuario = $row['id_usuario'];
    $nombre = $row['nombre'];
} else {
    $nombre = "Usuario"; // En caso de error, muestra "Usuario"
    $id_usuario = 0; // Asignar un valor por defecto si no se encuentra el usuario
}

// Obtener datos del presupuesto a editar
if (isset($_GET['id'])) {
    $id_presupuesto = $_GET['id'];
    $stmt_presupuesto = $conn->prepare("SELECT descripcion, monto FROM presupuesto WHERE id_presupuesto = ? AND id_usuario = ?");
    $stmt_presupuesto->bind_param("ii", $id_presupuesto, $id_usuario);
    $stmt_presupuesto->execute();
    $result_presupuesto = $stmt_presupuesto->get_result();

    if ($result_presupuesto->num_rows > 0) {
        $presupuesto = $result_presupuesto->fetch_assoc();
    } else {
        echo "<script>alert('Presupuesto no encontrado.'); window.location.href = 'presupuestos.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('ID de presupuesto no especificado.'); window.location.href = 'presupuestos.php';</script>";
    exit();
}

// Actualizar presupuesto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descripcion = $_POST['descripcion'] ?? '';
    $monto = $_POST['monto'] ?? '';

    if (empty($descripcion) || empty($monto)) {
        echo "<script>alert('Todos los campos son obligatorios');</script>";
    } else {
        // Validar monto como número positivo
        if (!is_numeric($monto) || $monto <= 0) {
            echo "<script>alert('El monto debe ser un número positivo');</script>";
        } else {
            $stmt_update = $conn->prepare("UPDATE presupuesto SET descripcion = ?, monto = ? WHERE id_presupuesto = ? AND id_usuario = ?");
            $stmt_update->bind_param("sdii", $descripcion, $monto, $id_presupuesto, $id_usuario);
            if ($stmt_update->execute()) {
                echo "<script>alert('Presupuesto actualizado exitosamente'); window.location.href = 'presupuestos.php';</script>";
            } else {
                echo "<script>alert('Error al actualizar el presupuesto: " . $stmt_update->error . "');</script>";
            }
            $stmt_update->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Presupuesto</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body>
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="index.php">Repaso de Cuentas</a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
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
                    <a class="nav-link" href="presupuestos.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Presupuestos
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="my-4 text-center">Editar Presupuesto</h1>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="descripcion-presupuesto" class="form-label">Descripción del Presupuesto</label>
                        <input type="text" class="form-control" id="descripcion-presupuesto" name="descripcion" value="<?php echo htmlspecialchars($presupuesto['descripcion']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="monto-presupuesto" class="form-label">Monto</label>
                        <input type="number" class="form-control" id="monto-presupuesto" name="monto" value="<?php echo htmlspecialchars($presupuesto['monto']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Presupuesto</button>
                    <a href="presupuestos.php" class="btn btn-secondary">Cancelar</a>
                </form>

            </div>
        </main>
    </div>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>

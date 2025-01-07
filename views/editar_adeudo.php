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

// Obtener el id del usuario basado en su email
$sql = "SELECT id_usuario, nombre FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_usuario = $row['id_usuario'];
    $nombre = $row['nombre'];
} else {
    echo "Error: Usuario no encontrado.";
    exit();
}

// Verificar si el ID del adeudo se ha pasado como parámetro
if (isset($_GET['id'])) {
    $id_adeudo = $_GET['id'];

    // Consultar el adeudo específico
    $sql_adeudo = "SELECT * FROM adeudo WHERE id_adeudo = '$id_adeudo' AND id_usuario = '$id_usuario'";
    $result_adeudo = $conn->query($sql_adeudo);

    if ($result_adeudo->num_rows > 0) {
        $row_adeudo = $result_adeudo->fetch_assoc();
    } else {
        echo "Error: Adeudo no encontrado.";
        exit();
    }
} else {
    echo "Error: No se ha proporcionado un ID de adeudo.";
    exit();
}

// Si se ha enviado el formulario para editar el adeudo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_adeudo'])) {
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $monto = $_POST['monto'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $estado = $_POST['estado'];  // Agregar el estado

    // Actualizar el adeudo en la base de datos
    $sql_update = "UPDATE adeudo SET descripcion = '$descripcion', categoria = '$categoria', monto = '$monto', fecha_vencimiento = '$fecha_vencimiento', estado = '$estado' WHERE id_adeudo = '$id_adeudo'";
    if ($conn->query($sql_update) === TRUE) {
        // Redirigir a la página de adeudos después de la actualización
        header("Location: adeudos.php");
        exit();
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Adeudo</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body>
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">Repaso de Cuentas</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
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
                <h1 class="my-4 text-center">Editar Adeudo</h1>
                <form action="editar_adeudo.php?id=<?php echo $id_adeudo; ?>" method="POST">
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción del Adeudo</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo $row_adeudo['descripcion']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoría</label>
                        <input type="text" class="form-control" id="categoria" name="categoria" value="<?php echo $row_adeudo['categoria']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto del Adeudo</label>
                        <input type="number" class="form-control" id="monto" name="monto" step="0.01" value="<?php echo $row_adeudo['monto']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                        <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" value="<?php echo $row_adeudo['fecha_vencimiento']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="Pendiente" <?php if ($row_adeudo['estado'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                            <option value="Pagado" <?php if ($row_adeudo['estado'] == 'Pagado') echo 'selected'; ?>>Pagado</option>
                        </select>
                    </div>
                    <button type="submit" name="editar_adeudo" class="btn btn-primary">Actualizar Adeudo</button>
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

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
include('../conexion.php'); // Suponiendo que la conexión está en este archivo

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

// Cerrar la conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presupuestos</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link href="../../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body>
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
                <h1 class="my-4 text-center">Gestión de Presupuestos</h1>

                <!-- Registrar Presupuesto -->
                <div id="registro-presupuesto" class="mb-4">
                    <h2 class="h4 mb-3">Registrar Presupuesto</h2>
                    <form id="form-registro-presupuesto">
                        <div class="mb-3">
                            <label for="descripcion-presupuesto" class="form-label">Descripción del Presupuesto</label>
                            <input type="text" class="form-control" id="descripcion-presupuesto" placeholder="Descripción del presupuesto" required>
                        </div>
                        <div class="mb-3">
                            <label for="monto-presupuesto" class="form-label">Monto del Presupuesto</label>
                            <input type="number" class="form-control" id="monto-presupuesto" placeholder="Monto del presupuesto" required>
                        </div>
                        <button type="button" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Registrar Presupuesto
                        </button>
                    </form>
                    <p id="mensaje-registro-presupuesto" class="mt-2"></p>
                </div>

                <!-- Modificar Presupuesto -->
                <div id="modificar-presupuesto" class="mb-4">
                    <h2 class="h4 mb-3">Modificar Presupuesto</h2>
                    <form id="form-modificar-presupuesto">
                        <div class="mb-3">
                            <label for="id-modificar-presupuesto" class="form-label">ID del Presupuesto</label>
                            <input type="number" class="form-control" id="id-modificar-presupuesto" placeholder="ID del presupuesto" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuevo-descripcion-presupuesto" class="form-label">Nueva Descripción</label>
                            <input type="text" class="form-control" id="nuevo-descripcion-presupuesto" placeholder="Nueva descripción" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuevo-monto-presupuesto" class="form-label">Nuevo Monto</label>
                            <input type="number" class="form-control" id="nuevo-monto-presupuesto" placeholder="Nuevo monto" required>
                        </div>
                        <button type="button" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modificar Presupuesto
                        </button>
                    </form>
                    <p id="mensaje-modificacion-presupuesto" class="mt-2"></p>
                </div>

                <!-- Eliminar Presupuesto -->
                <div id="eliminar-presupuesto" class="mb-4">
                    <h2 class="h4 mb-3">Eliminar Presupuesto</h2>
                    <form id="form-eliminar-presupuesto">
                        <div class="mb-3">
                            <label for="id-eliminar-presupuesto" class="form-label">ID del Presupuesto a Eliminar</label>
                            <input type="number" class="form-control" id="id-eliminar-presupuesto" placeholder="ID del presupuesto" required>
                        </div>
                        <button type="button" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Eliminar Presupuesto
                        </button>
                    </form>
                    <p id="mensaje-eliminacion-presupuesto" class="mt-2"></p>
                </div>

                <!-- Visualizar Presupuestos -->
                <div id="visualizar-presupuestos" class="mb-4">
                    <h2 class="h4 mb-3">Visualizar Presupuestos</h2>
                    <button type="button" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver Presupuestos
                    </button>
                    <ul id="lista-presupuestos" class="mt-3 list-group"></ul>
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
    <script src="../../js/bootstrap.bundle.min.js"></script>
    <script src="../../js/scripts.js"></script>
    <script src="../../controllers/budgetController.js"></script> <!-- Lógica JS -->
</body>
</html>

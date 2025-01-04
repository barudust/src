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

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Repaso de Cuentas</title>
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
                    <!-- Sección de Bienvenida -->
                    <section id="bienvenida" class="text-center mb-5">
                        <h2 class="my-4"><i class="fas fa-wallet"></i> Bienvenido a Repaso de Cuentas</h2>
                        <p>Tu herramienta integral para gestionar tus finanzas personales de manera efectiva y accesible.</p>
                    </section>

                    <!-- Sección de Carrusel de Imágenes -->
                    <section id="carrusel" class="text-center mb-5">
                        <h2><i class="fas fa-images"></i> Imágenes Representativas</h2>
                        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel" style="max-width: 400px; margin: 0 auto;">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="../assets/images/finanzas1.jpg" alt="Finanzas Personales" class="d-block w-100 mx-auto" style="height: 250px; object-fit: cover;">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/images/finanzas2.jpg" alt="Ahorro" class="d-block w-100 mx-auto" style="height: 250px; object-fit: cover;">
                                </div>
                                <div class="carousel-item">
                                    <img src="../assets/images/finanzas3.jpg" alt="Inversiones" class="d-block w-100 mx-auto" style="height: 250px; object-fit: cover;">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </section>

                    <!-- Sección de Información Adicional -->
                    <section id="informacion-adicional" class="mb-5">
                        <h2><i class="fas fa-info-circle"></i> ¿Qué Ofrecemos?</h2>
                        <p>Nuestra aplicación te permite:</p>
                        <ul class="list-group">
                            <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> Gestionar tus ingresos y gastos.</li>
                            <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> Realizar un seguimiento de tus inversiones.</li>
                            <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> Controlar tus deudas y adeudos.</li>
                            <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> Establecer presupuestos personalizados.</li>
                            <li class="list-group-item"><i class="fas fa-arrow-circle-right"></i> Acceder a recursos educativos sobre finanzas.</li>
                        </ul>
                    </section>
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

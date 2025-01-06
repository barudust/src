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
$sql = "SELECT nombre FROM usuario WHERE email = '$email'";
$result = $conn->query($sql);

// Verifica si se obtuvo el nombre correctamente
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre']; // Asume que el campo se llama 'nombre'
} else {
    $nombre = "Usuario"; // En caso de error, muestra "Usuario"
}

// Consulta para obtener los ingresos
$sql_ingresos = "SELECT id_transaccion, fecha, descripcion, monto, categoria FROM transaccion WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE email = '$email') AND tipo = 'Ingreso' ORDER BY fecha DESC";
$ingresos_result = $conn->query($sql_ingresos);

// Verifica si la consulta se ejecutó correctamente
if (!$ingresos_result) {
    echo "Error en la consulta: " . $conn->error;
    exit();
}

// Cerrar la conexión
$conn->close();

// Procesar el formulario de registro de ingresos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $categoria = $_POST['categoria'];
    $fecha = $_POST['fecha'];

    // Reconectar a la base de datos para insertar el nuevo ingreso
    include('conexion.php');

    // Obtener el ID del usuario
    $sql_usuario = "SELECT id_usuario FROM usuario WHERE email = '$email'";
    $result_usuario = $conn->query($sql_usuario);
    $row_usuario = $result_usuario->fetch_assoc();
    $id_usuario = $row_usuario['id_usuario'];

    // Insertar el nuevo ingreso
    $sql_insert = "INSERT INTO transaccion (id_usuario, fecha, descripcion, monto, categoria, tipo) VALUES ('$id_usuario', '$fecha', '$descripcion', '$monto', '$categoria', 'Ingreso')";
    
    if ($conn->query($sql_insert) === TRUE) {
        echo "<script>alert('Ingreso registrado correctamente.');</script>";
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
    <title>Ingresos</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script>
        // Validación para que las cantidades sean positivas
        function validarMonto(id) {
            let monto = document.getElementById(id).value;
            if (monto <= 0) {
                alert("El monto debe ser un número positivo.");
                return false;
            }
            return true;
        }

        // Mostrar el formulario de registrar ingreso
        function mostrarFormulario() {
            document.getElementById("formulario-ingreso").style.display = "block";
        }
    </script>
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
            <h1 class="my-4 text-center">Gestión de Ingresos</h1>

            <!-- Botón para agregar ingreso -->
            <button class="btn btn-sm btn-primary" onclick="mostrarFormulario()">
                <i class="fas fa-plus-circle"></i> Agregar Ingreso
            </button>

            <!-- Formulario de registro de ingresos -->
            <div id="formulario-ingreso" class="mb-4" style="display: none;">
                <h2 class="h4 mb-3">Registrar Ingreso</h2>
                <form method="POST" id="form-registro-ingreso" onsubmit="return validarMonto('monto-ingreso')">
                    <div class="mb-3">
                        <label for="fecha-ingreso" class="form-label">Fecha del Ingreso</label>
                        <input type="date" class="form-control" id="fecha-ingreso" name="fecha" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion-ingreso" class="form-label">Descripción del Ingreso</label>
                        <input type="text" class="form-control" id="descripcion-ingreso" name="descripcion" placeholder="Descripción del ingreso" required>
                    </div>
                    <div class="mb-3">
                        <label for="monto-ingreso" class="form-label">Monto del Ingreso</label>
                        <input type="number" class="form-control" id="monto-ingreso" name="monto" placeholder="Monto del ingreso" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria-ingreso" class="form-label">Categoría del Ingreso</label>
                        <input type="text" class="form-control" id="categoria-ingreso" name="categoria" placeholder="Categoría del ingreso" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle"></i> Registrar Ingreso
                    </button>
                </form>
                <p id="mensaje-registro-ingreso" class="mt-2"></p>
            </div>

            <!-- Tabla de Ingresos -->
            <div class="mb-4">
                <h2 class="h4 mb-3">Ingresos Registrados</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Categoría</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($ingresos_result->num_rows > 0) {
                            while($ingreso = $ingresos_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $ingreso['fecha'] . "</td>";
                                echo "<td>" . $ingreso['descripcion'] . "</td>";
                                echo "<td>" . number_format($ingreso['monto'], 2) . "</td>";
                                echo "<td>" . $ingreso['categoria'] . "</td>";
                                echo "<td>
                                        <a href='editar_ingreso.php?id_transaccion=" . $ingreso['id_transaccion'] . "' class='btn btn-sm btn-warning'>Editar</a>
                                        <a href='eliminar_ingreso.php?id_transaccion=" . $ingreso['id_transaccion'] . "' class='btn btn-sm btn-danger'>Eliminar</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No se han registrado ingresos.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</div>
</body>
</html>

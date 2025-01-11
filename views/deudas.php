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

$fecha_error = ''; // Variable para almacenar el mensaje de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica los datos recibidos
    $monto_deuda = $_POST['monto_deuda'] ?? '';
    $entidad = $_POST['entidad_acreedora'] ?? '';
    $tasa_interes = $_POST['tasa_interes'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $estatus = $_POST['estatus'] ?? '';  
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';  
    $fecha_fin = $_POST['fecha_fin'] ?? '';  

    // Compara si la fecha de fin es posterior a la fecha de inicio
    if ($fecha_fin < $fecha_inicio) {
        $fecha_error = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
    }

    // Marca como 'caducado' si la fecha de fin es anterior a la fecha actual
    $fecha_actual = date("Y-m-d");
    if ($fecha_fin < $fecha_actual) {
        $estatus = 'Caducado';
    }

    if (empty($fecha_error) && $estatus) {
        // Insertar la nueva deuda en la base de datos si no hay errores
        $sql_insert = "INSERT INTO deuda (id_usuario, monto, entidad_acreedora, tasa_interes, descripcion, estatus, fecha_inicio, fecha_fin) 
                       VALUES ((SELECT id_usuario FROM usuarios WHERE email = '$email'), '$monto_deuda', '$entidad', '$tasa_interes', '$descripcion', '$estatus', '$fecha_inicio', '$fecha_fin')";
        if ($conn->query($sql_insert) === TRUE) {
            // Deuda agregada exitosamente
        } else {
            echo "Error al insertar: " . $conn->error; // Agrega un mensaje de error si la inserción falla
        }
    }
}

// Consulta para obtener las deudas
$sql_deudas = "SELECT d.id_deuda, d.monto, d.entidad_acreedora, d.tasa_interes, d.descripcion, d.estatus, d.fecha_inicio, d.fecha_fin
               FROM deuda d
               JOIN usuarios u ON d.id_usuario = u.id_usuario
               WHERE u.email = '$email'";
$result_deudas = $conn->query($sql_deudas);

// Cerrar la conexión
$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deudas</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script>
        function toggleForm() {
            var form = document.getElementById("form-deuda");
            if (form.style.display === "none" || form.style.display === "") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }
    </script>
</head>
<body class="sb-nav-fixed">
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
                <h1 class="my-4 text-center">Gestión de Deudas</h1>

                <!-- Tabla de Deudas -->
                <h2 class="h4 mb-3">Lista de Deudas</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Deuda</th>
                            <th>Monto</th>
                            <th>Entidad Acreedora</th>
                            <th>Tasa de Interés</th>
                            <th>Descripcion</th>
                            <th>Estado</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_deudas->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['id_deuda']; ?></td>
                                <td><?php echo $row['monto']; ?></td>
                                <td><?php echo $row['entidad_acreedora']; ?></td>
                                <td><?php echo $row['tasa_interes']; ?>%</td>
                                <td><?php echo $row['descripcion']; ?></td>
                                <td><?php echo $row['estatus']; ?></td>
                                <td><?php echo $row['fecha_inicio']; ?></td>
                                <td><?php echo $row['fecha_fin']; ?></td>
                                <td>
                                    <a href="editar_deuda.php?id=<?php echo $row['id_deuda']; ?>" class="btn btn-warning">Editar</a>
                                    <a href="eliminar_deuda.php?id=<?php echo $row['id_deuda']; ?>" class="btn btn-danger">Eliminar</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Botón para mostrar/ocultar el formulario -->
                <button class="btn btn-success mb-3" onclick="toggleForm()">Agregar Deuda</button>

                <!-- Formulario para Añadir Deuda -->
                <div id="form-deuda" style="display: none;">
                    <h2 class="h4 mb-3">Añadir Nueva Deuda</h2>
                    <form action="deudas.php" method="POST" onsubmit="return validarFormulario()">
                        <div class="mb-3">
                            <label for="monto_deuda" class="form-label">Monto</label>
                            <input type="number" class="form-control" name="monto_deuda" min="0" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="entidad" class="form-label">Entidad Acreedora</label>
                            <input type="text" class="form-control" name="entidad_acreedora" required>
                        </div>
                        <div class="mb-3">
                            <label for="tasa_interes" class="form-label">Tasa de Interés (%)</label>
                            <input type="number" class="form-control" name="tasa_interes" min="0" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="estatus" class="form-label">Estatus</label>
                            <select class="form-control" name="estatus" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="Pagado">Pagado</option>
                                <option value="caducado">Caducado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" name="fecha_fin" required>
                            <div class="text-danger mt-2" id="fecha-error"></div> <!-- Mensaje de error -->
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
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
<script>
    function validarFormulario() {
        // Obtiene las fechas de inicio y fin
        var fecha_inicio = document.getElementsByName("fecha_inicio")[0].value;
        var fecha_fin = document.getElementsByName("fecha_fin")[0].value;
        
        // Verifica si la fecha de fin es anterior a la de inicio
        if (fecha_fin < fecha_inicio) {
            // Muestra el mensaje de error en la interfaz
            document.getElementById("fecha-error").innerText = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
            return false; // Evita que se envíe el formulario
        }
        
        // Si las fechas son correctas, el formulario puede enviarse
        document.getElementById("fecha-error").innerText = ''; // Limpia cualquier mensaje de error anterior
        return true;
    }
</script>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/scripts.js"></script>
</body>
</html>

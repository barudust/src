<?php
session_start(); // Inicia la sesión
$mensaje = "";
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

// Si se envía el formulario de presupuesto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descripcion = $_POST['descripcion'] ?? '';
    $monto = $_POST['monto'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';

    if (empty($descripcion) || empty($monto) || empty($fecha_inicio)) {
    } else {
        // Validar monto como número positivo
        if (!is_numeric($monto) || $monto <= 0) {
        } else {
            $fecha_inicio_obj = new DateTime($fecha_inicio);
            $dia_inicio = $fecha_inicio_obj->format('d');

            // Ajustar la fecha de inicio para el periodo correcto
            if ($dia_inicio >= 1 && $dia_inicio <= 15) {
                $fecha_inicio_obj->setDate($fecha_inicio_obj->format('Y'), $fecha_inicio_obj->format('m'), 1);
            } else {
                $fecha_inicio_obj->setDate($fecha_inicio_obj->format('Y'), $fecha_inicio_obj->format('m'), 16);
            }
            $fecha_inicio = $fecha_inicio_obj->format('Y-m-d');

            // Calcular la fecha de fin
            if ($dia_inicio >= 16) {
                $fecha_fin_obj = new DateTime($fecha_inicio);
                $fecha_fin_obj->modify('last day of this month');
            } else {
                $fecha_fin_obj = clone $fecha_inicio_obj;
                $fecha_fin_obj->modify('+14 days');
            }
            $fecha_fin = $fecha_fin_obj->format('Y-m-d');

            // Verifica si ya existe un presupuesto para la misma quincena usando prepared statements
            $stmt_verificar = $conn->prepare("SELECT * FROM presupuesto WHERE id_usuario = ? AND fecha_inicio = ?");
            $stmt_verificar->bind_param("is", $id_usuario, $fecha_inicio);
            $stmt_verificar->execute();
            $result_verificar = $stmt_verificar->get_result();

            if ($result_verificar->num_rows > 0) {
                $mensaje = "<div style='color: red; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; width: fit-content; margin-bottom: 15px;'>
                Ya existe un presupuesto para esta quincena. No se puede registrar otro.
              </div>";}
               else {
               
                // Insertar el nuevo presupuesto en la base de datos
                $stmt_insert = $conn->prepare("INSERT INTO presupuesto (id_usuario, descripcion, categoria, fecha_inicio, fecha_fin, monto) VALUES (?, ?, ?, ?, ?, ?)");
                $categoria = 'Categoría'; // Ajusta la categoría según sea necesario
                $stmt_insert->bind_param("issssd", $id_usuario, $descripcion, $categoria, $fecha_inicio, $fecha_fin, $monto);
                if ($stmt_insert->execute()) {
                } else {
                }
                $stmt_insert->close();
            }
            $stmt_verificar->close();
        }
    }
}

// Consulta para obtener los presupuestos del usuario
$stmt_presupuestos = $conn->prepare("SELECT * FROM presupuesto WHERE id_usuario = ?");
$stmt_presupuestos->bind_param("i", $id_usuario);
$stmt_presupuestos->execute();
$result_presupuestos = $stmt_presupuestos->get_result();

// Obtener la suma de los presupuestos para la quincena vigente
$fecha_actual = date('Y-m-d');
$stmt_suma = $conn->prepare("SELECT SUM(monto) AS total_quincena FROM presupuesto WHERE id_usuario = ? AND fecha_inicio <= ? AND fecha_fin >= ?");
$stmt_suma->bind_param("iss", $id_usuario, $fecha_actual, $fecha_actual);
$stmt_suma->execute();
$result_suma = $stmt_suma->get_result();
$total_quincena = 0;
if ($result_suma->num_rows > 0) {
    $row_suma = $result_suma->fetch_assoc();
    $total_quincena = $row_suma['total_quincena'];
}
$aux_total_quincena = $total_quincena 
                     - $_SESSION['totalMontoQuincenaActualSiguiente'] 
                     + $_SESSION['totalMontoQuincenaActual'] 
                     + $_SESSION['totalDeudaQuincenaActualConInteres'] 
                     + $_SESSION['totalIngresoQuincenaActual'] 
                     - $_SESSION['totalAdeudoQuincenaActual'] ;
// Cerrar la conexión
$stmt->close();
$stmt_presupuestos->close();
$stmt_suma->close();
$conn->close();
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presupuestos</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet" />
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
                        Análisis Financiero
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
                        Educación Financiera
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="my-4 text-center">Gestión de Presupuestos</h1>

                <!-- Mostrar el presupuesto para la quincena vigente -->
                <div class="alert alert-info" role="alert">
                    <strong>Presupuesto para la quincena vigente:</strong> $<?php echo number_format($total_quincena, 2); ?>
                </div>
                <div class="alert alert-danger" role="alert">
                    <strong>Presupuesto restante para la quincena vigente:</strong> $<?php echo number_format($aux_total_quincena, 2); ?>
                </div>
                <div class="alert alert-warning" role="alert" style="max-width: 1200px; font-size: 0.9em;">
                    <strong>Nota:</strong> 
                    El presupuesto restante se basa en el presupuesto vigente más la suma o resta de los adeudos, ingresos, deudas e inversiones vigentes.
                    <br> 
                    <span style="background-color: #f0ad4e; font-weight: bold;">Lo que se suma:</span> 
                    <br>
                    Ingresos durante esta quincena, Inversiones que terminen esta quincena
                    <br>
                    <span style="background-color: #f0ad4e; font-weight: bold;">Lo que se resta:</span>  
                    <br>
                    Inversiones hechas en la quincena vigente, Deudas que finalicen en esta quincena, Adeudos que finalicen en esta quincena
                    <br><br>
                    <span style="color: red; font-size: 0.9em;">La suma ya incluye intereses o rendimientos según corresponda</span>
                </div>



                <div class="container mt-4">
                <!-- Tabla de Presupuestos -->
                <div id="tabla-presupuestos" class="mb-4">
                    <h2 class="h4 mb-3">Presupuestos Quincenales Actuales</h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Monto</th>
                                <th>Fecha de Inicio</th>
                                <th>Fecha de Fin</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Carga los presupuestos de la base de datos -->
                            <?php
                            if ($result_presupuestos->num_rows > 0) {
                                while ($row = $result_presupuestos->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['descripcion']}</td>
                                            <td>\${$row['monto']}</td>
                                            <td>{$row['fecha_inicio']}</td>
                                            <td>{$row['fecha_fin']}</td>
                                            <td>
                                                <a href='editar_presupuesto.php?id={$row['id_presupuesto']}' class='btn btn-warning'><i class='fas fa-edit'></i> Editar</a>
                                                <a href='eliminar_presupuesto.php?id={$row['id_presupuesto']}' class='btn btn-danger'><i class='fas fa-trash-alt'></i> Eliminar</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No hay presupuestos registrados.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#registro-presupuesto">
                        <i class="fas fa-plus-circle"></i> Crear Nuevo Presupuesto
                    </button>
                    <?php  echo $mensaje;
                     ?>
                </div>

                <!-- Registrar Presupuesto -->
                <div id="registro-presupuesto" class="collapse mb-4">
                <div class="alert alert-info" role="alert">
                            <strong>Importante:</strong> Los presupuestos se dividen en quincenas. Si el presupuesto inicia entre el 1 y el 15, su fecha de finalización será el 15 del mismo mes. Si inicia entre el 16 y el 31, la fecha de finalización será 14 días después, iniciando el día 16.
                        </div>

                    <h2 class="h4 mb-3">Registrar Presupuesto</h2>
                        <form method="POST" action="presupuestos.php">
                            <div class="mb-3">
                                <label for="descripcion-presupuesto" class="form-label">Descripción del Presupuesto</label>
                                <input type="text" class="form-control" id="descripcion-presupuesto" name="descripcion" placeholder="Descripción" required>
                            </div>
                            <div class="mb-3">
                                <label for="monto" class="form-label">Monto</label>
                                <input type="number" class="form-control" name="monto" min="0" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Registrar Presupuesto</button>
                            
                        </form>
                </div>
            </div>
        </main>
        
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/scripts.js"></script>

</body>
</html>

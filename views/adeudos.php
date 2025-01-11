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
    exit();
}

// Si se ha enviado el formulario para registrar un nuevo adeudo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_adeudo'])) {
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $monto = $_POST['monto'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $estado = $_POST['estado'];  // Agregar el estado

    // Fecha actual
    $fecha_actual = date('Y-m-d');

    // Si la fecha de vencimiento es anterior a la fecha actual, por defecto el estado será "Caducado"
    if (strtotime($fecha_vencimiento) < strtotime($fecha_actual)) {
        $estado = 'Caducado';
    }

    // Insertar el nuevo adeudo en la base de datos
    $sql_insert = "INSERT INTO adeudo (id_usuario, descripcion, categoria, monto, fecha_vencimiento, estado)
                   VALUES ('$id_usuario', '$descripcion', '$categoria', '$monto', '$fecha_vencimiento', '$estado')";
    if ($conn->query($sql_insert) === TRUE) {
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

// Consulta para obtener los adeudos del usuario actual
$sql_adeudos = "SELECT * FROM adeudo WHERE id_usuario = '$id_usuario'";
$result_adeudos = $conn->query($sql_adeudos);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adeos</title>
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
                <h1 class="my-4 text-center">Gestión de Adeudos</h1>
                <h2 class="h4 mb-3">Lista de Adeudos</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>Monto</th>
                            <th>Fecha de Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_adeudos->num_rows > 0) {
                            while ($row = $result_adeudos->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . $row['id_adeudo'] . "</td>
                                        <td>" . $row['descripcion'] . "</td>
                                        <td>" . $row['categoria'] . "</td>
                                        <td>" . $row['monto'] . "</td>
                                        <td>" . $row['fecha_vencimiento'] . "</td>
                                        <td>" . $row['estado'] . "</td>
                                        <td>
                                            <a href='editar_adeudo.php?id=" . $row['id_adeudo'] . "' class='btn btn-warning btn-sm'>Modificar</a>
                                            <a href='eliminar_adeudo.php?id=" . $row['id_adeudo'] . "' class='btn btn-danger btn-sm'>Eliminar</a>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No hay adeudos registrados.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Botón para abrir el formulario de registro -->
                <button class="btn btn-success mb-4" data-bs-toggle="collapse" data-bs-target="#formulario-registro-adeudo">Registrar Nuevo Adeudo</button>

                <!-- Formulario para Registrar Adeudo (oculto inicialmente) -->
                <div id="formulario-registro-adeudo" class="collapse">
                    <form action="adeudos.php" method="POST">
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción del Adeudo</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <input type="text" class="form-control" id="categoria" name="categoria">
                        </div>
                        <div class="mb-3">
                            <label for="monto" class="form-label">Monto del Adeudo</label>
                            <input type="number" class="form-control" id="monto" name="monto" step="0.01" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Pagado">Pagado</option>
                                <option value="Caducado">Caducado</option>
                            </select>
                        </div>
                        <button type="submit" name="registrar_adeudo" class="btn btn-primary">Registrar Adeudo</button>
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

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/scripts.js"></script>

</body>
</html>

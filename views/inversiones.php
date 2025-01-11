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

// Consulta para obtener el nombre del usuario por el email
$sql = "SELECT nombre FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$nombre = "Usuario";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
}
$stmt->close();

// Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tipo'], $_POST['monto'], $_POST['rendimiento'], $_POST['fecha_inicio'], $_POST['fecha_fin'])) {
    $tipo = $_POST['tipo'];
    $monto = $_POST['monto'];
    $rendimiento = $_POST['rendimiento'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Obtener el id del usuario desde la sesión
    $stmtUser = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmtUser->bind_param("s", $email);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    if ($resultUser->num_rows > 0) {
        $userRow = $resultUser->fetch_assoc();
        $userId = $userRow['id_usuario'];

        // Insertar datos en la base de datos
        $sqlInsert = "INSERT INTO inversion (id_usuario, tipo, monto, rendimiento, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("isddss", $userId, $tipo, $monto, $rendimiento, $fecha_inicio, $fecha_fin);

        if ($stmtInsert->execute()) {
        } else {
            echo "<script>alert('Error al registrar la inversión.');</script>";
        }
        $stmtInsert->close();
    }
    $stmtUser->close();
}

// Consultar todas las inversiones
$sqlInversiones = "SELECT * FROM inversion";
$resultInversiones = $conn->query($sqlInversiones);
$inversiones = [];
if ($resultInversiones->num_rows > 0) {
    while ($row = $resultInversiones->fetch_assoc()) {
        $inversiones[] = $row;
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inversiones</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script>
        function toggleFormulario() {
            const formulario = document.getElementById("formulario-registro");
            formulario.style.display = formulario.style.display === "none" ? "block" : "none";
        }
    </script>
</head>
<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">Repaso de Cuentas</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i> <?= $nombre; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
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
                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                        Inicio
                    </a>
                    <a class="nav-link" href="inversiones.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                        Inversiones
                    </a>
                    <a class="nav-link" href="adeudos.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        Adeudos
                    </a>
                    <a class="nav-link" href="ingresos.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-wallet"></i></div>
                        Ingresos
                    </a>
                    <a class="nav-link" href="deudas.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-hand-holding-usd"></i></div>
                        Deudas
                    </a>
                    <a class="nav-link" href="presupuestos.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                        Presupuestos
                    </a>
                    <a class="nav-link" href="analisis_financiero.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                        Análisis Financiero
                    </a>
                    <a class="nav-link" href="exportar_datos.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-download"></i></div>
                        Exportar Datos Financieros
                    </a>
                    <a class="nav-link" href="informes_financieros.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                        Generar Informes Financieros
                    </a>
                    <a class="nav-link" href="educacion_financiera.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-graduation-cap"></i></div>
                        Educación Financiera
                    </a>   
                </div>
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="my-4 text-center">Gestión de Inversiones</h1>

                <!-- Tabla de inversiones -->
                <div id="tabla-inversiones" class="mb-4">
                    <h2 class="h4 mb-3">Listado de Inversiones</h2>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Rendimiento</th>
                                <th>Fecha inicio</th>
                                <th>Fecha fin</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inversiones as $inversion): ?>
                                <tr>
                                    <td><?= $inversion['id_inversion']; ?></td>
                                    <td><?= $inversion['tipo']; ?></td>
                                    <td>$<?= number_format($inversion['monto'], 2); ?></td>
                                    <td><?= $inversion['rendimiento']; ?>%</td>
                                    <td><?= $inversion['fecha_inicio']; ?></td>
                                    <td><?= $inversion['fecha_fin']; ?></td>
                                    <td>
                                        <a href="editar_inversion.php?id=<?= $inversion['id_inversion']; ?>" class="btn btn-warning btn-sm">Modificar</a>
                                        <a href="eliminar_inversion.php?id=<?= $inversion['id_inversion']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Botón para mostrar/ocultar formulario -->
                <div class="text-center mb-4">
                    <button class="btn btn-primary" onclick="toggleFormulario()">Agregar inversion</button>
                </div>

                <!-- Formulario de registro -->
                <div id="formulario-registro" style="display: none;">
                    <h2 class="h4">Registrar Inversión</h2>
                    <form action="inversiones.php" method="post">
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Inversión</label>
                            <input type="text" class="form-control" id="tipo" name="tipo" required>
                        </div>
                        <div class="mb-3">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="number" class="form-control" id="monto" name="monto" min="0" step="0.01" required>
                            <small class="text-muted">Debe ser un número positivo con máximo dos decimales.</small>
                        </div>
                        <div class="mb-3">
                            <label for="rendimiento" class="form-label">Rendimiento (%)</label>
                            <input type="number" class="form-control" id="rendimiento" name="rendimiento" min="0" step="0.01" required>
                            <small class="text-muted">Debe ser un número positivo con máximo dos decimales.</small>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                        <div id="mensaje-error" class="text-danger mb-3" style="display: none;"></div>
                        <button type="submit" class="btn btn-success">Registrar</button>
                    </form>

                </div>
            </div>
        </main>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">&copy; 2025 Repaso de Cuentas</div>
                </div>
            </div>
        </footer>
    </div>
</div>
<script>
    document.getElementById('formulario-registro').addEventListener('submit', function(event) {
    // Obtener valores de las fechas
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;

    // Convertir las fechas a objetos Date para compararlas
    const inicio = new Date(fechaInicio);
    const fin = new Date(fechaFin);

    // Referencia al contenedor del mensaje
    const mensajeError = document.getElementById('mensaje-error');

    // Validar que la fecha de fin sea mayor o igual a la fecha de inicio
    if (fin < inicio) {
        event.preventDefault(); // Detener el envío del formulario
        mensajeError.style.display = 'block';
        mensajeError.textContent = 'La fecha de fin debe ser igual o mayor a la fecha de inicio.';
    } else {
        mensajeError.style.display = 'none'; // Ocultar mensaje de error
    }
});

</script>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/scripts.js"></script>
</body>
</html>

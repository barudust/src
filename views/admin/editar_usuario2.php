<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include('../conexion.php');

// Obtener el ID del usuario desde la URL
$id_usuario = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id_usuario)) {
    header("Location: index.php");
    exit();
}

// Obtener los datos del usuario con el ID especificado
$sql = "SELECT nombre, email, rol, contraseña FROM usuarios WHERE id_usuario = '$id_usuario'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
    $email = $row['email'];
    $rol = $row['rol'];
    $password = $row['contraseña']; // Si se desea mostrar o editar la contraseña
} else {
    echo "No se encontró el usuario.";
    exit();
}

// Manejar la actualización de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $password = $_POST['password']; // Si la contraseña no está vacía, se actualiza

    if (!empty($password)) {
        $sql_update = "UPDATE usuarios SET nombre = '$nombre', email = '$email', rol = '$rol', contraseña = '$password' WHERE id_usuario = '$id_usuario'";
    } else {
        $sql_update = "UPDATE usuarios SET nombre = '$nombre', email = '$email', rol = '$rol' WHERE id_usuario = '$id_usuario'";
    }

    if ($conn->query($sql_update) === TRUE) {
        header("Location: index.php"); // Redirige al índice después de actualizar
        exit();
    } else {
        echo "Error al actualizar: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - Repaso de Cuentas</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <!-- Barra de navegación -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">Repaso de Cuentas</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i> 
                    <?= $nombre; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="cuenta.php">Cuenta</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    
    <!-- Contenido -->
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Inicio
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h2 class="my-4 text-center">Editar Usuario</h2>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $nombre; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= $email; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="Usuario" <?php if ($rol == 'Usuario') echo 'selected'; ?>>Usuario</option>
                                <option value="Administrador" <?php if ($rol == 'Administrador') echo 'selected'; ?>>Administrador</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </form>
                </div>
            </main>
            
            <!-- Footer -->
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy; 2024 Repaso de Cuentas. Todos los derechos reservados.</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="../../js/bootstrap.bundle.min.js"></script>
</body>
</html>

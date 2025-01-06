<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

include('../conexion.php');

$sql = "SELECT nombre FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
} else {
    $nombre = "Usuario";
}

// Manejar creación de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $emailUsuario = isset($_POST['email']) ? trim($_POST['email']) : '';
    $rolUsuario = isset($_POST['rol']) ? trim($_POST['rol']) : '';
    $contrasenaUsuario = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';

    if (empty($nombreUsuario) || empty($emailUsuario) || empty($rolUsuario) || empty($contrasenaUsuario)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($emailUsuario, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no tiene un formato válido.";
    } elseif (strlen($contrasenaUsuario) < 7) {
        $error = "La contraseña debe tener al menos 7 caracteres.";
    } elseif (!preg_match('/[a-z]/', $contrasenaUsuario) || !preg_match('/[A-Z]/', $contrasenaUsuario) || !preg_match('/\d/', $contrasenaUsuario)) {
        $error = "La contraseña debe contener al menos una letra minúscula, una mayúscula y un número.";
    } else {
        // Verificar si el correo ya está registrado
        $sqlCheckEmail = "SELECT email FROM usuarios WHERE email = '$emailUsuario'";
        $resultCheckEmail = $conn->query($sqlCheckEmail);

        if ($resultCheckEmail->num_rows > 0) {
            $error = "El correo electrónico ya está registrado.";
        } else {
            // Encriptar la contraseña antes de guardar
            $hashedPassword = password_hash($contrasenaUsuario, PASSWORD_BCRYPT);

            // Realizar inserción en la base de datos
            $sqlInsert = "INSERT INTO usuarios (nombre, email, rol, contraseña) VALUES ('$nombreUsuario', '$emailUsuario', '$rolUsuario', '$hashedPassword')";
            if ($conn->query($sqlInsert) === TRUE) {
                header("Location: index.php");
                exit();
            } else {
                $error = "Error al crear el usuario: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Usuario</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
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
                <section id="bienvenida" class="text-center mb-5">
                    <h2 class="my-4"><i class="fas fa-user-plus"></i> Crear Nuevo Usuario</h2>
                </section>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="nuevo_usuario.php" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= isset($nombreUsuario) ? $nombreUsuario : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= isset($emailUsuario) ? $emailUsuario : '' ?>">
                    </div>
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol:</label>
                        <select class="form-select" id="rol" name="rol">
                            <option value="" disabled>Selecciona un rol</option>
                            <option value="administrador" <?= isset($rolUsuario) && $rolUsuario == 'administrador' ? 'selected' : '' ?>>Administrador</option>
                            <option value="usuario" <?= isset($rolUsuario) && $rolUsuario == 'usuario' ? 'selected' : '' ?>>Usuario</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña:</label>
                        <input type="text" class="form-control" id="contrasena" name="contrasena" value="<?= isset($contrasenaUsuario) ? $contrasenaUsuario : '' ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
                </form>
            </div>
        </main>

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
<script src="../../js/scripts.js"></script>
</body>
</html>

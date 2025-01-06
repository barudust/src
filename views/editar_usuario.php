<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Conectar a la base de datos
include('conexion.php');

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para validar la contraseña
function validar_contraseña($contraseña) {
    if (strlen($contraseña) < 7) {
        return "La contraseña debe tener al menos 7 caracteres.";
    }
    if (!preg_match("/[A-Z]/", $contraseña)) {
        return "La contraseña debe contener al menos una mayúscula.";
    }
    if (!preg_match("/[a-z]/", $contraseña)) {
        return "La contraseña debe contener al menos una minúscula.";
    }
    if (!preg_match("/[0-9]/", $contraseña)) {
        return "La contraseña debe contener al menos un número.";
    }
    return true; // La contraseña es válida
}

// Función para validar el correo
function validar_correo($correo) {
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        return "El correo no tiene un formato válido.";
    }
    return true; // El correo es válido
}

// Definir variables para almacenar los mensajes de error
$errores = [];

// Si el formulario es enviado, actualiza la información del usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = $_POST['password']; // Se obtiene la contraseña del formulario

    // Validar el correo
    $validar_correo = validar_correo($correo);
    if ($validar_correo !== true) {
        $errores[] = $validar_correo;
    }

    // Validar la contraseña si se proporciona
    if (!empty($password)) {
        $validar_contraseña = validar_contraseña($password);
        if ($validar_contraseña !== true) {
            $errores[] = $validar_contraseña;
        }
    }

    // Si no hay errores, actualizar la base de datos
    if (empty($errores)) {
        // Si se proporciona una nueva contraseña, actualiza la contraseña
        if (!empty($password)) {
            // Encriptar la contraseña antes de almacenarla
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            $sql = "UPDATE usuarios SET nombre = '$nombre', email = '$correo', contraseña = '$password_hash' WHERE email = '$email'";
        } else {
            // Si no se proporciona nueva contraseña, se actualizan solo nombre y correo
            $sql = "UPDATE usuarios SET nombre = '$nombre', email = '$correo' WHERE email = '$email'";
        }

        if ($conn->query($sql) === TRUE) {
            $_SESSION['email'] = $correo; // Actualiza la sesión con el nuevo correo
            header("Location: cuenta.php"); // Redirige a la página de cuenta
            exit();
        } else {
            echo "Error al actualizar los datos: " . $conn->error;
        }
    }
}

// Consulta para obtener los datos actuales del usuario
$sql = "SELECT nombre, email FROM usuarios WHERE email = '$email'";
$result = $conn->query($sql);

// Verificar si la consulta se ejecutó correctamente y devolvió resultados
if ($result) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombre = $row['nombre'];
        $correo = $row['email'];
    } else {
        $nombre = "Usuario";
        $correo = "Correo no disponible";
    }
} else {
    echo "Error en la consulta: " . $conn->error;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Información</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">Repaso de Cuentas</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user fa-fw"></i> <?php echo $nombre; ?>
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
                    <!-- Menú de navegación -->
                </div>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Editar Información</h1>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user"></i> Edita tu información
                    </div>
                    <div class="card-body">
                        <?php
                        // Mostrar errores
                        if (!empty($errores)) {
                            echo "<div class='alert alert-danger'>";
                            foreach ($errores as $error) {
                                echo "<p>$error</p>";
                            }
                            echo "</div>";
                        }
                        ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $correo; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña (Opcional)</label>
                                <input type="password" class="form-control" id="password" name="password" value="" autocomplete="off" placeholder="Nueva contraseña (opcional)">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Actualizar Información</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">&copy; 2025 Repaso de Cuentas. Todos los derechos reservados.</div>
                </div>
            </div>
        </footer>
    </div>
</div>    

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/scripts.js"></script>
</body>
</html>

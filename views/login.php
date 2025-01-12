<?php
session_start(); // Inicia la sesión para manejar la autenticación

// Verifica si la sesión ya está iniciada, si es así, redirige al usuario al index
if (isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Incluye el archivo de conexión a la base de datos
include('conexion.php');

// Verifica si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtén los datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consulta SQL para obtener la información del usuario
    $sql = "SELECT * FROM usuarios WHERE email = ?";

    // Prepara la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincula el parámetro
        $stmt->bind_param("s", $email);
        
        // Ejecuta la consulta
        $stmt->execute();
        
        // Obtiene el resultado
        $result = $stmt->get_result();

        // Verifica si el usuario existe
        if ($result->num_rows > 0) {
            // Obtiene los datos del usuario
            $usuario = $result->fetch_assoc();
            
            // Verifica la contraseña
            if (password_verify($password, $usuario['contraseña'])) {
                // Guarda la sesión con el correo
                $_SESSION['email'] = $email;

                // Verifica el rol del usuario
                if ($usuario['rol'] == 'Administrador') {
                    // Redirige a la página de administración si es administrador
                    header("Location: admin/index.php");
                } else {
                    // Redirige al usuario normal al index
                    header("Location: index.php");
                }
                exit();
            } else {
                // Contraseña incorrecta
                echo "<script>alert('Credenciales incorrectas');</script>";
            }
        } else {
            // Usuario no encontrado
            echo "<script>alert('Credenciales incorrectas');</script>";
        }

        // Cierra la declaración
        $stmt->close();
    } else {
        // Si la consulta no se pudo preparar, muestra un error
        echo "Error al preparar la consulta: " . $conn->error;
    }

    // Cierra la conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Login</title>
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Login</h3>
                                </div>
                                <div class="card-body">
                                    <form id="loginForm" method="POST" action="login.php">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" required />
                                            <label for="inputEmail">Correo</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" required />
                                            <label for="inputPassword">Contraseña</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="password.php">¿Olvidaste tu contraseña?</a>
                                            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="registro.php">¿Necesitas una cuenta?</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy; 2024 Repaso de Cuentas. Todos los derechos reservados.</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>

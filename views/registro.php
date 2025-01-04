<?php
// Inicia la sesión para manejar la autenticación
session_start();

// Incluye el archivo de conexión a la base de datos
include('conexion.php');

// Verifica si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene los datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];
    $nombre = $_POST['nombre'];

    // Establecer el rol por defecto a "Usuario"
    $rol = 'Usuario';

    // Consulta SQL para insertar el nuevo usuario
    $sql = "INSERT INTO usuarios (email, contraseña, nombre, rol) VALUES (?, ?, ?, ?)";

    // Prepara la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincula los parámetros
        $stmt->bind_param("ssss", $email, $password, $nombre, $rol);
        
        // Ejecuta la consulta
        if ($stmt->execute()) {
            // Si la inserción fue exitosa, redirige al index.php
            header("Location: index.php");
            exit();
        } else {
            // Si ocurre un error al insertar, muestra un mensaje de error
            echo "<script>alert('Error al registrar el usuario. Inténtalo de nuevo.');</script>";
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
    <title>Registro</title>
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
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Registro de Usuario</h3></div>
                                <div class="card-body">
                                    <form id="registroForm" method="POST" action="registro.php">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputNombre" name="nombre" type="text" placeholder="Nombre" required />
                                            <label for="inputNombre">Nombre</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" name="email" type="email" placeholder="name@example.com" required />
                                            <label for="inputEmail">Correo</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" required />
                                            <label for="inputPassword">Contraseña</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button type="submit" class="btn btn-primary">Registrar</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="login.php">¿Ya tienes una cuenta? Inicia sesión</a></div>
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
                        <div class="text-muted"> &copy; 2024 Repaso de Cuentas. Todos los derechos reservados.</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>

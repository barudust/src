<?php
session_start(); // Inicia la sesión para manejar la autenticación

// Verifica si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtén los datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Aquí puedes conectar a tu base de datos
    $servername = "localhost";
    $username = "root"; // Tu usuario de base de datos
    $password_db = ""; // Tu contraseña de base de datos
    $dbname = "financiamiento"; // Tu base de datos

    // Crea una conexión con la base de datos
    $conn = new mysqli($servername, $username, $password_db, $dbname);

    // Verifica si la conexión fue exitosa
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta SQL para verificar si las credenciales son correctas
    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND contraseña = '$password'";

    // Ejecuta la consulta y guarda el resultado
    $result = $conn->query($sql);

    // Verifica si la consulta fue exitosa
    if ($result === false) {
        // Si la consulta falló, muestra el error SQL
        echo "Error en la consulta: " . $conn->error;
    } else {
        // Si la consulta fue exitosa, verifica el número de filas
        if ($result->num_rows > 0) {
            // Si las credenciales son correctas, redirige a index.php
            $_SESSION['email'] = $email; // Guarda la sesión
            header("Location: index.php");
            exit(); // Detiene la ejecución después de la redirección
        } else {
            // Si las credenciales no son correctas, muestra un mensaje de error
            echo "<script>alert('Credenciales incorrectas');</script>";
        }
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
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
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
                                            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="registro.html">¿Necesitas una cuenta?</a></div>
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
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>

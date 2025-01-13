<?php
include 'conexion.php';  // Asegúrate de que esta ruta sea correcta

// Verificar si el código de recuperación existe en la URL
if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];

    // Verificar si el código existe en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE codigo_recuperacion = ?");
    $stmt->execute([$codigo]);
    $user = $stmt->fetch();

    // Si el código es válido, mostrar el formulario de cambio de contraseña
    if ($user) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener la nueva contraseña y confirmar contraseña
            $nueva_contraseña = $_POST['nueva_contraseña'];
            $confirmar_contraseña = $_POST['confirmar_contraseña'];

            // Verificar que las contraseñas coinciden
            if ($nueva_contraseña !== $confirmar_contraseña) {
                echo "Las contraseñas no coinciden.";
                exit;
            }

            // Encriptar la nueva contraseña
            $hashed_password = password_hash($nueva_contraseña, PASSWORD_DEFAULT);

            // Actualizar la contraseña en la base de datos
            $stmt = $pdo->prepare("UPDATE usuarios SET contraseña = ?, codigo_recuperacion = NULL WHERE codigo_recuperacion = ?");
            $stmt->execute([$hashed_password, $codigo]);

            header('Location: mensajes/hola.html');
            exit;
        }
    } else {
        echo "Código de recuperación inválido o expirado.";
    }
} else {
    echo "No se proporcionó el código de recuperación.";
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
    <title>Cambiar Contraseña</title>
    <link href="../css/styles.css" rel="stylesheet" />
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Cambiar Contraseña</h3></div>
                                <div class="card-body">
                                    <form method="POST" action="cambiar.php?codigo=<?php echo $codigo; ?>">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" type="password" name="nueva_contraseña" placeholder="Nueva Contraseña" required />
                                            <label for="inputPassword">Nueva Contraseña</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputConfirmPassword" type="password" name="confirmar_contraseña" placeholder="Confirmar Contraseña" required />
                                            <label for="inputConfirmPassword">Confirmar Contraseña</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="login.php">Regresar al inicio de sesión</a>
                                            <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>

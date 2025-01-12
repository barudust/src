<?php
session_start();

// Verifica que el usuario esté autenticado
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirige al login si no está autenticado
    exit;
}

// Obtiene el correo del usuario desde la sesión
$email = $_SESSION['email'];

// Conexión a la base de datos
require 'conexion.php';

// Consulta para obtener el ID del usuario
$queryUsuario = "SELECT id_usuario FROM usuarios WHERE email = ?";
$stmtUsuario = $conn->prepare($queryUsuario);
$stmtUsuario->bind_param("s", $email);
$stmtUsuario->execute();
$resultUsuario = $stmtUsuario->get_result();

if ($resultUsuario->num_rows > 0) {
    $user = $resultUsuario->fetch_assoc();
    $id_usuario = $user['id_usuario'];
} else {
    echo "Usuario no encontrado.";
    exit;
}

// Consulta para obtener los adeudos del usuario
$queryAdeudos = "SELECT fecha, descripcion, monto, categoria FROM transaccion WHERE id_usuario = ? AND tipo = 'Adeudo'";
$stmtAdeudos = $conn->prepare($queryAdeudos);
$stmtAdeudos->bind_param("i", $id_usuario);
$stmtAdeudos->execute();
$resultAdeudos = $stmtAdeudos->get_result();

// Guarda los adeudos en un arreglo
$adeudos = [];
while ($row = $resultAdeudos->fetch_assoc()) {
    $adeudos[] = $row;
}

$stmtUsuario->close();
$stmtAdeudos->close();
$conn->close();

// El arreglo $adeudos está listo para ser utilizado en otro documento
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adeudos del Usuario</title>
</head>
<body>
    <h1>Adeudos del Usuario</h1>
    <p>Usuario: <?php echo htmlspecialchars($email); ?></p>

    <h2>Lista de Adeudos</h2>
    <?php if (count($adeudos) > 0): ?>
        <ul>
            <?php foreach ($adeudos as $adeudo): ?>
                <li>
                    <strong>Fecha:</strong> <?php echo htmlspecialchars($adeudo['fecha']); ?>,
                    <strong>Descripción:</strong> <?php echo htmlspecialchars($adeudo['descripcion']); ?>,
                    <strong>Monto:</strong> $<?php echo number_format($adeudo['monto'], 2); ?>,
                    <strong>Categoría:</strong> <?php echo htmlspecialchars($adeudo['categoria']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No se encontraron adeudos registrados.</p>
    <?php endif; ?>
</body>
</html>
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

// Consulta para obtener las inversiones del usuario
$queryInversiones = "SELECT fecha, descripcion, monto, categoria FROM transaccion WHERE id_usuario = ? AND tipo = 'Inversion'";
$stmtInversiones = $conn->prepare($queryInversiones);
$stmtInversiones->bind_param("i", $id_usuario);
$stmtInversiones->execute();
$resultInversiones = $stmtInversiones->get_result();

// Guarda las inversiones en un arreglo
$inversiones = [];
while ($row = $resultInversiones->fetch_assoc()) {
    $inversiones[] = $row;
}

$stmtUsuario->close();
$stmtInversiones->close();
$conn->close();

// El arreglo $inversiones está listo para ser utilizado en otro documento
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inversiones del Usuario</title>
</head>
<body>
    <h1>Inversiones del Usuario</h1>
    <p>Usuario: <?php echo htmlspecialchars($email); ?></p>

    <h2>Lista de Inversiones</h2>
    <?php if (count($inversiones) > 0): ?>
        <ul>
            <?php foreach ($inversiones as $inversion): ?>
                <li>
                    <strong>Fecha:</strong> <?php echo htmlspecialchars($inversion['fecha']); ?>,
                    <strong>Descripción:</strong> <?php echo htmlspecialchars($inversion['descripcion']); ?>,
                    <strong>Monto:</strong> $<?php echo number_format($inversion['monto'], 2); ?>,
                    <strong>Categoría:</strong> <?php echo htmlspecialchars($inversion['categoria']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No se encontraron inversiones registradas.</p>
    <?php endif; ?>
</body>
</html>
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

// Consulta para obtener los ingresos del usuario
$queryIngresos = "SELECT fecha, descripcion, monto, categoria FROM transaccion WHERE id_usuario = ? AND tipo = 'Ingreso'";
$stmtIngresos = $conn->prepare($queryIngresos);
$stmtIngresos->bind_param("i", $id_usuario);
$stmtIngresos->execute();
$resultIngresos = $stmtIngresos->get_result();

// Guarda los ingresos en un arreglo
$ingresos = [];
while ($row = $resultIngresos->fetch_assoc()) {
    $ingresos[] = $row;
}

$stmtUsuario->close();
$stmtIngresos->close();
$conn->close();

// El arreglo $ingresos está listo para ser utilizado en otro documento
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresos del Usuario</title>
</head>
<body>
    <h1>Ingresos del Usuario</h1>
    <p>Usuario: <?php echo htmlspecialchars($email); ?></p>

    <h2>Lista de Ingresos</h2>
    <?php if (count($ingresos) > 0): ?>
        <ul>
            <?php foreach ($ingresos as $ingreso): ?>
                <li>
                    <strong>Fecha:</strong> <?php echo htmlspecialchars($ingreso['fecha']); ?>,
                    <strong>Descripción:</strong> <?php echo htmlspecialchars($ingreso['descripcion']); ?>,
                    <strong>Monto:</strong> $<?php echo number_format($ingreso['monto'], 2); ?>,
                    <strong>Categoría:</strong> <?php echo htmlspecialchars($ingreso['categoria']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No se encontraron ingresos registrados.</p>
    <?php endif; ?>
</body>
</html>

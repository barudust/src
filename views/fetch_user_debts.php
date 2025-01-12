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

// Consulta para obtener las deudas del usuario
$queryDeudas = "SELECT fecha, descripcion, monto, categoria FROM transaccion WHERE id_usuario = ? AND tipo = 'Deuda'";
$stmtDeudas = $conn->prepare($queryDeudas);
$stmtDeudas->bind_param("i", $id_usuario);
$stmtDeudas->execute();
$resultDeudas = $stmtDeudas->get_result();

// Guarda las deudas en un arreglo
$deudas = [];
while ($row = $resultDeudas->fetch_assoc()) {
    $deudas[] = $row;
}

$stmtUsuario->close();
$stmtDeudas->close();
$conn->close();

// El arreglo $deudas está listo para ser utilizado en otro documento
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deudas del Usuario</title>
</head>
<body>
    <h1>Deudas del Usuario</h1>
    <p>Usuario: <?php echo htmlspecialchars($email); ?></p>

    <h2>Lista de Deudas</h2>
    <?php if (count($deudas) > 0): ?>
        <ul>
            <?php foreach ($deudas as $deuda): ?>
                <li>
                    <strong>Fecha:</strong> <?php echo htmlspecialchars($deuda['fecha']); ?>,
                    <strong>Descripción:</strong> <?php echo htmlspecialchars($deuda['descripcion']); ?>,
                    <strong>Monto:</strong> $<?php echo number_format($deuda['monto'], 2); ?>,
                    <strong>Categoría:</strong> <?php echo htmlspecialchars($deuda['categoria']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No se encontraron deudas registradas.</p>
    <?php endif; ?>
</body>
</html>
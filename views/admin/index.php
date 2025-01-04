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

// Manejar búsqueda
$busqueda = isset($_GET['busqueda']) ? $conn->real_escape_string($_GET['busqueda']) : '';
$sqlUsuarios = "SELECT id_usuario, nombre, email, rol FROM usuarios WHERE email != '$email'";

// Si hay un término de búsqueda, añadir cláusula WHERE
if (!empty($busqueda)) {
    $sqlUsuarios .= " AND nombre LIKE '%$busqueda%'";
}


$resultUsuarios = $conn->query($sqlUsuarios);

include('modales/ventana.php'); 

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Repaso de Cuentas</title>
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
                    <h2 class="my-4"><i class="fas fa-wallet"></i> Panel de administración</h2>
                </section>

                <div class="d-flex justify-content-center align-items-center mb-4">
                    <!-- Botón Crear Usuario -->
                    <a href="nuevo_usuario.php" class="btn btn-success me-3">
                        <i class="fas fa-user-plus"></i> Crear Usuario
                    </a>

                    <!-- Buscador -->
                    <form class="d-flex" method="GET" action="index.php">
                        <input 
                            class="form-control me-2" 
                            type="search" 
                            name="busqueda" 
                            placeholder="Buscar usuario..." 
                            aria-label="Buscar"
                        >
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </form>
                </div>

                <div class="container-md table-responsive text-nowrap">
                    <table class="table table-sm table-striped table-hover mt-4 table-bordered border border-black">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($resultUsuarios->num_rows > 0) {
                                while ($usuario = $resultUsuarios->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $usuario['id_usuario'] . "</td>";
                                    echo "<td>" . $usuario['nombre'] . "</td>";
                                    echo "<td>" . $usuario['email'] . "</td>";
                                    echo "<td>" . $usuario['rol'] . "</td>";
                                    echo "<td>
                                            <a href='ver.php?id=" . $usuario['id_usuario'] . "' class='btn btn-sm btn-primary'><i class='fas fa-user'></i> Ver perfil</a>
                                            <a href='editar_usuario2.php?id=" . $usuario['id_usuario'] . "' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i> Editar</a>
                                            <a href='#' class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#modalEliminar' data-id='" . $usuario['id_usuario'] . "'><i class='fas fa-trash'></i> Eliminar</a>
                                        </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No hay usuarios registrados</td></tr>";
                            }
                            ?>
                        </tbody>



                    </table>
                </div>
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

<script>
    const eliminarBtn = document.querySelectorAll('[data-bs-toggle="modal"]');
        eliminarBtn.forEach(btn => {
            btn.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                document.getElementById('eliminarUsuario').setAttribute('href', 'eliminar.php?id=' + userId);
            });
        });

</script>


<script src="../../js/bootstrap.bundle.min.js"></script>
<script src="../../js/scripts.js"></script>
</body>
</html>

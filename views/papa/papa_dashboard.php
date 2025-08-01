<?php
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/papa_dashboardController.php';

// ⚠️ Expiración por inactividad (20 minutos)
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1200)) {
    session_unset();
    session_destroy();
    header("Location: /index.php?expired=1");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Actualiza el tiempo de actividad



// 🧿 Control de sesión activa
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['nombre'])) {
    header("Location: /index.php?expired=1");
    exit;
}

// 🔐 Validación estricta por rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'papas') {
    die("🚫 Acceso restringido: esta sección es solo para el rol 'papas'.");
}

// 📦 Asignación de datos desde sesión
$usuario_id = $_SESSION['usuario_id'];
$usuario = $_SESSION['usuario'] ?? 'Sin usuario';
$nombre = $_SESSION['nombre'] ?? 'Sin nombre';
$correo = $_SESSION['correo'] ?? 'Sin correo';
$telefono = $_SESSION['telefono'] ?? 'Sin teléfono';
$saldo = $_SESSION['saldo'] ?? '0.00';


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IlMana Gastronomia</title>

    <!-- Íconos de Material Design -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <!-- Framework Success desde CDN -->
    <link rel="stylesheet" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css">
    <script src="https://www.fernandosalguero.com/cdn/assets/javascript/framework.js" defer></script>
</head>

<body>

    <!-- 🔲 CONTENEDOR PRINCIPAL -->
    <div class="layout">

        <!-- 🧭 SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <span class="material-icons logo-icon">dashboard</span>
                <span class="logo-text">AMPD</span>
            </div>

            <nav class="sidebar-menu">
                <ul>
                    <li onclick="location.href='admin_dashboard.php'">
                        <span class="material-icons" style="color: #5b21b6;">home</span><span class="link-text">Inicio</span>
                    </li>
                    <li onclick="location.href='admin_altaUsuarios.php'">
                        <span class="material-icons" style="color: #5b21b6;">person</span><span class="link-text">Alta usuarios</span>
                    </li>
                    <li onclick="location.href='admin_importarUsuarios.php'">
                        <span class="material-icons" style="color: #5b21b6;">upload_file</span><span class="link-text">Carga Masiva</span>
                    </li>
                    <li onclick="location.href='admin_pagoFacturas.php'">
                        <span class="material-icons" style="color: #5b21b6;">attach_money</span><span class="link-text">Pago Facturas</span>
                    </li>
                    <li onclick="location.href='../../../logout.php'">
                        <span class="material-icons" style="color: red;">logout</span><span class="link-text">Salir</span>
                    </li>
                </ul>
            </nav>


            <div class="sidebar-footer">
                <button class="btn-icon" onclick="toggleSidebar()">
                    <span class="material-icons" id="collapseIcon">chevron_left</span>
                </button>
            </div>
        </aside>

        <!-- 🧱 MAIN -->
        <div class="main">

            <!-- 🟪 NAVBAR -->
            <header class="navbar">
                <button class="btn-icon" onclick="toggleSidebar()">
                    <span class="material-icons">menu</span>
                </button>
                <div class="navbar-title">Inicio</div>
            </header>

            <!-- 📦 CONTENIDO -->
            <section class="content">

                <!-- Bienvenida -->
                <div class="card">
                    <h2>Hola 👋</h2>
                    <p>En esta página, vamos a tener KPI.</p>
                </div>

                <!-- Filtros -->
                <div class="card">
                    <form method="GET" class="form-modern">
                        <div class="grid-3">
                            <!-- Filtro: Hijo -->
                            <div class="input-group">
                                <label for="hijo_id">Nombre del alumno</label>
                                <div class="input-icon input-icon-user">
                                    <select id="hijo_id" name="hijo_id">
                                        <option value="">Todos</option>
                                        <?php foreach ($hijos as $hijo): ?>
                                            <option value="<?= $hijo['Id'] ?>" <?= $hijoSeleccionado == $hijo['Id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($hijo['Nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Filtro: Desde -->
                            <div class="input-group">
                                <label for="desde">Desde</label>
                                <div class="input-icon input-icon-date">
                                    <input type="date" id="desde" name="desde" value="<?= htmlspecialchars($desde) ?>">
                                </div>
                            </div>

                            <!-- Filtro: Hasta -->
                            <div class="input-group">
                                <label for="hasta">Hasta</label>
                                <div class="input-icon input-icon-date">
                                    <input type="date" id="hasta" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-buttons">
                            <button class="btn btn-aceptar" type="submit">Aplicar filtros</button>
                        </div>
                    </form>
                </div>


                <!-- Tablas de resultados -->
                <div class="card-grid grid-2">
                    <!-- Pedidos de Comida -->
                    <div class="card tabla-card">
                        <h2>Pedidos de comida</h2>
                        <div class="tabla-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha Entrega</th>
                                        <th>Fecha Pedido</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($pedidosComida)): ?>
                                        <?php foreach ($pedidosComida as $pedido): ?>
                                            <tr>
                                                <td><?= $pedido['Id'] ?></td>
                                                <td><?= $pedido['Fecha_entrega'] ?></td>
                                                <td><?= $pedido['Fecha_pedido'] ?></td>
                                                <td>
                                                    <span class="badge <?= $pedido['Estado'] === 'Procesando' ? 'success' : 'danger' ?>">
                                                        <?= $pedido['Estado'] ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4">No hay pedidos de comida.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pedidos de Saldo -->
                    <div class="card tabla-card">
                        <h2>Pedidos de saldo</h2>
                        <div class="tabla-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Fecha Pedido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($pedidosSaldo)): ?>
                                        <?php foreach ($pedidosSaldo as $saldo): ?>
                                            <tr>
                                                <td><?= $saldo['Id'] ?></td>
                                                <td>$<?= number_format($saldo['Saldo'], 2, ',', '.') ?></td>
                                                <td>
                                                    <span class="badge <?= $saldo['Estado'] === 'Aprobado' ? 'success' : ($saldo['Estado'] === 'Cancelado' ? 'danger' : 'warning') ?>">
                                                        <?= $saldo['Estado'] ?>
                                                    </span>
                                                </td>
                                                <td><?= $saldo['Fecha_pedido'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4">No hay pedidos de saldo.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </section>
        </div>
    </div>
    <!-- Spinner Global -->
    <script src="../partials/spinner-global.js"></script>

    <script>
        console.log(<?php echo json_encode($_SESSION); ?>);
    </script>
</body>

</html>
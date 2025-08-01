<?php
require_once __DIR__ . '/../models/papa_dashboardModel.php';


$model = new PapaDashboardModel($pdo);

$usuarioId = $_SESSION['usuario_id'] ?? null;
$hijoSeleccionado = $_GET['hijo_id'] ?? null;
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

$hijos = $model->obtenerHijosPorUsuario($usuarioId);
$pedidosSaldo = $model->obtenerPedidosSaldo($usuarioId, $desde, $hasta);
$pedidosComida = $model->obtenerPedidosComida($usuarioId, $hijoSeleccionado, $desde, $hasta);

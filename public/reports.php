<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../api/models/Order.php';

use Config\Database;
use Api\Models\Order;

// Obtener datos
$db = new Database();
$conn = $db->getConnection();
$orderModel = new Order($conn);
$orders = $orderModel->readAll();

// Calcular estadísticas básicas
$totalSales = 0;
$totalOrders = count($orders);
$avgTicket = 0;
$cashSales = 0;
$cardSales = 0;

foreach ($orders as $order) {
    $totalSales += $order['total'];
    if ($order['payment_method'] === 'Efectivo') {
        $cashSales += $order['total'];
    } else {
        $cardSales += $order['total'];
    }
}

if ($totalOrders > 0) {
    $avgTicket = $totalSales / $totalOrders;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas - KenkoPOS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-color: #f8fafc;
            --accent-color: #f59e0b;
            --accent-success: #10b981;
            --border-color: #334155;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding-bottom: 3rem;
        }

        .navbar-custom {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
        }

        .navbar-brand span {
            color: var(--accent-color);
            font-weight: 700;
        }

        .kpi-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .kpi-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent-color);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .kpi-icon {
            font-size: 2.2rem;
            color: var(--accent-color);
            opacity: 0.8;
        }

        .kpi-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-top: 0.5rem;
            margin-bottom: 0.2rem;
        }

        .kpi-title {
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .table-container {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .table-custom {
            color: var(--text-color);
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table-custom thead th {
            border-bottom: 1px solid var(--border-color);
            color: #94a3b8;
            font-weight: 600;
            padding: 1rem;
        }

        .table-custom tbody tr {
            background-color: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            transition: background-color 0.2s ease;
        }

        .table-custom tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
            cursor: pointer;
        }

        .table-custom td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }

        .badge-method {
            padding: 0.5rem 0.8rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-cash {
            background-color: rgba(16, 185, 129, 0.15);
            color: var(--accent-success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-card {
            background-color: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .pos-modal {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .pos-modal .modal-header {
            border-bottom: 1px solid var(--border-color);
        }

        .pos-modal .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        .receipt-item {
            border-bottom: 1px dashed rgba(255,255,255,0.08);
            padding: 0.5rem 0;
        }

        .receipt-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

    <!-- Navbar Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="assets/img/logo_kenkopos.png" alt="KenkoPOS" height="75" style="object-fit:contain;">
                <span class="fw-semibold fs-5 text-white">Reportes</span>
            </a>
            <div class="d-flex gap-2">
                <a href="pos.php" class="btn btn-outline-warning">
                    <i class="bi bi-receipt me-1"></i> Pantalla POS
                </a>
                <a href="products/list.php" class="btn btn-outline-light">
                    <i class="bi bi-gear-fill me-1"></i> Catálogo
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <h2 class="mb-4 fw-bold">Dashboard de Ventas</h2>

        <!-- Fila de KPI Cards -->
        <div class="row g-3">
            <div class="col-md-3">
                <div class="kpi-card d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-title">Ingresos Totales</div>
                        <div class="kpi-value text-success">$ <?= number_format($totalSales, 2, ',', '.') ?></div>
                        <div class="small text-white-50">Venta total acumulada</div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-cash-coin text-success"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-title">Comandas Cobradas</div>
                        <div class="kpi-value"><?= $totalOrders ?></div>
                        <div class="small text-white-50">Transacciones exitosas</div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-receipt-cutoff text-warning"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-title">Ticket Promedio</div>
                        <div class="kpi-value text-info">$ <?= number_format($avgTicket, 2, ',', '.') ?></div>
                        <div class="small text-white-50">Gasto promedio por mesa</div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-calculator text-info"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card d-flex align-items-center justify-content-between">
                    <div>
                        <div class="kpi-title">Método de Pago</div>
                        <div class="kpi-value" style="font-size: 1.4rem;">
                            <span class="text-success">Efe: <?= $totalSales > 0 ? round(($cashSales / $totalSales) * 100) : 0 ?>%</span> / 
                            <span class="text-primary">Tar: <?= $totalSales > 0 ? round(($cardSales / $totalSales) * 100) : 0 ?>%</span>
                        </div>
                        <div class="small text-white-50">Distribución de cobros</div>
                    </div>
                    <div class="kpi-icon"><i class="bi bi-credit-card-2-back text-primary"></i></div>
                </div>
            </div>
        </div>

        <!-- Tabla de Ventas Recientes -->
        <div class="table-container shadow-sm">
            <h4 class="fw-bold mb-4"><i class="bi bi-list-stars text-warning me-2"></i>Historial de Comandas Persistidas</h4>
            
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Mesa / Comanda</th>
                            <th>Fecha y Hora</th>
                            <th>Operador</th>
                            <th>Subtotal</th>
                            <th>Descuento</th>
                            <th>Servicio (10%)</th>
                            <th>Total Cobrado</th>
                            <th>Método</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($totalOrders > 0): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr onclick="showOrderDetails(<?= htmlspecialchars(json_encode($order)) ?>)">
                                    <td class="fw-bold"># <?= $order['order_id'] ?></td>
                                    <td>
                                        <span class="badge bg-warning text-dark px-3 py-1.5 fw-bold">
                                            <?= htmlspecialchars($order['table_name']) ?>
                                        </span>
                                    </td>
                                    <td class="text-white-50"><?= htmlspecialchars($order['created_at']) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($order['operator_name']) ?></div>
                                        <small class="text-muted text-uppercase" style="font-size: 0.7rem;"><?= htmlspecialchars($order['operator_role'] ?? 'admin') ?></small>
                                    </td>
                                    <td>$ <?= number_format($order['subtotal'], 2, ',', '.') ?></td>
                                    <td class="text-danger-emphasis">
                                        <?php if ($order['discount_amount'] > 0): ?>
                                            -$ <?= number_format($order['discount_amount'], 2, ',', '.') ?> (<?= $order['discount_percent'] ?>%)
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>$ <?= number_format($order['tax_amount'], 2, ',', '.') ?></td>
                                    <td class="fw-bold text-success">$ <?= number_format($order['total'], 2, ',', '.') ?></td>
                                    <td>
                                        <span class="badge-method <?= $order['payment_method'] === 'Efectivo' ? 'badge-cash' : 'badge-card' ?>">
                                            <i class="bi <?= $order['payment_method'] === 'Efectivo' ? 'bi-cash' : 'bi-credit-card' ?> me-1"></i>
                                            <?= htmlspecialchars($order['payment_method']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning" onclick="event.stopPropagation(); showOrderDetails(<?= htmlspecialchars(json_encode($order)) ?>)">
                                            <i class="bi bi-eye"></i> Detalle
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">No se han registrado comandas de ventas en la base de datos todavía.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL: Detalle de Comanda -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pos-modal shadow-lg">
                <div class="modal-header pos-modal">
                    <h5 class="modal-title fw-bold text-warning"><i class="bi bi-receipt-cutoff me-2"></i>Detalle de Ticket #<span id="modal-ticket-id"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Resumen del Ticket -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <span class="text-white-50 small d-block">Mesa</span>
                            <span class="fw-bold fs-5 text-white" id="modal-table-name"></span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-white-50 small d-block">Fecha</span>
                            <span class="fw-bold text-white" id="modal-date"></span>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-6">
                            <span class="text-white-50 small d-block">Operador</span>
                            <span class="fw-bold text-white" id="modal-operator"></span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-white-50 small d-block">Método Pago</span>
                            <span class="badge-method badge-cash d-inline-block mt-1" id="modal-payment-method"></span>
                        </div>
                    </div>

                    <h6 class="fw-bold border-bottom border-secondary pb-2 mb-2"><i class="bi bi-bag-fill text-warning me-2"></i>Productos Consumidos</h6>
                    <div id="modal-items-container" class="mb-4">
                        <!-- Items rendered dynamically -->
                    </div>

                    <!-- Totales -->
                    <div class="bg-dark p-3 rounded border border-secondary">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-white-50">Subtotal</span>
                            <span id="modal-subtotal"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-danger" id="modal-discount-row">
                            <span>Descuento</span>
                            <span id="modal-discount"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-white-50">Servicio Mesa (10%)</span>
                            <span id="modal-tax"></span>
                        </div>
                        <hr class="border-secondary my-2">
                        <div class="d-flex justify-content-between fw-bold fs-5 text-success">
                            <span>TOTAL COBRADO</span>
                            <span id="modal-total"></span>
                        </div>
                    </div>

                    <!-- Datos Efectivo -->
                    <div class="d-flex justify-content-between mt-2 px-2 small text-white-50" id="modal-cash-received-row">
                        <div>Efectivo Recibido: <span id="modal-cash-received"></span></div>
                        <div>Cambio Entregado: <span id="modal-change"></span></div>
                    </div>
                </div>
                <div class="modal-footer pos-modal">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Cerrar Detalle</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

        function formatMoney(amount) {
            return parseFloat(amount).toLocaleString('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 2
            });
        }

        function showOrderDetails(order) {
            document.getElementById('modal-ticket-id').textContent = order.order_id;
            document.getElementById('modal-table-name').textContent = order.table_name;
            document.getElementById('modal-date').textContent = order.created_at;
            document.getElementById('modal-operator').textContent = `${order.operator_name} (${order.operator_role})`;
            
            const payBadge = document.getElementById('modal-payment-method');
            payBadge.innerHTML = `<i class="bi ${order.payment_method === 'Efectivo' ? 'bi-cash' : 'bi-credit-card'} me-1"></i> ${order.payment_method}`;
            if (order.payment_method === 'Efectivo') {
                payBadge.className = 'badge-method badge-cash';
                document.getElementById('modal-cash-received-row').classList.remove('d-none');
                document.getElementById('modal-cash-received').textContent = formatMoney(order.cash_received);
                document.getElementById('modal-change').textContent = formatMoney(order.change_amount);
            } else {
                payBadge.className = 'badge-method badge-card';
                document.getElementById('modal-cash-received-row').classList.add('d-none');
            }

            // Totales
            document.getElementById('modal-subtotal').textContent = formatMoney(order.subtotal);
            const discRow = document.getElementById('modal-discount-row');
            if (parseFloat(order.discount_amount) > 0) {
                discRow.classList.remove('d-none');
                document.getElementById('modal-discount').textContent = `-${formatMoney(order.discount_amount)} (${order.discount_percent}%)`;
            } else {
                discRow.classList.add('d-none');
            }
            document.getElementById('modal-tax').textContent = formatMoney(order.tax_amount);
            document.getElementById('modal-total').textContent = formatMoney(order.total);

            // Renderizar items
            const container = document.getElementById('modal-items-container');
            container.innerHTML = '';
            if (order.items && order.items.length > 0) {
                order.items.forEach(item => {
                    const subtotal = parseFloat(item.price) * parseInt(item.qty);
                    const div = document.createElement('div');
                    div.className = 'd-flex justify-content-between align-items-center receipt-item';
                    div.innerHTML = `
                        <div>
                            <span class="fw-bold text-warning me-2">${item.qty}x</span>
                            <span>${item.name}</span>
                            <span class="text-white-50 d-block small" style="font-size: 0.75rem;">${item.sku} @ ${formatMoney(item.price)}</span>
                        </div>
                        <span class="fw-bold">${formatMoney(subtotal)}</span>
                    `;
                    container.appendChild(div);
                });
            } else {
                container.innerHTML = '<div class="text-center text-muted">No se registran productos en este ticket.</div>';
            }

            detailModal.show();
        }
    </script>
</body>
</html>

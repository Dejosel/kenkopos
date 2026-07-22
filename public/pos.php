<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/Controllers/ProductController.php';
require_once __DIR__ . '/../config/database.php';

use App\Controllers\ProductController;
use Config\Database;

$controller = new ProductController();
$products = $controller->index();

// Determinar el motor de base de datos para mostrar el estado en el header
$dbEngine = 'MySQL';
try {
    $db = new Database();
    $conn = $db->getConnection();
    $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'sqlite') {
        $dbEngine = 'SQLite Local';
    } else {
        $dbEngine = 'MySQL Activa';
    }
} catch (Exception $e) {
    $dbEngine = 'Desconectado';
}

// Extraer categorías únicas para los botones de filtro
$categories = [];
foreach ($products as $prod) {
    if (!empty($prod['category'])) {
        $categories[] = $prod['category'];
    }
}
$categories = array_unique($categories);
sort($categories);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta POS - KenkoPOS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="assets/css/pos.css">
</head>
<body class="pos-body">

    <!-- Header superior del POS -->
    <header class="pos-header">
        <div class="d-flex align-items-center gap-3">
            <div class="pos-logo">Kenko<span>POS</span></div>
            <span class="db-indicator <?= strpos(strtolower($dbEngine), 'sqlite') !== false ? 'sqlite' : 'mysql' ?>">
                <i class="bi bi-database-fill me-1"></i> <?= htmlspecialchars($dbEngine) ?>
            </span>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <span class="text-white-50"><i class="bi bi-clock me-1"></i> <span id="pos-clock">--:--:--</span></span>
            <a href="products/list.php" class="btn btn-outline-light btn-sm px-3">
                <i class="bi bi-gear-fill me-1"></i> Catálogo de Productos
            </a>
        </div>
    </header>

    <!-- Contenedor POS principal -->
    <div class="pos-wrapper">
        
        <!-- COLUMNA IZQUIERDA: Comanda / Ticket y Teclado Numérico -->
        <aside class="pos-sidebar">
            <!-- Encabezado del ticket -->
            <div class="ticket-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-receipt me-2 text-warning"></i>Comanda</h5>
                    <small class="text-white-50">Cliente General</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-warning text-dark px-3 py-2 fs-6 fw-bold" id="display-table-name" style="cursor: pointer;" onclick="openTableSelector()">Mesa 1</span>
                </div>
            </div>

            <!-- Listado de productos en la comanda -->
            <div class="ticket-items-container">
                <table class="ticket-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Descripción</th>
                            <th>Cant.</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody id="ticket-table-body">
                        <!-- Renderizado por pos.js -->
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                Sin productos cargados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Resumen de totales -->
            <div class="ticket-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="summary-subtotal">$ 0,00</span>
                </div>
                <div class="summary-row d-none">
                    <span>Descuento</span>
                    <span class="text-danger" id="summary-discount">-$ 0,00</span>
                </div>
                <div class="summary-row">
                    <span>Servicio Mesa (10%)</span>
                    <span id="summary-tax">$ 0,00</span>
                </div>
                <div class="summary-row total">
                    <span>TOTAL A PAGAR</span>
                    <span id="summary-total">$ 0,00</span>
                </div>
            </div>

            <!-- Teclado Numérico integrado -->
            <div class="pos-keypad-container">
                <div class="keypad-display-row">
                    <span>Teclado Comanda:</span>
                    <div class="keypad-display-val" id="keypad-display-val">---</div>
                </div>
                <div class="pos-keypad">
                    <button class="btn-key" onclick="pressKey('7')">7</button>
                    <button class="btn-key" onclick="pressKey('8')">8</button>
                    <button class="btn-key" onclick="pressKey('9')">9</button>
                    <button class="btn-key accent" onclick="applyKeypadQty()"><i class="bi bi-asterisk"></i> CANT</button>
                    
                    <button class="btn-key" onclick="pressKey('4')">4</button>
                    <button class="btn-key" onclick="pressKey('5')">5</button>
                    <button class="btn-key" onclick="pressKey('6')">6</button>
                    <button class="btn-key accent" onclick="applyKeypadPrice()"><i class="bi bi-currency-dollar"></i> PREC</button>
                    
                    <button class="btn-key" onclick="pressKey('1')">1</button>
                    <button class="btn-key" onclick="pressKey('2')">2</button>
                    <button class="btn-key" onclick="pressKey('3')">3</button>
                    <button class="btn-key accent" onclick="applyKeypadDiscount()"><i class="bi bi-percent"></i> DESC</button>
                    
                    <button class="btn-key danger" onclick="pressKey('C')">C</button>
                    <button class="btn-key" onclick="pressKey('0')">0</button>
                    <button class="btn-key danger" onclick="pressKey('BK')"><i class="bi bi-backspace"></i></button>
                    <button class="btn-key accent" onclick="openTableSelector()"><i class="bi bi-door-closed"></i> MESA</button>
                </div>
            </div>

            <!-- Botones de acciones inferiores -->
            <div class="ticket-actions">
                <button class="btn-pos-action cancel" onclick="cancelCurrentTicket()">
                    <i class="bi bi-trash fs-5"></i>Vaciar
                </button>
                <button class="btn-pos-action discount" onclick="openDiscountModal()">
                    <i class="bi bi-percent fs-5"></i>Desc.
                </button>
                <button class="btn-pos-action print" onclick="printPrecuenta()">
                    <i class="bi bi-printer fs-5"></i>Precuenta
                </button>
                <button class="btn-pos-action pay" onclick="openPaymentModal()">
                    <i class="bi bi-credit-card-2-back fs-4"></i>Cobrar
                </button>
            </div>
        </aside>

        <!-- COLUMNA DERECHA: Buscador, Categorías y Productos -->
        <main class="pos-menu">
            <!-- Barra de búsqueda rápida -->
            <div class="pos-search-wrapper">
                <i class="bi bi-search pos-search-icon"></i>
                <input type="text" class="pos-search-input" id="pos-search-input" placeholder="Buscar producto por nombre o SKU..." autocomplete="off">
            </div>

            <!-- Filtros por Categoría -->
            <div class="pos-categories" id="category-container">
                <button class="btn-category active" data-category="TODOS" onclick="selectCategory('TODOS')">
                    <i class="bi bi-grid-fill me-1"></i> TODOS
                </button>
                <?php foreach ($categories as $category): ?>
                    <button class="btn-category" data-category="<?= htmlspecialchars($category) ?>" onclick="selectCategory('<?= htmlspecialchars($category) ?>')">
                        <?= htmlspecialchars($category) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Cuadrícula de Productos -->
            <div class="pos-grid-container">
                <div class="pos-grid" id="products-grid">
                    <!-- Cargados por Javascript -->
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL: Cambiar de Mesa -->
    <div class="modal fade" id="tableModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pos-modal">
                <div class="modal-header pos-modal">
                    <h5 class="modal-title"><i class="bi bi-door-open-fill text-warning me-2"></i>Seleccionar Mesa / Comanda</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="text-white-50">Elige la mesa correspondiente a esta orden:</p>
                    <div class="row row-cols-3 g-2 justify-content-center">
                        <?php for ($i = 1; $i <= 9; $i++): ?>
                            <div class="col">
                                <button class="btn btn-table-opt w-100 py-3 <?= $i === 1 ? 'btn-primary' : 'btn-outline-secondary text-white' ?>" 
                                        data-table="Mesa <?= $i ?>" 
                                        onclick="selectCurrentTable('Mesa <?= $i ?>')">
                                    Mesa <?= $i ?>
                                </button>
                            </div>
                        <?php endfor; ?>
                        <div class="col">
                            <button class="btn btn-table-opt w-100 py-3 btn-outline-secondary text-white" data-table="Bar" onclick="selectCurrentTable('Bar')">Bar</button>
                        </div>
                        <div class="col">
                            <button class="btn btn-table-opt w-100 py-3 btn-outline-secondary text-white" data-table="Llevar" onclick="selectCurrentTable('Para Llevar')">Para Llevar</button>
                        </div>
                        <div class="col">
                            <button class="btn btn-table-opt w-100 py-3 btn-outline-secondary text-white" data-table="Domicilio" onclick="selectCurrentTable('Domicilio')">Domicilio</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: Aplicar Descuento -->
    <div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content pos-modal">
                <div class="modal-header pos-modal">
                    <h5 class="modal-title"><i class="bi bi-percent text-warning me-2"></i>Descuento Ticket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="discount-options">
                        <button class="btn btn-discount-opt" onclick="applyQuickDiscount(5)">5%</button>
                        <button class="btn btn-discount-opt" onclick="applyQuickDiscount(10)">10%</button>
                        <button class="btn btn-discount-opt" onclick="applyQuickDiscount(15)">15%</button>
                        <button class="btn btn-discount-opt" onclick="applyQuickDiscount(20)">20%</button>
                    </div>
                    <div class="mb-3">
                        <label for="custom-discount-input" class="form-label text-white-50">Descuento Personalizado (%)</label>
                        <input type="number" class="form-control bg-dark text-white border-secondary text-center fs-4 fw-bold" id="custom-discount-input" min="0" max="100" value="0">
                    </div>
                    <button class="btn btn-success w-100 py-2 fw-semibold" onclick="applyCustomDiscountSubmit()">Aplicar Descuento</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: Cobro de Orden -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pos-modal">
                <div class="modal-header pos-modal">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-cash-coin text-success me-2 fs-4"></i>Procesar Pago</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <!-- Método de pago -->
                    <div class="payment-methods">
                        <button type="button" class="btn-pay-method active" id="btn-pay-cash" onclick="setPaymentMethod('Efectivo')">
                            <i class="bi bi-cash fs-4"></i> Efectivo
                        </button>
                        <button type="button" class="btn-pay-method" id="btn-pay-card" onclick="setPaymentMethod('Tarjeta')">
                            <i class="bi bi-credit-card fs-4"></i> Tarjeta
                        </button>
                    </div>

                    <!-- Total a Pagar -->
                    <div class="bg-dark p-3 rounded mb-3 border border-secondary text-center">
                        <span class="text-white-50 d-block fs-6">TOTAL A COBRAR</span>
                        <span class="text-success fs-1 fw-bold" id="pay-total-payable">$ 0,00</span>
                    </div>

                    <!-- Sección Efectivo (Cálculo de Cambio) -->
                    <div id="cash-calculation-group">
                        <div class="mb-3">
                            <label class="form-label text-white-50">Efectivo Recibido</label>
                            <input type="number" class="form-control bg-dark text-white border-secondary text-center fs-2 fw-bold" id="pay-cash-input" value="0">
                        </div>

                        <!-- Sugerencias de billetes rápidos -->
                        <div class="d-flex justify-content-between gap-1 mb-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary text-white flex-fill py-2" onclick="suggestCashAmount(5000)">$5.000</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary text-white flex-fill py-2" onclick="suggestCashAmount(10000)">$10.000</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary text-white flex-fill py-2" onclick="suggestCashAmount(20000)">$20.000</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary text-white flex-fill py-2" onclick="suggestCashAmount(50000)">$50.000</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary text-white flex-fill py-2" onclick="suggestCashAmount(100000)">$100.000</button>
                        </div>

                        <!-- Cambio a entregar -->
                        <div class="d-flex justify-content-between align-items-center p-3 rounded mb-3 bg-dark border border-secondary">
                            <span class="fw-semibold text-white-50">CAMBIO A ENTREGAR</span>
                            <span class="fs-4 fw-bold" id="pay-change-val">$ 0,00</span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer pos-modal d-block">
                    <button class="btn btn-success w-100 py-3 fw-bold fs-5 shadow" onclick="confirmPayment()">
                        <i class="bi bi-check-circle-fill me-2"></i> CONFIRMAR TRANSACCIÓN
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: Recibo Térmico de Venta -->
    <div class="modal fade" id="receiptModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content pos-modal">
                <div class="modal-header pos-modal">
                    <h5 class="modal-title"><i class="bi bi-printer-fill me-2 text-info"></i>Recibo de Venta</h5>
                </div>
                <div class="modal-body bg-secondary-subtle py-4" style="max-height: 70vh; overflow-y: auto;">
                    
                    <div id="receipt-thermal-content">
                        <!-- Generado dinámicamente por JS -->
                    </div>

                </div>
                <div class="modal-footer pos-modal d-flex justify-content-between">
                    <button class="btn btn-primary px-3" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
                    <button class="btn btn-success px-4" onclick="closeReceiptAndReset()"><i class="bi bi-check2"></i> Listo (Nueva Venta)</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cargar productos desde PHP a JavaScript -->
    <script>
        const allProducts = <?php echo json_encode($products); ?>;
    </script>
    
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script de reloj y POS interactivo -->
    <script>
        // Reloj en tiempo real
        function updateClock() {
            const clockEl = document.getElementById('pos-clock');
            if (clockEl) {
                const now = new Date();
                clockEl.textContent = now.toLocaleTimeString('es-CO');
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    
    <script src="assets/js/pos.js"></script>
</body>
</html>

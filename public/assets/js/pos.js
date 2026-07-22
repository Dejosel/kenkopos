/**
 * KenkoPOS - SambaPOS Style Frontend Logic
 */

document.addEventListener('DOMContentLoaded', function() {
    // ----------------------------------------
    // Estado de la Aplicación (POS State)
    // ----------------------------------------
    let ticket = {
        tableName: 'Mesa 1',
        items: [],
        discountPercent: 0,
        taxPercent: 10, // 10% de Servicio
    };
    
    let activeItemIndex = null; // Fila del ticket seleccionada
    let keypadBuffer = "";      // Memoria del teclado numérico
    let currentCategory = 'TODOS';
    let searchQuery = "";
    let activePaymentMethod = 'Efectivo';
    
    // Elementos DOM
    const ticketTableBody = document.getElementById('ticket-table-body');
    const displayTableName = document.getElementById('display-table-name');
    const summarySubtotal = document.getElementById('summary-subtotal');
    const summaryTax = document.getElementById('summary-tax');
    const summaryDiscount = document.getElementById('summary-discount');
    const summaryTotal = document.getElementById('summary-total');
    const keypadDisplayVal = document.getElementById('keypad-display-val');
    const searchInput = document.getElementById('pos-search-input');
    const productsGrid = document.getElementById('products-grid');
    const categoryContainer = document.getElementById('category-container');
    
    // Modales Bootstrap
    const payModal = new bootstrap.Modal(document.getElementById('payModal'));
    const discountModal = new bootstrap.Modal(document.getElementById('discountModal'));
    const tableModal = new bootstrap.Modal(document.getElementById('tableModal'));
    const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));

    // ----------------------------------------
    // Renderizado de Productos y Categorías
    // ----------------------------------------
    function renderProducts() {
        productsGrid.innerHTML = '';
        
        // Filtrar productos
        const filtered = allProducts.filter(prod => {
            const matchesCat = currentCategory === 'TODOS' || prod.category === currentCategory;
            const matchesSearch = prod.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                                  prod.sku.toLowerCase().includes(searchQuery.toLowerCase());
            return matchesCat && matchesSearch;
        });

        if (filtered.length === 0) {
            productsGrid.innerHTML = `
                <div class="col-span-full text-center py-5 text-slate-400">
                    <p class="mb-0">No se encontraron productos en esta sección.</p>
                </div>
            `;
            return;
        }

        filtered.forEach(product => {
            const card = document.createElement('div');
            card.className = 'product-card';
            card.style.setProperty('--card-accent-color', product.color || '#0d6efd');
            
            // Si el color existe, le damos un tono sutil de fondo
            if (product.color) {
                card.style.backgroundColor = `${product.color}15`; // hex + 15 = ~8% opacidad
                card.style.borderColor = `${product.color}40`;     // hex + 40 = ~25% opacidad
            }

            card.innerHTML = `
                <div class="product-name">${escapeHTML(product.name)}</div>
                <div class="d-flex justify-content-between align-items-end mt-auto">
                    <span class="product-sku">${escapeHTML(product.sku)}</span>
                    <span class="product-price">$ ${formatMoney(product.price)}</span>
                </div>
            `;
            
            card.addEventListener('click', () => {
                addProductToTicket(product);
            });
            
            productsGrid.appendChild(card);
        });
    }

    // Cambiar Categoría Activa
    window.selectCategory = function(catName) {
        currentCategory = catName;
        
        // Actualizar botones de categoría en UI
        const buttons = categoryContainer.querySelectorAll('.btn-category');
        buttons.forEach(btn => {
            if (btn.getAttribute('data-category') === catName) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        renderProducts();
    };

    // Escucha de búsqueda en tiempo real
    searchInput.addEventListener('input', function(e) {
        searchQuery = e.target.value;
        renderProducts();
    });

    // ----------------------------------------
    // Gestión de Comanda / Ticket
    // ----------------------------------------
    function addProductToTicket(product) {
        // Determinar cantidad inicial (si hay buffer en el teclado se usa, si no, es 1)
        let qtyToAdd = 1;
        if (keypadBuffer !== "") {
            const parsed = parseInt(keypadBuffer);
            if (!isNaN(parsed) && parsed > 0) {
                qtyToAdd = parsed;
            }
            clearKeypad();
        }

        // Buscar si ya existe el ítem en la comanda
        const existingIndex = ticket.items.findIndex(item => item.product_id === product.product_id);
        
        if (existingIndex !== -1) {
            ticket.items[existingIndex].qty += qtyToAdd;
            activeItemIndex = existingIndex;
        } else {
            ticket.items.push({
                product_id: parseInt(product.product_id),
                name: product.name,
                sku: product.sku,
                price: parseFloat(product.price),
                qty: qtyToAdd
            });
            activeItemIndex = ticket.items.length - 1;
        }
        
        updateTicketUI();
    }

    function updateTicketUI() {
        ticketTableBody.innerHTML = '';
        displayTableName.textContent = ticket.tableName;

        if (ticket.items.length === 0) {
            ticketTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        Sin productos cargados
                    </td>
                </tr>
            `;
            activeItemIndex = null;
            calculateTotals();
            return;
        }

        ticket.items.forEach((item, index) => {
            const row = document.createElement('tr');
            row.className = `ticket-row ${activeItemIndex === index ? 'active' : ''}`;
            
            const subtotal = item.price * item.qty;
            
            row.innerHTML = `
                <td style="width: 40px; text-align: center;">
                    <button class="btn-item-delete" data-index="${index}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                          <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                        </svg>
                    </button>
                </td>
                <td>
                    <div class="fw-semibold">${escapeHTML(item.name)}</div>
                    <small class="text-muted">${escapeHTML(item.sku)} @ $ ${formatMoney(item.price)}</small>
                </td>
                <td style="width: 110px;">
                    <div class="qty-control">
                        <button class="btn-qty decrease" data-index="${index}">-</button>
                        <span class="fw-bold px-1">${item.qty}</span>
                        <button class="btn-qty increase" data-index="${index}">+</button>
                    </div>
                </td>
                <td class="text-end fw-bold" style="width: 100px;">
                    $ ${formatMoney(subtotal)}
                </td>
            `;
            
            // Seleccionar fila al hacer clic (excepto si se hace clic en botones de qty/delete)
            row.addEventListener('click', function(e) {
                if (!e.target.closest('.qty-control') && !e.target.closest('.btn-item-delete')) {
                    activeItemIndex = index;
                    updateTicketUI();
                }
            });
            
            ticketTableBody.appendChild(row);
        });

        calculateTotals();
        
        // Auto-scroll al fondo
        const container = document.querySelector('.ticket-items-container');
        container.scrollTop = container.scrollHeight;
    }

    // Acciones de clicks delegados en la tabla del ticket
    ticketTableBody.addEventListener('click', function(e) {
        const btnDelete = e.target.closest('.btn-item-delete');
        const btnQty = e.target.closest('.btn-qty');
        
        if (btnDelete) {
            const index = parseInt(btnDelete.getAttribute('data-index'));
            ticket.items.splice(index, 1);
            if (activeItemIndex >= ticket.items.length) {
                activeItemIndex = ticket.items.length > 0 ? ticket.items.length - 1 : null;
            }
            updateTicketUI();
        }
        
        if (btnQty) {
            const index = parseInt(btnQty.getAttribute('data-index'));
            if (btnQty.classList.contains('increase')) {
                ticket.items[index].qty += 1;
            } else if (btnQty.classList.contains('decrease')) {
                ticket.items[index].qty -= 1;
                if (ticket.items[index].qty <= 0) {
                    ticket.items.splice(index, 1);
                    if (activeItemIndex >= ticket.items.length) {
                        activeItemIndex = ticket.items.length > 0 ? ticket.items.length - 1 : null;
                    }
                }
            }
            updateTicketUI();
        }
    });

    function calculateTotals() {
        let subtotal = 0;
        ticket.items.forEach(item => {
            subtotal += item.price * item.qty;
        });

        const discountAmount = subtotal * (ticket.discountPercent / 100);
        const taxableAmount = subtotal - discountAmount;
        const taxAmount = taxableAmount * (ticket.taxPercent / 100);
        const total = taxableAmount + taxAmount;

        summarySubtotal.textContent = `$ ${formatMoney(subtotal)}`;
        summaryTax.textContent = `$ ${formatMoney(taxAmount)} (${ticket.taxPercent}%)`;
        
        if (ticket.discountPercent > 0) {
            summaryDiscount.textContent = `-$ ${formatMoney(discountAmount)} (${ticket.discountPercent}%)`;
            summaryDiscount.parentElement.classList.remove('d-none');
        } else {
            summaryDiscount.parentElement.classList.add('d-none');
        }
        
        summaryTotal.textContent = `$ ${formatMoney(total)}`;
        
        // Guardar el total en un atributo de datos en el ticket para uso posterior
        ticket.totalVal = total;
        ticket.subtotalVal = subtotal;
        ticket.taxVal = taxAmount;
        ticket.discountVal = discountAmount;
    }

    // ----------------------------------------
    // Gestión del Teclado Numérico (Keypad)
    // ----------------------------------------
    window.pressKey = function(key) {
        if (key === 'C') {
            clearKeypad();
        } else if (key === 'BK') {
            keypadBuffer = keypadBuffer.slice(0, -1);
            updateKeypadUI();
        } else {
            // Limitar longitud
            if (keypadBuffer.length < 8) {
                keypadBuffer += key;
                updateKeypadUI();
            }
        }
    };

    function clearKeypad() {
        keypadBuffer = "";
        updateKeypadUI();
    }

    function updateKeypadUI() {
        keypadDisplayVal.textContent = keypadBuffer === "" ? "---" : keypadBuffer;
    }

    // Acción del teclado: CANT (Establecer cantidad de fila activa)
    window.applyKeypadQty = function() {
        if (keypadBuffer === "") return;
        const newQty = parseInt(keypadBuffer);
        
        if (!isNaN(newQty) && newQty > 0 && activeItemIndex !== null && ticket.items[activeItemIndex]) {
            ticket.items[activeItemIndex].qty = newQty;
            updateTicketUI();
        }
        clearKeypad();
    };

    // Acción del teclado: PRECIO (Cambiar precio unitario de fila activa - requiere contraseña/permiso comúnmente, aquí directo para el demo)
    window.applyKeypadPrice = function() {
        if (keypadBuffer === "") return;
        const newPrice = parseFloat(keypadBuffer);
        
        if (!isNaN(newPrice) && newPrice >= 0 && activeItemIndex !== null && ticket.items[activeItemIndex]) {
            ticket.items[activeItemIndex].price = newPrice;
            updateTicketUI();
        }
        clearKeypad();
    };

    // Acción del teclado: DESC (Aplicar descuento directo en porcentaje)
    window.applyKeypadDiscount = function() {
        if (keypadBuffer === "") return;
        const disc = parseInt(keypadBuffer);
        
        if (!isNaN(disc) && disc >= 0 && disc <= 100) {
            ticket.discountPercent = disc;
            updateTicketUI();
        }
        clearKeypad();
    };

    // ----------------------------------------
    // Modales y Acciones Principales
    // ----------------------------------------

    // Cancelar/Vaciar Ticket
    window.cancelCurrentTicket = function() {
        if (ticket.items.length === 0) return;
        
        if (confirm("¿Estás seguro de que deseas cancelar la comanda actual de la " + ticket.tableName + "?")) {
            resetTicket();
        }
    };

    function resetTicket() {
        ticket.items = [];
        ticket.discountPercent = 0;
        activeItemIndex = null;
        clearKeypad();
        updateTicketUI();
    }

    // Cambiar de Mesa
    window.openTableSelector = function() {
        tableModal.show();
    };

    window.selectCurrentTable = function(tableName) {
        ticket.tableName = tableName;
        displayTableName.textContent = tableName;
        
        // Actualizar botones en modal
        document.querySelectorAll('.btn-table-opt').forEach(btn => {
            if (btn.getAttribute('data-table') === tableName) {
                btn.classList.add('btn-primary');
                btn.classList.remove('btn-outline-secondary');
            } else {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-secondary');
            }
        });
        
        tableModal.hide();
    };

    // Descuentos en Ticket
    window.openDiscountModal = function() {
        if (ticket.items.length === 0) return;
        
        // Set valor actual en el input
        document.getElementById('custom-discount-input').value = ticket.discountPercent;
        discountModal.show();
    };

    window.applyQuickDiscount = function(percent) {
        ticket.discountPercent = percent;
        updateTicketUI();
        discountModal.hide();
    };

    window.applyCustomDiscountSubmit = function() {
        const input = document.getElementById('custom-discount-input');
        const percent = parseInt(input.value);
        if (!isNaN(percent) && percent >= 0 && percent <= 100) {
            ticket.discountPercent = percent;
            updateTicketUI();
            discountModal.hide();
        } else {
            alert("Por favor ingresa un porcentaje válido entre 0 y 100.");
        }
    };

    // Cobrar / Pagar
    window.openPaymentModal = function() {
        if (ticket.items.length === 0) {
            alert("La comanda está vacía. Añade productos antes de cobrar.");
            return;
        }

        const totalPayable = ticket.totalVal;
        document.getElementById('pay-total-payable').textContent = `$ ${formatMoney(totalPayable)}`;
        
        // Configurar display de efectivo
        const payCashInput = document.getElementById('pay-cash-input');
        payCashInput.value = Math.ceil(totalPayable / 1000) * 1000; // Sugiere redondear al millar superior
        
        // Reset método activo
        setPaymentMethod('Efectivo');
        calculateChange();
        
        payModal.show();
    };

    window.setPaymentMethod = function(method) {
        activePaymentMethod = method;
        
        const btnCash = document.getElementById('btn-pay-cash');
        const btnCard = document.getElementById('btn-pay-card');
        const cashCalcGroup = document.getElementById('cash-calculation-group');

        if (method === 'Efectivo') {
            btnCash.classList.add('active', 'btn-success');
            btnCash.classList.remove('rgba-bg');
            btnCard.classList.remove('active', 'btn-success');
            cashCalcGroup.classList.remove('d-none');
        } else {
            btnCard.classList.add('active', 'btn-success');
            btnCard.classList.remove('rgba-bg');
            btnCash.classList.remove('active', 'btn-success');
            cashCalcGroup.classList.add('d-none');
        }
    };

    window.calculateChange = function() {
        const total = ticket.totalVal;
        const cash = parseFloat(document.getElementById('pay-cash-input').value) || 0;
        const change = cash - total;
        
        const changeVal = document.getElementById('pay-change-val');
        if (change >= 0) {
            changeVal.textContent = `$ ${formatMoney(change)}`;
            changeVal.classList.remove('text-danger');
            changeVal.classList.add('text-success');
        } else {
            changeVal.textContent = `Faltan $ ${formatMoney(Math.abs(change))}`;
            changeVal.classList.remove('text-success');
            changeVal.classList.add('text-danger');
        }
    };

    document.getElementById('pay-cash-input').addEventListener('input', calculateChange);

    // Sugerencias de billetes rápidos
    window.suggestCashAmount = function(amount) {
        document.getElementById('pay-cash-input').value = amount;
        calculateChange();
    };

    // Confirmar Pago & Mostrar Recibo
    window.confirmPayment = function() {
        const total = ticket.totalVal;
        const cash = parseFloat(document.getElementById('pay-cash-input').value) || 0;
        
        if (activePaymentMethod === 'Efectivo' && cash < total) {
            alert("El efectivo recibido es menor al total a pagar.");
            return;
        }

        // Generar Recibo Térmico
        generateReceipt(cash);

        // Ocultar modal de pago
        payModal.hide();
        
        // Mostrar modal de recibo
        receiptModal.show();
    };

    function generateReceipt(cashPaid) {
        const container = document.getElementById('receipt-thermal-content');
        const now = new Date();
        const dateStr = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
        const ticketNum = Math.floor(Math.random() * 90000) + 10000;
        
        let itemsHTML = '';
        ticket.items.forEach(item => {
            const sub = item.price * item.qty;
            itemsHTML += `
                <div class="receipt-item-line">
                    <div>${item.qty}</div>
                    <div>${escapeHTML(item.name.substring(0, 18))}</div>
                    <div class="text-right">$${formatMoney(item.price)}</div>
                    <div class="text-right">$${formatMoney(sub)}</div>
                </div>
            `;
        });

        const total = ticket.totalVal;
        const change = activePaymentMethod === 'Efectivo' ? Math.max(0, cashPaid - total) : 0;
        const discountAmount = ticket.discountVal;
        const serviceTax = ticket.taxVal;

        let discountRow = '';
        if (discountAmount > 0) {
            discountRow = `
                <div class="receipt-row">
                    <div>DESCUENTO (${ticket.discountPercent}%)</div>
                    <div>-$${formatMoney(discountAmount)}</div>
                </div>
            `;
        }

        container.innerHTML = `
            <div class="receipt-paper">
                <div class="receipt-header">
                    <div class="receipt-title">KENKOPOS RESTAURANTE</div>
                    <div>NIT: 900.123.456-1</div>
                    <div>Calle Gourmet 123, Gastronomía</div>
                    <div>Tel: (601) 555-4321</div>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-row">
                    <div>TICKET: #${ticketNum}</div>
                    <div class="text-right">${ticket.tableName}</div>
                </div>
                <div class="receipt-row">
                    <div>FECHA: ${dateStr}</div>
                    <div class="text-right">Cajero: Admin</div>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-item-line bold">
                    <div>CANT</div>
                    <div>DESCRIPCION</div>
                    <div class="text-right">P.UNI</div>
                    <div class="text-right">TOTAL</div>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-items">
                    ${itemsHTML}
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-row">
                    <div>SUBTOTAL</div>
                    <div>$${formatMoney(ticket.subtotalVal)}</div>
                </div>
                ${discountRow}
                <div class="receipt-row">
                    <div>SERVICIO DE MESA (${ticket.taxPercent}%)</div>
                    <div>$${formatMoney(serviceTax)}</div>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-row bold">
                    <div>TOTAL</div>
                    <div>$${formatMoney(total)}</div>
                </div>
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-row">
                    <div>METODO PAGO</div>
                    <div class="text-right">${activePaymentMethod}</div>
                </div>
                ${activePaymentMethod === 'Efectivo' ? `
                <div class="receipt-row">
                    <div>EFECTIVO RECIBIDO</div>
                    <div class="text-right">$${formatMoney(cashPaid)}</div>
                </div>
                <div class="receipt-row">
                    <div>CAMBIO</div>
                    <div class="text-right">$${formatMoney(change)}</div>
                </div>
                ` : ''}
                
                <div class="receipt-divider"></div>
                
                <div class="receipt-header" style="margin-top: 1rem; margin-bottom: 0;">
                    <div>¡GRACIAS POR SU VISITA!</div>
                    <div style="font-size: 0.75rem;">KenkoPOS Software POS</div>
                </div>
            </div>
        `;
    }

    // Imprimir ticket de precuenta
    window.printPrecuenta = function() {
        if (ticket.items.length === 0) return;
        generateReceipt(0);
        receiptModal.show();
    };

    // Al cerrar recibo finalizado, reiniciar la comanda actual
    window.closeReceiptAndReset = function() {
        receiptModal.hide();
        resetTicket();
    };

    // ----------------------------------------
    // Helpers de Formato y Escape
    // ----------------------------------------
    function formatMoney(amount) {
        return parseFloat(amount).toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function escapeHTML(str) {
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Inicializar carga de productos
    renderProducts();
});

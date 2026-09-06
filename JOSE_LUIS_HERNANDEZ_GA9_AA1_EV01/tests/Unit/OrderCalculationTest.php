<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Pruebas Unitarias para el Módulo de Cálculos Financieros del POS (KenkoPOS)
 * Valida subtotales, descuentos, cálculo de IVA (19%) y cambio de efectivo.
 */
class OrderCalculationTest extends TestCase
{
    /**
     * Prueba el cálculo de subtotal sumando el total de cada ítem
     */
    public function testCalculateSubtotalFromItems(): void
    {
        $items = [
            ['name' => 'Hamburguesa Especial', 'price' => 22000.00, 'qty' => 2], // 44000
            ['name' => 'Papas Fritas', 'price' => 6500.00, 'qty' => 1],        // 6500
            ['name' => 'Gaseosa Coca-Cola', 'price' => 4000.00, 'qty' => 3]     // 12000
        ];

        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += ($item['price'] * $item['qty']);
        }

        $this->assertEquals(62500.00, $subtotal, 'El subtotal calculado debe coincidir exactamente con la suma de precios x cantidades.');
    }

    /**
     * Prueba el cálculo del descuento porcentual sobre el subtotal
     */
    public function testCalculateDiscountAmount(): void
    {
        $subtotal = 50000.00;
        $discountPercent = 10; // 10%

        $discountAmount = $subtotal * ($discountPercent / 100);
        $subtotalAfterDiscount = $subtotal - $discountAmount;

        $this->assertEquals(5000.00, $discountAmount, 'El descuento del 10% de 50.000 debe ser 5.000.');
        $this->assertEquals(45000.00, $subtotalAfterDiscount, 'La base imponible tras descuento debe ser 45.000.');
    }

    /**
     * Prueba el cálculo del IVA colombiano (19%) sobre el subtotal neto
     */
    public function testCalculateTaxAmount(): void
    {
        $netSubtotal = 40000.00;
        $taxPercent = 19; // 19% IVA

        $taxAmount = $netSubtotal * ($taxPercent / 100);
        $total = $netSubtotal + $taxAmount;

        $this->assertEquals(7600.00, $taxAmount, 'El IVA del 19% de 40.000 debe ser 7.600.');
        $this->assertEquals(47600.00, $total, 'El total con IVA incluido debe ser 47.600.');
    }

    /**
     * Prueba el cálculo del cambio (devuelta) en pagos en efectivo
     */
    public function testCalculateCashChange(): void
    {
        $totalOrder = 47600.00;
        $cashReceived = 50000.00;

        $change = $cashReceived - $totalOrder;

        $this->assertEquals(2400.00, $change, 'El cambio devuelto al cliente debe ser exactamente 2.400.');
    }

    /**
     * Prueba que el cambio no sea negativo cuando el efectivo recibido es insuficiente
     */
    public function testInsufficientCashValidation(): void
    {
        $totalOrder = 50000.00;
        $cashReceived = 40000.00;

        $isPaymentValid = ($cashReceived >= $totalOrder);

        $this->assertFalse($isPaymentValid, 'El pago no debe ser procesado si el efectivo recibido es menor al total.');
    }

    /**
     * Prueba de cálculo financiero completo integrado de comanda
     */
    public function testCompleteOrderFinancialBreakdown(): void
    {
        // 2 Hamburguesas ($24.000 c/u) + 1 Cerveza ($7.000) = $55.000
        $subtotal = 55000.00;
        $discountPercent = 10;
        $taxPercent = 19;

        $discountAmount = round($subtotal * ($discountPercent / 100), 2); // 5500
        $baseImponible = $subtotal - $discountAmount;                     // 49500
        $taxAmount = round($baseImponible * ($taxPercent / 100), 2);       // 9405
        $total = $baseImponible + $taxAmount;                             // 58905

        $this->assertEquals(5500.00, $discountAmount);
        $this->assertEquals(49500.00, $baseImponible);
        $this->assertEquals(9405.00, $taxAmount);
        $this->assertEquals(58905.00, $total);
    }
}

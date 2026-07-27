<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controlador de Productos — KenkoPOS (Laravel)
 *
 * Gestiona las operaciones CRUD del módulo de productos.
 * Equivalente a ProductController del módulo PHP puro (v1.x),
 * ahora implementando el patrón Resource Controller de Laravel.
 */
class ProductController extends Controller
{
    /**
     * Muestra el listado de todos los productos.
     * GET /products
     */
    public function index(): View
    {
        $products = Product::orderBy('created_at', 'desc')->get();

        return view('products.index', compact('products'));
    }

    /**
     * Muestra el formulario de creación de producto.
     * GET /products/create
     */
    public function create(): View
    {
        return view('products.create');
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     * POST /products
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación con las reglas del framework (equivalent a !empty() en v1.x)
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'sku'   => 'required|string|max:50|unique:products,sku',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Muestra el formulario de edición de un producto.
     * GET /products/{product}/edit
     */
    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Actualiza un producto existente en la base de datos.
     * PUT /products/{product}
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'sku'   => 'required|string|max:50|unique:products,sku,' . $product->product_id . ',product_id',
            'price' => 'required|numeric|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Elimina un producto de la base de datos.
     * DELETE /products/{product}
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}

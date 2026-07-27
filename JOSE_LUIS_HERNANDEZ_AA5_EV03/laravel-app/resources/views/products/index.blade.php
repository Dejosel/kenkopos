@extends('layouts.app')

@section('title', 'Lista de Productos - KenkoPOS')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Productos</h2>
        <a href="{{ route('products.create') }}" class="btn btn-primary">Nuevo Producto</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>SKU</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->product_id }}</td>
                            <td><span class="badge bg-secondary">{{ $product->sku }}</span></td>
                            <td>{{ $product->name }}</td>
                            <td>$ {{ number_format($product->price, 2) }}</td>
                            <td>
                                <a href="{{ route('products.edit', $product->product_id) }}" class="btn btn-sm btn-warning">Editar</a>
                                <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $product->product_id }})">Eliminar</button>
                                
                                <form id="delete-form-{{ $product->product_id }}" action="{{ route('products.destroy', $product->product_id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

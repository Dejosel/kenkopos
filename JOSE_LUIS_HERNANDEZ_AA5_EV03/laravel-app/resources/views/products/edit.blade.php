@extends('layouts.app')

@section('title', 'Editar Producto - KenkoPOS')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Editar Producto</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product->product_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Precio</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

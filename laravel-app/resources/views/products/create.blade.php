@extends('layouts.app')

@section('title', 'Crear Producto - KenkoPOS')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Crear Nuevo Producto</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU (Código único)</label>
                            <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku') }}" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Precio</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price') }}" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Guardar Producto</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

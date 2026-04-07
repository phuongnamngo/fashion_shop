@extends('admin.layout')

@section('title', 'Edit Variant')

@section('content')
    <div class="row between">
        <h1>Edit Variant #{{ $variant->id }} for {{ $product->name }}</h1>
        <a class="btn" href="{{ route('admin.products.edit', $product) }}">Back to Product</a>
    </div>

    <form method="POST" action="{{ route('admin.products.variants.update', [$product, $variant]) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="color">Color</label>
            <input id="color" name="color" type="text" value="{{ old('color', $variant->color) }}" required>
            @error('color') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="size">Size</label>
            <input id="size" name="size" type="text" value="{{ old('size', $variant->size) }}" required>
            @error('size') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="sku">SKU</label>
            <input id="sku" name="sku" type="text" value="{{ old('sku', $variant->sku) }}" required>
            @error('sku') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="stock_qty">Stock Qty</label>
            <input id="stock_qty" name="stock_qty" type="number" min="0" value="{{ old('stock_qty', $variant->stock_qty) }}" required>
            @error('stock_qty') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="price_override">Price Override</label>
            <input id="price_override" name="price_override" type="number" min="0" step="0.01" value="{{ old('price_override', $variant->price_override) }}">
            @error('price_override') <div class="error">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary" type="submit">Update Variant</button>
    </form>
@endsection

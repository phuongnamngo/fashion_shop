@extends('admin.layout')

@section('title', 'Edit Product')

@section('content')
    <div class="row between">
        <h1>Edit Product: {{ $product->name }}</h1>
        <a class="btn" href="{{ route('admin.products.index') }}">Back</a>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}">
        @method('PUT')
        @include('admin.products._form')
    </form>

    <hr style="margin: 24px 0;">

    <h2>Product Variants</h2>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Color</th>
            <th>Size</th>
            <th>SKU</th>
            <th>Stock</th>
            <th>Price Override</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($product->variants as $variant)
            <tr>
                <td>{{ $variant->id }}</td>
                <td>{{ $variant->color }}</td>
                <td>{{ $variant->size }}</td>
                <td>{{ $variant->sku }}</td>
                <td>{{ $variant->stock_qty }}</td>
                <td>{{ $variant->price_override ?? '-' }}</td>
                <td>
                    <div class="row">
                        <a class="btn" href="{{ route('admin.products.variants.edit', [$product, $variant]) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" type="submit" onclick="return confirm('Delete this variant?')">Delete</button>
                    </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No variants yet.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <h3 style="margin-top: 20px;">Add Variant</h3>
    <form method="POST" action="{{ route('admin.products.variants.store', $product) }}">
        @csrf
        <div class="row">
            <div style="flex: 1;">
                <label for="new_color">Color</label>
                <input id="new_color" name="color" type="text" required>
            </div>
            <div style="flex: 1;">
                <label for="new_size">Size</label>
                <input id="new_size" name="size" type="text" required>
            </div>
            <div style="flex: 1;">
                <label for="new_sku">SKU</label>
                <input id="new_sku" name="sku" type="text" required>
            </div>
        </div>
        <div class="row" style="margin-top: 12px;">
            <div style="flex: 1;">
                <label for="new_stock_qty">Stock Qty</label>
                <input id="new_stock_qty" name="stock_qty" type="number" min="0" value="0" required>
            </div>
            <div style="flex: 1;">
                <label for="new_price_override">Price Override</label>
                <input id="new_price_override" name="price_override" type="number" min="0" step="0.01">
            </div>
            <div style="flex: 1; display: flex; align-items: end;">
                <button class="btn btn-primary" type="submit">Add Variant</button>
            </div>
        </div>
    </form>
@endsection

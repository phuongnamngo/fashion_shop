@extends('admin.layout')

@section('title', 'Edit Product')

@section('content')
    <div class="row between">
        <h1>Edit Product: {{ $product->name }}</h1>
        <a class="btn" href="{{ route('admin.products.index') }}">Back</a>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.products._form')
    </form>

    <hr style="margin: 24px 0;">

    <h2>Gallery</h2>
    @if($product->images->isNotEmpty())
        <div class="row" style="flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
            @foreach($product->images as $img)
                <div style="border: 1px solid #ddd; padding: 8px; border-radius: 6px;">
                    <img src="{{ \App\Support\ProductMedia::publicUrl($img->image_url) }}" alt="" style="max-height: 100px; display: block;">
                    <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $img]) }}" style="margin-top: 8px;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" type="submit" onclick="return confirm('Remove this image?')">Remove</button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <p>No gallery images yet.</p>
    @endif

    <h3 style="margin-top: 12px;">Add gallery images</h3>
    <form method="POST" action="{{ route('admin.products.images.store', $product) }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="gallery_images">Images (max 10 per upload)</label>
            <input id="gallery_images" name="images[]" type="file" accept="image/*" multiple required>
            @error('images') <div class="error">{{ $message }}</div> @enderror
            @error('images.*') <div class="error">{{ $message }}</div> @enderror
        </div>
        <button class="btn btn-primary" type="submit">Upload</button>
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

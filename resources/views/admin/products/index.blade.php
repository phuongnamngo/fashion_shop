@extends('admin.layout')

@section('title', 'Manage Products')

@section('content')
    <div class="row between">
        <h1>Products</h1>
        <a class="btn btn-primary" href="{{ route('admin.products.create') }}">Create Product</a>
    </div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Base Price</th>
            <th>Status</th>
            <th>Variants</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category?->name }}</td>
                <td>{{ number_format((float) $product->base_price, 2) }}</td>
                <td>{{ $product->status ? 'Active' : 'Inactive' }}</td>
                <td>{{ $product->variants->count() }}</td>
                <td>
                    <div class="row">
                        <a class="btn" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit" onclick="return confirm('Delete this product?')">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No products found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 16px;">
        {{ $products->links() }}
    </div>
@endsection

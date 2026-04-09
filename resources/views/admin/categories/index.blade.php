@extends('admin.layout')

@section('title', 'Manage Categories')

@section('content')
    <div class="row between">
        <h1>Categories</h1>
        <a class="btn btn-primary" href="{{ route('admin.categories.create') }}">Create Category</a>
    </div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Slug</th>
            <th>Parent</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->slug }}</td>
                <td>{{ $category->parent?->name ?? '—' }}</td>
                <td>
                    <div class="row">
                        <a class="btn" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit" onclick="return confirm('Delete this category?')">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No categories found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 16px;">
        {{ $categories->links() }}
    </div>
@endsection

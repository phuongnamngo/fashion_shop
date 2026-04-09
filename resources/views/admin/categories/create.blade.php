@extends('admin.layout')

@section('title', 'Create Category')

@section('content')
    <div class="row between">
        <h1>Create Category</h1>
        <a class="btn" href="{{ route('admin.categories.index') }}">Back</a>
    </div>

    <form method="POST" action="{{ route('admin.categories.store') }}">
        @include('admin.categories._form', ['category' => null])
    </form>
@endsection

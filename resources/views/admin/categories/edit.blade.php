@extends('admin.layout')

@section('title', 'Edit Category')

@section('content')
    <div class="row between">
        <h1>Edit Category: {{ $category->name }}</h1>
        <a class="btn" href="{{ route('admin.categories.index') }}">Back</a>
    </div>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @method('PUT')
        @include('admin.categories._form')
    </form>
@endsection

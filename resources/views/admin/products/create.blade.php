@extends('admin.layout')

@section('title', 'Create Product')

@section('content')
    <div class="row between">
        <h1>Create Product</h1>
        <a class="btn" href="{{ route('admin.products.index') }}">Back</a>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @include('admin.products._form')
    </form>
@endsection

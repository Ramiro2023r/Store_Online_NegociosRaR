@extends('layouts.admin')
@section('title', 'Editar producto - Admin')
@section('page-title', '✏️ Editar producto')

@section('content')
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded-xl p-6 max-w-3xl space-y-4">
    @csrf @method('PUT')
    @include('admin.products._form')
    <button class="bg-rar-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-rar-700">Actualizar producto</button>
</form>
@endsection

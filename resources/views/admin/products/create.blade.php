@extends('layouts.admin')
@section('title', 'Nuevo producto - Admin')
@section('page-title', '➕ Nuevo producto')

@section('content')
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded-xl p-6 max-w-3xl space-y-4">
    @csrf
    @include('admin.products._form')
    <button class="bg-rar-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-rar-700">Guardar producto</button>
</form>
@endsection

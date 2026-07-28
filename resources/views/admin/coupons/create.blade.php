@extends('layouts.admin')
@section('title', 'Nuevo cupón - Admin')
@section('page-title', '➕ Nuevo cupón')

@section('content')
<form action="{{ route('admin.coupons.store') }}" method="POST" class="bg-white border rounded-xl p-6 max-w-3xl space-y-4">
    @csrf
    @include('admin.coupons._form')
    <button class="bg-rar-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-rar-700">Guardar cupón</button>
</form>
@endsection

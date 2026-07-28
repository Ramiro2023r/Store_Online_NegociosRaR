@extends('layouts.admin')
@section('title', 'Editar cupón - Admin')
@section('page-title', '✏️ Editar cupón')

@section('content')
<form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="bg-white border rounded-xl p-6 max-w-3xl space-y-4">
    @csrf @method('PUT')
    @include('admin.coupons._form')
    <button class="bg-rar-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-rar-700">Actualizar cupón</button>
</form>
@endsection

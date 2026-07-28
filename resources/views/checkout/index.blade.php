@extends('layouts.app')
@section('title', 'Finalizar compra - Negocios RaR')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Finalizar compra</h1>

    @if(session('culqi_error'))
        <div class="bg-cobre-50 border border-cobre-200 text-cobre-800 text-sm rounded-xl px-5 py-4 mb-6">{{ session('culqi_error') }}</div>
    @endif

    <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        <input type="hidden" name="culqi_token" id="culqi_token">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border rounded-xl p-6">
                <h3 class="font-bold mb-4">📍 Dirección de envío</h3>

                @if($addresses->count())
                    <div class="space-y-2 mb-4" x-data="{ selected: {{ old('address_id', $addresses->where('is_default', true)->first()?->id ?? $addresses->first()->id) }} }">
                        @foreach($addresses as $addr)
                            <label class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer hover:bg-gray-50 {{ old('address_id') == $addr->id || (!$loop->first && !old('address_id') && $addr->is_default) || ($loop->first && !old('address_id') && !$addresses->where('is_default', true)->first()) ? 'ring-2 ring-rar-500' : '' }}">
                                <input type="radio" name="address_id" value="{{ $addr->id }}" @checked(old('address_id', $addresses->where('is_default', true)->first()?->id ?? $addresses->first()->id) == $addr->id) @change="selected = {{ $addr->id }}" class="mt-1">
                                <div class="text-sm">
                                    <span class="font-semibold text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $addr->label }}</span>
                                    @if($addr->is_default) <span class="text-xs text-rar-600">Principal</span> @endif
                                    <p class="mt-1">{{ $addr->address }}</p>
                                    @if($addr->city)<p class="text-xs text-gray-500">{{ $addr->city }}</p>@endif
                                    @if($addr->phone)<p class="text-xs text-gray-500">📞 {{ $addr->phone }}</p>@endif
                                </div>
                            </label>
                        @endforeach
                        <p class="text-xs text-gray-400 mt-1">
                            <a href="{{ route('addresses.index') }}" class="text-rar-600 hover:underline">Gestionar direcciones</a>
                        </p>
                    </div>
                @else
                    <p class="text-sm text-gray-400 mb-3">No tienes direcciones guardadas.</p>
                @endif

                <div x-data="{ showForm: {{ $addresses->count() ? 'false' : 'true' }} }">
                    <button type="button" @click="showForm = !showForm" class="text-sm text-rar-600 hover:underline mb-3" x-show="{{ $addresses->count() ? 'true' : 'false' }}">
                        <span x-show="!showForm">+ Usar una dirección nueva</span>
                        <span x-show="showForm">- Usar dirección guardada</span>
                    </button>

                    <div x-show="showForm" class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="text-sm font-medium">Etiqueta</label>
                                <select name="new_address_label" class="w-full border rounded-lg px-3 py-2 mt-1 text-sm">
                                    <option value="Casa">Casa</option>
                                    <option value="Trabajo">Trabajo</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="text-sm font-medium">Dirección</label>
                                <input type="text" name="new_address" value="{{ old('new_address') }}" class="w-full border rounded-lg px-3 py-2 mt-1">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium">Ciudad</label>
                                <input type="text" name="new_city" value="{{ old('new_city') }}" class="w-full border rounded-lg px-3 py-2 mt-1">
                            </div>
                            <div>
                                <label class="text-sm font-medium">Teléfono</label>
                                <input type="text" name="new_phone" value="{{ old('new_phone', auth()->user()->phone) }}" class="w-full border rounded-lg px-3 py-2 mt-1">
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="save_address" id="save_address" value="1" checked class="rounded">
                            <label for="save_address">Guardar dirección para futuras compras</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="text-sm font-medium">Notas (opcional)</label>
                    <textarea name="notes" rows="2" class="w-full border rounded-lg px-3 py-2 mt-1">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="bg-white border rounded-xl p-6">
                <h3 class="font-bold mb-4">Método de pago</h3>
                <div class="space-y-2 text-sm">
                    <label class="flex items-center gap-2 border rounded-lg p-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="tarjeta" checked data-culqi> 💳 Tarjeta de crédito / débito
                    </label>
                    <label class="flex items-center gap-2 border rounded-lg p-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="transferencia"> 🏦 Transferencia bancaria
                    </label>
                    <label class="flex items-center gap-2 border rounded-lg p-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="contraentrega"> 💵 Pago contra entrega
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded-xl p-6 h-fit">
            <h3 class="font-bold mb-4">Tu pedido</h3>

            {{-- Cupón --}}
            <div class="mb-4 pb-4 border-b">
                <p class="text-sm font-medium mb-2">¿Tienes un cupón?</p>
                <div id="coupon-form" class="flex gap-2">
                    <input type="text" id="coupon-input" placeholder="Ingresa el código" class="flex-1 border rounded-lg px-3 py-2 text-sm uppercase" value="{{ $coupon->code ?? '' }}" {{ $coupon ? 'disabled' : '' }}>
                    <button type="button" id="coupon-apply-btn" class="bg-rar-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-rar-700 {{ $coupon ? 'hidden' : '' }}">Aplicar</button>
                    <button type="button" id="coupon-remove-btn" class="text-red-500 text-sm font-semibold px-3 py-2 rounded-lg border border-red-200 hover:bg-red-50 {{ $coupon ? '' : 'hidden' }}">✕ Quitar</button>
                </div>
                <div id="coupon-message" class="text-xs mt-1"></div>
            </div>

            @if($pointsBalance >= $minPoints)
                <div class="mb-4 pb-4 border-b" x-data="{ redeemPoints: false }">
                    <label class="flex items-start gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="redeem_points" value="1" class="mt-0.5 rounded" x-model="redeemPoints">
                        <div>
                            <span class="font-medium">⭐ Canjear puntos</span>
                            <p class="text-xs text-gray-400">
                                Tienes <strong>{{ number_format($pointsBalance) }} pts</strong> — 
                                valor: <strong>S/ {{ number_format($pointsBalance * $redeemRate, 2) }}</strong>
                                (mín. {{ $minPoints }} pts)
                            </p>
                        </div>
                    </label>
                </div>
            @endif

            <div class="space-y-2 max-h-64 overflow-y-auto mb-4">
                @foreach($cart->items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $item->quantity }}x {{ Str::limit($item->product->name, 25) }}
                            @if($item->variant)
                                <span class="text-xs text-gray-400">({{ $item->variant->size ?? '' }}{{ $item->variant->size && $item->variant->color ? ' / ' : '' }}{{ $item->variant->color ?? '' }})</span>
                            @endif
                        </span>
                        <span class="font-medium">S/ {{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                    </div>
                @endforeach
            </div>
            @php
                $subtotalShow = $cart->items->sum(fn ($i) => $i->unit_price * $i->quantity);
                $shippingShow = $subtotalShow >= 200 ? 0 : 15;
                $totalShow = $subtotalShow + $shippingShow - $discount;
            @endphp
            <div class="border-t pt-3 space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span id="coupon-subtotal">S/ {{ number_format($subtotalShow,2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Envío</span><span id="coupon-shipping">{{ $shippingShow == 0 ? 'Gratis' : 'S/ 15.00' }}</span></div>
                @if($discount > 0)
                    <div class="flex justify-between text-green-600 font-medium" id="coupon-discount-row">
                        <span>Descuento (<span id="coupon-code-display">{{ $coupon->code ?? '' }}</span>)</span>
                        <span id="coupon-discount-amount">-S/ {{ number_format($discount,2) }}</span>
                    </div>
                @else
                    <div class="flex justify-between text-green-600 font-medium hidden" id="coupon-discount-row">
                        <span>Descuento (<span id="coupon-code-display"></span>)</span>
                        <span id="coupon-discount-amount"></span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-lg pt-2 border-t mt-2">
                    <span>Total</span><span class="text-rar-600" id="coupon-total">S/ {{ number_format(max($totalShow, 0), 2) }}</span>
                </div>
            </div>
            <button id="submit-btn" type="submit" class="w-full mt-4 bg-rar-600 text-white font-semibold py-3 rounded-lg hover:bg-rar-700">Confirmar pedido</button>
        </div>
    </form>
</div>

@php $totalCents = (int) round(max($totalShow, 0) * 100); @endphp

<script src="https://checkout.culqi.com/js/v4"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const culqiRadio = document.querySelector('input[value="tarjeta"]');
        const form = document.getElementById('checkout-form');
        const submitBtn = document.getElementById('submit-btn');
        const couponInput = document.getElementById('coupon-input');
        const couponApplyBtn = document.getElementById('coupon-apply-btn');
        const couponRemoveBtn = document.getElementById('coupon-remove-btn');
        const couponMessage = document.getElementById('coupon-message');
        const couponDiscountRow = document.getElementById('coupon-discount-row');
        const couponCodeDisplay = document.getElementById('coupon-code-display');
        const couponDiscountAmount = document.getElementById('coupon-discount-amount');
        const couponTotal = document.getElementById('coupon-total');

        let currentTotalCents = {{ $totalCents }};

        function updateCulqiAmount(cents) {
            currentTotalCents = cents;
            Culqi.settings({ amount: cents });
        }

        couponApplyBtn.addEventListener('click', function () {
            const code = couponInput.value.trim();
            if (!code) return;

            couponMessage.innerHTML = '';
            couponApplyBtn.disabled = true;
            couponApplyBtn.textContent = '...';

            fetch('{{ route("checkout.coupon.apply") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ code: code }),
            })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function ({ ok, data }) {
                if (!ok) {
                    couponMessage.innerHTML = '<span class="text-red-500">' + (data.error || 'Error al aplicar el cupón.') + '</span>';
                    couponApplyBtn.disabled = false;
                    couponApplyBtn.textContent = 'Aplicar';
                    return;
                }

                couponInput.disabled = true;
                couponInput.value = data.coupon_code;
                couponApplyBtn.classList.add('hidden');
                couponRemoveBtn.classList.remove('hidden');
                couponDiscountRow.classList.remove('hidden');
                couponCodeDisplay.textContent = data.coupon_code;
                couponDiscountAmount.textContent = '-' + data.discount_formatted;
                couponTotal.textContent = data.new_total_formatted;
                couponMessage.innerHTML = '<span class="text-green-600">' + data.success + '</span>';

                var newCents = Math.round(parseFloat(data.new_total) * 100);
                updateCulqiAmount(newCents);
                couponApplyBtn.disabled = false;
                couponApplyBtn.textContent = 'Aplicar';
            })
            .catch(function () {
                couponMessage.innerHTML = '<span class="text-red-500">Error de conexión. Intenta nuevamente.</span>';
                couponApplyBtn.disabled = false;
                couponApplyBtn.textContent = 'Aplicar';
            });
        });

        couponRemoveBtn.addEventListener('click', function () {
            couponRemoveBtn.disabled = true;

            fetch('{{ route("checkout.coupon.remove") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                couponInput.disabled = false;
                couponInput.value = '';
                couponApplyBtn.classList.remove('hidden');
                couponRemoveBtn.classList.add('hidden');
                couponDiscountRow.classList.add('hidden');
                couponTotal.textContent = data.total_formatted;
                couponMessage.innerHTML = '';
                couponRemoveBtn.disabled = false;

                var newCents = Math.round(parseFloat(data.total) * 100);
                updateCulqiAmount(newCents);
            })
            .catch(function () {
                couponRemoveBtn.disabled = false;
            });
        });

        Culqi.publicKey('{{ config('services.culqi.public_key') }}');

        Culqi.settings({
            title: 'Negocios RaR',
            currency: 'PEN',
            description: 'Pedido en Negocios RaR',
            amount: {{ $totalCents }},
        });

        Culqi.options({
            lang: 'auto',
            modal: true,
            installments: false,
        });

        form.addEventListener('submit', function (e) {
            const method = document.querySelector('input[name="payment_method"]:checked').value;

            if (method !== 'tarjeta') {
                return;
            }

            e.preventDefault();
            Culqi.open();
        });

        window.culqi = function () {
            if (Culqi.token) {
                document.getElementById('culqi_token').value = Culqi.token.id;
                HTMLFormElement.prototype.submit.call(form);
            } else if (Culqi.error) {
                alert(Culqi.error.user_message || 'Error al procesar el pago.');
            }
        };

        document.querySelectorAll('input[name="payment_method"]').forEach(function (r) {
            r.addEventListener('change', function () {
                submitBtn.textContent = this.value === 'tarjeta'
                    ? 'Pagar con tarjeta'
                    : 'Confirmar pedido';
            });
        });
    });
</script>
@endsection

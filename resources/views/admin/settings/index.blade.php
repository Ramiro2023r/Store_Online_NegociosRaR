@extends('layouts.admin')
@section('title', 'Configuración - Admin')
@section('page-title', '⚙️ Configuración General')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- Sección: Barra superior y pie --}}
    {{-- Sección: Información del negocio --}}
    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-semibold text-lg mb-4 text-gray-700">🏪 Información del negocio</h3>
        @php $logo = App\Models\Setting::getValue('store_logo'); @endphp
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Logo principal</label>
                @if($logo)
                    <div class="mb-2">
                        <img src="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/'.$logo) }}" class="h-12">
                    </div>
                @endif
                <input type="file" name="store_logo" accept="image/*" class="w-full border rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Dejar vacío para mantener el actual.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Logo icono (favicon/app)</label>
                @php $logoIcon = App\Models\Setting::getValue('store_logo_icon'); @endphp
                @if($logoIcon)
                    <div class="mb-2">
                        <img src="{{ str_starts_with($logoIcon, 'http') ? $logoIcon : asset('storage/'.$logoIcon) }}" class="h-12">
                    </div>
                @endif
                <input type="file" name="store_logo_icon" accept="image/*" class="w-full border rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Dejar vacío para mantener el actual.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">RUC</label>
                <input type="text" name="store_ruc" value="{{ old('store_ruc', App\Models\Setting::getValue('store_ruc')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Razón social</label>
                <input type="text" name="store_business_name" value="{{ old('store_business_name', App\Models\Setting::getValue('store_business_name')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Dirección fiscal</label>
                <input type="text" name="store_address" value="{{ old('store_address', App\Models\Setting::getValue('store_address')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Teléfono</label>
                <input type="text" name="store_phone" value="{{ old('store_phone', App\Models\Setting::getValue('store_phone')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="text" name="store_email" value="{{ old('store_email', App\Models\Setting::getValue('store_email')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-semibold text-lg mb-4 text-gray-700">📢 Barra superior</h3>
        <div>
            <label class="block text-sm font-medium mb-1">Texto de la barra promocional</label>
            <input type="text" name="top_bar_text" value="{{ old('top_bar_text', App\Models\Setting::getValue('top_bar_text')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
    </div>

    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-semibold text-lg mb-4 text-gray-700">📄 Pie de página</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">Descripción</label>
                <input type="text" name="footer_description" value="{{ old('footer_description', App\Models\Setting::getValue('footer_description')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Dirección</label>
                <input type="text" name="footer_address" value="{{ old('footer_address', App\Models\Setting::getValue('footer_address')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Teléfono</label>
                <input type="text" name="footer_phone" value="{{ old('footer_phone', App\Models\Setting::getValue('footer_phone')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="text" name="footer_email" value="{{ old('footer_email', App\Models\Setting::getValue('footer_email')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-semibold text-lg mb-4 text-gray-700">🛒 Recuperación de carrito abandonado</h3>
        <p class="text-xs text-gray-400 mb-3">Configura el envío automático de correos para recuperar ventas. Requiere un worker de cola corriendo: <code>php artisan queue:work</code>.</p>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Horas de abandono</label>
                <input type="number" name="abandoned_delay_hours" value="{{ old('abandoned_delay_hours', App\Models\Setting::getValue('abandoned_delay_hours', '24')) }}" min="1" class="w-full border rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Tiempo sin actividad para considerar el carrito como abandonado.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Asunto del correo</label>
                <input type="text" name="abandoned_cart_subject" value="{{ old('abandoned_cart_subject', App\Models\Setting::getValue('abandoned_cart_subject')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
        <div class="mt-3 text-xs text-gray-400">
            <p>📌 Para programar el envío automático, agrega esta tarea a tu cron:</p>
            <code class="block bg-gray-50 p-2 rounded mt-1">* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code>
            <p class="mt-2">O ejecuta el comando manualmente: <code>php artisan rar:send-abandoned-carts</code></p>
        </div>
    </div>

    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-semibold text-lg mb-4 text-gray-700">📦 Página de Envío y Devoluciones</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Información de envío</label>
                <textarea name="shipping_info" rows="5" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('shipping_info', App\Models\Setting::getValue('shipping_info')) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Se muestra en la sección de envío. Puedes usar saltos de línea.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Política de devoluciones</label>
                <textarea name="returns_policy" rows="5" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('returns_policy', App\Models\Setting::getValue('returns_policy')) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Se muestra en la sección de devoluciones.</p>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-semibold text-lg mb-4 text-gray-700">🚚 Configuración de envío</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Monto mínimo para envío gratis (S/)</label>
                <input type="number" step="0.01" name="shipping_min_amount" value="{{ old('shipping_min_amount', App\Models\Setting::getValue('shipping_min_amount')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Costo de envío (S/)</label>
                <input type="number" step="0.01" name="shipping_cost" value="{{ old('shipping_cost', App\Models\Setting::getValue('shipping_cost')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-semibold text-lg mb-4 text-gray-700">🏠 Página de inicio</h3>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Título - Categorías</label>
                <input type="text" name="home_title_categories" value="{{ old('home_title_categories', App\Models\Setting::getValue('home_title_categories')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Título - Productos destacados</label>
                <input type="text" name="home_title_featured" value="{{ old('home_title_featured', App\Models\Setting::getValue('home_title_featured')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Título - Recién llegados</label>
                <input type="text" name="home_title_newest" value="{{ old('home_title_newest', App\Models\Setting::getValue('home_title_newest')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    <div id="loyalty" class="bg-white border rounded-xl p-6">
        <h3 class="font-semibold text-lg mb-4 text-gray-700">⭐ Puntos de fidelización</h3>
        <p class="text-xs text-gray-400 mb-3">Configura el programa de puntos. Los puntos se acreditan automáticamente al marcar un pedido como Entregado.</p>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tasa de ganancia (pts por S/ 1)</label>
                <input type="number" step="0.1" name="points_earning_rate" value="{{ old('points_earning_rate', App\Models\Setting::getValue('points_earning_rate', '1')) }}" min="0.1" class="w-full border rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Ej: 1 = 1 punto por cada sol.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Valor del punto (S/)</label>
                <input type="number" step="0.01" name="points_redeem_rate" value="{{ old('points_redeem_rate', App\Models\Setting::getValue('points_redeem_rate', '0.10')) }}" min="0.01" class="w-full border rounded-lg px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Ej: 0.10 = cada punto vale 10 céntimos.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Puntos mínimos para canjear</label>
                <input type="number" name="min_points_to_redeem" value="{{ old('min_points_to_redeem', App\Models\Setting::getValue('min_points_to_redeem', '100')) }}" min="1" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-xl p-6">
        <h3 class="font-semibold text-lg mb-4 text-gray-700">ℹ️ Página "Acerca de"</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Misión</label>
                <textarea name="about_mission" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('about_mission', App\Models\Setting::getValue('about_mission')) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Visión</label>
                <textarea name="about_vision" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('about_vision', App\Models\Setting::getValue('about_vision')) }}</textarea>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">Valores</label>
                <textarea name="about_values" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('about_values', App\Models\Setting::getValue('about_values')) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Clientes (ej: +15,000)</label>
                <input type="text" name="about_clients_count" value="{{ old('about_clients_count', App\Models\Setting::getValue('about_clients_count')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Productos (ej: +500)</label>
                <input type="text" name="about_products_count" value="{{ old('about_products_count', App\Models\Setting::getValue('about_products_count')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Regiones (ej: 24)</label>
                <input type="text" name="about_regions_count" value="{{ old('about_regions_count', App\Models\Setting::getValue('about_regions_count')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Calificación (ej: 4.8★)</label>
                <input type="text" name="about_rating" value="{{ old('about_rating', App\Models\Setting::getValue('about_rating')) }}" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">Subtítulo / descripción</label>
                <textarea name="about_subtitle" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('about_subtitle', App\Models\Setting::getValue('about_subtitle')) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="bg-rar-600 text-white font-semibold px-6 py-2 rounded-lg text-sm hover:bg-rar-700">Guardar configuración</button>
    </div>
</form>
@endsection

@component('emails.layout', ['subject' => '¡No olvides tu carrito!'])
    @php $storeLogo = App\Models\Setting::getValue('store_logo', 'images/Mejoradelogo.svg'); @endphp

    <p style="margin:0 0 16px;">Hola <strong>{{ $cart->user->name }}</strong>,</p>
    <p style="margin:0 0 16px;">Notamos que dejaste algunos productos en tu carrito. ¡No queremos que te pierdas de ellos!</p>

    <h3 style="font-size:15px;font-weight:bold;color:#0F1F3D;margin:0 0 12px;">Tu carrito</h3>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;border-collapse:collapse;">
        <thead>
            <tr style="background-color:#f9fafb;">
                <th style="padding:10px 12px;text-align:left;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Producto</th>
                <th style="padding:10px 12px;text-align:center;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Cant.</th>
                <th style="padding:10px 12px;text-align:right;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cart->items as $item)
                <tr>
                    <td style="padding:8px 12px;font-size:13px;border-bottom:1px solid #e5e7eb;">{{ $item->product->name ?? 'Producto' }}</td>
                    <td style="padding:8px 12px;text-align:center;font-size:13px;border-bottom:1px solid #e5e7eb;">{{ $item->quantity }}</td>
                    <td style="padding:8px 12px;text-align:right;font-size:13px;border-bottom:1px solid #e5e7eb;">S/ {{ number_format($item->unit_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="padding:10px 12px;text-align:right;font-weight:bold;font-size:14px;">Total:</td>
                <td style="padding:10px 12px;text-align:right;font-weight:bold;font-size:14px;color:#1B4F91;">S/ {{ number_format($cart->items->sum(fn($i) => $i->unit_price * $i->quantity), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:16px 0;">
                <a href="{{ route('cart.index') }}"
                   style="display:inline-block;background-color:#1B4F91;color:#ffffff;text-decoration:none;font-weight:bold;font-size:15px;padding:12px 32px;border-radius:8px;">
                    Recuperar mi carrito
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:20px 0 0;font-size:12px;color:#9ca3af;text-align:center;">
        Si ya completaste tu compra, ignora este mensaje.
    </p>
@endcomponent

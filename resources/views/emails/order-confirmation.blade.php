@component('emails.layout', ['subject' => 'Confirmamos tu pedido #'.$order->order_number.' - Negocios RaR'])
    <p style="margin:0 0 16px;">Hola <strong>{{ $order->user->name }}</strong>,</p>
    <p style="margin:0 0 16px;">Gracias por tu compra. Hemos recibido tu pedido y lo estamos procesando.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
        <tr>
            <td style="background-color:#EEF3FB;padding:12px 16px;border-radius:8px;">
                <p style="margin:0;font-size:13px;color:#6b7280;">Número de pedido</p>
                <p style="margin:2px 0 0;font-size:18px;font-weight:bold;color:#1B4F91;">#{{ $order->order_number }}</p>
            </td>
        </tr>
    </table>

    <h3 style="font-size:15px;font-weight:bold;color:#0F1F3D;margin:0 0 12px;">Productos</h3>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;border-collapse:collapse;">
        <thead>
            <tr style="background-color:#f9fafb;">
                <th style="padding:10px 12px;text-align:left;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Producto</th>
                <th style="padding:10px 12px;text-align:center;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Cant.</th>
                <th style="padding:10px 12px;text-align:right;font-size:13px;font-weight:600;color:#374151;border-bottom:1px solid #e5e7eb;">Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td style="padding:10px 12px;font-size:14px;color:#374151;border-bottom:1px solid #e5e7eb;">{{ $item->product_name }}</td>
                    <td style="padding:10px 12px;text-align:center;font-size:14px;color:#374151;border-bottom:1px solid #e5e7eb;">{{ $item->quantity }}</td>
                    <td style="padding:10px 12px;text-align:right;font-size:14px;color:#374151;border-bottom:1px solid #e5e7eb;">S/ {{ number_format($item->unit_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
        <tr>
            <td style="padding:4px 0;font-size:14px;color:#6b7280;">Subtotal</td>
            <td style="padding:4px 0;font-size:14px;color:#374151;text-align:right;">S/ {{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td style="padding:4px 0;font-size:14px;color:#6b7280;">Envío</td>
            <td style="padding:4px 0;font-size:14px;color:#374151;text-align:right;">{{ $order->shipping_cost > 0 ? 'S/ '.number_format($order->shipping_cost, 2) : 'Gratis' }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0 4px;border-top:2px solid #0F1F3D;font-size:16px;font-weight:bold;color:#0F1F3D;">Total</td>
            <td style="padding:8px 0 4px;border-top:2px solid #0F1F3D;font-size:16px;font-weight:bold;color:#1B4F91;text-align:right;">S/ {{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    <h3 style="font-size:15px;font-weight:bold;color:#0F1F3D;margin:20px 0 8px;">Dirección de envío</h3>
    <p style="margin:0 0 4px;font-size:14px;color:#374151;">{{ $order->shipping_address }}</p>
    <p style="margin:0 0 4px;font-size:14px;color:#374151;">{{ $order->shipping_city }}</p>
    <p style="margin:0 0 4px;font-size:14px;color:#374151;">{{ $order->shipping_phone }}</p>

    <h3 style="font-size:15px;font-weight:bold;color:#0F1F3D;margin:20px 0 8px;">Método de pago</h3>
    <p style="margin:0 0 20px;font-size:14px;color:#374151;">{{ ucfirst($order->payment_method) }}</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:8px 0 0;">
                <a href="{{ route('checkout.my-orders') }}" style="display:inline-block;background-color:#1B4F91;color:#ffffff;text-decoration:none;font-size:15px;font-weight:bold;padding:14px 32px;border-radius:8px;">Ver mi pedido</a>
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 0;font-size:13px;color:#9ca3af;">Si tienes alguna consulta, escríbenos a través de nuestro chat de soporte en la tienda.</p>
@endcomponent

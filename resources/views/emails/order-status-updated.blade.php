@component('emails.layout', ['subject' => 'Tu pedido #'.$order->order_number.' cambió a: '.$order->statusLabel().' - Negocios RaR'])
    <p style="margin:0 0 16px;">Hola <strong>{{ $order->user->name }}</strong>,</p>

    @php
        $message = match ($order->status) {
            'pagado' => 'Confirmamos tu pago, estamos preparando tu pedido.',
            'enviado' => 'Tu pedido va en camino.',
            'entregado' => 'Tu pedido fue entregado, ¡gracias por tu compra!',
            'cancelado' => 'Tu pedido fue cancelado.',
            default => 'El estado de tu pedido ha sido actualizado.',
        };
    @endphp

    <p style="margin:0 0 20px;font-size:16px;">{{ $message }}</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;">
        <tr>
            <td style="background-color:#EEF3FB;padding:12px 16px;border-radius:8px;">
                <p style="margin:0;font-size:13px;color:#6b7280;">Número de pedido</p>
                <p style="margin:2px 0 0;font-size:18px;font-weight:bold;color:#1B4F91;">#{{ $order->order_number }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:12px 0 0;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="background-color:#EEF3FB;padding:12px 16px;border-radius:8px;">
                            <p style="margin:0;font-size:13px;color:#6b7280;">Estado actual</p>
                            <p style="margin:2px 0 0;font-size:16px;font-weight:bold;color:#1B4F91;">{{ $order->statusLabel() }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:8px 0 0;">
                <a href="{{ route('checkout.my-orders') }}" style="display:inline-block;background-color:#1B4F91;color:#ffffff;text-decoration:none;font-size:15px;font-weight:bold;padding:14px 32px;border-radius:8px;">Ver mi pedido</a>
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 0;font-size:13px;color:#9ca3af;">Si tienes alguna consulta, escríbenos a través de nuestro chat de soporte en la tienda.</p>
@endcomponent

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Negocios RaR' }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f5;">
        <tr>
            <td align="center" style="padding:24px 16px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;">
                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#0F1F3D;padding:24px 32px;text-align:center;">
                            <img src="{{ asset('images/Mejoradelogoiconoapp.svg') }}" alt="Negocios RaR" style="height:48px;display:block;margin:0 auto;">
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px;font-size:15px;line-height:1.5;color:#374151;">
                            {{ $slot }}
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#f9fafb;padding:20px 32px;text-align:center;font-size:12px;color:#9ca3af;">
                            &copy; {{ date('Y') }} Negocios RaR. Todos los derechos reservados.<br>
                            Lima, Perú
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

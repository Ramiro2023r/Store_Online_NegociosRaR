@extends('layouts.app')
@section('title', 'Política de Privacidad - Negocios RaR')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-cobre-50 border border-cobre-200 text-cobre-800 text-sm rounded-xl px-5 py-4 mb-8">
        ⚠️ <strong>Aviso:</strong> Este documento es un borrador de referencia basado en la Ley N° 29733.
        Debe ser revisado por un abogado antes de su publicación oficial.
    </div>

    <h1 class="text-3xl font-bold mb-2">Política de Privacidad</h1>
    <p class="text-sm text-gray-500 mb-8">Última actualización: 28 de julio de 2026</p>

    <div class="prose prose-sm max-w-none text-gray-700 space-y-6">
        <h2 class="text-xl font-semibold text-rar-700 mt-8">1. Responsable del tratamiento de datos</h2>
        <p>
            [COMPLETAR — Razón Social], identificada con RUC [COMPLETAR], con domicilio fiscal en
            [COMPLETAR — Dirección], Lima, Perú. Correo electrónico de contacto: [COMPLETAR].
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">2. Datos personales que recopilamos</h2>
        <p>Recopilamos los siguientes datos personales cuando usted interactúa con nuestra tienda online:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li><strong>Datos de registro:</strong> nombre completo, correo electrónico, teléfono, dirección de envío.</li>
            <li><strong>Datos de compra:</strong> historial de pedidos, método de pago elegido, montos y fechas de transacción.</li>
            <li><strong>Datos de navegación:</strong> dirección IP, tipo de navegador, páginas visitadas, productos vistos (recopilados mediante cookies técnicas y de sesión).</li>
            <li><strong>Comunicaciones:</strong> mensajes enviados a través de nuestro chat de soporte.</li>
        </ul>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">3. Finalidad del tratamiento</h2>
        <p>Sus datos personales serán tratados para las siguientes finalidades:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Gestionar su registro y cuenta de usuario.</li>
            <li>Procesar y dar seguimiento a sus pedidos (incluyendo confirmación, envío y entrega).</li>
            <li>Atender consultas, reclamos y solicitudes a través de nuestro chat de soporte.</li>
            <li>Enviar comunicaciones relacionadas con sus pedidos (confirmación, cambio de estado, facturación).</li>
            <li>Cumplir con obligaciones legales y tributarias.</li>
            <li>Mejorar nuestra plataforma y la experiencia de usuario.</li>
        </ul>
        <p>No utilizaremos sus datos para fines de marketing o publicidad sin su consentimiento expreso adicional.</p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">4. Base legal</h2>
        <p>
            El tratamiento de sus datos personales se basa en su <strong>consentimiento</strong>, manifestado al
            aceptar la presente Política de Privacidad durante el registro de su cuenta. Adicionalmente, el
            tratamiento de datos necesarios para la ejecución de la relación contractual (procesamiento de
            pedidos) se ampara en el artículo 5 de la Ley N° 29733.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">5. Tiempo de conservación</h2>
        <p>
            Conservaremos sus datos personales mientras mantenga una cuenta activa en nuestra plataforma.
            Una vez que su cuenta sea desactivada o eliminada, conservaremos únicamente los datos necesarios
            para cumplir con obligaciones legales y tributarias durante el plazo establecido por ley
            (10 años para fines tributarios en Perú), y serán eliminados de forma segura al término de dicho plazo.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">6. Compartición de datos</h2>
        <p>
            No compartimos sus datos personales con terceros, salvo en los siguientes casos:
        </p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Empresas de transporte y logística para la entrega de pedidos.</li>
            <li>Autoridades competentes cuando sea requerido por ley o mandato judicial.</li>
            <li>Proveedores de servicios de pago (cuando se integre una pasarela de pago real).</li>
        </ul>
        <p>En ningún caso vendemos datos personales a terceros.</p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">7. Derechos ARCO</h2>
        <p>
            De acuerdo con la Ley N° 29733, usted tiene derecho a:
        </p>
        <ul class="list-disc pl-6 space-y-1">
            <li><strong>Acceso:</strong> conocer qué datos personales suyos tenemos y cómo los tratamos.</li>
            <li><strong>Rectificación:</strong> solicitar la corrección de datos inexactos o desactualizados.</li>
            <li><strong>Cancelación:</strong> solicitar la eliminación de sus datos personales cuando ya no sean necesarios para las finalidades descritas.</li>
            <li><strong>Oposición:</strong> oponerse al tratamiento de sus datos para fines específicos.</li>
        </ul>
        <p>
            Para ejercer sus derechos ARCO, puede enviarnos un correo electrónico a [COMPLETAR — correo de contacto]
            indicando su nombre completo, el derecho que desea ejercer y los motivos de su solicitud.
            Le responderemos en un plazo máximo de 15 días hábiles, conforme a lo establecido por la normativa.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">8. Seguridad de la información</h2>
        <p>
            Implementamos medidas de seguridad técnicas, organizativas y legales para proteger sus datos
            personales contra acceso no autorizado, pérdida, destrucción o alteración. Estas medidas incluyen:
            cifrado de contraseñas (bcrypt), protección CSRF en formularios, control de acceso por roles,
            y uso de conexiones seguras (HTTPS) en producción.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">9. Datos de contacto</h2>
        <p>
            Para cualquier consulta relacionada con la presente Política de Privacidad o el ejercicio de sus
            derechos ARCO, puede contactarnos a través de:
        </p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Correo electrónico: [COMPLETAR]</li>
            <li>Teléfono: [COMPLETAR]</li>
            <li>Dirección: [COMPLETAR]</li>
        </ul>
        <p>
            También tiene derecho a presentar una reclamación ante la Autoridad Nacional de Protección de Datos
            Personales del Perú si considera que no hemos atendido adecuadamente sus solicitudes.
        </p>
    </div>
</div>
@endsection

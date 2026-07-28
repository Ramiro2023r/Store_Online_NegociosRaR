@extends('layouts.app')
@section('title', 'Términos y Condiciones - Negocios RaR')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-cobre-50 border border-cobre-200 text-cobre-800 text-sm rounded-xl px-5 py-4 mb-8">
        ⚠️ <strong>Aviso:</strong> Este documento es un borrador de referencia basado en la legislación peruana
        (Código de Protección y Defensa del Consumidor, Ley N° 29571, y Código Civil). Debe ser revisado por
        un abogado antes de su publicación oficial.
    </div>

    <h1 class="text-3xl font-bold mb-2">Términos y Condiciones</h1>
    <p class="text-sm text-gray-500 mb-8">Última actualización: 28 de julio de 2026</p>

    <div class="prose prose-sm max-w-none text-gray-700 space-y-6">
        <h2 class="text-xl font-semibold text-rar-700 mt-8">1. Aceptación de los términos</h2>
        <p>
            Al registrarse y utilizar la plataforma de Negocios RaR, usted declara haber leído, entendido y
            aceptado los presentes Términos y Condiciones. Si no está de acuerdo con alguno de ellos, no
            debe utilizar nuestros servicios.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">2. Registro de cuenta</h2>
        <p>
            Para realizar compras es necesario registrarse proporcionando información veraz y actualizada.
            Usted es responsable de mantener la confidencialidad de sus credenciales de acceso. Nos reservamos
            el derecho de suspender o cancelar cuentas que proporcionen información falsa o que incumplan
            estos términos.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">3. Precios y disponibilidad</h2>
        <p>
            Los precios de los productos se muestran en soles peruanos (S/) e incluyen el Impuesto General a
            las Ventas (IGV) salvo que se indique lo contrario. Nos reservamos el derecho de modificar precios
            en cualquier momento, pero los cambios no afectarán a pedidos ya confirmados. La disponibilidad de
            los productos está sujeta al stock existente; en caso de que un producto no esté disponible después
            de confirmado el pedido, le informaremos y procederemos a reembolsar el monto correspondiente.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">4. Proceso de compra</h2>
        <p>El proceso de compra incluye los siguientes pasos:</p>
        <ol class="list-decimal pl-6 space-y-1">
            <li>Seleccionar los productos deseados y agregarlos al carrito de compras.</li>
            <li>Ingresar o confirmar los datos de envío y el método de pago.</li>
            <li>Revisar el resumen del pedido y confirmar la compra.</li>
            <li>Recibir una confirmación del pedido con un número único de seguimiento.</li>
        </ol>
        <p>La confirmación del pedido constituye la aceptación de la oferta de compra por nuestra parte.</p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">5. Métodos de pago</h2>
        <p>
            Actualmente aceptamos los siguientes métodos de pago: tarjeta de crédito/débito, transferencia
            bancaria y pago contra entrega. [COMPLETAR — agregar otros métodos según corresponda].
            El pago contra entrega solo está disponible en [COMPLETAR — zonas/distritos].
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">6. Envíos y entregas</h2>
        <p>
            Realizamos envíos a todo el Perú. Los plazos de entrega varían según la ubicación y se informarán
            al momento de la compra. El costo de envío es gratuito para pedidos mayores a S/ 200; para pedidos
            menores, el costo es de S/ 15.00.
        </p>
        <p>
            Una vez entregado el pedido al operador logístico, le proporcionaremos un código de seguimiento.
            Negocios RaR no se hace responsable por retrasos causados por el operador logístico o por fuerza mayor.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">7. Cambios, devoluciones y derecho de retracto</h2>
        <p>
            De conformidad con el artículo 58 del Código de Protección y Defensa del Consumidor (Ley N° 29571),
            usted tiene derecho a retractarse de la compra dentro de los <strong>7 días hábiles</strong>
            posteriores a la recepción del producto, siempre que este se encuentre en su estado original,
            sin uso y con todos sus empaques y accesorios.
        </p>
        <p>
            Para ejercer el derecho de retracto o solicitar un cambio/devolución, debe contactarnos a través de
            nuestro chat de soporte o al correo [COMPLETAR]. Los detalles específicos de nuestra política de
            devoluciones (incluyendo productos excluidos, condiciones y plazos) se encuentran en [COMPLETAR].
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">8. Libro de Reclamaciones</h2>
        <p>
            De acuerdo con el Código de Protección y Defensa del Consumidor, ponemos a disposición de nuestros
            clientes un Libro de Reclamaciones virtual. Para presentar un reclamo, puede solicitarlo a través de
            nuestro chat de soporte o enviando un correo a [COMPLETAR]. Atenderemos su reclamo en un plazo
            máximo de 15 días hábiles.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">9. Propiedad intelectual</h2>
        <p>
            Todos los contenidos de la plataforma (textos, imágenes, logotipos, diseño, código) son propiedad
            de Negocios RaR o de sus licenciantes y están protegidos por las leyes de propiedad intelectual.
            Queda prohibida su reproducción, distribución o modificación sin autorización expresa.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">10. Limitación de responsabilidad</h2>
        <p>
            Negocios RaR no será responsable por daños indirectos o pérdidas de lucro derivadas del uso de la
            plataforma, dentro de los límites establecidos por la legislación peruana. Nuestra responsabilidad
            máxima se limita al monto pagado por el producto objeto del reclamo.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">11. Ley aplicable y jurisdicción</h2>
        <p>
            Los presentes Términos y Condiciones se rigen por la legislación de la República del Perú.
            Para cualquier controversia derivada de su uso, las partes se someten a la jurisdicción de los
            juzgados y tribunales de la ciudad de Lima, Perú, renunciando expresamente a cualquier otro fuero.
        </p>

        <h2 class="text-xl font-semibold text-rar-700 mt-8">12. Actualización de los términos</h2>
        <p>
            Nos reservamos el derecho de modificar estos términos en cualquier momento. Las modificaciones
            entrarán en vigor desde su publicación en la plataforma. Le notificaremos sobre cambios relevantes
            a través del correo electrónico registrado. El uso continuado de la plataforma después de dichas
            modificaciones constituye su aceptación.
        </p>
    </div>
</div>
@endsection

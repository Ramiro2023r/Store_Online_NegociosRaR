<?php

namespace App\Mail;

use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart->loadMissing('items.product.category');
    }

    public function envelope(): Envelope
    {
        $subject = \App\Models\Setting::getValue('abandoned_cart_subject', '¡No olvides tu carrito! Tienes productos esperándote');

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.abandoned-cart');
    }
}

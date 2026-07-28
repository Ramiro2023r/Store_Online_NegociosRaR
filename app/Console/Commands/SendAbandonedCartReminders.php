<?php

namespace App\Console\Commands;

use App\Mail\AbandonedCartMail;
use App\Models\Cart;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartReminders extends Command
{
    protected $signature = 'rar:send-abandoned-carts';
    protected $description = 'Envía correos de recuperación para carritos abandonados hace 24h (o las horas configuradas)';

    public function handle(): int
    {
        $delayHours = (int) Setting::getValue('abandoned_delay_hours', 24);
        $cutoff = Carbon::now()->subHours($delayHours);

        $carts = Cart::with('items.product', 'user')
            ->whereNotNull('user_id')
            ->whereHas('items')
            ->where('last_active_at', '<=', $cutoff)
            ->whereDoesntHave('items.product', fn($q) => $q->where('active', false))
            ->get();

        if ($carts->isEmpty()) {
            $this->info('No hay carritos abandonados para recordar.');
            return Command::SUCCESS;
        }

        $sent = 0;
        foreach ($carts as $cart) {
            if (!$cart->user || !$cart->user->email) {
                continue;
            }

            try {
                Mail::to($cart->user->email)->queue(new AbandonedCartMail($cart));

                $cart->update(['last_active_at' => now()]);

                $sent++;
            } catch (\Throwable $e) {
                Log::error("Error al enviar recordatorio de carrito abandonado #{$cart->id}: " . $e->getMessage());
            }
        }

        $this->info("Recordatorios enviados: {$sent} de {$carts->count()} carritos.");
        return Command::SUCCESS;
    }
}

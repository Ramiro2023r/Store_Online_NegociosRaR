<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:100',
        ]);

        $exists = Newsletter::where('email', $request->email)->first();

        if ($exists) {
            if (!$exists->active) {
                $exists->update(['active' => true, 'name' => $request->name ?? $exists->name]);
                $message = '¡Te has reactivado en nuestro newsletter!';
            } else {
                $message = 'Ya estás suscrito a nuestras novedades.';
            }
        } else {
            Newsletter::create([
                'email' => $request->email,
                'name' => $request->name,
            ]);
            $message = '¡Gracias por suscribirte! Recibirás nuestras novedades.';
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }
}

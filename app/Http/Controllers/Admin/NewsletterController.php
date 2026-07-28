<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index()
    {
        $subscribers = Newsletter::latest()->paginate(20);
        return view('admin.newsletters.index', compact('subscribers'));
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();
        return back()->with('success', 'Suscriptor eliminado.');
    }

    public function export()
    {
        $emails = Newsletter::where('active', true)->pluck('email')->implode("\n");
        return response($emails, 200)->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="suscriptores-newsletter.txt"');
    }
}

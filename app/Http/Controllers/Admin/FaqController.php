<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::ordered()->get();

        return view('admin.faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
        ]);

        $data['sort_order'] ??= 0;
        $data['active'] = $request->boolean('active', true);

        Faq::create($data);

        return back()->with('success', 'FAQ creada.');
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
        ]);

        $data['sort_order'] ??= 0;
        $data['active'] = $request->boolean('active', true);

        $faq->update($data);

        return back()->with('success', 'FAQ actualizada.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with('success', 'FAQ eliminada.');
    }
}

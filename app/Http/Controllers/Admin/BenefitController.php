<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Benefit;
use Illuminate\Http\Request;

class BenefitController extends Controller
{
    public function index()
    {
        $benefits = Benefit::ordered()->get();

        return view('admin.benefits.index', compact('benefits'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'icon' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
        ]);

        $data['sort_order'] ??= 0;
        $data['active'] = $request->boolean('active', true);

        Benefit::create($data);

        return back()->with('success', 'Beneficio creado.');
    }

    public function update(Request $request, Benefit $benefit)
    {
        $data = $request->validate([
            'icon' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
        ]);

        $data['sort_order'] ??= 0;
        $data['active'] = $request->boolean('active', true);

        $benefit->update($data);

        return back()->with('success', 'Beneficio actualizado.');
    }

    public function destroy(Benefit $benefit)
    {
        $benefit->delete();

        return back()->with('success', 'Beneficio eliminado.');
    }
}

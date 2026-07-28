<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())->latest()->get();

        return view('addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'phone' => 'required|string|max:30',
            'is_default' => 'nullable|boolean',
        ]);

        $data['user_id'] = Auth::id();

        if ($request->boolean('is_default')) {
            Address::where('user_id', Auth::id())->update(['is_default' => false]);
        }

        Address::create($data);

        return back()->with('success', 'Dirección guardada.');
    }

    public function update(Request $request, Address $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);

        $data = $request->validate([
            'label' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'phone' => 'required|string|max:30',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            Address::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return back()->with('success', 'Dirección actualizada.');
    }

    public function destroy(Address $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);
        $address->delete();

        return back()->with('success', 'Dirección eliminada.');
    }
}

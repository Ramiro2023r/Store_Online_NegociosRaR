<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::with('category')->latest()->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.coupons.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['code'] = strtoupper($data['code']);

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Cupón creado correctamente.');
    }

    public function edit(Coupon $coupon)
    {
        $categories = Category::all();

        return view('admin.coupons.edit', compact('coupon', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $this->validateData($request, $coupon->id);
        $data['code'] = strtoupper($data['code']);

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Cupón actualizado correctamente.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('success', 'Cupón eliminado.');
    }

    protected function validateData(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $ignoreId,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'active' => 'nullable|boolean',
        ]) + [
            'min_purchase' => $request->min_purchase ?? 0,
            'active' => $request->boolean('active', true),
        ];
    }
}

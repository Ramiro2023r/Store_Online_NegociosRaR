<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalVentas = Order::whereIn('status', ['pagado', 'enviado', 'entregado'])->sum('total');
        $pedidosHoy = Order::whereDate('created_at', today())->count();
        $totalProductos = Product::count();
        $totalClientes = User::where('role', 'cliente')->count();
        $stockBajo = Product::where('stock', '<=', 5)->where('active', true)->count();

        $ventasPorDia = Order::select(DB::raw("DATE(created_at) as fecha"), DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->whereIn('status', ['pagado', 'enviado', 'entregado'])
            ->groupBy('fecha')->orderBy('fecha')->get();

        $ultimosPedidos = Order::with('user')->latest()->take(8)->get();
        $productosMasVendidos = DB::table('order_items')
            ->select('product_name', DB::raw('SUM(quantity) as vendidos'))
            ->groupBy('product_name')->orderByDesc('vendidos')->take(5)->get();

        $reviewsPendientes = Review::where('approved', false)->count();

        $cuponesActivos = Coupon::where('active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->count();

        $productosMasDeseados = DB::table('wishlists')
            ->select('product_id', DB::raw('COUNT(*) as veces'))
            ->groupBy('product_id')->orderByDesc('veces')->take(5)
            ->get()
            ->map(function ($item) {
                $item->product = Product::find($item->product_id);
                return $item;
            });

        return view('admin.dashboard', compact(
            'totalVentas', 'pedidosHoy', 'totalProductos', 'totalClientes',
            'stockBajo', 'ventasPorDia', 'ultimosPedidos', 'productosMasVendidos',
            'reviewsPendientes', 'productosMasDeseados', 'cuponesActivos'
        ));
    }
}

<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function salesSummary(?string $from = null, ?string $to = null): array
    {
        $start = $from ? Carbon::parse($from) : now()->startOfMonth();
        $end = $to ? Carbon::parse($to) : now()->endOfDay();
        $statuses = ['pagado', 'enviado', 'entregado'];

        $data = Order::select(
            DB::raw('COUNT(*) as pedidos'),
            DB::raw('SUM(total) as total'),
            DB::raw('SUM(subtotal) as subtotal'),
            DB::raw('SUM(shipping_cost) as envio'),
        )->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->whereIn('status', $statuses)
            ->first();

        $byCategory = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$start->startOfDay(), $end->endOfDay()])
            ->whereIn('orders.status', $statuses)
            ->select('categories.name as category', DB::raw('COUNT(DISTINCT orders.id) as pedidos'), DB::raw('SUM(order_items.total) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get()
            ->toArray();

        return [
            'period' => ['from' => $start->toDateString(), 'to' => $end->toDateString()],
            'summary' => [
                'pedidos' => (int) ($data->pedidos ?? 0),
                'total' => (float) ($data->total ?? 0),
                'subtotal' => (float) ($data->subtotal ?? 0),
                'envio' => (float) ($data->envio ?? 0),
            ],
            'by_category' => $byCategory,
        ];
    }

    public function topProducts(?string $from = null, ?string $to = null, int $limit = 20): array
    {
        $start = $from ? Carbon::parse($from) : now()->startOfMonth();
        $end = $to ? Carbon::parse($to) : now()->endOfDay();
        $statuses = ['pagado', 'enviado', 'entregado'];

        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$start->startOfDay(), $end->endOfDay()])
            ->whereIn('orders.status', $statuses)
            ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as vendidos'), DB::raw('SUM(order_items.total) as total'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function inventoryReport(): array
    {
        return [
            'total' => Product::count(),
            'low_stock' => Product::where('stock', '>', 0)->whereRaw('stock <= min_stock')->count(),
            'out_of_stock' => Product::where('stock', '<=', 0)->where('active', true)->count(),
            'in_stock' => Product::where('stock', '>', 0)->count(),
        ];
    }

    public function customerReport(?string $from = null, ?string $to = null, int $limit = 20): array
    {
        $start = $from ? Carbon::parse($from) : now()->startOfMonth();
        $end = $to ? Carbon::parse($to) : now()->endOfDay();
        $statuses = ['pagado', 'enviado', 'entregado'];

        return DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$start->startOfDay(), $end->endOfDay()])
            ->whereIn('orders.status', $statuses)
            ->select('users.id', 'users.name', 'users.email', DB::raw('COUNT(orders.id) as pedidos'), DB::raw('SUM(orders.total) as total_gastado'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_gastado')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function orderReport(?string $from = null, ?string $to = null): array
    {
        $start = $from ? Carbon::parse($from) : now()->startOfMonth();
        $end = $to ? Carbon::parse($to) : now()->endOfDay();

        $byStatus = Order::select('status', DB::raw('COUNT(*) as total'), DB::raw('SUM(total) as monto'))
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->groupBy('status')
            ->get()
            ->toArray();

        return [
            'period' => ['from' => $start->toDateString(), 'to' => $end->toDateString()],
            'by_status' => $byStatus,
            'total_pedidos' => array_sum(array_column($byStatus, 'total')),
            'total_monto' => array_sum(array_column($byStatus, 'monto')),
        ];
    }
}

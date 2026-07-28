<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->date('from') ?? now()->startOfMonth();
        $end = $request->date('to') ?? now()->endOfDay();

        $statuses = ['pagado', 'enviado', 'entregado'];

        // Ventas por período
        $periodSales = Order::select(
            DB::raw("DATE(created_at) as fecha"),
            DB::raw('COUNT(*) as pedidos'),
            DB::raw('SUM(total) as total'),
            DB::raw('SUM(subtotal) as subtotal'),
            DB::raw('SUM(shipping_cost) as envio')
        )
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->whereIn('status', $statuses)
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $resumen = [
            'pedidos' => $periodSales->sum('pedidos'),
            'total' => $periodSales->sum('total'),
            'subtotal' => $periodSales->sum('subtotal'),
            'envio' => $periodSales->sum('envio'),
        ];

        // Ventas por categoría
        $byCategory = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$start->startOfDay(), $end->endOfDay()])
            ->whereIn('orders.status', $statuses)
            ->select(
                'categories.name as category',
                DB::raw('COUNT(DISTINCT orders.id) as pedidos'),
                DB::raw('SUM(order_items.quantity) as vendidos'),
                DB::raw('SUM(order_items.total) as total')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();

        // Ventas por producto
        $byProduct = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$start->startOfDay(), $end->endOfDay()])
            ->whereIn('orders.status', $statuses)
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as vendidos'),
                DB::raw('SUM(order_items.total) as total')
            )
            ->groupBy('order_items.product_name')
            ->orderByDesc('total')
            ->take(100)
            ->get();

        return view('admin.reports.index', compact(
            'periodSales', 'resumen', 'byCategory', 'byProduct', 'start', 'end'
        ));
    }

    public function exportCsv(Request $request)
    {
        $type = $request->query('type', 'period');
        $start = $request->date('from') ?? now()->startOfMonth();
        $end = $request->date('to') ?? now()->endOfDay();
        $statuses = ['pagado', 'enviado', 'entregado'];

        $filename = match ($type) {
            'category' => 'ventas-por-categoria',
            'product' => 'ventas-por-producto',
            default => 'ventas-por-periodo',
        };
        $filename .= '-' . $start->format('Ymd') . '-' . $end->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($type, $start, $end, $statuses) {
            $handle = fopen('php://output', 'w');
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            if ($type === 'category') {
                fputcsv($handle, ['Categoría', 'Pedidos', 'Unidades vendidas', 'Total (S/)']);
                $data = DB::table('order_items')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$start->startOfDay(), $end->endOfDay()])
                    ->whereIn('orders.status', $statuses)
                    ->select(
                        'categories.name as category',
                        DB::raw('COUNT(DISTINCT orders.id) as pedidos'),
                        DB::raw('SUM(order_items.quantity) as vendidos'),
                        DB::raw('SUM(order_items.total) as total')
                    )
                    ->groupBy('categories.id', 'categories.name')
                    ->orderByDesc('total')
                    ->get();
                foreach ($data as $row) {
                    fputcsv($handle, [$row->category, $row->pedidos, $row->vendidos, number_format($row->total, 2)]);
                }
            } elseif ($type === 'product') {
                fputcsv($handle, ['Producto', 'Unidades vendidas', 'Total (S/)']);
                $data = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$start->startOfDay(), $end->endOfDay()])
                    ->whereIn('orders.status', $statuses)
                    ->select(
                        'order_items.product_name',
                        DB::raw('SUM(order_items.quantity) as vendidos'),
                        DB::raw('SUM(order_items.total) as total')
                    )
                    ->groupBy('order_items.product_name')
                    ->orderByDesc('total')
                    ->get();
                foreach ($data as $row) {
                    fputcsv($handle, [$row->product_name, $row->vendidos, number_format($row->total, 2)]);
                }
            } else {
                fputcsv($handle, ['Fecha', 'Pedidos', 'Subtotal (S/)', 'Envío (S/)', 'Total (S/)']);
                $data = Order::select(
                    DB::raw("DATE(created_at) as fecha"),
                    DB::raw('COUNT(*) as pedidos'),
                    DB::raw('SUM(total) as total'),
                    DB::raw('SUM(subtotal) as subtotal'),
                    DB::raw('SUM(shipping_cost) as envio')
                )
                    ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
                    ->whereIn('status', $statuses)
                    ->groupBy('fecha')
                    ->orderBy('fecha')
                    ->get();
                foreach ($data as $row) {
                    fputcsv($handle, [
                        $row->fecha, $row->pedidos,
                        number_format($row->subtotal, 2),
                        number_format($row->envio, 2),
                        number_format($row->total, 2),
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

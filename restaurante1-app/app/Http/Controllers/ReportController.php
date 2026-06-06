<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Generar y descargar el reporte consolidado de operaciones en PDF.
     */
    public function downloadPdf()
    {
        // Doble verificación de seguridad
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado.');
        }

        // 1. Estadísticas generales (excluyendo cancelados para ingresos)
        $total_orders = Order::where('status', '!=', 'Cancelado')->count();
        $total_revenue = Order::where('status', 'Entregado')->sum('total');
        $avg_ticket = $total_orders > 0 ? ($total_revenue / $total_orders) : 0;

        // 2. Productos más vendidos
        $most_sold = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // 3. Stock actual de todos los productos
        $products_stock = Product::select('name', 'stock', 'price')->orderBy('stock', 'asc')->get();

        // 4. Últimos pedidos en el sistema
        $recent_orders = Order::with('user')->orderBy('created_at', 'desc')->take(10)->get();

        $data = [
            'date' => date('d/m/Y H:i'),
            'total_orders' => $total_orders,
            'total_revenue' => (float)$total_revenue,
            'avg_ticket' => (float)$avg_ticket,
            'most_sold' => $most_sold,
            'products_stock' => $products_stock,
            'recent_orders' => $recent_orders
        ];

        // Cargar vista HTML y compilarla a PDF
        $pdf = Pdf::loadView('reports.sales', $data);

        return $pdf->download('Reporte_Operaciones_QuickBite_' . date('Ymd_His') . '.pdf');
    }
}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas y Rendimiento</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
        }
        @page {
            margin: 100px 50px 80px 50px;
        }
        header {
            position: fixed;
            top: -75px;
            left: 0;
            right: 0;
            height: 60px;
            border-bottom: 3px solid #b7102a;
            padding-bottom: 10px;
        }
        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            text-align: center;
            font-size: 9px;
            color: #777;
        }
        .page-number:after {
            content: counter(page);
        }
        .header-logo {
            float: left;
            font-size: 20px;
            font-weight: bold;
            color: #b7102a;
            text-transform: uppercase;
        }
        .header-meta {
            float: right;
            text-align: right;
            font-size: 10px;
            color: #555;
        }
        .section-title {
            background-color: #f8f9fa;
            border-left: 4px solid #ffcc00;
            padding: 6px 10px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .stats-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .stats-card {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            width: 30%;
        }
        .stats-val {
            font-size: 18px;
            font-weight: bold;
            color: #b7102a;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #333;
            color: #fff;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-pending { background-color: #ffeeba; color: #856404; }
        .badge-preparing { background-color: #cce5ff; color: #004085; }
        .badge-ready { background-color: #e2e3e5; color: #383d41; }
        .badge-delivered { background-color: #d4edda; color: #155724; }
        .badge-cancelled { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <header>
        <div class="header-logo">QuickBite Express HQ</div>
        <div class="header-meta">
            <strong>REPORTE OPERATIVO Y DE VENTAS</strong><br>
            Fecha de emisión: {{ $date }}<br>
            Generado por: Administrador
        </div>
    </header>

    <footer>
        QuickBite Express &copy; {{ date('Y') }} - Todos los derechos reservados. Página <span class="page-number"></span>
    </footer>

    <div style="margin-top: 20px;">
        <h2 style="font-size: 18px; margin-bottom: 5px; color: #333; text-transform: uppercase;">Resumen Ejecutivo</h2>
        <p style="color: #666; margin-bottom: 20px; font-size: 11px;">
            Este reporte contiene el rendimiento consolidado del restaurante, incluyendo las ventas del día, el estado del inventario de productos y el desglose de los pedidos procesados recientemente.
        </p>

        <table class="stats-grid">
            <tr>
                <td class="stats-card">
                    <div style="font-size: 10px; color: #777; font-weight: bold; text-transform: uppercase;">Ingresos Totales</div>
                    <div class="stats-val">${{ number_format($total_revenue, 2) }}</div>
                </td>
                <td style="width: 5%;"></td>
                <td class="stats-card">
                    <div style="font-size: 10px; color: #777; font-weight: bold; text-transform: uppercase;">Pedidos Procesados</div>
                    <div class="stats-val">{{ $total_orders }}</div>
                </td>
                <td style="width: 5%;"></td>
                <td class="stats-card">
                    <div style="font-size: 10px; color: #777; font-weight: bold; text-transform: uppercase;">Ticket Promedio</div>
                    <div class="stats-val">${{ number_format($avg_ticket, 2) }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Productos Más Vendidos</div>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Cantidad Vendida</th>
                    <th class="text-right">Ingreso Generado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($most_sold as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td class="text-right">{{ $item->total_qty }} u.</td>
                        <td class="text-right">${{ number_format($item->total_revenue, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No se registran ventas de productos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Estado de Inventario</div>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Stock Disponible</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products_stock as $prod)
                    <tr>
                        <td><strong>{{ $prod->name }}</strong></td>
                        <td>${{ number_format($prod->price, 2) }}</td>
                        <td>{{ $prod->stock }} u.</td>
                        <td>
                            @if($prod->stock == 0)
                                <span class="badge badge-cancelled">Agotado</span>
                            @elseif($prod->stock <= 3)
                                <span class="badge badge-pending">Stock Bajo</span>
                            @else
                                <span class="badge badge-delivered">Saludable</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Últimos Pedidos Procesados</div>
        <table>
            <thead>
                <tr>
                    <th>N° Pedido</th>
                    <th>Cliente</th>
                    <th>Fecha / Hora</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_orders as $ord)
                    <tr>
                        <td><strong>{{ $ord->order_number }}</strong></td>
                        <td>{{ $ord->user->name ?? 'Cliente' }} ({{ $ord->user->email ?? 'N/A' }})</td>
                        <td>{{ $ord->created_at->format('d/m/Y H:i') }}</td>
                        <td><strong>${{ number_format($ord->total, 2) }}</strong></td>
                        <td>
                            @if($ord->status == 'Pendiente')
                                <span class="badge badge-pending">Pendiente</span>
                            @elseif($ord->status == 'En preparación')
                                <span class="badge badge-preparing">En prep.</span>
                            @elseif($ord->status == 'Listo para entrega')
                                <span class="badge badge-ready">Listo</span>
                            @elseif($ord->status == 'Entregado')
                                <span class="badge badge-delivered">Entregado</span>
                            @else
                                <span class="badge badge-cancelled">Cancelado</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No hay pedidos recientes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>

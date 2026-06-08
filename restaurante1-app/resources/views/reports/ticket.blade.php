<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #000;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
            background-color: #fff;
        }
        .ticket-container {
            max-width: 300px;
            margin: 0 auto;
            border: 1px dashed #000;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .logo-text {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .subtitle {
            font-size: 9px;
            color: #555;
            margin-bottom: 5px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .info-table, .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .info-table td {
            padding: 2px 0;
        }
        .items-table th {
            border-bottom: 1px dashed #000;
            text-align: left;
            padding: 4px 0;
            font-size: 10px;
        }
        .items-table td {
            padding: 4px 0;
        }
        .text-right {
            text-align: right;
        }
        .totals-section {
            font-size: 11px;
            margin-top: 5px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }
        .total-bold {
            font-weight: bold;
            font-size: 13px;
            border-top: 1px double #000;
            border-bottom: 1px double #000;
            padding: 4px 0;
            margin-top: 4px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 6px;
            border: 1px solid #000;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .qr-section {
            text-align: center;
            margin: 15px 0 10px 0;
        }
        .qr-image {
            width: 120px;
            height: 120px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .footer-msg {
            text-align: center;
            font-size: 10px;
            margin-top: 15px;
            font-style: italic;
        }
        .print-btn-container {
            text-align: center;
            margin-top: 20px;
        }
        .print-btn {
            background-color: #000;
            color: #fff;
            border: 2px solid #000;
            padding: 8px 16px;
            font-weight: bold;
            font-family: inherit;
            cursor: pointer;
            text-transform: uppercase;
            font-size: 11px;
        }
        .print-btn:hover {
            background-color: #fff;
            color: #000;
        }
        @media print {
            .print-btn-container {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .ticket-container {
                border: none;
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="ticket-container">
        <!-- Logo / Header -->
        <div class="header">
            <div class="logo-text">QUICKBITE EXPRESS</div>
            <div class="subtitle">Sabor a alta velocidad</div>
            <div style="font-size: 9px;">Calle Principal N° 2026, San Salvador</div>
            <div style="font-size: 9px;">Tel: +503 2222-2026</div>
        </div>

        <div class="divider"></div>

        <!-- Meta Info -->
        <table class="info-table">
            <tr>
                <td><strong>ORDEN:</strong></td>
                <td class="text-right"><strong>{{ $order->order_number }}</strong></td>
            </tr>
            <tr>
                <td><strong>CLIENTE:</strong></td>
                <td class="text-right">{{ $order->user->name ?? 'Cliente' }}</td>
            </tr>
            <tr>
                <td><strong>FECHA:</strong></td>
                <td class="text-right">{{ $order->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>ESTADO:</strong></td>
                <td class="text-right">
                    <span class="status-badge">{{ $order->status }}</span>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>CANT/DETALLE</th>
                    <th class="text-right">P.UNIT</th>
                    <th class="text-right">SUBT</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $subtotalCalculated = 0;
                @endphp
                @foreach($order->items as $item)
                    @php
                        $itemSubtotal = $item->quantity * $item->price;
                        $subtotalCalculated += $itemSubtotal;
                    @endphp
                    <tr>
                        <td>{{ $item->quantity }}x {{ $item->product->name ?? 'Producto' }}</td>
                        <td class="text-right">${{ number_format($item->price, 2) }}</td>
                        <td class="text-right">${{ number_format($itemSubtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-row">
                <span>SUBTOTAL PRODUCTOS:</span>
                <span>${{ number_format($subtotalCalculated, 2) }}</span>
            </div>
            
            @if($order->discount > 0)
                <div class="totals-row">
                    <span>DESCUENTO ({{ $order->promotion_code ?? 'CUPÓN' }}):</span>
                    <span>-${{ number_format($order->discount, 2) }}</span>
                </div>
            @endif

            <!-- Costos adicionales (Envio y Servicio, asumiendo los del carrito) -->
            @php
                // En CartView, envio = 2.50 y servicio = 0.99. Si el total es superior, calculamos la diferencia como envio + servicio.
                $difference = $order->total - ($subtotalCalculated - $order->discount);
                $envioServicio = $difference > 0 ? $difference : 0.00;
            @endphp
            
            @if($envioServicio > 0)
                <div class="totals-row">
                    <span>ENVÍO Y SERVICIO:</span>
                    <span>${{ number_format($envioServicio, 2) }}</span>
                </div>
            @endif

            <div class="totals-row total-bold">
                <span>TOTAL A PAGAR:</span>
                <span>${{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <!-- QR Code unique -->
        <div class="qr-section">
            <p style="font-size: 8px; margin-bottom: 5px; text-transform: uppercase;">Escanear para seguimiento de pedido</p>
            <img class="qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(url('/tracking/' . $order->order_number)) }}" alt="Código QR de seguimiento">
        </div>

        <div class="divider"></div>

        <!-- Thank you message -->
        <div class="footer-msg">
            ¡Muchas gracias por tu compra!<br>
            Tu comida está en buenas manos.<br>
            QuickBite Express.
        </div>
    </div>

    <!-- Print Button -->
    <div class="print-btn-container">
        <button class="print-btn" onclick="window.print()">Imprimir Ticket</button>
    </div>

</body>
</html>

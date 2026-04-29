<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->transaction_id }}</title>
    <style>
        /* Thermal Printer Optimization (58mm width approx 2in or 48mm printable) */
        @page {
            margin: 0;
            size: 58mm auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            line-height: 1.2;
            width: 58mm;
            margin: 0 auto;
            padding: 2mm;
            box-sizing: border-box;
            text-transform: uppercase;
            /* CRITICAL: Disable anti-aliasing to fix blurry thermal prints */
            -webkit-font-smoothing: none;
            text-rendering: optimizeSpeed;
            font-smooth: never;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        
        .header {
            margin-bottom: 5px;
            border-bottom: 1px solid #000; /* Solid borders are crisper than dashed */
            padding-bottom: 5px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
        }
        
        .info {
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            font-size: 10px;
            font-weight: bold; /* Make text thicker */
        }
        .info p { margin: 2px 0; display: flex; justify-content: space-between; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 5px;
        }
        th, td {
            padding: 2px 0;
            vertical-align: top;
        }
        th {
            border-bottom: 1px solid #000;
            text-align: left;
            font-weight: bold;
        }
        .col-qty { width: 15%; text-align: center; }
        .col-desc { width: 50%; }
        .col-total { width: 35%; text-align: right; }
        
        .totals {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-bottom: 5px;
            font-weight: bold; /* Thicker numbers */
        }
        .totals .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 11px;
        }
        .totals .grand-total {
            font-weight: 900;
            font-size: 14px;
            border-top: 2px solid #000; /* Thicker borders */
            border-bottom: 2px solid #000;
            padding: 2px 0;
            margin-top: 2px;
        }
        
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 10px;
            padding-bottom: 15px; /* Extra space at the bottom for tearing */
        }
        .footer p { margin: 2px 0; }
        
        /* Hide everything else when printing if needed, though this is a standalone view */
        @media print {
            body { width: 58mm; padding: 0; margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h1>KTV SYSTEM</h1>
        <p>123 Karaoke Street</p>
        <p>Tel: (123) 456-7890</p>
    </div>

    <div class="info">
        <p><span>TRX:</span> <span>{{ $order->transaction_id }}</span></p>
        <p><span>DATE:</span> <span>{{ $order->updated_at->format('Y-m-d H:i') }}</span></p>
        <p><span>CASHIER:</span> <span>{{ $order->user->name ?? 'System' }}</span></p>
        @if($order->roomSession)
        <p><span>ROOM:</span> <span>{{ $order->roomSession->room->name ?? 'N/A' }}</span></p>
        @else
        <p><span>TYPE:</span> <span>{{ ucfirst($order->order_type) }}</span></p>
        <p><span>OPTION:</span> <span>{{ ucfirst($order->dining_option ?? 'Dine-in') }}</span></p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-qty">Q</th>
                <th class="col-desc">ITEM</th>
                <th class="col-total">AMT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td class="col-qty">{{ $item->quantity }}</td>
                <td class="col-desc">{{ Str::limit($item->name, 15) }}<br><small>@ {{ number_format($item->unit_price, 2) }}</small></td>
                <td class="col-total">{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>SUBTOTAL:</span>
            <span>{{ number_format($order->total_amount, 2) }}</span>
        </div>
        <div class="row grand-total">
            <span>TOTAL:</span>
            <span>{{ number_format($order->total_amount, 2) }}</span>
        </div>
        <div class="row">
            <span>PAID:</span>
            <span>{{ number_format($order->amount_received ?? $order->total_amount, 2) }}</span>
        </div>
        <div class="row">
            <span>CHANGE:</span>
            <span>{{ number_format(max(0, ($order->amount_received ?? $order->total_amount) - $order->total_amount), 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for visiting!</p>
        <p>Please come again.</p>
        <br>
        <p>......................</p>
        <p>Customer Signature</p>
    </div>

    <script>
        // Critical Enhancement #2: Auto-Open Print Dialog
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

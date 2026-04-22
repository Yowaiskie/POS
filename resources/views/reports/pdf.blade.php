<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report - {{ ucfirst($period) }}</title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif; /* DejaVu Sans supports the Peso symbol */
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        .business-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e1b4b;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .report-title {
            font-size: 14px;
            color: #475569;
            margin: 5px 0 0;
            font-weight: normal;
        }
        .info-bar {
            background: #f8fafc;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .info-item {
            display: inline-block;
            width: 48%;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #312e81;
            text-transform: uppercase;
            margin: 25px 0 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-table {
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 10px 0;
            margin-left: -10px;
            border-collapse: separate;
        }
        .summary-card {
            width: 25%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px 10px;
            text-align: center;
            vertical-align: middle;
        }
        .summary-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
        }
        .summary-value.highlight {
            color: #4f46e5;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: #fff;
        }
        th {
            background: #f1f5f9;
            color: #475569;
            text-align: left;
            padding: 10px 8px;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .currency { font-family: 'DejaVu Sans'; } /* Ensure Peso sign uses the right font */
        
        .badge {
            padding: 3px 7px;
            border-radius: 999px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-gcash { background: #dbeafe; color: #1d4ed8; }
        .badge-cash { background: #dcfce7; color: #15803d; }
        .badge-promo { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
        .ref-num {
            font-size: 8px;
            color: #64748b;
            display: block;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div style="margin-bottom: 15px;">
            <img src="{{ public_path('images/logo.png') }}" style="height: 80px; width: 80px; border-radius: 40px; border: 1px solid #e2e8f0;">
        </div>
        <h1 class="business-name">BOSSTON KTV BAR</h1>
        <p class="report-title">SALES REPORT — {{ strtoupper($period) }}</p>
    </div>

    <div class="info-bar clearfix">
        <div class="info-item">
            <span style="color: #64748b">Date Range:</span> <span class="font-bold">{{ $start->format('M d, Y') }} - {{ $end->format('M d, Y') }}</span>
        </div>
        <div class="info-item text-right">
            <span style="color: #64748b">Generated:</span> <span class="font-bold">{{ $generated_at }}</span>
        </div>
    </div>

    <div class="section-title">1. Executive Summary</div>
    <table class="summary-table">
        <tr>
            <td class="summary-card">
                <div class="summary-label">Total Sales</div>
                <div class="summary-value highlight">₱{{ number_format($totalSales) }}</div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Room Rental</div>
                <div class="summary-value">₱{{ number_format($roomSales) }}</div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Short Orders</div>
                <div class="summary-value">₱{{ number_format($shortSales) }}</div>
            </td>
            <td class="summary-card">
                <div class="summary-label">Transactions</div>
                <div class="summary-value">{{ $totalTransactions }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">2. Sales Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Revenue Category</th>
                <th class="text-right">Amount (₱)</th>
                <th class="text-right">Share (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Room Rental Sales</td>
                <td class="text-right font-bold">₱{{ number_format($roomSales) }}</td>
                <td class="text-right">{{ $roomPercent }}%</td>
            </tr>
            <tr>
                <td>Short Order Sales</td>
                <td class="text-right font-bold">₱{{ number_format($shortSales) }}</td>
                <td class="text-right">{{ $shortPercent }}%</td>
            </tr>
            <tr style="background: #f8fafc;">
                <td class="font-bold">TOTAL REVENUE</td>
                <td class="text-right font-bold" style="color: #4f46e5; font-size: 13px;">₱{{ number_format($totalSales) }}</td>
                <td class="text-right font-bold">100%</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">3. Top Performing Products (Top {{ count($topSelling) }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Rank</th>
                <th>Item Description</th>
                <th class="text-center">Units Sold</th>
                <th class="text-right">Gross Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topSelling as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-bold">{{ $item['name'] }}</td>
                <td class="text-center">{{ $item['qty'] }}</td>
                <td class="text-right font-bold">₱{{ number_format($item['rev']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">4. Staff Performance Metrics</div>
    <table style="width: 70%;">
        <thead>
            <tr>
                <th>Staff Member</th>
                <th class="text-right">Total Generated Sales</th>
                <th class="text-center">Performance Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staffPerformance as $staff)
            <tr>
                <td class="font-bold">{{ $staff['name'] }}</td>
                <td class="text-right">₱{{ number_format($staff['sales']) }}</td>
                <td class="text-center">
                    <span style="color: {{ $staff['perf'] === 'Top Performer' ? '#059669' : '#475569' }}; font-weight: bold; font-size: 9px;">
                        {{ strtoupper($staff['perf']) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="page-break-before: always;"></div> <!-- Force Audit Log to next page if needed -->

    <div class="section-title">5. Transaction Audit Log</div>
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Category</th>
                <th>Payment Method</th>
                <th>Staff Name</th>
                <th class="text-center">Timestamp</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentOrders as $order)
            <tr>
                <td style="font-family: monospace; font-size: 9px; color: #4f46e5;">{{ $order->transaction_id ?? $order->order_number }}</td>
                <td>
                    @if($order->promo_name)
                        <span class="badge badge-promo">{{ $order->promo_name }}</span>
                    @else
                        {{ ucfirst($order->order_type) }}
                    @endif
                </td>
                <td>
                    <span class="badge {{ $order->payment_method === 'gcash' ? 'badge-gcash' : 'badge-cash' }}">
                        {{ strtoupper($order->payment_method) }}
                    </span>
                    @if($order->reference_number)
                        <span class="ref-num">Ref: {{ $order->reference_number }}</span>
                    @endif
                </td>
                <td style="font-size: 9px; font-weight: bold;">{{ $order->user->name ?? 'SYSTEM' }}</td>
                <td class="text-center" style="font-size: 9px;">{{ $order->closed_at->format('M d, Y h:i A') }}</td>
                <td class="text-right font-bold">₱{{ number_format($order->total_amount) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated by {{ config('app.name') }} POS System • Office Copy • Do not duplicate without authorization
    </div>
</body>
</html>

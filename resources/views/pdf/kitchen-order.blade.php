<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kitchen Order - {{ $date }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #059669;
            margin-bottom: 30px;
        }
        .logo-container {
            margin-bottom: 15px;
        }
        .logo {
            max-height: 60px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 20px;
            font-weight: bold;
            color: #111;
            margin-top: 15px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 15px 5px 0;
            width: 120px;
            color: #374151;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
            color: #111;
        }
        table.orders {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.orders th {
            background: #059669;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
        }
        table.orders th:last-child {
            text-align: center;
            width: 120px;
        }
        table.orders td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        table.orders td:last-child {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }
        table.orders tr:nth-child(even) {
            background: #f9fafb;
        }
        table.orders tr:hover {
            background: #ecfdf5;
        }
        .total-row {
            background: #059669 !important;
            color: white;
        }
        .total-row td {
            font-weight: bold;
            font-size: 16px;
            border: none !important;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            {{-- GFZA Logo --}}
            <img src="{{ public_path('images/gfza-logo.png') }}" alt="GFZA Logo" class="logo" onerror="this.style.display='none'">
        </div>
        <div class="company-name">GHANA FREE ZONES AUTHORITY</div>
        <div class="document-title">KITCHEN ORDER SUMMARY</div>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">Date:</span>
            <span class="info-value">{{ $date }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Day:</span>
            <span class="info-value">{{ $dayOfWeek }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Caterer:</span>
            <span class="info-value">{{ $caterer }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Week:</span>
            <span class="info-value">{{ $weekLabel }}</span>
        </div>
    </div>

    @if($mealCounts->count() > 0)
        <table class="orders">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Meal</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mealCounts as $index => $meal)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $meal->name }}</td>
                        <td>{{ $meal->total_orders }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2">TOTAL ORDERS</td>
                    <td>{{ $totalOrders }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="no-data">
            No meal orders for this date.
        </div>
    @endif

    <div class="footer">
        Generated on {{ now()->format('l, jS F Y \a\t g:i A') }} | GFZA Internal Operations Portal
    </div>
</body>
</html>

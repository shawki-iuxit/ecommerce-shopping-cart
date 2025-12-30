<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .products-table th,
        .products-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .products-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .products-table tr:hover {
            background-color: #f8f9fa;
        }
        .no-sales {
            text-align: center;
            color: #6c757d;
            padding: 40px;
            font-style: italic;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        .currency {
            color: #28a745;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Daily Sales Report</h1>
            <p style="margin: 0; opacity: 0.9;">{{ $reportDate }}</p>
        </div>
        
        <div class="content">
            @if(!empty($salesData['orders']) && count($salesData['orders']) > 0)
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">{{ count($salesData['orders']) }}</div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ $salesData['total_items_sold'] }}</div>
                        <div class="stat-label">Items Sold</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${{ number_format($salesData['total_revenue'], 2) }}</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>

                <h3>Products Sold Today</h3>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Quantity Sold</th>
                            <th>Unit Price</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesData['products'] as $product)
                            <tr>
                                <td>{{ $product['name'] }}</td>
                                <td>{{ $product['sku'] }}</td>
                                <td>{{ $product['quantity_sold'] }}</td>
                                <td><span class="currency">${{ number_format($product['unit_price'], 2) }}</span></td>
                                <td><span class="currency">${{ number_format($product['total_revenue'], 2) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h3>Recent Orders</h3>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesData['orders'] as $order)
                            <tr>
                                <td>{{ $order['order_number'] }}</td>
                                <td>{{ $order['customer_name'] }}</td>
                                <td>{{ $order['items_count'] }}</td>
                                <td><span class="currency">${{ number_format($order['total_amount'], 2) }}</span></td>
                                <td>{{ $order['created_at'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-sales">
                    <h3>📭 No Sales Today</h3>
                    <p>There were no orders placed on {{ $reportDate }}.</p>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p>This daily sales report was automatically generated by your E-commerce System.</p>
            <p>Generated on: {{ now()->format('F j, Y g:i A') }}</p>
        </div>
    </div>
</body>
</html>
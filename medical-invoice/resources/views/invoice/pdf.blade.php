<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 4px; }
    </style>
</head>
<body>
    <h2>FAST ENTERPRISES</h2>
    <p>Doctors Line Hyderabad - Pharmaceutical Distributors</p>
    <p><strong>Invoice To:</strong> {{ $data['customer_name'] }} | {{ $data['customer_address'] }}</p>

    <table>
        <thead>
            <tr>
                <th>Product</th><th>Batch</th><th>Expiry</th><th>TP</th><th>Rate</th>
                <th>Qty</th><th>Bonus</th><th>Total</th><th>Disc %</th><th>Disc Amt</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['items'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['batch'] }}</td>
                    <td>{{ $item['expiry'] }}</td>
                    <td>{{ $item['tp'] }}</td>
                    <td>{{ $item['rate'] }}</td>
                    <td>{{ $item['qty'] }}</td>
                    <td>{{ $item['bonus'] }}</td>
                    <td>{{ $item['total'] }}</td>
                    <td>{{ $item['disc_percent'] }}</td>
                    <td>{{ $item['disc_amount'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <p><strong>Advance Tax:</strong> {{ $data['advance_tax'] }}</p>
    <p><strong>Gross Total:</strong> {{ $data['gross_total'] }}</p>

    <br><br>
    <p><i>Warranty: Form 2A... valid only under DRAP rules.</i></p>

    <p style="text-align:right;">Signature: ______________</p>
</body>
</html>

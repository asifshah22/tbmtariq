<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Print</title>
    <?php
      $invoiceData = $_REQUEST['data'];
      $uniqueId = strtoupper(substr(uniqid('INV', true), 0, 5));  // Using 'INV' prefix and limiting to 5 characters

      //print_r($data);
     ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .invoice-header, .invoice-footer {
            text-align: center;
        }
        .invoice-footer {
            margin-top: 30px;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
            .table td, .table th {
                border: 1px solid #000 !important;
            }
        }
        .row {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.col-md-3 {
    margin-bottom: 10px;
}

strong {
    font-weight: bold;
}

span {
    font-size: 1.1em;
}

    </style>
</head>
<body>
    <div class="container print-area">
        <div class="invoice-header">
            <h2>Invoice</h2>
            <p><strong>Invoice ID:</strong> <span id="invoiceId"></span></p>
            <p><strong>Date & Time:</strong> <span id="invoiceDateTime"></span></p>
        </div>

    <div class="mt-4">
    <div class="row">
        <div class="col-md-6">
            <p><strong>Customer Name:</strong> <span id="customerName"></span></p>
        </div>
        <div class="col-md-6">
            <p><strong>Salesman:</strong> <span id="salesman"></span></p>
        </div>
    </div>
    <!-- New row for additional detail -->
    <div class="row">
        <!-- <div class="col-md-6">
            <p><strong>Phone Number:</strong> <span id="customerPhone"></span></p>
        </div> -->
        <div class="col-md-6">
            <!-- You can add another field here if needed -->
        </div>
    </div>
</div>



        <h4 class="mt-4">Invoice Items</h4>
        <table class="table">
            <thead>
                <tr>
                <th>Product Names</th>
                <th>Pack</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Stx(%)</th>
                <th>Rat Disc(%)</th>
                <th>Amount</th>
                </tr>
            </thead>
            <tbody id="invoiceItems">
                <!-- Items will be populated here -->
            </tbody>
        </table>

        <div class="row">
    <div class="col-md-3">
        <strong>Stx Total:</strong> <span id="taxTotal"></span>
    </div>
    <div class="col-md-3">
        <strong>Discount Total:</strong> <span id="Discount"></span>
    </div>
    <div class="col-md-3">
        <strong>Sub Total:</strong> <span id="subTotal"></span>
    </div>
    <div class="col-md-3">
        <strong>Total Amount:</strong> <span id="totalAmount"></span>
    </div>
</div>


       <!--  <div class="invoice-footer">
        <p><strong>Net Payable Amount:</strong> <span id="grandTotal"></span></p>
        </div> -->

        <button type="button" class="btn btn-primary no-print mt-3" onclick="window.print()">Print Invoice</button>
    </div>

    <script>
        function numberToWords(num) {
            const ones = [
                'Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 
                'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 
                'Nineteen'
            ];
            const tens = [
                '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
            ];
            const thousands = [
                '', 'Thousand', 'Million', 'Billion', 'Trillion'
            ];

            if (num === 0) return ones[0];

            let words = '';
            let chunkIndex = 0;

            while (num > 0) {
                let chunk = num % 1000;
                if (chunk > 0) {
                    words = helper(chunk) + (thousands[chunkIndex] ? ' ' + thousands[chunkIndex] : '') + ' ' + words;
                }
                num = Math.floor(num / 1000);
                chunkIndex++;
            }

            return words.trim();

            function helper(n) {
                let result = '';
                if (n >= 100) {
                    result += ones[Math.floor(n / 100)] + ' Hundred ';
                    n = n % 100;
                }
                if (n >= 20) {
                    result += tens[Math.floor(n / 10)] + ' ';
                    n = n % 10;
                }
                if (n > 0) {
                    result += ones[n] + ' ';
                }
                return result.trim();
            }
        }
        function calculateTotal(data) {
    let subTotal = 0;
    let taxTotal = 0;
    let discountTotal = 0;  // Initialize discount total

    // Loop through items and populate table
    const tableBody = document.getElementById("invoiceItems");
    data.items.forEach(item => {
        const row = document.createElement("tr");
        let amount = item.quantity * item.rate;
        let taxAmount = (amount * item.tax) / 100;
        let totalAmount = amount + taxAmount;

        // Calculate the discount for this item
        let discountAmount = (amount * item.discount) / 100;
        discountTotal += discountAmount;  // Add the discount to the total

        subTotal += amount;
        taxTotal += taxAmount;

        row.innerHTML = `
            <td>${item.description}</td>
            <td>${item.pack}</td>
            <td>${item.quantity}</td>
            <td>${item.rate}</td>
            <td>${item.tax}</td>
            <td>${item.discount}</td>
            <td class="amount">${(totalAmount - discountAmount).toFixed(2)}</td>
        `;
        tableBody.appendChild(row);
    });

    let totalAmount = subTotal + taxTotal - discountTotal;  // Subtract the total discount from the total amount

    // Populate totals
    document.getElementById("invoiceDateTime").textContent = data.invoiceDateTime;
    document.getElementById("salesman").textContent = data.salesman;
    document.getElementById("customerName").textContent = data.customerName;
    document.getElementById("subTotal").textContent = subTotal.toFixed(2);
    document.getElementById("taxTotal").textContent = taxTotal.toFixed(2);
    document.getElementById("Discount").textContent = discountTotal.toFixed(2);  // Display total discount
    document.getElementById("totalAmount").textContent = totalAmount.toFixed(2);
    
}

        /*function calculateTotal(data) {
            let subTotal = 0;
            let taxTotal = 0;
            let Discount = 0;

            // Loop through items and populate table
            const tableBody = document.getElementById("invoiceItems");
            data.items.forEach(item => {
                const row = document.createElement("tr");
                let amount = item.quantity * item.rate;
                let taxAmount = (amount * item.tax) / 100;
                let totalAmount = amount + taxAmount;

                subTotal += amount;
                taxTotal += taxAmount;

                row.innerHTML = `
                    <td>${item.description}</td>
                    <td>${item.pack}</td>
                    <td>${item.quantity}</td>
                    <td>${item.rate}</td>
                    <td>${item.tax}</td>
                    <td>${item.discount}</td>
                    <td class="amount">${totalAmount.toFixed(2)}</td>
                `;
                tableBody.appendChild(row);
            });

            let totalAmount = subTotal + taxTotal;
            // Populate totals
            document.getElementById("invoiceDateTime").textContent = data.invoiceDateTime;
            document.getElementById("salesman").textContent = data.salesman;
            document.getElementById("customerName").textContent = data.customerName;
            document.getElementById("subTotal").textContent = subTotal.toFixed(2);
            document.getElementById("taxTotal").textContent = taxTotal.toFixed(2);
            document.getElementById("totalAmount").textContent = totalAmount.toFixed(2);
            //document.getElementById("grandTotal").textContent = totalAmount.toFixed(2);
           // document.getElementById("amountInWords").textContent = numberToWords(totalAmount);
        }*/
        document.addEventListener('DOMContentLoaded', function() {
            var uniqueId = <?php echo json_encode($uniqueId); ?>;
            // Set the unique ID as the content of the span
            document.getElementById('invoiceId').textContent = uniqueId;
            var invoiceData = <?php echo $invoiceData; ?>;
            calculateTotal(invoiceData);
             // Get the current date and time
            var currentDateTime = new Date();

            // Format the date and time in English (e.g., "February 18, 2025 12:00 PM")
            var formattedDateTime = currentDateTime.toLocaleString('en-US', {
                weekday: 'long', // Day of the week
                year: 'numeric',
                month: 'long',   // Full month name
                day: 'numeric',
                hour: 'numeric', // Hour (12-hour format)
                minute: 'numeric',
                second: 'numeric',
                hour12: true      // Use 12-hour clock
            });
            document.getElementById('invoiceDateTime').textContent = formattedDateTime;

            // Load your dynamic data (e.g., from a JSON file or object)
            /*fetch('invoiceData.json')  // Fetch data from JSON file
                .then(response => response.json())
                .then(data => {
                    calculateTotal(data);
                })
                .catch(error => {
                    console.error('Error loading invoice data:', error);
                });*/
        });
    </script>
</body>
</html>

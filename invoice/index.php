<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Generator</title>
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
            #subTotal, #taxTotal, #totalAmount {
                display: none;
            }
            #subTotal + label, #taxTotal + label, #totalAmount + label {
                visibility: visible;
            }
        }
    </style>
</head>
<body>
    <div class="container print-area">
        <form>
            <div class="mb-3">
                <label class="form-label">Salesman</label>
                <input type="text" class="form-control" id="salesman" required>
            </div>
            <!-- <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" required>
            </div> -->
            <!-- <div class="mb-3">
                <label class="form-label">Day of Time</label>
                <input type="datetime-local" class="form-control" id="dayOfTime" required>
            </div> -->

            <div class="mb-3">
                <label class="form-label">Customer Name</label>
                <input type="text" id="customerName" class="form-control" required>
            </div>
            <!-- <div class="mb-3">
                <label class="form-label">Customer Address</label>
                <textarea class="form-control" id="customerAddress" required></textarea>
            </div> -->
            <h4 class="mt-4">Invoice Items</h4>
            <table class="table table-bordered" id="invoiceTable">
                <thead>
                    <tr>
                        <th>Product Names</th>
                        <th>Pack</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Stx(%)</th>
                        <th>Rat Disc(%)</th>
                        <th>Amount</th>
                        <th class="no-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" class="form-control" required></td>
                        <td><input type="text" class="form-control pack" required></td>
                        <td><input type="number" class="form-control qty" required></td>
                        <td><input type="number" class="form-control rate" required></td>
                        <td><input type="number" class="form-control tax" required></td>
                        <td><input type="number" class="form-control discount" required></td>
                        <td><input type="text" class="form-control amount" readonly></td>
                        <td class="no-print"><button type="button" class="btn btn-danger remove">X</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-success no-print" id="addRow">+ Add Item</button>
            <div class="row mt-4">
                <div class="col-md-4">
                    <label>Sub Total</label>
                    <input type="text" class="form-control" id="subTotal" readonly>
                </div>
                <div class="col-md-4">
                    <label>Stx Total</label>
                    <input type="text" class="form-control" id="taxTotal" readonly>
                </div>
                <div class="col-md-4">
                    <label>Total</label>
                    <input type="text" class="form-control" id="totalAmount" readonly>
                </div>
            </div>
            <!-- <div class="mt-4">
                <p><strong>Amount in Words:</strong> <span id="amountInWords"></span></p>
                <p><strong>Net Payable Amount:</strong> <span id="grandTotal"></span></p>
                <p><strong>Software Design By:</strong> Zeshan Ghuryani (03123634048)</p>
            </div> -->
            <button type="button" class="btn btn-primary no-print mt-3" id="generateJson">Next to invoice</button>
        </form>
    </div>
    <script>
        document.getElementById('generateJson').addEventListener('click', function() {
            const invoiceData = {
                "salesman": document.getElementById('salesman').value,
                "customerName": document.getElementById('customerName').value,
                "items": [],
                "subTotal": document.getElementById('subTotal').value,
                "taxTotal": document.getElementById('taxTotal').value,
                "totalAmount": document.getElementById('totalAmount').value
            };

            document.querySelectorAll("#invoiceTable tbody tr").forEach(row => {
                const description = row.querySelector("input[type=text]").value;
                const quantity = row.querySelector(".qty").value;
                const rate = row.querySelector(".rate").value;
                const tax = row.querySelector(".tax").value;
                const discount = row.querySelector(".discount").value;
                const amount = row.querySelector(".amount").value;
                const pack = row.querySelector(".pack").value;
                if (description && quantity && rate && tax) {
                    invoiceData.items.push({
                        description,
                        pack,
                        quantity,
                        rate,
                        tax,
                        discount,
                        amount
                    });
                }
            });
            const jsonString = JSON.stringify(invoiceData);
            const baseUrl = 'print.php';
            const queryString = '?data='+jsonString;
            // Redirect to the new URL with the query string
            window.location.href = baseUrl + queryString;
            console.log("invoiceData", invoiceData);
        });

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

        function calculateTotal() {
            let subTotal = 0;
            let taxTotal = 0;
            
            document.querySelectorAll("#invoiceTable tbody tr").forEach(row => {
                let qty = parseFloat(row.querySelector(".qty").value) || 0;
                let rate = parseFloat(row.querySelector(".rate").value) || 0;
                let tax = parseFloat(row.querySelector(".tax").value) || 0;
                let discount = parseFloat(row.querySelector(".discount").value) || 0;
                let amountField = row.querySelector(".amount");
                
                let amount = qty * rate;
                let discountAmount = (amount * discount) / 100;
                let amountAfterDiscount = amount - discountAmount;
                let taxAmount = (amountAfterDiscount * tax) / 100;
                let totalAmount = amountAfterDiscount + taxAmount;
                
                amountField.value = totalAmount.toFixed(2);
                subTotal += amountAfterDiscount;
                taxTotal += taxAmount;
            });
            
            let totalAmount = subTotal + taxTotal;
            
            document.getElementById("subTotal").value = subTotal.toFixed(2);
            document.getElementById("taxTotal").value = taxTotal.toFixed(2);
            document.getElementById("totalAmount").value = totalAmount.toFixed(2);
            document.getElementById("amountInWords").textContent = numberToWords(totalAmount.toFixed(2));
            document.getElementById("grandTotal").textContent = totalAmount.toFixed(2);
        }

document.getElementById('addRow').addEventListener('click', function() {
    let table = document.getElementById('invoiceTable').getElementsByTagName('tbody')[0];
    let newRow = table.insertRow();
    newRow.innerHTML = `
        <td><input type="text" class="form-control" required></td>
        <td><input type="text" class="form-control pack" required></td>
        <td><input type="number" class="form-control qty" required></td>
        <td><input type="number" class="form-control rate" required></td>
        <td><input type="number" class="form-control tax" required></td>
        <td><input type="number" class="form-control discount" required></td>
        <td><input type="text" class="form-control amount" readonly></td>
        <td class="no-print"><button type="button" class="btn btn-danger remove">X</button></td>
    `;
    table.appendChild(newRow);
    calculateTotal();
});

        document.querySelector("#invoiceTable").addEventListener('click', function(event) {
            if (event.target.classList.contains('remove')) {
                event.target.closest("tr").remove();
                calculateTotal();
            }
        });

        document.querySelector("#invoiceTable").addEventListener('input', function(event) {
    // Check if the input is of the type we care about (like qty, rate, etc.)
    if (event.target.matches('.qty, .rate, .tax, .discount')) {
        calculateTotal();
    }
});
    </script>
</body>
</html>

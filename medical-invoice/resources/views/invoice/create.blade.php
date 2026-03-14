<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FAST ENTERPRISES - Medical Invoice</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Signature Pad Library -->
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>

  <style>
    table input {
      border: none;
      width: 100%;
    }
    @media print {
      @page {
        size: A3 portrait;
        margin: 1cm;
      }
      .no-print {
        display: none !important;
      }
      input, textarea, canvas {
        border: none !important;
        outline: none !important;
      }
      .table-responsive {
        overflow: visible !important;
      }
    }
    th, td {
      vertical-align: middle !important;
    }
    canvas {
      touch-action: none;
    }
  </style>
</head>
<body class="p-4">

  <div class="container border p-4" id="invoice-area">
    <!-- Header -->
    <div class="text-center mb-4">
      <h4 class="fw-bold">FAST ENTERPRISES</h4>
      <p>Pharmaceutical Distributors</p>
      <p>Doctors Line Hyderabad | Phone: 022-2632221</p>
      <p><strong>Drug Sale License:</strong> D-16534</p>
    </div>

    <!-- Customer Info -->
    <div class="row mb-3">
      <div class="col-md-6">
        <label>To:</label>
        <input type="text" class="form-control" placeholder="Customer Name" />
      </div>
      <div class="col-md-6">
        <label>Address:</label>
        <input type="text" class="form-control" placeholder="Customer Address" />
      </div>
    </div>

    <!-- Product Table -->
    <div>
      <table class="table table-bordered align-middle w-100" id="invoiceTable" style="table-layout: auto;">
        <thead class="table-light text-center">
          <tr>
            <th>Product</th>
            <th>Pack</th>
            <th>Batch</th>
            <th>Expiry</th>
            <th>TP</th>
            <th>Rate</th>
            <th>Qty</th>
            <th>Bonus</th>
            <th>Total</th>
            <th>Disc %</th>
            <th>Amount</th>
            <th class="no-print">❌</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input name="product[]" required /></td>
            <td><input name="pack[]" /></td>
            <td><input name="batch[]" /></td>
            <td><input name="expiry[]" type="date" class="form-control" /></td>
            <td><input name="tp[]" type="number" /></td>
            <td><input name="rate[]" type="number" oninput="calculate(this)" /></td>
            <td><input name="qty[]" type="number" oninput="calculate(this)" /></td>
            <td><input name="bonus[]" /></td>
            <td><input name="total[]" readonly /></td>
            <td><input name="disc_percent[]" type="number" oninput="calculate(this)" /></td>
            <td><input name="amount[]" readonly /></td>
            <td class="text-center no-print"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">×</button></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Buttons -->
    <div class="mb-3 no-print">
      <button type="button" class="btn btn-secondary" onclick="addRow()">➕ Add Item</button>
      <button type="button" class="btn btn-primary" onclick="window.print()">🖨️ Print / Download PDF</button>
    </div>

    <!-- Totals -->
    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <label>Advance Tax</label>
        <input type="number" class="form-control" name="tax" value="0" oninput="calculate()" />
      </div>
      <div class="col-md-4">
        <label>Gross Total</label>
        <input type="number" class="form-control" id="grossTotal" readonly />
      </div>
      <div class="col-md-4">
        <label>Net Sale</label>
        <input type="number" class="form-control" id="netSale" readonly />
      </div>
    </div>

    <!-- Signature Area -->
    <div class="mt-3">
      <label><strong>Distributor Signature:</strong></label>
      <div style="border:1px solid #ccc; width:300px; height:120px;">
        <canvas id="signature-pad" width="300" height="120"></canvas>
      </div>
      <button type="button" class="btn btn-sm btn-secondary mt-2 no-print" onclick="clearSignature()">🧹 Clear Signature</button>
    </div>

    <!-- Warranty -->
    <div class="mt-4" style="font-size: 12px; line-height: 1.5;">
      <p><strong>WARRANTY:</strong> FORM 2A FOR PRESCRIPTION DRUGS IS VALID ONLY UNDER DRAP RULES.</p>

      <p><strong>Warranty - Form 2A (See Rules 19 and 30)</strong><br>
        Mehmood Ali being a person Resident in Pakistan carrying a business at Saddar Hyderabad under the name of <strong>FAST ENTERPRISES</strong> and being an authorized Distributor do hereby warranty the use of drugs sold (identified by us) do not contravene any provision of the Drugs Act 1976 as per warranty of the manufacturer.
      </p>
      <p><strong>Note:</strong> This warranty does not apply to Ayurvedic, Unani, Homeopathic or Biochemic system of medicines, food supplements, General and Cosmetic items mentioned in this invoice.</p>
      <p>2. Opened products shall not be accepted back unless written intimation with invoice is given six months in advance of expiry date.</p>
    </div>

    <hr class="mt-4">
    <p class="text-center" style="font-size: 12px;">
      Developed by <strong>Shahnawaz Ghuryani</strong> | Contact: <strong>03083259933</strong>
    <p class="text-center mt-5 no-print">
  📱 For queries or support, contact via
  <a href="https://wa.me/923083259933" target="_blank" class="btn btn-success btn-sm">WhatsApp</a>
</p>
    </p>

  </div>

  <!-- JS Functions -->
  <script>
    function addRow() {
      const table = document.querySelector("#invoiceTable tbody");
      const newRow = table.rows[0].cloneNode(true);
      newRow.querySelectorAll("input").forEach(input => input.value = '');
      table.appendChild(newRow);
    }

    function removeRow(button) {
      const row = button.closest("tr");
      const table = document.querySelector("#invoiceTable tbody");
      if (table.rows.length > 1) row.remove();
      calculate();
    }

    function calculate(el) {
      const table = document.querySelector("#invoiceTable tbody");
      let gross = 0;

      table.querySelectorAll("tr").forEach(row => {
        const qty = parseFloat(row.querySelector('input[name="qty[]"]').value) || 0;
        const rate = parseFloat(row.querySelector('input[name="rate[]"]').value) || 0;
        const discountPercent = parseFloat(row.querySelector('input[name="disc_percent[]"]').value) || 0;

        const total = qty * rate;
        const discountAmount = total * (discountPercent / 100);
        const netAmount = total - discountAmount;

        row.querySelector('input[name="total[]"]').value = total.toFixed(2);
        row.querySelector('input[name="amount[]"]').value = netAmount.toFixed(2);
        gross += netAmount;
      });

      document.getElementById("grossTotal").value = gross.toFixed(2);
      const tax = parseFloat(document.querySelector('input[name="tax"]').value) || 0;
      document.getElementById("netSale").value = (gross + tax).toFixed(2);
    }

    // Signature Pad
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas);
    function clearSignature() {
      signaturePad.clear();
    }

    // Optional: disable signature drawing during print
    window.onbeforeprint = () => signaturePad.off();
    window.onafterprint = () => signaturePad.on();
  </script>
</body>
</html>

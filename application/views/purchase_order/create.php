<?php date_default_timezone_set("Asia/Karachi"); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Purchase Order
      <small>Create</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Purchase Order</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12 col-xs-12">
        <div id="messages"></div>

        <?php if($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif($this->session->flashdata('error')): ?>
          <div class="alert alert-error alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>

        <?php
          $is_edit = isset($is_edit) ? $is_edit : false;
          $order = isset($order) ? $order : array();
          $items = isset($items) ? $items : array();
          $action_url = isset($action_url) ? $action_url : base_url() . 'index.php/Purchase_order/create';
        ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title"><?php echo $is_edit ? 'Edit Purchase Order' : 'Purchase Order Form'; ?></h3>
          </div>
          <form role="form" action="<?php echo $action_url; ?>" method="post" class="form-horizontal" id="purchaseOrderForm">
            <div class="box-body">
              <div class="form-group">
                <label class="col-sm-2 control-label">Vendor</label>
                <div class="col-sm-10">
                  <select class="form-control" name="vendor_id" id="vendor_id" required>
                    <option value="">Select Vendor</option>
                    <?php foreach ($vendor_data as $value): ?>
                      <?php $selected = (!empty($order) && isset($order['vendor_id']) && (int)$order['vendor_id'] === (int)$value['id']) ? 'selected' : ''; ?>
                      <option value="<?php echo $value['id']; ?>" <?php echo $selected; ?>>
                        <?php echo $value['first_name'].' '.$value['last_name']; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">PO Number</label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" name="po_number" value="<?php echo isset($order['po_number']) ? $order['po_number'] : ''; ?>" required>
                </div>

                <label class="col-sm-2 control-label">PO Date</label>
                <div class="col-sm-4">
                  <?php $po_date = isset($order['po_date']) ? $order['po_date'] : date('Y-m-d'); ?>
                  <input type="date" class="form-control" name="po_date" value="<?php echo $po_date; ?>" required>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Contact Person</label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" name="contact_person" id="contact_person" value="<?php echo isset($order['contact_person']) ? $order['contact_person'] : ''; ?>">
                </div>

                <label class="col-sm-2 control-label">Contact No</label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" name="contact_no" id="contact_no" value="<?php echo isset($order['contact_no']) ? $order['contact_no'] : ''; ?>">
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Terms of Payment</label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" name="terms_of_payment" value="<?php echo isset($order['terms_of_payment']) ? $order['terms_of_payment'] : ''; ?>">
                </div>

                <label class="col-sm-2 control-label">Delivery</label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" name="delivery" value="<?php echo isset($order['delivery']) ? $order['delivery'] : ''; ?>">
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-2 control-label">Remarks</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" name="remarks" id="remarks" value="<?php echo isset($order['remarks']) ? $order['remarks'] : ''; ?>">
                </div>
              </div>

              <hr>

              <div class="table-responsive">
                <table class="table table-bordered" id="po_items_table">
                  <thead>
                    <tr>
                      <th style="width:4%">S#</th>
                      <th>Parts Name</th>
                      <th>Model</th>
                      <th style="width:8%">Qty</th>
                      <th style="width:8%">Unit</th>
                      <th style="width:10%">Rate</th>
                      <th style="width:12%">Amount</th>
                      <th>Remarks</th>
                      <th style="width:6%">
                        <button type="button" id="add_row" class="btn btn-default"><i class="fa fa-plus"></i></button>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(!empty($items)): ?>
                      <?php $row_index = 1; foreach($items as $item): ?>
                        <tr id="row_<?php echo $row_index; ?>">
                          <td><?php echo $row_index; ?></td>
                          <td><input type="text" name="part_name[]" class="form-control" value="<?php echo $item['part_name']; ?>" required></td>
                          <td><input type="text" name="model[]" class="form-control" value="<?php echo $item['model']; ?>"></td>
                          <td><input type="number" name="qty[]" class="form-control qty" min="0" step="0.01" value="<?php echo $item['qty']; ?>"></td>
                          <td><input type="text" name="unit[]" class="form-control" value="<?php echo $item['unit']; ?>"></td>
                          <td><input type="number" name="rate[]" class="form-control rate" min="0" step="0.01" value="<?php echo $item['rate']; ?>"></td>
                          <td><input type="number" name="amount[]" class="form-control amount" min="0" step="0.01" value="<?php echo $item['amount']; ?>" readonly></td>
                          <td><input type="text" name="item_remarks[]" class="form-control" value="<?php echo $item['remarks']; ?>"></td>
                          <td><button type="button" class="btn btn-default remove_row" data-row="<?php echo $row_index; ?>"><i class="fa fa-minus"></i></button></td>
                        </tr>
                        <?php $row_index++; ?>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr id="row_1">
                        <td>1</td>
                        <td>
                          <select name="part_name[]" class="form-control part_name_select" data-current="" required>
                            <option value="">Select Part</option>
                          </select>
                        </td>
                        <td><input type="text" name="model[]" class="form-control"></td>
                        <td><input type="number" name="qty[]" class="form-control qty" min="0" step="0.01"></td>
                        <td><input type="text" name="unit[]" class="form-control"></td>
                        <td><input type="number" name="rate[]" class="form-control rate" min="0" step="0.01"></td>
                        <td><input type="number" name="amount[]" class="form-control amount" min="0" step="0.01" readonly></td>
                        <td><input type="text" name="item_remarks[]" class="form-control"></td>
                        <td><button type="button" class="btn btn-default remove_row" data-row="1"><i class="fa fa-minus"></i></button></td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label class="col-sm-4 control-label">Total Amount</label>
                    <div class="col-sm-8">
                      <input type="number" class="form-control" name="total_amount" id="total_amount" value="<?php echo isset($order['total_amount']) ? $order['total_amount'] : ''; ?>" readonly>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-4 control-label">Sales Tax %</label>
                    <div class="col-sm-8">
                      <?php $tax_percent = isset($order['sales_tax_percent']) ? $order['sales_tax_percent'] : 18; ?>
                      <input type="number" class="form-control" name="sales_tax_percent" id="sales_tax_percent" value="<?php echo $tax_percent; ?>" min="0" step="0.01">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-4 control-label">Sales Tax Amount</label>
                    <div class="col-sm-8">
                      <input type="number" class="form-control" name="sales_tax_amount" id="sales_tax_amount" value="<?php echo isset($order['sales_tax_amount']) ? $order['sales_tax_amount'] : ''; ?>" readonly>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-4 control-label">Grand Total</label>
                    <div class="col-sm-8">
                      <input type="number" class="form-control" name="grand_total" id="grand_total" value="<?php echo isset($order['grand_total']) ? $order['grand_total'] : ''; ?>" readonly>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Update' : 'Save & Print'; ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  (function() {
    var rowCount = <?php echo !empty($items) ? count($items) : 1; ?>;
    var vendorProducts = [];

    function buildPartOptions(currentValue) {
      var html = '<option value="">Select Part</option>';
      var found = false;
      for (var i = 0; i < vendorProducts.length; i++) {
        var p = vendorProducts[i];
        var name = p.product_name || '';
        if (!name) {
          continue;
        }
        var label = name;
        if (p.category_name) {
          label += ' - ' + p.category_name;
        }
        var price = (p.price !== null && typeof p.price !== 'undefined') ? p.price : '';
        var priceAttr = price !== '' ? ' data-price="' + price + '"' : '';
        html += '<option value="' + name + '"' + priceAttr + '>' + label + '</option>';
        if (currentValue && name === currentValue) {
          found = true;
        }
      }
      if (currentValue && !found) {
        html += '<option value="' + currentValue + '" selected>' + currentValue + '</option>';
      }
      return html;
    }

    function refreshPartSelects() {
      $('.part_name_select').each(function() {
        var current = $(this).data('current') || $(this).val() || '';
        $(this).html(buildPartOptions(current));
        if (current) {
          $(this).val(current);
        }
        $(this).data('current', '');
      });
    }

    function loadVendorProducts(vendorId) {
      if (!vendorId) {
        vendorProducts = [];
        refreshPartSelects();
        return;
      }

      $.ajax({
        url: "<?php echo base_url(); ?>index.php/Purchase_order/get_vendor_products",
        type: "post",
        dataType: "json",
        data: { vendor_id: vendorId },
        success: function(response) {
          if (response && response.success) {
            vendorProducts = response.data || [];
          } else {
            vendorProducts = [];
          }
          refreshPartSelects();
        }
      });
    }

    function recalcRow(row) {
      var qty = parseFloat(row.find('.qty').val()) || 0;
      var rate = parseFloat(row.find('.rate').val()) || 0;
      var amount = qty * rate;
      row.find('.amount').val(amount.toFixed(2));
    }

    function recalcTotals() {
      var total = 0;
      $('#po_items_table .amount').each(function() {
        var val = parseFloat($(this).val()) || 0;
        total += val;
      });
      $('#total_amount').val(total.toFixed(2));

      var taxPercent = parseFloat($('#sales_tax_percent').val()) || 0;
      var taxAmount = (total * taxPercent) / 100;
      $('#sales_tax_amount').val(taxAmount.toFixed(2));

      var grand = total + taxAmount;
      $('#grand_total').val(grand.toFixed(2));
    }

    $('#add_row').on('click', function() {
      rowCount++;
      var row = '<tr id="row_' + rowCount + '">' +
        '<td>' + rowCount + '</td>' +
        '<td><select name="part_name[]" class="form-control part_name_select" data-current="" required><option value="">Select Part</option></select></td>' +
        '<td><input type="text" name="model[]" class="form-control"></td>' +
        '<td><input type="number" name="qty[]" class="form-control qty" min="0" step="0.01"></td>' +
        '<td><input type="text" name="unit[]" class="form-control"></td>' +
        '<td><input type="number" name="rate[]" class="form-control rate" min="0" step="0.01"></td>' +
        '<td><input type="number" name="amount[]" class="form-control amount" min="0" step="0.01" readonly></td>' +
        '<td><input type="text" name="item_remarks[]" class="form-control"></td>' +
        '<td><button type="button" class="btn btn-default remove_row" data-row="' + rowCount + '"><i class="fa fa-minus"></i></button></td>' +
      '</tr>';
      $('#po_items_table tbody').append(row);
      refreshPartSelects();
    });

    $('#po_items_table').on('click', '.remove_row', function() {
      if ($('#po_items_table tbody tr').length === 1) {
        return;
      }
      var rowId = $(this).data('row');
      $('#row_' + rowId).remove();
      recalcTotals();
    });

    $('#po_items_table').on('input', '.qty, .rate', function() {
      var row = $(this).closest('tr');
      recalcRow(row);
      recalcTotals();
    });

    $('#po_items_table').on('change', '.part_name_select', function() {
      var row = $(this).closest('tr');
      var price = $(this).find('option:selected').data('price');
      if (typeof price !== 'undefined') {
        row.find('.rate').val(price);
      } else {
        row.find('.rate').val('');
      }
      recalcRow(row);
      recalcTotals();
    });

    $('#sales_tax_percent').on('input', function() {
      recalcTotals();
    });

    $('#vendor_id').on('change', function() {
      var vendorId = $(this).val();
      loadVendorProducts(vendorId);
      if (!vendorId) {
        $('#contact_person').val('');
        $('#contact_no').val('');
        $('#remarks').val('');
        return;
      }

      $.ajax({
        url: "<?php echo base_url(); ?>index.php/Supplier/fetchSupplierDataById/" + vendorId,
        type: "get",
        dataType: "json",
        success: function(response) {
          var data = response && response.data ? response.data : null;
          var phones = response && response.phones ? response.phones : [];

          var contactName = '';
          if (data) {
            contactName = $.trim((data.first_name || '') + ' ' + (data.last_name || ''));
          }

          $('#contact_person').val(contactName);
          $('#contact_no').val(phones && phones.length ? phones[0] : '');
          $('#remarks').val(data && data.remarks ? data.remarks : '');
        }
      });
    });

    $('#mainPurchasingNav').addClass('menu-open');
    $('#purchaseOrderNav').addClass('active');

    var initialVendorId = $('#vendor_id').val();
    if (initialVendorId) {
      loadVendorProducts(initialVendorId);
    }

    recalcTotals();
  })();
</script>














<div class="content-wrapper">
  <section class="content-header">
    <h1>Purchase Order <small>Print</small></h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Purchase Order Print</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header">
            <button class="btn btn-primary pull-right" onclick="window.print()">
              <i class="fa fa-print"></i> Print
            </button>
          </div>
          <div class="box-body" id="printArea">
            <style>
              @media print {
                .box-header { display: none; }
                .content-header { display: none; }
                .content-wrapper { margin: 0; }
              }
            </style>
            <table style="width:100%; border-collapse:collapse;" border="1">
              <tr>
                <td colspan="8" style="text-align:center; font-weight:bold; padding:6px;">
                  TBM AUTOMOBILE (PVT) LTD.<br>
                  PURCHASE ORDER
                </td>
              </tr>
              <tr>
                <td colspan="4" style="padding:6px;">
                  <strong>Vendor / Supplier</strong><br>
                  <?php if(!empty($vendor)): ?>
                    <?php echo $vendor['first_name'].' '.$vendor['last_name']; ?>
                  <?php endif; ?>
                </td>
                <td colspan="4" style="padding:6px;">
                  <strong>Contact Person & Contact No</strong><br>
                  <?php echo $order['contact_person']; ?><br>
                  <?php echo $order['contact_no']; ?>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="padding:6px;"><strong>PO No</strong>: <?php echo $order['po_number']; ?></td>
                <td colspan="2" style="padding:6px;"><strong>Date</strong>: <?php echo $order['po_date']; ?></td>
                <td colspan="2" style="padding:6px;"><strong>Terms Of Payment</strong>: <?php echo $order['terms_of_payment']; ?></td>
                <td colspan="2" style="padding:6px;"><strong>Delivery</strong>: <?php echo $order['delivery']; ?></td>
              </tr>
              <tr style="background:#f5f5f5; font-weight:bold; text-align:center;">
                <td style="width:5%">S#</td>
                <td>Part's Name</td>
                <td>Model</td>
                <td style="width:8%">Qty</td>
                <td style="width:8%">Unit</td>
                <td style="width:10%">Rate</td>
                <td style="width:12%">Amount</td>
                <td>Remarks</td>
              </tr>
              <?php $i = 1; foreach($items as $item): ?>
                <tr>
                  <td style="text-align:center; padding:4px;"><?php echo $i++; ?></td>
                  <td style="padding:4px;"><?php echo $item['part_name']; ?></td>
                  <td style="padding:4px;"><?php echo $item['model']; ?></td>
                  <td style="text-align:right; padding:4px;"><?php echo $item['qty']; ?></td>
                  <td style="padding:4px;"><?php echo $item['unit']; ?></td>
                  <td style="text-align:right; padding:4px;"><?php echo number_format((float)$item['rate'], 2); ?></td>
                  <td style="text-align:right; padding:4px;"><?php echo number_format((float)$item['amount'], 2); ?></td>
                  <td style="padding:4px;"><?php echo $item['remarks']; ?></td>
                </tr>
              <?php endforeach; ?>
              <tr>
                <td colspan="6" style="text-align:right; padding:6px;"><strong>Total Amount</strong></td>
                <td colspan="2" style="text-align:right; padding:6px;"><?php echo number_format((float)$order['total_amount'], 2); ?></td>
              </tr>
              <tr>
                <td colspan="6" style="text-align:right; padding:6px;"><strong>Sales Tax (<?php echo $order['sales_tax_percent']; ?>%)</strong></td>
                <td colspan="2" style="text-align:right; padding:6px;"><?php echo number_format((float)$order['sales_tax_amount'], 2); ?></td>
              </tr>
              <tr>
                <td colspan="6" style="text-align:right; padding:6px;"><strong>Grand Total</strong></td>
                <td colspan="2" style="text-align:right; padding:6px;"><?php echo number_format((float)$order['grand_total'], 2); ?></td>
              </tr>
              <tr>
                <td colspan="8" style="padding:10px;">
                  <strong>Remarks:</strong> <?php echo $order['remarks']; ?>
                </td>
              </tr>
              <tr>
                <td colspan="8" style="padding:16px;">
                  <table style="width:100%;" border="0">
                    <tr>
                      <td style="width:25%; text-align:center;">Prepared By</td>
                      <td style="width:25%; text-align:center;">Recommended By</td>
                      <td style="width:25%; text-align:center;">Approved By</td>
                      <td style="width:25%; text-align:center;">Received By</td>
                    </tr>
                    <tr>
                      <td style="padding-top:40px;"></td>
                      <td style="padding-top:40px;"></td>
                      <td style="padding-top:40px;"></td>
                      <td style="padding-top:40px;"></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

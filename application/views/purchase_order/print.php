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
              .po-table { width:100%; border-collapse:collapse; font-size:12px; }
              .po-table td, .po-table th { border:1px solid #333; padding:6px; vertical-align:top; }
              .po-header { text-align:center; font-weight:bold; }
              .po-muted { color:#444; font-size:11px; }
              .po-title { font-size:14px; letter-spacing:0.5px; }
              .po-sub { font-size:12px; }
              .po-label { font-weight:bold; }
              .po-center { text-align:center; }
              .po-right { text-align:right; }
              .po-grey { background:#f3f3f3; font-weight:bold; }
              .po-tight td { padding:4px; }
              .po-sig td { height:60px; }
            </style>
            <table class="po-table">
              <tr>
                <td colspan="8" class="po-header">
                  <div class="po-title">TBM AUTOMOBILE (PVT) LTD.</div>
                  <div class="po-sub">PURCHASE ORDER</div>
                </td>
              </tr>
              <tr class="po-tight">
                <td colspan="4">
                  <span class="po-label">Vendor / Supplier</span><br>
                  <?php if(!empty($vendor)): ?>
                    <?php echo $vendor['first_name'].' '.$vendor['last_name']; ?><br>
                    <?php if (!empty($vendor['address'])): ?>
                      <span class="po-muted"><?php echo $vendor['address']; ?></span><br>
                    <?php endif; ?>
                    <?php if (!empty($vendor['city'])): ?>
                      <span class="po-muted"><?php echo $vendor['city']; ?></span>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
                <td colspan="2">
                  <span class="po-label">NTN</span><br>5118249
                </td>
                <td colspan="2">
                  <span class="po-label">STRN</span><br>
                    3277876175158
                </td>
              </tr>
              <tr class="po-tight">
                <td colspan="4">
                  <span class="po-label">Contact Person & Contact No</span><br>
                  <?php echo !empty($order['contact_person']) ? $order['contact_person'] : ''; ?><br>
                  <?php echo !empty($order['contact_no']) ? $order['contact_no'] : ''; ?>
                </td>
                <td colspan="2">
                  <span class="po-label">PO No</span><br>
                  <?php echo $order['po_number']; ?>
                </td>
                <td colspan="2">
                  <span class="po-label">Date</span><br>
                  <?php echo $order['po_date']; ?>
                </td>
              </tr>
              <tr class="po-tight">
                <td colspan="4">
                  <span class="po-label">Terms Of Payment</span><br>
                  <?php echo !empty($order['terms_of_payment']) ? $order['terms_of_payment'] : ''; ?>
                </td>
                <td colspan="4">
                  <span class="po-label">Delivery</span><br>
                  <?php echo !empty($order['delivery']) ? $order['delivery'] : ''; ?>
                </td>
              </tr>
              <tr class="po-grey po-center">
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
                  <td class="po-center" style="padding:4px;"><?php echo $i++; ?></td>
                  <td style="padding:4px;"><?php echo $item['part_name']; ?></td>
                  <td style="padding:4px;"><?php echo $item['model']; ?></td>
                  <td class="po-right" style="padding:4px;"><?php echo $item['qty']; ?></td>
                  <td style="padding:4px;"><?php echo $item['unit']; ?></td>
                  <td class="po-right" style="padding:4px;"><?php echo number_format((float)$item['rate'], 2); ?></td>
                  <td class="po-right" style="padding:4px;"><?php echo number_format((float)$item['amount'], 2); ?></td>
                  <td style="padding:4px;"><?php echo $item['remarks']; ?></td>
                </tr>
              <?php endforeach; ?>
              <tr>
                <td colspan="6" class="po-right"><strong>Total Amount</strong></td>
                <td colspan="2" class="po-right"><?php echo number_format((float)$order['total_amount'], 2); ?></td>
              </tr>
              <tr>
                <td colspan="6" class="po-right"><strong>Sales Tax (<?php echo $order['sales_tax_percent']; ?>%)</strong></td>
                <td colspan="2" class="po-right"><?php echo number_format((float)$order['sales_tax_amount'], 2); ?></td>
              </tr>
              <tr>
                <td colspan="6" class="po-right"><strong>Grand Total</strong></td>
                <td colspan="2" class="po-right"><?php echo number_format((float)$order['grand_total'], 2); ?></td>
              </tr>
              <tr>
                <td colspan="8" style="padding:10px;">
                  <strong>Remarks:</strong> <?php echo !empty($order['remarks']) ? $order['remarks'] : ''; ?>
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
                    <tr class="po-sig">
                      <td style="text-align:center"><?php echo !empty($prepared_by) ? $prepared_by : ""; ?></td>
                      <td></td>
                      <td></td>
                      <td></td>
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

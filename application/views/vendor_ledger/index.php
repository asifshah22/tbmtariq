<?php
  date_default_timezone_set("Asia/Karachi");
  $range_to = date('m/d/Y');
  $range_from = date('m/d/Y', strtotime('01/01/2019'));
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Vendors
      <small><?php echo $heading; ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?php echo $heading; ?></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">
        <?php if(in_array('printVendorLedger', $user_permission)): ?>
          <a href="<?php echo base_url() ?>index.php/Vendor_ledger/print_vendor_ledger<?php if(isset($_GET['selected_vendor'])){ echo "?selected_vendor=".$_GET['selected_vendor']; } if(isset($_GET['date_range'])){ echo "&date_range=".$_GET['date_range']; } ?>" target="__blank" class="btn btn-success btn-sm btn-flat" id="print"><span class="glyphicon glyphicon-print"></span> Print</a>
          <br /> <br />
        <?php endif; ?>
        
        <div class="box">
          <div class="box-header">
            <h3 class="box-title"><?php echo $heading; ?></h3>
            <div class="pull-right">
              <form method="GET" class="form-inline" id="vendorsLedgerForm">
                <div class="form-group">
                  <select required class="form-control" id="selected_vendor" name="selected_vendor">
                    <option value=""> - Select Vendor ... - </option>
                    <?php $vendors_data = $this->Model_supplier->getSupplierData(); ?>
                    <?php foreach($vendors_data as $key => $value): ?>
                      <?php 
                        $selected = "";
                        if(isset($_GET['selected_vendor'])){
                          if($_GET['selected_vendor'] == $value['id']){
                            $selected = "selected";
                          }
                          else{
                            $selected = "";
                          }
                        }
                      ?>
                      <option <?php if(!empty($selected)){echo $selected;} ?> value="<?php echo $value['id'] ?>"><?php echo $value['first_name'].' '.$value['last_name']; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range" value="<?php echo (isset($_GET['date_range'])) ? $_GET['date_range'] : $range_from.' - '.$range_to; ?>">
                </div>
                <button title="Select Vednor and Dates to view the Vednor Ledger between selected dates" type="submit" class="btn btn-primary btn-sm btn-flat" id="view-ledger"> View Ledger</button>
              </form>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="example1" class="table table-bordered">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Vendor</th>
                  <th>Products & Payments</th>
                  <th></th>
                  <th>Delivary Date</th>
                  <?php if( (in_array('viewPurchasing', $user_permission)) || (in_array('updatePurchasing', $user_permission)) ): ?>
                    <th></th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody bgcolor="#eaeaea">
                <!-- Show Added Payments Even Befor the Purchasing -->
                <?php
                  $vendor_payments_data = $this->Model_supplier->fetchSupplierPaymentsData($vendor_id, $most_recent_order_id = 0);
                  $vendor_payments_array = array();

                  if(!empty($vendor_payments_data)){
                    foreach ($vendor_payments_data as $key => $value) {
                      $date = date('d-m-Y', strtotime($value['datetime_creation']));
                      if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date)){
                        array_push($vendor_payments_array, $value);
                      }
                    }
                  }
                  $vendor_data = $this->Model_supplier->getSupplierData($vendor_id);
                ?>
                <?php
                  $firstIteration = true; 
                  $opening_balance = 0;
                  $closing_balance = 0;
                  //print_r($result);
                  $counter = 1;
                ?>
                <?php if(!empty($vendor_payments_array)): ?>
                  <tr>
                    <td>#</td>
                    <td style="text-transform: capitalize;"><?php echo $vendor_data['first_name']. ' '. $vendor_data['last_name']; ?></td>
                    <td>
                      <table class="table table-bordered example3">
                        <thead>
                          <tr>
                            <th>Payment Method</th>
                            <th>Paid</th>
                            <th>Payment Note</th>
                            <th>DateTime</th>
                            <th>Paid By</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $total_amount_paid = 0;
                            $opening_balance = $vendor_data['starting_balance'];
                          ?>
                           <?php foreach($vendor_payments_array as $key => $value): ?>
                            <?php
                              $date = date('d-m-Y', strtotime($value['datetime_creation']));
                              $time = date('h:i a', strtotime($value['datetime_creation']));
                              $date_time = $date . ' ' . $time;
                              $paid_by = '';
                              if($value['paid_by'] == 1)
                              {
                                $paid_by = "TBM";
                               }
                              elseif($value['paid_by'] == 2)
                              {
                                $paid_by = "Vendor";
                              }
                              $total_amount_paid += $value['paid_amount'];
                            ?>
                            <tr>
                              <td><?php echo $value['payment_method']; ?></td>
                              <td><?php echo floatval($value['paid_amount']); ?></td>
                              <td style="white-space:pre-wrap; word-wrap:break-word"><?php echo $value['payment_note']; ?></td>
                              <td><?php echo $date_time; ?></td>
                              <td><?php echo $paid_by; ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                      <span style="background-color: #ffffff"><strong>Total Amount Paid: <?php echo floatval($total_amount_paid); ?></strong></span>
                    </td>
                    <td width="25%" bgcolor="#ffffff">
                      <div class="row">
                        <div class="col-xs-6">
                          <p><strong>Opening Balance:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval($opening_balance); ?></strong></p>
                        </div>
                       </div>
                      <div class="row">
                        <div class="col-xs-6">
                           <p><strong>Total Paid:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval($total_amount_paid); ?></strong></p>
                        </div>
                      </div>

                      <?php 
                        if($value['paid_by'] == 1){
                          $closing_balance = $opening_balance - $total_amount_paid;
                        }
                        elseif($value['paid_by'] == 2){
                          $closing_balance = $opening_balance + $total_amount_paid;
                        }
                      ?>
                      <div class="row">
                        <div class="col-xs-6">
                           <p><strong>Closing Balance:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo number_format($closing_balance, 2); ?></strong></p>
                        </div>
                      </div>
                      <?php $opening_balance = $closing_balance; ?>
                    </td>
                    <td></td>
                    <td></td>
                  </tr>
                <?php endif; ?>

                <!-- Now Show the Orders -->

                <?php foreach($result as $key => $value): ?>
                  <?php
                    $purchase_order_data = $this->Model_products->getPurchaseOrdersData($value);
                    $purchase_items_data = $this->Model_products->getPurchaseItemsData($value);
                    $purchase_return_data = $this->Model_products->fetchPurchaseReturnsData($value);
                    $vendor_ob_payment_data = $this->Model_products->fetchVendorOBPaymentData($value);
                    $loan_deductions = $this->Model_loan->getLoanDeductions($value);
                    $vendor_id = $purchase_items_data[0]['vendor_id'];
                    $vendor_data = $this->Model_supplier->getSupplierData($vendor_id);
                    $purchase_return_amount = 0;
                    foreach ($purchase_return_data as $key => $value) {
                        $purchase_return_amount += $value['amount'];
                    }
                    if($firstIteration)
                    {
                      $firstIteration = false;
                      $opening_balance = $purchase_order_data['opening_balance'];
                    }  
                  ?>

                  <tr>
                    <td><?php echo "{$purchase_order_data['bill_no']}"." &#8212 "."{$purchase_order_data['id']}";  ?></td>
                    <td style="text-transform: capitalize;"><?php echo $vendor_data['first_name']. ' '. $vendor_data['last_name']; ?></td>
                    <td>
                      <table class="table table-bordered example2">
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Returns(P.P)</th>
                            <th>Rate</th>
                            <th>Total</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                            $total_retun_amount = 0;
                            $total_products_amount = 0;
                            $total_qty_delivered = 0; 
                          ?>
                          <?php 
                            foreach($purchase_items_data as $k => $v)
                            {
                              $product_data = $this->Model_products->getAllProductData($v['product_id']);
                              $category_data = $this->Model_products->getAllProductCategoryData($v['product_id'], $v['category_id']);
                              
                              $unit_name = '';
                              $unit_value = 1;
                              $units_data = $this->Model_products->getAllUnitsData();
                              foreach ($units_data as $unit)
                              {
                                $unit_id = $v['unit_id'];
                                $unit_data_values = $this->Model_products->getUnitValuesData();
                                if($unit['id'] == $unit_id)
                                {
                                  foreach ($unit_data_values as $key_2 => $value_2)
                                  {
                                    if($unit['id'] == $value_2['unit_id'])
                                    {
                                      $unit_name = $unit['unit_name'];
                                      $unit_value = $value_2['unit_value'];
                                      break;
                                    }
                                  }
                                }
                              }
                              $item_name = '';
                              if(!empty($category_data) && $category_data['category_name'])
                              {
                                $item_name = $product_data['name']. ' - ' .$category_data['category_name'];
                              }
                              else
                              {
                                $item_name = $product_data['name'];
                              }
                              $return_qty = 0;
                              foreach ($purchase_return_data as $returnKey => $returnValue) {
                                if($returnValue['product_id'] == $v['product_id'] && $returnValue['unit_id'] == $v['unit_id']){
                                  $return_qty = $returnValue['qty'];
                                  $total_retun_amount += $returnValue['amount'];
                                  break;
                                }
                                else{
                                  $return_qty = 0;
                                  $total_retun_amount = 0;
                                }
                              }
                              $total_products_amount += ($v['qty'] * $v['product_price']) - $total_retun_amount;
                              $total_qty_delivered += $v['qty'] - $return_qty;
                              $date = date('d-m-Y', strtotime($purchase_order_data['datetime_created']));
                              $time = date('h:i a', strtotime($purchase_order_data['datetime_created']));
                              $date_time = $date . ' ' . $time;
                              echo "<tr><td>".$item_name."<td>".$unit_name."</td><td>".floatval($v['qty'])."</td><td>".floatval($return_qty)."</td><td>".floatval($v['product_price'])."</td><td>".floatval( ($v['qty'] * $v['product_price']) - ($total_retun_amount) )."</td></tr>"; 
                            }
                          ?>
                        </tbody>
                      </table>
                      <p>
                        <span style="background-color: #ffffff">
                          <strong>Total Qunatity Delivered: <?php echo floatval($total_qty_delivered); ?></strong>
                        </span>
                      </p>
                      <p>
                        <span style="background-color: #ffffff">
                          <strong>Total Products Amount: <?php echo floatval($total_products_amount); ?></strong>
                        </span>
                      </p>

                      <?php
                        $total_amount_paid = 0;
                        $temp = '!@#$';
                        $data = $purchase_order_data;
                        $payment_method_array = explode($temp, $data['payment_method']);
                        $payment_date_array = explode($temp, $data['payment_date']);
                        $paid_array = explode($temp, $data['paid']);
                        $payment_note_array = explode($temp, $data['payment_note']);
                        $i = 0;
                        $x = 1;
                      ?>
                      <?php if(!empty($payment_method_array[0])): ?>
                        <table class="table table-bordered example3">
                          <thead>
                            <tr>
                              <th>Payment Method</th>
                              <th>Paid</th>
                              <th>Payment Note</th>
                              <th>Date</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach($payment_method_array as $payment_key => $payment_value): ?>
                              <?php
                                
                                $payment_method = $payment_method_array[$i];      
                                $paid_amount = $paid_array[$i];
                                $payment_note = $payment_note_array[$i];
                                $payment_date = $payment_date_array[$i];
                                $total_amount_paid += $paid_amount;
                                $i++;
                                
                              ?>
                              <tr>
                                <td><?php echo $payment_method; ?></td>
                                <td><?php echo floatval($paid_amount); ?></td>
                                <td style="white-space:pre-wrap; word-wrap:break-word"><?php echo $payment_note; ?></td>
                                <td><?php echo $payment_date; ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                        <span style="background-color: #ffffff"><strong>Total Amount Paid: <?php echo floatval($total_amount_paid); ?></strong></span>  
                      <?php endif; ?>  
                    </td>
                    <?php
                      $loan_deduction = 0;
                      if(!empty($loan_deductions)){
                        $loan_deduction = $loan_deductions['deduction_amount'];
                      }
                    ?>
                    <td width="25%" bgcolor="#ffffff">
                      <div class="row">
                        <div class="col-xs-6">
                          <p><strong>Opening Balance:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval($opening_balance); ?></strong></p>
                        </div>
                      </div>  
                      <div class="row">
                        <div class="col-xs-6">
                          <p><strong>Gross Amount:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval($purchase_order_data['gross_amount'] - $purchase_return_amount); ?></strong></p>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-xs-6">
                          <p><strong>Loan Deduction:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval($loan_deduction); ?></strong></p>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-xs-6">
                          <p><strong>Discount:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval($purchase_order_data['discount']); ?></strong></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-6">
                          <p><strong>Freight Charges:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval($purchase_order_data['loading_or_affair']); ?></strong></p>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-xs-6">
                          <p><strong>Net Amount:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval(($purchase_order_data['net_amount'] - $purchase_return_amount)); ?></strong></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-6">
                          <p><strong>Total:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval(($purchase_order_data['net_amount'] - $purchase_return_amount) + $opening_balance); ?></strong></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-6">
                          <p><strong>Total Paid:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval($total_amount_paid); ?></strong></p>
                        </div>
                      </div>
                      <?php 
                        $closing_balance = $opening_balance + $purchase_order_data['net_amount'] - $purchase_return_amount - $purchase_order_data['total_paid'];
                      ?>
                      <div class="row">
                        <div class="col-xs-6">
                          <p><strong>Closing Balance:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo number_format($closing_balance, 2); ?></strong></p>
                        </div>
                      </div>
                      <?php $opening_balance = $closing_balance; ?>
                    </td>
                    <td><?php echo $date_time; ?></td>
                    <?php if( (in_array('viewPurchasing', $user_permission)) || (in_array('updatePurchasing', $user_permission)) ): ?>
                      <td>
                        <?php if( (in_array('viewPurchasing', $user_permission)) ): ?>
                          <a href="<?php echo base_url() ?>index.php/Product/view_order_items/<?php echo $purchase_order_data['id'] ?>"><i class="fa fa-eye"></i>
                          </a>
                        <?php endif; ?>
                        <?php if( (in_array('updatePurchasing', $user_permission)) ): ?>
                          <a href="<?php echo base_url() ?>index.php/Product/update_purchase_order/<?php echo $purchase_order_data['id'] ?>"><i class="fa fa-edit"></i>
                          </a>
                        <?php endif; ?>
                        
                      </td>
                    <?php endif; ?>
                  </tr>
                  <?php if(!empty($vendor_ob_payment_data)): ?>
                    <tr>
                      <td>#</td>
                      <td style="text-transform: capitalize;"><?php echo $vendor_data['first_name']. ' '. $vendor_data['last_name']; ?></td>
                      <td>
                        <table class="table table-bordered example3">
                          <thead>
                            <tr>
                              <th>Payment Method</th>
                              <th>Paid</th>
                              <th>Payment Note</th>
                              <th>DateTime</th>
                              <th>Paid By</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $total_amount_paid = 0;
                            ?>
                            <?php foreach($vendor_ob_payment_data as $ob_payment_key => $ob_payment_value): ?>
                              <?php
                                $date = date('d-m-Y', strtotime($ob_payment_value['datetime_creation']));
                                $time = date('h:i a', strtotime($ob_payment_value['datetime_creation']));
                                $date_time = $date . ' ' . $time;
                                $payment_method = $ob_payment_value['payment_method'];
                                $paid_by = '';
                                if($ob_payment_value['paid_by'] == 1)
                                {
                                  $paid_by = "TBM";
                                }
                                elseif($ob_payment_value['paid_by'] == 2)
                                {
                                  $paid_by = "Vendor";
                                }
                                $total_amount_paid += $ob_payment_value['paid_amount'];
                              ?>
                              <tr>
                                <td><?php echo $payment_method; ?></td>
                                <td><?php echo floatval($ob_payment_value['paid_amount']); ?></td>
                                <td style="white-space:pre-wrap; word-wrap:break-word"><?php echo $ob_payment_value['payment_note']; ?></td>
                                <td><?php echo $date_time; ?></td>
                                <td><?php echo $paid_by; ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                        <span style="background-color: #ffffff"><strong>Total Amount Paid: <?php echo floatval($total_amount_paid); ?></strong></span>
                      </td>

                      <td width="25%" bgcolor="#ffffff">
                        <div class="row">
                          <div class="col-xs-6">
                            <p><strong>Opening Balance:</strong></p>
                          </div>
                          <div class="col-xs-6 text-right">
                            <p><strong><?php echo floatval($opening_balance); ?></strong></p>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-xs-6">
                            <p><strong>Total Paid:</strong></p>
                          </div>
                          <div class="col-xs-6 text-right">
                            <p><strong><?php echo floatval($total_amount_paid); ?></strong></p>
                          </div>
                        </div>
                        <?php
                          if($ob_payment_value['paid_by'] == 1){
                            $closing_balance = $opening_balance - $total_amount_paid;
                          }
                          elseif($ob_payment_value['paid_by'] == 2){
                            $closing_balance = $opening_balance + $total_amount_paid;
                          }
                        ?>
                        <div class="row">
                          <div class="col-xs-6">
                            <p><strong>Closing Balance:</strong></p>
                          </div>
                          <div class="col-xs-6 text-right">
                            <p><strong><?php echo number_format($closing_balance, 2); ?></strong></p>
                          </div>
                        </div>
                        <?php $opening_balance = $closing_balance; ?>
                      </td>
                      <td></td>
                      <td></td>
                    </tr>
                  <?php endif; ?>
                <?php endforeach; ?>
              </tbody>
            </table>  
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- col-md-12 -->
    </div>
    <!-- /.row -->
    

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->



<script type="text/javascript">
var manageTable;
var base_url = "<?php echo base_url(); ?>";

$(document).ready(function() {
  $("select").select2();
  $("#mainVendorLedgerNav").addClass('active');
  $("#mainVendorLedgerNav").addClass('menu-open');
  $("#vendorsLedgerNav").addClass('active');

});


</script>
<script>
  $(function () {
    $('#example1').DataTable({
      responsive: true,
      "ordering": false,
      "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
    });
    $('.example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : false
    });
    $('.example3').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : false,
      'info'        : false,
      'autoWidth'   : false
    });
  });
</script>
<script>
$(function(){
  //Date picker
  $('#datepicker_add').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  })
  $('#datepicker_edit').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  })

  //Timepicker
  $('.timepicker').timepicker({
    showInputs: false
  })

  //Date range picker
  $('#reservation').daterangepicker()
  //Date range picker with time picker
  $('#reservationtime').daterangepicker({ timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A' })
  //Date range as a button
  $('#daterange-btn').daterangepicker(
    {
      ranges   : {
        'Today'       : [moment(), moment()],
        'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month'  : [moment().startOf('month'), moment().endOf('month')],
        'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: moment().subtract(29, 'days'),
      endDate  : moment()
    },
    function (start, end) {
      $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
    }
  )
  
});
</script>
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
      Reports
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
        <div class="box">
          <div class="box-header">
            <h3 class="box-title"><?php echo $heading; ?></h3>
            <div class="pull-right">
              <form method="POST" class="form-inline" id="adjustProductRateForm">
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range" value="<?php echo (isset($_GET['range'])) ? $_GET['range'] : $range_from.' - '.$range_to; ?>">
                </div>
                <?php if(in_array('printPurchasingDetails', $user_permission)): ?>
                  <a href="<?php echo base_url() ?>index.php/Reports/print_purchased_orders_details<?php if(isset($_GET['range'])){ echo "?range=".$_GET['range']; } ?>" target="__blank" class="btn btn-success btn-sm btn-flat" id="print"><span class="glyphicon glyphicon-print"></span> Print</a>
                <?php endif; ?>
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
                <?php
                  $counter = 1;
                  // Descing Order
                  $result = array_reverse($result);
                ?>
                <?php foreach($result as $key => $value): ?>
                  <?php
                    $purchase_order_data = $this->Model_products->getPurchaseOrdersData($value);
                    $purchase_items_data = $this->Model_products->getPurchaseItemsData($value);
                    $purchase_return_data = $this->Model_products->fetchPurchaseReturnsData($value);
                    $loan_deductions = $this->Model_loan->getLoanDeductions($value);
                    $vendor_id = $purchase_items_data[0]['vendor_id'];
                    $vendor_data = $this->Model_products->getSupplierData($vendor_id);
                    $purchase_return_amount = 0;
                    foreach ($purchase_return_data as $key => $value) {
                        $purchase_return_amount += $value['amount'];
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
                            <th>Returns</th>
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
                          <p><strong>Products Total:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval($total_products_amount); ?></strong></p>
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
                          <p><strong>Total Paid:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval($total_amount_paid); ?></strong></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-6">
                          <p><strong>Remaining:</strong></p>
                        </div>
                        <div class="col-xs-6 text-right">
                          <p><strong><?php echo floatval(floatval(($purchase_order_data['net_amount'] - $purchase_return_amount)) - floatval($total_amount_paid)); ?></strong></p>
                        </div>
                      </div>
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

  $("#mainReportsNav").addClass('active');
  $("#mainReportsNav").addClass('menu-open');
  $("#purchasedOrdersDetailNav").addClass('active');

});


</script>
<script>
  $(function () {
    $('#example1').DataTable({
      responsive: true,
      'ordering'    : false,
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
<script>
$(function(){
  
  $("#reservation").on('change', function(){
    var range = encodeURI($(this).val());
    window.location = '<?php echo base_url() ?>index.php/Reports/purchased_orders_details?range='+range;
  });

});
</script>
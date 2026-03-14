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
      Report
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
                <?php if(in_array('printSaleDetailsEmp', $user_permission)): ?>
                  <a href="<?php echo base_url() ?>index.php/Reports/print_company_sale_orders_detail<?php if(isset($_GET['range'])){ echo "?range=".$_GET['range']; } ?>" target="__blank" class="btn btn-success btn-sm btn-flat" id="print"><span class="glyphicon glyphicon-print"></span> Print</a>
                <?php endif; ?>
              </form>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="example1" class="table table-bordered">
              <thead>
                <tr>
                  <th width="3%">#</th>
                  <th width="57%">Order</th>
                  <th width="40%">Order Items</th>
                  <th width="40%"></th>
              </tr>
              </thead>
              <tbody bgcolor="#eaeaea">
                <?php
                  $counter = 1;
                ?>
                <?php foreach ($result as $key => $value): ?>
                  <tr>
                    <?php
                      $sale_order_data = $this->Model_products->getCompanySaleOrdersData($value);
                      $sale_items_data = $this->Model_products->getCompanySaleItemsData($value);
                      $customer_data = $this->Model_Customers->getCustomerData($sale_order_data['customer_id']);
                      $department_data = $this->Model_department->getDepartmentData($sale_order_data['department_id']);
                      $count_total_item = $this->Model_products->countCompanySaleOrderItem($sale_order_data['id']);
                      $stock_type = '';
                      if ($sale_order_data['stock_type'] == 1) {
                        $stock_type = 'Factory Stock';
                      }
                      else if ($sale_order_data['stock_type'] == 2) {
                        $stock_type = 'Office Stock';
                      }
                      
                      $date = date('d-m-Y', strtotime($sale_order_data['date_time']));
                      $time = date('h:i a', strtotime($sale_order_data['date_time']));

                      $date_time = $date . ' ' . $time;
                    ?>
                    <td><?php echo $counter++; ?></td>
                    <td>
                      <table class="table table-bordered table-striped example2">
                        <thead>
                          <tr>
                            <th>Bill_no</th>
                            <th>DateTime</th>
                            <th>Customer</th>
                            <th>Department</th>
                            <th>Stock</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td><?php echo $sale_order_data['bill_no']; ?></td>
                            <td><?php echo $date_time; ?></td>
                            <td><?php echo $customer_data['full_name']; ?></td>
                            <td><?php echo $department_data['department_name']; ?></td>
                            <td><?php echo $stock_type; ?></td>
                            
                          </tr>
                          
                        </tbody>

                      </table>
                    </td>
                    <td>
                      <table class="table table-bordered table-striped example3">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Item</th>
                            <th>Unit</th>
                            <th>Qty</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $count = 1; ?>
                          <?php foreach($sale_items_data as $key => $value): ?>
                            <?php
                              $product_data = $this->Model_products->getAllProductData($value['product_id']);
                              $product_name = $product_data['name'];
                              $category_name = '';
                              if($value['category_id'])
                              {
                                $category_name = $this->Model_category->getAllCategoryData($value['category_id'])['name'];
                              }
                              else
                              {
                                $category_name = 'Nill';
                              }
                              $unit_value = 1;
                              $unit_data_values = $this->Model_products->fetchUnitValueData($value['unit_id']);
                              if(!empty($unit_data_values)){
                                $unit_value = $unit_data_values['unit_value'];
                              }
                              
                            ?>
                            <tr>
                              <td><?php echo $count++ ?></td>
                              <td><?php echo $category_name; ?></td>
                              <td><?php echo $product_name; ?></td>
                              <td>
                                <?php
                                $unit_name = "Not Mentioned";
                                if($value['unit_id'] != 0){
                                  $unit_data = $this->Model_products->getAllUnitsData($value['unit_id']);
                                  $unit_name = $unit_data['unit_name'];
                                }
                                echo $unit_name;
                                ?>
                              </td>
                              <td><?php echo floatval($value['qty']/$unit_value); ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>

                      </table>
                    </td>
                    <?php if( (in_array('viewSaleOrderE', $user_permission)) || (in_array('updateSaleOrderE', $user_permission)) ): ?>
                      <td>
                        <?php if( (in_array('viewSaleOrderE', $user_permission)) ): ?>
                          <a href="<?php echo base_url() ?>index.php/Product/view_company_sale_order_items/<?php echo $sale_order_data['id'] ?>"><i class="fa fa-eye"></i>
                          </a>
                        <?php endif; ?>
                        <?php if( (in_array('updateSaleOrderE', $user_permission)) ): ?>
                          <a href="<?php echo base_url() ?>index.php/Product/update_company_sale_order/<?php echo $sale_order_data['id'] ?>"><i class="fa fa-edit"></i>
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
  $("#companySaleOrdersDetailNav").addClass('active');

});


</script>
<script>
  $(function () {
    $('#example1').DataTable({
      responsive: true,
      "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
    })
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
    $('.example3').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });
  })
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
    window.location = '<?php echo base_url() ?>index.php/Reports/company_sale_orders_detail?range='+range;
  });

});
</script>
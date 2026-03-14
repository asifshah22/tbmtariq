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
              <form method="POST" class="form-inline" id="payForm">
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range" value="<?php echo (isset($_GET['range'])) ? $_GET['range'] : $range_from.' - '.$range_to; ?>">
                </div>
                <?php if(in_array('printReports', $user_permission)): ?>
                  <a href="<?php echo base_url() ?>index.php/Reports/print_sold_items_1<?php if(isset($_GET['range'])){ echo "?range=".$_GET['range']; } ?>" target="__blank" class="btn btn-success btn-sm btn-flat" id="print"><span class="glyphicon glyphicon-print"></span> Print</a>
                <?php endif; ?>
              </form>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>#</th>
                <th>Bill_no</th>
                <th>Customer Name</th>
                <th>Category Name</th>
                <th>Item Name</th>
                <th>Stock Type</th>
                <th>Qty</th>
              </tr>
              </thead>
              <tbody>
                <?php 
                  $counter = 1;
                  $sum_total_items = 0;
                 ?>
                <?php foreach ($result as $key => $value): ?>
                  <?php
                    $unit_value = 1;
                    if($value['unit_id'] != 0){
                      $unit_data_values = $this->Model_products->fetchUnitValueData($value['unit_id']);
                      $unit_value = $unit_data_values['unit_value'];
                    }
                    
                    $category_name = '';
                    if($value['category_id'])
                    {
                      $category_name = $this->Model_category->getAllCategoryData($value['category_id'])['name'];
                    }
                    else
                    {
                      $category_name = 'Nill';
                    }
                    $product_data = $this->Model_products->getAllProductData($value['product_id']);
                    $item_name = $product_data['name'];
                    $stock_type = '';
                    if ($value['stock_type'] == 1) {
                      $stock_type = 'Factory Stock';
                    }
                    else if ($value['stock_type'] == 2) {
                      $stock_type = 'Office Stock';
                    }

                  ?>
                  <tr>
                    <td><?php echo $counter++; ?></td>
                    <td><?php echo explode("-", $value['bill_no'])[1]; ?></td>
                    <td><?php echo $value['customer_name']; ?></td>
                    <td><?php echo $category_name; ?></td>    
                    <td><?php echo $item_name; ?></td>    
                    <td><?php echo $stock_type; ?></td>
                    
                    <td><?php echo floatval($value['qty'] * $unit_value); ?></td>
                  </tr>
                  <?php $sum_total_items += $value['qty'] * $unit_value ?>
                  
                <?php endforeach; ?>
              </tbody>

            </table>
            
            <span style="font-weight: bold;">Total Items: </span>
            <span><?php echo floatval($sum_total_items) ?></span>
            
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
  $("#itemsSoldNav-1").addClass('active');

});


</script>
<script>
  $(function () {
    $('#example1').DataTable({
      responsive: true
    })
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
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

$(function(){
  
  $("#reservation").on('change', function(){
    var range = encodeURI($(this).val());
    window.location = '<?php echo base_url() ?>index.php/Reports/sold_items_1?range='+range;
  });
  // $('#print').click(function(e){
  //   e.preventDefault();
  //   var range = encodeURI($(this).val());
  //   $('#payForm').attr('action', '<?php echo base_url() ?>index.php/Reports/print_sold_items_1');
  //   $('#payForm').submit();
  // });

});

</script>
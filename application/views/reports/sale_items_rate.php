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
                <?php if(in_array('printSaleItemsRate', $user_permission)): ?>
                  <a href="<?php echo base_url() ?>index.php/Reports/print_sale_items_rate<?php if(isset($_GET['range'])){ echo "?range=".$_GET['range']; } ?>" target="__blank" class="btn btn-success btn-sm btn-flat" id="print"><span class="glyphicon glyphicon-print"></span> Print</a>
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
                  <th>DateTime</th>
                  <th>Category Name</th>
                  <th>Item Name</th>
                  <th>Unit</th>
                  <th>Rate</th>
              </tr>
              </thead>
              <tbody>
                
                <?php foreach ($result['data'] as $key => $value): ?>
                  <?php $counter = 0; ?>
                  <tr>
                    <td>
                      <?php print_r($value[$counter]); ?>
                      <?php $counter++; ?>
                    </td>
                    <td>
                      <?php print_r($value[$counter]); ?>
                      <?php $counter++; ?>
                    </td>
                    <td>
                      <?php print_r($value[$counter]); ?>
                      <?php $counter++; ?>
                    </td>
                    <td>
                      <?php print_r($value[$counter]); ?>
                      <?php $counter++; ?>
                    </td>
                    <td>
                      <?php print_r($value[$counter]); ?>
                      <?php $counter++; ?>
                    </td>
                    <td>
                      <?php print_r(floatval($value[$counter])); ?>
                    </td>  
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
  $("#saleItemsRateNav").addClass('active');

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
    window.location = '<?php echo base_url() ?>index.php/Reports/sale_items_rate?range='+range;
  });

});
</script>
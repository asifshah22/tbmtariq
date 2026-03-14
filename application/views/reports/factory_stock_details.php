<?php
  date_default_timezone_set("Asia/Karachi");
  $range_to = date('m/d/Y');
  $range_from = date('m/d/Y', strtotime('01/01/2019'));
  $date_time = date('m/d/Y', strtotime('01/01/2019'));

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
                  <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range" value="<?php echo isset($_GET['date_time']) ? htmlspecialchars($_GET['date_time']) : ''; ?>">
                </div>
                <?php if(in_array('printVendorItemsRate', $user_permission)): ?>
                  <a href="<?php echo base_url() ?>index.php/Reports/factory_stock_details<?php if(isset($_GET['date_time'])){ echo "?date_time=".$_GET['date_time']; } else if(isset($_GET['selected_vendor'])){ echo "?selected_vendor=".$_GET['selected_vendor']; } ?>" target="__blank" class="btn btn-success btn-sm btn-flat" id="print"><span class="glyphicon glyphicon-print"></span> Print</a>
                <?php endif; ?>
              </form>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
              <thead>
                <tr>
                <th width="8%">#</th>
                <th width="23%" style="color:#3c8dbc">Category</th>
                <th width="23%" style="color:#3c8dbc">Item</th>
                <th width="23%" style="color:#3c8dbc">Unit</th>
                <!-- <th width="23%" style="color:#3c8dbc">Date</th> -->
                <th width="23%" style="color:#3c8dbc">
                  Quantity &nbsp;<a href="#" data-toggle="tooltip" title="Quantity without units are shown with other!"><i class="fa fa-question-circle"></i></a>
                </th>
              </tr>
              </thead>
              <tbody>
            <?php foreach ($result['data'] as $key => $value): ?>
              <tr <?php echo ($value[5] == 1) ? 'style="background-color: #ffcccc;"' : ''; ?>>
                    <td><?php echo $key + 1; ?></td>
                    <td><?php echo $value[1]; ?></td>
                    <td><?php echo $value[2]; ?></td>
                    <td><?php echo $value[3]; ?></td>
                    <!-- <td><?php //echo $value[4]; ?></td> -->
                    <td><?php echo $value[4]; ?></td>
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
  $("#itemsRateNav").addClass('active');

  $("select").select2();

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
  //Date range picker
  $('#reservation').datepicker()

});

$(function(){
    $('#reservation').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true, // Optional: to show month and year dropdowns
        minYear: 2000, // Set the minimum year
        maxYear: 2040, // Set the maximum year
        locale: {
            format: 'YYYY-MM-DD' // Set the date format as MM/DD/YYYY
        }
    });

    $('#reservation').on('apply.daterangepicker', function(ev, picker) {
        var formattedDate = picker.startDate.format('YYYY-MM-DD'); // Format the date as YYYY-MM-DD
        //alert(formattedDate);
        //window.location = '<?php //echo base_url() ?>index.php/Product/manage_stock?date_time=' + formattedDate;
        window.location = '<?php echo base_url() ?>index.php/Reports/factory_stock_details?date_time='+formattedDate;

    });

});

</script>
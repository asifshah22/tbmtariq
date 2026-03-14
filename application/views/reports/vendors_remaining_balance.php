
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
        <?php if(in_array('printVendorRemainingBalance', $user_permission)): ?>  
          <a title="Print Vendors Remaining Balance" target="__blank" href="<?php base_url() ?>print_vendors_remaining_balance" class="btn btn-success btn-sm btn-flat" id="print">
            <span class="glyphicon glyphicon-print"></span>
            Print
          </a>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title"><?php echo $heading; ?></h3>
            
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <!-- table table-bordered table-striped -->
            <table id="example1" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Sr.</th>
                  <th>Vendor Name</th>
                  <th>Credit</th>
                  <th>Debit</th>
              </tr>
              </thead>
              <tbody>
                <?php $total_credit = 0;$total_debit = 0; ?>
                <?php foreach($result as $key => $value): ?>
                  <tr>
                    <td><?php print_r($value[0]); ?></td>
                    <td><?php print_r($value[1]); ?></td>
                    <?php
                      
                      if($value[2] > 0)
                      {
                        $total_credit += $value[2];
                        echo "<td>".floatval($value[2])."</td>";
                        echo "<td> - </td>";
                      }
                      else if($value[2] < 0)
                      {
                        $total_debit += abs($value[2]);
                        echo "<td> - </td>";
                        echo "<td>".abs(floatval($value[2]))."</td>";
                      }
                      else
                      {
                        echo "<td> - </td>";
                        echo "<td> - </td>";
                      }
                    ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <div style="margin-top: 5px">
              <div class="col-md-6 text-right">
                <span style="font-weight: bold;">Total Credit: </span>
                <span style="font-weight: bold;" id="display_total"><?php echo floatval($total_credit); ?></span>
              </div>
              <div class="col-md-6">
                <span style="font-weight: bold;">Total Debit: </span>
                <span style="font-weight: bold;" id="display_total"><?php echo floatval($total_debit); ?></span>
              </div>
            </div>
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
  $("#vendorsRemainingBalance").addClass('active');

});


</script>
<script>
  $(function () {
    $('#example1').DataTable({
      responsive: true,
      "ordering": false,
      "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
    });
  })
</script>
<script>
$(function(){
  
  $("#reservation").on('change', function(){
    var range = encodeURI($(this).val());
    window.location = '<?php echo base_url() ?>index.php/Reports/sale_orders_detail?range='+range;
  });

});
</script>
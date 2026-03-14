

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Loan
      <small>Loan Deductions Summary</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Loan Deductions Summary</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">
        <?php if(in_array('printLoanDeductionsSummary', $user_permission)): ?>    
          <a title="Print Loan Deductions Summary" target="__blank" href="<?php base_url() ?>print_loan_deductions" class="btn btn-info" id="print">
            <i class="glyphicon glyphicon-print"></i>
          </a>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Loan Deductions Summary</h3>
            <div class="pull-right">
              <form method="POST" class="form-inline">
                <div class="form-group">
                  <select class="form-control" id="selected_vendor" name="selected_vendor">
                    <option value=""> --- Select Vendor ... -- </option>
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
                <a href="<?php echo base_url() ?>index.php/Loan/print_loan_deductions<?php if(isset($_GET['selected_vendor'])){ echo "?selected_vendor=".$_GET['selected_vendor']; } ?>" target="__blank" class="btn btn-success btn-sm btn-flat" id="print"><span class="glyphicon glyphicon-print"></span> Print</a>
              </form>
            </div>
          </div>
          
          <!-- /.box-header -->
          <div class="box-body">
            <table class="table table-bordered table-hover" id="example1">
              <thead>
                <tr>
                  <th width="3%">#</th>
                  <th style="color:#3c8dbc">Loan</th>
                  <th style="color:#3c8dbc">Deductions</th>
                </tr>
              </thead>
              <tbody bgcolor="#eaeaea">
                <?php 
                  $counter = 1;
                  $total_remaining_loan = 0;
                ?>
                <?php foreach($result as $key => $value): ?>
                  <tr>
                    <?php 
                      $loan_data = $this->Model_loan->getLoanData($value);
                      $loan_deductions = $this->Model_loan->fetchLoanDeductions($value);
                      $vendor_data = $this->Model_loan->checkVendor($loan_data['supplier_id']);
                      $vendor_name = $vendor_data['first_name']. ' '. $vendor_data['last_name'];
                    ?>
                    <td><?php echo $counter++; ?></td>
                    <td>
                      <table class="table table-bordered table-striped example2">
                        <thead>
                          <tr>
                            <th>Vendor Name</th>
                            <th>Given Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td><?php echo $vendor_name; ?></td>
                            <td><?php echo floatval($loan_data['amount']); ?></td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                    <td>
                      <table class="table table-bordered table-striped example3">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Bill_no</th>
                            <th>DateTime</th>
                            <th>Deduction Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                            $count = 1; 
                            $sum_deduction_amount = 0;
                          ?>
                          <?php foreach($loan_deductions as $key => $value): ?>
                            <?php
                              $sum_deduction_amount += $value['deduction_amount'];
                              $purchase_order_data = $this->Model_products->getPurchaseOrdersData($value['order_id']);
                              $date = date('d-m-Y', strtotime($purchase_order_data['datetime_created']));
                              $time = date('h:i a', strtotime($purchase_order_data['datetime_created']));
                              $date_time = $date . ' ' . $time;
                            ?>
                            <tr>
                              <td><?php echo $count++; ?></td>
                              <td><?php echo $purchase_order_data['bill_no'] ?></td>
                              <td><?php echo $date_time; ?></td>
                              <td><?php echo floatval($value['deduction_amount']); ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                      <p>
                        <span style="background-color: #ffffff">
                          <strong>Total Deductions: <?php echo floatval($sum_deduction_amount); ?></strong>
                        </span>
                      </p>
                      <p>
                        <span style="background-color: #ffffff">
                          <strong>Remaining Amount: <?php echo floatval($loan_data['amount'] - $sum_deduction_amount); ?></strong>
                        </span>
                        <?php $total_remaining_loan += $loan_data['amount'] - $sum_deduction_amount; ?>
                      </p>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>

            </table>
            <div style="margin: 10px">
              <span><b>Total Remaining Loan: </b></span>
              <span><b><?php echo floatval($total_remaining_loan); ?></b></span>
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

<script>
  $(function () {
    $('#example1').DataTable({
      responsive: true,
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
<script type="text/javascript">

$(document).ready(function() {
  $("select").select2();
  $("#mainLoanNav").addClass('active');
  $("#loanDeductionsNav").addClass('active');

});

$(function(){

    $("#selected_vendor").on('change', function(){
      var selected_vendor = encodeURI($(this).val());
      window.location = '<?php echo base_url() ?>index.php/Loan/loan_deductions?selected_vendor='+selected_vendor;
    });


  });

</script>

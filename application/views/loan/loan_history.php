
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
        <?php if(in_array('printLoanHistory', $user_permission)): ?>
          <a href="<?php echo base_url() ?>index.php/Loan/print_loan_history<?php if(isset($_GET['selected_vendor'])){ echo "?selected_vendor=".$_GET['selected_vendor']; } ?>" target="__blank" class="btn btn-info"><i class="glyphicon glyphicon-print"></i></a>
          <br /> <br />
        <?php endif; ?>
        
        <div class="box">
          <div class="box-header">
            <h3 class="box-title"><?php echo $heading; ?></h3>
            <div class="pull-right">
              <form method="GET" class="form-inline" id="vendorsLedgerForm">
                <div class="form-group">
                  <select required class="form-control" id="selected_vendor" name="selected_vendor">
                    <option value=""> -- Select Vendor ... -- </option>
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
                <button title="Select Vednor to view the Vendor Loan History" type="submit" class="btn btn-primary btn-sm btn-flat" id="view-ledger"> View Loan History</button>
              </form>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="example1" class="table table-bordered">
              <thead>
                <tr>
                  <th>#</th>
                  <th style="color:#3c8dbc">Vendor</th>
                  <th style="color:#3c8dbc">Given Loans</th>
                  <th style="color:#3c8dbc">Loan Deductions</th>
                </tr>
              </thead>
              <tbody bgcolor="#eaeaea">
                <?php $counter = 1 ?>
                <?php foreach($result as $key => $value): ?>
                  <tr>
                    <?php
                      $overall_loan_data = $this->Model_loan->getLoanData($value);
                      $loan_deductions = $this->Model_loan->fetchLoanDeductions($value);
                    ?>
                    <td><?php echo $counter++ ?></td>
                    <?php
                      $vendor_data = $this->Model_loan->checkVendor($overall_loan_data['supplier_id']);
                      $vendor_name = $vendor_data['first_name']. ' '. $vendor_data['last_name'];
                    ?>
                    <td style="text-transform: capitalize;"><?php echo $vendor_name ; ?></td>
                    <td>
                      <table class="table table-bordered table-striped example2">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Loan Amount</th>
                            <th>Installment Amount</th>
                            <th>Payment</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                            $vendor_loan_data = $this->Model_loan->getVendorsLoan($value);
                            $total_loan_amount = 0;
                          ?>
                          <?php 
                            foreach($vendor_loan_data as $k => $v)
                            {
                              $total_loan_amount += $v['loan_amount'];
                              
                              echo "<tr><td>".$v['loan_date']."</td><td>".floatval($v['loan_amount'])."</td><td>".floatval($v['vendor_installment_amount'])."</td><td>".$v['payment_method']."</td></tr>";
                            }
                          ?>
                        </tbody>
                      </table>
                      <p>
                        <span style="background-color: #ffffff">
                          <strong>Total Loan Amount: <?php echo floatval($total_loan_amount); ?></strong>
                        </span>
                      </p>
                    </td>
                    <td>
                      <table class="table table-bordered table-striped example3">
                        <thead>
                          <tr>
                            <th>Date Time</th>
                            <th>Deduction Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                            $loan_deductions = $this->Model_loan->fetchLoanDeductions($value);
                            $total_deduction_amount = 0;
                          ?>
                          <?php 
                            foreach($loan_deductions as $k => $v)
                            {
                              $total_deduction_amount += $v['deduction_amount'];
                              $purchase_order_data = $this->Model_products->getPurchaseOrdersData($v['order_id']);
                              $date = date('d-m-Y', strtotime($purchase_order_data['datetime_created']));
                              $time = date('h:i a', strtotime($purchase_order_data['datetime_created']));
                              $date_time = $date . ' ' . $time;
                              echo "<tr><td>".$date_time."</td><td>".floatval($v['deduction_amount'])."</td></tr>";
                            }
                          ?>
                        </tbody>
                      </table>
                      <p>
                        <span style="background-color: #ffffff">
                          <strong>Total Deduction Amount: <?php echo floatval($total_deduction_amount); ?></strong>
                        </span>
                      </p>
                      <p>
                        <span style="background-color: #ffffff">
                          <strong>Remaining Amount: <?php echo floatval($total_loan_amount - $total_deduction_amount); ?></strong>
                        </span>
                      </p>
                    </td>
                    
                  </tr>
                <?php endforeach; ?><!-- endforeach -->
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

  $("#mainLoanNav").addClass('active');
  $("#loanHistoryNav").addClass('active');
  $("select").select2();

});


</script>
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
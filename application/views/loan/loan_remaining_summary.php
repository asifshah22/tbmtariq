

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Loan
      <small>Remaining Loan Summary</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Remaining Loan Summary</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">
        <?php if(in_array('printRemainingLoanSummary', $user_permission)): ?>  
          <a title="Print Remaining Loan Summary" target="__blank" href="<?php base_url() ?>print_remaining_loan_summary" class="btn btn-info" id="print">
            <i class="glyphicon glyphicon-print"></i>
          </a>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Remaining Loan Summary</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th width="3%">#</th>
                  <th style="color:#3c8dbc">Image</th>
                  <th style="color:#3c8dbc">Vendor Name</th>
                  <th style="color:#3c8dbc">Loan Amount</th>
                  <th style="color:#3c8dbc">Installment</th>
                  <th style="color:#3c8dbc">Paid</th>
                  <th style="color:#3c8dbc">Remaining Amount</th>
                </tr>
              </thead>
              <tbody>
                <?php $counter = 1; $total_amount = 0;?>
                <?php foreach($vendor_data as $key => $value): ?>
                  <tr>
                    <?php
                      $total_amount += $value['remaining_amount'];
                    ?>
                    <td><?php echo $counter++;?></td>
                    <?php if($value['image'] == ""): ?>
                      <?php
                        $image = '<a target="_blank" href="'.base_url().'assets/images/vendor_images/vendor-default-im.jpg" title="Vendor default image"><img src="'.base_url('/assets/images/vendor_images/vendor-default-im.jpg').'" alt="vendor default image" class="img-circle" width="50" height="50" /></a>';
                      ?>
                      <td><?php echo $image; ?></td>
                    
                    <?php elseif($value['image'] != ""): ?>
                      <?php
                        $image = '<a target="_blank" href="'.base_url().'assets/images/vendor_images/'.$value['image'].'" title="Vendor image"><img src="'.base_url('/assets/images/vendor_images/'.$value['image'].'').'" alt="Vendor image" class="img-circle" width="60" height="60" /></a>';
                      ?>
                      <td><?php echo $image; ?></td>
                    <?php endif; ?>
                      <td><?php echo $value['first_name'].' '.$value['last_name'] ?></td>
                      <td><?php echo floatval($value['amount']); ?></td>
                      <td><?php echo floatval($value['installment_amount']); ?></td>
                      <td><?php echo floatval($value['paid_amount']); ?></td>
                      <td><?php echo floatval($value['remaining_amount']); ?></td>
                    
                  </tr>
                <?php endforeach; ?>
              </tbody>

            </table>
            <div style="margin-top: 5px">
              <span style="font-weight: bold;">Total Remaining Amount: </span>
              <span style="font-weight: bold;" id="display_total"><?php echo floatval($total_amount); ?></span>
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
  $(document).ready(function() {
    $("#mainLoanNav").addClass('active');
    $("#loanRemainingNav").addClass('active');
    $(".table").DataTable({
      "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
    });
  });

</script>

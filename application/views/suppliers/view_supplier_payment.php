<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Suppliers 
      <small>Supplier Payment View</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Supplier Payment View</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">View</h3>    
          </div>
          <div class="box-body">
            <table class="table table-striped table-bordered">
              <tbody>
                <?php
                  $date = date('d-m-Y', strtotime($payment_data['datetime_creation']));
                  $time = date('h:i a', strtotime($payment_data['datetime_creation']));
                  $date_time = $date . ' ' . $time;
                  $paid_by = '';
                  if($payment_data['paid_by'] == 1)
                  {
                    $paid_by = "System (TBM)";
                  }
                  elseif($payment_data['paid_by'] == 2)
                  {
                    $paid_by = "Vendor";
                  }
                  $vendor_data = $this->Model_supplier->getSupplierData($payment_data['vendor_id']);
                ?>
                <tr>
                  <td><strong>ID</strong></td>
                  <td><?php echo $payment_data['id']; ?></td>
                </tr>
                <tr>
                  <td><strong>Name</strong></td>
                  <td><?php echo $vendor_data['first_name']. ' '.$vendor_data['last_name']; ?></td>
                </tr>
                <tr>
                  <td><strong>Paid Amount</strong></td>
                  <td><?php echo floatval($payment_data['paid_amount']); ?></td>
                </tr>
                <tr>
                  <td><strong>Payment Method</strong></td>
                  <td><?php echo $payment_data['payment_method']; ?></td>
                </tr>
                <tr>
                  <td><strong>Payment Note</strong></td>
                  <td><?php echo $payment_data['payment_note']; ?></td>
                </tr>
                <tr>
                  <td><strong>Paid By</strong></td>
                  <td><strong><?php echo $paid_by; ?></strong></td>
                </tr>
                <tr>
                  <td><strong>Date Time</strong></td>
                  <td><?php echo $date_time; ?></td>
                </tr>
                
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script type="text/javascript">

  $("#mainVendorNav").addClass('active');

</script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Suppliers 
      <small>Supplier View</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Supplier View</li>
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
                  $phones = '';
                  $db_phones = unserialize($supplier_data['phone']);
                  $count_phones = count($db_phones);
                  for ($x = 0; $x < $count_phones; $x++) {
                    $phones .= $db_phones[$x]. "<br>";
                  }

                  $image = '';
                  if($supplier_data['image']){
                    $image = '<a target="_blank" href="'.base_url().'assets/images/vendor_images/'.$supplier_data['image'].'" title="Vendor image"><img src="'.base_url('/assets/images/vendor_images/'.$supplier_data['image'].'').'" alt="Vendor image" class="img-circle" width="60" height="60" /></a>';
                  }
                  else{
                    $image = '<a target="_blank" href="'.base_url().'assets/images/vendor_images/vendor-default-im.jpg" title="Vendor default image"><img src="'.base_url('/assets/images/vendor_images/vendor-default-im.jpg').'" alt="vendor default image" class="img-circle" width="50" height="50" /></a>';
                  }
                  $date = date('d-m-Y', strtotime($supplier_data['creation_date_time']));
                  $time = date('h:i a', strtotime($supplier_data['creation_date_time']));
                  $date_time = $date . ' ' . $time;
                ?>
                <tr>
                  <td><strong>ID</strong></td>
                  <td><?php echo $supplier_data['id']; ?></td>
                </tr>
                <tr>
                  <td><strong>Name</strong></td>
                  <td><?php echo $supplier_data['first_name']. ' '.$supplier_data['last_name']; ?></td>
                </tr>
                <tr>
                  <td><strong>Address</strong></td>
                  <td><?php echo $supplier_data['address']; ?></td>
                </tr>
                <tr>
                  <td><strong>City</strong></td>
                  <td><?php echo $supplier_data['city']; ?></td>
                </tr>
                <tr>
                  <td><strong>Balance</strong></td>
                  <td><?php echo floatval($supplier_data['balance']); ?></td>
                </tr>
                <tr>
                  <td><strong>Country</strong></td>
                  <td><?php echo $supplier_data['country']; ?></td>
                </tr>
                <tr>
                  <td><strong>CNIC</strong></td>
                  <td><?php echo $supplier_data['cnic']; ?></td>
                </tr>
                <tr>
                  <td><strong>Contact</strong></td>
                  <td><?php echo $phones; ?></td>
                </tr>
                <tr>
                  <td><strong>Email</strong></td>
                  <td><?php echo $supplier_data['email']; ?></td>
                </tr>
                <tr>
                  <td><strong>Remarks</strong></td>
                  <td><?php echo $supplier_data['remarks']; ?></td>
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

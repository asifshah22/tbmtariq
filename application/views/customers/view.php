<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Customers 
      <small>Customer View</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Customer View</li>
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
                  $department_name = $this->Model_department->getCustomerDeparment($customer_data['id'])['department_name'];
                ?>
                <tr>
                  <td><strong>ID</strong></td>
                  <td><?php echo $customer_data['id']; ?></td>
                </tr>
                <tr>
                  <td><strong>Name</strong></td>
                  <td><?php echo $customer_data['full_name']; ?></td>
                </tr>
                <tr>
                  <td><strong>Department</strong></td>
                  <td><?php echo $department_name; ?></td>
                </tr>
                <tr>
                  <td><strong>CNIC</strong></td>
                  <td><?php echo $customer_data['cnic']; ?></td>
                </tr>
                <tr>
                  <td><strong>Contact</strong></td>
                  <td><?php echo $customer_data['phone_number']; ?></td>
                </tr>
                <tr>
                  <td><strong>Address</strong></td>
                  <td><?php echo $customer_data['address']; ?></td>
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

  $("#mainCustomersNav").addClass('active');

</script>

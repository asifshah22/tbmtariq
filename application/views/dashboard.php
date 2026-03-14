
<?php 

  $user_id = $this->session->userdata('id');
  $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
  $permissions = unserialize($group_data['permission']);

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>Control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
              <div class="inner">
                <h3><?php echo $count_items_in_stock ?></h3>

                <p>Items in Factory Stock</p>
              </div>
              <div class="icon">
                <!-- <i class="ion ion-bag"></i> -->
                <i class="fa fa-cube"></i>
              </div>
              <?php if(in_array('viewFactoryStock', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Product/manage_stock" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
              <div class="inner">
                <h3><?php echo $daily_purchase_orders ?></h3>

                <p>Daily Purchase Orders</p>
              </div>
              <div class="icon">
                <!-- <i class="ion ion-stats-bars"></i> -->
                <i class="fa fa-cart-plus"></i>
              </div>
              <?php if(in_array('recordPurchasing', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Product/manage_purchase_orders" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-purple">
              <div class="inner">
                <h3><?php echo $count_daily_sale_orders; ?></h3>

                <p>Daily Sale Orders (Non-Emp)</p>
              </div>
              <div class="icon">
              </div>
              <?php if(in_array('recordSaleOrderNE', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Product/manage_sales" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
              <div class="inner">
                <h3><?php echo $count_daily_sale_orders_emp ?></h3>

                <p>Daily Sale Oders (Employees)</p>
              </div>
              <div class="icon">
                <i>-</i>
              </div>
              <?php if(in_array('recordSaleOrderE', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Product/company_sales" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
              <div class="inner">
                <h3><?php echo $count_items_in_office_stock ?></h3>

                <p>Items in Office Stock</p>
              </div>
              <div class="icon">
                <!-- <i class="ion ion-bag"></i> -->
                <i class="fa fa-cube"></i>
              </div>
              <?php if(in_array('viewOfficeStock', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Product/view_office_stock" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
              <div class="inner">
                <h3><?php echo $count_daily_office_stock_transfers; ?></h3>

                <p>Daily Office Stock Transfers</p>
              </div>
              <div class="icon">
                <i class="fa fa-arrow-right"></i>
              </div>
              <?php if(in_array('recordOfficeStockTransfer', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Product/manage_office_stock" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-purple">
              <div class="inner">
                <?php if(!empty($daily_sales_amount)): ?>
                  <h3><?php echo $daily_sales_amount ?></h3>
                  
                <?php else: ?>
                  <h3><?php echo '0' ?></h3>
                <?php endif; ?>

                <p>Daily Sales Amount</p>
              </div>
              <div class="icon">
                <i class="ion ion-cash"></i>
              </div>
              <?php if(in_array('recordSaleOrderNE', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Product/manage_sales" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
              <div class="inner">
                <h3><?php if($count_remaining_loan_amount){echo $count_remaining_loan_amount;} else{echo 0;}  ?></h3>

                <p>Remaining Loan</p>
              </div>
              <div class="icon">
                <i class="ion ion-cash"></i>
              </div>
              <?php if(in_array('recordRemainingLoanSummary', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Loan/loan_remaining_summary" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
              <div class="inner">
                <h3><?php echo $count_products ?></h3>

                <p>Total Items</p>
              </div>
              <div class="icon">
                <!-- <i class="ion ion-bag"></i> -->
                <i class="fa fa-product-hunt"></i>
              </div>
              <?php if(in_array('recordProduct', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Product/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
              <div class="inner">
                <h3><?php echo $count_categories ?></h3>

                <p>Total Categories</p>
              </div>
              <div class="icon">
                <!-- <i class="ion ion-stats-bars"></i> -->
                <i class="fa fa-list-alt"></i>
              </div>
              <?php if(in_array('recordCategory', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Category/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-purple">
              <div class="inner">
                <h3><?php echo $count_users; ?></h3>

                <p>Users</p>
              </div>
              <div class="icon">
                <i class="fa fa-users"></i>
              </div>
              <?php if(in_array('recordUser', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/User/manage_users" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
              <div class="inner">
                <h3><?php echo $count_permission_groups ?></h3>

                <p>Permission Groups</p>
              </div>
              <div class="icon">
                <i class="fa fa-unlock-alt"></i>
              </div>
              <?php if(in_array('recordGroup', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/User/manage_groups" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          
        </div>
      <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
              <div class="inner">
                <h3><?php echo $total_vendors ?></h3>

                <p>Vendors</p>
              </div>
              <div class="icon">
                <i class="ion ion-android-people"></i>
              </div>
              <?php if(in_array('recordVendor', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Supplier/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
              <div class="inner">
                <h3><?php echo $total_trusted_customers ?></h3>

                <p>Trusted Customers</p>
              </div>
              <div class="icon">
                <i class="fa fa-user-circle"></i>
              </div>
              <?php if(in_array('recordCustomer', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Customers/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-purple">
              <div class="inner">
                <h3><?php echo $count_departments ?></h3>

                <p>Departments</p>
              </div>
              <div class="icon">
                <i class="ion ion-android-home"></i>
              </div>
              <?php if(in_array('recordDepartment', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Department/index" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
              <div class="inner">
                <h3><?php echo $scaling_units ?></h3>

                <p>Scaling Units</p>
              </div>
              <div class="icon">
                <i class="fa fa-circle"></i>
              </div>
              <?php if(in_array('recordScalingUnits', $permissions)): ?>
                <a href="<?php echo base_url() ?>index.php/Product/manage_units" class="small-box-footer"> More info<i class="fa fa-arrow-circle-right"></i></a>
            <?php endif; ?>
            </div>
          </div>
      </div>      

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <script type="text/javascript">
    $(document).ready(function() {
      $("#dashboardMainMenu").addClass('active');
    }); 
  </script>

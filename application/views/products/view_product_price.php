<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Product Price
      <small>Product Price View</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Product Price View</li>
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
                  $category_name = '';
                  if($product_price_data['category_id'])
                  {
                      $category_data = $this->Model_category->getCategoryData($product_price_data['category_id']);
                      $category_name = $category_data['name'];
                      
                  }
                  else
                  {
                      $category_name = 'Nill';
                  }
                  $vendor_name = $product_price_data['first_name']. ' '. $product_price_data['last_name'];
                  $unit_name = $this->Model_products->getUnitsData($product_price_data['unit_id'])['unit_name'];
                  $date = date('d-m-Y', strtotime($product_price_data['product_prices_datetime']));
                  $time = date('h:i a', strtotime($product_price_data['product_prices_datetime']));

                  $date_time = $date . ' ' . $time;
                ?>


                <tr>
                  <td><strong>ID</strong></td>
                  <td><?php echo $product_price_data['product_prices_id']; ?></td>
                </tr>
                <tr>
                  <td><strong>Vendor Name</strong></td>
                  <td><?php echo $vendor_name; ?></td>
                </tr>
                <tr>
                  <td><strong>Category</strong></td>
                  <td><?php echo $category_name; ?></td>
                </tr>
                <tr>
                  <td><strong>Item Name</strong></td>
                  <td><?php echo $product_price_data['product_name']; ?></td>
                </tr>
                <tr>
                  <td><strong>Unit</strong></td>
                  <td><?php echo $unit_name; ?></td>
                </tr>
                <tr>
                  <td><strong>Price</strong></td>
                  <td><?php echo number_format($product_price_data['price'], 2); ?></td>
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


<?php date_default_timezone_set("Asia/Karachi"); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Sales
      <small>View Sale Order</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">iew Sale Order</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif($this->session->flashdata('errors')): ?>
          <div class="alert alert-error alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('errors'); ?>
          </div>
        <?php endif; ?>

        <?php if(in_array("updateSaleOrderNE", $user_permission)): ?>
          <a href="<?php echo base_url() ?>index.php/Product/update_sale_order/<?php echo $sale_order_data['id']; ?>" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>
        <?php endif; ?>

        <?php if(in_array("printSaleOrderNE", $user_permission)): ?>
          <a target="__blank" href="<?php echo base_url() ?>index.php/Product/print_sale_invoice/<?php echo $sale_order_data['id']; ?>" class="btn btn-info"><span class="glyphicon glyphicon-print"></span></a>
        <?php endif; ?>

        <?php if( in_array("updateSaleOrderNE", $user_permission) || in_array("printSaleOrderNE", $user_permission) ): ?>
          <br><br>
        <?php endif; ?>


        <div class="box">
          <div class="box-header">
            <h3 class="box-title">View</h3>
          </div>
          <!-- /.box-header -->
          <div class="form-horizontal">
            <div class="box-body">
              <table class="table table-striped table-bordered">
                <tbody>
                  <tr>
                    <td><strong>ID</strong></td>
                    <td><?php echo $sale_order_data['id'] ?></td>
                  </tr>
                  <tr>
                    <td><strong>Customer Name</strong></td>
                    <td><?php echo $sale_order_data['customer_name'] ?></td>
                  </tr>

                  <tr>
                    <td><strong>Customer CNIC</strong></td>
                    <td><?php echo $sale_order_data['customer_cnic'] ?></td>
                  </tr>
                  <tr>
                    <td><strong>Customer Address</strong></td>
                    <td><?php echo $sale_order_data['customer_address'] ?></td>
                  </tr>
                  <tr>
                    <td><strong>Customer Contact</strong></td>
                    <td><?php echo $sale_order_data['customer_contact'] ?></td>
                  </tr>
                  <?php
                    $payment_method = $sale_order_data['payment_method'];
                    $payment_note = $sale_order_data['payment_note'];
                    $stock_type = '';
                    if ($sale_order_data['stock_type'] == 1) {
                        $stock_type = 'Factory Stock';
                    }
                    else if ($sale_order_data['stock_type'] == 2) {
                        $stock_type = 'Office Stock';
                    }
                  ?>
                  <tr>
                    <td><strong>Stock</strong></td>
                    <td><?php echo $stock_type; ?></td>
                  </tr>
                  <tr>
                    <td><strong>Gross Amount</strong></td>
                    <td><?php echo floatval($sale_order_data['gross_amount']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Discount</strong></td>
                    <td><?php echo floatval($sale_order_data['discount']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Freight</strong></td>
                    <td><?php echo floatval($sale_order_data['loading_or_affair']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Net Amount</strong></td>
                    <td><?php echo floatval($sale_order_data['net_amount']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Remarks</strong></td>
                    <td><?php echo ($sale_order_data['remarks']) ? $sale_order_data['remarks']: 'Nill' ?></td>
                  </tr>
                  <tr>
                    <td><strong>Recieved</strong></td>
                    <td><?php echo floatval($sale_order_data['paid_amount']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Remaining</strong></td>
                    <td><?php echo floatval($sale_order_data['net_amount'] - $sale_order_data['paid_amount']) ?></td>
                  </tr>
                  <tr>
                    <td><strong>Payment Method</strong></td>
                    <td><?php echo ($payment_method) ? $payment_method : 'Not Provided'; ?></td>
                  </tr>
                  <tr>
                    <td><strong>Payment Note</strong></td>
                    <td><?php echo ($payment_note) ? $payment_note : 'Not Provided'; ?></td>
                  </tr>
                  
                  <tr>
                    <?php
                      $date = date('d-m-Y', strtotime($sale_order_data['date_time']));
                      $time = date('h:i a', strtotime($sale_order_data['date_time']));
                      $date_time = $date.' '.$time;
                    ?>
                    <td><strong>DateTime</strong></td>
                    <td><?php echo $date_time; ?></td>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>
          
          <hr>
          <div class="box-header">
            <h3 class="box-title">Order Items</h3>
          </div>
          <!-- /.box-header -->
          <div class="form-horizontal">
              <div class="box-body">
                <table class="table table-bordered table-striped" id="manageTable">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Unit</th>
                      <th>Quantity</th>
                      <th>Rate</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                   <tbody>
                    <?php if(isset($sale_items_data)): ?>
                    <?php $x = 1; ?>
                    <?php foreach ($sale_items_data as $key => $val): ?>
                      <tr>
                        <td>
                            <?php foreach ($products as $k => $v): ?>
                              <?php
                                $unit_name = $this->Model_products->getUnitsData($v['unit_id'])['unit_name'];
                                $category_name = '';
                                $category_id = '';
                                if($v['category_name'] == null)
                                {
                                  $category_name = '';
                                  $category_id = ' ';
                                }
                                else
                                {
                                  $category_name = ' &#8212 ' . $v['category_name'];
                                  $category_id = $v['category_id'];
                                }
                              ?>
                              
                              <?php if($val['product_id'] == $v['product_id'] && $val['category_id'] == $v['category_id'] && $val['stock_unit_id'] == $v['unit_id']): ?>
                                 <?php echo $v['product_name']. ' ' . $category_name.' &nbsp;&nbsp;&nbsp;&nbsp; ('.$unit_name.')' ?>
                              <?php endif; ?>

                            <?php endforeach; ?>
                        </td>
                        
                        <td>
                          <?php
                            $unit_name = "Not Mentioned";
                            if($val['unit_id'] != 0){
                              $unit_data = $this->Model_products->getUnitsData($val['unit_id']);
                              $unit_name = $unit_data['unit_name'];
                            }
                            echo $unit_name;
                          ?>
                        </td>
                        <td>
                          <?php
                            $unit_value = 1;
                            if($val['unit_id']){
                              $unit_data_values = $this->Model_products->getUnitValuesData();
                              foreach($unit_data_values as $k => $v){
                                  if($v['unit_id'] == $val['unit_id']){
                                      $unit_value = $v['unit_value'];
                                  }
                              }
                            }
                          ?>
                          <?php echo floatval($val['qty'] / $unit_value); ?>
                        </td>
                              <td>
                                <?php echo floatval($val['product_price']) ?>
                              </td>
                              <td>
                                <?php
                                  $unit_value = 1;
                                  if($val['unit_id']){
                                    $unit_data_values = $this->Model_products->getUnitValuesData();
                                    foreach($unit_data_values as $k => $v){
                                        if($v['unit_id'] == $val['unit_id']){
                                            $unit_value = $v['unit_value'];
                                        }
                                    }
                                  }
                                  $amount = $val['qty'] * $val['product_price'] / $unit_value; 
                                ?>
                                <?php echo floatval($amount) ?>
                                
                              </td>
                          </tr>
                        <?php $x++; ?>
                      <?php endforeach; ?>
                    <?php endif; ?>
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

<!-- remove brand modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Sale</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Product/remove_sale_order" method="post" id="removeForm">
        <div class="modal-body">
          <p>Do you really want to remove?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
  var base_url = "<?php echo base_url(); ?>";

  $(document).ready(function() {
    $("#mainSalesNav").addClass('active');
    $('#manageTable').DataTable();
    var stock_value = "<?php echo $sale_order_data['stock_type'];  ?>";
    if(stock_value){
      $("#select_stock").val(stock_value);
    }
  });

</script>
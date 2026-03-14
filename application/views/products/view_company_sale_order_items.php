<?php 
  date_default_timezone_set("Asia/Karachi");
  $datetime = date_create($sale_order_data['date_time']);
  $date = date_format($datetime,"Y-m-d");
  $time = date_format($datetime,"h:i:s a");
?>
<?php date_default_timezone_set("Asia/Karachi"); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Sales
      <small>Update Company Sale</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Update Company Sale</li>
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

        <?php if(in_array('updateSaleOrderE', $user_permission)): ?>
          <a href="<?php echo base_url() ?>index.php/Product/update_company_sale_order/<?php echo $sale_order_data['id'] ?>" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>
        <?php endif; ?>

        <?php if(in_array('printSaleOrderE', $user_permission)): ?>
          <a title="Print Purchased Order" class="btn btn-info" target="__blank" href="<?php echo base_url('index.php/Product/print_company_sale_invoice/'.$sale_order_data['id']) ?>"><span class="glyphicon glyphicon-print"></span></a>
        <?php endif; ?>
        <?php if( ( in_array('updateSaleOrderE', $user_permission) ) || ( in_array('deleteSaleOrderE', $user_permission) ) || ( in_array('printSaleOrderE', $user_permission) ) ): ?>
          <br /> <br />
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
                  <?php
                    $customer_data = $this->Model_Customers->getCustomerData($sale_order_data['customer_id']);
                    $department_data = $this->Model_department->getDepartmentData($sale_order_data['department_id']);
                    $count_total_item = $this->Model_products->countCompanySaleOrderItem($sale_order_data['id']);
                    $stock_type = '';
                    if ($sale_order_data['stock_type'] == 1) {
                        $stock_type = 'Factory Stock';
                    }
                    else if ($sale_order_data['stock_type'] == 2) {
                        $stock_type = 'Office Stock';
                    }

                    $date = date('d-m-Y', strtotime($sale_order_data['date_time']));
                    $time = date('h:i a', strtotime($sale_order_data['date_time']));
                    $date_time = $date.' '.$time;
                  ?>
                  <tr>
                    <td><strong>ID</strong></td>
                    <td><?php echo $sale_order_data['id'] ?></td>
                  </tr>

                  <tr>
                    <td><strong>Bill no</strong></td>
                    <td><?php echo $sale_order_data['bill_no'] ?></td>
                  </tr>

                  <tr>
                    <td><strong>Customer Name</strong></td>
                    <td><?php echo $customer_data['full_name'] ?></td>
                  </tr>

                  <tr>
                    <td><strong>Customer Department</strong></td>
                    <td><?php echo $department_data['department_name'] ?></td>
                  </tr>

                  <tr>
                    <td><strong>Total Items</strong></td>
                    <td><?php echo $count_total_item ?></td>
                  </tr>
                  <tr>
                    <td><strong>Stock Type</strong></td>
                    <td><?php echo $stock_type ?></td>
                  </tr>
                  <tr>
                    <td><strong>Remarks</strong></td>
                    <td><?php echo ($sale_order_data['remarks']) ? $sale_order_data['remarks']: 'Nill' ?></td>
                  </tr>

                  <tr>
                    <td><strong>DateTime</strong></td>
                    <td><?php echo $date_time ?></td>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>
          <!-- /.box-body -->
          <hr>
          <div class="box-header">
              <h3 class="box-title">Items</h3>
              <div class="form-horizontal">
                <div class="box-body">
                  <table class="table table-bordered" id="manageTable">
                    <thead>
                      <tr>
                        <th width="25%">Product</th>
                        <th width="25%">Unit</th>
                        <th width="25%">Qty</th>
                        <th width="25%">Stock Remaining</th>
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
                              if($v['category_name'] == null)
                              {
                                $category_name = '';
                              }
                              else
                              {
                                $category_name = ' &#8212 ' . $v['category_name'];
                              }
                          ?>
                            <?php if($val['product_id'] == $v['product_id'] && $val['category_id'] == $v['category_id'] && $val['stock_unit_id'] == $v['unit_id']): ?>
                              <?php echo $v['product_name']. ' ' . $category_name.' &nbsp;&nbsp;&nbsp;&nbsp; ( '.$unit_name.' )' ?>      
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
                          <?php if($stock_data_array){ echo floatval($stock_data_array[$x-1]['quantity']);} ?>
                        </td>
                          </tr>
                        <?php $x++; ?>
                      <?php endforeach; ?>
                    <?php endif; ?>

                    </tbody>

                  </table>
                </div>
              </div>
            </div>
          </div>
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

      <form role="form" action="<?php echo base_url() ?>index.php/Product/remove_company_sale_order" method="post" id="removeForm">
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

    $("#manageTable").DataTable();

    $("#mainSalesNav").addClass('active');
    var stock_value = "<?php echo $sale_order_data['stock_type'];  ?>";
    if(stock_value){
      $("#select_stock").val(stock_value);
    }

    $("select").select2();
  }); // /document


</script>
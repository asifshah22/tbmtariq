
<?php date_default_timezone_set("Asia/Karachi"); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Sales
      <small>Edit Sale Order</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Edit Sale Order</li>
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


        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Update Sale Order</h3>
          </div>
          <!-- /.box-header -->
          <form role="form" action="<?php echo base_url() ?>index.php/Product/update_sale_order/<?php echo $this->uri->segment(3)?>" method="post" class="form-horizontal">
              <div class="box-body">

                <?php echo validation_errors(); ?>

                <div class="form-group">
                  <label for="gross_amount" class="col-sm-12 control-label">Date: <?php echo date('Y-m-d', strtotime($sale_order_data['date_time'])) ?></label>
                </div>
                <div class="form-group">
                  <label for="gross_amount" class="col-sm-12 control-label">Time: <?php echo date('h:i a', strtotime($sale_order_data['date_time'])) ?></label>
                </div>

                <div class="col-md-6 col-xs-12 pull pull-left">

                  <div class="form-group">
                    <label for="customer_name" class="col-sm-3 required-field control-label">Full Name</label>
                    <div class="col-sm-9">
                      <input required="true" type="text" value="<?php echo $sale_order_data['customer_name'] ?>" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" autocomplete="off" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="customer_cnic" class="col-sm-3 required-field control-label">CNIC</label>
                    <div class="col-sm-9">
                      <input required="true" type="text" value="<?php echo $sale_order_data['customer_cnic'] ?>" class="form-control" id="customer_cnic" name="customer_cnic" placeholder="Customer CNIC" autocomplete="off" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="customer_address" class="col-sm-3 required-field control-label">Address</label>
                    <div class="col-sm-9">
                      <input type="text" value="<?php echo $sale_order_data['customer_address'] ?>" required="true" class="form-control" id="customer_address" name="customer_address" placeholder="Customer Address" autocomplete="off">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="customer_contact" class="col-sm-3 required-field control-label">Contact</label>
                    <div class="col-sm-9">
                      <input type="text" value="<?php echo $sale_order_data['customer_contact'] ?>" required="true" class="form-control" id="customer_contact" name="customer_contact" placeholder="Customer Phone" autocomplete="off">
                    </div>
                  </div>
                </div>
                <div class="col-md-4 col-xs-12 pull pull-right">
                  <div class="form-group">
                    <label for="select_stock" class="col-sm-6 required-field control-label">Selected Stock</label>
                    <div class="col-sm-6">
                      <select class="form-control" name="select_stock" id="select_stock" onchange="setOnStockChange()">
                        <option value="1">Factory Stock</option>
                        <option value="2">Office Stock</option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <br /> <br/>
                <table class="table table-bordered" id="product_info_table">
                  <thead>
                    <tr>
                      <th style="width:30%" class="required-field">Product</th>
                      <th style="width:20%">Unit</th>
                      <th style="width:10%" class="required-field">Qty</th>
                      <th style="width:10%" class="required-field">Rate</th>
                      <th style="width:10%">R.S.Qty <a href="#" data-toggle="tooltip" title="Remaining Stock Quantity."><i class="fa fa-question-circle"></i></a></th>
                      <th style="width:10%">Amount</th>
                      <th style="width:10%"><button type="button" id="add_row" class="btn btn-default"><i class="fa fa-plus"></i></button></th>
                    </tr>
                  </thead>
                   <tbody>
                    <?php if(isset($sale_items_data)): ?>
                    <?php $x = 1; ?>
                    <?php foreach ($sale_items_data as $key => $val): ?>
    
                      <tr id="row_<?php echo $x; ?>">
                        <td>
                          <select class="form-control" data-row-id="row_<?php echo $x; ?>" id="product_<?php echo $x; ?>" name="product[]" onchange="setOnProductChange(<?php echo $x; ?>)" required>
                            <option value="">Select Product</option>
                            <?php foreach ($products as $k => $v): ?>
                              <?php
                                $unit_name = $this->Model_products->getUnitsData($v['unit_id'])['unit_name'];
                                $category_name = '';
                                $value = '';
                                if($v['category_name'] == null)
                                {
                                  $category_name = '';
                                  $value = $v['product_id'] . '- 0- '.$v['unit_id'];
                                }
                                else
                                {
                                  $category_name = ' &#8212 ' . $v['category_name'];
                                  $value = $v['product_id'] .'- '.$v['category_id'].'- '.$v['unit_id'];
                                }
                              ?>
                              
                              <?php if($val['product_id'] == $v['product_id'] && $val['category_id'] == $v['category_id'] && $val['stock_unit_id'] == $v['unit_id']): ?>
                                <option value="<?php echo $value; ?>" <?php echo "selected='selected'"; ?>>
                                  <?php echo $v['product_name']. ' ' . $category_name.' &#8212&#8212&#8212 ('.$unit_name.')' ?>
                                </option>
                              <?php else: ?>
                                <option value="<?php echo $value ?>"><?php echo $v['product_name']. ' ' . $category_name.' &#8212&#8212&#8212 ('.$unit_name.')' ?>
                                </option>
                              <?php endif; ?>

                            <?php endforeach; ?>
                          </select>
                        </td>
                        
                        <td>
                          <select class="form-control" data-row-id="<?php echo $x; ?>" id="unit_<?php echo $x; ?>" name="unit[]" onchange="setOnUnitChange(<?php echo $x; ?>)">
                            <option value="">Select Unit</option>
                            <?php foreach ($units_data as $key => $value): ?>
                                <?php
                                  $unit_id = 0;
                                  $unit_name = '';
                                  if($val['unit_id'] == $value['id'])
                                  {
                                    $unit_id = $value['id'];
                                    $unit_name = $value['unit_name'];
                                  }
                                ?>
                                <?php if($unit_id && $unit_name): ?>
                                    <option <?php echo "selected='selected'"; ?> value="<?php echo $unit_id ?>">
                                      <?php echo $unit_name;?>
                                    </option>
                                  <?php else: ?>
                                    <option value="<?php echo $value['id']?>">
                                      <?php echo $value['unit_name']; ?>
                                    </option>
                                <?php endif; ?>
                              
                              <?php endforeach; ?>
                          </select>
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
                          <input type="number" min="0.1" step="any" name="qty[]" id="qty_<?php echo $x; ?>" class="form-control noscroll" onkeyup="getTotal(<?php echo $x; ?>)" value="<?php echo ($val['qty'] / $unit_value); ?>">
                        </td>
                              <td>
                                <?php if($val['unit_id']): ?>
                                  <!-- if unit is selected and it does not have value in sale prices => inputedPrice -->
                                  <!-- if unit is selected and it aslo has value in sale prices => selectedPrice -->
                                  <?php
                                    $isInput = false;
                                    $data = $this->Model_products->getSalePrices($val['product_id'], $val['category_id'], $val['unit_id']);
                                    if(empty($data)){
                                      $isInput = true;
                                    }
                                    else{
                                      $isInput = false;
                                    }
                                  ?>
                                  <?php if($isInput): ?>
                                    <input type="text" name="rate[]" id="rate_<?php echo $x; ?>" class="form-control" value="<?php echo $val['product_price'] ?>" min="0.1" step="any" onkeyup="getTotal_2(<?php echo $x; ?>)">
                                    <input type="hidden" name="rate_value[]" id="rate_value_<?php echo $x; ?>" class="form-control" value="<?php echo $val['product_price'] ?>">
                                    <?php else: ?>
                                      <input disabled type="text" name="rate[]" min="0.1" step="any" id="rate_<?php echo $x; ?>" class="form-control" value="<?php echo $val['product_price'] ?>">
                                      <input type="hidden" name="rate_value[]" id="rate_value_<?php echo $x; ?>" class="form-control" value="<?php echo $val['product_price'] ?>">
                                  <?php endif; ?>
                                    
                                  <?php else: ?>
                                    <input type="text" name="rate[]" id="rate_<?php echo $x; ?>" class="form-control" value="<?php echo $val['product_price'] ?>" min="0.1" step="any" onkeyup="getTotal_2(<?php echo $x; ?>)">
                                    <input type="hidden" name="rate_value[]" id="rate_value_<?php echo $x; ?>" class="form-control" value="<?php echo $val['product_price'] ?>">
                                <?php endif; ?>
                              </td>
                              <td>
                                <input type="text" disabled value="<?php if($stock_data_array){ echo $stock_data_array[$x-1]['quantity'];} ?>" name="s_qty_value[]" id="s_qty_value_<?php echo $x; ?>" class="form-control" autocomplete="off">
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
                                <input type="text" name="amount[]" id="amount_<?php echo $x; ?>" class="form-control" disabled value="<?php echo $amount ?>">
                                <input type="hidden" name="amount_value[]" id="amount_value_<?php echo $x; ?>" class="form-control" value="<?php echo $amount ?>">
                              </td>
                              <td>
                                <?php
                                  $isFirstRow = false;
                                  if($x == 1){
                                    $isFirstRow = true;
                                  }
                                  else{
                                    $isFirstRow = false;
                                  }
                                ?>
                                <button type="button" <?php if($isFirstRow){echo "disabled";} ?> class="btn btn-default" onclick="removeRow('<?php echo $x; ?>')"><i class="fa fa-close"></i></button>
                              </td>
                          </tr>
                        <?php $x++; ?>
                      <?php endforeach; ?>
                    <?php endif; ?>
                   </tbody>
                </table>

                <br /> <br/>

                <div class="col-md-6 col-xs-12 pull pull-right">

                  <div class="form-group">
                    <label for="gross_amount" class="col-sm-5 control-label">Gross Amount</label>
                    <div class="col-sm-7">
                      <input type="text" value="<?php echo $sale_order_data['gross_amount'] ?>" class="form-control" id="gross_amount" name="gross_amount" disabled autocomplete="off">
                      <input type="hidden" value="<?php echo $sale_order_data['gross_amount'] ?>" class="form-control" id="gross_amount_value" name="gross_amount_value" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="discount" class="col-sm-5 control-label">Discount</label>
                    <div class="col-sm-7">
                      <input type="text" value="<?php echo $sale_order_data['discount'] ?>" class="form-control" id="discount" name="discount" placeholder="Discount" onkeyup="subAmount()" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="alert alert-success alert-dismissible" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>Be Careful &#128578. Enter plus amount to add and negive to subtract from the bill.
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="affair_loading" class="col-sm-5 control-label">Freight</label>
                    <div class="col-sm-7">
                      <input type="number" step="any" value="<?php echo $sale_order_data['loading_or_affair'] ?>" class="form-control noscroll" name="affair_loading" id="affair_loading" placeholder="Enter Plus or Minus Accordingly" onkeyup="subAmount_3()">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="remarks" class="col-sm-5 control-label">Remarks</label>
                    <div class="col-sm-7">
                      <textarea class="form-control" name="remarks" id="remarks" placeholder="Remarks"><?php echo $sale_order_data['remarks']?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="net_amount" class="col-sm-5 control-label">Net Amount</label>
                    <div class="col-sm-7">
                      <input type="text" value="<?php echo $sale_order_data['net_amount'] ?>" class="form-control" id="net_amount" name="net_amount" disabled autocomplete="off">
                      <input type="hidden" value="<?php echo $sale_order_data['net_amount'] ?>" class="form-control" id="net_amount_value" name="net_amount_value" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="paid_amount" class="required-field col-sm-5 control-label">Received Amount</label>
                    <div class="col-sm-7">
                      <input required type="number" min="1" step=".01" onkeyup="validateReceivedAmount()" value="<?php echo $sale_order_data['paid_amount'] ?>" class="form-control noscroll" id="paid_amount" name="paid_amount" autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="required-field col-sm-5 control-label" for="payment_method">Payment Mthod</label>
                    <div class="col-sm-7">
                      <select class="form-control" required id="payment_method" name="payment_method">
                        <option value="">Select Payment Method</option>
                        <?php 
                          $payment_method = $sale_order_data['payment_method'];
                          $payment_data = $this->Model_payment_method->getPaymentMethodData(); 
                        ?>
                        
                        <?php foreach($payment_data as $key => $value): ?>
                          <option <?php if($payment_method == $value['name']){echo "selected=selected";} ?> value="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="payment_note" class="col-sm-5 control-label">Payment Note</label>
                    <div class="col-sm-7">
                      <input type="text" value="<?php echo $sale_order_data['payment_note'] ?>" class="form-control" id="payment_note" name="payment_note" autocomplete="off" placeholder="Payment Note">
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <a target="__blank" href="<?php echo base_url() . 'index.php/Product/print_sale_invoice/'.$sale_order_data['id'] ?>" class="btn btn-success" >Print</a>
                <a href="<?php echo base_url() . 'index.php/Product/view_sale_order_items/'.$sale_order_data['id'] ?>" class="btn btn-default" >View</a>
                <button type="submit" id="btnCreateOrder" class="btn btn-primary">Update Order</button>
                <a href="<?php echo base_url() ?>index.php/Product/manage_sales" class="btn btn-warning">Back</a>
              </div>
            </form>
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
  var base_url = "<?php echo base_url(); ?>";

  $(document).ready(function() {
    $( ".required-field" ).append('<label style="color:red" for="name">*</label>');
    $("#mainSalesNav").addClass('active');
    var stock_value = "<?php echo $sale_order_data['stock_type'];  ?>";
    if(stock_value){
      $("#select_stock").val(stock_value);
    }

    $("select").select2();
    document.addEventListener("wheel", function(event){
        if(document.activeElement.type === "number" &&
           document.activeElement.classList.contains("noscroll"))
        {
            document.activeElement.blur();
        }
    });

    // Add new row in the table 
    $("#add_row").unbind('click').bind('click', function() {

      var selected_stock = $("#select_stock").val();
      if(selected_stock == 1)
      {
        $("#add_row").attr("disabled", "disabled");

        var table = $("#product_info_table");
        var count_table_tbody_tr = $("#product_info_table tbody tr").length;
        var row_id = count_table_tbody_tr + 1;

        $.ajax({
          url: base_url + 'index.php/Product/getTableFactoryStockData',
          type: 'post',
          dataType: 'json',
          success:function(response) 
          {
              
            var html = '<tr id="row_'+row_id+'">'+
                  '<td>'+ 
                    '<select class="form-control" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" required onchange="setOnProductChange('+row_id+')">'+
                        '<option value=""> -- Select Product -- </option>';
                        console.log(response.data.products);
                        $.each(response.data.products, function(index, value)
                        {
                          if(value.category_name != null)
                          {
                            html += '<option value="'+value.product_id+'-'+value.category_id+'-'+value.unit_id+'">'+value.product_name+''+" &#8212 "+''+value.category_name+" &#8212&#8212&#8212 ("+value.unit_name+")"+'</option>';             
                          }
                          else
                          {
                            html += '<option value="'+value.product_id+'- 0- '+value.unit_id+'">'+value.product_name+" &#8212&#8212&#8212 ("+value.unit_name+")"+'</option>';
                          }
                        });

            html += '</select>'+
                  '</td>'+

                  '<td>'+ 
                    '<select class="form-control" data-row-id="'+row_id+'" id="unit_'+row_id+'" name="unit[]" disabled onchange="setOnUnitChange('+row_id+')">'+
                      '<option value="">Select Unit</option>';
                      $.each(response.data.units_data, function(index, value) {
                        html += '<option value="'+value.id+'">'+value.unit_name+'</option>';             
                      });
                          
            html += '</select>'+
                  '</td>'+ 
                  '<td><input required disabled type="number" min="0.1" step="any" name="qty[]" id="qty_'+row_id+'" class="form-control noscroll" onkeyup="getTotal('+row_id+')"></td>'+
                  '<td><input required disabled type="number" min="0.1" step="any" name="rate[]" id="rate_'+row_id+'" class="form-control noscroll" onkeyup="getTotal_2('+row_id+')"><input type="hidden" name="rate_value[]" id="rate_value_'+row_id+'" class="form-control"></td>'+
                  '<td><input type="text" name="s_qty[]" id="s_qty_'+row_id+'" class="form-control" disabled autocomplete="off"><input type="hidden" name="s_qty_value[]" id="s_qty_value_'+row_id+'" class="form-control" autocomplete="off"></td>'+
                  '<td><input type="text" name="amount[]" id="amount_'+row_id+'" class="form-control" disabled><input type="hidden" name="amount_value[]" id="amount_value_'+row_id+'" class="form-control"></td>'+
                  '<td><button type="button" class="btn btn-default" onclick="removeRow(\''+row_id+'\')"><i class="fa fa-close"></i></button></td>'+
                '</tr>';
              $("#add_row").removeAttr("disabled", "disabled");
              if(count_table_tbody_tr >= 1)
              {
                $("#product_info_table tbody tr:last").after(html);  
              }
              else
              {
                $("#product_info_table tbody").html(html);
              }

            $("select").select2();
          }
        });
      }//if
      else if(selected_stock == 2){
        $("#add_row").attr("disabled", "disabled");

        var table = $("#product_info_table");
        var count_table_tbody_tr = $("#product_info_table tbody tr").length;
        var row_id = count_table_tbody_tr + 1;

        $.ajax({
          url: base_url + 'index.php/Product/getTableOfficeStockItemsData',
          type: 'post',
          dataType: 'json',
          success:function(response) 
          {
              
            var html = '<tr id="row_'+row_id+'">'+
                  '<td>'+ 
                    '<select class="form-control" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" required onchange="setOnProductChange('+row_id+')">'+
                        '<option value=""> -- Select Product -- </option>';
                        console.log(response.data.products);
                        $.each(response.data.products, function(index, value)
                        {
                          if(value.category_name != null)
                          {
                            html += '<option value="'+value.product_id+'-'+value.category_id+'-'+value.unit_id+'">'+value.product_name+''+" &#8212 "+''+value.category_name+" &#8212&#8212&#8212 ("+value.unit_name+")"+'</option>';             
                          }
                          else
                          {
                            html += '<option value="'+value.product_id+'- 0- '+value.unit_id+'">'+value.product_name+" &#8212&#8212&#8212 ("+value.unit_name+")"+'</option>';
                          }
                        });
                          
            html += '</select>'+
                  '</td>'+

                  '<td>'+ 
                    '<select class="form-control" data-row-id="'+row_id+'" id="unit_'+row_id+'" name="unit[]" disabled onchange="setOnUnitChange('+row_id+')">'+
                      '<option value="">Select Unit</option>';
                      $.each(response.data.units_data, function(index, value) {
                        html += '<option value="'+value.id+'">'+value.unit_name+'</option>';             
                      });
                          
            html += '</select>'+
                  '</td>'+ 
                  '<td><input required disabled type="number" min="0.1" step="any" name="qty[]" id="qty_'+row_id+'" class="form-control noscroll" onkeyup="getTotal('+row_id+')"></td>'+
                  '<td><input required disabled type="number" min="0.1" step="any" name="rate[]" id="rate_'+row_id+'" class="form-control noscroll" onkeyup="getTotal_2('+row_id+')"><input type="hidden" name="rate_value[]" id="rate_value_'+row_id+'" class="form-control"></td>'+
                  '<td><input type="text" name="s_qty[]" id="s_qty_'+row_id+'" class="form-control" disabled autocomplete="off"><input type="hidden" name="s_qty_value[]" id="s_qty_value_'+row_id+'" class="form-control" autocomplete="off"></td>'+
                  '<td><input type="text" name="amount[]" id="amount_'+row_id+'" class="form-control" disabled><input type="hidden" name="amount_value[]" id="amount_value_'+row_id+'" class="form-control"></td>'+
                  '<td><button type="button" class="btn btn-default" onclick="removeRow(\''+row_id+'\')"><i class="fa fa-close"></i></button></td>'+
                '</tr>';
              $("#add_row").removeAttr("disabled", "disabled");
              if(count_table_tbody_tr >= 1)
              {
                $("#product_info_table tbody tr:last").after(html);  
              }
              else
              {
                $("#product_info_table tbody").html(html);
              }

            $("select").select2();
          }
        });
      }//else
      return false;
    });

  }); // /document

  function getTotal(row = null) {
    if(row) {
      var total = Number($("#rate_"+row).val()) * Number($("#qty_"+row).val());
      total = total.toFixed(2);
      $("#amount_"+row).val(total);
      $("#amount_value_"+row).val(total);
      subAmount();
    } else {
      alert('no row !! please refresh the page');
    }
  }
  function getTotal_2(row = null) {
    if(row) {
      // set this value for rate_value
      $("#rate_value_"+row).val($("#rate_"+row).val());
      //
      var total = Number($("#rate_"+row).val()) * Number($("#qty_"+row).val());
      total = total.toFixed(2);
      $("#amount_"+row).val(total);
      $("#amount_value_"+row).val(total);
      subAmount();
    } else {
      alert('no row !! please refresh the page');
    }
  }

  function setOnUnitChange(row_id) {
    var unit_id = $('#unit_'+row_id).val();
    if(unit_id == ""){
      $("#rate_"+row_id).val("");
      $("#rate_value_"+row_id).val("");
      $("#rate_"+row_id).prop("disabled", false);
      $("#rate_value_"+row_id).prop("disabled", false);
    }
    else{
      var product_input = $("#product_"+row_id).val().split("-");
      var p_id = product_input[0];
      var c_id = product_input[1];
      var unit_id = $("#unit_"+row_id).val();
      $.ajax({
        url: base_url + 'index.php/Product/getSalePriceDataById',
        type: 'post',
        data: 
        {
          product_id : p_id,
          category_id : c_id,
          unit_id : unit_id
        },
        dataType: 'json',
        success:function(response){
          console.log(response.data);
          if(response.data){
            $("#rate_"+row_id).prop("disabled", true);
            $("#rate_"+row_id).val(response.data.price);
            $("#rate_value_"+row_id).val(response.data.price);
            // to set new amount for bill
            getTotal(row_id);
          }
          else{
            $("#rate_"+row_id).val("");
            $("#rate_value_"+row_id).val("");
            $("#rate_"+row_id).prop("disabled", false);
            // to set new amount for bill
            getTotal(row_id);
          }
        } // /success
      }); // /ajax function to fetch the product data
    }
    
  }

  function setOnProductChange(row_id)
  {
    var product_id = $("#product_"+row_id).val();    
    if(product_id == "") {
      $("#rate_"+row_id).val("");
      $("#rate_value_"+row_id).val("");
      $("#rate_"+row_id).prop("disabled", true);

      $("#unit_"+row_id).val("");
      $("#unit_"+row_id).select2().trigger('change');
      $("#unit_"+row_id).prop("disabled", true);

      $("#qty_"+row_id).val("");           
      $("#qty_value_"+row_id).val("");
      $("#qty_"+row_id).prop("disabled", true);
      $("#qty_value_"+row_id).prop("disabled", true);

      $("#s_qty"+row_id).val("");
      $("#s_qty_value_"+row_id).val("");

      $("#amount_"+row_id).val("");
      $("#amount_value_"+row_id).val("");

    }
    else
    {
      var selected_stock = $("#select_stock").val();
      if(selected_stock == 1){
        var product_input = $("#product_"+row_id).val().split("-");
        var p_id = product_input[0];
        var c_id = product_input[1];
        $.ajax({
          url: base_url + 'index.php/Product/getProductFromFactoryStock',
          type: 'post',
          data: 
          {
            product_id : p_id,
            category_id : c_id
          },
          dataType: 'json',
          success:function(response){
            console.log(response.data);
            if(response.data){
              $("#s_qty_"+row_id).val(response.data.quantity);
              $("#s_qty_value_"+row_id).val(response.data.quantity);
            }

            $("#rate_"+row_id).val("");
            $("#rate_value_"+row_id).val("");
            $("#rate_"+row_id).prop("disabled", false);

            $("#unit_"+row_id).val("");
            $("#unit_"+row_id).select2().trigger('change');
            $("#unit_"+row_id).prop("disabled", false);

            $("#qty_"+row_id).val("");
            $("#qty_value_"+row_id).val("");
            $("#qty_"+row_id).prop("disabled", false);
            $("#qty_value_"+row_id).prop("disabled", false);
          
            $("#amount_"+row_id).val("");
            $("#amount_value_"+row_id).val("");

          } // /success
        }); // /ajax function to fetch the product data
      }
      else if(selected_stock == 2){
        var product_input = $("#product_"+row_id).val().split("-");
        var p_id = product_input[0];
        var c_id = product_input[1];
        $.ajax({
          url: base_url + 'index.php/Product/getProductFromOfficeStock',
          type: 'post',
          data: 
          {
            product_id : p_id,
            category_id : c_id
          },
          dataType: 'json',
          success:function(response){
            console.log(response.data);
            if(response.data){
              $("#s_qty_"+row_id).val(response.data.quantity);
              $("#s_qty_value_"+row_id).val(response.data.quantity);
            }

            $("#rate_"+row_id).val("");
            $("#rate_value_"+row_id).val("");
            $("#rate_"+row_id).prop("disabled", false);

            $("#unit_"+row_id).val("");
            $("#unit_"+row_id).select2().trigger('change');
            $("#unit_"+row_id).prop("disabled", false);

            $("#qty_"+row_id).val("");
            $("#qty_value_"+row_id).val("");
            $("#qty_"+row_id).prop("disabled", false);
            $("#qty_value_"+row_id).prop("disabled", false);
          
            $("#amount_"+row_id).val("");
            $("#amount_value_"+row_id).val("");

          } // /success
        }); // /ajax function to fetch the product data
      }//elseif
    }//else
  }

  function setOnStockChange() 
  {
    var selected_stock = $("#select_stock").val();
    $("#gross_amount").val(0);    
    $("#gross_amount_value").val(0);
    $("#net_amount").val(0);    
    $("#net_amount_value").val(0);
    $("#discount").val(0);
    $("#affair_loading").val(0);    
    if(selected_stock == 1) 
    {
      var table = $("#product_info_table");
      var count_table_tbody_tr = $("#product_info_table tbody tr").length;
      // remove all the rows
      for(var i = 1; i <= count_table_tbody_tr; i++)
      {
        $("#product_info_table tbody tr#row_"+i).remove();
      }

      // create first row with the slected stock
      $("#add_row").attr("disabled", "disabled");

      var table = $("#product_info_table");
      var count_table_tbody_tr = $("#product_info_table tbody tr").length;
      var row_id = count_table_tbody_tr + 1;
      $.ajax({
          url: base_url + 'index.php/Product/getTableFactoryStockData',
          type: 'post',
          dataType: 'json',
          success:function(response) {
            
            var html = '<tr id="row_'+row_id+'">'+
                   '<td>'+ 
                    '<select class="form-control" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" required onchange="setOnProductChange('+row_id+')">'+
                        '<option value=""> -- Select Product -- </option>';
                        console.log(response.data.products);
                        $.each(response.data.products, function(index, value)
                        {
                          if(value.category_name != null)
                          {
                            html += '<option value="'+value.product_id+'-'+value.category_id+'-'+value.unit_id+'">'+value.product_name+''+" &#8212 "+''+value.category_name+" &#8212&#8212&#8212 ("+value.unit_name+")"+'</option>';             
                          }
                          else
                          {
                            html += '<option value="'+value.product_id+'- 0- '+value.unit_id+'">'+value.product_name+" &#8212&#8212&#8212 ("+value.unit_name+")"+'</option>';
                          }
                        });
                        
                html += '</select>'+
                    '</td>'+

                    '<td>'+ 
                    '<select class="form-control" data-row-id="'+row_id+'" id="unit_'+row_id+'" name="unit[]" disabled onchange="setOnUnitChange('+row_id+')">'+
                        '<option value="">Select Unit</option>';
                        $.each(response.data.units_data, function(index, value) {
                          html += '<option value="'+value.id+'">'+value.unit_name+'</option>';             
                        });
                        
                html += '</select>'+
                      '</td>'+ 
                      '<td><input required disabled type="number" min="0.1" step="any" name="qty[]" id="qty_'+row_id+'" class="form-control noscroll" onkeyup="getTotal('+row_id+')"></td>'+
                      '<td><input required disabled type="number" min="0.1" step="any" name="rate[]" id="rate_'+row_id+'" class="form-control noscroll" onkeyup="getTotal_2('+row_id+')"><input type="hidden" name="rate_value[]" id="rate_value_'+row_id+'" class="form-control"></td>'+
                      '<td><input type="text" name="s_qty[]" id="s_qty_'+row_id+'" class="form-control" disabled autocomplete="off"><input type="hidden" name="s_qty_value[]" id="s_qty_value_'+row_id+'" class="form-control" autocomplete="off"></td>'+
                      '<td><input type="text" name="amount[]" id="amount_'+row_id+'" class="form-control" disabled><input type="hidden" name="amount_value[]" id="amount_value_'+row_id+'" class="form-control"></td>'+
                      '<td><button type="button" class="btn btn-default" disabled onclick="removeRow(\''+row_id+'\')"><i class="fa fa-close"></i></button></td>'+
                      '</tr>';
                $("#add_row").removeAttr("disabled", "disabled");
                if(count_table_tbody_tr >= 1)
                {
                  $("#product_info_table tbody tr:last").after(html);  
                }
                else
                {
                  $("#product_info_table tbody").html(html);
                }

              $("select").select2();

          }
        });
    }
    else if(selected_stock == 2) 
    {
      var table = $("#product_info_table");
      var count_table_tbody_tr = $("#product_info_table tbody tr").length;
      for(var i = 1; i <= count_table_tbody_tr; i++)
      {
        $("#product_info_table tbody tr#row_"+i).remove();
      }
      // create first row with the slected stock
      $("#add_row").attr("disabled", "disabled");
      
      var table = $("#product_info_table");
      var count_table_tbody_tr = $("#product_info_table tbody tr").length;
      var row_id = count_table_tbody_tr + 1;

      $.ajax({
          url: base_url + 'index.php/Product/getTableOfficeStockItemsData',
          type: 'post',
          dataType: 'json',
          success:function(response) {
            
            var html = '<tr id="row_'+row_id+'">'+
                   '<td>'+ 
                    '<select class="form-control" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" required onchange="setOnProductChange('+row_id+')">'+
                        '<option value=""> -- Select Product -- </option>';
                        console.log(response.data.products);
                        $.each(response.data.products, function(index, value)
                        {
                          if(value.category_name != null)
                          {
                            html += '<option value="'+value.product_id+'-'+value.category_id+'-'+value.unit_id+'">'+value.product_name+''+" &#8212 "+''+value.category_name+" &#8212&#8212&#8212 ("+value.unit_name+")"+'</option>';             
                          }
                          else
                          {
                            html += '<option value="'+value.product_id+'- 0- '+value.unit_id+'">'+value.product_name+" &#8212&#8212&#8212 ("+value.unit_name+")"+'</option>';
                          }
                        });
                        
                html += '</select>'+
                    '</td>'+

                    '<td>'+ 
                    '<select class="form-control" data-row-id="'+row_id+'" id="unit_'+row_id+'" name="unit[]" disabled onchange="setOnUnitChange('+row_id+')">'+
                        '<option value="">Select Unit</option>';
                        $.each(response.data.units_data, function(index, value) {
                          html += '<option value="'+value.id+'">'+value.unit_name+'</option>';             
                        });
                        
                html += '</select>'+
                      '</td>'+ 
                      '<td><input required disabled type="number" min="0.1" step="any" name="qty[]" id="qty_'+row_id+'" class="form-control noscroll" onkeyup="getTotal('+row_id+')"></td>'+
                      '<td><input required disabled type="number noscroll" min="0.1" step="any" name="rate[]" id="rate_'+row_id+'" class="form-control" onkeyup="getTotal_2('+row_id+')"><input type="hidden" name="rate_value[]" id="rate_value_'+row_id+'" class="form-control"></td>'+
                      '<td><input type="text" name="s_qty[]" id="s_qty_'+row_id+'" class="form-control" disabled autocomplete="off"><input type="hidden" name="s_qty_value[]" id="s_qty_value_'+row_id+'" class="form-control" autocomplete="off"></td>'+
                      '<td><input type="text" name="amount[]" id="amount_'+row_id+'" class="form-control" disabled><input type="hidden" name="amount_value[]" id="amount_value_'+row_id+'" class="form-control"></td>'+
                      '<td><button type="button" class="btn btn-default" disabled onclick="removeRow(\''+row_id+'\')"><i class="fa fa-close"></i></button></td>'+
                      '</tr>';
                $("#add_row").removeAttr("disabled", "disabled");
                if(count_table_tbody_tr >= 1)
                {
                  $("#product_info_table tbody tr:last").after(html);  
                }
                else
                {
                  $("#product_info_table tbody").html(html);
                }

              $("select").select2();

          }
        });
    }
  }

  // calculate the total amount of the order
  function subAmount() {
    
    var tableProductLength = $("#product_info_table tbody tr").length;
    var totalSubAmount = 0;
    for(x = 0; x < tableProductLength; x++) {
      var tr = $("#product_info_table tbody tr")[x];
      var count = $(tr).attr('id');
      count = count.substring(4);

      totalSubAmount = Number(totalSubAmount) + Number($("#amount_"+count).val());
    } // /for

    totalSubAmount = totalSubAmount.toFixed(2);

    // sub total
    $("#gross_amount").val(totalSubAmount);
    $("#gross_amount_value").val(totalSubAmount);

    // total amount
    var totalAmount = Number(totalSubAmount);
    totalAmount = totalAmount.toFixed(2);

    var discount = $("#discount").val();
    if(discount) {
      var grandTotal = Number(totalAmount) - Number(discount);
      grandTotal = grandTotal.toFixed(2);
      $("#net_amount").val(grandTotal);
      $("#net_amount_value").val(grandTotal);
    } else {
      $("#net_amount").val(totalAmount);
      $("#net_amount_value").val(totalAmount);
      
    } // /else discount 

  } // /sub total amount

  function subAmount_3() {

    var tableProductLength = $("#product_info_table tbody tr").length;
    var totalSubAmount = 0;
    for(x = 0; x < tableProductLength; x++) {
      var tr = $("#product_info_table tbody tr")[x];
      var count = $(tr).attr('id');
      count = count.substring(4);

      totalSubAmount = Number(totalSubAmount) + Number($("#amount_"+count).val());
    } // /for

    totalSubAmount = totalSubAmount.toFixed(2);

    // sub total
    $("#gross_amount").val(totalSubAmount);
    $("#gross_amount_value").val(totalSubAmount);

    var discount = $("#discount").val();
    var totalamount = Number(totalSubAmount) - Number(discount);

    var affair_loading = $("#affair_loading").val();
    if(affair_loading) {
      var grandTotal = Number(totalamount) + Number(affair_loading);
      grandTotal = grandTotal.toFixed(2);
      $("#net_amount").val(grandTotal);
      $("#net_amount_value").val(grandTotal);
    } else {
      $("#net_amount").val(totalamount);
      $("#net_amount_value").val(totalamount);
      
    } // /else discount

  } // /sub total amount

  function validateReceivedAmount() {
    var net_amount = Number($("#net_amount").val());
    var received_amount = Number($("#paid_amount").val());
    if(received_amount > net_amount){
      alert("Received Amount should be equal or less than the Net Amount!");
      $('#btnCreateOrder').prop('disabled', true);
    }
    else{
      $('#btnCreateOrder').prop('disabled', false);
    }
    

  }

  function removeRow(tr_id)
  {
    $("#product_info_table tbody tr#row_"+tr_id).remove();
    // to remove that info from the array as well
    $("#rate_value_"+tr_id).val($("#rate_"+tr_id).val());
    subAmount();
  }
</script>
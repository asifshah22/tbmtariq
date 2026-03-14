<?php if($this->session->flashdata('input_data_array')): ?>
  <?php $input_data_array = $this->session->flashdata('input_data_array'); ?> 
<?php endif; ?>
<?php date_default_timezone_set("Asia/Karachi"); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Sale Page
      <small>Trsuted Customers</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Sale Page - Trusted Customers</li>
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
            <h3 class="box-title">Sale Page - Trusted Customers</h3>
          </div>
          <!-- /.box-header -->
          <form role="form" action="<?php echo base_url() ?>index.php/Product/create_company_sale_order" method="post" class="form-horizontal">
              <div class="box-body">

                <?php echo validation_errors(); ?>

                <div class="form-group">
                  <label for="gross_amount" class="col-sm-12 control-label">Date: <?php echo date('Y-m-d') ?></label>
                </div>
                <div class="form-group">
                  <label for="gross_amount" class="col-sm-12 control-label">Time: <?php echo date('h:i a') ?></label>
                </div>

                <div class="col-md-6 col-xs-12 pull pull-right">
                  <div class="form-group">
                    <label for="select_stock" class="col-sm-4 required-field control-label">Selected Stock</label>
                    <div class="col-sm-8">
                      <select class="form-control" name="select_stock" id="select_stock" onchange="setOnStockChange()">
                        <option value="1">Factory Stock</option>
                        <option value="2">Office Stock</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="customer_name" class="col-sm-4 control-label">Full Name</label>
                    <div class="col-sm-8">
                      <input required="true" disabled type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Full Name" autocomplete="off" value="<?php if($input_data_array){ echo $input_data_array['customer_name'];} ?>"/>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="customer_deparment" class="col-sm-4 control-label">Department</label>
                    <div class="col-sm-8">
                      <input required="true" disabled type="text" class="form-control" id="customer_deparment" name="customer_deparment" placeholder="Department" autocomplete="off" value="<?php if($input_data_array){ echo $input_data_array['customer_deparment'];} ?>"/>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="customer_address" class="col-sm-4 control-label">Address</label>
                    <div class="col-sm-8">
                      <input type="text" disabled required="true" class="form-control" id="customer_address" name="customer_address" placeholder="Customer Address" autocomplete="off" value="<?php if($input_data_array){ echo $input_data_array['customer_address'];} ?>">
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="customer_contact" class="col-sm-4 control-label">Contact</label>
                    <div class="col-sm-8">
                      <input type="text" disabled required="true" class="form-control" id="customer_contact" name="customer_contact" placeholder="Contact Info" autocomplete="off" value="<?php if($input_data_array){ echo $input_data_array['customer_contact'];} ?>">
                    </div>
                  </div>
                </div>

                <div class="col-md-6 col-xs-12 pull pull-left">
                  <div class="form-group">
                    <label for="trusted_customer" class="col-sm-4 required-field control-label">Select Customer</label>
                    <div class="col-sm-8">
                      <select required name="trusted_customer" id="trusted_customer" onchange="getCustomerData()" class="form-control">
                        <option value="">Select Trusted Customers</option>
                        <?php foreach ($customers as $k => $v): ?>
                          <?php if($input_data_array): ?>
                              <?php foreach($customer_deparment as $key => $value): ?>
                                <?php 
                                  $deparment_name = '';
                                  if($v['id'] == $value['customer_id']):?>
                                    <?php 
                                      $deparment_id = $value['department_table_id'];
                                      $deparment_data = $this->Model_department->getDepartmentData($deparment_id);
                                      $deparment_name = $deparment_data['department_name'];
                                    ?>
                                    <?php if(explode('-', $input_data_array['input_customer'])[0] == $v['id']): ?>
                                      <option style="text-transform: capitalize;" value="<?php echo $v['id'].'-'.$deparment_id; ?>" <?php echo "selected='selected'"; ?>><?php echo $v['full_name']. ' &#8212 ' . $deparment_name ?></option>
                                      <?php else: ?>
                                        <option style="text-transform: capitalize;" value="<?php echo $v['id'].'-'.$deparment_id; ?>"><?php echo $v['full_name']. ' &#8212 ' . $deparment_name ?></option>
                                    <?php endif; ?>
                                  <?php endif; ?>
                              <?php endforeach; ?>
                            <?php else: ?>
                              <?php foreach($customer_deparment as $key => $value): ?>
                                <?php
                                  $deparment_name = '';
                                  if($v['id'] == $value['customer_id']):
                                ?>
                                    <?php
                                      $deparment_id = $value['department_table_id'];
                                      $deparment_data = $this->Model_department->getDepartmentData($deparment_id);
                                      $deparment_name = $deparment_data['department_name'];
                                    ?>
                                    <option style="text-transform: capitalize;" value="<?php echo $v['id'].'-'.$deparment_id; ?>"><?php echo $v['full_name']. ' &#8212 ' . $deparment_name ?></option>
                                  <?php endif; ?>
                              <?php endforeach; ?>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>
                
                
                <br /> <br/>
                <table class="table table-bordered" id="product_info_table">
                  <thead>
                    <tr>
                      <th style="width:40%" class="required-field">Product</th>
                      <th style="width:30%">Unit</th>
                      <th style="width:10%" class="required-field">Qty</th>
                      <th style="width:10%">S.Qty</th>
                      <th style="width:10%"><button type="button" id="add_row" class="btn btn-default"><i class="fa fa-plus"></i></button></th>
                    </tr>
                  </thead>

                   <tbody>
                     <?php if($input_data_array): ?>
                      <?php $x = 1; ?>
                      <?php for ($i = 0; $i < count($input_data_array['input_products']); $i++): ?>
                        
                        <tr id="row_<?php echo $x; ?>">
                         <td>
                          <select style="width: 100%" class="form-control" data-row-id="row_<?php echo $x; ?>" id="product_<?php echo $x; ?>" name="product[]" onchange="setOnProductChange(<?php echo $x; ?>)" required="true">
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
                                <?php if($input_data_array['input_products'][$i] == $value): ?>
                                  <option value="<?php echo $value; ?>" <?php echo "selected='selected'"; ?>>
                                    <?php echo $v['product_name']. ' ' . $category_name.' &#8212&#8212&#8212 ('.$unit_name.')' ?>
                                  </option>
                                <?php else: ?>
                                  <option value="<?php echo $value ?>"><?php echo $v['product_name']. ' ' . $category_name.' &#8212&#8212&#8212 ('.$unit_name.')' ?>
                                  </option>
                                <?php endif; ?>
                              <?php endforeach ?>
                            </select>
                          </td>
                          <td>
                          <select style="width: 100%" class="form-control" data-row-id="row_<?php echo $x; ?>" id="unit_<?php echo $x; ?>" name="unit[]">
                              <option value="">Select Unit</option>
                              <?php foreach ($units_data as $key => $value): ?>
                                <?php
                                  $unit_id = 0;
                                  $unit_name = '';
                                  if($input_data_array['input_units'][$i] == $value['id'])
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
                              <?php endforeach ?>
                            </select>
                          </td>
                          <td>  
                            <input type="number" value="<?php echo $input_data_array['input_qty'][$i]; ?>" min="0.1" step="any" name="qty[]" id="qty_<?php echo $x; ?>" class="form-control noscroll" required onkeyup="getTotal(<?php echo $x; ?>)">
                          </td>
                          <td>
                            <input type="text" value="<?php echo $input_data_array['input_s_qty'][$i]; ?>" name="s_qty[]" id="s_qty_<?php echo $x; ?>" class="form-control" disabled autocomplete="off">
                            <input type="hidden" value="<?php echo $input_data_array['input_s_qty'][$i]; ?>" name="s_qty_value[]" id="s_qty_value_<?php echo $x; ?>" class="form-control" autocomplete="off">
                          </td>
                          <td><button <?php if($x == 1){echo "disabled";} ?> type="button" class="btn btn-default" onclick="removeRow(<?php echo $x; ?>)"><i class="fa fa-close"></i></button></td>
                        </tr>
                        <?php $x++; ?>
                      <?php endfor; ?>
                    <?php else: ?>
                     <tr id="row_1">
                       <td>
                        <select style="width: 100%" class="form-control" data-row-id="row_1" id="product_1" name="product[]" onchange="setOnProductChange(1)" required="true">
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
                              <option value="<?php echo $value ?>"><?php echo $v['product_name']. ' ' . $category_name.' &#8212&#8212&#8212 ('.$unit_name.')' ?></option>
                            <?php endforeach ?>
                          </select>
                        </td>
                        <td>
                        <select style="width: 100%" class="form-control" data-row-id="row_1" id="unit_1" name="unit[]" disabled>
                            <option value="">Select Unit</option>
                            <?php foreach ($units_data as $key => $value): ?>
                              <option value="<?php echo $value['id'] ?>"><?php echo $value['unit_name']; ?></option>
                            <?php endforeach ?>
                          </select>
                        </td>
                        <td><input disabled type="number" min="0.1" step="any" name="qty[]" id="qty_1" class="form-control noscroll" required onkeyup="getTotal(1)"></td>
                        <td>
                          <input type="text" name="s_qty[]" id="s_qty_1" class="form-control" disabled autocomplete="off">
                          <input type="hidden" name="s_qty_value[]" id="s_qty_value_1" class="form-control" autocomplete="off">
                        </td>
                        <td><button disabled type="button" class="btn btn-default" onclick="removeRow('1')"><i class="fa fa-close"></i></button></td>
                     </tr>
                    <?php endif; ?>
                    
                   </tbody>
                </table>
                <div class="col-md-4">
                  <textarea style="resize:none" placeholder="Remarks" rows="5" name="remarks" id="remarks" class="form-control" /><?php if(!empty($input_data_array)){ print_r($input_data_array['remarks']); } ?></textarea>
                </div>

                <br /> <br/>

              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Create Order</button>
                <a href="<?php echo base_url() ?>index.php/Product/company_sales" class="btn btn-warning">Back</a>
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
    $("#salesPageTrustedCustomerNav").addClass('active');

    var stock_value = "<?php if($input_data_array) { echo $input_data_array['input_selected_stock']; } ?>";
    if(stock_value){
      $("#select_stock").val(stock_value);
    }
    $('select').select2();
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
      if(selected_stock == 1){
        
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
                      '<select style="width: 100%" class="form-control" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" required="true" onchange="setOnProductChange('+row_id+')">'+
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
                      '<select style="width: 100%" class="form-control" data-row-id="'+row_id+'" id="unit_'+row_id+'" name="unit[]" disabled>'+
                          '<option value="">Select Unit</option>';
                          $.each(response.data.units_data, function(index, value) {
                            html += '<option value="'+value.id+'">'+value.unit_name+'</option>';             
                          });
                          
                  html += '</select>'+
                        '</td>'+ 
                        '<td><input required disabled type="number" min="0.1" step="any" name="qty[]" id="qty_'+row_id+'" class="form-control noscroll" onkeyup="getTotal('+row_id+')"></td>'+
                        '<td><input type="text" name="s_qty[]" id="s_qty_'+row_id+'" class="form-control" disabled autocomplete="off"><input type="hidden" name="s_qty_value[]" id="s_qty_value_'+row_id+'" class="form-control" autocomplete="off"></td>'+
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

                  $('select').select2();

            }
          });
      }
      else if(selected_stock == 2){

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
                     '<select style="width: 100%" class="form-control" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" required="true" onchange="setOnProductChange('+row_id+')">'+
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
                      '<select style="width: 100%" class="form-control" data-row-id="'+row_id+'" id="unit_'+row_id+'" name="unit[]" disabled>'+
                          '<option value="">Select Unit</option>';
                          $.each(response.data.units_data, function(index, value) {
                            html += '<option value="'+value.id+'">'+value.unit_name+'</option>';             
                          });
                          
                  html += '</select>'+
                        '</td>'+ 
                        '<td><input required disabled type="number" min="0.1" step="any" name="qty[]" id="qty_'+row_id+'" class="form-control noscroll" onkeyup="getTotal('+row_id+')"></td>'+
                        '<td><input type="text" name="s_qty[]" id="s_qty_'+row_id+'" class="form-control" disabled autocomplete="off"><input type="hidden" name="s_qty_value[]" id="s_qty_value_'+row_id+'" class="form-control" autocomplete="off"></td>'+
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

                  $('select').select2();

            }
          });
      }//elseif

      return false;
    });

  }); // /document

  function setOnProductChange(row_id)
  {
    var product_id = $("#product_"+row_id).val();    
    if(product_id == "") {
      $("#unit_"+row_id).val("");
      $("#unit_"+row_id).select2().trigger('change');
      $("#unit_"+row_id).prop("disabled", true);

      $("#s_qty_"+row_id).val("");           
      $("#s_qty_value_"+row_id).val("");      

      $("#qty_"+row_id).val("");           
      $("#qty_value_"+row_id).val("");
      $("#qty_"+row_id).prop("disabled", true);
      $("#qty_value_"+row_id).prop("disabled", true);
    }
    else
    {
      var selected_stock = $("#select_stock").val();
      if(selected_stock == 1){

        var product_input = $("#product_"+row_id).val().split("-");
        var p_id = product_input[0];
        var c_id = product_input[1];
        var u_id = product_input[2];
        $.ajax({
          url: base_url + 'index.php/Product/getProductFromFactoryStock',
          type: 'post',
          data: 
          {
            product_id : p_id,
            category_id : c_id,
            unit_id : u_id
          },
          dataType: 'json',
          success:function(response){
            console.log(response.data);
            if(response.data){
              $("#s_qty_"+row_id).val(response.data.quantity);
              $("#s_qty_value_"+row_id).val(response.data.quantity);
            }

            $("#unit_"+row_id).val("");
            $("#unit_"+row_id).select2().trigger('change');
            $("#unit_"+row_id).prop("disabled", false);

            $("#qty_"+row_id).val("");
            $("#qty_value_"+row_id).val("");
            $("#qty_"+row_id).prop("disabled", false);
            $("#qty_value_"+row_id).prop("disabled", false);
          } // /success
        }); // /ajax function to fetch the product data  
      }
      else if(selected_stock == 2){

        var product_input = $("#product_"+row_id).val().split("-");
        var p_id = product_input[0];
        var c_id = product_input[1];
        var u_id = product_input[2];
        $.ajax({
          url: base_url + 'index.php/Product/getProductFromOfficeStock',
          type: 'post',
          data: 
          {
            product_id : p_id,
            category_id : c_id,
            unit_id : u_id
          },
          dataType: 'json',
          success:function(response){
            console.log(response.data);
            if(response.data){
              $("#s_qty_"+row_id).val(response.data.quantity);
              $("#s_qty_value_"+row_id).val(response.data.quantity);
            }

            $("#unit_"+row_id).val("");
            $("#unit_"+row_id).select2().trigger('change');
            $("#unit_"+row_id).prop("disabled", false);

            $("#qty_"+row_id).val("");
            $("#qty_value_"+row_id).val("");
            $("#qty_"+row_id).prop("disabled", false);
            $("#qty_value_"+row_id).prop("disabled", false);
          } // /success
        }); // /ajax function to fetch the product data  
      }//elseif
    }//else
  }

  function getCustomerData() {
    var trusted_customer = $("#trusted_customer").val();
    if(trusted_customer){
      var customer_id = trusted_customer.split("-")[0];
      var deparment_id = trusted_customer.split("-")[1];
      $.ajax({
        url: base_url + 'index.php/Product/getCustomerDepartmentData',
        type: 'post',
        data: 
        {
          customer_id: customer_id,
          department_id: deparment_id
        },
        dataType: 'json',
        success:function(response) {
          // console.log(response.data.customer_data);
          $("#customer_name").val(response.data.customer_data.full_name);
          $("#customer_deparment").val(response.data.department_data.department_name);
          $("#customer_address").val(response.data.customer_data.address);
          $("#customer_contact").val(response.data.customer_data.phone_number);
        }
      });
    }else{
      $("#customer_name").val("");
      $("#customer_deparment").val("");
      $("#customer_address").val("");
      $("#customer_contact").val("");
    }
  }

  function setOnStockChange() 
  {
    var selected_stock = $("#select_stock").val();  
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
                    '<select style="width: 100%" class="form-control" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" required="true" onchange="setOnProductChange('+row_id+')">'+
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
                    '<select style="width: 100%" class="form-control" data-row-id="'+row_id+'" id="unit_'+row_id+'" name="unit[]" disabled>'+
                        '<option value="">Select Unit</option>';
                        $.each(response.data.units_data, function(index, value) {
                          html += '<option value="'+value.id+'">'+value.unit_name+'</option>';             
                        });
                        
                html += '</select>'+
                      '</td>'+ 
                      '<td><input required disabled type="number" min="0.1" step="any" name="qty[]" id="qty_'+row_id+'" class="form-control noscroll" onkeyup="getTotal('+row_id+')"></td>'+
                      '<td><input type="text" name="s_qty[]" id="s_qty_'+row_id+'" class="form-control" disabled autocomplete="off"><input type="hidden" name="s_qty_value[]" id="s_qty_value_'+row_id+'" class="form-control" autocomplete="off"></td>'+
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
                    '<select style="width: 100%" class="form-control" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" required="true" onchange="setOnProductChange('+row_id+')">'+
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
                    '<select style="width: 100%" class="form-control" data-row-id="'+row_id+'" id="unit_'+row_id+'" name="unit[]" disabled>'+
                        '<option value="">Select Unit</option>';
                        $.each(response.data.units_data, function(index, value) {
                          html += '<option value="'+value.id+'">'+value.unit_name+'</option>';             
                        });
                        
                html += '</select>'+
                      '</td>'+ 
                      '<td><input required disabled type="number" min="0.1" step="any" name="qty[]" id="qty_'+row_id+'" class="form-control noscroll" onkeyup="getTotal('+row_id+')"></td>'+
                      '<td><input type="text" name="s_qty[]" id="s_qty_'+row_id+'" class="form-control" disabled autocomplete="off"><input type="hidden" name="s_qty_value[]" id="s_qty_value_'+row_id+'" class="form-control" autocomplete="off"></td>'+
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

  function removeRow(tr_id)
  {
    $("#product_info_table tbody tr#row_"+tr_id).remove();
  }
</script>
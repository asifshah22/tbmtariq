
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Sales
      <small>Edit Office Stock Transfer</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Edit Office Stock Transfer</li>
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
            <h3 class="box-title">Edit Office Stock Transfer</h3>
          </div>
          <!-- /.box-header -->
          <form role="form" action="<?php echo base_url() ?>index.php/Product/update_office_stock/<?php echo $this->uri->segment(3)?>" method="post" class="form-horizontal">
              <div class="box-body">

                <?php echo validation_errors(); ?>
                <table class="table table-bordered" id="product_info_table">
                  <thead>
                    <tr>
                      <th style="width: 30%" class="required-field">Factory Stock Items</th>
                      <th style="width: 30%" class="required-field">Unit</th>
                      <th class="required-field">Qty</th>
                      <th>Factory Stock Qty</th>
                      <th><button type="button" id="add_row" class="btn btn-default"><i class="fa fa-plus"></i></button></th>
                    </tr>
                  </thead>

                   <tbody>
                     <?php if(isset($transfered_data)): ?>
                      <?php $x = 1; ?>
                      <?php foreach ($transfered_data as $key => $val): ?>
                      <tr id="row_<?php echo $x; ?>">
                        <td>
                          <select class="form-control" data-row-id="row_<?php echo $x; ?>" id="product_<?php echo $x; ?>" name="product[]" onchange="setOnProductChange(<?php echo $x; ?>)" required="true">
                            <option value="">Select Product</option>
                            <?php foreach ($products as $k => $v): ?>
                              <?php
                                $unit_name = $this->Model_products->getUnitsData($v['unit_id'])['unit_name'];
                                $category_name = '';
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
                              
                              <?php if($val['product_id'] == $v['product_id'] && $val['category_id'] == $v['category_id'] && $val['factory_stock_unit_id'] == $v['unit_id']): ?>
                                <option value="<?php echo $value; ?>" <?php echo "selected='selected'"; ?> > <?php echo $v['product_name']. ' ' . $category_name.' &#8212&#8212&#8212 ('.$unit_name.')' ?>
                                </option>
                              <?php else: ?>
                                <option value="<?php echo $value ?>"><?php echo $v['product_name']. ' ' . $category_name.' &#8212&#8212&#8212 ('.$unit_name.')' ?>
                                </option>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </select>
                        </td>

                        <td>
                          <select style="width: 100%" class="form-control" data-row-id="row_<?php echo $x; ?>" id="unit_<?php echo $x; ?>" name="unit[]" required="true" onchange="setOnUnitChange(<?php echo $x; ?>)">
                            <option value=""> -- Select Unit -- </option>
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
                            <?php endforeach ?>
                          </select>
                        </td>
                        
                        <td>
                          <input type="number" min="0.1" step="any" name="qty[]" id="qty_<?php echo $x; ?>" class="form-control noscroll" value="<?php echo $val['quantity']; ?>">
                        </td>
                        <td>
                          <input type="text" readonly value="<?php if($stock_data_array){ echo $stock_data_array[$x-1]['quantity'];} ?>" name="s_qty_value[]" id="s_qty_value_<?php echo $x; ?>" class="form-control" autocomplete="off">
                        </td>
                              
                              <td>
                                <button type="button" class="btn btn-default" <?php if($x == 1){echo "disabled";} ?> onclick="removeRow('<?php echo $x; ?>')"><i class="fa fa-close"></i></button>
                              </td>
                          </tr>
                        <?php $x++; ?>
                      <?php endforeach; ?>
                    <?php endif; ?>
                   </tbody>
                </table>

                <br /> <br/>

              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update Order</button>
                <a href="<?php echo base_url() ?>index.php/Product/manage_office_stock" class="btn btn-warning">Back</a>
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

    $("#mainStockNav").addClass('active');
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
                    '<select class="form-control" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" required="true" onchange="setOnProductChange('+row_id+')">'+
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
                      '<select style="width: 100%" class="form-control" data-row-id="'+row_id+'" id="unit_'+row_id+'" name="unit[]" required="true" disabled onchange="setOnUnitChange('+row_id+')">'+
                          '<option value=""> -- Select Unit -- </option>';
                          $.each(response.data.units_data, function(index, value) {
                            html += '<option value="'+value.id+'">'+value.unit_name+'</option>';
                          });
              html += '</select>'+
                    '</td>'+
                    '<td><input required disabled type="number" min="0.1" step="any" name="qty[]" id="qty_'+row_id+'" class="form-control noscroll"></td>'+
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

              $("#product_"+row_id).select2();
              $("#unit_"+row_id).select2();

          }
        });

      return false;
    });

  }); // /document

  function setOnProductChange(row_id)
  {
    var product_id = $("#product_"+row_id).val();    
    if(product_id == "") {
      
      $("#s_qty_"+row_id).val("");           
      $("#s_qty_value_"+row_id).val("");

      $("#unit_"+row_id).val("");
      $("#unit_"+row_id).select2().trigger('change');      
      $("#unit_"+row_id).prop("disabled", true);
    }
    else
    {
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
        } // /success
      }); // /ajax function to fetch the product data  
    }
  }

  function setOnUnitChange(row_id) {
    var unit_id = $('#unit_'+row_id).val();
    if(unit_id == ""){

      $("#qty_"+row_id).val("");           
      $("#qty_value_"+row_id).val("");
      $("#qty_"+row_id).prop("disabled", true);
      $("#qty_value_"+row_id).prop("disabled", true);
    }
    else{
      $("#qty_"+row_id).val("");
      $("#qty_value_"+row_id).val("");
      $("#qty_"+row_id).prop("disabled", false);
      $("#qty_value_"+row_id).prop("disabled", false);
    }
  }

  function removeRow(tr_id)
  {
    $("#product_info_table tbody tr#row_"+tr_id).remove();
  }
</script>
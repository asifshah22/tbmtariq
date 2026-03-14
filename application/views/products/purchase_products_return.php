<?php date_default_timezone_set("Asia/Karachi"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Purchasing
      <small>Purchase Order Return</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Purchase Product Return</li>
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
            <?php elseif(validation_errors()): ?>
              <div class="alert alert-error alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <?php echo validation_errors(); ?>
              </div>
            <?php endif; ?>


            <div class="box">
              <div class="box-header">
                <h3 class="box-title">Purchase Product Return</h3>
              </div>
              <!-- /.box-header -->
              <form role="form" action="<?php echo base_url() ?>index.php/Product/add_purchase_products_return/<?php echo $this->uri->segment(3)?>" method="post"  class="form-horizontal" id="input_form">
               <div class="box-body">
                <div class="form-group">
                 <label class="col-sm-12 control-label">Date: <?php echo date('Y-m-d') ?></label>
               </div>
               <div class="col-sm-6">
                 
                 <label style="text-transform: capitalize; text-decoration: underline;" class="control-label">  <p>Vendor Name: <?php echo $vendor_data['first_name']. ' ' . $vendor_data['last_name']; ?></p>
                 </label>
                 <input type="hidden" name="vendor_id" id="vendor_id" value="<?php echo $vendor_data['id']; ?>">
               </div>
               <div class="form-group">
                 <label class="col-sm-6 control-label">Time: <?php echo date('h:i a') ?></label>
               </div>

               <br /> <br/>
               <table class="table table-bordered" id="product_info_table">
                 <thead>
                  <tr>
                   <th style="width:20%">Product</th>
                   <th style="width: 15%">Unit</th>
                   <th style="width:10%">Qty</th>
                   <th style="width: 10%">Return Qty <a href="#" data-toggle="tooltip" title="This return quantity is without units. Enter a number accordingly."><i class="fa fa-question-circle"></i></a> 
                   </th>
                   <th style="width:20%">Reason</th>
                   <th style="width:13%">Price</th>
                   <th style="width:12%">Amount</th>
                 </tr>
               </thead>

               <tbody>
                <?php if(isset($purchase_items_data)): ?>
                 <?php $x = 1; ?>
                 <?php $i = 0; ?>
                 <?php $selectedProduct = 0;$selectedUnit = 0; ?>
                 <?php foreach ($purchase_items_data as $key => $val): ?>
                  <tr id="row_<?php echo $x; ?>">
                   <td>
                    <select class="form-control" data-row-id="<?php echo $x; ?>" id="product_<?php echo $x; ?>" name="product[]">
                     <?php foreach ($vendor_products as $k => $v): ?>
                      <?php
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
                      <!-- select_product_price = product_id -->
                      <?php if($val['product_id'] == $v['select_product_price']): ?>
                        <?php $selectedProduct = $v['select_product_price']; ?>
                        <option value="<?php echo $v['select_product_price'].'-'.$category_id ?>" <?php echo "selected='selected'"; ?>><?php echo $v['product_name']. ' ' . $category_name ?>
                     </option>
                   <?php endif; ?>
                 <?php endforeach; ?>
               </select>
             </td>
             <?php
                $unit_name = "";
                $unit_id = 0;
                foreach ($units_data as $unit)
                {
                  if($unit['id'] == $val['unit_id']){
                    $unit_name = $unit['unit_name'];
                    $unit_id = $unit['id'];
                    $selectedUnit = $unit_id;
                  }
                } 
              ?>
            <td>
              <select name="selected_unit[]" id="selected_unit_<?php echo $x; ?>" class="form-control">
                <option value="<?php echo $unit_id; ?>" <?php echo "selected='selected'";?> ><?php echo $unit_name; ?></option>
              </select>
            </td>
            <td>  
              <input disabled type="number" min="0.1" step="any" name="qty[]" id="qty_<?php echo $x; ?>" class="form-control noscroll" value="<?php echo $val['qty']; ?>">
            </td>
              <?php if($purchase_returns_data): ?>
                <?php 
                  $countRows = count($purchase_returns_data);
                  $count = 0; 
                ?>

                <?php foreach ($purchase_returns_data as $returnKey => $returnValue):?>
                  <?php $count = $count + 1; ?>
                  <?php if(in_array($selectedProduct, $returnValue) && in_array($selectedUnit, $returnValue)): ?>
                    <td>
                      <input readonly type="number" min="0.1" step="any" onkeyup="getTotal(<?php echo $x; ?>)" name="return_qty[]" id="return_qty_<?php echo $x; ?>" class="form-control noscroll">
                    </td>
                    <td>
                      <textarea rows="1" readonly class="form-control" name="reason[]" id="reason_<?php echo $x?>" placeholder="Reason of Return"></textarea>
                    </td>
                    <?php $count = 0; ?>
                    <?php break; ?>
                  <?php endif; ?>
                <?php endforeach; ?>
                <?php if($countRows == $count): ?>
                  <td>
                    <input type="number" min="0.1" step="any" onkeyup="getTotal(<?php echo $x; ?>)" name="return_qty[]" id="return_qty_<?php echo $x; ?>" class="form-control noscroll">
                  </td>
                  <td>
                    <textarea rows="1" class="form-control" name="reason[]" id="reason_<?php echo $x?>" placeholder="Reason of Return"></textarea>
                  </td>  
                <?php endif; ?>
              <?php else: ?>
                <td>
                  <input type="number" min="0.1" step="any" onkeyup="getTotal(<?php echo $x; ?>)" name="return_qty[]" id="return_qty_<?php echo $x; ?>" class="form-control noscroll">
                </td>
                <td>
                  <textarea rows="1" class="form-control" name="reason[]" id="reason_<?php echo $x?>" placeholder="Reason of Return"></textarea>
                </td>  
              <?php endif; ?>
              <?php 
                $unit_product_price = 0;
                foreach ($unit_data_values as $key => $value) {
                  if($value['unit_id'] == $val['unit_id'])
                  {
                  // product price for one qty
                    $unit_product_price = $val['product_price'];
                    break;
                  }
                }
                ?>
                <?php
                 $qty = 0; 
                 if($val['unit_id'])
                 { 
                  foreach ($unit_data_values as $key => $value) {
                    if($value['unit_id'] == $val['unit_id']){
                      $qty = $val['qty'] * $value['unit_value'];
                      break;
                    }
                  }
                }
                else
                {
                  $qty = $val['qty'];
                } 
                ?>
                <td>  
                  <input disabled type="number" min="0.1" step="any" name="price[]" id="price_<?php echo $x; ?>" class="form-control noscroll" value="<?php echo $unit_product_price; ?>">
                </td>
                <td>
                  <input disabled type="number" name="amount[]" id="amount_<?php echo $x?>" class="form-control noscroll">
                  <input type="hidden" name="amount_value[]" id="amount_value_<?php echo $x?>" class="form-control">
                  <!-- to set the value of r.amount -->
                  <input type="hidden" name="unit_product_price[]" id="unit_product_price_<?php echo $x?>" value="<?php echo $unit_product_price;  ?>">
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
          <label for="net_amount" class="col-sm-5 control-label">Net Amount</label>
          <div class="col-sm-7">
            <input type="text" class="form-control" id="net_amount" name="net_amount" disabled autocomplete="off">
            <input type="hidden" class="form-control" id="net_amount_value" name="net_amount_value" autocomplete="off">
          </div>
        </div>
      </div>
    </div>
    <!-- /.box-body -->

    <div class="box-footer" id="footer">
      <a href="<?php echo base_url() ?>index.php/Product/mark_purchase_order_returns/<?php echo $this->uri->segment(3); ?>" class="btn btn-warning">Back</a>
      <button id="btnCreateReturn" type="submit" class="btn btn-primary">Add Return</button>
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

  $(document).ready(function() {
    $("#mainPurchasingNav").addClass('active');
    $("select").select2();
    document.addEventListener("wheel", function(event){
        if(document.activeElement.type === "number" &&
           document.activeElement.classList.contains("noscroll"))
        {
            document.activeElement.blur();
        }
    });
    var tableProductLength = $("#product_info_table tbody tr").length;
    var isReturned = 0;
    for(x = 0; x < tableProductLength; x++) {
      var tr = $("#product_info_table tbody tr")[x];
      var count = $(tr).attr('id');
      count = count.substring(4);
      $("#return_qty_"+count).val('');
      $("#reason_"+count).val('');
      if($("#return_qty_"+count)[0].readOnly){
        isReturned = isReturned + 1;
      }
    } // /for to clear input fields
    // form validation
    if(isReturned === tableProductLength){
      $('#btnCreateReturn').attr("disabled", "disabled");
      alert('Phoooo! You already have marked the return for this Order. Wanna change them? You can do this by Editing the Return Items 😊');
    }
  });

  function getTotal(row = null) {
    if(row) {
      var total = (Number($("#return_qty_"+row).val()) * Number($("#unit_product_price_"+row).val()));
      total = total.toFixed(2);
      $("#amount_"+row).val(total);
      $("#amount_value_"+row).val(total);
      
      subAmount();

    } else {
      alert('no row !! please refresh the page');
    }
  }

  function subAmount() {

    var tableProductLength = $("#product_info_table tbody tr").length;
    var totalSubAmount = 0;
    for(x = 0; x < tableProductLength; x++) {
      var tr = $("#product_info_table tbody tr")[x];
      var count = $(tr).attr('id');
      count = count.substring(4);

      totalSubAmount = Number(totalSubAmount) + Number($("#amount_"+count).val());
    } // /for

    totalAmount = totalSubAmount.toFixed(2);
    $("#net_amount").val(totalAmount);
    $("#net_amount_value").val(totalAmount);

  } // /sub total amount
</script>
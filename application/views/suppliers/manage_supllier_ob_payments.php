

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Vendor Payments</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Vendor Payments</li>
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
          <?php elseif($this->session->flashdata('error')): ?>
            <div class="alert alert-error alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo $this->session->flashdata('error'); ?>
            </div>
          <?php endif; ?>

          <?php if(in_array('createVendorBalancePayments', $user_permission)): ?>  
            <button title="Add Vendor Payment" class="btn btn-success" data-toggle="modal" data-target="#addSupplierOBPaymentModal"><i class="glyphicon glyphicon-plus"></i></button>
          <?php endif; ?>
          <?php if(in_array('printVendorBalancePayments', $user_permission)): ?> 
            <a title="Print Vendor Payments" target="__blank" href="<?php echo base_url() ?>index.php/Supplier/print_supplier_ob_payment" class="btn btn-info" id="print">
              <i class="glyphicon glyphicon-print"></i>
            </a>
          <?php endif; ?>
          <?php if( ( in_array('createVendorBalancePayments', $user_permission) ) || ( in_array('printVendorBalancePayments', $user_permission) ) ): ?>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Payments</h3>
            <div class="pull-right">
              <form method="POST" class="form-inline" id="adjustProductRateForm">
                <div class="form-group">
                  <select class="form-control" id="selected_vendor" name="selected_vendor">
                    <option value=""> --- Select Vendor ... -- </option>
                    <?php $vendors_data = $this->Model_supplier->getSupplierData(); ?>
                    <?php foreach($vendors_data as $key => $value): ?>
                      <?php 
                      $selected = "";
                      if(isset($_GET['selected_vendor'])){
                        if($_GET['selected_vendor'] == $value['id']){
                          $selected = "selected";
                        }
                        else{
                          $selected = "";
                        }
                      }
                      ?>
                      <option <?php if(!empty($selected)){echo $selected;} ?> value="<?php echo $value['id'] ?>"><?php echo $value['first_name'].' '.$value['last_name']; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <a href="<?php echo base_url() ?>index.php/Supplier/print_supplier_ob_payment<?php if(isset($_GET['selected_vendor'])){ echo "?selected_vendor=".$_GET['selected_vendor']; } ?>" target="__blank" class="btn btn-success btn-sm btn-flat" id="print"><span class="glyphicon glyphicon-print"></span> Print</a>
              </form>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th width="3%">#</th>
                  <th style="color:#3c8dbc">DateTime</th>
                  <th style="color:#3c8dbc">Supplier</th>
                  <th style="color:#3c8dbc">Paid Amount</th>
                  <th style="color:#3c8dbc">Note</th>
                  <th style="color:#3c8dbc">Paid By</th>
                  <?php if( in_array('updateVendorBalancePayments', $user_permission) || in_array('viewVendorBalancePayments', $user_permission) || in_array('deleteVendorBalancePayments', $user_permission) ): ?>
                  <th width="7%"></th>
                <?php endif; ?>

              </tr>
            </thead>
            <tbody>
              
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

<!-- create supplier modal -->
<div class="modal fade" role="dialog" id="addSupplierOBPaymentModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Pay OB Payment</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Supplier/create_supplier_ob_payment" method="post" id="createSupplierOBPaymentForm">

        <div class="modal-body">
          <div class="form-group">
            <label class="required-field" for="selected_supplier">Select Vendor</label>
            <select style="width: 100%" class="form-control" id="selected_supplier" name="selected_supplier" onchange="getVendorOBData()">
              <option value="">Select Vendor</option>
              <?php foreach($supplier_data as $key => $value): ?>
                <option style="text-transform: uppercase;" value="<?php echo $value['id']; ?>"><?php echo $value['first_name']. ' '.$value['last_name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="amount_to_pay">Balance</label>
            <input type="text" readonly class="form-control" id="amount_to_pay" name="amount_to_pay" >
          </div>


          <div class="form-group">
            <label for="input_date" class="required-field">Date</label>
            <input type="date" required class="form-control" id="input_date" name="input_date" autocomplete="off">
          </div>
          
          <div class="form-group">
            <label for="pay_amount" class="required-field">Pay Amount</label>
            <input type="number" step="any" min="0" class="form-control noscroll" id="pay_amount" name="pay_amount" placeholder="Pay Amount" autocomplete="off">
          </div>
          <?php
            $payment_data = $this->Model_payment_method->getPaymentMethodData();
          ?>
          <div class="form-group">
            <label class="required-field" for="payment_method">Payment Method</label>
            <select style="width: 100%" class="form-control" id="payment_method" name="payment_method">
              <option value="">Select Method</option>
              <?php foreach($payment_data as $key => $value): ?>
                <option value="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="payment_note">Payment Note</label>
            <input type="text" class="form-control" id="payment_note" name="payment_note" placeholder="Payment Note" autocomplete="off">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" id="btn_save_changes" class="btn btn-primary">Save changes</button>
        </div>

      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- edit supplier modal -->
<div class="modal fade" role="dialog" id="editSupplierOBPaymentModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit OB Payment</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Supplier/update_supplier_ob_payment" method="post" id="updateSupplierOBPaymentForm">

        <div class="modal-body">
          <div id="messages"></div>

          <div class="form-group">
            <label class="required-field" for="edit_selected_supplier">Select Vendor</label>
            <select style="width: 100%" class="form-control" id="edit_selected_supplier" name="edit_selected_supplier" onchange="getVendorOBData_2()">
              <option value="">Select Vendor</option>
              <?php foreach($supplier_data as $key => $value): ?>
                <option style="text-transform: uppercase;" value="<?php echo $value['id']; ?>"><?php echo $value['first_name']. ' '.$value['last_name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="edit_amount_to_pay">Balance</label>
            <input type="text" readonly class="form-control" id="edit_amount_to_pay" name="edit_amount_to_pay">
          </div>

          <div class="form-group">
            <label for="edit_input_date" class="required-field">Date</label>
            <input type="date" required class="form-control" id="edit_input_date" name="edit_input_date" autocomplete="off">
          </div>
          
          <div class="form-group">
            <label for="edit_pay_amount" class="required-field">Pay Amount</label>
            <input type="number" step="any" min="0" class="form-control noscroll" id="edit_pay_amount" name="edit_pay_amount" placeholder="Pay Amount" autocomplete="off">
          </div>
          <?php
            $payment_data = $this->Model_payment_method->getPaymentMethodData();
          ?>
          <div class="form-group">
            <label class="required-field" for="edit_payment_method">Payment Method</label>
            <select style="width: 100%" class="form-control" id="edit_payment_method" name="edit_payment_method">
              <option value="">Select Method</option>
              <?php foreach($payment_data as $key => $value): ?>
                <option value="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="edit_payment_note" class="required-field">Payment Note</label>
            <input type="text" class="form-control" id="edit_payment_note" name="edit_payment_note" placeholder="Payment Note" autocomplete="off">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" id="edit_btn_save_changes" class="btn btn-primary">Save changes</button>
        </div>

      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- remove supplier modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeSupplierOBPaymentModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove OB Payment</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Supplier/remove_supplier_ob_payment" method="post" id="removeSupplierOBPaymentForm">
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
  var manageTable;

  $(document).ready(function() {

    $('select').select2();

    document.addEventListener("wheel", function(event){
      if(document.activeElement.type === "number" &&
       document.activeElement.classList.contains("noscroll"))
      {
        document.activeElement.blur();
      }
    });

    $('#input_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd' 
    });

    $('#input_date').datepicker().datepicker("setDate", new Date());

    $("#mainVendorNav").addClass('active');
    $("#mainVendorNav").addClass('menu-open');
    $("#ManageSupllierOBPaymentsNav").addClass('active');
    $( ".required-field" ).append('<label style="color:red" for="name">*</label>');

  // initialize the datatable
  var selected_vendor = '<?php if(isset($_GET['selected_vendor'])){ echo $_GET['selected_vendor']; }  ?>';
  manageTable = $('#manageTable').DataTable({
    'ajax': '<?php echo base_url()?>index.php/Supplier/fetchSupplierPymentData/'+selected_vendor,
    'order': [],
    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
  });

  // submit the create from 
  $("#createSupplierOBPaymentForm").unbind('submit').on('submit', function() {
    var form = $(this);

    // remove the text-danger
    $(".text-danger").remove();
    $.ajax({
      url: form.attr('action'),
      type: form.attr('method'),
      data: form.serialize(), // /converting the form data into array and sending it to server
      dataType: 'json',
      success:function(response) {

        manageTable.ajax.reload(null, false);

        if(response.success === true) {
          $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
            '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
            '</div>');


          // hide the modal
          $("#addSupplierOBPaymentModal").modal('hide');

          // reset the form
          $("#createSupplierOBPaymentForm")[0].reset();

          $("#createSupplierOBPaymentForm .form-group").removeClass('has-error').removeClass('has-success');

        } else {

          if(response.messages instanceof Object) {
            $.each(response.messages, function(index, value) {
              var id = $("#"+index);

              id.closest('.form-group')
              .removeClass('has-error')
              .removeClass('has-success')
              .addClass(value.length > 0 ? 'has-error' : 'has-success');
              
              id.after(value);

            });
          } else {
            $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
              '</div>');
          }
        }
      }
    }); 

    return false;
  });

});


  function editSupplierOBPayment(id)
  { 
    $.ajax({
      url: '<?php echo base_url();?>index.php/Supplier/fetchSupplierOBPaymentDataById/'+id,
      type: 'post',
      dataType: 'json',
      success:function(response) {
        var date = new Date(response.data.datetime_creation);
        var year    = date.getFullYear();
        var month   = date.getMonth()+1; 
        var day     = date.getDate();
        if(month.toString().length == 1) 
        {
          month = '0'+month;
        }
        if(day.toString().length == 1) 
        {
          day = '0'+day;
        }  
        var final = year+'-'+month+'-'+day;

        $("#edit_selected_supplier").val(response.data.vendor_id);
        $("#edit_selected_supplier").select2().trigger('change');

        $("#edit_pay_amount").val(response.data.paid_amount);
        $("#edit_amount_to_pay").val(response.vendor_data.balance);

        $("#edit_payment_method").val(response.data.payment_method);
        $("#edit_payment_method").select2().trigger('change');

        $("#edit_payment_note").val(response.data.payment_note);
        document.getElementById("edit_input_date").value = final;

      // submit the edit from 
      $("#updateSupplierOBPaymentForm").unbind('submit').bind('submit', function() {
        var form = $(this);

        // remove the text-danger
        $(".text-danger").remove();

        $.ajax({
          url: form.attr('action') + '/' + id,
          type: form.attr('method'),
          data: form.serialize(), // /converting the form data into array and sending it to server
          dataType: 'json',
          success:function(response) {

            manageTable.ajax.reload(null, false);

            if(response.success === true) {
              $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
                '</div>');


              // hide the modal
              $("#editSupplierOBPaymentModal").modal('hide');
              // reset the form 
              $("#updateSupplierOBPaymentForm .form-group").removeClass('has-error').removeClass('has-success');

            } else {

              if(response.messages instanceof Object) {
                $.each(response.messages, function(index, value) {
                  var id = $("#"+index);

                  id.closest('.form-group')
                  .removeClass('has-error')
                  .removeClass('has-success')
                  .addClass(value.length > 0 ? 'has-error' : 'has-success');
                  
                  id.after(value);

                });
              } else {
                $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">'+
                  '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                  '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
                  '</div>');
              }
            }
          }
        }); 

        return false;
      });

    }
  });
  }


  function removeSupplierOBPayment(id)
  {
    if(id) {
      $("#removeSupplierOBPaymentForm").on('submit', function() {

        var form = $(this);

      // remove the text-danger
      $(".text-danger").remove();

      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: { supplier_payment_id:id }, 
        dataType: 'json',
        success:function(response) {

          manageTable.ajax.reload(null, false);

          if(response.success === true) {
            $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
              '</div>');

            // hide the modal
            $("#removeSupplierOBPaymentModal").modal('hide');

          } else {

            $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
              '</div>'); 
          }
        }
      }); 

      return false;
    });
    }
  }

function getVendorOBData() {
  var selected_supplier = $("#selected_supplier").val();
  if(selected_supplier == ""){
    $("#btn_save_changes").attr("disabled", "disabled");
    $("#pay_amount").val("");
  }
  else{
    $("#btn_save_changes").removeAttr("disabled");
    $.ajax({
      url: '<?php echo base_url();?>index.php/Supplier/fetchSupplierDataById/'+selected_supplier,
      type: 'post',
      dataType: 'json',
      success:function(response) {
        if(response.data.balance != 0){
          $("#amount_to_pay").val(response.data.balance);
        }
        else{
          $("#amount_to_pay").val(0);
        }
      }
    });
  }
}

function getVendorOBData_2() {
  let selected_supplier = $("#edit_selected_supplier").val();
  if(selected_supplier == ""){
    $("#edit_btn_save_changes").attr("disabled", "disabled");
    $("#edit_pay_amount").val("");
  }
  else{
    $("#edit_btn_save_changes").removeAttr("disabled");
    $.ajax({
      url: '<?php echo base_url();?>index.php/Supplier/fetchSupplierDataById/'+selected_supplier,
      type: 'post',
      dataType: 'json',
      success:function(response) {
        if(response.data.balance != 0){
          $("#edit_amount_to_pay").val(response.data.balance);
        }
        else{
          $("#edit_amount_to_pay").val("0");
        }
      }
    });
  }
}

$(function(){

  $("#selected_vendor").on('change', function(){
    var selected_vendor = encodeURI($(this).val());
    window.location = '<?php echo base_url() ?>index.php/Supplier/manage_supllier_ob_payments?selected_vendor='+selected_vendor;
  });


});


</script>

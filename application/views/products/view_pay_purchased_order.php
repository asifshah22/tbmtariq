

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      List
      <small>Purchased Order Payments</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Purchased Order Payments</li>
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
        <?php if(in_array('printPurchasingPayment', $user_permission)): ?>
          <a title="Print Purchase Order Returns" target="__blank" href="<?php echo base_url() ?>index.php/Product/print_pay_purchased_order/<?php echo $this->uri->segment(3) ?>" class="btn btn-primary" id="print">
            <i class="fa fa-print"></i>
          </a>
          <br /> <br />
        <?php endif; ?>
          
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Purchased Order Payments</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>Sr.</th>
                <th>Bill no</th>
                <th>Date</th>
                <th>Vendor name</th>
                <th>Paid Amount</th>
                <th>Payment</th>
                <th>Bank</th>
                <th>Check no</th>
                <th>Payment Note</th>
                <?php if( ( in_array('updatePurchasingPayment', $user_permission) ) || ( in_array('deletePurchasingPayment', $user_permission) ) ): ?>
                  <th width="10%">Action</th>
                <?php endif; ?>
              </tr>
              </thead>

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



<div class="modal fade" tabindex="-1" role="dialog" id="editPayPurchaseOrderModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Payment Purchase Order</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Product/edit_pay_purchased_order" method="post" id="editPayPurchaseOrderForm">
        <div class="modal-body">
          <div class="form-group">
            <label for="edit_bill_amount">Bill Amount</label>
            <input disabled type="number" class="form-control" id="edit_bill_amount" min="1" name="edit_bill_amount" placeholder="Bill Amount" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="edit_paid_amount">Paid Amount</label>
            <input disabled type="number" required class="form-control" id="edit_paid_amount" min="0" step="0.01" name="edit_paid_amount" placeholder="Paid Amount" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="datepicker_edit">Date</label>
            <input required type="date" class="form-control" id="datepicker_edit" name="datepicker_edit">
          </div>
          <div class="form-group">
            <label class="required-field" for="edit_pay_amount">Pay Amount</label>
            <input type="number" required class="form-control" id="edit_pay_amount" min="1" step="0.01" name="edit_pay_amount" placeholder="Pay Amount" autocomplete="off">
          </div>

          <div class="form-group">
            <label class="required-field" for="edit_payment_method">Payment Mthod</label>
            <select class="form-control" required id="edit_payment_method" name="edit_payment_method" onchange="editDisplayOtherInfo()">
              <option value="">Payment Method</option>
              <option value="1">Cash</option>
              <option value="2">Check</option>
            </select>
          </div>
          <div id="edit_otherInfo" style="display: none;">
            <div class="form-group">
              <label for="edit_bank_name" class="required-field control-label">Bank Name</label>
              <input type="text" class="form-control" id="edit_bank_name" name="edit_bank_name" placeholder="Bank Name" autocomplete="off">
            </div>
            <div class="form-group">
              <label class="required-field control-label" for="edit_check_number">Check Number</label>
              <input type="text" class="form-control" id="edit_check_number" name="edit_check_number" placeholder="Check Number" autocomplete="off">
            </div>
          </div>
          <div class="form-group">
            <label class="control-label" for="edit_payment_note">Payment Note</label>
            <input type="text" class="form-control" id="edit_payment_note" name="edit_payment_note" placeholder="Payment Note" autocomplete="off">
          </div>
        </div>
        <div class="modal-footer">
          <button title="CLose Form" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button title="Save Form" type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<!-- remove modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Order Payment</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Product/remove_purchase_order_payment" method="post" id="removeForm">
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
  manageTable = $('#manageTable').DataTable({
    'ajax': '<?php echo base_url()?>index.php/Product/fetchPurchasedOrderPaymentsData/<?php echo $this->uri->segment(3)?>',
    'order': []
  });

  $('#datepicker_edit').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd' 
  });

});


function editPayPurchaseOrder(payment_id) {
  $.ajax({
    url: '<?php echo base_url();?>index.php/Product/fetchPurchasedOrderPayInfo/'+payment_id,
    type: 'post',
    dataType: 'json',
    success:function(response) {
      var bill_amount = response.purchase_order_data.net_amount - response.purchase_return_data.returns_amount;
      
      $("#edit_bill_amount").val(bill_amount);
      if(response.purchased_payment_data.payment_method == 1){
        $("#edit_payment_method").val(response.purchased_payment_data.payment_method);
        // hide other info
        document.getElementById("edit_otherInfo").setAttribute("style", "display: none;");
        $("#edit_bank_name").removeAttr('required');
        $("#edit_check_number").removeAttr('required');
        $("#edit_bank_name").val();
        $("#edit_check_number").val();
      }
      else if(response.purchased_payment_data.payment_method == 2){
        $("#edit_payment_method").val(response.purchased_payment_data.payment_method);
        // display other info
        document.getElementById("edit_otherInfo").setAttribute("style", "display: visible;");
        $("#edit_bank_name").prop('required',true);
        $("#edit_check_number").prop('required',true);
        $("#edit_bank_name").val(response.purchased_payment_data.bank_name);
        $("#edit_check_number").val(response.purchased_payment_data.check_number);
      }
      
      $("#edit_paid_amount").val(response.purchase_order_data.paid_amount);
      $("#edit_pay_amount").val(response.purchased_payment_data.paid_amount);
      $("#edit_payment_note").val(response.purchased_payment_data.payment_note);

      $('#datepicker_edit').datepicker().datepicker("setDate", response.purchased_payment_data.date);

      $("#editPayPurchaseOrderForm").unbind('submit').bind('submit', function() {
        var form = $(this);

        // remove the text-danger
        $(".text-danger").remove();
        $.ajax({
          url: form.attr('action') + '/' + payment_id,
          type: form.attr('method'),
          data: { payment_id:payment_id },
          data: form.serialize(), 
          dataType: 'json',
          success:function(response) {
            manageTable.ajax.reload(null, false); 
            console.log(response);
            if(response.success === true) {
              $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
              '</div>');


              // hide the modal
              $("#editPayPurchaseOrderModal").modal('hide');
              // reset the form
              $("#ditPayPurchaseOrderForm")[0].reset(); 
              $("#ditPayPurchaseOrderForm .form-group").removeClass('has-error').removeClass('has-success');

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

function removePurchaseOrderPayment(id) {
    if(id) {
    $("#removeForm").on('submit', function() {

      var form = $(this);
      // remove the text-danger
      $(".text-danger").remove();

      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: { payment_id:id }, 
        dataType: 'json',
        success:function(response) {

          manageTable.ajax.reload(null, false); 

          if(response.success === true) {
            $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
            '</div>');

            // hide the modal
            $("#removeModal").modal('hide');

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


function editDisplayOtherInfo()
{
  var payment_method = $("#edit_payment_method").val();
  if(payment_method == "") {
    document.getElementById("edit_otherInfo").setAttribute("style", "display: none;");
  }
  else if(payment_method == "1")
  {
    document.getElementById("edit_otherInfo").setAttribute("style", "display: none;");
    $("#edit_bank_name").removeAttr('required');
    $("#edit_check_number").removeAttr('required');
  } 
  else
  {
    document.getElementById("edit_otherInfo").setAttribute("style", "display: visible;");
    $("#edit_bank_name").prop('required',true);
    $("#edit_check_number").prop('required',true);
  }
}
</script>

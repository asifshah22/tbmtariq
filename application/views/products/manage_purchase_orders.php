

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Purchase Orders</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Purchase Orders</li>
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

          <?php if(in_array('createPurchasing', $user_permission)): ?>  
            <a title="Create New Purchasing" href="<?php echo base_url() ?>index.php/Product/purchasing" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i></a>
          <?php endif; ?>
          <?php if(in_array('printPurchasing', $user_permission)): ?>  
            <a title="Print Purchase Orders" target="__blank" href="<?php base_url() ?>print_purchase_orders" class="btn btn-info" id="print">
              <i class="glyphicon glyphicon-print"></i>
            </a>
          <?php endif; ?>
          <?php if( ( in_array('createPurchasing', $user_permission) ) || ( in_array('printPurchasing', $user_permission) ) ): ?>
          <br /> <br />
        <?php endif; ?>
        
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Purchase Orders</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th width="3%">#</th>
                  <th style="color:#3c8dbc">DateTime</th>
                  <th style="color:#3c8dbc">Bill no</th>
                  <th style="color:#3c8dbc">Vendor</th>
                  <th style="color:#3c8dbc">Created By</th>
                  <th style="color:#3c8dbc">Total Bill Amount</th>
                  <th style="color:#3c8dbc">Bill Remaining Amount</th>
                  <!-- <th>Remaining</th> -->
                  <?php if( (in_array('updatePurchasing', $user_permission)) || (in_array('viewPurchasing', $user_permission)) || (in_array('deletePurchasing', $user_permission)) || (in_array('createPurchasingPayment', $user_permission)) || (in_array('viewPurchasingPayment', $user_permission)) || (in_array('createPurchaseReturn', $user_permission)) || (in_array('viewPurchaseReturn', $user_permission)) ): ?>
                  <th width="10%"></th>
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

<!-- remove brand modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Purchased Order</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Product/remove_purchase_order" method="post" id="removeForm">
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


<div class="modal fade" tabindex="-1" role="dialog" id="payPurchaseOrderModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Pay Purchase Order</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Product/pay_purchased_order" method="post" id="payPurchaseOrderForm">
        <div class="modal-body">
          <div class="form-group">
            <label for="bill_amount">Bill Amount</label>
            <input readonly type="number" class="form-control" id="bill_amount" min="1" name="bill_amount" placeholder="Bill Amount" autocomplete="off">
          </div>
          <div id="paidInfo">

          </div>
          <div class="form-group"  id="opening_balance_div" style="display: none;">
            <label id="opening_balance_label" for="opening_balance">Opening Balance</label>
            <input readonly type="number" class="form-control" id="opening_balance" min="1" name="opening_balance" placeholder="Opening Balance" autocomplete="off">
          </div>
          <!-- <div class="form-group" id="bill_total_div" style="display: none;">
            <label for="bill_total">Total To Be Paid</label>
            <input readonly type="number" class="form-control" id="bill_total" min="1" name="bill_total" placeholder="Total" autocomplete="off">
          </div> -->
          <div class="form-group">
            <label class="required-field" for="datepicker_add">Date</label>
            <input required type="date" class="form-control" id="datepicker_add" name="datepicker_add">
          </div>
          <div class="form-group" id="ob_payment_div" style="display: none;">
            <label for="ob_payment">OB Payment</label>
            <input type="text" class="form-control" id="ob_payment" min="1" name="ob_payment" placeholder="Opening Balance Payment" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="pay_amount">Pay Amount</label>
            <input type="number" required class="form-control" id="pay_amount" min="1" step="0.01" name="pay_amount" placeholder="Pay Amount" autocomplete="off">
          </div>

          <div class="form-group">
            <label class="required-field" for="payment_method">Payment Mthod</label>
            <select class="form-control" required id="payment_method" name="payment_method" onchange="displayOtherInfo()">
              <option value="">Payment Method</option>
              <option value="1">Cash</option>
              <option value="2">Check</option>
            </select>
          </div>
          <div id="otherInfo" style="display: none;">
            <div class="form-group">
              <label for="bank_name" class="required-field control-label">Bank Name</label>
              <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Bank Name" autocomplete="off">
            </div>
            <div class="form-group">
              <label class="required-field control-label" for="check_number">Check Number</label>
              <input type="text" class="form-control" id="check_number" name="check_number" placeholder="Check Number" autocomplete="off">
            </div>
          </div>
          <div class="form-group">
            <label for="payment_note">Payment Note</label>
            <input type="test" class="form-control" id="payment_note" name="payment_note" placeholder="Payment Note" autocomplete="off">
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



<script type="text/javascript">
  var manageTable;
  var base_url = "<?php echo base_url(); ?>";

  $(document).ready(function() {

    $("#mainPurchasingNav").addClass('active');
    $("#managePurchasingsNav").addClass('active');
    $( ".required-field" ).append('<label style="color:red" for="name">*</label>');

    $('#datepicker_add').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd' 
    });

  // initialize the datatable 
  manageTable = $('#manageTable').DataTable({
    'ajax': base_url + 'index.php/Product/fetchPurchaseOrders',
    'order': [],
    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
  });

});

// remove functions 
function removePurchaseOrder(id)
{
  if(id) {
    $("#removeForm").on('submit', function() {
      var form = $(this);
      // remove the text-danger
      $(".text-danger").remove();

      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: { order_id:id }, 
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

function payPurchaseOrder(order_id) {
  $.ajax({
    url: '<?php echo base_url();?>index.php/Product/getPurchasedOrderPayInfo/'+order_id,
    type: 'post',
    dataType: 'json',
    success:function(response) {
      var bill_amount = Number(response.purchase_order_data.net_amount) - Number(response.purchase_return_data.returns_amount);
      if(response.purchase_order_payment_data.total_paid_amount > 0){
        var firstDiv = document.createElement("div");
        firstDiv.className = "form-group";
        firstDiv.innerHTML = '<label for="paid_amount" id="paid_amount_lbl">Paid Amount</label>';
        firstDiv.innerHTML += '<input readonly type="number" class="form-control" id="paid_amount" name="paid_amount" placeholder="Paid Amount" value="'+response.purchase_order_payment_data.total_paid_amount+'" autocomplete="off">';

        var secondDiv = document.createElement("div");
        secondDiv.className = "form-group";
        secondDiv.innerHTML = '<label for="remaining_amount" id="remaining_amount_lbl">Remaining Amount</label>';
        secondDiv.innerHTML += '<input readonly type="number" class="form-control" id="remaining_amount" name="remaining_amount" placeholder="Remaining Amount" value="'+(Number(bill_amount) - Number(response.purchase_order_data.paid_amount))+'" autocomplete="off">';
        var element = document.getElementById("paidInfo");
        element.appendChild(firstDiv);
        element.appendChild(secondDiv);
      }

      // bill amount and datetime
      $("#bill_amount").val(bill_amount);
      $('#datepicker_add').datepicker().datepicker("setDate", new Date());



      if(Number(response.opening_balance) !== 0)
      {
        document.getElementById("opening_balance_div").setAttribute("style", "display: visible;");
        document.getElementById("ob_payment_div").setAttribute("style", "display: visible;");
        if(Number(response.opening_balance) > 0){
          document.getElementById("opening_balance_label").innerHTML = "Opening Balance (TBM Remaining)";
        }
        else if(Number(response.opening_balance) < 0){
          document.getElementById("opening_balance_label").innerHTML = "Opening Balance (Vendor Remaining)";
        }
        $("#opening_balance").val(response.opening_balance);
      }
      else
      {
        document.getElementById("opening_balance_div").setAttribute("style", "display: none;");
        document.getElementById("ob_payment_div").setAttribute("style", "display: none;");
      }


      $("#payPurchaseOrderForm").unbind('submit').bind('submit', function() {
        var form = $(this);

        // remove the text-danger
        $(".text-danger").remove();
        $.ajax({
          url: form.attr('action') + '/' + order_id,
          type: form.attr('method'),
          data: { order_id:order_id },
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
              $("#payPurchaseOrderModal").modal('hide');
              // reset the form
              $("#payPurchaseOrderForm")[0].reset();
              //
              $("#payment_method").val("");
              document.getElementById("otherInfo").setAttribute("style", "display: none;");
              $("#bank_name").removeAttr('required');
              $("#check_number").removeAttr('required');

              $("#payPurchaseOrderForm .form-group").removeClass('has-error').removeClass('has-success');

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

$('#payPurchaseOrderModal').on('hidden.bs.modal', function(e)
{
  var paidInfo = $("#paidInfo");
  var paidInfo_childrens = paidInfo.children().length;
  if(paidInfo_childrens > 0){
    $("#paidInfo .form-group #paid_amount").remove();
    $("#paidInfo .form-group #paid_amount_lbl").remove();
    $("#paidInfo .form-group #remaining_amount").remove();
    $("#paidInfo .form-group #remaining_amount_lbl").remove();
  }
}) ;

function displayOtherInfo()
{
  var payment_method = $("#payment_method").val();
  if(payment_method == "") {
    document.getElementById("otherInfo").setAttribute("style", "display: none;");
  }
  else if(payment_method == "1")
  {
    document.getElementById("otherInfo").setAttribute("style", "display: none;");
    $("#bank_name").removeAttr('required');
    $("#check_number").removeAttr('required');
  } 
  else
  {
    document.getElementById("otherInfo").setAttribute("style", "display: visible;");
    $("#bank_name").prop('required',true);
    $("#check_number").prop('required',true);
  }
}


</script>

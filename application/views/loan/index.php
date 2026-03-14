

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      List
      <small>Vendors Loan</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Vendors Loan</li>
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

        <?php if(in_array('createLoan', $user_permission)): ?>
          <button title="Create Loan" class="btn btn-success" data-toggle="modal" data-target="#addModal"><i class="glyphicon glyphicon-plus"></i></button>
        <?php endif; ?>
        <?php if(in_array('printLoan', $user_permission)): ?>
          <a title="Print Vendors Loan" target="__blank" href="<?php base_url() ?>print_vendors_loan" class="btn btn-info">
            <i class="glyphicon glyphicon-print"></i>
          </a>
        <?php endif; ?>
        <?php if( ( in_array('createLoan', $user_permission) ) || ( in_array('printLoan', $user_permission) ) ): ?>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Vendors Loan</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th width="3%">#</th>
                <th style="color:#3c8dbc">Vendor Name</th>
                <th style="color:#3c8dbc">Loan Amount</th>
                <th style="color:#3c8dbc">Installment Amount</th>
                <th style="color:#3c8dbc">Paid</th>
                <th style="color:#3c8dbc">Remaining</th>
                <?php if( in_array('viewLoan', $user_permission) || in_array('deleteLoan', $user_permission) ): ?>
                  <th width="7%"></th>
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

<!-- create brand modal -->
<div class="modal fade" role="dialog" id="addModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add First Time Loan</h4>
      </div>

      <form role="form" action="<?php echo base_url(); ?>index.php/Loan/create_loan" method="post" id="createForm">

        <div class="modal-body">

          <div class="form-group">
            <label class="required-field" for="loan_date">DateTime</label>
            <input type="date" class="form-control" id="datepicker_add" name="datepicker_add">
          </div>
          <div class="form-group">
            <label class="required-field" for="select_vendor">Select Vendor</label>
            <select style="width: 100%" required="true" class="form-control" id="select_vendor" name="select_vendor">
              <option value="">Select</option>
              <?php foreach ($vendor_data as $key => $value): ?>
                <option value="<?php echo $value['id'] ?>"><?php echo $value['first_name']. ' '. $value['last_name']; ?></option>   
              <?php endforeach ?>
              
            </select>
          </div>
          <div class="form-group">
            <label class="required-field" for="amount">Amount</label>
            <input type="number" class="form-control noscroll" id="amount" min="1" step="any" name="amount" placeholder="Amount" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="installment_amount">Installment Amount</label>
            <input onkeyup="validateInstallment()" type="number" class="form-control noscroll" id="installment_amount" min="1" step="any" name="installment_amount" placeholder="Installment Amount" autocomplete="off">
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
          <button type="submit" id="btnCreateLoan" class="btn btn-primary">Save changes</button>
        </div>

      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- remove brand modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Loan</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Loan/remove_loan" method="post" id="removeForm">
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
  
  $("#mainLoanNav").addClass('active');
  $("#manageLoanNav").addClass('active');
  $( ".required-field" ).append('<label style="color:red" for="name">*</label>');
  //Date picker
  $('#datepicker_add').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd' 
  });
  $('#datepicker_add_2').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd' 
  });
  $('#datepicker_add').datepicker().datepicker("setDate", new Date());
  $('#datepicker_add_2').datepicker().datepicker("setDate", new Date());

  $('#datepicker_edit').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  });

  $("select").select2();
  document.addEventListener("wheel", function(event){
      if(document.activeElement.type === "number" &&
         document.activeElement.classList.contains("noscroll"))
      {
          document.activeElement.blur();
      }
  });
  
  

  // initialize the datatable 
  manageTable = $('#manageTable').DataTable({
    'ajax': '<?php echo base_url()?>index.php/Loan/fetchLoanData',
    'order': [],
    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
  });


  // submit the create from 
  $("#createForm").unbind('submit').on('submit', function() {
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
          $("#addModal").modal('hide');

          // reset the form
          $("#createForm")[0].reset();
          $("#createForm .form-group").removeClass('has-error').removeClass('has-success');

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

// remove functions 
function removeFunc(id)
{
  if(id) {
    $("#removeForm").on('submit', function() {

      var form = $(this);

      // remove the text-danger
      $(".text-danger").remove();

      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: { loan_id:id }, 
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

function validateInstallment() {
  var amount = Number($("#amount").val());
  var installmentAmount = Number($("#installment_amount").val());
  if(amount < installmentAmount){
    alert("Installment Amount should be equal or less than the Loan Amount!");
    $('#btnCreateLoan').prop('disabled', true);
  }
  else{
    $('#btnCreateLoan').prop('disabled', false);
  }
}


</script>

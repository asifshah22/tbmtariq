
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      List
      <small>Given Loans</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Given Loans</li>
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
          <button title="Give Another Loan" onclick="addAnotherLoanFunc('<?php echo $vendors_loan[0]['loan_id']; ?>')" class="btn btn-success" data-toggle="modal" data-target="#addAnotherLoanModal"><i class="glyphicon glyphicon-plus"></i></button>
          <br><br>
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Given Loans</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table class="table table-bordered table-hover" id="manageTable">
              <thead>
              <tr>
                <th width="3%">#</th>
                <th style="color:#3c8dbc">Date</th>
                <th style="color:#3c8dbc">Loan Amount</th>
                <th style="color:#3c8dbc">Intallment Amount</th>
                <th style="color:#3c8dbc">Payment Method</th>
                <th style="color:#3c8dbc">Payment Note</th>
                <?php if( (in_array('updateLoan', $user_permission)) || (in_array('deleteLoan', $user_permission)) ): ?>
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
<!-- remove brand modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Given Loan</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Loan/remove_vendor_loan" method="post" id="removeForm">
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


<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Given Loan</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Loan/update_vendor_loan" method="post" id="updateForm">

        <div class="modal-body">
          <div id="messages"></div>

          <div class="form-group">
            <label class="required-field" for="datepicker_edit">Date</label>
            <input type="date" class="form-control" id="datepicker_edit" name="datepicker_edit">
          </div>
          <div class="form-group">
            <label class="required-field" for="edit_amount">Loan Amount</label>
            <input type="number" class="form-control noscroll" min="1" step="any" id="edit_amount" name="edit_amount" placeholder="Amount" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="edit_installment_amount">Installment Amount</label>
            <input type="number" class="form-control noscroll" min="1" step="any" id="edit_installment_amount" name="edit_installment_amount" placeholder="Installment Amount" autocomplete="off">
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
            <label for="edit_payment_note">Payment Note</label>
            <input type="text" class="form-control" id="edit_payment_note" name="edit_payment_note" placeholder="Payment Note" autocomplete="off">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>

      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- create another loan modal -->
<div class="modal fade" role="dialog" id="addAnotherLoanModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Another Loan</h4>
      </div>

      <form role="form" action="<?php echo base_url(); ?>index.php/Loan/create_another_loan" method="post" id="createAnotherLoanForm">

        <div class="modal-body">
          <input type="hidden" name="loan_id" id="loan_id">
          <div class="form-group">
            <label class="required-field" for="datepicker_add_2">Date</label>
            <input type="date" class="form-control" id="datepicker_add_2" name="datepicker_add_2">
          </div>
          <div class="form-group">
            <label class="required-field" for="amount_2">Amount</label>
            <input type="number" class="form-control noscroll" id="amount_2" min="1" step="any" name="amount_2" placeholder="Amount" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="installment_amount_2">Installment Amount</label>
            <input type="number" class="form-control noscroll" id="installment_amount_2" min="1" step="any" name="installment_amount_2" placeholder="Amount" autocomplete="off">
          </div>
          <?php
            $payment_data = $this->Model_payment_method->getPaymentMethodData();
          ?>
          <div class="form-group">
            <label class="required-field" for="payment_method_2">Payment Method</label>
            <select style="width: 100%" class="form-control" id="payment_method_2" name="payment_method_2">
              <option value="">Select Method</option>
              <?php foreach($payment_data as $key => $value): ?>
                <option value="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="payment_note_2">Payment Note</label>
            <input type="text" class="form-control" id="payment_note_2" name="payment_note_2" placeholder="Payment Note" autocomplete="off">
          </div>
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
  $(document).ready(function()
  {
    $("#mainLoanNav").addClass('active');
    var id = "<?php echo $this->uri->segment(3)?>";
    manageTable = $('#manageTable').DataTable({
      'ajax': '<?php echo base_url()?>index.php/Loan/fetchVendorLoanData/'+id,
      'order': []
    });
    $( ".required-field" ).append('<label style="color:red" for="name">*</label>');

    $('#datepicker_edit').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });

    $('#datepicker_add_2').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd' 
    });
    $('#datepicker_add_2').datepicker().datepicker("setDate", new Date());
  });

  $("select").select2();
  document.addEventListener("wheel", function(event){
      if(document.activeElement.type === "number" &&
         document.activeElement.classList.contains("noscroll"))
      {
          document.activeElement.blur();
      }
  });


  // add another loan function
  function addAnotherLoanFunc(id)
  {
    $("#createAnotherLoanForm").unbind('submit').bind('submit', function() {
      var form = $(this);
      $('#loan_id').val(id);
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
            $("#addAnotherLoanModal").modal('hide');
            // reset the form
            $("#createAnotherLoanForm")[0].reset();
            // reset the form
            $("#createAnotherLoanForm .form-group").removeClass('has-error').removeClass('has-success');
          } 
          else
          {
            if(response.messages instanceof Object) 
            {
              $.each(response.messages, function(index, value) 
              {
                var id = $("#"+index);
                id.closest('.form-group')
                .removeClass('has-error')
                .removeClass('has-success')
                .addClass(value.length > 0 ? 'has-error' : 'has-success');
                id.after(value);
              });
            }
            else
            {
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

  function editFunc(id)
  {
    $.ajax({
      url: '<?php echo base_url();?>index.php/Loan/fetchVendorLoanById/'+id,
      type: 'post',
      dataType: 'json',
      success:function(response) {
        <?php date_default_timezone_set("Asia/Karachi"); ?>
        $('#datepicker_edit').datepicker().datepicker("setDate", response.loan_date);
        $("#edit_amount").val(response.amount);
        $("#edit_payment_note").val(response.payment_note);
        $("#edit_installment_amount").val(response.installment_amount);
        $("#edit_payment_method").val(response.payment_method);
        $("#edit_payment_method").select2().trigger('change');
        // submit the edit form 
        $("#updateForm").unbind('submit').bind('submit', function() {
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
                $("#editModal").modal('hide');
                // reset the form 
                $("#updateForm .form-group").removeClass('has-error').removeClass('has-success');

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
          data: { vendor_loan_id:id }, 
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
</script>
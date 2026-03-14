

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Customers</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Customers</li>
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
        <?php if(in_array('createCustomer', $user_permission)): ?>
          <a title="Add New Customer" class="btn btn-success" data-toggle="modal" href="#addCustomerModal"><i class="glyphicon glyphicon-plus"></i></a>
        <?php endif; ?>
        <?php if(in_array('printCustomer', $user_permission)): ?>
          <a title="Print Permissions" target="__blank" href="<?php base_url() ?>print_customers" class="btn btn-info" id="print">
            <span class="glyphicon glyphicon-print"></span>
          </a>
        <?php endif; ?>
        <?php if( ( in_array('createCustomer', $user_permission) ) || ( in_array('printCustomer', $user_permission) ) ): ?>  
          <br /> <br />
        <?php endif; ?>
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Customers</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th width="3%">#</th>
                <th style="color:#3c8dbc">Name</th>
                <th style="color:#3c8dbc">Department</th>
                <th style="color:#3c8dbc">CNIC</th>
                <th style="color:#3c8dbc">Address</th>
                <?php if( (in_array('updateCustomer', $user_permission)) || (in_array('deleteCustomer', $user_permission)) ): ?>
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

<!-- create customer modal -->
<div class="modal fade" role="dialog" id="addCustomerModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Customer</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Customers/create_customer" method="post" id="createCustomerForm">

        <div class="modal-body">

          <div class="form-group">
            <label class="required-field" for="customer_full_name">Full Name</label>
            <input type="text" class="form-control" id="customer_full_name" name="customer_full_name" placeholder="Enter full name" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="customer_cnic">CNIC</label>
            <input type="text" class="form-control" id="customer_cnic" name="customer_cnic" placeholder="Enter cnic" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="customer_phone">Phone</label>
            <input type="text" class="form-control" id="customer_phone" name="customer_phone" placeholder="Enter phone" autocomplete="off">
          </div>
         
          <div class="form-group">
            <label class="required-field" for="customer_address">Address</label>
            <input type="text" class="form-control" id="customer_address" name="customer_address" placeholder="Enter Address" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="department">Department</label>
            <select style="width: 100%" class="form-control" id="department" name="department">
              <option value="">Select Department</option>
              <?php foreach($department_data as $key => $value): ?>
                <option value="<?php echo $value['id'] ?>"><?php echo $value['department_name']; ?></option>
              <?php endforeach; ?>
            </select>
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

<!-- edit customer modal -->
<div class="modal fade" role="dialog" id="editCustomerModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Customer</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Customers/update_customer" method="post" id="updateCustomerForm">

        <div class="modal-body">
          <div id="messages"></div>

          <div class="form-group">
            <label class="required-field" for="edit_customer_full_name">First Name</label>
            <input type="text" class="form-control" id="edit_customer_full_name" name="edit_customer_full_name" placeholder="Enter first name" autocomplete="off">
          </div>
         
          <div class="form-group">
            <label for="edit_customer_cnic">CNIC</label>
            <input type="text" class="form-control" id="edit_customer_cnic" name="edit_customer_cnic" placeholder="Enter cnic" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="edit_customer_phone">Phone</label>
            <input type="text" class="form-control" id="edit_customer_phone" name="edit_customer_phone" placeholder="Enter phone" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="edit_customer_address">Address</label>
            <input type="text" class="form-control" id="edit_customer_address" name="edit_customer_address" placeholder="Enter email" autocomplete="off">
          </div>

          <div class="form-group">
            <label class="required-field" for="edit_department">Department</label>
            <select style="width: 100%" class="form-control" id="edit_department" name="edit_department">
              <option value="">Select Department</option>
              <?php foreach($department_data as $key => $value): ?>
                <option value="<?php echo $value['id'] ?>"><?php echo $value['department_name']; ?></option>
              <?php endforeach; ?>
            </select>
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

<!-- remove customer modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeCustomerModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Customer</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Customers/remove_customer" method="post" id="removeCustomerForm">
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

  $("#mainCustomersNav").addClass('active');
  $("#customersNav").addClass('active');
  $( ".required-field" ).append('<label style="color:red" for="name">*</label>');
  $('select').select2();
  // initialize the datatable 
  manageTable = $('#manageTable').DataTable({
    'ajax': '<?php echo base_url()?>index.php/Customers/fetchCustomerData',
    'order': [],
    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
  });
  // submit the create from 
  
  $(window).on('shown.bs.modal', function() { 
    $("#createCustomerForm")[0].reset();
  });
  $("#createCustomerForm").unbind('submit').on('submit', function() {

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
          $("#addCustomerModal").modal('hide');

          // reset the form
          $("#createCustomerForm")[0].reset();
  
          $("#createCustomerForm .form-group").removeClass('has-error').removeClass('has-success');

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

function editCustomer(id)
{
  $.ajax({
    url: '<?php echo base_url();?>index.php/Customers/fetchCustomerDataById/'+id,
    type: 'post',
    dataType: 'json',
    success:function(response) {

      $("#edit_customer_full_name").val(response.customers_data.full_name);
      $("#edit_customer_cnic").val(response.customers_data.cnic);
      $("#edit_customer_phone").val(response.customers_data.phone_number);
      $("#edit_customer_address").val(response.customers_data.address);
      $("#edit_department").val(response.department_data.department_id);
      $("#edit_department").select2().trigger('change');

      // submit the edit from 
      $("#updateCustomerForm").unbind('submit').bind('submit', function() {
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
              $("#editCustomerModal").modal('hide');
              // reset the form 
              $("#updateCustomerForm .form-group").removeClass('has-error').removeClass('has-success');

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



function removeCustomer(id)
{
  if(id) {
    $("#removeCustomerForm").on('submit', function() {

      var form = $(this);

      // remove the text-danger
      $(".text-danger").remove();

      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: { customer_id:id }, 
        dataType: 'json',
        success:function(response) {

          manageTable.ajax.reload(null, false); 

          if(response.success === true) {
            $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
            '</div>');

            // hide the modal
            $("#removeCustomerModal").modal('hide');

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

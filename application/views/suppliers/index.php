

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Vendors</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Vendors</li>
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
        
        <?php if(in_array('createVendor', $user_permission)): ?>  
          <button title="Add New Vendor" class="btn btn-success" data-toggle="modal" data-target="#addSupplierModal"><i class="glyphicon glyphicon-plus"></i></button>
        <?php endif; ?>
        <?php if(in_array('printVendor', $user_permission)): ?> 
          <a title="Print Suppliers" target="__blank" href="<?php echo base_url() ?>index.php/Supplier/print_suppliers" class="btn btn-info" id="print">
            <i class="glyphicon glyphicon-print"></i>
          </a>
        <?php endif; ?>
        <?php if( ( in_array('createVendor', $user_permission) ) || ( in_array('printVendor', $user_permission) ) ): ?>
          <br /> <br />
        <?php endif; ?>
       
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Vendors</h3>
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
                <a href="<?php echo base_url() ?>index.php/Supplier/print_suppliers<?php if(isset($_GET['selected_vendor'])){ echo "?selected_vendor=".$_GET['selected_vendor']; } ?>" target="__blank" class="btn btn-success btn-sm btn-flat" id="print"><span class="glyphicon glyphicon-print"></span> Print</a>
              </form>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th width="3%">#</th>
                <th>Image</th>
                <th style="color:#3c8dbc">Name</th>
                <th style="color:#3c8dbc; word-wrap: break-word;">Address</th>
                <th style="color:#3c8dbc">City</th>
                <th title="Supplier Balance" style="color:#3c8dbc">Balance</th>
                <?php if( in_array('updateVendor', $user_permission) || in_array('deleteVendor', $user_permission) || in_array('viewVendor', $user_permission) ): ?>
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

<!-- create supplier modal -->
<div class="modal fade" role="dialog" id="addSupplierModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Vendor</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Supplier/create_supplier" method="post" id="createSupplierForm">

        <div class="modal-body">
          <div class="form-group">
            <label class="required-field" for="supplier_fname">First Name</label>
            <input type="text" class="form-control" id="supplier_fname" name="supplier_fname" placeholder="Vendor First Name" autocomplete="anyrandomstring">
          </div>
          <div class="form-group">
            <label for="supplier_lname">Last Name</label>
            <input type="text" class="form-control" id="supplier_lname" name="supplier_lname" placeholder="Vendor last name" autocomplete="anyrandomstring">
          </div>
          <div class="form-group">
            <label for="opening_balance">Balance</label>
            <input type="number" step="any" class="form-control noscroll" id="opening_balance" name="opening_balance" placeholder="Opening Balance" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="supplier_address">Address</label>
            <input type="text" class="form-control" id="supplier_address" name="supplier_address" placeholder="Address" autocomplete="anyrandomstring">
          </div>
          <div class="form-group">
            <label class="required-field" for="supplier_city">City</label>
            <input type="text" class="form-control" id="supplier_city" name="supplier_city" placeholder="City" autocomplete="anyrandomstring">
          </div>
          <div class="form-group">
            <label for="supplier_country">Country</label>
            <input type="text" class="form-control" id="supplier_country" name="supplier_country" placeholder="Country" autocomplete="anyrandomstring">
          </div>
          <div class="form-group">
            <label for="supplier_cnic">CNIC</label>
            <input type="text" class="form-control" id="supplier_cnic" name="supplier_cnic" placeholder="CNIC" autocomplete="off">
          </div>
        
          <div id="phone_info_div">
            <div class="form-group row" id="row_1">
              <div class="col-xs-10">
                <label class="required-field" for="phone_1">Phone</label>
                <input required="true" type="text" width="10%" class="form-control" id="phone_1" name="phone[]" placeholder="Phone" autocomplete="anyrandomstring">
              </div>
              <div class="col-xs-2">
                <br>
                <button style="margin-top: 9px" title="Add Multiple Phone" type="button" id="add_row" class="btn btn-default"><i class="fa fa-plus"></i></button>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="supplier_email">Email</label>
            <input type="email" class="form-control" id="supplier_email" name="supplier_email" placeholder="Email" autocomplete="anyrandomstring">
          </div>
          <div class="form-group">
            <label for="remarks">Remarks</label>
            <textarea type="text" class="form-control" id="remarks" name="remarks" placeholder="Remarks" autocomplete="off"></textarea>
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

<!-- edit supplier modal -->
<div class="modal fade" role="dialog" id="editSupplierModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Vendor</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Supplier/update_supplier" method="post" id="updateSupplierForm">

        <div class="modal-body">
          <div id="messages"></div>

          <div class="form-group">
            <label class="required-field" for="edit_supplier_fname">First Name</label>
            <input type="text" class="form-control" id="edit_supplier_fname" name="edit_supplier_fname" placeholder="First Name" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="edit_supplier_lname">Last Name</label>
            <input type="text" class="form-control" id="edit_supplier_lname" name="edit_supplier_lname" placeholder="Last Name" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="edit_opening_balance">Balance</label>
            <input type="number" step="any" class="form-control noscroll" id="edit_opening_balance" name="edit_opening_balance" placeholder="Opening Balance" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" class="required-field" for="edit_supplier_address">Address</label>
            <input type="text" class="form-control" id="edit_supplier_address" name="edit_supplier_address" placeholder="Address" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" class="required-field" for="edit_supplier_city">City</label>
            <input type="text" class="form-control" id="edit_supplier_city" name="edit_supplier_city" placeholder="City" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="edit_supplier_country">Country</label>
            <input type="text" class="form-control" id="edit_supplier_country" name="edit_supplier_country" placeholder="Country" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="edit_supplier_cnic">CNIC</label>
            <input type="text" class="form-control" id="edit_supplier_cnic" name="edit_supplier_cnic" placeholder="CNIC" autocomplete="off">
          </div>
          <div id="edit_phone_info_div">
            <div class="form-group row" id="edit_row_1">
              <div class="col-xs-10">
                <label class="required-field" for="edit_phone_1">Phone</label>
                <input required="true" type="text" width="10%" class="form-control" id="edit_phone_1" name="edit_phone[]" placeholder="Phone" autocomplete="off">
              </div>
              <div class="col-xs-2">
                <br>
                <button style="margin-top: 9px" title="Add Multiple Phone" type="button" id="edit_add_row" class="btn btn-default"><i class="fa fa-plus"></i></button>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_supplier_email">Email</label>
            <input type="email" class="form-control" id="edit_supplier_email" name="edit_supplier_email" placeholder="Email" autocomplete="off">
          </div>
          <div class="form-group">
            <label for="edit_remarks">Remarks</label>
            <textarea type="text" class="form-control" id="edit_remarks" name="edit_remarks" placeholder="Remarks" autocomplete="off"></textarea>
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

<!-- remove supplier modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeSupplierModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Vendor</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Supplier/remove_supplier" method="post" id="removeSupplierForm">
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

<div class="modal fade" id="edit_photo">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
           <h4 class="modal-title"><b><span class="ven_name"></span></b></h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="<?php echo base_url() ?>index.php/Supplier/edit_photo" enctype="multipart/form-data">
            <input type="hidden" name="vendor_id" id="vendor_id">
            <div class="form-group">
              <label for="input_edit_photo">Select Image</label>
              <div class="kv-avatar">
                <div class="file-loading">
                  <input id="input_edit_photo" name="input_edit_photo" required="true" type="file">
                </div>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" name="upload" class="btn btn-primary">Save changes</button>
        </form>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
var manageTable;

$(document).ready(function() {

  var btnCust = '<button type="button" class="btn btn-secondary" title="Add picture tags" ' + 
      'onclick="alert(\'Call your custom code here.\')">' +
      '<i class="glyphicon glyphicon-tag"></i>' +
      '</button>'; 
  $("#input_edit_photo").fileinput({
      overwriteInitial: true,
      maxFileSize: 3000,
      showClose: false,
      showCaption: false,
      browseLabel: '',
      removeLabel: '',
      browseIcon: '<i class="glyphicon glyphicon-folder-open"></i>',
      removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
      removeTitle: 'Cancel or reset changes',
      elErrorContainer: '#kv-avatar-errors-1',
      msgErrorClass: 'alert alert-block alert-danger',
      // defaultPreviewContent: '<img src="/uploads/default_avatar_male.jpg" alt="Your Avatar">',
      layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
      allowedFileExtensions: ["jpg", "png", "gif"]
  });

  document.addEventListener("wheel", function(event){
      if(document.activeElement.type === "number" &&
         document.activeElement.classList.contains("noscroll"))
      {
          document.activeElement.blur();
      }
  });

  $("#mainVendorNav").addClass('active');
  $("#vendorsNav").addClass('active');
  $( ".required-field" ).append('<label style="color:red" for="name">*</label>');

  // initialize the datatable 
  var selected_vendor = '<?php if(isset($_GET['selected_vendor'])){ echo $_GET['selected_vendor']; }  ?>';

  manageTable = $('#manageTable').DataTable({
    'ajax': '<?php echo base_url()?>index.php/Supplier/fetchSupplierData/'+selected_vendor,
    'order': [],
    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
  });

  $("select").select2();

  // submit the create from 
  $("#createSupplierForm").unbind('submit').on('submit', function() {
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
          $("#addSupplierModal").modal('hide');

          // reset the form
          $("#createSupplierForm")[0].reset();

          $("#createSupplierForm .form-group").removeClass('has-error').removeClass('has-success');
          // reload data of select vendor
          $.ajax({
            url : "<?php echo base_url();?>index.php/Supplier/fetchSupplierDataForSelect",
            method : "POST",
            async : true,
            dataType : 'json',
            success: function(response){
              // console.log()
              // console.log(response.data.length)
              var html = '';
              var i;
              html += '<option value=""> --- Select Vendor ... -- </option>';
              for(i=0; i<response.data.length; i++){
                html += '<option value='+response.data[i].id+'>'+response.data[i].first_name+'</option>';
              }
              $('#selected_vendor').html(html);

            }
          });
          
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

  function editPhoto(id) {
    $.ajax({
      type: 'POST',
      url: '<?php echo base_url() ?>index.php/Supplier/get_vendor_row',
      data: {id:id},
      dataType: 'json',
      success: function(response){
        $('#vendor_id').val(response.data.id);
        $('.ven_name').html(response.data.first_name+' '+response.data.last_name);
      }
    });
  }


  function editSupplier(id)
  { 
    $.ajax({
      url: '<?php echo base_url();?>index.php/Supplier/fetchSupplierDataById/'+id,
      type: 'post',
      dataType: 'json',
      success:function(response) {

        var phones = response.phones;
        $("#edit_phone_1").val(response.phones[0]);
        for (var i = 1; i < phones.length; i++) 
        {
          var phone_info_div = $("#edit_phone_info_div");
          var div_len = $("#edit_phone_info_div .form-group.row").length;
          var row_id = div_len + 1;

          var html = 
          '<div class="form-group row" id="edit_row_'+row_id+'">'+
          '<div class="col-xs-10">'+
          '<input type="text" name="edit_phone[]" id="edit_phone_'+row_id+'" width="10%" class="form-control" placeholder="Phone" autocomplete="off">'+
          '</div>'+
          '<div class="col-xs-2">'+
          '<button type="button" class="btn btn-default" onclick="edit_removeRow(\''+row_id+'\')"><i class="fa fa-close"></i></button>'+
          '</div>'+
          '</div>';
          $("#edit_phone_info_div .form-group.row:last").after(html);

          $("#edit_phone_"+row_id+'').val(response.phones[i]);       
        }

        $("#edit_supplier_fname").val(response.data.first_name);
        $("#edit_supplier_lname").val(response.data.last_name);
        $("#edit_opening_balance").val(response.data.balance);
        $("#edit_supplier_address").val(response.data.address);
        $("#edit_supplier_city").val(response.data.city);
        $("#edit_supplier_country").val(response.data.country);
        $("#edit_supplier_cnic").val(response.data.cnic);
        $("#edit_supplier_email").val(response.data.email);
        $("#edit_remarks").val(response.data.remarks);


        // submit the edit from 
        $("#updateSupplierForm").unbind('submit').bind('submit', function() {
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
                $("#editSupplierModal").modal('hide');
                // reset the form 
                $("#updateSupplierForm .form-group").removeClass('has-error').removeClass('has-success');

                // reload data of select vendor
                $.ajax({
                  url : "<?php echo base_url();?>index.php/Supplier/fetchSupplierDataForSelect",
                  method : "POST",
                  async : true,
                  dataType : 'json',
                  success: function(response){
                    // console.log()
                    // console.log(response.data.length)
                    var html = '';
                    var i;
                    html += '<option value=""> --- Select Vendor ... -- </option>';
                    for(i=0; i<response.data.length; i++){
                      html += '<option value='+response.data[i].id+'>'+response.data[i].first_name+'</option>';
                    }
                    $('#selected_vendor').html(html);

                  }
                });
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

  $('#addSupplierModal').on('hidden.bs.modal', function(e)
  {
    var phone_info_div = $("#phone_info_div");
    var div_len = $("#phone_info_div .form-group.row").length;
    if(div_len > 0){
      for(var i = 1; i <= div_len; i++){
        console.log($("#phone_info_div .form-group.row#row_"+i+'').attr('id'));
        $("#phone_info_div .form-group.row#row_"+(i+1)).remove();
      }
    }
  }) ;

  $('#editSupplierModal').on('hidden.bs.modal', function(e)
  {
    var phone_info_div = $("#edit_phone_info_div");
    var div_len = $("#edit_phone_info_div .form-group.row").length;
    if(div_len > 0){
      for(var i = 1; i <= div_len; i++){
        console.log($("#edit_phone_info_div .form-group.row#edit_row_"+i+'').attr('id'));
        $("#edit_phone_info_div .form-group.row#edit_row_"+(i+1)).remove();
      }
    }
  }) ;

  $("#add_row").unbind('click').bind('click', function() {
    var phone_info_div = $("#phone_info_div");
    var div_len = $("#phone_info_div .form-group.row").length;
    var row_id = div_len + 1;

    var html = 
    '<div class="form-group row" id="row_'+row_id+'">'+
    '<div class="col-xs-10">'+
    '<input required="true" type="text" name="phone[]" id="phone_'+row_id+'" width="10%" class="form-control" placeholder="Phone" autocomplete="anyrandomstring">'+
    '</div>'+
    '<div class="col-xs-2">'+
    '<button type="button" class="btn btn-default" onclick="removeRow(\''+row_id+'\')"><i class="fa fa-close"></i></button>'+
    '</div>'+
    '</div>';
    if(div_len >= 1) {
      $("#phone_info_div .form-group.row:last").after(html);  
    }
    else {
      $("#phone_info_div .form-group.row").html(html);
    }
    }); // document

  $("#edit_add_row").unbind('click').bind('click', function() {
    var phone_info_div = $("#edit_phone_info_div");
    var div_len = $("#edit_phone_info_div .form-group.row").length;
    var row_id = div_len + 1;

    var html = 
    '<div class="form-group row" id="edit_row_'+row_id+'">'+
    '<div class="col-xs-10">'+
    '<input required="true" type="text" name="edit_phone[]" id="edit_phone_'+row_id+'" width="10%" class="form-control" placeholder="Phone" autocomplete="anyrandomstring">'+
    '</div>'+
    '<div class="col-xs-2">'+
    '<button type="button" class="btn btn-default" onclick="edit_removeRow(\''+row_id+'\')"><i class="fa fa-close"></i></button>'+
    '</div>'+
    '</div>';
    if(div_len >= 1) {
      $("#edit_phone_info_div .form-group.row:last").after(html);  
    }
    else {
      $("#edit_phone_info_div .form-group.row").html(html);
    }
  }); // document


  function removeRow(r_id)
  {
    $("#phone_info_div .form-group.row#row_"+r_id).remove();
    subAmount();
  }

  function edit_removeRow(r_id)
  {
    $("#edit_phone_info_div .form-group.row#edit_row_"+r_id).remove();
    subAmount();
  }

  function removeSupplier(id)
  {
    if(id) {
      $("#removeSupplierForm").on('submit', function() {

        var form = $(this);

        // remove the text-danger
        $(".text-danger").remove();

        $.ajax({
          url: form.attr('action'),
          type: form.attr('method'),
          data: { supplier_id:id }, 
          dataType: 'json',
          success:function(response) {

            manageTable.ajax.reload(null, false); 

            if(response.success === true) {
              $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
                '</div>');

              // hide the modal
              $("#removeSupplierModal").modal('hide');
              // reload data of select vendor
              $.ajax({
                url : "<?php echo base_url();?>index.php/Supplier/fetchSupplierDataForSelect",
                method : "POST",
                async : true,
                dataType : 'json',
                success: function(response){
                  // console.log()
                  // console.log(response.data.length)
                  var html = '';
                  var i;
                  html += '<option value=""> --- Select Vendor ... -- </option>';
                  for(i=0; i<response.data.length; i++){
                    html += '<option value='+response.data[i].id+'>'+response.data[i].first_name+'</option>';
                  }
                  $('#selected_vendor').html(html);

                }
              });

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

  $(function(){

    $("#selected_vendor").on('change', function(){
      var selected_vendor = encodeURI($(this).val());
      window.location = '<?php echo base_url() ?>index.php/Supplier/index?selected_vendor='+selected_vendor;
    });


  });


</script>

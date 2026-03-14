<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Users</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Users</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">

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
        <?php if(in_array('createUser', $user_permission)): ?>
          <button title="Create User" class="btn btn-success" data-toggle="modal" data-target="#addUserModal">
            <span class="glyphicon glyphicon-plus"></span>
          </button>
        <?php endif; ?>
        
        <?php if(in_array('printUser', $user_permission)): ?>
          <a title="Print Users" target="__blank" href="<?php base_url() ?>print_users" class="btn btn-info" id="print">
            <span class="glyphicon glyphicon-print"></span>
          </a>
        <?php endif; ?>
        <?php if( ( in_array('createUser', $user_permission) ) || ( in_array('printUser', $user_permission) ) ): ?>
          <br /> <br />
        <?php endif; ?>
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Users</h3>    
          </div>
          <div class="box-body table-responsive">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th style="color:#3c8dbc">Image</th>
                  <th style="color:#3c8dbc">Name</th>
                  <th style="color:#3c8dbc">Username</th>
                  <th style="color:#3c8dbc">Email</th>
                  <th style="color:#3c8dbc">Password</th>
                  <?php if( (in_array('createUser', $user_permission)) || (in_array('updateUser', $user_permission)) || (in_array('deleteUser', $user_permission)) ): ?>
                    <th width="7%"></th>
                  <?php endif; ?>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<div class="modal fade" tabindex="-1" role="dialog" id="addUserModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add User</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/User/create_user" method="post" id="createUserForm">

        <div class="modal-body">

          <div class="form-group">
            <label class="required-field" for="fname">First Name</label>
            <input type="text" class="form-control" id="fname" name="fname" placeholder="First name" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" class="form-control" id="lname" name="lname" placeholder="Last name" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email" autocomplete="off">
          </div>

          <div class="form-group">
            <label class="required-field" for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" autocomplete="off">
          </div>

          <div class="form-group">
            <label class="required-field" for="cpassword">Confirm password</label>
            <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Confirm Password" autocomplete="off">
          </div>


          <div id="phone_info_div">
            <div class="form-group row" id="row_1">
              <div class="col-xs-10">
                <label class="required-field" for="phone_1">Phone</label>
                <input required="true" type="text" width="10%" class="form-control" id="phone_1" name="phone[]" placeholder="Phone" autocomplete="off">
              </div>
              <div class="col-xs-2">
                <br>
                <button style="margin-top: 9px" title="Add Multiple Phone" type="button" id="add_row" class="btn btn-default"><i class="fa fa-plus"></i></button>
              </div>
            </div>
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

<!-- edit User modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="editUserModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit User</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/User/edit_user" method="post" id="updateUserForm">

        <div class="modal-body">
          <div id="messages"></div>

          <div class="form-group">
            <label class="required-field" for="edit_fname">First name</label>
            <input type="text" class="form-control" id="edit_fname" name="edit_fname" placeholder="First name" autocomplete="off">
          </div>

          <div class="form-group">
            <label for="edit_lname">Last name</label>
            <input type="text" class="form-control" id="edit_lname" name="edit_lname" placeholder="Last name" autocomplete="off">
          </div>

          <div class="form-group">
            <label class="required-field" for="edit_username">Username</label>
            <input type="text" class="form-control" id="edit_username" name="edit_username" placeholder="Username" autocomplete="off">
          </div>
          <div class="form-group">
            <label class="required-field" for="edit_email">Email</label>
            <input type="email" class="form-control" id="edit_email" name="edit_email" placeholder="Email" autocomplete="off">
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
            <div class="alert alert-info alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <p> Leave the password field empty if you don't want to change.</p>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_password">Password</label>
            <input type="password" class="form-control" id="edit_password" name="edit_password" placeholder="Password"  autocomplete="off">
          </div>

          <div class="form-group">
            <label for="edit_cpassword">Confirm password</label>
            <input type="password" class="form-control" id="edit_cpassword" name="edit_cpassword" placeholder="Confirm Password" autocomplete="off">
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

<div class="modal fade" tabindex="-1" role="dialog" id="viewUserModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">View User</h4>
      </div>

      <div class="modal-body">
        <div id="messages"></div>

        <div class="form-group">
          <label for="view_fname">First name</label>
          <input readonly type="text" class="form-control" id="view_fname" name="view_fname" placeholder="First name" autocomplete="off">
        </div>

        <div class="form-group">
          <label for="view_lname">Last name</label>
          <input readonly type="text" class="form-control" id="view_lname" name="view_lname" placeholder="Last name" autocomplete="off">
        </div>

        <div class="form-group">
          <label for="view_username">Username</label>
          <input readonly type="text" class="form-control" id="view_username" name="view_username" placeholder="Username" autocomplete="off">
        </div>
        <div class="form-group">
          <label for="view_email">Email</label>
          <input readonly type="email" class="form-control" id="view_email" name="view_email" placeholder="Email" autocomplete="off">
        </div>                
        <div id="view_phone_info_div">
          <div class="form-group" id="view_row_1">
            <label for="view_phone_1">Phone</label>
            <input readonly required="true" type="text" class="form-control" id="view_phone_1" name="view_phone[]" placeholder="Phone" autocomplete="off">
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- remove user modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeUserModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove User</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/User/remove_user" method="post" id="removeUserForm">
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
           <h4 class="modal-title"><b><span class="user_name"></span></b></h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="<?php echo base_url() ?>index.php/User/edit_photo" enctype="multipart/form-data">
            <input type="hidden" name="user_id" id="user_id">
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

    var btnUser = '<button type="button" class="btn btn-secondary" title="Add picture tags" ' + 
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
        layoutTemplates: {main2: '{preview} ' +  btnUser + ' {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "gif"]
    });

    $("#mainUserPermissionsNav").addClass('active');
    $("#mainUserPermissionsNav").addClass('menu-open');
    $("#manageUserNav").addClass('active');

    $( ".required-field" ).append('<label style="color:red" for="name">*</label>');

    // initialize the datatable 
    manageTable = $('#manageTable').DataTable({
      'ajax': '<?php echo base_url()?>index.php/User/fetchUserData',
      'order': [],
      "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
    });

    // submit the create from 
    $("#createUserForm").unbind('submit').on('submit', function() {
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
            $("#addUserModal").modal('hide');

            // reset the form
            $("#createUserForm")[0].reset();
    
            $("#createUserForm .form-group").removeClass('has-error').removeClass('has-success');
            

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

  $("#add_row").unbind('click').bind('click', function() {
    var phone_info_div = $("#phone_info_div");
    var div_len = $("#phone_info_div .form-group.row").length;
    var row_id = div_len + 1;
    
    var html = 
      '<div class="form-group row" id="row_'+row_id+'">'+
        '<div class="col-xs-10">'+
          '<input required="true" type="text" name="phone[]" id="phone_'+row_id+'" width="10%" class="form-control" placeholder="Phone" autocomplete="off">'+
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
          '<input required="true" type="text" name="edit_phone[]" id="edit_phone_'+row_id+'" width="10%" class="form-control" placeholder="Phone" autocomplete="off">'+
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

  // edit function
  function userEditFunc(id)
  { 
    $.ajax({
      url: '<?php echo base_url();?>index.php/User/fetchUserDataById/'+id,
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

        $("#edit_username").val(response.data.username);
        $("#edit_email").val(response.data.email);
        $("#edit_fname").val(response.data.firstname);
        $("#edit_lname").val(response.data.lastname);

        // submit the edit from 
        $("#updateUserForm").unbind('submit').bind('submit', function() {
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

              console.log(response.messages);

              if(response.success === true) {
                $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
                  '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                  '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
                '</div>');


                // hide the modal
                $("#editUserModal").modal('hide');
                // reset the form 
                $("#updateUserForm .form-group").removeClass('has-error').removeClass('has-success');

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

  function editPhoto(id) {
    $.ajax({
      type: 'POST',
      url: '<?php echo base_url() ?>index.php/User/get_user_row',
      data: {id:id},
      dataType: 'json',
      success: function(response){
        $('#user_id').val(response.data.id);
        $('.user_name').html(response.data.firstname+' '+response.data.lastname);
      }
    });
  }

  function userViewFunc(id)
  { 
    $.ajax({
      url: '<?php echo base_url();?>index.php/User/fetchUserDataById/'+id,
      type: 'post',
      dataType: 'json',
      success:function(response) {

        var phones = response.phones;
        $("#view_phone_1").val(response.phones[0]);
        for (var i = 1; i < phones.length; i++) 
        {
          var phone_info_div = $("#view_phone_info_div");
          var div_len = $("#view_phone_info_div .form-group").length;
          var row_id = div_len + 1;

          var html = 
          '<div class="form-group" id="view_row_'+row_id+'">'+
          '<input readonly type="text" name="view_phone[]" id="view_phone_'+row_id+'" class="form-control" placeholder="Phone" autocomplete="off">'+
          '</div>';
          $("#view_phone_info_div .form-group:last").after(html);

          $("#view_phone_"+row_id+'').val(response.phones[i]);       
        }        

        $("#view_username").val(response.data.username);
        $("#view_email").val(response.data.email);
        $("#view_fname").val(response.data.firstname);
        $("#view_lname").val(response.data.lastname);

      }
    });
  }

  $('#addUserModal').on('hidden.bs.modal', function(e)
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

  $('#editUserModal').on('hidden.bs.modal', function(e)
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

  // remove functions 
function userRemoveFunc(id)
{
  if(id) {
    $("#removeUserForm").on('submit', function() {

      var form = $(this);

      // remove the text-danger
      $(".text-danger").remove();

      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: { user_id:id }, 
        dataType: 'json',
        success:function(response) {

          manageTable.ajax.reload(null, false); 

          if(response.success === true) {
            $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
            '</div>');

            // hide the modal
            $("#removeUserModal").modal('hide');

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

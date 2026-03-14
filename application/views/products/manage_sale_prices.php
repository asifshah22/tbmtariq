

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Sale Prices</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Sale Prices</li>
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

        <?php if(in_array('createSalePricesNE', $user_permission)): ?>
          <button title="Add New Sale Price" class="btn btn-success" data-toggle="modal" data-target="#addModal"><i class="glyphicon glyphicon-plus"></i></button>
        <?php endif; ?>
        <?php if(in_array('printSalePricesNE', $user_permission)): ?>
          <a title="Print Sale Prices" target="__blank" href="<?php base_url() ?>print_sale_prices" class="btn btn-info" id="print">
            <span class="glyphicon glyphicon-print"></span>
          </a>
        <?php endif ?>
        <?php if( ( in_array('createSalePricesNE', $user_permission) ) || ( in_array('printSalePricesNE', $user_permission) ) ): ?>
          <br /> <br />
        <?php endif ?>
          
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Sale Prices</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th width="3%">#</th>
                <th style="color:#3c8dbc">DateTime</th>
                <th style="color:#3c8dbc">Category</th>
                <th style="color:#3c8dbc">Item Name</th>
                <th style="color:#3c8dbc">Unit</th>
                <th style="color:#3c8dbc">Price</th>
                <?php if( ( in_array('updateSalePricesNE', $user_permission) ) || ( in_array('deleteSalePricesNE', $user_permission) ) ): ?>
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
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Add Sale Price</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Product/create_sale_price" method="post" id="createForm">

        <div class="modal-body">

          <div class="form-group">
            <label class="required-field" for="select_product">Product</label>
            <select style="width: 100%" required="true" class="form-control" id="select_product" name="select_product">
              <option value="">Select Product</option>
              <?php foreach($product_category_data as $key => $value): ?>
                <?php
                  $category_name = '';
                  $category_id = '';
                  if($value['category_id'] == NULL)
                  {
                    $category_name = '';
                    $category_id = ' ';
                  }
                  else
                  {
                    $category_name = ' &#8212 ' .$value['category_name'];
                    $category_id = $value['category_id'];
                  }
                ?>
                <option value="<?php echo $value['product_id'].'-'.$category_id ?>"><?php echo $value['product_name']. ' ' . $category_name ?></option>  
              <?php endforeach; ?>
              
            </select>
          </div>
          <div class="form-group">
            <label class="required-field" for="select_unit">Unit</label>
            <select style="width: 100%" required="true" class="form-control" id="select_unit" name="select_unit">
              <option value="">Select Unit</option>
              <?php foreach($units_data as $key => $value): ?>
                <option value="<?php echo $value['id'] ?>"><?php echo $value['unit_name']; ?></option>  
              <?php endforeach; ?>
              
            </select>
          </div>
          <div class="form-group">
            <label class="required-field" for="sale_price">Sale Price</label>
            <input type="number" class="form-control noscroll" min="0.1" step="any" name="sale_price" id="sale_price" placeholder="Sale Price">
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

<!-- edit brand modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Sale Price</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Product/update_sale_price" method="post" id="updateForm">

        <div class="modal-body">
          <div id="messages"></div>
          <div class="form-group">
            <label class="required-field" for="edit_select_product">Product</label>
            <select style="width: 100%" required="true" class="form-control" id="edit_select_product" name="edit_select_product">
              <option value="">Select Product</option>
              <?php foreach($product_category_data as $key => $value): ?>
                <?php
                  $category_name = '';
                  $category_id = '';
                  if($value['category_id'] == NULL)
                  {
                    $category_name = '';
                    $category_id = ' ';
                  }
                  else
                  {
                    $category_name = ' &#8212 ' .$value['category_name'];
                    $category_id = $value['category_id'];
                  }
                ?>
                <option value="<?php echo $value['product_id'].'-'.$category_id ?>"><?php echo $value['product_name']. ' '. $category_name ?></option>  
              <?php endforeach; ?>
              
            </select>
          </div>
          <div class="form-group">
            <label class="required-field" for="edit_select_unit">Unit</label>
            <select style="width: 100%" required="true" class="form-control" id="edit_select_unit" name="edit_select_unit">
              <option value="">Select Unit</option>
              <?php foreach($units_data as $key => $value): ?>
                <option value="<?php echo $value['id'] ?>"><?php echo $value['unit_name']; ?></option>  
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="required-field" for="edit_product_price">Sale Price</label>
            <input type="number" class="form-control noscroll" min="0.1" step="any" name="edit_sale_price" id="edit_sale_price" placeholder="Sale Price">
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


<!-- remove brand modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Sale Price</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Product/remove_sale_price" method="post" id="removeForm">
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
var base_url = "<?php echo base_url(); ?>";

$(document).ready(function() {

  $("#mainSalesNav").addClass('active');
  $("#manageSalePricesNav").addClass('active');

  $( ".required-field" ).append('<label style="color:red" for="name">*</label>');
  // initialize the datatable 
  manageTable = $('#manageTable').DataTable({
    'ajax': base_url + 'index.php/Product/fetchSalePricesData',
    'order': [],
    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
  });

  $("select").select2();

  document.addEventListener("wheel", function(event){
    if(document.activeElement.type === "number" &&
     document.activeElement.classList.contains("noscroll"))
    {
      document.activeElement.blur();
    }
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


function editFunc(id)
{
  $.ajax({
    url: '<?php echo base_url();?>index.php/Product/fetchSalePriceDataById/'+id,
    type: 'post',
    dataType: 'json',
    success:function(response) {
      var value = '';
      if(response.category_id == 0)
      {
        value = response.product_id + '- ';
      }
      else
      {
        value = response.product_id + '-' + response.category_id
      }
      $("#edit_select_product").val(value);
      $('#edit_select_product').select2().trigger('change');

      $("#edit_select_unit").val(response.unit_id);
      $('#edit_select_unit').select2().trigger('change');

      $("#edit_sale_price").val(response.price);

      // submit the edit from 
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
        data: { sale_price_id:id }, 
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

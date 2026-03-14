

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Company Sales</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Manage Company Sales</li>
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
        <?php if(in_array('createSaleOrderE', $user_permission)): ?> 
          <a title="Create Company Sale Order" href="<?php echo base_url() ?>index.php/Product/create_company_sale_order" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i></a>
        <?php endif; ?>
        <?php if(in_array('printSaleOrderE', $user_permission)): ?> 
          <a title="Print Company Sale Order" target="__blank" href="<?php base_url() ?>print_company_sales_order" class="btn btn-info" id="print">
            <span class="glyphicon glyphicon-print"></span>
          </a>
        <?php endif; ?>
        <?php if( ( in_array('createSaleOrderE', $user_permission) ) || ( in_array('printSaleOrderE', $user_permission) ) ): ?>
          <br /> <br />
        <?php endif; ?>
        
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Company Sales</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th width="3%">#</th>
                <th style="color:#3c8dbc">Bill no</th>
                <th style="color:#3c8dbc">DateTime</th>
                <th style="color:#3c8dbc">Customer</th>
                <th style="color:#3c8dbc">Department</th>
                <th style="color:#3c8dbc">Products</th>
                <th style="color:#3c8dbc">Stock</th>
                <th style="color:#3c8dbc">Created By</th>
                <th width="20%" style="color:#3c8dbc">Remarks</th>
                <?php if( ( in_array('viewSaleOrderE', $user_permission) ) || ( in_array('updateSaleOrderE', $user_permission) ) || ( in_array('deleteSaleOrderE', $user_permission) ) || ( in_array('printSaleOrderE', $user_permission) ) ): ?>
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
        <h4 class="modal-title">Remove Sale</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Product/remove_company_sale_order" method="post" id="removeForm">
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
  $("#manageCompanySalesNav").addClass('active');

  // initialize the datatable 
  manageTable = $('#manageTable').DataTable({
    'ajax': base_url + 'index.php/Product/fetchCompanySaleOrders',
    'order': [],
    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
  });

});

// remove functions 
function removeCompanySaleOrder(id)
{
  if(id) {
    $("#removeForm").on('submit', function() {

      var form = $(this);
      // remove the text-danger
      $(".text-danger").remove();

      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: { company_sale_order_id:id }, 
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

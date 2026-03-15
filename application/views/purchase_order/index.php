<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Purchase Order
      <small>Listing</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Purchase Order</li>
    </ol>
  </section>

  <section class="content">
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
          <a title="Add Purchase Order" href="<?php echo base_url()?>index.php/Purchase_order/create_form" class="btn btn-success">
            <i class="glyphicon glyphicon-plus"></i>
          </a>
        <?php endif; ?>
        <br /><br />

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Purchase Order List</h3>
          </div>
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th width="3%">#</th>
                  <th style="color:#3c8dbc">Date</th>
                  <th style="color:#3c8dbc">PO Number</th>
                  <th style="color:#3c8dbc">Vendor</th>
                  <th style="color:#3c8dbc">Total</th>
                  <th style="color:#3c8dbc">Sales Tax</th>
                  <th style="color:#3c8dbc">Grand Total</th>
                  <th width="10%"></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

      </div>
    </div>
  </section>
</div>

<!-- remove modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Purchase Order</h4>
      </div>
      <form role="form" action="<?php echo base_url()?>index.php/Purchase_order/remove" method="post" id="removeForm">
        <div class="modal-body">
          <p>Do you really want to remove?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  var manageTable;
  var base_url = "<?php echo base_url(); ?>";

  $(document).ready(function() {
    $("#mainPurchasingNav").addClass('active');
    $("#purchaseOrderNav").addClass('active');

    manageTable = $('#manageTable').DataTable({
      'ajax': base_url + 'index.php/Purchase_order/fetchOrders',
      'order': [],
      "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
    });
  });

  function removePurchaseOrder(id)
  {
    if(id) {
      $("#removeForm").on('submit', function() {
        var form = $(this);
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



<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Stock
      <small>Stock Order Level</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Stock Order Level</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">
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

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Stock Order Level</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th width="3%">#</th>
                <th style="color:#3c8dbc">Stock Type</th>
                <th style="color:#3c8dbc">Level Value</th>
                <?php if(in_array('updateStockOrderLevel', $user_permission)): ?>
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

<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><span class="edit_modal_heading"></span></h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Product/edit_stock_order_level" method="post">
        <div class="modal-body">
          <input type="hidden" name="stock_order_level_id" id="stock_order_level_id">

          <div class="form-group">
            <label for="order_level_value">Order Level Vale</label>
            <input type="number" min="1" required class="form-control noscroll" id="order_level_value" name="order_level_value" placeholder="Order Level Value" autocomplete="off">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" name="submit" class="btn btn-primary">Save changes</button>
        </div>

      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script type="text/javascript">
  var manageTable;
  var base_url = "<?php echo base_url(); ?>";

  $(document).ready(function() {

    $("#mainStockNav").addClass('active');
    $("#stockOrderLevelNav").addClass('active');

    document.addEventListener("wheel", function(event){
      if(document.activeElement.type === "number" &&
       document.activeElement.classList.contains("noscroll"))
      {
        document.activeElement.blur();
      }
    });

    // initialize the datatable 
    manageTable = $('#manageTable').DataTable({
      'ajax': base_url + 'index.php/Product/fetchStockOrderLevelData',
      'order': []
    });

  });

  function editFunc(id) {
    $.ajax({
      type: 'POST',
      url: '<?php echo base_url() ?>index.php/Product/get_stock_order_level_row',
      data: {id:id},
      dataType: 'json',
      success: function(response){
        $('#stock_order_level_id').val(response.data.id);
        $('#order_level_value').val(response.data.value);
        if(response.data.stock_type == 1){
          $('.edit_modal_heading').html("Edit Factory Stock Order Level");
        }
        else if(response.data.stock_type == 2){
          $('.edit_modal_heading').html("Edit Office Stock Order Level");
        }
      }
    });
  }

</script>

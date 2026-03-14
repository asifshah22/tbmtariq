

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Office Stock</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Office Stock</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">
        
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Office Stock</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th width="8">#</th>
                <th width="23%" style="color:#3c8dbc">Category</th>
                <th width="23%" style="color:#3c8dbc">Item</th>
                <th width="23%" style="color:#3c8dbc">Unit</th>
                <th width="23%" style="color:#3c8dbc">
                  Quantity &nbsp;<a href="#" data-toggle="tooltip" title="Quantity without units are shown with other!"><i class="fa fa-question-circle"></i></a>
                </th>
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

<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.print.min.js"></script>
<script type="text/javascript">
var manageTable;
var base_url = "<?php echo base_url(); ?>";

$(document).ready(function() {

  $("#mainStockNav").addClass('active');
  $("#viewOfficeStockNav").addClass('active');
  // datetime
  let datetime = new Date().toLocaleString().replace(",","").replace(/:.. /," ").replace("/", "-").replace("/", "-");

  // initialize the datatable
  manageTable = $('#manageTable').DataTable({
    'ajax': base_url + 'index.php/Product/fetchOfficeStockData',
    'order': [2, "asc"],
    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'print',
        text: '<i class="fa fa-print"></i>'+' Print',
        messageTop: '<p style="font-size: 14px; float:right;"><strong>Printed At</strong>: '+datetime+'</p>'
      }
    ]
  });
  $(".dt-button.buttons-print").addClass('btn btn-info');
});

</script>

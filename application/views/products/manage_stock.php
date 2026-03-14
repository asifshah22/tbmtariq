<?php
  date_default_timezone_set("Asia/Karachi");
  $range_to = date('m/d/Y');
  $range_from = date('m/d/Y', strtotime('01/01/2019'));
  $date_time = date('m/d/Y', strtotime('01/01/2019'));

  //print_r($result);


?>

<style>

    .dataTables_length {

        display: inline;

    }

    #manageTable_filter {

        display: inline;

        float: right;

    }

    .dt-buttons {

        margin-bottom: 1rem;

    }

    .deleted-row {
    background-color: #f2dede; /* light red background color */
}


</style>



<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

  <!-- Content Header (Page header) -->

  <section class="content-header">

    <h1>

      Manage

      <small>Factory Stock</small>

    </h1>

    <ol class="breadcrumb">

      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

      <li class="active">Factory Stock</li>

    </ol>

  </section>



  <!-- Main content -->

  <section class="content">

    <!-- Small boxes (Stat box) -->

    <div class="row">

      <div class="col-md-12 col-xs-12">

        <div class="box">

          <div class="box-header">

            <h3 class="box-title">Factory Stock</h3>

            <div class="pull-right hidden">
              <form method="POST" class="form-inline" id="adjustProductRateForm">
                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <!-- <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range" value=""> -->
<!--                   <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range" value="<?php //echo (isset($_GET['range'])) ? $_GET['range'] : $range_from.' - '.$range_to; ?>">
 -->
                      <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range" value="<?php echo isset($_GET['date_time']) ? htmlspecialchars($_GET['date_time']) : ''; ?>">

                <?php /* if(in_array('printVendorItemsRate', $user_permission)): ?>
                  <a href="<?php echo base_url() ?>index.php/Reports/print_items_rate<?php if(isset($_GET['range'])){ echo "?range=".$_GET['range']; } else if(isset($_GET['selected_vendor'])){ echo "?selected_vendor=".$_GET['selected_vendor']; } ?>" target="__blank" class="btn btn-success btn-sm btn-flat" id="print"><span class="glyphicon glyphicon-print"></span> Print</a>
                <?php endif; */ ?>
              </form>
            </div>

          </div>

          <!-- /.box-header -->

          <div class="box-body">

            <table id="manageTable" class="table table-bordered table-hover">

              <thead>

              <tr>
	<!--               <th width="8%" ></th>
 -->
                <th width="8%" ><b>S #</b></th>

                <th width="23%" style="color:#3c8dbc">Category<!-- Category --></th>

                <th width="23%" style="color:#3c8dbc">Items</th>

                <th width="23%" style="color:#3c8dbc">Unit</th>

                <!-- <th width="23%" style="color:#3c8dbc">Date</th> -->

                <th width="23%" style="color:#3c8dbc">

                  Quantity &nbsp;<a href="#" data-toggle="tooltip" title="Quantity without units are shown with other!"><i class="fa fa-question-circle"></i></a>

                </th>
                <th width="23%" style="color:#3c8dbc">Remarks</th>
                <?php
                $user_id = $this->session->userdata('id');
                if($user_id == 6){
                ?>
                 <th width="23%" style="color:#3c8dbc">action</th>
                <?php } ?>


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

  $("#manageStockNav").addClass('active');

  // datetime

  let datetime = new Date().toLocaleString().replace(",","").replace(/:.. /," ").replace("/", "-").replace("/", "-");

  var dateRange = $('#reservation').val(); // Get the value of the date range input field

// Extracting only the date part from the dateRange string
var selectedDate = dateRange.split(' - ')[0];
//alert(selectedDate);
// Define the AJAX URL with the date parameter
var ajaxUrl = base_url + 'index.php/Product/fetchStockData?date_time=' + encodeURI(selectedDate);

// Initialize DataTable with AJAX and other options
var manageTable = $('#manageTable').DataTable({
    'ajax': ajaxUrl,
    'order': [2, "asc"],
    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
    dom: 'Blfrtip',
    buttons: [{
        extend: 'print',
        text: '<i class="fa fa-print"></i>' + ' Print',
        messageTop: '<p style="font-size: 14px; float:right;"><strong>Printed At</strong>: ' + datetime + '</p>'
    }],
    "rowCallback": function( row, data ) {//console.log(data);
      console.log(data); // Check if data is logged properly
        console.log(data[0]); // Check the value of is_deleted
        // Check if the record is deleted (is_deleted = 1)
        if (data[0] == 'Deleted') { // Assuming is_deleted column ka index 5 hai
            // Highlight the row (change background color to red)
            //alert(123);
            $(row).addClass('deleted-row');
        }
    }
});


$(document).on('click', '.delete-record', function(event) {
        event.preventDefault(); // Prevent the default action of the link

        var recordId = $(this).data('id');
        var successMessage = $('#success-message'); // Element to display success message
        //alert(recordId);
        //return false;
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: base_url + 'index.php/Product/manage_stock_remove',
                type: 'POST',
                data: {id: recordId},
                success: function(response) {
                    //successMessage.show(); // Show the success message
                    alert("Deleted successfully"); // Show success message in alert
                    manageTable.ajax.reload();
                },
                error: function(xhr, status, error) {
                    alert('Error deleting record');
                    console.error(xhr.responseText);
                }
            });
        }
    });


  // initialize the datatable

  /* manageTable = $('#manageTable').DataTable({

    'ajax': base_url + 'index.php/Product/fetchStockData',

    'order': [2, "asc"],

    "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],

    dom: 'Blfrtip',

    buttons: [

      {

        extend: 'print',

        text: '<i class="fa fa-print"></i>'+' Print',

        messageTop: '<p style="font-size: 14px; float:right;"><strong>Printed At</strong>: '+datetime+'</p>'

      }

    ]

  }); */


  $(".dt-button.buttons-print").addClass('btn btn-info');



});



</script>

<script>

$(function(){
  //Date range picker
  $('#reservation').datepicker()

});

$(function(){
    $('#reservation').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true, // Optional: to show month and year dropdowns
        minYear: 2000, // Set the minimum year
        maxYear: 2040, // Set the maximum year
        locale: {
            format: 'YYYY-MM-DD' // Set the date format as MM/DD/YYYY
        }
    });

    $('#reservation').on('apply.daterangepicker', function(ev, picker) {
        var formattedDate = picker.startDate.format('YYYY-MM-DD'); // Format the date as YYYY-MM-DD
        //alert(formattedDate);
        window.location = '<?php echo base_url() ?>index.php/Product/manage_stock?date_time=' + formattedDate;
    });

});

</script>
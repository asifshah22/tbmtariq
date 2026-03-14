<?php date_default_timezone_set("Asia/Karachi"); ?>



<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

  <!-- Content Header (Page header) -->

  <section class="content-header">

    <h1>

      View

      <small>Purchased Order</small>

    </h1>

    <ol class="breadcrumb">

      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

      <li class="active">View Purchased Order</li>

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

        <?php endif; ?>



        <?php if(in_array('updatePurchasing', $user_permission)): ?>

          <a href="<?php echo base_url() ?>index.php/Product/update_purchase_order/<?php echo $purchase_order_data['id'] ?>" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>

        <?php endif; ?>



        <?php if(in_array('printPurchasing', $user_permission)): ?>

          <a title="Print Purchased Order" class="btn btn-info" target="__blank" href="<?php echo base_url('index.php/Product/print_invoice/'.$purchase_order_data['id']) ?>"><span class="glyphicon glyphicon-print"></span></a>

        <?php endif; ?>

        <?php if( ( in_array('updatePurchasing', $user_permission) ) || ( in_array('deletePurchasing', $user_permission) ) || ( in_array('printPurchasing', $user_permission) ) ): ?>

          <br /> <br />

        <?php endif; ?>

       

        <div class="box">

          <div class="box-header">

            <h3 class="box-title">View</h3>

          </div>

          <!-- /.box-header -->

          <div class="form-horizontal">

          	<div class="box-body">

              <table class="table table-striped table-bordered">

                <tbody>

                  <tr>

                    <td><strong>ID</strong></td>

                    <td><?php echo $purchase_order_data['id']; ?></td>

                  </tr>

                  <tr>

                    <td><strong>Vendor Name</strong></td>

                    <td>

                      <span style="text-transform: capitalize;">

                        <?php echo $vendor_data['first_name']. ' '. $vendor_data['last_name']; ?>  

                      </span>

                    </td>

                  </tr>



                  <tr>

                    <td><strong>Opening Balance</strong></td>

                    <td><?php echo floatval($purchase_order_data['opening_balance']); ?></td>

                  </tr>



                  <tr>

                    <td><strong>Gross Amount</strong></td>

                    <td><?php echo floatval($purchase_order_data['gross_amount']); ?> </td>

                  </tr>

                  <?php $return_items_total_amount = 0; ?>

                  

                  <?php if(!empty($purchase_return_data)): ?>

                    <?php

                      foreach ($purchase_return_data as $k => $v) 

                      {

                        $return_items_total_amount += $v['amount'];

                      }

                    ?>

                    <tr>

                      <td><strong>Returns Amount</strong></td>

                      <td><?php echo floatval($return_items_total_amount); ?> </td>

                    </tr>

                    <tr>

                      <td><strong>New Gross Amount</strong></td>

                      <td><?php echo floatval($purchase_order_data['gross_amount'] - $return_items_total_amount); ?> </td>

                    </tr>

                  <?php endif; ?>



                  <tr>

                    <td><strong>Discount</strong></td>

                    <td><?php echo floatval($purchase_order_data['discount']); ?> </td>

                  </tr>

                  

                   <tr>

                    <td><strong>Sales tax (<?php echo floatval($purchase_order_data['sales_tax']); ?>%)</strong></td>

                    <td><?php echo floatval($purchase_order_data['sales_tax_value']); ?></td>

                  </tr>
                  <tr>
                  <td><strong>Sales tax total</strong></td>
                    <td><?php echo floatval($purchase_order_data['sales_tax_value_total']); ?></td>

                  </tr>

                  

                   <tr>

                    <td><strong>W.H.T (<?php echo floatval($purchase_order_data['w_h_t']); ?>%)</strong></td>

                    <td><?php echo floatval($purchase_order_data['w_h_t_value']); ?></td>

                  </tr>

                  <tr>

                    <td><strong>W.H.T total</strong></td>

                    <td><?php echo floatval($purchase_order_data['w_h_t_value_total']); ?></td>

                  </tr>



                  <tr>

                    <td><strong>Loan Deduction</strong></td>

                    <td>

                      <?php

                       $loan_deduction = 0;

                       if(!empty($loan_deductions))

                       {

                          $loan_deduction = $loan_deductions['deduction_amount'];

                       }

                      ?>

                      <?php echo floatval($loan_deduction); ?> 

                    </td>

                  </tr>



                  <tr>

                    <td><strong>Freight</strong></td>

                    <td><?php echo floatval($purchase_order_data['loading_or_affair']); ?></td>

                  </tr>



                  <tr>

                    <td><strong>Fine Deduction</strong></td>

                    <td><?php echo floatval($purchase_order_data['fine_deduction']); ?></td>

                  </tr>



                  <tr>

                    <td><strong>Other Deduction</strong></td>

                    <td><?php echo floatval($purchase_order_data['other_deduction']); ?></td>

                  </tr>



                  <tr>

                    <td><strong>Net Amount</strong></td>

                    <td><?php echo floatval($purchase_order_data['net_amount'] - $return_items_total_amount); ?>  </td>

                  </tr>



                  <tr>

                    <td><strong>Total Amount Recieved</strong></td>

                    <td><?php echo floatval($purchase_order_data['total_paid']); ?> </td>

                  </tr>



                  <tr>

                    <td><strong>Remaining Balance</strong></td>

                    <td>

                      <?php

                        $remaining_balance = $purchase_order_data['opening_balance'] + $purchase_order_data['net_amount'] - $purchase_order_data['total_paid'] - $return_items_total_amount;

                      ?>

                      <?php echo floatval($remaining_balance); ?>

                    </td>

                  </tr>



                  <tr>

                    <td><strong>Remarks</strong></td>

                    <td>

                      <?php echo ($purchase_order_data['remarks']) ? $purchase_order_data['remarks'] : 'Nill'; ?>

                    </td>

                  </tr>



                  <tr>

                    <td><strong>DateTime Created</strong></td>

                    <td>

                      <?php

                        $date = date('d-m-Y', strtotime($purchase_order_data['datetime_created']));

                        $time = date('h:i a', strtotime($purchase_order_data['datetime_created']));

                        $datetime_created = $date . ' ' . $time;

                      ?>

                      <?php echo $datetime_created; ?>

                    </td>

                  </tr>



                  <tr>

                    <td><strong>DateTime Modified</strong></td>

                    <td>

                      <?php

                        $date = date('d-m-Y', strtotime($purchase_order_data['datetime_modified']));

                        $time = date('h:i a', strtotime($purchase_order_data['datetime_modified']));

                        $datetime_modified = $date . ' ' . $time;

                      ?>

                      <?php echo $datetime_modified; ?>  

                    </td>

                  </tr>



                  <tr>

                    <td><strong>Total Products</strong></td>

                    <td>

                      <?php

                        $count_total_item = $this->Model_products->countOrderItem($purchase_order_data['id']);

                      ?>

                      <?php echo $count_total_item; ?>

                    </td>

                  </tr>

                </tbody>

              </table>

              

            </div><!-- //box-body -->

            <hr>

          	<!-- /.box-body -->

            <div class="box-header">

              <h3 class="box-title">Payments</h3>

              <div class="form-horizontal">

                <div class="box-body">

                  <table class="table table-bordered table-striped" id="manageTable">

                    <thead>

                      <th>#</th>

                      <th>Payment Method</th>

                      <th>Paid Amount</th>

                      <th>Payment Note</th>

                      <th>Payment Date</th>

                    </thead>

                  </table>

                  <tbody>

                    

                  </tbody>

                  <div class="row">

                    <div class="col-md-4" style="text-align: right;">

                      <strong>Total Paid Amount: <?php echo floatval($purchase_order_data['total_paid']); ?></strong>

                    </div>

                  </div>

                </div>

              </div>

            </div>



            <hr>

            <div class="box-header">

              <h3 class="box-title">Items</h3>

              <div class="form-horizontal">

                <div class="box-body">

                  <table class="table table-bordered table-striped" id="manageTable_2">

                    <thead>

                      <th>#</th>

                      <th>Category</th>

                      <th>Item Name</th>

                      <th>Unit</th>

                      <th>Quantity</th>

                      <?php if(!empty($purchase_return_data)): ?>

                        <th>Returns Qty</th>

                      <?php endif; ?>

                      <th>Rate</th>

                      <th>Amount</th>

                    </thead>

                  </table>

                  <tbody>

                    

                  </tbody>

                </div>

              </div>

            </div>

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

        <h4 class="modal-title">Remove Purchased Order</h4>

      </div>



      <form role="form" action="<?php echo base_url() ?>index.php/Product/remove_purchase_order" method="post" id="removeForm">

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

  var base_url = "<?php echo base_url(); ?>";



  $(document).ready(function()

  {

    $("#mainPurchasingNav").addClass('active');

    var order_id = "<?php echo $purchase_order_data['id']; ?>"

    $( ".required-field" ).append('<label style="color:red" for="name">*</label>');

    $('#manageTable').DataTable({

      'ajax': base_url + 'index.php/Product/fetchPurchasePayments/'+order_id,

      'order': []

    });

    $('#manageTable_2').DataTable({

      'ajax': base_url + 'index.php/Product/fetchPurchaseItems/'+order_id,

      'order': []

    });

  });



</script>
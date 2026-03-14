
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Trasnfer
      <small>View Office Stock Transfer</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">View Office Stock Transfer</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <?php if(in_array('updateOfficeStockTransfer', $user_permission)): ?>
          <a href="<?php echo base_url() ?>index.php/Product/update_office_stock/<?php echo $this->uri->segment(3); ?>" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>
        <?php endif; ?>

        <?php if(in_array('printOfficeStockTransfer', $user_permission)): ?>
          <a title="Print Purchased Order" class="btn btn-info" target="__blank" href="<?php echo base_url() ?>index.php/Product/print_office_stock_transfered_order/<?php echo $this->uri->segment(3) ?>"><span class="glyphicon glyphicon-print"></span></a>
        <?php endif; ?>
        <?php if( ( in_array('updateOfficeStockTransfer', $user_permission) ) || ( in_array('deleteOfficeStockTransfer', $user_permission) ) || ( in_array('printOfficeStockTransfer', $user_permission) ) ): ?>
          <br /> <br />
        <?php endif; ?>

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">View</h3>
          </div>
          <!-- /.box-header -->
          <div class="form-horizontal">
              <div class="box-body">
                <table class="table table-bordered" id="manageTable">
                  <thead>
                    <tr>
                      <th width="3%">#</th>
                      <th style="color:#3c8dbc">Transfered Factory Stock Info</th>
                      <th style="color:#3c8dbc">Transfer Unit</th>
                      <th style="color:#3c8dbc">Transfer Quantity</th>
                    </tr>
                  </thead>

                   <tbody>
                     <?php if(isset($office_stock_transfered_items)): ?>
                      <?php $x = 1; ?>
                      <?php foreach ($office_stock_transfered_items as $key => $val): ?>
                      <tr>
                        <td><?php echo $x++; ?></td>
                        <td>
                          <?php foreach ($products as $k => $v): ?>
                            <?php
                              $unit_name = $this->Model_products->getUnitsData($v['unit_id'])['unit_name'];
                              $category_name = '';
                              if($v['category_name'] == null)
                              {
                                $category_name = '';
                              }
                              else
                              {
                                $category_name = ' &#8212 ' . $v['category_name'];
                              }
                            ?>
                              
                            <?php if($val['product_id'] == $v['product_id'] && $val['category_id'] == $v['category_id'] && $val['factory_stock_unit_id'] == $v['unit_id']): ?>
                                <?php echo $v['product_name']. ' ' . $category_name.' &nbsp;&nbsp;&nbsp;&nbsp; ( '.$unit_name.' )' ?>
                              <?php endif; ?>
                            <?php endforeach; ?>
                          </select>
                        </td>
                        <td>
                          <?php foreach ($units_data as $key => $value): ?>
                            <?php
                              $unit_id = 0;
                              $unit_name = '';
                              if($val['unit_id'] == $value['id'])
                              {
                                $unit_id = $value['id'];
                                $unit_name = $value['unit_name'];
                              }
                            ?>
                            <?php if($unit_id && $unit_name): ?>
                                <?php echo $unit_name;?>
                            <?php endif; ?>
                          <?php endforeach ?>
                        </td>
                        
                        <td>
                          <?php echo floatval($val['quantity']); ?>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                   </tbody>
                </table>
              </div>
              <!-- /.box-body -->
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

<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remove Office Stock Transfered Items</h4>
      </div>

      <form role="form" action="<?php echo base_url() ?>index.php/Product/delete_office_stock" method="post" id="removeForm">
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

  $(document).ready(function() {
    $("#mainStockNav").addClass('active');
    $('#manageTable').DataTable();
  });

</script>
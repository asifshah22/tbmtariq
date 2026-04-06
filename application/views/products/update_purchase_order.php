<?php date_default_timezone_set("Asia/Karachi"); ?>



<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

  <!-- Content Header (Page header) -->

  <section class="content-header">

    <h1>

      Update Purchse

      <small>Products</small>

    </h1>

    <ol class="breadcrumb">

      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

      <li class="active">Update Purchse</li>

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

            <?php elseif(validation_errors()): ?>

              <div class="alert alert-error alert-dismissible" role="alert">

                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <?php echo validation_errors(); ?>

              </div>

            <?php endif; ?>





            <div class="box">

              <div class="box-header">

                <h3 class="box-title">Update Purchase</h3>

              </div>

              <!-- /.box-header -->

              <form role="form" action="<?php echo base_url() ?>index.php/Product/update_purchase_order/<?php echo $this->uri->segment(3)?>" method="post" class="form-horizontal">

               <div class="box-body">

                <div class="form-group">

                 <label class="col-sm-12 control-label">Date: <?php echo date('Y-m-d', strtotime($purchase_order_data['datetime_modified'])) ?></label>

               </div>

               <div class="form-group">

                 <label class="col-sm-12 control-label">Time: <?php echo date('h:i a', strtotime($purchase_order_data['datetime_modified'])) ?></label>

               </div>

              <div class="form-group">

                <label class="col-sm-1 control-label">Name</label>

                <div class="col-sm-11">

                  <input style="text-transform: capitalize;" type="text" class="form-control" readonly name="vendor_info" id="vendor_info" value="<?php echo $vendor_data['first_name'].' '.$vendor_data['last_name'] ?>">

                  <input type="hidden" name="vendor_id" id="vendor_id" value="<?php echo $vendor_data['id'] ?>">

                </div>

              </div>

              <div class="form-group">
                <label class="col-sm-1 control-label">PO No</label>
                <div class="col-sm-11">
                  <select class="form-control" name="purchase_order_id" id="purchase_order_id" disabled>
                    <option value="">-- Select PO Number --</option>
                    <?php if (!empty($po_orders)): ?>
                      <?php foreach($po_orders as $po): ?>
                        <?php if ((string)$po['vendor_id'] !== (string)$vendor_data['id']) { continue; } ?>
                        <?php
                          $vendor_name = trim($po['first_name'].' '.$po['last_name']);
                          $supply_label = isset($po['supply_status']) ? $po['supply_status'] : '';
                          $payment_label = isset($po['payment_status']) ? $po['payment_status'] : '';
                          $label_parts = array();
                          if (!empty($po['po_number'])) {
                            $label_parts[] = $po['po_number'];
                          }
                          if (!empty($vendor_name)) {
                            $label_parts[] = $vendor_name;
                          }
                          if (!empty($supply_label)) {
                            $label_parts[] = 'Supply: ' . $supply_label;
                          }
                          if (!empty($payment_label)) {
                            $label_parts[] = 'Payment: ' . $payment_label;
                          }
                          $label = implode(' - ', $label_parts);
                          $selected = (!empty($selected_po_id) && (int)$selected_po_id === (int)$po['id']) ? 'selected' : '';
                        ?>
                        <option value="<?php echo $po['id']; ?>" <?php echo $selected; ?>><?php echo $label; ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                  <input type="hidden" name="purchase_order_id" value="<?php echo !empty($selected_po_id) ? $selected_po_id : ''; ?>">
                </div>
              </div>

              <div class="form-group" id="remaining_opening_balance_div">

                <label class="col-sm-1 control-label">Opening Balance</label>                   

                <div class="col-sm-11">

                  <input type="text" class="form-control" readonly name="opening_balance" id="opening_balance" value="<?php echo $vendor_data['balance'] ?>">

                </div>

              </div>

              <div class="form-group">

                <label class="col-sm-1 control-label">Payments</label>  

                <div class="col-sm-11">

                  <table class="table table-bordered" id="payment_info_table">

                    <thead>

                      <tr>

                        <th class="required-field">Payment Method</th>

                        <th class="required-field">Paid</th>

                        <th class="required-field">Payment Date</th>

                        <th>Payment Note</th>

                        <th style="width:10%"><button type="button" id="payment_add_row" class="btn btn-default"><i class="fa fa-plus"></i></button></th>

                      </tr>

                    </thead>



                    <tbody>

                      <?php

                      $payment_data = $this->Model_payment_method->getPaymentMethodData();

                      $temp = '!@#$';

                      $data = $purchase_order_data;

                      $payment_method_array = explode($temp, $data['payment_method']);

                      $payment_date_array = explode($temp, $data['payment_date']);

                      $paid_array = explode($temp, $data['paid']);

                      $payment_note_array = explode($temp, $data['payment_note']);

                      $i = 0;

                      $x = 1;

                      ?>

                      <?php foreach ($payment_method_array as $key => $val): ?>

                        <?php

                          $payment_method = $payment_method_array[$i];

                          $paid_amount = $paid_array[$i];

                          $payment_note = $payment_note_array[$i];

                          $payment_date = $payment_date_array[$i];

                          $i++;

                        ?>

                        <tr id="row_<?php echo $x; ?>">

                          <td>

                            <select class="form-control select_payment" id="select_payment_<?php echo $x; ?>" name="select_payment[]" required>

                              <option value="">Select Payment Method</option>

                              <?php foreach($payment_data as $key => $value): ?>

                                <option <?php if($payment_method == $value['name']){ echo "selected=selected"; } ?> value="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></option>

                              <?php endforeach; ?>

                            </select>

                                

                                </td>

                                <td>

                                  <?php if($payment_method == ''): ?>

                                    <input type="number" class="form-control noscroll" name="amount_paid[]" id="amount_paid_<?php echo $x; ?>" value="0" min="0" step="any">

                                    <?php else: ?>

                                      <input type="number" class="form-control noscroll" name="amount_paid[]" id="amount_paid_<?php echo $x; ?>" min="1" value="<?php echo $paid_amount ?>" step="any" required>

                                    <?php endif; ?>

                                  </td>

                                  <td>

                                    <?php if($payment_method == ''): ?>

                                      <input type="date" class="form-control" name="payment_date[]" id="payment_date_<?php echo $x; ?>" value="<?php echo $payment_date ?>">

                                      <?php else: ?>

                                        <input type="date" class="form-control" name="payment_date[]" id="payment_date_<?php echo $x; ?>" value="<?php echo $payment_date ?>" required>

                                      <?php endif; ?>

                                    </td>

                                    <td>

                                      <?php if($payment_note != 'Nill'): ?>

                                        <textarea class="form-control" style="resize:none" rows="1" name="payment_note[]" id="payment_note_<?php echo $x; ?>"><?php echo $payment_note; ?></textarea>

                                        <?php else: ?>

                                          <textarea class="form-control" style="resize:none" rows="1" name="payment_note[]" id="payment_note_<?php echo $x; ?>"></textarea>

                                        <?php endif; ?>

                                      </td>

                                      <td>

                                        <button type="button" class="btn btn-default removePaymentRow" onclick="removePaymentRow(<?php echo $x; ?>)"><i class="fa fa-close"></i></button>

                                      </td>

                                    </tr>

                                    <?php $x++; ?>

                                  <?php endforeach; ?>

                                </tbody>

                              </table>

                            </div>

                          </div>

                          <br /> <br/>

                          <div class="form-group">

                            <label class="col-sm-1 control-label">Items</label> 

                            <div class="col-sm-11">      

                              <table class="table table-bordered" id="product_info_table">

                               <thead>

                                <tr>

                                 <th class="required-field">Date</th>

                                 <th style="width:25%" class="required-field">Product</th>

                                 <th style="width:20%" class="required-field">Unit</th>

                                 <th class="required-field">Qty</th>

                                 <?php if(in_array('viewProductRate', $user_permission)): ?>

                                    <th>Rate</th>

                                 <?php endif;?>

                                 <?php if(isset($purchase_return_data)): ?>

                                  <th>Return <a href="#" data-toggle="tooltip" title="These items have not relationship with purchasing units. These items are having by default per piece unit."><i class="fa fa-question-circle"></i></a> </th>

                                <?php endif; ?>

                                <th>Amount</th>

                                <th><button type="button" id="add_row" class="btn btn-default"><i class="fa fa-plus"></i></button></th>

                              </tr>

                            </thead>



                            <tbody>

                              <?php $return_items_total_amount = 0; ?>

                              <?php if(isset($purchase_items_data)): ?>

                               <?php $x = 1; ?>

                               <?php foreach ($purchase_items_data as $key => $val): ?>

                                <tr id="row_<?php echo $x; ?>">

                                 <td>
                                  <input type="date" name="item_date[]" id="item_date_<?php echo $x; ?>" class="form-control" value="<?php echo isset($val['item_date']) && $val['item_date'] ? $val['item_date'] : date('Y-m-d'); ?>" required>
                                 </td>

                                 <td>

                                  <select class="form-control select_product" data-row-id="<?php echo $x; ?>" id="product_<?php echo $x; ?>" name="product[]" style="width:100%;" onchange="setOnProductChange(<?php echo $x; ?>)" required>

                                   <option value="">Select Product</option>

                                   <?php foreach ($vendor_products as $k => $v): ?>

                                    <?php

                                    $category_name = '';

                                    $category_id = '';

                                    if($v['category_name'] == null)

                                    {

                                      $category_name = '';

                                      $category_id = ' ';

                                    }

                                    else

                                    {

                                      $category_name = ' &#8212 ' . $v['category_name'];

                                      $category_id = $v['category_id'];

                                    }

                                    ?>

                                    <!-- select_product_price = product_id -->

                                    <?php if($val['product_id'] == $v['select_product_price']): ?>

                                     <option value="<?php echo $v['select_product_price'].'-'.$category_id ?>" <?php echo "selected='selected'"; ?>><?php echo $v['product_name']. ' ' . $category_name ?>

                                   </option>

                                   <?php else: ?>

                                     <option value="<?php echo $v['select_product_price'].'-'.$category_id ?>"><?php echo $v['product_name']. ' ' . $category_name ?>

                                   </option>

                                 <?php endif; ?>



                               <?php endforeach; ?>

                             </select>

                           </td>



                           <td>

                            <select required="true" class="form-control select_unit" data-row-id="<?php echo $x; ?>" id="select_unit_<?php echo $x; ?>" name="select_unit[]" onchange="getProductPriceData(<?php echo $x; ?>)">
                             <option value="">Select Unit</option>
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
                                <option <?php echo "selected='selected'"; ?> value="<?php echo $unit_id ?>">
                                 <?php echo $unit_name;?>
                               </option>
                               <?php else: ?>
                                <option value="<?php echo $value['id']?>">
                                 <?php echo $value['unit_name']; ?>
                               </option>
                             <?php endif; ?>  

                           <?php endforeach; ?>
                         </select>

                       </td>

                      <td>  

                        <input type="number" min="0.1" step="any" name="qty[]" id="qty_<?php echo $x; ?>" class="form-control noscroll" onkeyup="getTotal(<?php echo $x; ?>)" value="<?php echo $val['qty']; ?>">

                      </td>

                      <?php if(in_array('viewProductRate', $user_permission)): ?>

                      <td>

                        <input type="text" name="rate[]" id="rate_<?php echo $x; ?>" class="form-control" disabled value="<?php echo $val['product_price'] ?>">

                        <input type="hidden" name="rate_value[]" id="rate_value_<?php echo $x; ?>" class="form-control" value="<?php echo $val['product_price'] ?>">

                        

                      </td>

                      <?php else:?>

                        <input type="hidden" name="rate[]" id="rate_<?php echo $x; ?>" class="form-control" disabled value="<?php echo $val['product_price'] ?>">

                        <input type="hidden" name="rate_value[]" id="rate_value_<?php echo $x; ?>" class="form-control" value="<?php echo $val['product_price'] ?>">

                      <?php endif;?>

                      <?php $return_items_amount = 0; ?>

                      <?php if(isset($purchase_return_data)): ?>

                        <?php

                        $return_items = 0;

                        foreach ($purchase_return_data as $k => $v) {

                          if($val['product_id'] == $v['product_id'] && $val['category_id'] == $v['category_id'] && $val['unit_id'] == $v['unit_id'])

                          {

                            $return_items = $v['qty'];

                            $return_items_amount = $v['amount'];

                            $return_items_total_amount += $v['amount'];

                          }

                        }

                        ?>

                        <td>

                          <input type="text" class="form-control" value="<?php echo $return_items; ?>" disabled id="return_items_<?php echo $x; ?>" name="return_items[]">

                          <input type="hidden" value="<?php echo $return_items_amount; ?>" id="return_items_amount_<?php echo $x; ?>" name="return_items_amount[]">

                        </td>

                      <?php endif; ?>

                      <td>

                        <?php 

                        $amount = ($val['qty'] * $val['product_price']) - $return_items_amount;

                        ?>

                        <input type="text" name="amount[]" id="amount_<?php echo $x; ?>" class="form-control" disabled value="<?php echo $amount ?>">

                        <input type="hidden" name="amount_value[]" id="amount_value_<?php echo $x; ?>" class="form-control" value="<?php echo $amount ?>">

                      </td>

                      <td>

                        <button type="button" class="btn btn-default removeRow" onclick="removeRow('<?php echo $x; ?>')"><i class="fa fa-close"></i></button>

                      </td>

                    </tr>

                    <?php $x++; ?>

                  <?php endforeach; ?>

                <?php endif; ?>

              </tbody>

            </table>

          </div>

        </div>



        <br /> <br/>

        <div class="col-md-6 col-xs-12 pull pull-left">

         <div class="form-group" style="margin-top: 50px">

          <label class="col-sm-6 control-label" for="loan_amount">Remaining Loan Amount</label>

          <div class="col-sm-5">

           <input type="text" class="form-control" disabled name="loan_amount" id="loan_amount" value="<?php if(isset($loan_data)){ echo $loan_data['remaining_amount']; } ?>">

         </div>

       </div>

       <div class="form-group" id="remainingAmount" style="display: none;">

        <label class="col-sm-6 control-label" for="new_loan_amount">New Remaining Amount</label>

        <div class="col-sm-5">

         <input type="text" class="form-control" disabled name="new_loan_amount" id="new_loan_amount">

       </div>

     </div>

   </div>



   <div class="col-md-6 col-xs-12 pull pull-right">



     <div class="form-group">

      <label for="gross_amount" class="col-sm-5 control-label">Gross Amount</label>

      <div class="col-sm-7">

       <input type="text" class="form-control" id="gross_amount" name="gross_amount" disabled value="<?php echo ($purchase_order_data['gross_amount'] - $return_items_total_amount); ?>">

       <input type="hidden" class="form-control" id="gross_amount_value" name="gross_amount_value" value="<?php echo $purchase_order_data['gross_amount'] - $return_items_total_amount ?>">

     </div>

   </div>



   <div class="form-group">

    <label for="discount" class="col-sm-5 control-label">Discount</label>

    <div class="col-sm-7">

     <input type="number" min="0" step="any" class="form-control noscroll" id="discount" name="discount" placeholder="Discount" onkeyup="subAmount()" autocomplete="off" value="<?php echo $purchase_order_data['discount']; ?>">

   </div>

 </div>

<div class="form-group">

                      <label for="discount" class="col-sm-5 control-label">Sales tax</label>

                      <div class="col-sm-7">

                        <select class="form-control" id="sales_tax" name="sales_tax" onchange="subAmount()">

                        <option value="18" <?php if($data['sales_tax'] == 18){?> selected <?php } ?>>

                          18%

                        </option>

                        <option value="0" <?php if($data['sales_tax'] == 0){?> selected <?php } ?>>

                          0%

                        </option>

                      </select>

                      </div>

                    </div>

                    <div class="form-group">

                      <label for="discount" class="col-sm-5 control-label">Sales tax</label>

                      <div class="col-sm-7">

                        <input type="text" value="<?php echo $data['sales_tax']; ?>" class="form-control" id="sales_tax_val" name="sales_tax_val" disabled autocomplete="off">

                       <input type="hidden" value="<?php echo $data['sales_tax_value']; ?>" class="form-control" id="sales_tax_value" name="sales_tax_value" autocomplete="off">

                      </div>

                    </div>

                                          <div class="form-group">

                      <label for="discount" class="col-sm-5 control-label">Sales tax total</label>

                      <div class="col-sm-7">

                        <input type="text" value="" class="form-control" id="sales_tax_val_total" name="sales_tax_val_total" disabled autocomplete="off">
                           <input type="hidden" value="<?php echo $data['sales_tax_value_total']; ?>" class="form-control" id="sales_tax_value_total" name="sales_tax_value_total" autocomplete="off">

                      </div>

                    </div>

                     <div class="form-group">

                      <label for="discount" class="col-sm-5 control-label">W.H.T(%)</label>

                      <div class="col-sm-7">
                        <!-- <input type="number" class="form-control" id="w_h_t" name="w_h_t" value="<?=$data['w_h_t']?>"  autocomplete="off" onchange="subAmount()"> -->
                        <select class="form-control" id="w_h_t" name="w_h_t" onchange="subAmount()">
                        <option value="<?=$data['w_h_t']?>">

                          <?=$data['w_h_t']?>%

                        </option>
                        <option value="5" <?php if($data['w_h_t'] == 5){?> selected <?php } ?>>

                          5%

                        </option>

                        <option value="5.5" <?php if($data['w_h_t'] == '5.5'){?> selected <?php } ?>>

                          5.5%

                        </option>

                        

                      </select>
                      <input type="number" id="customValue" placeholder="Enter custom percentage" step="0.1" min="0">
                      <button type="button" onclick="addCustomOption()">Add Custom Value</button>

                      </div>

                    </div>

                    <div class="form-group">

                      <label for="discount" class="col-sm-5 control-label">W.H.T</label>

                      <div class="col-sm-7">

                        <input type="text" class="form-control" id="w_h_t_val" name="w_h_t_val" disabled autocomplete="off">

                        <input type="hidden" class="form-control" id="w_h_t_value" name="w_h_t_tax_value" autocomplete="off">

                      </div>

                    </div>

                     <div class="form-group">

                      <label for="discount" class="col-sm-5 control-label">W.H.T total</label>

                      <div class="col-sm-7">

                        <input type="text" class="form-control"  id="w_h_t_val_total" name="w_h_t_val_total" disabled autocomplete="off">
                        <input type="hidden" class="form-control" value="<?php echo $data['w_h_t_value_total']; ?>" id="w_h_t_value_total" name="w_h_t_tax_value_total" autocomplete="off">


                       </div>

                    </div>



 <div class="form-group">

  <label for="loan_deduction" class="col-sm-5 control-label">Loan Deduction</label>

  <div class="col-sm-7">

   <?php

   $loan_deduction = 0;

   if(!empty($loan_deductions))

   {

     $loan_deduction = $loan_deductions['deduction_amount'];

   }

   ?>

   <input type="hidden" name="previous_loan_deduction" id="previous_loan_deduction" value="<?php echo $loan_deduction; ?>">

   <input type="number" min="0" step="any" class="form-control noscroll" name="loan_deduction" id="loan_deduction" placeholder="Deduction Amount" onkeyup="subAmount_2()" value="<?php echo $loan_deduction; ?>">

 </div>

</div>

<div class="form-group">

  <div class="alert alert-success alert-dismissible" role="alert">

   <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

   <strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>Be Careful &#128578. Enter plus amount to add and negive to subtract from the bill.

 </div>

</div>

<div class="form-group">

  <label for="affair_loading" class="col-sm-5 control-label">Freight</label>

  <div class="col-sm-7">

   <input type="number" step="any" class="form-control noscroll" name="affair_loading" id="affair_loading" placeholder="Enter Plus or Minus Accordingly" value="<?php echo $purchase_order_data['loading_or_affair']; ?>" onkeyup="subAmount_3()">

 </div>

</div>



<div class="form-group">

  <label for="fine_deduction" class="col-sm-5 control-label">Fine Deduction</label>

  <div class="col-sm-7">

    <input type="number" min="0" step="any" class="form-control noscroll" name="fine_deduction" id="fine_deduction" placeholder="Fine Deduction" value="<?php echo $purchase_order_data['fine_deduction']; ?>" onkeyup="subAmount_4()">

  </div>

</div>

<div class="form-group">

  <label for="other_deduction" class="col-sm-5 control-label">Other Deduction</label>

  <div class="col-sm-7">

    <input type="number" min="0" step="any" class="form-control noscroll" name="other_deduction" id="other_deduction" placeholder="Other Deduction" value="<?php echo $purchase_order_data['other_deduction']; ?>" onkeyup="subAmount_5()">

  </div>

</div>



<div class="form-group">

  <label for="remarks" class="col-sm-5 control-label">Remarks</label>

  <div class="col-sm-7">

   <textarea class="form-control" name="remarks" id="remarks" placeholder="Remarks" autocomplete="off"><?php echo $purchase_order_data['remarks'];?></textarea>

 </div>

</div>

<div class="form-group">

  <label for="net_amount" class="col-sm-5 control-label">Net Amount</label>

  <div class="col-sm-7">

    <input type="number" min="1" step="any" class="form-control noscroll" id="net_amount" name="net_amount" disabled value="<?php echo ($purchase_order_data['net_amount'] - $return_items_total_amount); ?>">

    <input type="hidden" class="form-control" id="net_amount_value" name="net_amount_value" value="<?php echo ($purchase_order_data['net_amount'] - $return_items_total_amount); ?>">

  </div>

</div>



</div>

</div>

<!-- /.box-body -->

<div class="box-footer">

  <a target="__blank" href="<?php echo base_url() . 'index.php/Product/print_invoice/'.$purchase_order_data['id'] ?>" class="btn btn-success" >Print</a>

  <a href="<?php echo base_url() . 'index.php/Product/view_order_items/'.$purchase_order_data['id'] ?>" class="btn btn-default" >View</a>

  <button id="btnCreateOrder" type="submit" class="btn btn-primary">Update Order</button>

  <a href="<?php echo base_url() ?>index.php/Product/manage_purchase_orders" class="btn btn-warning">Back</a>

</div>

</form>

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





<script type="text/javascript">
  function addCustomOption() {
    // Get the value entered by the user
    var customValue = document.getElementById('customValue').value;

    // Validate the input (check if it's a valid number and not empty)
    if (customValue !== "" && !isNaN(customValue) && customValue >= 0) {
      var selectElement = document.getElementById('w_h_t');
      
      // Create a new option element
      var newOption = document.createElement('option');
      newOption.value = customValue;
      newOption.textContent = customValue + '%';

      // Add the new option to the select element
      selectElement.appendChild(newOption);

      // Optionally, select the newly added option
      selectElement.value = customValue;

      // Clear the input field
      document.getElementById('customValue').value = '';
    } else {
      alert("Please enter a valid percentage value.");
    }
  }


  var base_url = "<?php echo base_url(); ?>";
  var poFillQtyMap = {};
  var itemRowDefaultDate = "<?php echo date('Y-m-d'); ?>";

  function buildProductRowHtmlUpdate(row_id, vendor_products, showRateCol) {
    var html = '<tr id="row_'+row_id+'">'+
      '<td><input type="date" required name="item_date[]" id="item_date_'+row_id+'" class="form-control" value="'+itemRowDefaultDate+'"></td>'+
      '<td>'+
        '<select class="form-control select_product" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" style="width:100%;" onchange="setOnProductChange('+row_id+')" required>'+
        '<option value="">Select Product</option>';

    for (var i = 0; i < vendor_products.length; i++) {
      var category_name = '';
      var value = '';
      if (vendor_products[i].category_name == null) {
        category_name = '';
        value = vendor_products[i].select_product_price + '- ';
      } else {
        category_name = ' &#8212 ' + vendor_products[i].category_name;
        value = vendor_products[i].select_product_price + '-' + vendor_products[i].category_id;
      }
      html += '<option value="'+value+'">'+vendor_products[i].product_name + ' ' + category_name+'</option>';
    }

    html += '</select>'+
      '</td>'+
      '<td>'+
        '<select required="true" class="form-control select_unit" data-row-id="'+row_id+'" id="select_unit_'+row_id+'" name="select_unit[]" onchange="getProductPriceData('+row_id+')">'+
        '<option value="">Select Unit</option>';
        <?php foreach ($units_data as $key => $value): ?>
          var unit_id = <?php echo $value['id']; ?>;
          var unit_name = "<?php echo $value['unit_name']; ?>";
          html += '<option value="'+unit_id+'">'+unit_name+'</option>';
        <?php endforeach; ?>
    html += '</select>'+
      '</td>'+
      '<td><input type="number" min="0.1" step="any" name="qty[]" id="qty_'+row_id+'" class="form-control noscroll" onkeyup="getTotal('+row_id+')"></td>';

    if (showRateCol) {
      html += '<td><input type="text" name="rate[]" id="rate_'+row_id+'" class="form-control" disabled><input type="hidden" name="rate_value[]" id="rate_value_'+row_id+'" class="form-control"></td>';
    } else {
      html += '<input type="hidden" name="rate[]" id="rate_'+row_id+'" class="form-control" disabled><input type="hidden" name="rate_value[]" id="rate_value_'+row_id+'" class="form-control">';
    }

    html += '<td><input type="text" name="return_items[]" id="return_items_'+row_id+'" class="form-control" disabled value="0"><input type="hidden" name="return_items_amount[]" value="0" id="return_items_amount_'+row_id+'" class="form-control"></td>'+
      '<td><input type="text" name="amount[]" id="amount_'+row_id+'" class="form-control" disabled><input type="hidden" name="amount_value[]" id="amount_value_'+row_id+'" class="form-control"></td>'+
      '<td><button type="button" class="btn btn-default" onclick="removeRow(\''+row_id+'\')"><i class="fa fa-close"></i></button></td>'+
    '</tr>';

    return html;
  }

  function findProductValueUpdate($select, product_id, part_name) {
    if (!product_id) {
      return '';
    }
    var found = '';
    $select.find('option').each(function() {
      var optVal = $(this).val();
      if (!optVal) {
        return;
      }
      var optProductId = String(optVal).split('-')[0];
      if (String(optProductId).trim() === String(product_id)) {
        found = optVal;
        return false;
      }
    });

    if (!found && part_name) {
      var newVal = product_id + '- ';
      $select.append($('<option>', { value: newVal, text: part_name }));
      found = newVal;
    }

    return found;
  }

  function findUnitValueUpdate($select, unitName) {
    if (!unitName) {
      return '';
    }
    var desired = $.trim(unitName).toLowerCase();
    var found = '';
    $select.find('option').each(function() {
      var text = $.trim($(this).text()).toLowerCase();
      if (text === desired) {
        found = $(this).val();
        return false;
      }
    });
    return found;
  }



  $(document).ready(function()

  {

$(".select_product").select2({
    width: '100%' // Full width container ke hisaab se
});;
    $(".select_unit").select2({
    width: '100%' // Full width container ke hisaab se
});;

    $('.select_payment').select2(); 



    $( ".required-field" ).append('<label style="color:red" for="name">*</label>');

  	$("#mainPurchasingNav").addClass('active');



    document.addEventListener("wheel", function(event){

      if(document.activeElement.type === "number" &&

       document.activeElement.classList.contains("noscroll"))

      {

        document.activeElement.blur();

      }

    });



    $("#row_1 td .removeRow").attr('disabled', 'disabled');

    $("#row_1 td .removePaymentRow").attr('disabled', 'disabled');

    var first_payment_row = $("#row_1 td .select_payment").val();

    if(first_payment_row == ''){

      $("#row_1 td .select_payment").removeAttr('required');

    }

  	// Add new row in the table 

    $("#add_row").unbind('click').bind('click', function() {



      $("#add_row").attr("disabled", "disabled");

      var table = $("#product_info_table");

      var count_table_tbody_tr = $("#product_info_table tbody tr").length;

      var row_id = count_table_tbody_tr + 1;

      var vendor_id = "<?php echo $vendor_data['id'] ?>";



      $.ajax({



       url: base_url + 'index.php/Product/getTablesData',

       type: 'post',

       data: {vendor_id : vendor_id},

       dataType: 'json',

       success:function(response)

       {



        if(response.success === true) {

          var showRateCol = <?php if(in_array('viewProductRate', $user_permission)){ echo 1; }else{ echo 0; } ?>;

         var output = response.data.vendor_data;	

         var html = '<tr id="row_'+row_id+'">'+



         '<td><input type="date" required name="item_date[]" id="item_date_'+row_id+'" class="form-control" value="'+itemRowDefaultDate+'"></td>'+
         '<td>'+ 

         '<select class="form-control" data-row-id="'+row_id+'" id="product_'+row_id+'" name="product[]" onchange="setOnProductChange('+row_id+')" required>'+

         '<option value="">Select Product</option>';

         for(var i = 0; i < response.data.vendor_products.length; i++)

         {

                        	//select_product_price id

                        	console.log(response.data.vendor_products[i]);

                          var category_name = '';

                          var value = '';

                          if(response.data.vendor_products[i].category_name == null)

                          {

                            category_name = '';

                            value = response.data.vendor_products[i].select_product_price + '- ';

                          }

                          else

                          {

                            category_name = response.data.vendor_products[i].category_name;

                            value = response.data.vendor_products[i].select_product_price +

                            '-' +

                            response.data.vendor_products[i].category_id;

                          }

                          html += '<option value="'+value+'">'+response.data.vendor_products[i].product_name + ' ' + category_name+'</option>'



                        }



                        html += '</select>'+

                        '</td>'+



                        '<td>'+

                        '<select required="true" disabled class="form-control select_group select_unit" data-row-id="'+row_id+'" id="select_unit_'+row_id+'" name="select_unit[]" onchange="getProductPriceData('+row_id+')">'+

                        '<option value="">Select Unit</option>';

                        <?php foreach ($units_data as $key => $value): ?>



                         var unit_id = <?php echo $value['id']; ?>;

                         var unit_name = "<?php echo $value['unit_name']; ?>";



                         html += '<option value="'+unit_id+'">'+unit_name+'</option>'

                       <?php endforeach; ?>



                       html += '</select>'+

                       '</td>'+



                       '<td><input disabled type="number" min="0.1" step="any" name="qty[]" id="qty_'+row_id+'" class="form-control noscroll" onkeyup="getTotal('+row_id+')"></td>';

                       if(showRateCol){

                          html += '<td><input type="text" name="rate[]" id="rate_'+row_id+'" class="form-control" disabled><input type="hidden" name="rate_value[]" id="rate_value_'+row_id+'" class="form-control"></td>';

                       }else{

                          html += '<input type="hidden" name="rate[]" id="rate_'+row_id+'" class="form-control" disabled><input type="hidden" name="rate_value[]" id="rate_value_'+row_id+'" class="form-control">';

                       }



                       html +='<td><input type="text" name="return_items[]" id="return_items_'+row_id+'" class="form-control" disabled value="0"><input type="hidden" name="return_items_amount[]" value="0" id="return_items_amount_'+row_id+'" class="form-control"></td>'+

                       '<td><input type="text" name="amount[]" id="amount_'+row_id+'" class="form-control" disabled><input type="hidden" name="amount_value[]" id="amount_value_'+row_id+'" class="form-control"></td>'+

                       '<td><button type="button" class="btn btn-default" onclick="removeRow(\''+row_id+'\')"><i class="fa fa-close"></i></button></td>'+

                       '</tr>';

                       $("#add_row").removeAttr("disabled", "disabled");

                       if(count_table_tbody_tr >= 1) {

                        $("#product_info_table tbody tr:last").after(html);  

                      }

                      else

                      {

                        $("#product_info_table tbody").html(html);

                      }



                      $('#product_'+row_id+'').select2({
    width: '100%' // Full width container ke hisaab se
});;

                      $('#select_unit_'+row_id+'').select2({
    width: '100%' // Full width container ke hisaab se
});;

                    }

                  }

                });

return false;

});

    $("#purchase_order_id").on('change', function() {
      var po_id = $(this).val();
      if (!po_id) {
        return;
      }

      var vendor_id = "<?php echo $vendor_data['id'] ?>";
      if (!vendor_id) {
        return;
      }

      $.ajax({
        url: base_url + 'index.php/Product/getPurchaseOrderItems',
        type: 'post',
        data: {po_id: po_id},
        dataType: 'json',
        success: function(poResponse) {
          if (!poResponse || poResponse.success !== true) {
            return;
          }

          var poItems = poResponse.items || [];
          if (poItems.length === 0) {
            $("#product_info_table tbody").html('');
            subAmount();
            return;
          }

          $.ajax({
            url: base_url + 'index.php/Product/getTablesData',
            type: 'post',
            data: {vendor_id: vendor_id},
            dataType: 'json',
            success: function(response) {
              if (response.success !== true) {
                return;
              }

              var showRateCol = <?php if(in_array('viewProductRate', $user_permission)){ echo 1; }else{ echo 0; } ?>;
              var vendor_products = response.data.vendor_products || [];
              var html = '';
              for (var i = 0; i < poItems.length; i++) {
                var row_id = i + 1;
                html += buildProductRowHtmlUpdate(row_id, vendor_products, showRateCol);
              }

              $("#product_info_table tbody").html(html);

              for (var j = 0; j < poItems.length; j++) {
                var rId = j + 1;
                var item = poItems[j];

                $('#product_' + rId).select2({ width: '100%' });
                $('#select_unit_' + rId).select2({ width: '100%' });

                var $product = $("#product_" + rId);
                var productVal = findProductValueUpdate($product, item.product_id, item.part_name);
                if (productVal) {
                  $product.val(productVal).trigger('change');
                }

                setOnProductChange(rId);

                var $unit = $("#select_unit_" + rId);
                var unitVal = findUnitValueUpdate($unit, item.unit);
                if (unitVal) {
                  $unit.val(unitVal).trigger('change');
                }
                $unit.prop("disabled", false);

                if (item.item_date) {
                  $("#item_date_" + rId).val(item.item_date);
                }

                if (item.rate && Number(item.rate) > 0) {
                  $("#rate_" + rId).val(item.rate);
                  $("#rate_value_" + rId).val(item.rate);
                  $("#qty_" + rId).val(item.qty);
                  $("#qty_" + rId).prop("disabled", false);

                  var total = item.amount && Number(item.amount) > 0 ? Number(item.amount) : Number(item.rate) * Number(item.qty);
                  total = total.toFixed(2);
                  $("#amount_" + rId).val(total);
                  $("#amount_value_" + rId).val(total);
                } else {
                  poFillQtyMap[rId] = Number(item.qty);
                  getProductPriceData(rId);
                }
              }

              subAmount();
            }
          });
        }
      });
    });



    // Add new row in the payment table 

    $("#payment_add_row").unbind('click').bind('click', function() {



      $("#payment_add_row").attr("disabled", "disabled");

      var table = $("#payment_info_table");

      var count_table_tbody_tr = $("#payment_info_table tbody tr").length;

      var row_id = count_table_tbody_tr + 1;

      var vendor_id = "<?php echo $vendor_data['id'] ?>";



      $.ajax({



        url: base_url + 'index.php/Product/getTablesData',

        type: 'post',

        data: {vendor_id : vendor_id},

        dataType: 'json',

        success:function(response)

        {

          if(response.success === true) {

            var output = response.data.vendor_data; 

            var html = '<tr id="row_'+row_id+'">'+



            '<td>'+ 

            '<select class="form-control select_payment" data-row-id="'+row_id+'" id="select_payment_'+row_id+'" name="select_payment[]" required>'+

            '<option value="">Select Payment Method</option>';

            

            <?php foreach ($payment_data as $key => $value): ?>

              var payment_method = "<?php echo $value['name']; ?>";

              html += '<option value="'+payment_method+'">'+payment_method+'</option>'

            <?php endforeach; ?>

            html += '</select>'+

            '</td>'+



            '<td>'+

            '<input type="number" class="form-control noscroll" name="amount_paid[]" id="amount_paid_'+row_id+'" value="0" min="1" step="any" required>'+

            '</td>'+



            '<td><input type="date" class="form-control" name="payment_date[]" id="payment_date_'+row_id+'" required></td>'+



            '<td><textarea class="form-control" style="resize:none" rows="1" name="payment_note[]" id="payment_note_'+row_id+'"></textarea></td>'+

            '<td><button type="button" class="btn btn-default" onclick="removePaymentRow(\''+row_id+'\')"><i class="fa fa-close"></i></button></td>'+

            '</tr>';

            $("#payment_add_row").removeAttr("disabled", "disabled");

            if(count_table_tbody_tr >= 1) {

              $("#payment_info_table tbody tr:last").after(html);  

            }

            else

            {

              $("#payment_info_table tbody").html(html);

            }

            $('#select_payment_'+row_id+'').select2();

            $('#payment_date_'+row_id+'').datepicker({

              autoclose: true,

              format: 'yyyy-mm-dd' 

            });

            $('#payment_date_'+row_id+'').datepicker().datepicker("setDate", new Date());

                // add required attr to first row

                if(row_id == 2){

                  $('#select_payment_1').attr('required', 'required');

                  $("#amount_paid_1").prop('min',1);

                  $("#payment_date_1").attr("required", "required");

                }

              }

            }

          });

      return false;

    });

  });



function setOnProductChange(row_id)

{

  var product_id = $("#product_"+row_id).val();

    // alert(product_id);

    var vendor_id = "<?php echo $vendor_data['id'] ?>";

    if(product_id == "" || vendor_id == "") {

      $("#rate_"+row_id).val("");

      $("#rate_value_"+row_id).val("");

      $("#select_unit_"+row_id).val("");

      $("#select_unit_"+row_id).select2().trigger('change');

      $("#select_unit_"+row_id).prop("disabled", true);



      $("#qty_"+row_id).val("");

      $("#qty_value_"+row_id).val("");

      $("#qty_"+row_id).prop("disabled", true);

      $("#qty_value_"+row_id).prop("disabled", true);



      $("#amount_"+row_id).val("");

      $("#amount_value_"+row_id).val("");

    }

    else

    {

      // once the product having previous price id is changed

      // other validations

      $("#rate_"+row_id).val("");

      $("#rate_value_"+row_id).val("");



      $("#select_unit_"+row_id).val("");

      $("#select_unit_"+row_id).select2().trigger('change');

      $("#select_unit_"+row_id).prop("disabled", false);



      $("#qty_"+row_id).val("");

      $("#qty_value_"+row_id).val("");

      $("#qty_"+row_id).prop("disabled", true);

      $("#qty_value_"+row_id).prop("disabled", true);



      $("#amount_"+row_id).val("");

      $("#amount_value_"+row_id).val("");   

    }

  }



  function	getProductPriceData(row_id)

  {

  	var selected_product_data = $("#product_"+row_id).val();

    var product_id = selected_product_data.split("-")[0];

    var category_id = selected_product_data.split("-")[1];



  	var vendor_id = "<?php echo $vendor_data['id'] ?>";

    var unit_id = $("#select_unit_"+row_id).val();

    if(product_id == "") {

    	$("#rate_"+row_id).val("");

    	$("#rate_value_"+row_id).val("");



      $("#select_unit_"+row_id).val("");

      $("#select_unit_"+row_id).select2().trigger('change');

    	$("#select_unit_"+row_id).prop("disabled", true);



    	$("#qty_"+row_id).val("");

    	$("#qty_value_"+row_id).val("");



    	$("#amount_"+row_id).val("");

    	$("#amount_value_"+row_id).val("");



    }

    else

    {

      $.ajax({

        url: base_url + 'index.php/Product/getProductPriceDataById',

        type: 'post',

        data: 

        {

        	category_id : category_id,

          product_id : product_id,

          vendor_id : vendor_id,

          unit_id : unit_id

        },

        dataType: 'json',

        success:function(response) {

        	if(response.success === true) 

        	{

        		console.log(response.data);

            // if price is set

            return_product_price = 0;

            if(response.data)

            {

              // once the product having previous price id has dif unit. now prev rate is not required anymore

              // setting the rate value into the rate input field

              $("#rate_"+row_id).val(response.data.price);

              $("#rate_value_"+row_id).val(response.data.price);



              $("#qty_"+row_id).val(1);

              $("#qty_value_"+row_id).val(1);

              $("#qty_"+row_id).prop("disabled", false); 



              var total = Number(response.data.price);

              total = total.toFixed(2);

              $("#amount_"+row_id).val(total);

              $("#amount_value_"+row_id).val(total);
              if (poFillQtyMap.hasOwnProperty(row_id)) {
                var desiredQty = poFillQtyMap[row_id];
                $("#qty_"+row_id).val(desiredQty);
                $("#qty_"+row_id).prop("disabled", false);
                delete poFillQtyMap[row_id];
                getTotal(row_id);
              } else {
                subAmount();
              }

            }

            // if price is not set

            else

            {

              // deduct amount from gross and net amount

              var amount = Number($("#amount_value_"+row_id).val());

              var gross_amount = Number($("#gross_amount_value").val());

              var temp = Number(gross_amount) - Number(amount);

              $("#gross_amount").val(temp.toFixed(2));

              $("#gross_amount_value").val(temp.toFixed(2));



              var net_amount = Number($("#net_amount_value").val());

              temp = Number(net_amount) - Number(amount);

              $("#net_amount").val(temp.toFixed(2));

              $("#net_amount_value").val(temp.toFixed(2));



              $("#rate_"+row_id).val('');

              $("#rate_value_"+row_id).val('');

              $("#qty_"+row_id).val('');

              $("#qty_"+row_id).prop("disabled", true);

              $("#qty_value_"+row_id).val('');

              $("#amount_"+row_id).val('');

              $("#amount_value_"+row_id).val('');



            }

          }

        } // /success





      }); // /ajax function to fetch the product data 

    }



  }



  function getTotal(row = null) {

    if(row) {

      var total = Number($("#rate_value_"+row).val()) * Number($("#qty_"+row).val());

      if($("#return_items_amount_"+row).val())

      {

        total = (Number($("#rate_value_"+row).val()) * Number($("#qty_"+row).val())) - Number($("#return_items_amount_"+row).val());

      }

      total = total.toFixed(2);

      $("#amount_"+row).val(total);

      $("#amount_value_"+row).val(total);

      subAmount();

    } else {

      alert('no row !! please refresh the page');

    }

  }



  function getTotal_2(row = null) {

  	if(row)

  	{

  		var total = Number($("#rate_value_"+row).val()) * Number($("#qty_"+row).val());

  		total = total.toFixed(2);

  		$("#amount_"+row).val(total);

  		$("#amount_value_"+row).val(total);

  		subAmount();

  	}

  }



  function subAmount() {

    var tableProductLength = $("#product_info_table tbody tr").length;

    var totalSubAmount = 0;

    for(x = 0; x < tableProductLength; x++) {

      var tr = $("#product_info_table tbody tr")[x];

      var count = $(tr).attr('id');

      count = count.substring(4);



      totalSubAmount = Number(totalSubAmount) + Number($("#amount_"+count).val());

    }

    totalSubAmount = totalSubAmount.toFixed(2);

    // sub total

    $("#gross_amount").val(totalSubAmount);

    $("#gross_amount_value").val(totalSubAmount);



    var sales_tax = $('#sales_tax').val();

    var sales_tax = (totalSubAmount * sales_tax) / 100;

    $("#sales_tax_value").val(sales_tax);

    $("#sales_tax_val").val(sales_tax);
    var sales_tax_val_total = parseInt(totalSubAmount)+parseInt(sales_tax)
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     

     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);
    $("#sales_tax_value_total").val(sales_tax_val_total);







    var w_h_t = $('#w_h_t').val();
     if($('#sales_tax').val() == 0){
    sales_tax_val_total = totalSubAmount;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
   if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
     }
    $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);

    





    // loan deduction

    var loan_deduction = 0;

    if($("#loan_deduction").val()){

      loan_deduction = $("#loan_deduction").val();

    }

    // affair / loading

    var affair_loading = 0;

    if($("#affair_loading").val()){

      affair_loading = $("#affair_loading").val();

    }

    // fine deduction

    var fine_deduction = 0;

    if($("#fine_deduction").val()){

      fine_deduction = $("#fine_deduction").val();

    }

  // other deduction

    var other_deduction = 0;

    if($("#other_deduction").val()){

      other_deduction = $("#other_deduction").val();

    }

    // final sub amount

    var totalSubAmount = Number(totalSubAmount) - Number(loan_deduction) - Number(fine_deduction) - Number(other_deduction) + Number(affair_loading);

    totalSubAmount = totalSubAmount.toFixed(2);

    

    var discount = $("#discount").val();

    if(discount) {

      var grandTotal = Number(totalSubAmount) - Number(discount);

      grandTotal = grandTotal.toFixed(2);

    var sales_tax = $('#sales_tax').val();

    var sales_tax = (grandTotal * sales_tax) / 100;
    var sales_tax_val_total = parseInt(grandTotal)+parseInt(sales_tax)
    $("#sales_tax_value").val(sales_tax);
    $("#sales_tax_val").val(sales_tax);
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





    var w_h_t = $('#w_h_t').val();
     if($('#sales_tax').val() == 0){
    sales_tax_val_total = grandTotal;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
   if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
     }
    $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);



      grandTotal = parseInt(grandTotal)+parseInt($('#w_h_t_value').val())+parseInt($('#sales_tax_value').val());
      if($('#w_h_t').val()!= 0){
      grandTotal = $('#w_h_t_val_total').val();
      }
      $("#net_amount").val(grandTotal);

      $("#net_amount_value").val(grandTotal);

      

      if(grandTotal < 0)

      {

        alert("Net amount should be positive or zero");

        $('#btnCreateOrder').prop('disabled', true);

      }

      else

      {

        $('#btnCreateOrder').prop('disabled', false);

      }

    } else {

       var sales_tax = $('#sales_tax').val();

      var sales_tax = (totalSubAmount * sales_tax) / 100;

      $("#sales_tax_value").val(sales_tax);

      $("#sales_tax_val").val(sales_tax);
      var sales_tax_val_total = parseInt(totalSubAmount)+parseInt(sales_tax)
       if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





      var w_h_t = $('#w_h_t').val();

       if($('#sales_tax').val() == 0){
    sales_tax_val_total = totalSubAmount;
    }

      var w_h_t = (sales_tax_val_total * w_h_t) / 100;

      $("#w_h_t_value").val(w_h_t);

      $("#w_h_t_val").val(w_h_t);
      var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
      if($('#w_h_t_val').val() == 0){
        w_h_t_val_total = 0;
      }
      $("#w_h_t_val_total").val(w_h_t_val_total);
      $("#w_h_t_value_total").val(w_h_t_val_total);

      totalSubAmount = parseInt(totalSubAmount)+parseInt($('#w_h_t_value').val())+parseInt($('#sales_tax_value').val());
      if($('#w_h_t_val').val() != 0){
      totalSubAmount = $('#w_h_t_val_total').val();
      }
      $("#net_amount").val(totalSubAmount);

      $("#net_amount_value").val(totalSubAmount); 



    }

  }

  function subAmount_2() {

    var tableProductLength = $("#product_info_table tbody tr").length;

    var totalSubAmount = 0;

    for(x = 0; x < tableProductLength; x++) {

      var tr = $("#product_info_table tbody tr")[x];

      var count = $(tr).attr('id');

      count = count.substring(4);



      totalSubAmount = Number(totalSubAmount) + Number($("#amount_"+count).val());

    }

    totalSubAmount = totalSubAmount.toFixed(2);

    // sub total

    $("#gross_amount").val(totalSubAmount);

    $("#gross_amount_value").val(totalSubAmount);





    var sales_tax = $('#sales_tax').val();

    var sales_tax = (totalSubAmount * sales_tax) / 100;

    $("#sales_tax_value").val(sales_tax);

    $("#sales_tax_val").val(sales_tax);
    var sales_tax_val_total = parseInt(totalSubAmount)+parseInt(sales_tax)
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





    var w_h_t = $('#w_h_t').val();
     if($('#sales_tax').val() == 0){
    sales_tax_val_total = totalSubAmount;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
    if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
    }

      $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);

    // discount

    var discount = 0;

    if($("#discount").val()){

      var discount = $("#discount").val();

    }

    // affair / loading

    var affair_loading = 0;

    if($("#affair_loading").val()){

      affair_loading = $("#affair_loading").val();

    }

    // fine deduction

    var fine_deduction = 0;

    if($("#fine_deduction").val()){

      fine_deduction = $("#fine_deduction").val();

    }

    // other deduction

    var other_deduction = 0;

    if($("#other_deduction").val()){

      other_deduction = $("#other_deduction").val();

    }

    // final sub amount

    var totalSubAmount = (Number(totalSubAmount)) - Number(discount) - Number(fine_deduction) - Number(other_deduction) + Number(affair_loading);

    totalSubAmount = totalSubAmount.toFixed(2);



    var loan_deduction = $("#loan_deduction").val();

    if(loan_deduction) {



      var loan_amount = $("#loan_amount").val();

      if(Number(loan_amount) < Number(loan_deduction))

      {

        alert("Loan deduction should be less than Loan Amount");

        $('#btnCreateOrder').prop('disabled', true);

      }

      else

      {

        $('#btnCreateOrder').prop('disabled', false);

        var grandTotal = Number(totalSubAmount) - Number(loan_deduction);

        grandTotal = grandTotal.toFixed(2);

        grandTotal = parseInt(grandTotal)+parseInt($('#w_h_t_value').val())+parseInt($('#sales_tax_value').val());
          if($('#w_h_t').val()!= 0){
      grandTotal = $('#w_h_t_val_total').val();
      }



        $("#net_amount").val(grandTotal);

        $("#net_amount_value").val(grandTotal);



        document.getElementById("remainingAmount").setAttribute("style", "display: visible;");

        var loan_amount = $("#loan_amount").val();

        var new_loan_amount = Number(loan_amount) - Number(loan_deduction);

        $("#new_loan_amount").val(new_loan_amount);

      }



    }

    else

    {

      var sales_tax = $('#sales_tax').val();

    var sales_tax = (totalSubAmount * sales_tax) / 100;

    $("#sales_tax_value").val(sales_tax);

    $("#sales_tax_val").val(sales_tax);
    var sales_tax_val_total = parseInt(totalSubAmount)+parseInt(sales_tax)
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





    var w_h_t = $('#w_h_t').val();
     if($('#sales_tax').val() == 0){
    sales_tax_val_total = totalSubAmount;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
    if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
    }

      $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);

      totalSubAmount = parseInt(totalSubAmount)+parseInt($('#w_h_t_value').val())+parseInt($('#sales_tax_value').val());
      
      if($('#w_h_t_val').val() != 0){
      totalSubAmount = $('#w_h_t_val_total').val();
      }


      $("#net_amount").val(totalSubAmount);

      $("#net_amount_value").val(totalSubAmount);

      

      document.getElementById("remainingAmount").setAttribute("style", "display: none;");

      $("#new_loan_amount").val(0);

    } 

  }



  function subAmount_3() {



    var tableProductLength = $("#product_info_table tbody tr").length;

    var totalSubAmount = 0;

    for(x = 0; x < tableProductLength; x++) {

      var tr = $("#product_info_table tbody tr")[x];

      var count = $(tr).attr('id');

      count = count.substring(4);



      totalSubAmount = Number(totalSubAmount) + Number($("#amount_"+count).val());

    }

    totalSubAmount = totalSubAmount.toFixed(2);

    // sub total

    $("#gross_amount").val(totalSubAmount);

    $("#gross_amount_value").val(totalSubAmount);





    var sales_tax = $('#sales_tax').val();

    var sales_tax = (totalSubAmount * sales_tax) / 100;

    $("#sales_tax_value").val(sales_tax);

    $("#sales_tax_val").val(sales_tax);
    var sales_tax_val_total = parseInt(totalSubAmount)+parseInt(sales_tax)
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





    var w_h_t = $('#w_h_t').val();
     if($('#sales_tax').val() == 0){
    sales_tax_val_total = totalSubAmount;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
    if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
    }


      $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);

    // loan deduction

    var loan_deduction = 0;

    if($("#loan_deduction").val()){

      loan_deduction = $("#loan_deduction").val();

    }

    // discount

    var discount = 0;

    if($("#discount").val()){

      discount = $("#discount").val();

    }

    // fine deduction

    var fine_deduction = 0;

    if($("#fine_deduction").val()){

      fine_deduction = $("#fine_deduction").val();

    }

    // other dedcution

    var other_deduction = 0;

    if($("#other_deduction").val()){

      other_deduction = $("#other_deduction").val();

    }

    // final sub total

    var totalSubAmount = Number(totalSubAmount) - Number(loan_deduction) - Number(discount) - Number(fine_deduction) - Number(other_deduction);

    totalSubAmount = totalSubAmount.toFixed(2);



    var affair_loading = $("#affair_loading").val();

    if(affair_loading) {

      var grandTotal = Number(totalSubAmount) + Number(affair_loading);

      grandTotal = grandTotal.toFixed(2);

      grandTotal = parseInt(grandTotal)+parseInt($('#w_h_t_value').val())+parseInt($('#sales_tax_value').val());
        if($('#w_h_t').val()!= 0){
      grandTotal = $('#w_h_t_val_total').val();
      }
      $("#net_amount").val(grandTotal);

      $("#net_amount_value").val(grandTotal);

      if(grandTotal < 0)

      {

        alert("Net amount should be positive or zero");

        $('#btnCreateOrder').prop('disabled', true);

      }

      else

      {

        $('#btnCreateOrder').prop('disabled', false);

      }

    } else {

      var sales_tax = $('#sales_tax').val();

    var sales_tax = (totalSubAmount * sales_tax) / 100;

    $("#sales_tax_value").val(sales_tax);

    $("#sales_tax_val").val(sales_tax);
    var sales_tax_val_total = parseInt(totalSubAmount)+parseInt(sales_tax)
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





    var w_h_t = $('#w_h_t').val();
     if($('#sales_tax').val() == 0){
    sales_tax_val_total = totalSubAmount;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
   if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
     }
    $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);

      totalSubAmount = parseInt(totalSubAmount)+parseInt($('#w_h_t_value').val())+parseInt($('#sales_tax_value').val());
      if($('#w_h_t_val').val() != 0){
      totalSubAmount = $('#w_h_t_val_total').val();
      }
      $("#net_amount").val(totalSubAmount);

      $("#net_amount_value").val(totalSubAmount);

      

    }

  }



  function subAmount_4() {

    var tableProductLength = $("#product_info_table tbody tr").length;

    var totalSubAmount = 0;

    for(x = 0; x < tableProductLength; x++) {

      var tr = $("#product_info_table tbody tr")[x];

      var count = $(tr).attr('id');

      count = count.substring(4);



      totalSubAmount = Number(totalSubAmount) + Number($("#amount_"+count).val());

    }

    totalSubAmount = totalSubAmount.toFixed(2);



    // sub total

    $("#gross_amount").val(totalSubAmount);

    $("#gross_amount_value").val(totalSubAmount);





    var sales_tax = $('#sales_tax').val();

    var sales_tax = (totalSubAmount * sales_tax) / 100;

    $("#sales_tax_value").val(sales_tax);

    $("#sales_tax_val").val(sales_tax);
    var sales_tax_val_total = parseInt(totalSubAmount)+parseInt(sales_tax)
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





    var w_h_t = $('#w_h_t').val();
     if($('#sales_tax').val() == 0){
    sales_tax_val_total = totalSubAmount;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
   if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
     }
    $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);



    // discount

    var discount = 0;

    if($("#discount").val()){

      var discount = $("#discount").val();

    }

    // affair / loading

    var affair_loading = 0;

    if($("#affair_loading").val()){

      affair_loading = $("#affair_loading").val();

    }

    // loan deduction

    var loan_deduction = 0;

    if($("#loan_deduction").val()){

      loan_deduction = $("#loan_deduction").val();

    }

    // other deduction

    var other_deduction = 0;

    if($("#other_deduction").val()){

      other_deduction = $("#other_deduction").val();

    }

    // final sub amount

    var totalSubAmount = (Number(totalSubAmount)) - Number(discount) - Number(loan_deduction) - Number(other_deduction) + Number(affair_loading);

    totalSubAmount = totalSubAmount.toFixed(2);

    var fine_deduction = $("#fine_deduction").val();

    if(fine_deduction) {

      var fine_deduction = $("#fine_deduction").val();

      var grandTotal = Number(totalSubAmount) - Number(fine_deduction);

    grandTotal = grandTotal.toFixed(2);

    var sales_tax = $('#sales_tax').val();

    var sales_tax = (grandTotal * sales_tax) / 100;

    $("#sales_tax_value").val(sales_tax);

    $("#sales_tax_val").val(sales_tax);
    var sales_tax_val_total = parseInt(grandTotal)+parseInt(sales_tax)
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





    var w_h_t = $('#w_h_t').val();
     if($('#sales_tax').val() == 0){
    sales_tax_val_total = grandTotal;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
   if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
     }
    $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);
    grandTotal = parseInt(grandTotal)+parseInt($('#w_h_t_value').val())+parseInt($('#sales_tax_value').val());
      if($('#w_h_t').val()!= 0){
      grandTotal = $('#w_h_t_val_total').val();
      }
    $("#net_amount").val(grandTotal);

    $("#net_amount_value").val(grandTotal);

    

    if(grandTotal < 0)

    {

      alert("Net amount should be positive or zero");

      $('#btnCreateOrder').prop('disabled', true);

    } else{

        $('#btnCreateOrder').prop('disabled', false);

     }

    }

    else

    {

      var sales_tax = $('#sales_tax').val();

    var sales_tax = (totalSubAmount * sales_tax) / 100;

    $("#sales_tax_value").val(sales_tax);

    $("#sales_tax_val").val(sales_tax);
    var sales_tax_val_total = parseInt(totalSubAmount)+parseInt(sales_tax)
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





    var w_h_t = $('#w_h_t').val();
     if($('#sales_tax').val() == 0){
    sales_tax_val_total = totalSubAmount;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
   if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
     }
    $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);

      totalSubAmount = parseInt(totalSubAmount)+parseInt($('#w_h_t_value').val())+parseInt($('#sales_tax_value').val());
      if($('#w_h_t_val').val() != 0){
      totalSubAmount = $('#w_h_t_val_total').val();
      }
      $("#net_amount").val(totalSubAmount);

      $("#net_amount_value").val(totalSubAmount);

      

    }

  }



  function subAmount_5() {



    var tableProductLength = $("#product_info_table tbody tr").length;

    var totalSubAmount = 0;

    for(x = 0; x < tableProductLength; x++) {

      var tr = $("#product_info_table tbody tr")[x];

      var count = $(tr).attr('id');

      count = count.substring(4);



      totalSubAmount = Number(totalSubAmount) + Number($("#amount_"+count).val());

    }

    totalSubAmount = totalSubAmount.toFixed(2);



    // sub total

    $("#gross_amount").val(totalSubAmount);

    $("#gross_amount_value").val(totalSubAmount);





    var sales_tax = $('#sales_tax').val();

    var sales_tax = (totalSubAmount * sales_tax) / 100;

    $("#sales_tax_value").val(sales_tax);

    $("#sales_tax_val").val(sales_tax);
    var sales_tax_val_total = parseInt(totalSubAmount)+parseInt(sales_tax)
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





    var w_h_t = $('#w_h_t').val();
    if($('#sales_tax').val() == 0){
    sales_tax_val_total = totalSubAmount;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
   if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
     }
    $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);



    // discount

    var discount = 0;

    if($("#discount").val()){

      var discount = $("#discount").val();

    }

    // affair / loading

    var affair_loading = 0;

    if($("#affair_loading").val()){

      affair_loading = $("#affair_loading").val();

    }

    // loan deduction

    var loan_deduction = 0;

    if($("#loan_deduction").val()){

      loan_deduction = $("#loan_deduction").val();

    }

    // fine deduction

    var fine_deduction = 0;

    if($("#fine_deduction").val()){

      fine_deduction = $("#fine_deduction").val();

    }

    // final sub amount

    var totalSubAmount = (Number(totalSubAmount)) - Number(discount) - Number(loan_deduction) - Number(fine_deduction) + Number(affair_loading);

    totalSubAmount = totalSubAmount.toFixed(2);

    var other_deduction = $("#other_deduction").val();

    if(other_deduction) {

      var other_deduction = $("#other_deduction").val();

      var grandTotal = Number(totalSubAmount) - Number(other_deduction);

    grandTotal = grandTotal.toFixed(2);

    var sales_tax = $('#sales_tax').val();

    var sales_tax = (grandTotal * sales_tax) / 100;

    $("#sales_tax_value").val(sales_tax);

    $("#sales_tax_val").val(sales_tax);
    var sales_tax_val_total = parseInt(grandTotal)+parseInt(sales_tax)
    if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





    var w_h_t = $('#w_h_t').val();
    if($('#sales_tax').val() == 0){
    sales_tax_val_total = grandTotal;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
   if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
     }
    $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);

    grandTotal = parseInt(grandTotal)+parseInt($('#w_h_t_value').val())+parseInt($('#sales_tax_value').val());
      if($('#w_h_t').val()!= 0){
      grandTotal = $('#w_h_t_val_total').val();
      }
    $("#net_amount").val(grandTotal);

    $("#net_amount_value").val(grandTotal);

      

    if(grandTotal < 0)

    {

      alert("Net amount should be positive or zero");

      $('#btnCreateOrder').prop('disabled', true);

    } else{

        $('#btnCreateOrder').prop('disabled', false);

     }

    }

    else

    {

      

    var sales_tax = $('#sales_tax').val();

    var sales_tax = (totalSubAmount * sales_tax) / 100;

    $("#sales_tax_value").val(sales_tax);

    $("#sales_tax_val").val(sales_tax);
    var sales_tax_val_total = parseInt(totalSubAmount)+parseInt(sales_tax)
     if($('#sales_tax').val() == 0){
      sales_tax_val_total = 0;
     }
     $("#sales_tax_val_total").val(sales_tax_val_total);
     $("#sales_tax_value_total").val(sales_tax_val_total);





    var w_h_t = $('#w_h_t').val();
     if($('#sales_tax').val() == 0){
    sales_tax_val_total = totalSubAmount;
    }
    var w_h_t = (sales_tax_val_total * w_h_t) / 100;

    $("#w_h_t_value").val(w_h_t);

    $("#w_h_t_val").val(w_h_t);
    var w_h_t_val_total = parseInt(sales_tax_val_total)-parseInt(w_h_t)
   if($('#w_h_t_val').val() == 0){
      w_h_t_val_total = 0;
     }
     
    $("#w_h_t_val_total").val(w_h_t_val_total);
    $("#w_h_t_value_total").val(w_h_t_val_total);

      totalSubAmount = parseInt(totalSubAmount)+parseInt($('#w_h_t_value').val())+parseInt($('#sales_tax_value').val());
      if($('#w_h_t_val').val() != 0){
      totalSubAmount = $('#w_h_t_val_total').val();
      }

      $("#net_amount").val(totalSubAmount);
      $("#net_amount_value").val(totalSubAmount); 

    }

  }



  function removeRow(tr_id)

  {

  	$("#product_info_table tbody tr#row_"+tr_id).remove();

    subAmount();

  }



  function removePaymentRow(tr_id)

  {

    // remove required attr from first row

    if(tr_id == 2){

      var select_payment_1 = $('#select_payment_1').val();

      if(select_payment_1 == '')

      {

        $('#select_payment_1').removeAttr('required');

        $("#payment_date_1").removeAttr("required");

        $("#amount_paid_1").prop('min',0);

      }

    }

    $("#payment_info_table tbody tr#row_"+tr_id).remove();

  }



  
    subAmount();

</script>

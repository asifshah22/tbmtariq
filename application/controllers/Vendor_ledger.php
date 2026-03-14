
<?php
class Vendor_ledger extends CI_Controller 
{
  var $permission = array();

  public function __construct()
	{
    parent::__construct();
    $group_data = array();
    if(!$this->session->userdata('logged_in')){
			redirect('User/index');		
		}
		else {
			$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$this->data['user_permission'] = unserialize($group_data['permission']);
			$this->permission = unserialize($group_data['permission']);
		}
  }
    
  public function index()
  {
    if(!in_array('viewVendorLedger', $this->permission)) 
    {
      $data['page_title'] = "No Permission";
      $this->load->view('templates/header', $data);
      $this->load->view('templates/header_menu');
      $this->load->view('templates/side_menubar');
      $this->load->view('errors/forbidden_access');
    }
    else
    {
      if(isset($_GET['date_range']) && isset($_GET['selected_vendor']))
      {
        $range = $_GET['date_range'];
        $ex = explode(' - ', $range);
        $from = date('d-m-Y', strtotime($ex[0]));
        $to = date('d-m-Y', strtotime($ex[1]));

        $result = array('data' => array());

        $data = $this->Model_vendor_ledger->getVendorPurchasedOrders($_GET['selected_vendor']);
        $order_ids = array();
        // for getting unpaid amount if exist before the first order
        $all_order_ids = array();
        foreach ($data as $key => $value) {

        	$date = date('d-m-Y', strtotime($value['datetime_created']));
        	$time = date('h:i a', strtotime($value['datetime_created']));
        	$date_time = $date . ' ' . $time;
        	if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))
        	{
	        	if(!in_array($value['purchase_order_id'], $order_ids)){
	        		array_push($order_ids, $value['purchase_order_id']);
	        	}
        	}
        	if(!in_array($value['purchase_order_id'], $all_order_ids)){
        		array_push($all_order_ids, $value['purchase_order_id']);
        	}
        	
        } // foreach

        // if this orders of vendor exist else unpaid amount = 0
        $unpaid_amount = 0;
        if(!empty($order_ids))
        {

          $date1 = date('d-m-Y', 
            strtotime($this->Model_products->getPurchaseOrdersData($order_ids[0])['datetime_created'])
          );

          foreach ($all_order_ids as $key => $value) 
          {
            $purchase_order_data = $this->Model_products->getPurchaseOrdersData($value);
            $date2 = date('d-m-Y', strtotime($purchase_order_data['datetime_created']));
            if(strtotime($date1) > strtotime($date2))
            {
              $purchase_items_data = $this->Model_products->getPurchaseItemsData($value);
              $purchase_return_data = $this->Model_products->fetchPurchaseReturnsData($value);
              $total_retun_amount = 0;
              foreach ($purchase_items_data as $k => $v) {
                foreach ($purchase_return_data as $returnKey => $returnValue) {
                  if($returnValue['product_id'] == $v['product_id'] && $returnValue['unit_id'] == $v['unit_id'])
                  {
                    $total_retun_amount += $returnValue['amount'];
                    break;
                  }
                  else{
                    $total_retun_amount = 0;
                  }
                }
              }
            }
          }
        }

        $data['page_title'] = "Vendor - Vendor Ledger";
        $data['heading'] = "Vendor Ledger";

        $data['result'] = $order_ids;
        $data['vendor_id'] = $_GET['selected_vendor'];
        $data['from'] = $from;
        $data['to'] = $to;
        
        $user_id = $this->session->userdata('id');
        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
        $data['user_permission'] = unserialize($group_data['permission']);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/header_menu');
        $this->load->view('templates/side_menubar');

        $this->load->view('vendor_ledger/index');  
      }
      else{
        $data['page_title'] = "Vendors - Vendors Ledger";
        $data['heading'] = "Vendors Ledger";
        $data['result'] = array();
        $data['vendor_id'] = '';

        $user_id = $this->session->userdata('id');
        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
        $data['user_permission'] = unserialize($group_data['permission']);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/header_menu');
        $this->load->view('templates/side_menubar');
        $this->load->view('vendor_ledger/index');
      }
    }
  }

  public function print_vendor_ledger()
  {
    if(!in_array('printVendorLedger', $this->permission)) 
    {
      $data['page_title'] = "No Permission";
      $this->load->view('templates/header', $data);
      $this->load->view('templates/header_menu');
      $this->load->view('templates/side_menubar');
      $this->load->view('errors/forbidden_access');
    }
    else
    {
      if(isset($_GET['date_range']) && isset($_GET['selected_vendor']))
      {
        $range = $_GET['date_range'];
        $ex = explode(' - ', $range);
        $from = date('d-m-Y', strtotime($ex[0]));
        $to = date('d-m-Y', strtotime($ex[1]));

        $result = array('data' => array());

        $data = $this->Model_vendor_ledger->getVendorPurchasedOrders($_GET['selected_vendor']);
        $order_ids = array();
        // for getting unpaid amount if exist before the first order
        $all_order_ids = array();
        foreach ($data as $key => $value) {

          $date = date('d-m-Y', strtotime($value['datetime_created']));
          $time = date('h:i a', strtotime($value['datetime_created']));
          $date_time = $date . ' ' . $time;
          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))
          {
            if(!in_array($value['purchase_order_id'], $order_ids)){
              array_push($order_ids, $value['purchase_order_id']);
            }
          }
          if(!in_array($value['purchase_order_id'], $all_order_ids)){
            array_push($all_order_ids, $value['purchase_order_id']);
          }
          
        } // foreach

        // if this orders of vebdor exist else unpaid amount = 0
        $unpaid_amount = 0;
        if(!empty($order_ids))
        {

          $date1 = date('d-m-Y', 
            strtotime($this->Model_products->getPurchaseOrdersData($order_ids[0])['datetime_created'])
          );

          foreach ($all_order_ids as $key => $value) 
          {
            $purchase_order_data = $this->Model_products->getPurchaseOrdersData($value);
            $date2 = date('d-m-Y', strtotime($purchase_order_data['datetime_created']));
            if(strtotime($date1) > strtotime($date2))
            {
              $purchase_items_data = $this->Model_products->getPurchaseItemsData($value);
              $purchase_return_data = $this->Model_products->fetchPurchaseReturnsData($value);
              $total_retun_amount = 0;
              foreach ($purchase_items_data as $k => $v) {
                foreach ($purchase_return_data as $returnKey => $returnValue) {
                  if($returnValue['product_id'] == $v['product_id'] && $returnValue['unit_id'] == $v['unit_id'])
                  {
                    $total_retun_amount += $returnValue['amount'];
                    break;
                  }
                  else{
                    $total_retun_amount = 0;
                  }
                }
              }
              $total_amount_paid = $purchase_order_data['total_paid'];
              
              $amount_to_be_paid = ($purchase_order_data['net_amount'] - $total_retun_amount);
              if($amount_to_be_paid > $total_amount_paid)
              {
                $unpaid_amount += ($amount_to_be_paid - $total_amount_paid);
              }
            }
          }
        }

        $data['result'] = $order_ids;
        $data['vendor_id'] = $_GET['selected_vendor'];
        $data['unpaid_amount'] = $unpaid_amount;
        $data['from'] = $from;
        $data['to'] = $to;
      }
      else
      {
        $data['result'] = array();
        $data['vendor_id'] = "";
      }
      date_default_timezone_set("Asia/Karachi");
      $range_to = date('m/d/Y');
      $range_from = date('m/d/Y');
      
      $print_date = date('d/m/Y');
      $user_id = $this->session->userdata('id');
      $user_data = $this->Model_users->getUserData($user_id);
      $html = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
              <meta charset="UTF-8">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <meta http-equiv="X-UA-Compatible" content="ie=edge">
              <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">
              <style>
                .invoice-title h2, .invoice-title h3 {
                  display: inline-block;
                }
                
              </style>
              <title>TBM - Print Vendor Ledger</title>
            </head>
            <body onload="window.print();">
              <div class="container">
                <div class="row">
                  <div class="col-xs-12">
                    <div class="invoice-title text-center">
                      <h3>TBM Automobile Private Ltd</h3>
                    </div>
                    <hr>
                    <div class="row">
                      <div class="col-xs-6">
                        <address style="text-transform:capitalize">
                          <strong>Printed By:</strong><br>
                            '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>
                        </address>
                      </div>
                      <div class="col-xs-6 text-right">
                        <address>
                          <strong>Print Date:</strong><br>
                            '.date("d-m-Y").'<br>                      
                        </address>
                      </div>
                    </div>
                  </div>
                </div>
                              
                <div class="row">
                  <div class="col-md-12">
                    
                        <div class="table-responsive">
                          <table id="example1" class="table table-bordered">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Vendor</th>
                                <th width="50%">Products & Payments</th>
                                <th width="30%"></th>
                                <th width="10%">Delivary Date</th>
                              </tr>
                            </thead>
                            <tbody bgcolor="#eaeaea">';

                              $vendor_payments_data = $this->Model_supplier->fetchSupplierPaymentsData($data['vendor_id'], $most_recent_order_id = 0);
                              $vendor_payments_array = array();
                              if(!empty($vendor_payments_data)){
                                foreach ($vendor_payments_data as $key => $value) {
                                  $date = date('d-m-Y', strtotime($value['datetime_creation']));
                                  if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date)){
                                    array_push($vendor_payments_array, $value);
                                  }
                                }
                              }
                              $vendor_data = $this->Model_supplier->getSupplierData($data['vendor_id']);
                              $firstIteration = true; 
                              $opening_balance = 0;
                              $closing_balance = 0;
                              $counter = 1;
                              if(!empty($vendor_payments_array)):
                                $vendor_name = $vendor_data['first_name']. ' '. $vendor_data['last_name'];
                                $total_amount_paid = 0;
                                $opening_balance = $vendor_data['starting_balance'];
                                $html .='<tr>
                                          <td>#</td>
                                          <td style="text-transform: capitalize">
                                            '.$vendor_name.'
                                          </td>
                                          <td>
                                            <table class="table table-bordered example3">
                                              <thead>
                                                <tr>
                                                  <th>Payment Method</th>
                                                  <th>Paid</th>
                                                  <th>Payment Note</th>
                                                  <th>DateTime</th>
                                                  <th>Paid By</th>
                                                </tr>
                                              </thead>
                                              <tbody>';
                                foreach($vendor_payments_array as $key => $value):
                                  $date = date('d-m-Y', strtotime($value['datetime_creation']));
                                  $time = date('h:i a', strtotime($value['datetime_creation']));
                                  $date_time = $date . ' ' . $time;
                                  $payment_method = $value['payment_method'];
                                  $paid_by = '';
                                  if($value['paid_by'] == 1)
                                  {
                                    $paid_by = "TBM";
                                   }
                                  elseif($value['paid_by'] == 2)
                                  {
                                    $paid_by = "Vendor";
                                  }
                                  $total_amount_paid += $value['paid_amount'];
                                  $html .='
                                                <tr>
                                                  <td>'.$payment_method.'</td>
                                                  <td>'.floatval($value['paid_amount']).'</td>
                                                  <td style="white-space:pre-wrap; word-wrap:break-word">'.$value['payment_note'].'</td>
                                                  <td>'.$date_time.'</td>
                                                  <td>'.$paid_by.'</td>
                                                </tr>';
                                endforeach;
                                $html .='
                                              </tbody>
                                            </table>
                                            <span style="background-color: #ffffff"><strong>Total Amount Paid: '.floatval($total_amount_paid).'</strong></span>
                                          </td>
                                          <td width="25%" bgcolor="#ffffff">
                                            <div class="row">
                                              <div class="col-xs-6">
                                                <p><strong>Opening Balance:</strong></p>
                                              </div>
                                              <div class="col-xs-6 text-right">
                                                <p><strong>'.floatval($opening_balance).'</strong></p>
                                              </div>
                                             </div>
                                            <div class="row">
                                              <div class="col-xs-6">
                                                 <p><strong>Total Paid:</strong></p>
                                              </div>
                                              <div class="col-xs-6 text-right">
                                                <p><strong>'.floatval($total_amount_paid).'</strong></p>
                                              </div>
                                            </div>
                                          ';
                                if($value['paid_by'] == 1){
                                  $closing_balance = $opening_balance - $total_amount_paid;
                                }
                                elseif($value['paid_by'] == 2){
                                  $closing_balance = $opening_balance + $total_amount_paid;
                                }
                                $html .='   <div class="row">
                                              <div class="col-xs-6">
                                                 <p><strong>Closing Balance:</strong></p>
                                              </div>
                                              <div class="col-xs-6 text-right">
                                                <p><strong>'.number_format($closing_balance, 2).'</strong></p>
                                              </div>
                                            </div>
                                          ';
                                $opening_balance = $closing_balance;
                                $html .= '  
                                          </td>
                                          <td></td>
                                        </tr>';

                              endif;
                              foreach($data['result'] as $key => $value):
                                $purchase_order_data = $this->Model_products->getPurchaseOrdersData($value);
                                $purchase_items_data = $this->Model_products->getPurchaseItemsData($value);
                                $purchase_return_data = $this->Model_products->fetchPurchaseReturnsData($value);
                                $vendor_ob_payment_data = $this->Model_products->fetchVendorOBPaymentData($value);
                                $loan_deductions = $this->Model_loan->getLoanDeductions($value);
                                $vendor_id = $purchase_items_data[0]['vendor_id'];
                                $vendor_data = $this->Model_supplier->getSupplierData($vendor_id);
                                $purchase_return_amount = 0;
                                foreach ($purchase_return_data as $key => $value) {
                                    $purchase_return_amount += $value['amount'];
                                }
                                if($firstIteration)
                                {
                                  $firstIteration = false;
                                  $opening_balance = $purchase_order_data['opening_balance'];
                                }
                          $html .='
                                <tr>
                                  <td>
                                    '.$purchase_order_data['bill_no'].' &#8212 '.$purchase_order_data['id'].'
                                  </td>
                                  <td style="text-transform: capitalize;">'. $vendor_data['first_name']. " ". $vendor_data['last_name'] .'</td>
                                  <td>
                                    <table class="table table-bordered example2">
                                      <thead>
                                        <tr>
                                          <th>Name</th>
                                          <th>Unit</th>
                                          <th>Quantity</th>
                                          <th>Returns(P.P)</th>
                                          <th>Rate</th>
                                          <th>Total</th>
                                        </tr>
                                      </thead>
                                      <tbody>';
                                        $total_retun_amount = 0;
                                        $total_products_amount = 0;
                                        $total_qty_delivered = 0;
                                        foreach($purchase_items_data as $k => $v):
                                          $product_data = $this->Model_products->getAllProductData($v['product_id']);
                                          $category_data = $this->Model_products->getAllProductCategoryData($v['product_id'], $v['category_id']);
                                          
                                          $unit_name = '';
                                          $unit_value = 1;
                                          $units_data = $this->Model_products->getAllUnitsData();
                                          foreach ($units_data as $unit)
                                          {
                                            $unit_id = $v['unit_id'];
                                            $unit_data_values = $this->Model_products->getUnitValuesData();
                                            if($unit['id'] == $unit_id)
                                            {
                                              foreach ($unit_data_values as $key_2 => $value_2)
                                              {
                                                if($unit['id'] == $value_2['unit_id'])
                                                {
                                                  $unit_name = $unit['unit_name'];
                                                  $unit_value = $value_2['unit_value'];
                                                  break;
                                                }
                                              }
                                            }
                                          }
                                          $item_name = '';
                                          if(!empty($category_data) && $category_data['category_name'])
                                          {
                                            $item_name = $product_data['name']. ' - ' .$category_data['category_name'];
                                          }
                                          else
                                          {
                                            $item_name = $product_data['name'];
                                          }
                                          $return_qty = 0;
                                          foreach ($purchase_return_data as $returnKey => $returnValue) {
                                            if($returnValue['product_id'] == $v['product_id'] && $returnValue['unit_id'] == $v['unit_id']){
                                              $return_qty = $returnValue['qty'];
                                              $total_retun_amount += $returnValue['amount'];
                                              break;
                                            }
                                            else{
                                              $return_qty = 0;
                                              $total_retun_amount = 0;
                                            }
                                          }
                                          $total_products_amount += ($v['qty'] * $v['product_price']) - $total_retun_amount;
                                          $total_qty_delivered += $v['qty'] - $return_qty;
                                          $date = date('d-m-Y', strtotime($purchase_order_data['datetime_created']));
                                          $time = date('h:i a', strtotime($purchase_order_data['datetime_created']));
                                          $date_time = $date . ' ' . $time;
                          $html .='  
                                          <tr>
                                            <td>'. $item_name .'</td>
                                            <td>'. $unit_name .'</td>
                                            <td>'. floatval($v['qty']) .'</td>
                                            <td>'. floatval($return_qty) .'</td>
                                            <td>'. floatval($v['product_price']) .'</td>
                                            <td>'. floatval(($v['qty'] * $v['product_price']) - ($total_retun_amount)) .'</td>
                                          </tr>';

                                        endforeach;
                          $html .='
                                      </tbody>
                                    </table>
                                    <p>
                                      <span style="background-color: #ffffff">
                                        <strong>Total Qunatity Delivered: '. floatval($total_qty_delivered) .'</strong>
                                      </span>
                                    </p>
                                    <p>
                                      <span style="background-color: #ffffff">
                                        <strong>Total Products Amount: '. floatval($total_products_amount) .'</strong>
                                      </span>
                                    </p>';
                                      $total_amount_paid = 0;
                                      $temp = '!@#$';
                                      $data = $purchase_order_data;
                                      $payment_method_array = explode($temp, $data['payment_method']);
                                      $payment_date_array = explode($temp, $data['payment_date']);
                                      $paid_array = explode($temp, $data['paid']);
                                      $payment_note_array = explode($temp, $data['payment_note']);
                                      $i = 0;
                                      $x = 1;
                                      if(!empty($payment_method_array[0])):

                          $html .='
                                        <table class="table table-bordered example3">
                                          <thead>
                                            <tr>
                                              <th>Payment Method</th>
                                              <th>Paid</th>
                                              <th>Payment Note</th>
                                              <th>Date</th>
                                            </tr>
                                          </thead>
                                          <tbody>';

                                            foreach($payment_method_array as $payment_key => $payment_value):
                                              $payment_method = $payment_method_array[$i];
                                              $paid_amount = $paid_array[$i];
                                              $payment_note = $payment_note_array[$i];
                                              $payment_date = $payment_date_array[$i];
                                              $total_amount_paid += $paid_amount;
                                              $i++;
                          $html .='
                                              <tr>
                                                <td>'. $payment_method .'</td>
                                                <td>'. floatval($paid_amount) .'</td>
                                                <td style="white-space:pre-wrap; word-wrap:break-word">'.$payment_note.'</td>
                                                <td>'. $payment_date .'</td>
                                              </tr>';
                                            endforeach;
                          $html .='
                                          </tbody>
                                        </table>
                                        <span style="background-color: #ffffff"><strong>Total Amount Paid: '. floatval($total_amount_paid) .'</strong></span>';
                                      endif;
                          $html .='
                                  </td>';
                                  $loan_deduction = 0;
                                  if(!empty($loan_deductions)){
                                    $loan_deduction = $loan_deductions['deduction_amount'];
                                  }
                                  
                          $html .='
                                  <td width="25%" bgcolor="#ffffff">
                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Opening Balance:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'. floatval($opening_balance) .'</strong></p>
                                      </div>
                                    </div>  
                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Gross Amount:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'. floatval(($purchase_order_data['gross_amount'] - $purchase_return_amount)) .'</strong></p>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Loan Deduction:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'. floatval($loan_deduction) .'</strong></p>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Discount:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'. floatval($purchase_order_data['discount']) .'</strong></p>
                                      </div>
                                    </div>
                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Freight Charges:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'. floatval($purchase_order_data['loading_or_affair']) .'</strong></p>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Net Amount:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'. floatval(($purchase_order_data['net_amount'] - $purchase_return_amount)) .'</strong></p>
                                      </div>
                                    </div>

                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Total:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'. floatval(($purchase_order_data['net_amount'] - $purchase_return_amount) + $opening_balance) .'</strong></p>
                                      </div>
                                    </div>
                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Total Paid:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'. floatval($total_amount_paid) .'</strong></p>
                                      </div>
                                    </div>';
                                    $closing_balance = $opening_balance + $purchase_order_data['net_amount'] - $purchase_return_amount - $purchase_order_data['total_paid'];
                          $html .='
                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Closing Balance:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'.number_format($closing_balance, 2).'</strong></p>
                                      </div>
                                    </div>
                            ';
                                      $opening_balance = $closing_balance;
                            
                            $html .='
                                  <td>'. $date_time .'</td>
                                  
                                </tr>';
                                if(!empty($vendor_ob_payment_data)):
                            $html .='
                                <tr>
                                  <td>#</td>
                                  <td style="text-transform: capitalize;">'.$vendor_data['first_name']. ' '. $vendor_data['last_name'].'</td>
                                  <td>
                                     <table class="table table-bordered example3">
                                      <thead>
                                        <tr>
                                          <th>Payment Method</th>
                                          <th>Paid</th>
                                          <th>Payment Note</th>
                                          <th>DateTime</th>
                                          <th>Paid By</th>
                                        </tr>
                                      </thead>
                                      <tbody>';
                                      $total_amount_paid = 0;
                                      foreach($vendor_ob_payment_data as $ob_payment_key => $ob_payment_value):
                                        $date = date('d-m-Y', strtotime($ob_payment_value['datetime_creation']));
                                        $time = date('h:i a', strtotime($ob_payment_value['datetime_creation']));
                                        $date_time = $date . ' ' . $time;
                                        $payment_method = $ob_payment_value['payment_method'];
                                        
                                        $paid_by = '';
                                        if($ob_payment_value['paid_by'] == 1)
                                        {
                                          $paid_by = "TBM";
                                        }
                                        elseif($ob_payment_value['paid_by'] == 2)
                                        {
                                          $paid_by = "Vendor";
                                        }
                                        $total_amount_paid += $ob_payment_value['paid_amount'];
                            $html .='
                                        <tr>
                                          <td>'.$payment_method.'</td>
                                          <td>'.floatval($ob_payment_value['paid_amount']).'</td>
                                          <td style="white-space:pre-wrap; word-wrap:break-word">'.$ob_payment_value['payment_note'].'</td>
                                          <td>'.$date_time.'</td>
                                          <td>'.$paid_by.'</td>
                                        </tr>
                            ';
                                      endforeach;
                            $html .='
                                      </tbody>
                                    </table>
                                    <span style="background-color: #ffffff"><strong>Total Amount Paid: '.floatval($total_amount_paid).'</strong></span>
                                  </td>
                                  <td width="25%" bgcolor="#ffffff">
                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Opening Balance:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'.floatval($opening_balance).'</strong></p>
                                      </div>
                                    </div>
                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Total Paid:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'.floatval($total_amount_paid).'</strong></p>
                                      </div>
                                    </div>
                            ';
                                  $closing_balance = $opening_balance - $total_amount_paid;
                            $html .='
                                    <div class="row">
                                      <div class="col-xs-6">
                                        <p><strong>Closing Balance:</strong></p>
                                      </div>
                                      <div class="col-xs-6 text-right">
                                        <p><strong>'.number_format($closing_balance, 2).'</strong></p>
                                      </div>
                                    </div>
                            ';
                                  $opening_balance = $closing_balance;
                            $html .='
                                  </td>
                                  <td></td>
                                </tr>
                            ';      
                                endif;
                              endforeach;
                          $html .='  
                            </tbody>
                          </table>
                        </div>
                      </div>
                    
                </div>
              </div>
            </body>

            <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>
            <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>
          </html>';
      echo $html; 
    }
  }	

















}
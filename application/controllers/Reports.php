<?php

class Reports extends CI_Controller {



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


  public function wht()
  {
    {

    if(!in_array('viewVendorItemsRate', $this->permission)) {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }else{
      if(isset($_GET['range'])){
       $range = $_GET['range'];
       $ex = explode(' - ', $range);
       $from = date('y-m-d', strtotime($ex[0]));
       $to = date('y-m-d', strtotime($ex[1]));
       $data = $this->Model_products->getPurchaseOrdersWthData($from,$to);

      }else if(isset($_GET['selected_vendor'])){
       $selected_vendor = $_GET['selected_vendor'];
       $data = $this->Model_products->getPurchaseOrdersWthData("","",$selected_vendor);
      }else{
        $data = $this->Model_products->getPurchaseOrdersWthData();
      }


      date_default_timezone_set("Asia/Karachi");
      $to = date('d-m-Y');
      $from = date('d-m-Y');
      $result = array('data' => array());


        $data['page_title'] = "Report - W.H.T";
        $data['heading'] = "W.H.T";
        $data['result'] = $data;
        $user_id = $this->session->userdata('id');
        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
        $data['user_permission'] = unserialize($group_data['permission']);
        $this->load->view('templates/header', $data);
        $this->load->view('templates/header_menu');
        $this->load->view('templates/side_menubar');
        $this->load->view('reports/purchased_orders_wht_detail');
    }
  }
}
  public function print_items_wht()
  {
     if(!in_array('viewVendorItemsRate', $this->permission)) {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }else{
       if(isset($_GET['range'])){
       $range = $_GET['range'];
       $ex = explode(' - ', $range);
       $from = date('y-m-d', strtotime($ex[0]));
       $to = date('y-m-d', strtotime($ex[1]));
       $data = $this->Model_products->getPurchaseOrdersWthData($from,$to);

      }else if(isset($_GET['selected_vendor'])){
       $selected_vendor = $_GET['selected_vendor'];
       $data = $this->Model_products->getPurchaseOrdersWthData("","",$selected_vendor);
      }else{
        $data = $this->Model_products->getPurchaseOrdersWthData();
      }


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

                .table > tbody > tr > .no-line {

                  border-top: none;

                }

                .table > thead > tr > .no-line {

                  border-bottom: none;

                }

                .table > tbody > tr > .thick-line {

                  border-top: 2px solid;

                }

              </style>

              <title>TBM - Vendor Items Rate List</title>

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

                          <table class="table table-condensed table-bordered">

                            <thead>

                              <tr>

                                <th width="10%"><strong>#</strong></th>

                                <th width="15%"><strong>Bill no</strong></th>

                                <th width="15%"><strong>Vendor Name</strong></th>

                                <th width="15%"><strong>W.H.T</strong></th>

                                <th width="15%"><strong>W.H.T total</strong></th>

                                <th width="15%"><strong>Total amount</strong></th>

                              </tr>

                            </thead>

                            <tbody>';

                                foreach ($data as $key => $value) {
                                  if(isset($value['id'])){
                                  $vendor_data = $this->Model_products->getSupplierData($value['vendor_id'] );
                                  $html .= '<tr>

                                    <td>'.$value['id'].'</td>

                                    <td>'.$value['bill_no'].'</td>

                                    <td>'.$vendor_data['first_name']." ".$vendor_data['last_name'].'</td>

                                    <td>'.$value['w_h_t']."%"."(".$value['w_h_t_value'].")".'</td>

                                    <td>'.$value['w_h_t_value_total'].'</td>

                                    <td>'.$value['net_amount'].'</td>

                                  </tr>';

                                }
                                }
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

  public function items_rate()

  {

    if(!in_array('viewVendorItemsRate', $this->permission)) {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else{

      if(isset($_GET['range'])){



        $range = $_GET['range'];

        $ex = explode(' - ', $range);

        $from = date('d-m-Y', strtotime($ex[0]));

        $to = date('d-m-Y', strtotime($ex[1]));



        $result = array('data' => array());



        $data = $this->Model_products->getGroupedProductPricesData();


        $counter = 1;

        foreach ($data as $key => $value) {



          $category_name = '';

          if($value['category_id'])

          {

            $category_data = $this->Model_category->getCategoryData($value['category_id']);

            $category_name = $category_data['name'];



          }

          else

          {

            $category_name = 'Nill';

          }

          $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];



          $date = date('d-m-Y', strtotime($value['product_prices_datetime']));

          $time = date('h:i a', strtotime($value['product_prices_datetime']));



          $date_time = $date . ' ' . $time;

          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))

          {

            $result['data'][$key] = array(

              $counter++,

              $date_time,

              $value['first_name']. ' '. $value['last_name'],

              $category_name,

              $value['product_name'],

              $unit_name,

              $value['price']

            );

          }

          } // /foreach

          $data['page_title'] = "Report - Vendors Items Rate History";

          $data['heading'] = "Vendors Items Rate History";



          $data['result'] = $result;

          $user_id = $this->session->userdata('id');

          $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

          $data['user_permission'] = unserialize($group_data['permission']);



          $this->load->view('templates/header', $data);

          $this->load->view('templates/header_menu');

          $this->load->view('templates/side_menubar');



          $this->load->view('reports/items_rate');

        }

        else if(!empty($_GET['selected_vendor'])){



          $result = array('data' => array());



          $data = $this->Model_products->getGroupedVendorProductPricesData($_GET['selected_vendor']);

          $counter = 1;

          foreach ($data as $key => $value) {



            $category_name = '';

            if($value['category_id'])

            {

              $category_data = $this->Model_category->getCategoryData($value['category_id']);

              $category_name = $category_data['name'];



            }

            else

            {

              $category_name = 'Nill';

            }

            $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];



            $date = date('d-m-Y', strtotime($value['product_prices_datetime']));

            $time = date('h:i a', strtotime($value['product_prices_datetime']));



            $date_time = $date . ' ' . $time;

            $result['data'][$key] = array(

              $counter++,

              $date_time,

              $value['first_name']. ' '. $value['last_name'],

              $category_name,

              $value['product_name'],

              $unit_name,

              $value['price']

            );



          } // /foreach



          $data['page_title'] = "Report - Selected Vendor Items Rate List History";

          $data['heading'] = "Selected Vendor Items Rate List History";



          $data['result'] = $result;

          $user_id = $this->session->userdata('id');

          $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

          $data['user_permission'] = unserialize($group_data['permission']);



          $this->load->view('templates/header', $data);

          $this->load->view('templates/header_menu');

          $this->load->view('templates/side_menubar');



          $this->load->view('reports/items_rate');

        }

        else{



          date_default_timezone_set("Asia/Karachi");

          $to = date('d-m-Y');

          $from = date('d-m-Y');



          $result = array('data' => array());



          $data = $this->Model_products->getGroupedCurrentVendorProductsRate();

          $counter = 1;

          foreach ($data as $key => $value) {

            $category_name = '';

            if($value['category_id'])

            {

              $category_data = $this->Model_category->getAllCategoryData($value['category_id']);

              $category_name = $category_data['name'];

            }

            else

            {

              $category_name = 'Nill';

            }

            $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];



            $date = date('d-m-Y', strtotime($value['max_date_time']));

            $time = date('h:i a', strtotime($value['max_date_time']));



            $date_time = $date . ' ' . $time;

            // currently working rates

            $result['data'][$key] = array(

              $counter++,

              $date_time,

              $value['first_name']. ' '. $value['last_name'],

              $category_name,

              $value['product_name'],

              $unit_name,

              $value['price']

            );

          } // /foreach

          $data['page_title'] = "Report - Current Vendor Items Rate List";

          $data['heading'] = "Current Vendor Items Rate List";

          $data['result'] = $result;

          $user_id = $this->session->userdata('id');

          $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

          $data['user_permission'] = unserialize($group_data['permission']);

          $this->load->view('templates/header', $data);

          $this->load->view('templates/header_menu');

          $this->load->view('templates/side_menubar');



          $this->load->view('reports/items_rate');

        }

      }

      // end function

    }


  public function print_items_rate()

  {

    if(!in_array('printVendorItemsRate', $this->permission))

    {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else

    {

      if(isset($_GET['range']))

      {

        $range = $_GET['range'];

        $ex = explode(' - ', $range);

        $from = date('d-m-Y', strtotime($ex[0]));

        $to = date('d-m-Y', strtotime($ex[1]));



        $result = array('data' => array());



        $data = $this->Model_products->getGroupedProductPricesData();

        $counter = 1;

        foreach ($data as $key => $value) {



          $category_name = '';

          if($value['category_id'])

          {

            $category_data = $this->Model_category->getCategoryData($value['category_id']);

            $category_name = $category_data['name'];



          }

          else

          {

            $category_name = 'Nill';

          }

          $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];



          $date = date('d-m-Y', strtotime($value['product_prices_datetime']));

          $time = date('h:i a', strtotime($value['product_prices_datetime']));



          $date_time = $date . ' ' . $time;

          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))

          {

            $result['data'][$key] = array(

              $counter++,

              $date_time,

              $value['first_name']. ' '. $value['last_name'],

              $category_name,

              $value['product_name'],

              $unit_name,

              $value['price']

            );

          }

        } // /foreach

      }

      else if(!empty($_GET['selected_vendor']))

      {

        $result = array('data' => array());



        $data = $this->Model_products->getGroupedVendorProductPricesData($_GET['selected_vendor']);

        $counter = 1;



        foreach ($data as $key => $value)

        {

          $category_name = '';

          if($value['category_id'])

          {

            $category_data = $this->Model_category->getCategoryData($value['category_id']);

            $category_name = $category_data['name'];



          }

          else

          {

            $category_name = 'Nill';

          }

          $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];



          $date = date('d-m-Y', strtotime($value['product_prices_datetime']));

          $time = date('h:i a', strtotime($value['product_prices_datetime']));



          $date_time = $date . ' ' . $time;

          $result['data'][$key] = array(

            $counter++,

            $date_time,

            $value['first_name']. ' '. $value['last_name'],

            $category_name,

            $value['product_name'],

            $unit_name,

            $value['price']

          );

        } // /foreach

      }

      else

      {

        date_default_timezone_set("Asia/Karachi");

        $to = date('d-m-Y');

        $from = date('d-m-Y');



        $result = array('data' => array());



        $data = $this->Model_products->getGroupedCurrentVendorProductsRate();

        $counter = 1;

        foreach ($data as $key => $value)

        {

          $category_name = '';

          if($value['category_id'])

          {

            $category_data = $this->Model_category->getAllCategoryData($value['category_id']);

          }

          else

          {

            $category_name = 'Nill';

          }

          $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];



          $date = date('d-m-Y', strtotime($value['max_date_time']));

          $time = date('h:i a', strtotime($value['max_date_time']));



          $date_time = $date . ' ' . $time;

            // currently working rates

          $result['data'][$key] = array(

            $counter++,

            $date_time,

            $value['first_name']. ' '. $value['last_name'],

            $category_name,

            $value['product_name'],

            $unit_name,

            $value['price']

          );

        }//foreach

      }

      date_default_timezone_set("Asia/Karachi");

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

                .table > tbody > tr > .no-line {

                  border-top: none;

                }

                .table > thead > tr > .no-line {

                  border-bottom: none;

                }

                .table > tbody > tr > .thick-line {

                  border-top: 2px solid;

                }

              </style>

              <title>TBM - Vendor Items Rate List</title>

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

                          <table class="table table-condensed table-bordered">

                            <thead>

                              <tr>

                                <th width="10%"><strong>#</strong></th>

                                <th width="15%"><strong>DateTime</strong></th>

                                <th width="15%"><strong>Vendor Name</strong></th>

                                <th width="15%"><strong>Category Name</strong></th>

                                <th width="15%"><strong>Item Name</strong></th>

                                <th width="15%"><strong>Unit</strong></th>

                                <th width="15%"><strong>Rate</strong></th>

                              </tr>

                            </thead>

                            <tbody>';

                                foreach ($result['data'] as $key => $value) {

                                  $counter = 0;

                                  $html .= '<tr>

                                    <td>'.$value[$counter++].'</td>

                                    <td>'.$value[$counter++].'</td>

                                    <td>'.$value[$counter++].'</td>

                                    <td>'.$value[$counter++].'</td>

                                    <td>'.$value[$counter++].'</td>

                                    <td>'.$value[$counter++].'</td>

                                    <td>'.$value[$counter++].'</td>

                                  </tr>';

                                }

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

  public function sale_items_rate()

  {

    if(!in_array('viewSaleItemsRate', $this->permission)) {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else

    {

      if(isset($_GET['range']))

      {

        $range = $_GET['range'];

        $ex = explode(' - ', $range);

        $from = date('d-m-Y', strtotime($ex[0]));

        $to = date('d-m-Y', strtotime($ex[1]));



        $result = array('data' => array());



        $data = $this->Model_products->getSalePricesData();

        $counter = 1;

        foreach ($data as $key => $value) {

          $category_name = '';

          if($value['category_id'])

          {

            $category_data = $this->Model_category->getCategoryData($value['category_id']);

            $category_name = $category_data['name'];



          }

          else

          {

            $category_name = 'Nill';

          }

          $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];



          $date = date('d-m-Y', strtotime($value['sale_prices_datetime']));

          $time = date('h:i a', strtotime($value['sale_prices_datetime']));



          $date_time = $date . ' ' . $time;

          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))

          {

            $result['data'][$key] = array(

              $counter++,

              $date_time,

              $category_name,

              $value['product_name'],

              $unit_name,

              $value['price']

            );

          }

        } // /foreach

        $data['page_title'] = "Report - Sale Items Rate History";

        $data['heading'] = "Sale Items Rate History";



        $data['result'] = $result;

        $user_id = $this->session->userdata('id');

        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

        $data['user_permission'] = unserialize($group_data['permission']);



        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');



        $this->load->view('reports/sale_items_rate');

      }

      else

      {

        date_default_timezone_set("Asia/Karachi");

        $to = date('d-m-Y');

        $from = date('d-m-Y');

        $result = array('data' => array());



        $data = $this->Model_products->getCurrentSalePrices();

        $counter = 1;

        foreach ($data as $key => $value)

        {

          $category_name = '';

          if($value['category_id'])

          {

            $category_data = $this->Model_category->getCategoryData($value['category_id']);

            $category_name = $category_data['name'];

          }

          else

          {

            $category_name = 'Nill';

          }

          $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];



          $date = date('d-m-Y', strtotime($value['max_date_time']));

          $time = date('h:i a', strtotime($value['max_date_time']));



          $date_time = $date . ' ' . $time;

            // currently working rates

          $result['data'][$key] = array(

            $counter++,

            $date_time,

            $category_name,

            $value['product_name'],

            $unit_name,

            $value['price']

          );

        } // /foreach

        $data['page_title'] = "Report - Current Sale Items Rate";

        $data['heading'] = "Current Sale Items Rate";

        $data['result'] = $result;

        $user_id = $this->session->userdata('id');

        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

        $data['user_permission'] = unserialize($group_data['permission']);

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');



        $this->load->view('reports/sale_items_rate');

      }

    }

  }// end function



  public function print_sale_items_rate()

  {

    if(!in_array('printSaleItemsRate', $this->permission))

    {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else

    {

      if(isset($_GET['range']))

      {

        $range = $_GET['range'];

        $ex = explode(' - ', $range);

        $from = date('d-m-Y', strtotime($ex[0]));

        $to = date('d-m-Y', strtotime($ex[1]));



        $result = array('data' => array());



        $data = $this->Model_products->getSalePricesData();

        $counter = 1;

        foreach ($data as $key => $value) {

          $category_name = '';

          if($value['category_id'])

          {

            $category_data = $this->Model_category->getCategoryData($value['category_id']);

            $category_name = $category_data['name'];



          }

          else

          {

            $category_name = 'Nill';

          }

          $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];



          $date = date('d-m-Y', strtotime($value['sale_prices_datetime']));

          $time = date('h:i a', strtotime($value['sale_prices_datetime']));



          $date_time = $date . ' ' . $time;

          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))

          {

            $result['data'][$key] = array(

              $counter++,

              $date_time,

              $category_name,

              $value['product_name'],

              $unit_name,

              $value['price']

            );

          }

        } // /foreach

      }

      else

      {

        date_default_timezone_set("Asia/Karachi");

        $to = date('d-m-Y');

        $from = date('d-m-Y');

        $result = array('data' => array());



        $data = $this->Model_products->getCurrentSalePrices();

        $counter = 1;

        foreach ($data as $key => $value)

        {

          $category_name = '';

          if($value['category_id'])

          {

            $category_data = $this->Model_category->getCategoryData($value['category_id']);

            $category_name = $category_data['name'];

          }

          else

          {

            $category_name = 'Nill';

          }

          $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];



          $date = date('d-m-Y', strtotime($value['max_date_time']));

          $time = date('h:i a', strtotime($value['max_date_time']));



          $date_time = $date . ' ' . $time;

            // currently working rates

          $result['data'][$key] = array(

            $counter++,

            $date_time,

            $category_name,

            $value['product_name'],

            $unit_name,

            $value['price']

          );

        } // /foreach

      }

      date_default_timezone_set("Asia/Karachi");

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

                .table > tbody > tr > .no-line {

                  border-top: none;

                }

                .table > thead > tr > .no-line {

                  border-bottom: none;

                }

                .table > tbody > tr > .thick-line {

                  border-top: 2px solid;

                }

              </style>

              <title>TBM - Sale Items Rate</title>

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

                          <table class="table table-condensed table-bordered">

                            <thead>

                              <tr>

                                <th><strong>#</strong></th>

                                <th><strong>DateTime</strong></th>

                                <th><strong>Category Name</strong></th>

                                <th><strong>Item Name</strong></th>

                                <th><strong>Unit</strong></th>

                                <th><strong>Rate</strong></th>

                              </tr>

                            </thead>

                            <tbody>';

                                foreach ($result['data'] as $key => $value) {

                                  $counter = 0;

                                  $html .= '<tr>

                                    <td>'.$value[$counter++].'</td>

                                    <td>'.$value[$counter++].'</td>

                                    <td>'.$value[$counter++].'</td>

                                    <td>'.$value[$counter++].'</td>

                                    <td>'.$value[$counter++].'</td>

                                    <td>'.$value[$counter++].'</td>

                                  </tr>';

                                }

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



  public function purchased_orders_details()

  {

    if(!in_array('viewPurchasingDetails', $this->permission)) {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else

    {

      if(isset($_GET['range']))

      {

        $range = $_GET['range'];

        $ex = explode(' - ', $range);

        $from = date('d-m-Y', strtotime($ex[0]));

        $to = date('d-m-Y', strtotime($ex[1]));



        $result = array('data' => array());

        $data = $this->Model_products->getPurchaseOrdersData();

        $purchase_order_ids = array();

        foreach ($data as $key => $value)

        {

          $date = date('d-m-Y', strtotime($value['datetime_created']));

          $time = date('h:i a', strtotime($value['datetime_created']));



          $date_time = $date . ' ' . $time;

          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))

          {

            if (!in_array($value['id'], $purchase_order_ids)) {

              array_push($purchase_order_ids, $value['id']);

            }

          }

        } // /foreach



        $data['page_title'] = "Report - Purchased Orders Detail History";

        $data['heading'] = "Purchased Orders Detail History";



        $data['result'] = $purchase_order_ids;

        $user_id = $this->session->userdata('id');

        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

        $data['user_permission'] = unserialize($group_data['permission']);



        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');



        $this->load->view('reports/purchased_orders_detail');

      }

      else

      {

        date_default_timezone_set("Asia/Karachi");

        $to = date('d-m-Y');

        $from = date('d-m-Y');



        $result = array('data' => array());



        $data = $this->Model_products->getPurchaseOrdersData();

        $purchase_order_ids = array();

        foreach ($data as $key => $value)

        {

          $date = date('d-m-Y', strtotime($value['datetime_created']));

          $time = date('h:i a', strtotime($value['datetime_created']));



          $date_time = $date . ' ' . $time;



          if($date == $from && $date == $to)

          {

            if (!in_array($value['id'], $purchase_order_ids)) {

              array_push($purchase_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['page_title'] = "Report - Daily Purchased Orders Detail";

        $data['heading'] = "Daily Purchased Orders Detail";



        $data['result'] = $purchase_order_ids;

        $user_id = $this->session->userdata('id');

        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

        $data['user_permission'] = unserialize($group_data['permission']);



        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');



        $this->load->view('reports/purchased_orders_detail');

      }

    }

    // end function

  }



  public function print_purchased_orders_details()

  {

    if(!in_array('printPurchasingDetails', $this->permission))

    {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else

    {

      if(isset($_GET['range']))

      {

        $range = $_GET['range'];

        $ex = explode(' - ', $range);

        $from = date('d-m-Y', strtotime($ex[0]));

        $to = date('d-m-Y', strtotime($ex[1]));



        $result = array('data' => array());



        $data = $this->Model_products->getPurchaseOrdersData();

        $purchase_order_ids = array();

        foreach ($data as $key => $value)

        {

          $date = date('d-m-Y', strtotime($value['datetime_created']));

          $time = date('h:i a', strtotime($value['datetime_created']));



          $date_time = $date . ' ' . $time;

          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))

          {

            if (!in_array($value['id'], $purchase_order_ids)) {

              array_push($purchase_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['result'] = $purchase_order_ids;

      }

      else

      {

        date_default_timezone_set("Asia/Karachi");

        $to = date('d-m-Y');

        $from = date('d-m-Y');



        $result = array('data' => array());



        $data = $this->Model_products->getPurchaseOrdersData();

        $purchase_order_ids = array();

        foreach ($data as $key => $value)

        {

          $date = date('d-m-Y', strtotime($value['datetime_created']));

          $time = date('h:i a', strtotime($value['datetime_created']));



          $date_time = $date . ' ' . $time;



          if($date == $from && $date == $to)

          {

            if (!in_array($value['id'], $purchase_order_ids)) {

              array_push($purchase_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['result'] = $purchase_order_ids;

      }

      date_default_timezone_set("Asia/Karachi");

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

                      <h3>TBM Engineering</h3>

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



                              $firstIteration = true;

                              $opening_balance = 0;

                              $closing_balance = 0;

                              $counter = 1;

                              // Descding Order

                              $data['result'] = array_reverse($data['result']);

                              foreach($data['result'] as $key => $value):

                                $purchase_order_data = $this->Model_products->getPurchaseOrdersData($value);

                                $purchase_items_data = $this->Model_products->getPurchaseItemsData($value);

                                $purchase_return_data = $this->Model_products->fetchPurchaseReturnsData($value);

                                $vendor_ob_payment_data = $this->Model_products->fetchVendorOBPaymentData($value);

                                $loan_deductions = $this->Model_loan->getLoanDeductions($value);

                                $vendor_id = $purchase_items_data[0]['vendor_id'];

                                $vendor_data = $this->Model_products->getSupplierData($vendor_id);

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

                                          <th>Returns</th>

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

                                            <td>'. $v['qty'] .'</td>

                                            <td>'. $return_qty .'</td>

                                            <td>'. $v['product_price'] .'</td>

                                            <td>'. ( ($v['qty'] * $v['product_price']) - ($total_retun_amount) ) .'</td>

                                          </tr>';



                                        endforeach;

                          $html .='

                                      </tbody>

                                    </table>

                                    <p>

                                      <span style="background-color: #ffffff">

                                        <strong>Total Qunatity Delivered: '. $total_qty_delivered .'</strong>

                                      </span>

                                    </p>

                                    <p>

                                      <span style="background-color: #ffffff">

                                        <strong>Total Products Amount: '. $total_products_amount .'</strong>

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

                                                <td>'. $paid_amount .'</td>

                                                <td style="white-space:pre-wrap; word-wrap:break-word">'.$payment_note.'</td>

                                                <td>'. $payment_date .'</td>

                                              </tr>';

                                            endforeach;

                          $html .='

                                          </tbody>

                                        </table>

                                        <span style="background-color: #ffffff"><strong>Total Amount Paid: '. $total_amount_paid .'</strong></span>';

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

                                        <p><strong>Products Total:</strong></p>

                                      </div>

                                      <div class="col-xs-6 text-right">

                                        <p><strong>'. floatval($total_products_amount) .'</strong></p>

                                      </div>

                                    </div>



                                    <div class="row">

                                      <div class="col-xs-6">

                                        <p><strong>Loan Deduction:</strong></p>

                                      </div>

                                      <div class="col-xs-6 text-right">

                                        <p><strong>'.floatval( $loan_deduction ) .'</strong></p>

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

                                        <p><strong>Total Paid:</strong></p>

                                      </div>

                                      <div class="col-xs-6 text-right">

                                        <p><strong>'. floatval($total_amount_paid) .'</strong></p>

                                      </div>

                                    </div>

                                    <div class="row">

                                      <div class="col-xs-6">

                                        <p><strong>Remaining:</strong></p>

                                      </div>

                                      <div class="col-xs-6 text-right">

                                        <p><strong>'. floatval(floatval(($purchase_order_data['net_amount'] - $purchase_return_amount)) - floatval($total_amount_paid)) .'</strong></p>

                                      </div>

                                    </div>



                            ';

                            $html .='

                                  <td>'. $date_time .'</td>



                                </tr>';

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



  public function sale_orders_detail()

  {

    if(!in_array('viewSaleDetailsNonEmp', $this->permission)) {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else

    {

      if(isset($_GET['range']))

      {

        $range = $_GET['range'];

        $ex = explode(' - ', $range);

        $from = date('d-m-Y', strtotime($ex[0]));

        $to = date('d-m-Y', strtotime($ex[1]));



        $result = array('data' => array());



        $data = $this->Model_products->getSaleOrdersData();

        $sale_order_ids = array();

        foreach ($data as $key => $value)

        {

          $date = date('d-m-Y', strtotime($value['date_time']));

          $time = date('h:i a', strtotime($value['date_time']));



          $date_time = $date . ' ' . $time;

          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))

          {

            if (!in_array($value['id'], $sale_order_ids)) {

              array_push($sale_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['page_title'] = "Report - Sale Orders Detail History - Non-Employees";

        $data['heading'] = "Sale Orders Detail History - Non-Employees";

        $data['result'] = $sale_order_ids;

        $user_id = $this->session->userdata('id');

        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

        $data['user_permission'] = unserialize($group_data['permission']);



        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');



        $this->load->view('reports/sale_orders_detail');

      }

      else{

        date_default_timezone_set("Asia/Karachi");

        $to = date('d-m-Y');

        $from = date('d-m-Y');



        $result = array('data' => array());

        $sale_order_ids = array();

        $data = $this->Model_products->getSaleOrdersData();

        foreach ($data as $key => $value) {

          $date = date('d-m-Y', strtotime($value['date_time']));

          $time = date('h:i a', strtotime($value['date_time']));



          $date_time = $date . ' ' . $time;



          if($date == $from && $date == $to)

          {

            if (!in_array($value['id'], $sale_order_ids)) {

              array_push($sale_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['page_title'] = "Report - Daily Sale Orders Detail - Non-Employees";

        $data['heading'] = "Daily Sale Orders Detail - Non-Employees";



        $data['result'] = $sale_order_ids;

        $user_id = $this->session->userdata('id');

        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

        $data['user_permission'] = unserialize($group_data['permission']);



        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');



        $this->load->view('reports/sale_orders_detail');

      }

    }

    // end function

  }



  public function print_sale_orders_detail()

  {

    if(!in_array('printSaleDetailsNonEmp', $this->permission))

    {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else

    {

      if(isset($_GET['range']))

      {

        $range = $_GET['range'];

        $ex = explode(' - ', $range);

        $from = date('d-m-Y', strtotime($ex[0]));

        $to = date('d-m-Y', strtotime($ex[1]));



        $result = array('data' => array());



        $data = $this->Model_products->getSaleOrdersData();

        $sale_order_ids = array();

        foreach ($data as $key => $value)

        {

          $date = date('d-m-Y', strtotime($value['date_time']));

          $time = date('h:i a', strtotime($value['date_time']));



          $date_time = $date . ' ' . $time;

          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))

          {

            if (!in_array($value['id'], $sale_order_ids)) {

              array_push($sale_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['result'] = $sale_order_ids;

      }

      else

      {

        date_default_timezone_set("Asia/Karachi");

        $to = date('d-m-Y');

        $from = date('d-m-Y');



        $result = array('data' => array());

        $sale_order_ids = array();

        $data = $this->Model_products->getSaleOrdersData();

        foreach ($data as $key => $value) {

          $date = date('d-m-Y', strtotime($value['date_time']));

          $time = date('h:i a', strtotime($value['date_time']));



          $date_time = $date . ' ' . $time;



          if($date == $from && $date == $to)

          {

            if (!in_array($value['id'], $sale_order_ids)) {

              array_push($sale_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['result'] = $sale_order_ids;

      }



      date_default_timezone_set("Asia/Karachi");

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

                .table > tbody > tr > .no-line {

                  border-top: none;

                }

                .table > thead > tr > .no-line {

                  border-bottom: none;

                }

                .table > tbody > tr > .thick-line {

                  border-top: 2px solid;

                }

              </style>

              <title>TBM - Sale Orders Detail - Non-Employees</title>

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

                          <table class="table table-condensed table-bordered">

                            <thead>

                              <tr>

                                <th><strong>Bill no</strong></th>

                                <th><strong>Order</strong></th>

                                <th><strong>Order Items</strong></th>

                              </tr>

                            </thead>

                            <tbody>';



                                $sum_net_amounts = 0;

                                $sum_paid_amounts = 0;

                                $sum_remaining_amounts = 0;



                                foreach ($data['result'] as $key => $value)

                                {

                                  $count_total_item = $this->Model_products->countSaleOrderItem($value);

                                  $sale_order_data = $this->Model_products->getSaleOrdersData($value);

                                  $sale_items_data = $this->Model_products->getSaleItemsData($value);

                                  $payment_method = '';

                                  if($sale_order_data['payment_method'] == 1){

                                    $payment_method = '<span class="label label-success">Cash / Paid</span>';

                                  }

                                  else if($sale_order_data['payment_method'] == 2){

                                    $payment_method = '<span class="label label-warning">Check / Paid</span>';

                                  }

                                  $stock_type = '';

                                  if ($sale_order_data['stock_type'] == 1) {

                                    $stock_type = 'Factory Stock';

                                  }

                                  else if ($sale_order_data['stock_type'] == 2) {

                                    $stock_type = 'Office Stock';

                                  }



                                  $date = date('d-m-Y', strtotime($sale_order_data['date_time']));

                                  $time = date('h:i a', strtotime($sale_order_data['date_time']));



                                  $date_time = $date . ' ' . $time;



                                  $html .= '<tr>

                                    <td>'.$sale_order_data['bill_no'].'</td>

                                    <td>

                                      <table class="table table-bordered table-striped example2">

                                        <thead>

                                          <tr>

                                            <th>Date Time</th>

                                            <th>Customer</th>

                                            <th>Gross Amount</th>

                                            <th>Net Amount</th>

                                            <th>Recieved</th>

                                            <th>Remaining</th>

                                          </tr>

                                        </thead>

                                        <tbody>

                                          <tr>

                                            <td>'.$date_time.'</td>

                                            <td>'.$sale_order_data['customer_name'].'</td>

                                            <td>'.$sale_order_data['gross_amount'].'</td>

                                            <td>'.$sale_order_data['net_amount'].'</td>

                                            <td>'.$sale_order_data['paid_amount'].'</td>

                                            <td>'.($sale_order_data['net_amount'] - $sale_order_data['paid_amount']).'</td>

                                          </tr>

                                        </tbody>



                                      </table>

                                      <p>

                                        <span style="background-color: #ffffff">

                                          <strong>Discount: '.$sale_order_data['discount'].'</strong>

                                        </span>

                                      </p>

                                      <p>

                                        <span style="background-color: #ffffff">

                                          <strong>Freight: '.$sale_order_data['loading_or_affair'].'</strong>

                                        </span>

                                      </p>

                                    </td>

                                    <td>

                                      <table class="table table-bordered table-striped example3">

                                        <thead>

                                          <tr>

                                            <th>#</th>

                                            <th>Category</th>

                                            <th>Item</th>

                                            <th>Unit</th>

                                            <th>Qty</th>

                                            <th>Price</th>

                                          </tr>

                                        </thead>

                                        <tbody>';



                                          $count = 1;

                                          foreach($sale_items_data as $key => $value):

                                            $product_data = $this->Model_products->getAllProductData($value['product_id']);

                                            $product_name = $product_data['name'];

                                            $category_name = '';

                                            if($value['category_id'])

                                            {

                                              $category_name = $this->Model_category->getAllCategoryData($value['category_id'])['name'];

                                            }

                                            else

                                            {

                                              $category_name = 'Nill';

                                            }

                                            $unit_value = 1;

                                            $unit_data_values = $this->Model_products->fetchUnitValueData($value['unit_id']);

                                            if(!empty($unit_data_values)){

                                              $unit_value = $unit_data_values['unit_value'];

                                            }

                                            $unit_name = "Not Mentioned";

                                            if($value['unit_id'] != 0){

                                              $unit_data = $this->Model_products->getAllUnitsData($value['unit_id']);

                                              $unit_name = $unit_data['unit_name'];

                                            }

                                            $html .= '

                                            <tr>

                                              <td>'.$count++.'</td>

                                              <td>'.$category_name.'</td>

                                              <td>'.$product_name.'</td>

                                              <td>'.$unit_name.'</td>



                                              <td>'.$value['qty'].'</td>



                                              <td>'.($value['product_price'] / $unit_value).'</td>

                                            </tr>';

                                          endforeach;

                                          $html .= '

                                        </tbody>

                                      </table>

                                    </td>

                                  </tr>';

                                  $sum_net_amounts += $sale_order_data['net_amount'];

                                  $sum_paid_amounts += $sale_order_data['paid_amount'];

                                  $sum_remaining_amounts += ($sale_order_data['net_amount'] - $sale_order_data['paid_amount']);

                                }//endforeach

                              $html .='

                            </tbody>

                          </table>

                          <div class="row">

                            <div class="col-md-4">

                              <b>Total Net Amount: </b>'.$sum_net_amounts.'

                            </div>

                            <div class="col-md-3">

                              <b>Total Received Amount: </b>'.$sum_paid_amounts.'

                            </div>

                            <div class="col-md-5">

                              <b>Total Remaining Amount: </b>'.$sum_remaining_amounts.'

                            </div>

                          </div>

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



  public function company_sale_orders_detail()

  {

    if(!in_array('viewSaleDetailsEmp', $this->permission)) {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else

    {

      if(isset($_GET['range']))

      {

        $range = $_GET['range'];

        $ex = explode(' - ', $range);

        $from = date('d-m-Y', strtotime($ex[0]));

        $to = date('d-m-Y', strtotime($ex[1]));



        $result = array('data' => array());



        $data = $this->Model_products->getCompanySaleOrdersData();

        $sale_order_ids = array();

        foreach ($data as $key => $value)

        {

          $date = date('d-m-Y', strtotime($value['date_time']));

          $time = date('h:i a', strtotime($value['date_time']));



          $date_time = $date . ' ' . $time;

          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))

          {

            if (!in_array($value['id'], $sale_order_ids)) {

              array_push($sale_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['page_title'] = "Report - Sale Orders Detail - Employees";

        $data['heading'] = "Sale Orders Detail History - Employees";

        $data['result'] = $sale_order_ids;

        $user_id = $this->session->userdata('id');

        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

        $data['user_permission'] = unserialize($group_data['permission']);



        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');



        $this->load->view('reports/company_sale_orders_detail');

      }

      else{

        date_default_timezone_set("Asia/Karachi");

        $to = date('d-m-Y');

        $from = date('d-m-Y');



        $result = array('data' => array());

        $sale_order_ids = array();

        $data = $this->Model_products->getCompanySaleOrdersData();

        foreach ($data as $key => $value) {

          $date = date('d-m-Y', strtotime($value['date_time']));

          $time = date('h:i a', strtotime($value['date_time']));



          $date_time = $date . ' ' . $time;



          if($date == $from && $date == $to)

          {

            if (!in_array($value['id'], $sale_order_ids)) {

              array_push($sale_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['page_title'] = "Report - Sale Orders Detail - Employees";

        $data['heading'] = "Daily Sale Orders Detail - Employees";



        $data['result'] = $sale_order_ids;

        $user_id = $this->session->userdata('id');

        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

        $data['user_permission'] = unserialize($group_data['permission']);



        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');



        $this->load->view('reports/company_sale_orders_detail');

      }

    }

    // end function

  }



  public function print_company_sale_orders_detail()

  {

    if(!in_array('printSaleDetailsEmp', $this->permission))

    {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else

    {

      if(isset($_GET['range']))

      {

        $range = $_GET['range'];

        $ex = explode(' - ', $range);

        $from = date('d-m-Y', strtotime($ex[0]));

        $to = date('d-m-Y', strtotime($ex[1]));



        $result = array('data' => array());



        $data = $this->Model_products->getCompanySaleOrdersData();

        $sale_order_ids = array();

        foreach ($data as $key => $value)

        {

          $date = date('d-m-Y', strtotime($value['date_time']));

          $time = date('h:i a', strtotime($value['date_time']));



          $date_time = $date . ' ' . $time;

          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))

          {

            if (!in_array($value['id'], $sale_order_ids)) {

              array_push($sale_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['result'] = $sale_order_ids;

      }

      else

      {

        date_default_timezone_set("Asia/Karachi");

        $to = date('d-m-Y');

        $from = date('d-m-Y');



        $result = array('data' => array());

        $sale_order_ids = array();

        $data = $this->Model_products->getCompanySaleOrdersData();

        foreach ($data as $key => $value) {

          $date = date('d-m-Y', strtotime($value['date_time']));

          $time = date('h:i a', strtotime($value['date_time']));



          $date_time = $date . ' ' . $time;



          if($date == $from && $date == $to)

          {

            if (!in_array($value['id'], $sale_order_ids)) {

              array_push($sale_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['result'] = $sale_order_ids;

      }



      date_default_timezone_set("Asia/Karachi");

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

                .table > tbody > tr > .no-line {

                  border-top: none;

                }

                .table > thead > tr > .no-line {

                  border-bottom: none;

                }

                .table > tbody > tr > .thick-line {

                  border-top: 2px solid;

                }

              </style>

              <title>TBM - Sale Orders Detail - Employees</title>

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

                          <table class="table table-condensed table-bordered">

                            <thead>

                              <tr>

                                <th width="3%">#</th>

                                <th width="57%">Order</th>

                                <th width="40%">Order Items</th>

                              </tr>

                            </thead>

                            <tbody>';

                                $counter = 1;

                                foreach ($data['result'] as $key => $value)

                                {

                                  $sale_order_data = $this->Model_products->getCompanySaleOrdersData($value);

                                  $sale_items_data = $this->Model_products->getCompanySaleItemsData($value);

                                  $customer_data = $this->Model_Customers->getCustomerData($sale_order_data['customer_id']);

                                  $department_data = $this->Model_department->getDepartmentData($sale_order_data['department_id']);

                                  $count_total_item = $this->Model_products->countCompanySaleOrderItem($sale_order_data['id']);

                                  $stock_type = '';

                                  if ($sale_order_data['stock_type'] == 1) {

                                    $stock_type = 'Factory Stock';

                                  }

                                  else if ($sale_order_data['stock_type'] == 2) {

                                    $stock_type = 'Office Stock';

                                  }



                                  $date = date('d-m-Y', strtotime($sale_order_data['date_time']));

                                  $time = date('h:i a', strtotime($sale_order_data['date_time']));



                                  $date_time = $date . ' ' . $time;



                                  $html .= '<tr>

                                    <td>'.$counter++.'</td>

                                    <td>

                                      <table class="table table-bordered table-striped example2">

                                        <thead>

                                          <tr>

                                            <th>Bill_no</th>

                                            <th>DateTime</th>

                                            <th>Customer</th>

                                            <th>Department</th>

                                            <th>Products</th>

                                            <th>Stock</th>

                                          </tr>

                                        </thead>

                                        <tbody>

                                          <tr>

                                            <td>'.$sale_order_data['bill_no'].'</td>

                                            <td>'.$date_time.'</td>

                                            <td>'.$customer_data['full_name'].'</td>

                                            <td>'.$department_data['department_name'].'</td>

                                            <td>'.$count_total_item.'</td>

                                            <td>'.$stock_type.'</td>

                                          </tr>

                                        </tbody>



                                      </table>

                                    </td>

                                    <td>

                                      <table class="table table-bordered table-striped example3">

                                        <thead>

                                          <tr>

                                            <th>#</th>

                                            <th>Category</th>

                                            <th>Item</th>

                                            <th>Unit</th>

                                            <th>Qty</th>

                                          </tr>

                                        </thead>

                                        <tbody>';



                                          $count = 1;

                                          foreach($sale_items_data as $key => $value):

                                            $product_data = $this->Model_products->getAllProductData($value['product_id']);

                                            $product_name = $product_data['name'];

                                            $category_name = '';

                                            if($value['category_id'])

                                            {

                                              $category_name = $this->Model_category->getAllCategoryData($value['category_id'])['name'];

                                            }

                                            else

                                            {

                                              $category_name = 'Nill';

                                            }

                                            $unit_value = 1;

                                            $unit_data_values = $this->Model_products->fetchUnitValueData($value['unit_id']);

                                            if(!empty($unit_data_values)){

                                              $unit_value = $unit_data_values['unit_value'];

                                            }

                                            $unit_name = "Not Mentioned";

                                            if($value['unit_id'] != 0){

                                              $unit_data = $this->Model_products->getAllUnitsData($value['unit_id']);

                                              $unit_name = $unit_data['unit_name'];

                                            }

                                            $html .= '

                                            <tr>

                                              <td>'.$count++.'</td>

                                              <td>'.$category_name.'</td>

                                              <td>'.$product_name.'</td>

                                              <td>'.$unit_name.'</td>

                                              <td>'.$value['qty'].'</td>

                                            </tr>';

                                          endforeach;

                                          $html .= '

                                        </tbody>

                                      </table>

                                    </td>

                                  </tr>';

                                }//endforeach

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



  // all vendors remaining balance

  public function vendors_remaining_balance()

  {

    if(!in_array('viewVendorRemainingBalance', $this->permission)) {

      $data['page_title'] = "No Permission";

      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else

    {

      $result = array();



      $data = $this->Model_supplier->getSortedSupplierData();

      $counter = 1;

      foreach ($data as $key => $value)

      {

        $remaining_balance = $value['balance'];

        $result[$key] = array(

          $counter++,

          $value['first_name']. ' '. $value['last_name'],

          $remaining_balance

        );

      } // /foreach



      // Sort Result

      $empty_result = array();

      $not_empty_result = array();

      foreach ($result as $key => $value) {

        if(!empty($value[2])){

          array_push($not_empty_result, $value);

        }

        else{

          array_push($empty_result, $value);

        }

      }

      $new_result = array_merge($not_empty_result, $empty_result);

      $data['page_title'] = "Report - Vendors Remaining Balance";

      $data['heading'] = "Vendors Remaining Balance";



      $data['result'] = $new_result;

      $user_id = $this->session->userdata('id');

      $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

      $data['user_permission'] = unserialize($group_data['permission']);



      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('reports/vendors_remaining_balance');

    }

  }// end function



  public function print_vendors_remaining_balance()
  {

    if(!in_array('printVendorRemainingBalance', $this->permission))

    {
      $data['page_title'] = "No Permission";
      $this->load->view('templates/header', $data);

      $this->load->view('templates/header_menu');

      $this->load->view('templates/side_menubar');

      $this->load->view('errors/forbidden_access');

    }

    else

    {



      $result = array();



      $data = $this->Model_supplier->getSortedSupplierData();

      $counter = 1;

      foreach ($data as $key => $value)

      {

        $remaining_balance = $value['balance'];

        $result[$key] = array(

          $counter++,

          $value['first_name']. ' '. $value['last_name'],

          $remaining_balance

        );

      } // /foreach



      // Sort Result

      $empty_result = array();

      $not_empty_result = array();

      foreach ($result as $key => $value) {

        if(!empty($value[2])){

          array_push($not_empty_result, $value);

        }

        else{

          array_push($empty_result, $value);

        }

      }

      $new_result = array_merge($not_empty_result, $empty_result);

      $result = $new_result;



      date_default_timezone_set("Asia/Karachi");

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

                .table > tbody > tr > .no-line {

                  border-top: none;

                }

                .table > thead > tr > .no-line {

                  border-bottom: none;

                }

                .table > tbody > tr > .thick-line {

                  border-top: 2px solid;

                }

              </style>

              <title>TBM - Sale Items Rate</title>

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

                          <table class="table table-condensed table-bordered">

                            <thead>

                              <tr>

                                <th width="10%"><strong>Sr. </strong></th>

                                <th width="25%"><strong>Vendor Name</strong></th>

                                <th width="25%"><strong>Credit</strong></th>

                                <th><strong>Debit</strong></th>

                              </tr>

                            </thead>

                            <tbody>';



                                $total_credit = 0;$total_debit = 0;

                                foreach ($result as $key => $value):

                                  $html .='<tr><td>'.$value[0].'</td><td>'.$value[1].'</td>';

                                  if($value[2] > 0)

                                  {

                                    $total_credit += $value[2];

                                    $html .= '<td>'.$value[2].'</td><td> - </td>';

                                  }

                                  else if($value[2] < 0)

                                  {

                                    $total_debit += abs($value[2]);

                                    $html .= '<td> - </td><td>'.abs($value[2]).'</td>';

                                  }

                                  else

                                  {

                                    $html .= '<td> - </td><td> - </td>';

                                  }

                                  $html .= '</tr>';

                                endforeach;

                                $html .='



                            </tbody>

                          </table>

                          <div class="row" style="margin-top: 5px">

                            <div class="col-xs-6">

                              <span style="font-weight: bold;">Total Credit: </span>

                              <span style="font-weight: bold;" id="display_total">'.$total_credit.'</span>

                            </div>

                            <div class="col-xs-6">

                              <span style="font-weight: bold;">Total Debit: </span>

                              <span style="font-weight: bold;" id="display_total">'.$total_debit.'</span>

                            </div>

                          </div>

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



  public function factory_stock_details1()
  {
    if(!in_array('viewPurchasingDetails', $this->permission)) {

      $data['page_title'] = "No Permission";
      $this->load->view('templates/header', $data);
      $this->load->view('templates/header_menu');
      $this->load->view('templates/side_menubar');
      $this->load->view('errors/forbidden_access');
    }
    else
    {
      if(isset($_GET['range']))
      {
        $range = $_GET['range'];
        $ex = explode(' - ', $range);
        $from = date('d-m-Y', strtotime($ex[0]));
        $to = date('d-m-Y', strtotime($ex[1]));
        $result = array('data' => array());
        $data = $this->Model_products->getPurchaseOrdersData();
        $purchase_order_ids = array();
        foreach ($data as $key => $value)
        {
          $date = date('d-m-Y', strtotime($value['datetime_created']));
          $time = date('h:i a', strtotime($value['datetime_created']));
          $date_time = $date . ' ' . $time;
          if(strtotime($from) <= strtotime($date) and strtotime($to) >= strtotime($date))
          {
            if (!in_array($value['id'], $purchase_order_ids)) {

              array_push($purchase_order_ids, $value['id']);

            }

          }

        } // /foreach

        $data['page_title'] = "Report - Factory Stock Orders Detail History";
        $data['heading'] = "Factory Stock Orders Detail History";
        $data['result'] = $purchase_order_ids;
        $user_id = $this->session->userdata('id');
        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
        $data['user_permission'] = unserialize($group_data['permission']);
        $this->load->view('templates/header', $data);
        $this->load->view('templates/header_menu');
        $this->load->view('templates/side_menubar');
        $this->load->view('reports/factory_stock_details');
      }

      else

      {

        date_default_timezone_set("Asia/Karachi");
        $to = date('d-m-Y');
        $from = date('d-m-Y');
        $date_time = isset($_GET['date_time']) ? $_GET['date_time'] : ''; // Get the date from form submission
        $result = array('data' => array());
        //$data = $this->Model_products->getStockWithUnitsData();
        $data = $this->Model_products->getStockWithUnitsProductsData($date_time);
        //$purchase_order_ids = array();
       //  echo "<pre>";
       // print_r($data);
       // echo "</pre>";
       // exit;

        $data['page_title'] = "Report - Daily Factory Stock Orders Detail";
        $data['heading'] = "Daily Factory Stock Orders Detail";
        $data['result'] = $data;
        $user_id = $this->session->userdata('id');

        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
        $data['user_permission'] = unserialize($group_data['permission']);
        $this->load->view('templates/header', $data);
        $this->load->view('templates/header_menu');
        $this->load->view('templates/side_menubar');
        $this->load->view('reports/factory_stock_details');
      }

    }

    // end function

  }



  public function factory_stock_details()
  {
      $data['page_title'] = "Report - Daily Factory Stock Orders Detail";
      $data['heading'] = "Daily Factory Stock Orders Detail";
      $data['result'] = array('data' => array());
      $date_time = isset($_GET['date_time']) ? $_GET['date_time'] : date('Y-m-d');
      $data['stock_data'] = $this->Model_products->getStockWithUnitsProductsReportsData($date_time);

      /* echo "<pre>";
      print_r($data['stock_data']);
      echo "</pre>";
      exit; */
      $counter = 1;
      foreach ($data['stock_data'] as $key => $value) {
          $product_data = $this->Model_products->getAllProductData($value['product_id']);
          $product_name = $product_data['name'];
          $is_deleted = $value['is_deleted'];
          //$date_time = $product_data['date_time'];
          $category_name = '';
          if ($value['category_id']) {
              $category_data = $this->Model_category->getAllCategoryData($value['category_id']);
              if (!empty($category_data)) {
                  $category_name = $category_data['name'];
              }
          } else {
              $category_name = 'Nill';
          }
          $unit_name = $this->Model_products->getAllUnitsData($value['unit_id'])['unit_name'];
          $unit_value = $this->Model_products->fetchUnitValueData($value['unit_id'])['unit_value'];
          $unit_quantity = intval($value['sum_qty'] / $unit_value);
          $non_unit_qty = $value['sum_qty'] % $unit_value;
          $points_unit_qty = 0;
          if ($value['sum_qty'] - $unit_quantity < 1 && $value['sum_qty'] - $unit_quantity > 0) {
              $points_unit_qty = $value['sum_qty'] - $unit_quantity;
              $non_unit_qty += $points_unit_qty;
          }
          $qty = '';
          $stock_order_level = $this->Model_products->getFactoryStockOrderLevelData()['value'];
          if ($unit_quantity < $stock_order_level) {
              if ($non_unit_qty > 0) {
                  $qty = $unit_quantity . ' — ( ' . number_format($non_unit_qty, 3) . ' - Other )' . ' &nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-warning">Low</span>';
              } else {
                  $qty = $unit_quantity . ' &nbsp;&nbsp;&nbsp;&nbsp; <span class="label label-warning">Low</span>';
              }
          } else {
              if ($non_unit_qty > 0) {
                  $qty = $unit_quantity . ' — ( ' . number_format($non_unit_qty, 3) . ' - Other )';
              } else {
                  $qty = $unit_quantity;
              }
          }
          $user_id = $this->session->userdata('id');
          if ($user_id == 6) {
              $data['result']['data'][$key] = array(
                  $counter++,
                  $category_name,
                  $product_name,
                  $unit_name,
                  //$date_time,
                  $qty,
                  //"<a href='#' class='delete-record' data-id='" . $value['id'] . "'>delete</a>",
                  $is_deleted
              );
          } else {
              $data['result']['data'][$key] = array(
                  $counter++,
                  $category_name,
                  $product_name,
                  $unit_name,
                  //$date_time,
                  $qty,
                  $is_deleted,

              );
          }
      }
      $user_id = $this->session->userdata('id');

      $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
      $data['user_permission'] = unserialize($group_data['permission']);

      // Load the main view and pass the data
      $this->load->view('templates/header', $data);
      $this->load->view('templates/header_menu');
      $this->load->view('templates/side_menubar');
      $this->load->view('reports/factory_stock_details', $data);
  }





}



?>
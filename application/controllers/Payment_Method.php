<?php
class Payment_Method extends CI_Controller {
    
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
    	if(!in_array('recordPaymentMethod', $this->permission)) {
            $data['page_title'] = "No Permission";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/forbidden_access');
        }
        else{
        	$data['page_title'] = "Manage Payment Method";
        	$this->load->view('templates/header', $data);
        	$this->load->view('templates/header_menu');
        	$this->load->view('templates/side_menubar');

        	$result = $this->Model_payment_method->getPaymentMethodData();
        	$data['results'] = $result;

        	$user_id = $this->session->userdata('id');
            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
            $data['user_permission'] = unserialize($group_data['permission']);

        	$this->load->view('payment_method/index', $data);
        	$this->load->view('templates/footer');
        }
        
    }
    
	public function fetchPaymentMethodData()
	{
		$result = array('data' => array());

		$data = $this->Model_payment_method->getPaymentMethodData();
		$counter = 1;
		foreach ($data as $key => $value) {

			// button
			$buttons = '';
			if(in_array('viewPaymentMethod', $this->permission))
            {
			    $buttons .= '<a href="'.base_url("index.php/Payment_Method/view_payment_method/".$value['id']."").'"><i class="glyphicon glyphicon-eye-open"></i></a>';
            }
            if(in_array('updatePaymentMethod', $this->permission))
            {
			    $buttons .= ' <a onclick="editPaymentMethod('.$value['id'].')" data-toggle="modal" href="#editPaymentMethodModal"><i class="glyphicon glyphicon-pencil"></i></a>';	
            }

			if(in_array('deletePaymentMethod', $this->permission))
            {
				$buttons .= ' <a onclick="removePaymentMethod('.$value['id'].')" data-toggle="modal" href="#removePaymentMethodModal"><i class="glyphicon glyphicon-trash"></i></a>';
            }

			$result['data'][$key] = array(
				$counter++,
				$value['name'],
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	public function fetchPaymentMethodDataById($id)
	{
		if($id) {
			$data = $this->Model_payment_method->getPaymentMethodData($id);
			echo json_encode($data);
		}

		return false;
	}
	

	public function create_payment_method()
	{

		$response = array();
		$this->form_validation->set_rules('name', 'Name', 'trim|required|is_unique[payment_method.name]');
		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

        if ($this->form_validation->run() == TRUE) {
        	$data = array(
        		'name' => $this->input->post('name'),
                'is_deleted' => 0
        	);

        	$create = $this->Model_payment_method->create($data);
        	if($create == true) {
        		$response['success'] = true;
        		$response['messages'] = 'Succesfully created';
        	}
        	else {
        		$response['success'] = false;
        		$response['messages'] = 'Error in the database while creating the payment method information';
        	}
        }
        else {
        	$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
        }

        echo json_encode($response);

	}

	public function update_payment_method($id)
	{

		$response = array();

		if($id) {
			$this->form_validation->set_rules('edit_name', 'Name', 'trim|required|edit_unique[payment_method.name.'.$id.']');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

	        if ($this->form_validation->run() == TRUE) {
	        	$data = array(
	        		'name' => $this->input->post('edit_name'),	
	        	);

	        	$update = $this->Model_payment_method->update($data, $id);
	        	if($update == true) {
	        		$response['success'] = true;
	        		$response['messages'] = 'Succesfully updated';
	        	}
	        	else {
	        		$response['success'] = false;
	        		$response['messages'] = 'Error in the database while updated the payment method information';			
	        	}
	        }
	        else {
	        	$response['success'] = false;
	        	foreach ($_POST as $key => $value) {
	        		$response['messages'][$key] = form_error($key);
	        	}
	        }
		}
		else {
			$response['success'] = false;
    		$response['messages'] = 'Error please refresh the page again!!';
		}

		echo json_encode($response);
	}

	public function view_payment_method($id)
	{
		if(!in_array('viewPaymentMethod', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$data['payment_method_data'] = $this->Model_payment_method->getPaymentMethodData($id);
			if(!empty($data['payment_method_data'])){
				$data['page_title'] = "Payment Method View";
				$this->load->view('templates/header', $data);
				$this->load->view('templates/header_menu');
				$this->load->view('templates/side_menubar');
				$user_id = $this->session->userdata('id');
				$data['payment_method_data'] = $this->Model_payment_method->getPaymentMethodData($id);
				$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
				$data['user_permission'] = unserialize($group_data['permission']);
				$this->load->view('payment_method/view_payment_method', $data);
				$this->load->view('templates/footer');
			}
			else{
				$data['page_title'] = "404 - Not Found";
                $this->load->view('templates/header', $data);
                $this->load->view('templates/header_menu');
                $this->load->view('templates/side_menubar');
                $this->load->view('errors/404_not_found');
			}
		}
	}

	public function remove_payment_method()
	{
		
		$payment_method_id = $this->input->post('id');
		$response = array();
		if($payment_method_id) {
			$delete = $this->Model_payment_method->remove($payment_method_id);

			if($delete == true) {
				$response['success'] = true;
				$response['messages'] = "Successfully removed";	
			}
			else {
				$response['success'] = false;
				$response['messages'] = "Error in the database while removing the brand information";
			}
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refersh the page again!!";
		}

		echo json_encode($response);
	}

	public function print_payment_method()
	{
        if(!in_array('printPaymentMethod', $this->permission)) {
            $data['page_title'] = "No Permission";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/forbidden_access');
        }
        else
        {
            $result = array();
            date_default_timezone_set("Asia/Karachi");
    		$print_date = date('d/m/Y');
    		$user_id = $this->session->userdata('id');
    		$user_data = $this->Model_users->getUserData($user_id);
            $data = $this->Model_payment_method->getPaymentMethodData();

            if(!empty($data)){
              
                  $html = '<!DOCTYPE html>
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
                            
                            <title>TBM - Payment Method Print</title>
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
                                                    <table class="table table-condensed table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                            	<th width="20%"><strong>#</strong></th>
                                                                <th width="80%"><strong>Name Name</strong></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>'; 
                                                $counter = 1;
                                                foreach ($data as $key => $value) {
                                                	$html .= '<tr>
                                                    	<td>'.$counter++.'</td>
                                                    	<td style="text-transform:capitalize">'.$value['name'].'</td>
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
          else{
                $html = '<!-- Main content -->
                  <!DOCTYPE html>
                  <html>
                  <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <title>TBM Automobile Private Ltd | Payment Method Print</title>
                    <!-- Tell the browser to be responsive to screen width -->
                    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
                    <!-- Bootstrap 3.3.7 -->
                    <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">
                    <!-- Font Awesome -->
                    <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">
                    <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">
                  </head>
                  <body onload="window.print();">
                  
                    <div class="wrapper">
                      <section class="invoice">
                        <!-- title row -->
                        <div class="row">
                          <div class="col-xs-12">
                            <h2 class="page-header">
                              TBM Automobile Private Ltd
                            </h2>
                          </div>
                          <!-- /.col -->
                        </div>
                        <!-- Table row -->
                        <div class="row">
                          <div class="col-xs-12 table-responsive">
                            <table class="table table-striped">
                              <thead>
                              <tr>
    	                          <th><strong>#</strong></th>
    	                          <th><strong>Name</strong></th>
                              </tr>
                              </thead>
                              <tbody>'; 

                                $html .= '<tr>
                                  
                                </tr>';
                              
                              $html .= '</tbody>
                            </table>
                          </div>
                          <!-- /.col -->
                        </div>
                        <!-- /.row -->
                      </section>
                      <!-- /.content -->
                    </div>
                  </body>
                </html>';
              echo $html;
          }
      }
	}



































}
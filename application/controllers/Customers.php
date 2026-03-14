<?php
class Customers extends CI_Controller {

	var $permission = array();

    public function __construct()
	{
        parent::__construct();

        $group_data = array();
        if(!$this->session->userdata('logged_in')){
			redirect('User/index');		
		}
		else{
			$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$this->data['user_permission'] = unserialize($group_data['permission']);
			$this->permission = unserialize($group_data['permission']);
		}
    }
    public function index()
    {
    	if(!in_array('recordCustomer', $this->permission)) {
    		$data['page_title'] = "No Permission";
    		$this->load->view('templates/header', $data);
    		$this->load->view('templates/header_menu');
    		$this->load->view('templates/side_menubar');
    		$this->load->view('errors/forbidden_access');
		}
		else{
	        $data['page_title'] = "Manage Customers";
	        $this->load->view('templates/header', $data);
	        $this->load->view('templates/header_menu');
	        $this->load->view('templates/side_menubar');
	        
	        $data['department_data'] = $this->Model_department->getDepartmentData();
			$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$data['user_permission'] = unserialize($group_data['permission']);

	        $this->load->view('customers/index', $data);
	        $this->load->view('templates/footer');
		}
        
    }

    /*
	* Fetches the customers data from the customers table 
	* this function is called from the datatable ajax function
    */
    
	public function fetchCustomerData()
	{
		$result = array('data' => array());
		$data = $this->Model_Customers->getCustomerData();
        $counter = 1;
		foreach ($data as $key => $value) {

			// button
			$buttons = '';
			if(in_array('viewCustomer', $this->permission)){

				$buttons .= '<a title="View Customer" href="'.base_url("index.php/Customers/customer_view/".$value['id']).'"><i class="glyphicon glyphicon-eye-open"></i></a>';	
			}
			if(in_array('updateCustomer', $this->permission)){

				$buttons .= ' <a title="Edit Customer" onclick="editCustomer('.$value['id'].')" data-toggle="modal" href="#editCustomerModal"><i class="glyphicon glyphicon-pencil"></i></a>';	
			}
			if(in_array('deleteCustomer', $this->permission)){

				$buttons .= ' <a title="Delete Customer" onclick="removeCustomer('.$value['id'].')" data-toggle="modal" href="#removeCustomerModal"><i class="fa fa-trash"></i></a>
				';
			}
			$department_name = $this->Model_department->getCustomerDeparment($value['id'])['department_name'];
			$result['data'][$key] = array(
				$counter++,
				$value['full_name'],
				$department_name,
				$value['cnic'],
				$value['address'],
				$buttons
			);

		} // /foreach

		echo json_encode($result);
	}

	public function customer_view($customerId)
	{
		if(!in_array('viewCustomer', $this->permission)) 
		{
    		$data['page_title'] = "No Permission";
    		$this->load->view('templates/header', $data);
    		$this->load->view('templates/header_menu');
    		$this->load->view('templates/side_menubar');
    		$this->load->view('errors/forbidden_access');
    	}
    	else
    	{
    		$data['customer_data'] = $this->Model_Customers->getCustomerData($customerId);
    		if(!empty($data['customer_data'])){
    			$data['page_title'] = "Customer View";
    			$this->load->view('templates/header', $data);
    			$this->load->view('templates/header_menu');
    			$this->load->view('templates/side_menubar');

    			$data['customer_data'] = $this->Model_Customers->getCustomerData($customerId);
    			$data['department_data'] = $this->Model_department->getDepartmentData();

    			$user_id = $this->session->userdata('id');
    			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
    			$data['user_permission'] = unserialize($group_data['permission']);

    			$this->load->view('customers/view', $data);
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

	/*
	* It checks if it gets the customer id and retreives
	* the customer information from the customer model and 
	* returns the data into json format. 
	* This function is invoked from the view page.
	*/
	public function fetchCustomerDataById($id)
	{
		if($id) {
			$data['customers_data'] = $this->Model_Customers->getCustomerData($id);
			$data['department_data'] = $this->Model_department->getCustomerDeparment($id);
			echo json_encode($data);
		}

		return false;
	}

	/*
	* Its checks the customer form validation 
	* and if the validation is successfully then it inserts the data into the database 
	* and returns the json format operation messages
	*/
	public function create_customer()
	{
		$response = array();

		$this->form_validation->set_rules('customer_full_name', 'Full Name', 'trim|required');
		$this->form_validation->set_rules('customer_phone', 'Phone', 'trim|required');
		$this->form_validation->set_rules('customer_address', 'Address', 'trim|required');

		$this->form_validation->set_rules('department', 'Department', 'trim|required');

		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

		if ($this->form_validation->run() == TRUE) 
		{
			$customer_exist = $this->Model_Customers->customerExist
			(
				$this->input->post('customer_full_name')
			);
			if($customer_exist == 1)
			{
				$response['success'] = false;
				$response['messages'] = 'Customer with this Full Name Already Exist. PLease Try different FullName';
			}
			else
			{
				$data = array(
					'full_name' => $this->input->post('customer_full_name'),
					'cnic' => $this->input->post('customer_cnic'),
					'phone_number' => $this->input->post('customer_phone'),
					'address' => $this->input->post('customer_address'),
					'is_deleted' => 0,	
				);

				$create = $this->db->insert('customers', $data);
				$customer_id = $this->db->insert_id();
				if($create == true) {
					$data = array(
						'customer_id' => $customer_id,
						'department_id' => $this->input->post('department')
					);
					$this->db->insert('customer_department', $data);

					$response['success'] = true;
					$response['messages'] = 'Succesfully created';
				}
				else {
					$response['success'] = false;
					$response['messages'] = 'Error in the database while creating the customer information';
				}
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

	/*
	* Its checks the customer form validation 
	* and if the validation is successfully then it updates the data into the database 
	* and returns the json format operation messages
	*/
	public function update_customer($id)
	{
		$response = array();

		if($id) {

			$this->form_validation->set_rules('edit_customer_full_name', 'Full Name', 'trim|required');
			$this->form_validation->set_rules('edit_customer_phone', 'Phone', 'trim|required');
			$this->form_validation->set_rules('edit_customer_address', 'Address', 'trim|required');
			$this->form_validation->set_rules('edit_department', 'Department', 'trim|required');
			
			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

			if ($this->form_validation->run() == TRUE) {
		        // check if customer with fullname exist
				$customer_exist = $this->Model_Customers->customerExist
				(
					$this->input->post('edit_customer_full_name')
				);
				if($customer_exist == 1)
				{
					$customer_exist_row = $this->Model_Customers->customerExistRow
					(
						$this->input->post('edit_customer_full_name')
					);
					if($customer_exist_row['id'] == $id)
					{
						$data = array(
							'full_name' => $this->input->post('edit_customer_full_name'),
							'cnic' => $this->input->post('edit_customer_cnic'),
							'phone_number' => $this->input->post('edit_customer_phone'),
							'address' => $this->input->post('edit_customer_address'),
								
						);

						$update = $this->Model_Customers->update($data, $id);
						if($update == true) {

							$department_data = $this->Model_department->getCustomerDeparment($id);
							$data = array(
								'customer_id' => $id,
								'department_id' => $this->input->post('edit_department')
							);
							$this->db->where('id', $department_data['customer_department_table_id']);
							$this->db->update('customer_department', $data);

							$response['success'] = true;
							$response['messages'] = 'Succesfully updated';
						}
						else {
							$response['success'] = false;
							$response['messages'] = 'Error in the database while updated the customer information';			
						}
					}
					else
					{
						$response['success'] = false;
						$response['messages'] = 'Customer with this Full Name Already Exist. PLease Try different FullName';
					}
				}
				else if($customer_exist == 0)
				{
					$data = array(
						'full_name' => $this->input->post('edit_customer_full_name'),
						'cnic' => $this->input->post('edit_customer_cnic'),
						'phone_number' => $this->input->post('edit_customer_phone'),
						'address' => $this->input->post('edit_customer_address'),	
					);

					$update = $this->Model_Customers->update($data, $id);
					if($update == true) {

						$department_data = $this->Model_department->getCustomerDeparment($id);
						$data = array(
							'customer_id' => $id,
							'department_id' => $this->input->post('edit_department')
						);
						$this->db->where('id', $department_data['customer_department_table_id']);
						$this->db->update('customer_department', $data);
						$response['success'] = true;
						$response['messages'] = 'Succesfully updated';
					}
					else {
						$response['success'] = false;
						$response['messages'] = 'Error in the database while updated the customer information';			
					}
				}
				else
				{
					$response['success'] = false;
					$response['messages'] = 'Customer with this FullName Already Exist. PLease Try different FullName';
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
	
	/*
	* It removes the customer information from the database 
	* and returns the json format operation messages
	*/
	public function remove_customer()
	{
		$customer_id = $this->input->post('customer_id');
		$response = array();
		if($customer_id) {
			// if it is used donot delete it
			$customer_sales_rows = $this->Model_Customers->getCustomerOrderRows($customer_id);
			if(!empty($customer_sales_rows)){
				$response['success'] = false;
                $response['messages'] = "System could not delete the Customer. May be it's because this customer is being used somewhere in the system";
                echo json_encode($response);
                return;
			}
			else{
				$delete = $this->Model_Customers->remove($customer_id);
				if($delete == true) {
					$department_data = $this->Model_department->getCustomerDeparment($customer_id);
					$data = array('is_deleted' => 1);
					$this->db->where('id', $department_data['customer_department_table_id']);
					$this->db->update('customer_department', $data);
					$response['success'] = true;
					$response['messages'] = "Successfully removed";	
				}
				else {
					$response['success'] = false;
					$response['messages'] = "Error in the database while removing the customer information";
				}
			}
			
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refersh the page again!!";
		}
		echo json_encode($response);
	}

	public function print_customers()
	{
		if(!in_array('printCustomer', $this->permission)) {
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
	        $data = $this->Model_Customers->getCustomerData();
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
	                            
	                            
	                        </style>
	                        
	                        <title>TBM - Customers Print</title>
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
	                                                        	<th><strong>#</strong></th>
	                                                            <th><strong>FullName</strong></th>
	                                                        	<th><strong>Department</strong></th>
	                                                        	<th><strong>Phone</strong></th>
	                                                        	<th><strong>CNIC</strong></th>
	                                                        	<th><strong>Address</strong></th>
	                                                        </tr>
	                                                    </thead>
	                                                    <tbody>'; 
	                                            $counter = 1;
	                                            foreach ($data as $key => $value) {
	                                            	$department_name = $this->Model_department->getCustomerDeparment($value['id'])['department_name'];
													$html .= '<tr>
	                                                	<td>'.$counter++.'</td>
	                                                	<td style="text-transform:capitalize">'.$value['full_name'].'</td>
	                                                	<td>'.$department_name.'</td>
	                                                	<td>'.$value['phone_number'].'</td>
	                                                	<td>'.$value['cnic'].'</td>
	                                                	<td>'.$value['address'].'</td>
	                                                	
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
	                <title>TBM Automobile Private Ltd | Customers Print</title>
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
	                        <h2 class="invoice-title text-center">
	                          TBM Automobile Private Ltd
	                        </h2>
	                      </div>
	                      <!-- /.col -->
	                    </div>
	                    <!-- Table row -->
	                    <div class="row">
	                      <div class="col-xs-12 table-responsive">
	                        <table class="table table-striped table-bordered table-condensed">
	                          <thead>
	                          <tr>
		                          <th><strong>#</strong></th>
		                          <th><strong>FullName</strong></th>
		                          <th><strong>Department</strong></th>
		                          <th><strong>Phone</strong></th>
		                          <th><strong>CNIC</strong></th>
		                          <th><strong>Address</strong></th>
		                          <th><strong>Status</strong></th>
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
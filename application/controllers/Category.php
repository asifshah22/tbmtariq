<?php
class Category extends CI_Controller {

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
    	if(!in_array('recordCategory', $this->permission)) {
			$data['page_title'] = "No Permission";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/error_no_permission'); 
		}
		else{

	        $data['page_title'] = "Manage Categories";
	        $this->load->view('templates/header', $data);
	        $this->load->view('templates/header_menu');
	        $this->load->view('templates/side_menubar');

	        $user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$data['user_permission'] = unserialize($group_data['permission']);

	        $this->load->view('category/index', $data);
	        $this->load->view('templates/footer');
		}
    }

    /*
	* It checks if it gets the category id and retreives
	* the category information from the category model and 
	* returns the data into json format. 
	* This function is invoked from the view page.
	*/
	public function fetchCategoryDataById($id) 
	{
		if($id) {
			$data = $this->Model_category->getCategoryData($id);
			echo json_encode($data);
		}
		return false;
	}

	/*
	* Fetches the category value from the category table 
	* this function is called from the datatable ajax function
	*/
	public function fetchCategoryData()
	{
		$result = array('data' => array());
		$data = $this->Model_category->getCategoryData();
		$counter = 1;

		foreach ($data as $key => $value) {

			// button
			$buttons = '';
			
			if(in_array('updateCategory', $this->permission)) {

				$buttons .= '<a title="Edit Category" onclick="editFunc('.$value['id'].')" data-toggle="modal" href="#editModal"><i class="glyphicon glyphicon-pencil"></i></a>';
			}

			if(in_array('deleteCategory', $this->permission)) {
				$buttons .= ' <a title="Delete Category" onclick="removeFunc('.$value['id'].')" data-toggle="modal" href="#removeModal"><i class="glyphicon glyphicon-trash"></i></a>';
			}

			$result['data'][$key] = array(
				$counter++,
				$value['name'],
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	/*
	* Its checks the category form validation 
	* and if the validation is successfully then it inserts the data into the database 
	* and returns the json format operation messages
	*/
	public function create_category()
	{
		$response = array();
		$this->form_validation->set_rules('category_name', 'Category name', 'trim|is_unique[categories.name]|required');
		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'name' => $this->input->post('category_name')
			);
			$create = $this->Model_category->create($data);
			if($create == true) {
				$response['success'] = true;
				$response['messages'] = 'Succesfully created';
			}
			else {
				$response['success'] = false;
				$response['messages'] = 'Error in the database while creating the brand information';			
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
	* Its checks the category form validation 
	* and if the validation is successfully then it updates the data into the database 
	* and returns the json format operation messages
	*/

	public function update_category($id)
	{
		$response = array();
		if($id) {
			$this->form_validation->set_rules('edit_category_name', 'Category name', 'trim|edit_unique[categories.name.'.$id.']|required');
			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

			if ($this->form_validation->run() == TRUE) {
				$data = array(
					'name' => $this->input->post('edit_category_name')
				);

				$update = $this->Model_category->update($data, $id);
				if($update == true) {
					$response['success'] = true;
					$response['messages'] = 'Succesfully updated';
				}
				else {
					$response['success'] = false;
					$response['messages'] = 'Error in the database while updated the Category information';
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
	* It removes the category information from the database 
	* and returns the json format operation messages
	*/

	public function remove_category()
	{
		$category_id = $this->input->post('category_id');
		$response = array();
		if($category_id) {
			$delete = $this->Model_category->remove($category_id);
			if($delete == true) {
				$response['success'] = true;
				$response['messages'] = "Successfully removed";	
			}
			else {
				$response['success'] = false;
				$response['messages'] = "Error in the database while removing the categories information";
			}
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refersh the page again!!";
		}
		echo json_encode($response);
	}
	
	public function print_item_categories()
	{
		if(!in_array('printCategory', $this->permission)) {
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
	        $data = $this->Model_category->getCategoryData();
	        

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
	                        
	                        <title>TBM - Item Categories Print</title>
	                    </head>
	                    <body onload="window.print();">
	                        <div class="container">
	                            <div class="row">
	                                <div class="col-xs-12">
	                                    <div class="invoice-title tect-center">
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
	                                                            <th><strong>Category Name</strong></th>
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
		                <title>TBM Automobile Private Ltd | Item Categories Print</title>
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
			                          <th style="width:10%"><strong>#</strong></th>
			                          <th style="width:45%"><strong>Category Name</strong></th>
			                          <th style="width:45%"><strong>Status</strong></th>
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
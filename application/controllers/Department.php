<?php
class Department extends CI_Controller {

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
        if(!in_array('recordDepartment', $this->permission)) {
            $data['page_title'] = "No Permission";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/forbidden_access');
        }
        else
        {
            $data['page_title'] = "Manage Department";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');

            $user_id = $this->session->userdata('id');
            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
            $data['user_permission'] = unserialize($group_data['permission']);

            $this->load->view('department/index', $data);
            $this->load->view('templates/footer'); 
        }
    	   	
    }

    public function fetchDepartmentData()
	{
		$result = array('data' => array());

		$data = $this->Model_department->getDepartmentData();
		$counter = 1;

		foreach ($data as $key => $value) {

			// button
			$buttons = '';
			
            if(in_array('updateDepartment', $this->permission))
            {
			     $buttons .= '<a title="Edit Cash" onclick="editFunc('.$value['id'].')" data-toggle="modal" href="#editModal"><i class="glyphicon glyphicon-pencil"></i></a>';
            }
            if(in_array('deleteDepartment', $this->permission))
            {
			     $buttons .= ' <a title="Delete Cash"onclick="removeFunc('.$value['id'].')" data-toggle="modal" href="#removeModal"><i class="fa fa-trash"></i></a>';
            }

			$result['data'][$key] = array(
				$counter++,
				$value['department_name'],
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	public function create_department()
	{
		$response = array();

		$this->form_validation->set_rules('department_name', 'Department Name', 'trim|required|is_unique[department.department_name]');

		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

		if ($this->form_validation->run() == TRUE) {

			$data = array(
				'department_name' => $this->input->post('department_name'),
			);
			$create = $this->db->insert('department', $data);
			if($create)
			{
				$response['success'] = true;
				$response['messages'] = 'Succesfully created';
			}
			else
			{
				$response['success'] = false;
				$response['messages'] = 'Error in the database while creating the Department';			
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

	public function fetchDepartmentDataById($id) 
	{
		if($id) {
			$data = $this->Model_department->getDepartmentData($id);
			echo json_encode($data);
		}

		return false;
	}

	public function update_department($id)
	{

		$response = array();

		if($id) {
			$this->form_validation->set_rules('edit_department_name', 'Department Name', 'trim|required');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

			if ($this->form_validation->run() == TRUE) {
                $department_exist = $this->Model_department->departmentExist($this->input->post('edit_department_name'));
                if($department_exist == 1)
                {
                    $department_exist_row = $this->Model_department->departmentExistRow($this->input->post('edit_department_name'));
                    if($department_exist_row['id'] == $id)
                    {
                        $data = array(
                            'department_name' => $this->input->post('edit_department_name'),
                        );
                        $update = $this->Model_department->update($data, $id);
                        if($update) {
                            $response['success'] = true;
                            $response['messages'] = 'Succesfully updated';
                        }
                        else {
                            $response['success'] = false;
                            $response['messages'] = 'Error in the database while updating the department information';          
                        }
                    }
                    else
                    {
                        $response['success'] = false;
                        $response['messages'] = 'Department with this Name Already Exist. PLease Try different Name 1';
                    }
                }
                else if($department_exist == 0)
                {
                    $data = array(
                        'department_name' => $this->input->post('edit_department_name'),
                    );
                    $update = $this->Model_department->update($data, $id);
                    if($update) {
                        $response['success'] = true;
                        $response['messages'] = 'Succesfully updated';
                    }
                    else {
                        $response['success'] = false;
                        $response['messages'] = 'Error in the database while updating the department information';          
                    }
                }
                else
                {
                    $response['success'] = false;
                    $response['messages'] = 'Department with this Name Already Exist. PLease Try different Name 2';
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

	public function remove_department()
	{

		$deparment_id = $this->input->post('department_id');

		$response = array();
		if($deparment_id) {
            // if this department is used dont delete it
            $customer_dapartment_rows = $this->Model_department->fecthCustomerDeparmentRows($deparment_id);
            if(!empty($customer_dapartment_rows))
            {
                $response['success'] = false;
                $response['messages'] = "System could not delete the Department. May be it's because this Department is being used somewhere in the system";
                echo json_encode($response);
                return;
            }
            else{
                $delete = $this->Model_department->remove($deparment_id);
                if($delete == true) {
                    $response['success'] = true;
                    $response['messages'] = "Successfully removed"; 
                }
                else {
                    $response['success'] = false;
                    $response['messages'] = "Error in the database while removing the Department information";
                }
            }
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refersh the page again!!";
		}

		echo json_encode($response);
		
	}
    
	public function print_departments()
	{
        if(!in_array('printDepartment', $this->permission)) {
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
            $data = $this->Model_department->getDepartmentData();

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
                            
                            <title>TBM - Departments Print</title>
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
                                                            	<th style="width:10%"><strong>#</strong></th>
                                                                <th><strong>Department Name</strong></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>'; 
                                                $counter = 1;
                                                foreach ($data as $key => $value) {
                                                	$html .= '<tr>
                                                    	<td>'.$counter++.'</td>
                                                    	<td style="text-transform:capitalize">'.$value['department_name'].'</td>
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
                    <title>TBM Automobile Private Ltd | Departments Print</title>
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
    	                          <th style="width:20%"><strong>#</strong></th>
    	                          <th style="width:40%"><strong>Department Name</strong></th>
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
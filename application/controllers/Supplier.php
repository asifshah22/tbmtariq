<?php
class Supplier extends CI_Controller {

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
		if(!in_array('recordVendor', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else{

			$data['results'] = $this->Model_supplier->getSupplierData();
			$data['page_title'] = "Manage Supplier";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');

			$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$data['user_permission'] = unserialize($group_data['permission']);

			$this->load->view('suppliers/index', $data);
			$this->load->view('templates/footer');
		}

	}

    /*
	* Fetches the supplier data from the supplier table 
	* this function is called from the datatable ajax function
    */

	public function fetchSupplierData($selected_supplier = null)
	{
		$result = array('data' => array());
		if($selected_supplier != ""){
			$single_row = $this->Model_supplier->getSupplierData($selected_supplier);
			$data = array($single_row);
		}
		else{
			$data = $this->Model_supplier->getSupplierData();
		}
		$counter = 1;
		foreach ($data as $key => $value) {

			// button
			$buttons = '';
			$button_edit_photo = '';
			if(in_array('viewVendor', $this->permission)){

				$buttons .= '<a title="Edit Vendor" href="'.base_url('index.php/Supplier/supplier_view/'.$value['id']).'"><i class="glyphicon glyphicon-eye-open"></i></a>';
			}
			if(in_array('updateVendor', $this->permission)){

				$buttons .= ' <a title="Edit Vendor" onclick="editSupplier('.$value['id'].')" data-toggle="modal" href="#editSupplierModal"><i class="glyphicon glyphicon-pencil"></i></a>';

				$button_edit_photo = '<a href="#edit_photo" data-toggle="modal" class="pull-right" onclick="editPhoto('.$value['id'].')"><span class="fa fa-edit"></span></a>';	
			}
			if(in_array('deleteVendor', $this->permission)){
				$buttons .= ' <a title="Delete Vendor" onclick="removeSupplier('.$value['id'].')" data-toggle="modal" href="#removeSupplierModal"><i class="glyphicon glyphicon-trash"></i></a>
				';
			}
			$phones = '';
			$db_phones = unserialize($value['phone']);
			$count_phones = count($db_phones);
			for ($x = 0; $x < $count_phones; $x++) {
				$phones .= $db_phones[$x]. "<br>";
			}

			$image = '';
			if($value['image']){
				$image = '<a target="_blank" href="'.base_url().'assets/images/vendor_images/'.$value['image'].'" title="Vendor image"><img src="'.base_url('/assets/images/vendor_images/'.$value['image'].'').'" alt="Vendor image" class="img-circle" width="60" height="60" /></a>';
			}
			else{
				$image = '<a target="_blank" href="'.base_url().'assets/images/vendor_images/vendor-default-im.jpg" title="Vendor default image"><img src="'.base_url('/assets/images/vendor_images/vendor-default-im.jpg').'" alt="vendor default image" class="img-circle" width="50" height="50" /></a>';
			}
			$image .= $button_edit_photo;
			$date = date('d-m-Y', strtotime($value['creation_date_time']));
			$time = date('h:i a', strtotime($value['creation_date_time']));
			$date_time = $date . ' ' . $time;
			
			$result['data'][$key] = array(
				$counter++,
				$value['image'] = $image,
				$value['first_name']. ' '.$value['last_name'],
				$value['address'],
				$value['city'],
				floatval($value['balance']),
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	public function supplier_view($supplierId)
	{
		if(!in_array('viewVendor', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else{
			$data['supplier_data'] = $this->Model_supplier->getSupplierData($supplierId);
			if(!empty($data['supplier_data'])){
				$data['page_title'] = "Supplier View";
				$this->load->view('templates/header', $data);
				$this->load->view('templates/header_menu');
				$this->load->view('templates/side_menubar');

				$data['supplier_data'] = $this->Model_supplier->getSupplierData($supplierId);
				$user_id = $this->session->userdata('id');
				$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
				$data['user_permission'] = unserialize($group_data['permission']);
				$this->load->view('suppliers/view', $data);
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
	* It checks if it gets the supplier id and retreives
	* the supplier information from the supplier model and 
	* returns the data into json format. 
	* This function is invoked from the view page.
	*/
	public function fetchSupplierDataById($id)
	{
		if($id) {

			$output['data'] = $this->Model_supplier->getSupplierData($id);
			$output['phones'] = unserialize($output['data']['phone']);
			echo json_encode($output);
		}

		return false;
	}

	public function get_vendor_row()
	{
		if(isset($_POST['id'])){
			$id = $_POST['id'];
			$output['data'] = $this->Model_supplier->getSupplierData($id);
			echo json_encode($output);
		}	
	}

	public function edit_photo()
	{
		if(isset($_POST['upload'])){
			$id = $this->input->post('vendor_id');
			$filename = $_FILES['input_edit_photo']['name'];
			if(!empty($filename)){
				move_uploaded_file($_FILES['input_edit_photo']['tmp_name'], 'assets/images/vendor_images/'.$filename);
				$data = array(
					'image' => $_FILES['input_edit_photo']['name']
				);
				if($this->Model_supplier->update($data, $id)){
					$this->session->set_flashdata('success', 'Photo updated successfully!');
					return redirect('/Supplier/index');
				}	
			}
		}
		else{
			return redirect('/Supplier/index');
		}
	}

	/*
	* Its checks the supplier form validation 
	* and if the validation is successfully then it inserts the data into the database 
	* and returns the json format operation messages
	*/

	public function create_supplier()
	{
		$response = array();

		$this->form_validation->set_rules('supplier_fname', 'First name', 'trim|required');
		$this->form_validation->set_rules('supplier_address', 'Address', 'trim|required');
		$this->form_validation->set_rules('supplier_city', 'City', 'trim|required');
		
		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

		if ($this->form_validation->run() == TRUE) {
			date_default_timezone_set("Asia/Karachi");
			$datetime = date('Y-m-d H:i:s a');
			$data = array(
				'creation_date_time' => $datetime,
				'first_name' => $this->input->post('supplier_fname'),
				'last_name' => $this->input->post('supplier_lname'),
				'address' => $this->input->post('supplier_address'),
				'balance' => $this->input->post('opening_balance'),
				'starting_balance' => $this->input->post('opening_balance'),
				'city' => $this->input->post('supplier_city'),
				'country' => $this->input->post('supplier_country'),
				'cnic' => $this->input->post('supplier_cnic'),
				'phone' => serialize($this->input->post('phone')),
				'email' => $this->input->post('supplier_email'),
				'image' => "",
				'remarks' => $this->input->post('remarks'),
			);
			$create = $this->Model_supplier->create($data);
			if($create == true) {
				$response['success'] = true;
				$response['messages'] = 'Succesfully created';
			}
			else {
				$response['success'] = false;
				$response['messages'] = 'Error in the database while creating the supplier information';			
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

	public function upload_image()
	{
    	// assets/images/vendor_images
		$config['upload_path'] = 'assets/images/vendor_images';
		$config['file_name'] =  uniqid();
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size'] = '1000';

        // $config['max_width']  = '1024';
        // $config['max_height']  = '768';

		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload('vendor_image'))
		{
			$error = $this->upload->display_errors();
			return $error;
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			$type = explode('.', $_FILES['vendor_image']['name']);
			$type = $type[count($type) - 1];

			$path = $config['upload_path'].'/'.$config['file_name'].'.'.$type;
			return ($data == true) ? $path : false;            
		}
	}


	/*
	* Its checks the supplier form validation 
	* and if the validation is successfully then it updates the data into the database 
	* and returns the json format operation messages
	*/
	public function update_supplier($id)
	{
		$response = array();

		if($id) {

			$this->form_validation->set_rules('edit_supplier_fname', 'First name', 'trim|required');
			$this->form_validation->set_rules('edit_supplier_address', 'Address', 'trim|required');
			$this->form_validation->set_rules('edit_supplier_city', 'City', 'trim|required');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

			if ($this->form_validation->run() == TRUE) {

				$supplier_exist = $this->Model_supplier->supplierExist($this->input->post('edit_supplier_fname'), $this->input->post('edit_supplier_lname'));
				if($supplier_exist == 1)
				{
					$supplier_exist_row = $this->Model_supplier->supplierExistRow($this->input->post('edit_supplier_fname'), $this->input->post('edit_supplier_lname'));
					if($supplier_exist_row['id'] == $id)
					{
						$data = array
						(
							'first_name' => $this->input->post('edit_supplier_fname'),
							'last_name' => $this->input->post('edit_supplier_lname'),
							'balance' => $this->input->post('edit_opening_balance'),
							'starting_balance' => $this->input->post('edit_opening_balance'),
							'address' => $this->input->post('edit_supplier_address'),
							'city' => $this->input->post('edit_supplier_city'),
							'country' => $this->input->post('edit_supplier_country'),
							'cnic' => $this->input->post('edit_supplier_cnic'),
							'phone' => serialize($this->input->post('edit_phone')),
							'email' => $this->input->post('edit_supplier_email'),
							'remarks' => $this->input->post('edit_remarks'),
						);
						$update = $this->Model_supplier->update($data, $id);
						if($update == true) {
							$response['success'] = true;
							$response['messages'] = 'Succesfully updated';
						}
						else {
							$response['success'] = false;
							$response['messages'] = 'Error in the database while updated the supplier information';
						}
					}
					else
					{
						$response['success'] = false;
						$response['messages'] = 'Vendor with this First and Last Name Already Exist. PLease Try a different Name';
					}
				}
				else if($supplier_exist == 0)
				{
					// else update it
					$data = array(
						'first_name' => $this->input->post('edit_supplier_fname'),
						'last_name' => $this->input->post('edit_supplier_lname'),
						'balance' => $this->input->post('edit_opening_balance'),
						'starting_balance' => $this->input->post('edit_opening_balance'),
						'address' => $this->input->post('edit_supplier_address'),
						'city' => $this->input->post('edit_supplier_city'),
						'country' => $this->input->post('edit_supplier_country'),
						'cnic' => $this->input->post('edit_supplier_cnic'),
						'phone' => serialize($this->input->post('edit_phone')),
						'email' => $this->input->post('edit_supplier_email'),
						'remarks' => $this->input->post('edit_remarks'),
					);
					$update = $this->Model_supplier->update($data, $id);
					if($update == true) {
						$response['success'] = true;
						$response['messages'] = 'Succesfully updated';
					}
					else {
						$response['success'] = false;
						$response['messages'] = 'Error in the database while updated the supplier information';
					}
					
				}
				else
				{
					$response['success'] = false;
					$response['messages'] = 'Vendor with this CNIC Already Exist. PLease Try different CNIC';
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

	public function fetchSupplierDataForSelect()
	{
		$output['data'] = $this->Model_supplier->getSupplierData();
		echo json_encode($output);
		return false;
	}

	/*
	* Its checks the supplier form validation 
	* and if the validation is successfully then it updates the data into the database 
	* and returns the json format operation messages
	*/
	// public function add_phone($id)
	// {
	// 	$response = array();

	// 	if($id) {

	// 		$this->form_validation->set_rules('phone_number', 'Phone', 'trim|required');
	// 		$this->form_validation->set_rules('phone_active', 'Active', 'trim|required');

	// 		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

	// 		if ($this->form_validation->run() == TRUE) {
	// 			$data = array(
	// 				'suplier_id' => $id,
	// 				'phone_number' => $this->input->post('phone_number'),
	// 				'active' => $this->input->post('phone_active')	
	// 			);

	// 			$update = $this->Model_supplier->add_phone($data);
	// 			if($update == true) {
	// 				$response['success'] = true;
	// 				$response['messages'] = 'Succesfully Added';
	// 			}
	// 			else {
	// 				$response['success'] = false;
	// 				$response['messages'] = 'Error in the database while adding the phone number.';			
	// 			}
	// 		}
	// 		else {
	// 			$response['success'] = false;
	// 			foreach ($_POST as $key => $value) {
	// 				$response['messages'][$key] = form_error($key);
	// 			}
	// 		}
	// 	}
	// 	else {
	// 		$response['success'] = false;
	// 		$response['messages'] = 'Error please refresh the page again!!';
	// 	}

	// 	echo json_encode($response);
	// }

	/*
	* It removes the supplier information from the database 
	* and returns the json format operation messages
	*/
	public function remove_supplier()
	{
		$supplier_id = $this->input->post('supplier_id');
		$response = array();
		if($supplier_id) {
			$delete = $this->Model_supplier->remove($supplier_id);

			if($delete == true) {
				$response['success'] = true;
				$response['messages'] = "Successfully removed";	
			}
			else {
				$response['success'] = false;
				$response['messages'] = "Error in the database while removing the supplier information";
			}
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refersh the page again!!";
		}

		echo json_encode($response);
	}

	public function print_suppliers()
	{
		if(!in_array('printVendor', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			if(isset($_GET['selected_vendor']) && $_GET['selected_vendor'] != ""){
				$data = $this->Model_supplier->getSupplierData($_GET['selected_vendor']);
				$data = array($data);
			}
			else{
				$data = $this->Model_supplier->getSupplierData();
			}
			$company_info = $this->Model_company->getCompanyData(1);
			$user_id = $this->session->userdata('id');
			$user_data = $this->Model_users->getUserData($user_id);
			date_default_timezone_set("Asia/Karachi");
			$print_date = date('d-m-Y');

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

			<title>TBM- Supplier Print</title>
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
			
			<div class="table-responsive table-condensed table-bordered">
			<table style="width: 100%;" class="table table-condensed">
			<thead>
			<tr>
			<td><strong>#</strong></td>
			<td><strong>Image</strong></td>
			<td><strong>DateTime</strong></td>
			<td><strong>FullName</strong></td>
			<td><strong>Balance</strong></td>
			<td><strong>Address</strong></td>
			<td><strong>Contacts</strong></td>
			<td><strong>CNIC</strong></td>
			</tr>
			</thead>
			<tbody>';
			$count = 1;
			foreach ($data as $key => $value)
			{

				$contacts = '';
				$counter = 0;
				foreach (unserialize($value['phone']) as $k => $v) {
					$contacts .= $v . '<br>'; 
				}
				$img = "";
				if($value['image']){
					$img = '<img src="'.base_url().'assets/images/vendor_images/'.$value['image'].'" alt="vendor image" class="img-circle" width="50" height="50" />';
				}
				else{
					$img = '<img src="'.base_url('/assets/images/vendor_images/vendor-default-im.jpg').'" alt="vendor default image" class="img-circle" width="50" height="50" />';
				}
				$date = date('d-m-Y', strtotime($value['creation_date_time']));
				$time = date('h:i a', strtotime($value['creation_date_time']));
				$date_time = $date . ' ' . $time;
				$html .= '<tr>
				<td>'.$count++.'</td>
				<td>'.$img.'</td>
				<td>'.$date_time.'</td>
				<td>'.$value['first_name']. ' ' .$value['last_name']. '</td>
				<td>'.floatval($value['balance']).'</td>
				<td>'.$value['address'].'</td>
				<td>'.$contacts.'</td>
				<td>'.$value['cnic'].'</td>

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

	public function manage_supllier_ob_payments()
	{
		if(!in_array('recordVendorBalancePayments', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else{

			$data['page_title'] = "Manage Supplier OB payments";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');

			$result = $this->Model_supplier->getSupplierData();
			$data['supplier_data'] = $result;

			$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$data['user_permission'] = unserialize($group_data['permission']);

			$this->load->view('suppliers/manage_supllier_ob_payments', $data);
			$this->load->view('templates/footer');
		}
	}

	public function fetchSupplierPymentData($selected_supplier = null)
	{
		$result = array('data' => array());
		if($selected_supplier != ""){
			$data = $this->Model_supplier->fetchSupplierOBPayments($selected_supplier);	
		}
		else{
			$data = $this->Model_supplier->getSupplierOBPaymentsData();
		}

		$counter = 1;
		foreach ($data as $key => $value) {

			$buttons = '';
			if(in_array('viewVendorBalancePayments', $this->permission)){

				$buttons .= '<a title="View Supplier Balance Payment" onclick="editSupplierOBPayment('.$value['id'].')" href="'.base_url("index.php/Supplier/view_supplier_payment/".$value['id']).'"><i class="glyphicon glyphicon-eye-open"></i></a>';
			}
			if(in_array('updateVendorBalancePayments', $this->permission)){

				$buttons .= ' <a title="Edit Supplier Balance Payment" onclick="editSupplierOBPayment('.$value['id'].')" data-toggle="modal" href="#editSupplierOBPaymentModal"><i class="glyphicon glyphicon-pencil"></i></a>';
			}
			if(in_array('deleteVendorBalancePayments', $this->permission)){
				$buttons .= ' <a title="Delete Supplier Opening Balance Payment" onclick="removeSupplierOBPayment('.$value['id'].')" data-toggle="modal" href="#removeSupplierOBPaymentModal"><i class="glyphicon glyphicon-trash"></i></a>
				';
			}

			
			$date = date('d-m-Y', strtotime($value['datetime_creation']));
			$time = date('h:i a', strtotime($value['datetime_creation']));
			$date_time = $date . ' ' . $time;
			$paid_by = '';
			if($value['paid_by'] == 1)
			{
			  $paid_by = "System TBM";
			}
			elseif($value['paid_by'] == 2)
			{
			  $paid_by = "Vendor";
			}
			$vendor_data = $this->Model_supplier->getSupplierData($value['vendor_id']);
			
			$date = date('d-m-Y', strtotime($value['datetime_creation']));
            $time = date('h:i a', strtotime($value['datetime_creation']));
            $datetime = $date . ' ' . $time;

			$result['data'][$key] = array(
				$counter++,
				$datetime,
				$vendor_data['first_name']. ' '.$vendor_data['last_name'],
				floatval($value['paid_amount']),
				$value['payment_note'],
				$paid_by,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	public function view_supplier_payment($paymentId)
	{
		if(!in_array('viewVendorBalancePayments', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else{
			$data['payment_data'] = $this->Model_supplier->getSupplierOBPaymentsData($paymentId);
			if(!empty($data['payment_data'])){
				$data['page_title'] = "View Supplier payment";
				$this->load->view('templates/header', $data);
				$this->load->view('templates/header_menu');
				$this->load->view('templates/side_menubar');
				$data['payment_data'] = $this->Model_supplier->getSupplierOBPaymentsData($paymentId);
				$user_id = $this->session->userdata('id');
				$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
				$data['user_permission'] = unserialize($group_data['permission']);

				$this->load->view('suppliers/view_supplier_payment', $data);
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

	public function create_supplier_ob_payment()
	{
		$response = array();

		$this->form_validation->set_rules('selected_supplier', 'Supplier', 'trim|required');
		$this->form_validation->set_rules('pay_amount', 'Pay Amount', 'trim|required');
		$this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|required');
		
		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

		if ($this->form_validation->run() == TRUE) 
		{
			date_default_timezone_set("Asia/Karachi");
			$date = $this->input->post('input_date');
			$time = date('H:i:s a');
			$datetime = $date . ' ' . $time;
			$selected_supplier = $this->input->post('selected_supplier');
			$pay_amount = $this->input->post('pay_amount');
			$payment_method = $this->input->post('payment_method');
			$payment_note = $this->input->post('payment_note');
			
			$vendor_data = $this->Model_supplier->getSupplierData($selected_supplier);
			$vendor_balance = $vendor_data['balance'];
			if(abs($vendor_balance) < $pay_amount){
				$response['success'] = false;
				$response['messages'] = 'Vendor Balance is less than the Payment Amount.';
				echo json_encode($response);
				return;
			}
			else
			{
				$paid_by = 0;
				if($vendor_data['balance'] > 0)
				{
					// paid by tbm
					$paid_by = 1;
				}
				elseif($vendor_data['balance'] < 0)
				{
					// paid by vendor
					$paid_by = 2;
				}
				$most_recent_order_id = 0;
				$most_recent_order_data = $this->Model_supplier->getVendorMostRecentOrderId($selected_supplier);
				if(!empty($most_recent_order_data)){
					$most_recent_order_id = $most_recent_order_data['id'];
				}
				$data = array
				(
					'vendor_id' => $selected_supplier,
					'datetime_creation' => $datetime,
					'paid_amount' => abs($pay_amount),
					'payment_method' => $payment_method,
					'payment_note' => $payment_note,
					'paid_by' => $paid_by,
					'most_recent_order_id' => $most_recent_order_id
				);
				$insert = $this->db->insert('supplier_ob_payments', $data);
				if($insert){
						// update vendor data
					$temp = 0;
					if($vendor_data['balance'] < 0)
					{
						$temp = abs($vendor_data['balance']) - $pay_amount;
						if($temp != 0){
							$temp = -$temp;
						}
					}
					elseif($vendor_data['balance'] > 0) 
					{
						$temp = $vendor_data['balance'] - $pay_amount;
					}
					$data = array
					(
						'balance' => $temp
					);
					$this->db->where("id", $selected_supplier);
					$this->db->update('supplier', $data);
					$response['success'] = true;
					$response['messages'] = 'Succesfully created';
				}
				else {
					$response['success'] = false;
					$response['messages'] = 'Error in the database while creating the supplier OB information';			
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

	public function fetchSupplierOBPaymentDataById($id)
	{
		if($id) {

			$output['data'] = $this->Model_supplier->getSupplierOBPaymentsData($id);
			$output['vendor_data'] = $this->Model_supplier->getSupplierData($output['data']['vendor_id']);
			echo json_encode($output);
		}

		return false;
	}

	public function update_supplier_ob_payment($id)
	{
		$response = array();

		if($id) 
		{
			$this->form_validation->set_rules('edit_selected_supplier', 'Supplier', 'trim|required');
			$this->form_validation->set_rules('edit_pay_amount', 'Pay Amount', 'trim|required');
			$this->form_validation->set_rules('edit_payment_method', 'Payment Method', 'trim|required');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

			if ($this->form_validation->run() == TRUE) 
			{
				date_default_timezone_set("Asia/Karachi");
				$date = $this->input->post('edit_input_date');
				$time = date('H:i:s a');
				$datetime = $date . ' ' . $time;
				$selected_supplier = $this->input->post('edit_selected_supplier');
				$pay_amount = $this->input->post('edit_pay_amount');
				$payment_method = $this->input->post('edit_payment_method');
				$payment_note = $this->input->post('edit_payment_note');

				$previous_added_data = $this->Model_supplier->getSupplierOBPaymentsData($id);
				$vendor_data = $this->Model_supplier->getSupplierData($selected_supplier);
				// update data
				if($previous_added_data['vendor_id'] == $selected_supplier)
				{
					if($previous_added_data['paid_amount'] == $pay_amount)
					{
						// Just Update Billing Info
						// can change the payment method
						$data = array
						(
							'payment_method' => $payment_method,
							'payment_note' => $payment_note,
							'datetime_modified' => $datetime
						);
						// update it
						$this->db->where('id', $id);
						$update = $this->db->update('supplier_ob_payments', $data);
						if($update){
							$response['success'] = true;
							$response['messages'] = 'Succesfully updated';
						}
						else{
							$response['success'] = false;
							$response['messages'] = 'Error occured while updating the ob payment information';
						}
					}
					else
					{
						// Update The whole Information
						if($pay_amount > (abs($vendor_data['balance']) + $previous_added_data['paid_amount']))
						{
							$response['success'] = false;
							$response['messages'] = 'Vendor Balance is less than the Payment Amount.';
							echo json_encode($response);
							return;
						}
						else
						{
								// Remove the previous data
							$temp = 0;
							if($vendor_data['balance'] < 0)
							{
								$temp = abs($vendor_data['balance']) + $previous_added_data['paid_amount'];
								if($temp != 0){
									$temp = -$temp;
								}
							}
							elseif($vendor_data['balance'] > 0) 
							{
								$temp = $vendor_data['balance'] + $previous_added_data['paid_amount'];
							}
							$data = array
							(
								'balance' => $temp
							);
							$this->db->where("id", $selected_supplier);
							$this->db->update('supplier', $data);
								// new Vendor data
							$vendor_data = $this->Model_supplier->getSupplierData($selected_supplier);
								// update the information with new data
							$paid_by = 0;
							if($vendor_data['balance'] > 0)
							{
									// paid by tbm
								$paid_by = 1;
							}
							elseif($vendor_data['balance'] < 0)
							{
									// paid by vendor
								$paid_by = 2;
							}
							$data = array
							(
								'paid_amount' => $pay_amount,
								'payment_method' => $payment_method,
								'payment_note' => $payment_note,
								'datetime_modified' => $datetime,
								'paid_by' => $paid_by
							);
							$this->db->where('id', $id);
							$update = $this->db->update('supplier_ob_payments', $data);
							if($update)
							{
									// update vendor data
								$temp = 0;
								if($vendor_data['balance'] < 0)
								{
									$temp = abs($vendor_data['balance']) - $pay_amount;
									if($temp != 0){
										$temp = -$temp;
									}
								}
								elseif($vendor_data['balance'] > 0) 
								{
									$temp = $vendor_data['balance'] - $pay_amount;
								}
								$data = array
								(
									'balance' => $temp
								);
								$this->db->where("id", $selected_supplier);
								$this->db->update('supplier', $data);
								$response['success'] = true;
								$response['messages'] = 'Succesfully updated';
							}
							else {
								$response['success'] = false;
								$response['messages'] = 'Error in the database while updating the supplier OB information';			
							}
						}
					}
				}
				else
				{
					// Remove Previous added data
					$new_vendor_data = $this->Model_supplier->getSupplierData($selected_supplier);
					$vendor_balance = $new_vendor_data['balance'];
					if(abs($vendor_balance) < $pay_amount){
						$response['success'] = false;
						$response['messages'] = 'Vendor Balance is less than the Payment Amount.';
						echo json_encode($response);
						return;
					}
					else
					{
						// remove prevoius data from vendor
						$vendor_data = $this->Model_supplier->getSupplierData($previous_added_data['vendor_id']);
						$remaining_balance = 0;
						if($previous_added_data['paid_by'] == 1)
						{
							// was paid by tbm (positive balance)
							$remaining_balance += $vendor_data['balance'] + $previous_added_data['paid_amount'];
						}
						elseif($previous_added_data['paid_by'] == 2)
						{
							// was paid by vendor (negitive balance)
							$remaining_balance += abs($vendor_data['balance']) + $previous_added_data['paid_amount'];
							$remaining_balance = -$remaining_balance;
						}
						$data = array
						(
							'balance' => $remaining_balance
						);
						$this->db->where('id', $previous_added_data['vendor_id']);
						$update = $this->db->update('supplier', $data);
						// add new vendors data
						$paid_by = 0;
						if($new_vendor_data['balance'] > 0)
						{
							// paid by tbm
							$paid_by = 1;
						}
						elseif($new_vendor_data['balance'] < 0)
						{
							// paid by vendor
							$paid_by = 2;
						}
						$data = array
						(
							'vendor_id' => $selected_supplier,
							'datetime_creation' => $datetime,
							'paid_amount' => $pay_amount,
							'payment_method' => $payment_method,
							'payment_note' => $payment_note,
							'paid_by' => $paid_by
						);
						$this->db->where('id', $id);
						$update = $this->db->update('supplier_ob_payments', $data);
						if($update){
								// update vendor data
							$temp = 0;
							if($new_vendor_data['balance'] < 0)
							{
								$temp = abs($new_vendor_data['balance']) - $pay_amount;
								if($temp != 0){
									$temp = -$temp;
								}
							}
							elseif($new_vendor_data['balance'] > 0) 
							{
								$temp = $new_vendor_data['balance'] - $pay_amount;
							}
							$data = array
							(
								'balance' => $temp
							);
							$this->db->where("id", $selected_supplier);
							$update = $this->db->update('supplier', $data);
							if($update){
								$response['success'] = true;
								$response['messages'] = 'Succesfully updated';
							}
							else{
								$response['success'] = false;
								$response['messages'] = 'Error occured while updating vendors information';
							}
						}
						else {
							$response['success'] = false;
							$response['messages'] = 'Error in the database while creating the supplier information';			
						}
					}
				}
			}
			else 
			{
				$response['success'] = false;
				foreach ($_POST as $key => $value) 
				{
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



	public function remove_supplier_ob_payment()
	{
		$supplier_payment_id = $this->input->post('supplier_payment_id');
		$response = array();
		if($supplier_payment_id) 
		{
			// update vendor data
			$payment_data = $this->Model_supplier->getSupplierOBPaymentsData($supplier_payment_id);
			$vendor_data = $this->Model_supplier->getSupplierData($payment_data['vendor_id']);
			$remaining_balance = 0;
			if($payment_data['paid_by'] == 1)
			{
				// was paid by tbm (positive balance)
				$remaining_balance = $vendor_data['balance'] + $payment_data['paid_amount'];
			}
			elseif($payment_data['paid_by'] == 2)
			{
				// was paid by vendor (negitive balance)
				$remaining_balance = abs($vendor_data['balance']) + $payment_data['paid_amount'];
				$remaining_balance = -$remaining_balance;
			}
			$data = array
			(
				'balance' => $remaining_balance
			);
			$this->db->where('id', $payment_data['vendor_id']);
			$update = $this->db->update('supplier', $data);
			if($update){
				$this->db->where('id', $supplier_payment_id);
				$delete = $this->db->delete('supplier_ob_payments');
				if($delete){
					$response['success'] = true;
					$response['messages'] = "Successfully removed";	
				}
				else{
					$response['success'] = false;
					$response['messages'] = "Error in the database while removing the supplier ob payment information";
				}
			}
			else{
				$response['success'] = false;
				$response['messages'] = "Error in the database while updating the supplier information";
			}
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refersh the page again!!";
		}
		echo json_encode($response);
	}

	public function print_supplier_ob_payment()
	{
		if(!in_array('printVendor', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			if(isset($_GET['selected_vendor']) && $_GET['selected_vendor'] != ""){
				$data = $this->Model_supplier->fetchSupplierOBPayments($_GET['selected_vendor']);
			}
			else{
				$data = $this->Model_supplier->getSupplierOBPaymentsData();
			}
			$company_info = $this->Model_company->getCompanyData(1);
			$user_id = $this->session->userdata('id');
			$user_data = $this->Model_users->getUserData($user_id);
			date_default_timezone_set("Asia/Karachi");
			$print_date = date('d-m-Y');

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

			<title>TBM- Supplier Print</title>
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
			<table style="width: 100%;" class="table table-condensed table-bordered">
			<thead>
			<tr>
			<td><strong>#</strong></td>
			<td><strong>DateTime</strong></td>
			<td><strong>Vendor Name</strong></td>
			<td><strong>Paid Amount</strong></td>
			<td><strong>Payment Method</strong></td>
			<td><strong>Payment Note</strong></td>
			<td><strong>Paid By</strong></td>
			</tr>
			</thead>
			<tbody>';
			$counter = 1;
			foreach ($data as $key => $value)
			{
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
				$vendor_data = $this->Model_supplier->getSupplierData($value['vendor_id']);

				$html .= '<tr>
				<td>'.$counter++.'</td>
				<td>'.$date_time.'</td>
				<td>'.$vendor_data['first_name']. ' '.$vendor_data['last_name'].'</td>
				<td>'.floatval($value['paid_amount']).'</td>
				<td>'.$payment_method.'</td>
				<td>'.$value['payment_note'].'</td>
				<td>'.$paid_by.'</td>

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


}
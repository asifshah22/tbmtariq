<?php
class Loan extends CI_Controller {

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
		if(!in_array('recordLoan', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/error_no_permission'); 
		}
		else{

			$data['page_title'] = "Manage Loan";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');

			$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$data['user_permission'] = unserialize($group_data['permission']);
			$data['vendor_data'] = $this->Model_supplier->getSupplierData();

			$this->load->view('loan/index', $data);
			$this->load->view('templates/footer');
		}
	}

	public function fetchLoanData()
	{
		$result = array('data' => array());

		$data = $this->Model_loan->getLoanData();
		$counter = 1;
		foreach ($data as $key => $value) {

			// button
			$buttons = '';

			if(in_array('viewLoan', $this->permission))
			{
				$buttons .= '<a title="View Given Loans" href="'.base_url().'index.php/Loan/view_given_loans/'.$value['id'].'"><i class="glyphicon glyphicon-eye-open"></i></a>';
			}
			if(in_array('deleteLoan', $this->permission))
			{
				$buttons .= ' <a title="Delete Loan" onclick="removeFunc('.$value['id'].')" data-toggle="modal" href="#removeModal"><i class="fa fa-trash"></i></a>';
			}
			$vendor_data = $this->Model_loan->checkVendor($value['supplier_id']);
			$vendor_name = $vendor_data['first_name']. ' '. $vendor_data['last_name'];

			if($value['paid_status'] == 1){
				$paid_status = '<span class="label label-success">Paid</span>';				
			}
			else if($value['paid_status'] == 0){
				$paid_status = '<span class="label label-warning">Unpaid</span>';
			}


			$result['data'][$key] = array(
				$counter++,
				$vendor_name,
				floatval($value['amount']),
				floatval($value['installment_amount']),
				floatval($value['paid_amount']),
				floatval($value['remaining_amount']),
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	public function create_loan()
	{	
		$response = array();

		$this->form_validation->set_rules('select_vendor', 'Select Supplier', 'trim|required');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|required');
		$this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|required');
		$this->form_validation->set_rules('datepicker_add', 'Date', 'trim|required');
		$this->form_validation->set_rules('installment_amount', 'Installment Amount', 'trim|required');

		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

		if ($this->form_validation->run() == TRUE) 
		{
	        // if vendor id does not exit
			$vendor_exist = $this->Model_loan->checkVendor($this->input->post('select_vendor'));
			if($vendor_exist)
			{
	        	// check if already taking loan but not paid
				$exist_unpaid_loan = $this->Model_loan->checkExistingUnpaidLoan($this->input->post('select_vendor'));
				if(empty($exist_unpaid_loan))
				{
					$data = array(
						'supplier_id' => $this->input->post('select_vendor'),
						'amount' => $this->input->post('amount'),
						'installment_amount' => $this->input->post('installment_amount'),
						'remaining_amount' => $this->input->post('amount'),
						'paid_status' => 0,
						'paid_amount' => 0
					);

					$create = $this->db->insert('loan', $data);
					$last_id = $this->db->insert_id(); 
					if($create) {
						date_default_timezone_set("Asia/Karachi");
						$this->load->helper('date');
						$data = array(
							'loan_id' => $last_id,
							'loan_date' =>  $this->input->post('datepicker_add'),
							'amount' => $this->input->post('amount'),
							'installment_amount' => $this->input->post('installment_amount'),
							'payment_method' => $this->input->post('payment_method'),
							'payment_note' => $this->input->post('payment_note'),
						);
						$create = $this->db->insert('vendor_loan', $data);
						if($create)
						{
							$response['success'] = true;
							$response['messages'] = 'Succesfully created';
						}else
						{
							$response['success'] = false;
							$response['messages'] = 'An Error has occured please inform the admin!';
						}
					}
					else {
						$response['success'] = false;
						$response['messages'] = 'Error in the database while creating the Loan information';			
					}
				}
				else
				{
					$response['success'] = false;
					$response['messages'] = 'Sorry this vendor already have an unpaid Loan amount. Visit view to give more Loan';
				}
			}
			else{
				$response['success'] = false;
				$response['messages'] = 'Vendor with ID does not exist. Please confirm the Vendor ID';
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

	public function create_another_loan()
	{
		$response = array();
		$this->form_validation->set_rules('amount_2', 'Amount', 'trim|required');
		$this->form_validation->set_rules('installment_amount_2', 'Installment Amount', 'trim|required');
		$this->form_validation->set_rules('payment_method_2', 'Payment Method', 'trim|required');
		$this->form_validation->set_rules('datepicker_add_2', 'Date', 'trim|required');
		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
		if($this->form_validation->run() == TRUE)
		{

			$loan_id = $this->input->post('loan_id');
			// if edit given loan is less than the installment amount or paid amount
			$loan_data = $this->Model_loan->getLoanData($loan_id);
			
			$inputed_loan_amount = $this->input->post('amount_2');
			$inputed_installment_amount = $this->input->post('installment_amount_2');
			if(($inputed_loan_amount + $loan_data['amount']) < $inputed_installment_amount)
			{
				//errror
				$response['success'] = false;
				$response['messages'] = 'Installment amount should be less than the Overall Loan Amount!.';
			}
			//remaining 
			else if(($loan_data['amount'] - $loan_data['paid_amount'] + $inputed_loan_amount) < $inputed_installment_amount)
			{
				$response['success'] = false;
				$response['messages'] = 'Installment Amount is greater than Remaining Amount. Unable to Add Another Loan!.';
			}
			else
			{	
				// add the loan amount in existing loan amount and also add this into vendor_loan
				$prev_loan_data = $this->Model_loan->getLoanData($loan_id);
				$pre_loan_amount = $prev_loan_data['amount'];
				$pre_remaining_loan_amount = $prev_loan_data['remaining_amount'];

				$data = array(
					'amount' => $pre_loan_amount + $this->input->post('amount_2'),
					'remaining_amount' => $pre_remaining_loan_amount + $this->input->post('amount_2'),
					'installment_amount' => $this->input->post("installment_amount_2")
				);
				$this->db->where('id', $prev_loan_data['id']);
				$update = $this->db->update('loan', $data);
				if($update){
					date_default_timezone_set("Asia/Karachi");
					$this->load->helper('date');
					$data = array(
						'loan_date' => $this->input->post('datepicker_add_2'),
						'amount' => $this->input->post('amount_2'),
						'installment_amount' => $this->input->post('installment_amount_2'),
						'payment_method' => $this->input->post('payment_method_2'),
						'payment_note' => $this->input->post('payment_note_2'),
						'loan_id' => $loan_id
					);
					$create = $this->db->insert('vendor_loan', $data);
					if($create){
						$response['success'] = true;
						$response['messages'] = 'Succesfully Added';
					}
				}
				else{
					$response['success'] = false;
					$response['messages'] = 'Sorry an Error has occured try again!.';
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

	public function update_vendor_loan($id)
	{
		$response = array();
		$this->form_validation->set_rules('edit_amount', 'Amount', 'trim|required');
		$this->form_validation->set_rules('edit_installment_amount', 'Installment Amount', 'trim|required');
		$this->form_validation->set_rules('edit_payment_method', 'Payment Method', 'trim|required');
		$this->form_validation->set_rules('datepicker_edit', 'Date', 'trim|required');
		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
		if($this->form_validation->run() == TRUE)
		{
			$loan_id = $this->Model_loan->getVendorsLoanById($id)['loan_id'];
			// if edit given loan is less than the installment amount or paid amount
			$loan_data = $this->Model_loan->getLoanData($loan_id);
			
			$inputed_loan_amount = $this->input->post('edit_amount');
			$inputed_installment_amount = $this->input->post('edit_installment_amount');
			if(($inputed_loan_amount + $loan_data['amount']) < $inputed_installment_amount)
			{
				//errror
				$response['success'] = false;
				$response['messages'] = 'Installment amount should be less than the Overall Loan Amount!.';
			}
			//remaining 
			else if(($loan_data['amount'] - $loan_data['paid_amount']) < $inputed_installment_amount)
			{
				$response['success'] = false;
				$response['messages'] = 'Installment Amount is greater than Remaining Amount. Unable to update Loan!.';
			}
			else if($inputed_installment_amount > ($loan_data['amount'] - $loan_data['paid_amount']))
			{
				$response['success'] = false;
				$response['messages'] = 'Installment Amount should be less than paid amount!.';
			}
			else{
				
				// add the loan amount in existing loan amount and also add this into vendor_loan
				$pre_ven_loan_data = $this->Model_loan->getVendorsLoanById($id);
				$prev_loan_data = $this->Model_loan->getLoanData($pre_ven_loan_data['loan_id']);
				$pre_loan_amount = $prev_loan_data['amount'];
				$pre_remaining_loan_amount = $prev_loan_data['remaining_amount'];
					// subtract the amount which was given before and add new amount
				$temp_loan_amount = $pre_loan_amount - $pre_ven_loan_data['amount'];
				$temp_remaining_loan_amount = $pre_remaining_loan_amount - $pre_ven_loan_data['amount'];
					// add new amount into the loan table
				$temp_loan_amount = $temp_loan_amount + $this->input->post('edit_amount');
				$temp_remaining_loan_amount = $temp_remaining_loan_amount + $this->input->post('edit_amount');
				$vendor_latest_loan = $this->Model_loan->getVendorLatestLoan($loan_id = $prev_loan_data['id']);

				if($vendor_latest_loan['latest_vendor_loan_id'] == $id)
				{
					$data = array(
						'amount' => $temp_loan_amount,
						'installment_amount' => $this->input->post('edit_installment_amount'),
						'remaining_amount' => $temp_remaining_loan_amount
					);
				}
				else{
					$data = array(
						'amount' => $temp_loan_amount,
						'remaining_amount' => $temp_remaining_loan_amount
					);
				}
				$this->db->where('id', $prev_loan_data['id']);
				$update = $this->db->update('loan', $data);
				if($update){
					date_default_timezone_set("Asia/Karachi");
					$this->load->helper('date');
					$data = array(
						'loan_date' => $this->input->post('datepicker_edit'),
						'amount' => $this->input->post('edit_amount'),
						'installment_amount' => $this->input->post('edit_installment_amount'),
						'payment_method' => $this->input->post('edit_payment_method'),
						'payment_note' => $this->input->post('edit_payment_note'),
					);
					$this->db->where('id', $id);
					$update = $this->db->update('vendor_loan', $data);
					if($update){
						$response['success'] = true;
						$response['messages'] = 'Succesfully updated';
					}
				}
				else{
					$response['success'] = false;
					$response['messages'] = 'Sorry an Error has occured try again!.';
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

	public function remove_vendor_loan()
	{
		$response = array();
		$id = $this->input->post('vendor_loan_id');
		if($id) 
		{
			// vendor loan
			$pre_ven_loan_data = $this->Model_loan->getVendorsLoanById($id);
			// loan
			$prev_loan_data = $this->Model_loan->getLoanData($pre_ven_loan_data['loan_id']);
			// check if this loan has loan deduction and amount is less than actual amount than error while deleting the loan
			$loan_deductions = $this->Model_loan->fetchLoanDeductions($pre_ven_loan_data['loan_id']);
			if(!empty($loan_deductions) AND ($prev_loan_data['remaining_amount'] < $pre_ven_loan_data['amount']))
			{
				$response['success'] = false;
				$response['messages'] = "Loan has Loan deductions. System is unable to delete this loan";
			}
			else
			{
				$pre_loan_amount = $prev_loan_data['amount'];
				$pre_remaining_loan_amount = $prev_loan_data['remaining_amount'];
				// subtract the amount which was given before and add new amount
				$temp_loan_amount = $pre_loan_amount - $pre_ven_loan_data['amount'];
				$temp_remaining_loan_amount = $pre_remaining_loan_amount - $pre_ven_loan_data['amount'];

				// latest vendor loan
				$vendor_latest_loan = $this->Model_loan->getVendorLatestLoan($loan_id = $prev_loan_data['id']);
				// deleting latest row
				if($vendor_latest_loan['latest_vendor_loan_id'] == $id)
				{
					// 2nd latest vendor laon
					$vendor_2nd_latest_loan = $this->Model_loan->getVendorSecondLatestLoan($loan_id = $prev_loan_data['id']);
					// have other loans
					if(!empty($vendor_2nd_latest_loan))
					{
						$installment_amount = $this->Model_loan->getVendorsLoanById($vendor_2nd_latest_loan['latest_vendor_loan_id'])['installment_amount'];
						$data = array(
							'amount' => $temp_loan_amount,
							'installment_amount' => $installment_amount,
							'remaining_amount' => $temp_remaining_loan_amount
						);
					}
					else
					{
						// have no other loans
						$data = array(
							'amount' => $temp_loan_amount,
							'installment_amount' => 0,
							'remaining_amount' => $temp_remaining_loan_amount
						);
					}
				}
				else
				{
					// not deleting latest row
					$data = array(
						'amount' => $temp_loan_amount,
						'remaining_amount' => $temp_remaining_loan_amount
					);
				}
				// update loan
				$this->db->where('id', $prev_loan_data['id']);
				$update = $this->db->update('loan', $data);
				if($update){
					$this->db->where('id', $id);
					$delete = $this->db->delete('vendor_loan');
					if($delete){
						$response['success'] = true;
						$response['messages'] = "Successfully removed";
					}
					else{
						$response['success'] = false;
						$response['messages'] = "Error Occured while updating vendor Loan Table. Contac Maintenance Team.";
					}
				}
				else{
					$response['success'] = false;
					$response['messages'] = "Error in the database while removing the Given Loan information";
				}
			}
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refersh the page again!!";
		}
		echo json_encode($response);
	}

	public function view_given_loans($loan_id)
	{
		if(!in_array('viewLoan', $this->permission)) {
            $data['page_title'] = "No Permission";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/forbidden_access');
        }
        else
        {
        	$data['vendors_loan'] = $this->Model_loan->getVendorsLoan($loan_id);
        	if(!empty($data['vendors_loan'])){
        		$data['page_title'] = "Given Loans";
        		$this->load->view('templates/header', $data);
        		$this->load->view('templates/header_menu');
        		$this->load->view('templates/side_menubar');

        		$user_id = $this->session->userdata('id');
        		$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
        		$data['user_permission'] = unserialize($group_data['permission']);

        		$data['vendors_loan'] = $this->Model_loan->getVendorsLoan($loan_id);
        		$this->load->view('loan/view_given_loans', $data);
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

	public function fetchVendorLoanData($loan_id)
	{
		$result = array('data' => array());

		$data = $this->Model_loan->getVendorsLoan($loan_id);
		$counter = 1;
		foreach ($data as $key => $value) {

			// button
			$buttons = '';
			if(in_array('updateLoan', $this->permission))
			{
				$buttons .= ' <a title="Edit Given Loan" onclick="editFunc('.$value['vendor_loan_id'].')" data-toggle="modal" href="#editModal"> <i class="glyphicon glyphicon-pencil"></i></a>';
			}
			if(in_array('deleteLoan', $this->permission))
			{
				$buttons .= ' <a title="Delete Given Loan" onclick="removeFunc('.$value['vendor_loan_id'].')" data-toggle="modal" href="#removeModal"><i class="glyphicon glyphicon-trash"></i></a>';
			}
			$payment_note = ($value['payment_note']) ? $value['payment_note'] : 'Not Provided';

			$result['data'][$key] = array(
				$counter++,
				$value['loan_date'],
				floatval($value['loan_amount']),
				floatval($value['vendor_installment_amount']),
				$value['payment_method'],
				$payment_note,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	public function fetchVendorLoanById($id)
	{
		if($id) {
			$data = $this->Model_loan->getVendorsLoanById($id);
			echo json_encode($data);
		}

		return false;
	}

	public function remove_loan()
	{
		$loan_id = $this->input->post('loan_id');
		$loan_deductions = $this->Model_loan->fetchLoanDeductions($loan_id);
		if(!empty($loan_deductions)){
			$response['success'] = false;
			$response['messages'] = "Loan has Loan deductions. System is unable to delete this loan.";
		}
		else{
			$response = array();
			if($loan_id) {
				$vendor_loan = $this->Model_loan->getVendorsLoan($loan_id);
				foreach ($vendor_loan as $key => $value) 
				{
					$vendor_loan_id = $value['vendor_loan_id'];
					$this->db->where('id', $vendor_loan_id);
					$this->db->delete('vendor_loan');
				}
				$delete = $this->Model_loan->remove($loan_id);
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
		}
		echo json_encode($response);
	}

	public function loan_remaining_summary()
	{
		if(!in_array('recordRemainingLoanSummary', $this->permission)) {
            $data['page_title'] = "No Permission";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/forbidden_access');
        }
        else
        {
        	$data['page_title'] = "Remaining Loan Summary";
        	$this->load->view('templates/header', $data);
        	$this->load->view('templates/header_menu');
        	$this->load->view('templates/side_menubar');
        	$data['vendor_data'] = $this->Model_loan->getVendorData();
        	
        	$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$data['user_permission'] = unserialize($group_data['permission']);

        	$this->load->view('loan/loan_remaining_summary', $data);
        	$this->load->view('templates/footer');
        }
		
	}

	public function loan_deductions()
	{
		if(!in_array('recordLoanDeductionsSummary', $this->permission)) {
            $data['page_title'] = "No Permission";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/forbidden_access');
        }
        else
        {
        	$data['page_title'] = "Loan Deduction";
        	$this->load->view('templates/header', $data);
        	$this->load->view('templates/header_menu');
        	$this->load->view('templates/side_menubar');

        	if(isset($_GET['selected_vendor']) && $_GET['selected_vendor'] != ""){
				$data = $this->Model_loan->fetchVendorsLoan($_GET['selected_vendor']);
			}
			else{
				$data = $this->Model_loan->getLoanData();
			}

        	$loan_ids = array();
        	foreach ($data as $key => $value) {
        		if (!in_array($value['id'], $loan_ids)) {
        			array_push($loan_ids, $value['id']);
        		}
        	}
        	$data['result'] = $loan_ids;
        	$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$data['user_permission'] = unserialize($group_data['permission']);

        	$this->load->view('loan/loan_deductions', $data);
        	$this->load->view('templates/footer');
        }
	}

	public function print_vendors_loan()
	{
		if(!in_array('printLoan', $this->permission)) {
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
	        $data = $this->Model_loan->getLoanData();
	        
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
	                        
	                        <title>TBM - Vendors Loan Print</title>
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
	                                    
	                                            <div class="table-bordered table-striped table-condensed">
	                                                <table class="table table-condensed">
	                                                    <thead>
	                                                        <tr>
	                                                        	<th><strong>#</strong></th>
	                                                            <th><strong>Vendor Name</strong></th>
	                                                        	<th><strong>Amount</strong></th>
	                                                        	<th><strong>Installment Amount</strong></th>
	                                                        	<th><strong>Paid Amount</strong></th>
	                                                        	<th><strong>Remaining Amount</strong></th>
	                                                        </tr>
	                                                    </thead>
	                                                    <tbody>'; 
	                                            $counter = 1;
	                                            foreach ($data as $key => $value) {
	                                            	$vendor_data = $this->Model_loan->checkVendor($value['supplier_id']);
	                                            	$vendor_name = $vendor_data['first_name']. ' '. $vendor_data['last_name'];
	                                            
	                                            	$html .= '<tr>
	                                                	<td>'.$counter++.'</td>
	                                                	<td style="text-transform:capitalize">'.$vendor_name.'</td>
	                                                	<td>'.floatval($value['amount']).'</td>
	                                                	<td>'.floatval($value['installment_amount']).'</td>
	                                                	<td>'.floatval($value['paid_amount']).'</td>
	                                                	<td>'.floatval($value['remaining_amount']).'</td>
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
	                <title>TBM Automobile Private Ltd | Vendors Loan Print</title>
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
		                          <th style="width:8%"><strong>#</strong></th>
		                          <th style="width:22%"><strong>Vendor Name</strong></th>
		                          <th style="width:22%"><strong>Amount</strong></th>
		                          <th style="width:22%"><strong>Installment Amount</strong></th>
		                          <th style="width:22%"><strong>Status</strong></th>
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

	public function loan_history()
	{
		if(!in_array('viewLoanHistory', $this->permission)) 
		{
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			if(isset($_GET['selected_vendor']))
			{
				$result = array('data' => array());

				$data = $this->Model_loan->getVendorData($_GET['selected_vendor']);
				$loan_ids = array();
				foreach ($data as $key => $value) 
				{
					if(!in_array($value['loan_id'], $loan_ids)){
						array_push($loan_ids, $value['loan_id']);
					}
        		} 

		        $data['page_title'] = "Loan - Vendor Loan History";
		        $data['heading'] = "Vendor Loan History";

		        $data['result'] = $loan_ids;
		        
		        $user_id = $this->session->userdata('id');
		        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
		        $data['user_permission'] = unserialize($group_data['permission']);

		        $this->load->view('templates/header', $data);
		        $this->load->view('templates/header_menu');
		        $this->load->view('templates/side_menubar');

		        $this->load->view('loan/loan_history');  
    		}
	    	else
	    	{
	    		$data['page_title'] = "Loan - Vendor Loan History";
		    	$data['heading'] = "Vendor Loan History";
		    	$data['result'] = array();

		    	$user_id = $this->session->userdata('id');
		    	$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
		    	$data['user_permission'] = unserialize($group_data['permission']);

		    	$this->load->view('templates/header', $data);
		    	$this->load->view('templates/header_menu');
		    	$this->load->view('templates/side_menubar');
		    	$this->load->view('loan/loan_history');
	    	}
		}
	}

	public function print_loan_history()
  	{
    	if(!in_array('printLoanHistory', $this->permission)) {
            $data['page_title'] = "No Permission";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/forbidden_access');
        }
    	else
    	{
      		if(isset($_GET['selected_vendor']))
			{
				$result = array('data' => array());

				$data = $this->Model_loan->getVendorData($_GET['selected_vendor']);
				$loan_ids = array();
				foreach ($data as $key => $value) 
				{
					if(!in_array($value['loan_id'], $loan_ids)){
						array_push($loan_ids, $value['loan_id']);
					}
        		} 

		        $data['result'] = $loan_ids; 
    		}
	    	else
	    	{
		    	$data['result'] = array();
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
	              <title>TBM - Vendor Loan History</title>
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
	                          <table class="table table-condensed table-bordered table-striped">
	                            <thead>
	                              <tr>
		                              <th>#</th>
		                              <th>Vendor</th>
		                              <th>Given Loans</th>
		                              <th>Loan Deductions</th>
	                              </tr>
	                            </thead>
	                            <tbody bgcolor="#eaeaea">';
	                                
	                                $counter = 1;
	                                foreach ($data['result'] as $key => $value) 
	                                {
	                                	$overall_loan_data = $this->Model_loan->getLoanData($value);
	                                	$loan_deductions = $this->Model_loan->fetchLoanDeductions($value);
	                                	$vendor_data = $this->Model_loan->checkVendor($overall_loan_data['supplier_id']);
	                                	$vendor_name = $vendor_data['first_name']. ' '. $vendor_data['last_name'];

	                        $html .= '<tr>
	                                    <td>'.$counter++. ' — ' .'</td>
	                                    <td style="text-transform: capitalize;">'.$vendor_name.'</td>
	                                    <td>
	                                      <table class="table table-bordered table-striped example2">
	                                        <thead>
	                                          <tr>
	                                            <th>Date</th>
	                                            <th>Loan Amount</th>
	                                            <th>Installment Amount</th>
	                                            <th>Payment</th>
	                                            
	                                          </tr>
	                                        </thead>
	                                        <tbody>';
	                                        $vendor_loan_data = $this->Model_loan->getVendorsLoan($value);
	                                        $total_loan_amount = 0;
	                                        foreach($vendor_loan_data as $k => $v)
	                                        {
	                                        	$total_loan_amount += $v['loan_amount'];
	                                        	
	                                        	$html .='
	                                        	<tr>
	                                        		<td>'.$v['loan_date'].'</td>
	                                        		<td>'.floatval($v['loan_amount']).'</td>
	                                        		<td>'.floatval($v['vendor_installment_amount']).'</td>
	                                        		<td>'.$v['payment_method'].'</td>
	                                        	</tr>';
	                                        }
	                        $html .= '                
	                                        </tbody>

	                                      </table>
	                                      <p>
	                                        <span style="background-color: #ffffff">
	                                          <strong>Total Loan Amount: '.floatval($total_loan_amount).'</strong>
	                                        </span>
	                                      </p>
	                                    </td>
	                                    <td>
	                                      <table class="table table-bordered table-striped example3">
	                                        <thead>
	                                          <tr>
	                                          	<th>#</th>
	                                          	<th>Date Time</th>
		                                        <th>Deduction Amount</th>
	                                          </tr>
	                                        </thead>
	                                        <tbody>';

	                                          $count = 1;
	                                          $loan_deductions = $this->Model_loan->fetchLoanDeductions($value);
	                                          $total_deduction_amount = 0;
	                                          foreach($loan_deductions as $k => $v)
	                                          {
	                                          	$total_deduction_amount += $v['deduction_amount'];
	                                          	$purchase_order_data = $this->Model_products->getPurchaseOrdersData($v['order_id']);
	                                          	$date = date('d-m-Y', strtotime($purchase_order_data['datetime_created']));
	                                          	$time = date('h:i a', strtotime($purchase_order_data['datetime_created']));
	                                          	$date_time = $date . ' ' . $time;
	                                          	$html .='
	                                          		<tr>
	                                          			<td>'.$count++.'</td>
	                                          			<td>'.$date_time.'</td>
	                                          			<td>'.floatval($v['deduction_amount']).'</td>
	                                          		</tr>';
	                                          }
	                                          $html .= '
	                                        </tbody>
	                                      </table>
	                                      <p>
		                                      <span style="background-color: #ffffff">
		                                      <strong>Total Deduction Amount: '.floatval($total_deduction_amount).'</strong>
		                                      </span>
		                                      </p>
		                                      <p>
		                                      <span style="background-color: #ffffff">
		                                      <strong>Remaining Amount: '.floatval($total_loan_amount - $total_deduction_amount).'</strong>
		                                      </span>
	                                      </p>
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

    public function print_remaining_loan_summary()
	{
		if(!in_array('printRemainingLoanSummary', $this->permission)) {
            $data['page_title'] = "No Permission";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/forbidden_access');
        }
        else
        {

	        $data = $this->Model_loan->getVendorData();
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
	                        
	                        <title>TBM - Remaining Loan Summary</title>
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
	                                                <table style="width: 100%;" class="table table-condensed table-bordered table-striped">
	                                                    <thead>
	                                                        <tr>
	                                                            <td><strong>#</strong></td>
	                                                            <td><strong>Vendor</strong></td>
	                                                            <td><strong>Loan Amount</strong></td>
	                                                            <td><strong>Installment</strong></td>
	                                                            <td><strong>Paid</strong></td>
	                                                            <td><strong>Remaining</strong></td>
	                                                            <td><strong>Vendor Image</strong></td>
	                                                        </tr>
	                                                    </thead>
	                                                    <tbody>';
	                                            $counter = 1;
	                                            $total_amount = 0;
	                                            foreach ($data as $key => $value) {
	                                                $total_amount += $value['remaining_amount'];
											        
	                                                $html .= '<tr>
	                                                    <td>'.$counter++.'</td>
	                                                    <td>'.$value['first_name'].' '.$value['last_name'].'</td>
	                                                    <td>'.floatval($value['amount']).'</td>
	                                                    <td>'.floatval($value['installment_amount']).'</td>
	                                                    <td>'.floatval($value['paid_amount']).'</td>
	                                                    <td>'.floatval($value['remaining_amount']).'</td>';
	                                                    if($value['image'] == ""){
	                                                    	$image = '<img src="'.base_url('/assets/images/vendor_images/vendor-default-im.jpg').'" alt="vendor default image" class="img-circle" width="50" height="50" />';
	                                                    }
									                    elseif($value['image'] != ""){
									                    	$image = '<img src="'.base_url('/assets/images/vendor_images/'.$value['image'].'').'" alt="Vendor image" class="img-circle" width="60" height="60" />';
									                    }
									                    
	                                                $html .= '
	                                                	<td>'.$image.'</td>    
	                                                </tr>';
	                                            }
	                                        $html .='
	                                                
	                                            </tbody>

	                                                </table>
	                                                <div style="margin-top: 5px">
											        	<span>
											          		<strong>Total Remaining Amount:</strong>
											        	</span>
											          <span style="font-weight: bold;">'.floatval($total_amount).'</span>
											          
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

    public function print_loan_deductions()
	{
		if(!in_array('printLoanDeductionsSummary', $this->permission)) {
            $data['page_title'] = "No Permission";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/forbidden_access');
        }
        else
    	{
      		$data['page_title'] = "Loan Deduction";
        	$loan_deductions = $this->Model_loan->getLoanData();
        	if(isset($_GET['selected_vendor']) && $_GET['selected_vendor'] != ""){
				$loan_deductions = $this->Model_loan->fetchVendorsLoan($_GET['selected_vendor']);
			}
			else{
				$loan_deductions = $this->Model_loan->getLoanData();
			}
        	$loan_ids = array();
        	foreach ($loan_deductions as $key => $value) {
        		if (!in_array($value['id'], $loan_ids)) {
        			array_push($loan_ids, $value['id']);
        		}
        	}
        	$data['result'] = $loan_ids;

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
	              <title>TBM - Loan Deductions Summary</title>
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
		                              <th>#</th>
		                              <th>Loan</th>
		                              <th>Deductions</th>
	                              </tr>
	                            </thead>
	                            <tbody bgcolor="#eaeaea">';
	                                
	                            $counter = 1;
	                            $total_remaining_loan = 0;
	                                foreach ($data['result'] as $key => $value) 
	                                {
	                                	$loan_data = $this->Model_loan->getLoanData($value);
	                                	$loan_deductions = $this->Model_loan->fetchLoanDeductions($value);
	                                	$vendor_data = $this->Model_loan->checkVendor($loan_data['supplier_id']);
	                                	$vendor_name = $vendor_data['first_name']. ' '. $vendor_data['last_name'];

	                        $html .= '<tr>
	                                    <td>'.$counter++. ' — ' .'</td>
	                                    <td>
	                                      <table class="table table-bordered table-striped example2">
	                                        <thead>
	                                          <tr>
		                                          <th>Vendor Name</th>
		                                          <th>Given Amount</th>
	                                          </tr>
	                                        </thead>
	                                        <tbody>
		                                        <tr>
		                                        	<td>'.$vendor_name.'</td>
						                            <td>'.floatval($loan_data['amount']).'</td>
						                        </tr>
	                                        </tbody>
	                                      </table>
	                                    </td>
	                                    <td>
	                                      <table class="table table-bordered table-striped example3">
	                                        <thead>
	                                          <tr>
		                                          <th>#</th>
		                                          <th>Bill_no</th>
		                                          <th>DateTime</th>
		                                          <th>Deduction Amount</th>
	                                          </tr>
	                                        </thead>
	                                        <tbody>';

		                                        $count = 1; 
		                                        $sum_deduction_amount = 0;
		                                        foreach($loan_deductions as $key => $value)
		                                        {
		                                        	$sum_deduction_amount += $value['deduction_amount'];
		                                        	$purchase_order_data = $this->Model_products->getPurchaseOrdersData($value['order_id']);
		                                        	$date = date('d-m-Y', strtotime($purchase_order_data['datetime_created']));
		                                        	$time = date('h:i a', strtotime($purchase_order_data['datetime_created']));
		                                        	$date_time = $date . ' ' . $time;
		                                        	$html .='
		                                        	<tr>
			                                        	<td>'.$count++.'</td>
			                                        	<td>'.$purchase_order_data['bill_no'].'</td>
			                                        	<td>'.$date_time.'</td>
			                                        	<td>'.floatval($value['deduction_amount']).'</td>
		                                        	</tr>';
		                                        }
		                                        $html .= '
	                                        </tbody>
	                                      </table>
	                                      <p>
		                                      <span style="background-color: #ffffff">
		                                      	<strong>Total Deductions: '.floatval($sum_deduction_amount).'</strong>
		                                      </span>
		                                      </p>
		                                      <p>
		                                      <span style="background-color: #ffffff">
		                                      	<strong>Remaining Amount: '.floatval($loan_data['amount'] - $sum_deduction_amount).'</strong>
		                                      </span>';
		                                      $total_remaining_loan += $loan_data['amount'] - $sum_deduction_amount;
		                                      $html .= '
	                                      </p>
	                                    </td>
	                                  </tr>';
	                                }//endforeach
	                              $html .='                    
	                            </tbody>
	                          </table>
	                          <div style="margin: 30px">
		                          <span><b>Total Remaining Loan: </b></span>
		                          <span><b>'.floatval($total_remaining_loan).'</b></span>
	                          </div>
	                        </div>
	                      </div>
	                    
	                </div>
	              </div>
	            </body>
	            <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>
	            <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>
	            <script>
	              $(function () {
	                $("#example1").DataTable({
	                  responsive: true
	                });
	                $(".example2").DataTable({
	                  "paging"      : false,
	                  "lengthChange": false,
	                  "searching"   : false,
	                  "ordering"    : true,
	                  "info"        : false,
	                  "autoWidth"   : false
	                });
	                $(".example3").DataTable({
	                  "paging"      : false,
	                  "lengthChange": false,
	                  "searching"   : false,
	                  "ordering"    : true,
	                  "info"        : false,
	                  "autoWidth"   : false
	                });
	              });
	            </script>
	          </html>';
      		echo $html;
    	}
  	}









}
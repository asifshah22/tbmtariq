<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller 
{

	var $permission = array();

	public function __construct() 
	{
		parent::__construct();

		$group_data = array();
		if(empty($this->session->userdata('logged_in'))) 
		{
			$session_data = array('logged_in' => FALSE);
			$this->session->set_userdata($session_data);
		}
		else {
			$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$this->data['user_permission'] = unserialize($group_data['permission']);
			$this->permission = unserialize($group_data['permission']);
		}
	}

	// If user is not loggeed in
	// Redirect to login page
	// else redirect to home page
	public function index()
	{
		if($this->session->userdata('logged_in')){
			redirect('/Dashboard/index', 'refresh');
		}
		else{
			$this->login_user();
		}
	}
	// working great
	// public function test_email_send()
	// {
	// 	$this->load->library('email');

	// 	$config['protocol']    = 'smtp';
	// 	$config['smtp_host']    = 'ssl://smtp.gmail.com';
	// 	$config['smtp_port']    = '465';
	// 	$config['smtp_timeout'] = '7';
	// 	$config['smtp_user']    = 'bipmsystem@gmail.com';
	// 	$config['smtp_pass']    = 'Pms@Management@System@123';
	// 	$config['charset']    = 'utf-8';
	// 	$config['newline']    = "\r\n";
	//     $config['mailtype'] = 'text'; // or html
	//     $config['validation'] = TRUE; // bool whether to validate email or not      

	//     $this->email->initialize($config);


	//     $this->email->from('bipmsystem@gmail.com', 'myname');
	//     $this->email->to('muadeelsafdar@gmail.com');

	//     $this->email->subject('Email Test');
	//     $this->email->message('Testing the email class.');  

	//     $this->email->send();

	//     echo $this->email->print_debugger();
	// }

	// Login method
	// Email  
	// password
	public function login_user()
	{
		$this->load->model('Model_auth');
		$user_username = $this->input->post('username');
		$user_password = $this->input->post('password');
		
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if($this->form_validation->run() == FALSE)
		{
			$data['user_username'] = $user_username;
			$data['user_password'] = $user_password;
			$data['errors'] = "";
			$this->load->view('user/login', $data);
		}
		else{
			$username_exists = $this->Model_auth->check_username($this->input->post('username'));
			if($username_exists == TRUE) {
				$login = $this->Model_auth->login($user_username, $user_password);
				if($login) {

					$logged_in_sess = array(
						'id' => $login['id'],
						'username'  => $login['username'],
						'email'     => $login['email'],
						'logged_in' => TRUE
				 	);

				 	$this->session->set_userdata($logged_in_sess);
					redirect('/Dashboard/index', 'refresh');
					 
				}
				else {
					$data['user_username'] = $user_username;
					$data['user_password'] = $user_password;	
					$data['errors'] = 'Incorrect username/password combination';
					$this->load->view('user/login', $data);
				}
			}
			else {
				$data['user_username'] = $user_username;
				$data['user_password'] = $user_password;
				$data['errors'] = 'Username does not exists';
				$this->load->view('user/login', $data);
			}
		}

	}

	// Forgot Password via email

	public function forgot_password()
	{
		$this->form_validation->set_rules('email', 'Email', 'required');
		if($this->form_validation->run() == FALSE){
			$this->load->view('user/forgot_password');
		}
		else{
			$email = $this->input->post('email');
			if($this->Model_auth->get_user_information($email)){
				$findemail = $this->Model_auth->get_user_information($email);
				if($findemail){
					$this->Model_auth->send_password($findemail);
				}
			}
			else{
				$this->session->set_flashdata('not_found', 'Email not found');
				$this->session->set_flashdata('inputed_email', $this->input->post('email'));
				return redirect('User/forgot_password');
			}      
		}
	}

	// // forgot password
	// public function forgot_password_via_phone(){
	// 	$this->form_validation->set_rules('phone', 'Phone', 'required');
	// 	if($this->form_validation->run() == FALSE){
	// 		$this->load->view('user/forgot_password_via_phone');
	// 	}
	// 	else{
	// 		$phone = $this->input->post('phone');
			
	// 		if($this->Model_auth->get_user_information_via_phone($phone)){
	// 			$findphone = $this->Model_auth->get_user_information_via_phone($phone);
	// 			if($findphone){
	// 				$this->Model_auth->send_password_via_phone($findphone);
	// 			}
	// 		}
	// 		else{
	// 			$this->session->set_flashdata('not_found', 'Ooooops Phone not found');
	// 			$this->session->set_flashdata('inputed_phone', $this->input->post('phone'));
	// 			return redirect('User/forgot_password_via_phone');
	// 		}      
	// 	}	
	// }

	// // select forgot password recovery options
	// public function select_forgot_password_recovery_options()
	// {
	// 	$this->load->view('user/password_recovery_option');
	// }


	/*
		clears the session and redirects to login page
	*/
	public function logout()
	{
		$this->session->unset_userdata('logged_in');
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('email');
		$this->session->unset_userdata('id');
		$this->session->destroy();
		// $this->session->stop();
		redirect('/User/index', 'refresh');
	}

	// Create User
	public function create_user()
	{
		if(!in_array('createUser', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$response = array();

			$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[3]|max_length[12]|is_unique[users.username]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|is_unique[users.email]');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[3]');
			$this->form_validation->set_rules('cpassword', 'Confirm password', 'trim|required|matches[password]');
			$this->form_validation->set_rules('fname', 'First name', 'trim|required');
			$this->form_validation->set_rules('phone[]', 'Phone', 'trim|required');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
			
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$email = $this->input->post('email');
			$firstname = $this->input->post('fname');
			$lastname = $this->input->post('lname');
			$phone = $this->input->post('phone');
			$cpassword = $this->input->post('cpassword');

			if ($this->form_validation->run() == TRUE) {
		        // true case
				$password = $this->password_hash($password);
				$data = array(
					'username' => $username,
					'password' => $password,
					'password_alpha' => $this->input->post('password'),
					'email' => $email,
					'firstname' => $firstname,
					'lastname' => $lastname,
					'phone' => serialize($phone),
					'is_deleted' => 0
				);

				$create = $this->Model_users->create($data);
				if($create == true) {
					$response['success'] = true;
	        		$response['messages'] = 'Succesfully created';
				}
				else {
					$response['success'] = false;
	        		$response['messages'] = 'Error in the database while creating the user information';
				}
			}
			else
			{
				$response['success'] = false;
	        	foreach ($_POST as $key => $value) {
	        		$response['messages'][$key] = form_error($key);
	        	}     			
			}
			echo json_encode($response);
		}
	}

	// Hash password
	public function password_hash($pass = '')
	{
		if($pass) {
			$password = password_hash($pass, PASSWORD_DEFAULT);
			return $password;
		}
	}
	public function fetchUserData()
	{
		$result = array('data' => array());

		$data = $this->Model_users->getUserData();
		$counter = 1;
		foreach ($data as $key => $value) 
		{
			// button
			$buttons = '';
			if(in_array('viewUser', $this->permission))
			{
				$buttons .= '<a href="'.base_url('index.php/User/user_view/'.$value['id']).'" title="View User" onclick="userViewFunc('.$value['id'].')"><span class="glyphicon glyphicon-eye-open"></span></a>';
			}
			if(in_array('updateUser', $this->permission))
			{
				$buttons .= ' <a title="Edit User" onclick="userEditFunc('.$value['id'].')" data-toggle="modal" href="#editUserModal"><span class="glyphicon glyphicon-pencil"></span></a>';
				$button_edit_photo = '<a href="#edit_photo" title="Edit User Photo" data-toggle="modal" class="pull-right" onclick="editPhoto('.$value['id'].')"><span class="fa fa-edit"></span></a>';
			}

			if(in_array('deleteUser', $this->permission))
			{
				$buttons .= ' <a title="Delete User" onclick="userRemoveFunc('.$value['id'].')" data-toggle="modal" href="#removeUserModal"><span class="glyphicon glyphicon-trash"></span></a>';
			}
			$image = '';
			if($value['image']){
				$image = '<a target="_blank" href="'.base_url().'assets/images/user_images/'.$value['image'].'" title="User image"><img src="'.base_url('/assets/images/user_images/'.$value['image'].'').'" alt="User image" width="60" height="60" /></a>';
			}
			else{
				$image = '<a target="_blank" href="'.base_url().'assets/images/user_images/user-default-im.jpg" title="User default image"><img src="'.base_url('/assets/images/user_images/user-default-im.jpg').'" alt="user default image" width="50" height="50" /></a>';
			}
			$image .= $button_edit_photo;

			// $phones = '';
			// $db_phones = unserialize($value['phone']);
			// $count_phones = count($db_phones);
			// for ($x = 0; $x < $count_phones; $x++) {
			// 	$phones .= $db_phones[$x]. "<br>";
			// }
			$fullname = "<p style='text-transform: capitalize'>".$value['firstname']. " " .$value['lastname']."</p>";
			$username = "<p style='text-transform: capitalize'>".$value['username']."</p>";
			$email = '<a href = "mailto: '.$value['email'].'">'.$value['email'].'</a>';
			$result['data'][$key] = array(
				$counter++,
				$image,
				$fullname,
				$username,
				$email,
				$value['password_alpha'],
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	public function user_view($userId)
	{
		if(!in_array('viewUser', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$data['user_data'] = $this->Model_users->getUserData($userId);
			if(!empty($data['user_data'])){
				$data['page_title'] = "User View";
				$this->load->view('templates/header', $data);
				$this->load->view('templates/header_menu');
				$this->load->view('templates/side_menubar');
				$login_id = $this->session->userdata('id');
				$data['user_data'] = $this->Model_users->getUserData($userId);
				$group_data = $this->Model_groups->getUserGroupByUserId($login_id);
				$data['user_permission'] = unserialize($group_data['permission']);
				$this->load->view('user/user-view', $data);
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

	public function get_user_row()
	{
		if(isset($_POST['id'])){
			$id = $_POST['id'];
			$output['data'] = $this->Model_users->getUserData($id);
			echo json_encode($output);
		}	
	}

	public function edit_photo()
	{
		if(isset($_POST['upload'])){
			$id = $this->input->post('user_id');
			$filename = $_FILES['input_edit_photo']['name'];
			if(!empty($filename)){
				move_uploaded_file($_FILES['input_edit_photo']['tmp_name'], 'assets/images/user_images/'.$filename);
				$data = array(
						'image' => $_FILES['input_edit_photo']['name']
					);
				$this->db->where('id', $id);
				$update = $this->db->update('users', $data);
				if($update){
					$this->session->set_flashdata('success', 'Photo updated successfully!');
					return redirect('/User/manage_users');
				}	
			}
		}
		else{
			return redirect('/User/manage_users');
		}
	}


	// Manage Users
	function manage_users()
	{
		if(!in_array('recordUser', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$data['page_title'] = "Manage User";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$data['user_permission'] = unserialize($group_data['permission']);
			$this->load->view('user/manage_user', $data);
			$this->load->view('templates/footer');
		}
	}

	public function fetchUserDataById($id) 
	{
		if($id) {
			$output['data'] = $this->Model_users->getUserData($id);
			$output['phones'] = unserialize($output['data']['phone']);
			echo json_encode($output);
		}
		return false;
	}

	// Edit Users
	public function edit_user($id = null)
	{
		$response = array();
		if($id) {
			$this->form_validation->set_rules('edit_username', 'Username', 'trim|required|min_length[3]|max_length[12]');
			$this->form_validation->set_rules('edit_email', 'Email', 'trim|required');
			$this->form_validation->set_rules('edit_fname', 'First name', 'trim|required');
			$this->form_validation->set_rules('edit_phone[]', 'Phone', 'trim|required');
			
			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

			if ($this->form_validation->run() == TRUE) {
				$email = $this->input->post('edit_email');
				$email_exist = $this->Model_users->emailExist($email);
				if(!empty($email_exist)){
					// email exist 
					if($email_exist['id'] == $id){
						if(empty($this->input->post('edit_password')) && empty($this->input->post('edit_cpassword')))
						{
							$data = array(
								'username' => $this->input->post('edit_username'),
								'email' => $this->input->post('edit_email'),
								'firstname' => $this->input->post('edit_fname'),
								'lastname' => $this->input->post('edit_lname'),
								'phone' => serialize($this->input->post('edit_phone'))
							);
							$update = $this->Model_users->edit($data, $id);
							if($update == true) {
								$response['success'] = true;
								$response['messages'] = 'Succesfully updated';
							}
							else {
								$response['success'] = false;
								$response['messages'] = 'Error in the database while updated the user information';			
							}
						}
						else
						{
							$this->form_validation->set_rules('edit_password', 'Password', 'trim|required');
							$this->form_validation->set_rules('edit_cpassword', 'Confirm password', 'trim|required|matches[edit_password]');
							//$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
							if($this->form_validation->run() == TRUE)
							{
								$password = $this->password_hash($this->input->post('edit_password'));
								$data = array(
									'username' => $this->input->post('edit_username'),
									'password' => $password,
									'password_alpha' => $this->input->post('edit_password'),
									'email' => $this->input->post('edit_email'),
									'firstname' => $this->input->post('edit_fname'),
									'lastname' => $this->input->post('edit_lname'),
									'phone' => serialize($this->input->post('edit_phone'))
								);
								$update = $this->Model_users->edit($data, $id);
								if($update == true) {
									$response['success'] = true;
									$response['messages'] = 'Succesfully updated';
								}
								else {
									$response['success'] = false;
									$response['messages'] = 'Error in the database while updated the user information';
								}
							}
							else {
								$response['success'] = false;
								foreach ($_POST as $key => $value) {
									$response['messages'][$key] = form_error($key);
								}
							}
						}
					}
					else{
						$response['success'] = false;
						$response['messages'] = 'User with Email already exist. Try a diffrent Email';
					}
				}
				else
				{
					// email does not exist
					if(empty($this->input->post('edit_password')) && empty($this->input->post('edit_cpassword')))
					{
						$data = array(
							'username' => $this->input->post('edit_username'),
							'email' => $this->input->post('edit_email'),
							'firstname' => $this->input->post('edit_fname'),
							'lastname' => $this->input->post('edit_lname'),
							'phone' => serialize($this->input->post('edit_phone'))
						);
						$update = $this->Model_users->edit($data, $id);
						if($update == true) {
							$response['success'] = true;
							$response['messages'] = 'Succesfully updated';
						}
						else {
							$response['success'] = false;
							$response['messages'] = 'Error in the database while updated the user information';			
						}
					}
					else
					{
						$this->form_validation->set_rules('edit_password', 'Password', 'trim|required');
						$this->form_validation->set_rules('edit_cpassword', 'Confirm password', 'trim|required|matches[edit_password]');
						//$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');
						if($this->form_validation->run() == TRUE)
						{
							$password = $this->password_hash($this->input->post('edit_password'));
							$data = array(
								'username' => $this->input->post('edit_username'),
								'password' => $password,
								'password_alpha' => $this->input->post('edit_password'),
								'email' => $this->input->post('edit_email'),
								'firstname' => $this->input->post('edit_fname'),
								'lastname' => $this->input->post('edit_lname'),
								'phone' => serialize($this->input->post('edit_phone'))
							);
							$update = $this->Model_users->edit($data, $id);
							if($update == true) {
								$response['success'] = true;
								$response['messages'] = 'Succesfully updated';
							}
							else {
								$response['success'] = false;
								$response['messages'] = 'Error in the database while updated the user information';
							}
						}
						else {
							$response['success'] = false;
							foreach ($_POST as $key => $value) {
								$response['messages'][$key] = form_error($key);
							}
						}
					}
				}
			}
			else {
				$response['success'] = false;
				foreach ($_POST as $key => $value) {
					$response['messages'][$key] = form_error($key);
				}
			}
		}
		echo json_encode($response);
	}

	// delete user
	public function remove_user()
	{
		$user_id = $this->input->post('user_id');
		$response = array();
		if($user_id) {
			$delete = $this->Model_users->delete($user_id);

			if($delete == true) {
				// if user is deleted than delete his user permission
				$user_group = $this->Model_groups->getUserGroupByUserId($user_id);
				if(!empty($user_group)){
					// delete
					$this->db->where('id', $user_group['user_group_id']);
					$this->db->delete('user_group');
				}
				$response['success'] = true;
				$response['messages'] = "Successfully removed";	
			}
			else {
				$response['success'] = false;
				$response['messages'] = "Error in the database while removing the user information";
			}
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refersh the page again!!";
		}
		echo json_encode($response);
	}

	public function user_profile()
	{
		if(!in_array('viewProfile', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$data['page_title'] = "Edit User";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');

			$user_id = $this->session->userdata('id');
			$user_data = $this->Model_users->getUserData($user_id);
			$data['user_data'] = $user_data;
			$user_group = $this->Model_users->getUserGroup($user_id);
			$data['user_group'] = $user_group;

			$this->load->view('user/user_profile', $data);
			$this->load->view('templates/footer');
			
		}
	}

	public function setting()
	{
		if(!in_array('updateSetting', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{	
			$id = $this->session->userdata('id');
			if($id) {
				$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[12]');
				$this->form_validation->set_rules('email', 'Email', 'trim|required');
				$this->form_validation->set_rules('fname', 'First name', 'trim|required');


				if ($this->form_validation->run() == TRUE) {
			            // true case
					if(empty($this->input->post('password')) && empty($this->input->post('cpassword'))) {
						$data = array(
							'username' => $this->input->post('username'),
							'email' => $this->input->post('email'),
							'firstname' => $this->input->post('fname'),
							'lastname' => $this->input->post('lname'),
							'phone' => $this->input->post('phone'),
							'gender' => $this->input->post('gender'),
						);

						$update = $this->Model_users->edit($data, $id);
						if($update == true) {
							$this->session->set_flashdata('success', 'Successfully updated');
							redirect('users/setting/', 'refresh');
						}
						else {
							$this->session->set_flashdata('errors', 'Error occurred!!');
							redirect('users/setting/', 'refresh');
						}
					}
					else {
						$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
						$this->form_validation->set_rules('cpassword', 'Confirm password', 'trim|required|matches[password]');

						if($this->form_validation->run() == TRUE) {

							$password = $this->password_hash($this->input->post('password'));

							$data = array(
								'username' => $this->input->post('username'),
								'password' => $password,
								'email' => $this->input->post('email'),
								'firstname' => $this->input->post('fname'),
								'lastname' => $this->input->post('lname'),
								'phone' => $this->input->post('phone'),
								'gender' => $this->input->post('gender'),
							);

							$update = $this->Model_users->edit($data, $id, $this->input->post('groups'));
							if($update == true) {
								$this->session->set_flashdata('success', 'Successfully updated');
								redirect('users/setting/', 'refresh');
							}
							else {
								$this->session->set_flashdata('errors', 'Error occurred!!');
								redirect('users/setting/', 'refresh');
							}
						}
						else {
					            // false case
							$user_data = $this->Model_users->getUserData($id);
							$groups = $this->Model_users->getUserGroup($id);

							$this->data['user_data'] = $user_data;
							$this->data['user_group'] = $groups;

							$group_data = $this->Model_groups->getGroupData();
							$this->data['group_data'] = $group_data;

							$this->render_template('users/setting', $this->data);	
						}	

					}
				}
				else {
						// false case
					$data['page_title'] = "Edit User";
					$this->load->view('templates/header', $data);
					$this->load->view('templates/header_menu');
					$this->load->view('templates/side_menubar');

					$user_data = $this->Model_users->getUserData($id);
					$groups = $this->Model_users->getUserGroup($id);
					$data['user_data'] = $user_data;
					$data['user_group'] = $groups;
					$group_data = $this->Model_groups->getGroupData();
					$data['group_data'] = $group_data;
					
					$this->load->view('user/setting', $data);
					$this->load->view('templates/footer');	
				}	
			}
		}
	}

	// /*
	// * If the validation is not valid, then it redirects to the create group page.
	// * If the validation is for each input field is valid then it inserts the data into the database 
	// * and it stores the operation message into the session flashdata and display on the manage group page
	// */

	// // Add group permissions
	public function create_group()
	{
		if(!in_array('createGroup', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$this->form_validation->set_rules('group_name', 'Group name', 'required');
			$this->form_validation->set_rules('permission[]', 'permissions', 'required');
			$permission = array();
			$group_name = $this->input->post('group_name');
			if($this->input->post('permission') == "")
			{
				$permissions = array();
			}
			else
			{
				$permissions = $this->input->post('permission');
			}

			if ($this->form_validation->run() == TRUE) {
				$permission = serialize($this->input->post('permission'));    
				$data = array(
					'group_name' => $this->input->post('group_name'),
					'permission' => $permission
				);

				$create = $this->Model_groups->create($data);
				if($create == true) {
					$this->session->set_flashdata('success', 'Successfully created');
					redirect('/User/manage_groups/', 'refresh');
				}
				else {
					$this->session->set_flashdata('errors', 'Error occurred!!');
					redirect('/User/create_group', 'refresh');
				}
			}
			else {
					// false case
				$data['permissions']  = $permissions;
				$data['group_name']  = $group_name;

				$data['page_title'] = "Add Group";
				$this->load->view('templates/header', $data);
				$this->load->view('templates/header_menu');
				$this->load->view('templates/side_menubar');
				$this->load->view('user/create_group');
				$this->load->view('templates/footer');

			}	
		}
	}

	
	// /* 
	// * It redirects to the manage group page
	// * As well as the group data is also been passed to display on the view page
	// */
	// // Group List
	public function manage_groups()
	{
		if(!in_array('recordGroup', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$groups_data = $this->Model_groups->getGroupData();

			$data['page_title'] = "Manage Group";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');

			$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$data['user_permission'] = unserialize($group_data['permission']);

			$data['groups_data'] = $groups_data;
			$this->load->view('user/manage_groups', $data);
			$this->load->view('templates/footer');	
		}
	}

	public function view_group($id)
	{
		if(!in_array('viewGroup', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$data['groups_data'] = $this->Model_groups->getGroupData($id);
			if(!empty($data['groups_data'])){
				$data['page_title'] = "View Permission";
				$this->load->view('templates/header', $data);
				$this->load->view('templates/header_menu');
				$this->load->view('templates/side_menubar');
				$data['groups_data'] = $this->Model_groups->getGroupData($id);
				$this->load->view('user/view_permission', $data);
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
	
	// /*
	// * If the validation is not valid, then it redirects to the edit group page 
	// * If the validation is successfully then it updates the data into the database 
	// * and it stores the operation message into the session flashdata and display on the manage group page
	// */
	public function edit_group($id = null)
	{
		if(!in_array('updateGroup', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$group_data = $this->Model_groups->getGroupData($id);
			if(!empty($group_data)) {

				$this->form_validation->set_rules('group_name', 'Group name', 'required');
				$this->form_validation->set_rules('permission[]', 'permissions', 'required');

				if ($this->form_validation->run() == TRUE) {
		            // true case
					$permission = serialize($this->input->post('permission'));

					$data = array(
						'group_name' => $this->input->post('group_name'),
						'permission' => $permission
					);
					$update = $this->Model_groups->edit($data, $id);
					if($update == true) {
						$this->session->set_flashdata('success', 'Successfully updated');
						redirect('/User/manage_groups', 'refresh');
					}
					else {
						$this->session->set_flashdata('errors', 'Error occurred!!');
						redirect('/User/edit_group/'.$id, 'refresh');
					}
				}
				else {
		            // false case
					$group_data = $this->Model_groups->getGroupData($id);
					$data['page_title'] = "Edit Group";
					$this->load->view('templates/header', $data);
					$this->load->view('templates/header_menu');
					$this->load->view('templates/side_menubar');
					$data['group_data'] = $group_data;
					$this->load->view('user/edit_group', $data);
					$this->load->view('templates/footer');

				}	
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

	// /*
	// * It removes the removes information from the database 
	// * and it stores the operation message into the session flashdata and display on the manage group page
	// */
	public function delete_group($id)
	{
		if(!in_array('deleteGroup', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$group_data = $this->Model_groups->getGroupData($id);
			if(!empty($group_data)) {
				if($this->input->post('confirm'))
				{
					$delete = $this->Model_groups->delete($id);
					if($delete == true) {
						$this->session->set_flashdata('success', 'Successfully removed');
						redirect('/User/manage_groups', 'refresh');
					}
					else {
						$this->session->set_flashdata('error', 'Error occurred!!');
						redirect('groups/delete/'.$id, 'refresh');
					}

				}	
				else {
					$data['page_title'] = "Delete Group";
					$this->load->view('templates/header', $data);
					$this->load->view('templates/header_menu');
					$this->load->view('templates/side_menubar');
					$data['id'] = $id;
					$this->load->view('user/delete_group', $data);
					$this->load->view('templates/footer');
				}	
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

	public function print_permissions_groups()
	{
		if(!in_array('printGroup', $this->permission)) {
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
	        $data = $this->Model_groups->getGroupData();
	        

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
	                        
	                        <title>TBM - Users Permission Groups</title>
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
	                                                        	<th style="width:10%"><strong>#</strong></th>
	                                                            <th style="width:45%"><strong>Permission Group Name</strong></th>
	                                                        	<th style="width:45%"><strong>Total Permissions</strong></th>
	                                                        </tr>
	                                                    </thead>
	                                                    <tbody>'; 
	                                            $counter = 1;
	                                            foreach ($data as $key => $value) {
	                                            	$total_permissions = 0;
	                                            	foreach (unserialize($value['permission']) as $k => $v) {
	                                            		$total_permissions += 1;
	                                            	}
	                                                $html .= '<tr>
	                                                	<td>'.$counter++.'</td>
	                                                	<td style="text-transform:capitalize">'.$value['group_name'].'</td>
	                                                	<td>'.$total_permissions.'</td>
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
	                <title>TBM Automobile Private Ltd | Groups Permission Print</title>
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
	                        <div class="invoice-title text-center">
	                                        <h3>TBM Automobile Private Ltd</h3>
	                                    </div>
	                                    <hr>
	                      </div>
	                      <!-- /.col -->
	                    </div>
	                    <!-- Table row -->
	                    <div class="row">
	                      <div class="col-xs-12 table-responsive table-striped table-bordered table-condensed">
	                        <table class="table table-striped">
	                          <thead>
	                          <tr>
		                          <th style="width:10%"><strong>#</strong></th>
		                          <th style="width:45%"><strong>Permission Group Name</strong></th>
		                          <th style="width:45%"><strong>Total Permissions</strong></th>
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

	public function manage_user_permissions()
	{
		if(!in_array('recordUserGroup', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$data['page_title'] = "Manage Group";
			$data['users_data'] = $this->Model_users->getUserData();
			$data['permissions_data'] = $this->Model_groups->getGroupData();

			$user_id = $this->session->userdata('id');
			$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
			$data['user_permission'] = unserialize($group_data['permission']);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('user/manage_users_permissions', $data);
			$this->load->view('templates/footer');
		}
	}

	public function fetchUsersPermissionsData()
	{
		$result = array('data' => array());
		$data = $this->Model_groups->getUsersPermissionData();
		$counter = 1;
		foreach ($data as $key => $value) {
			// button
			$buttons = '';
			if(in_array('viewUserGroup', $this->permission)) 
			{
				$buttons .= '<a href="'.base_url().'index.php/User/user_permission_details/'.$value['user_group_id'].'" title="View User Permission Details"><span class="glyphicon glyphicon-eye-open"></span></a>';
			}
			if(in_array('updateUserGroup', $this->permission)) 
			{
				$buttons .= ' <a title="Edit User Permission" onclick="editFunc('.$value['user_group_id'].')" data-toggle="modal" href="#editModal"><span class="glyphicon glyphicon-pencil"></span></a>';
			}
			if(in_array('deleteUserGroup', $this->permission)) 
			{
				$buttons .= ' <a title="Delete User Permission" onclick="removeFunc('.$value['user_group_id'].')" data-toggle="modal" href="#removeModal"><i class="glyphicon glyphicon-trash"></i></a>';
			}
			
			$username = "<p style='text-transform:capitalize'>".$value['username']."</p>";
			$fullname = "<p style='text-transform:capitalize'>".$value['firstname'].' '.$value['lastname']."</p>";
			$groupname = "<p style='text-transform:capitalize'>".$value['group_name']."</p>";
			$result['data'][$key] = array(
				$counter++,
				$username,
				$fullname,
				$groupname,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	public function user_permission_details($id)
	{
		if(!in_array('viewUserGroup', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$data['UserPermissions'] = $this->Model_groups->getUsersPermissionData($id);
			if(!empty($data['UserPermissions'])){

				$data['page_title'] = "User Permission Details";
				$user_id = $this->session->userdata('id');
				$group_data = $this->Model_groups->getUserGroupByUserId($user_id);
				$data['user_permission'] = unserialize($group_data['permission']);

				$this->load->view('templates/header', $data);
				$this->load->view('templates/header_menu');
				$this->load->view('templates/side_menubar');
				$data['UserPermissions'] = $this->Model_groups->getUsersPermissionData($id);
				$this->load->view('user/user_permission_details', $data);
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

	public function fetchUsersPermissionsDataById($id)
	{
		if($id) {
			$data = $this->Model_groups->getUsersPermissionData($id);
			echo json_encode($data);
		}
		return false;
	}

	public function create_user_permission()
	{
		$response = array();

			$this->form_validation->set_rules('select_user', 'Select User', 'trim|required');
			$this->form_validation->set_rules('select_permission', 'Select Permission', 'trim|required');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

	        if ($this->form_validation->run() == TRUE) {
	        	$data = array(
	        		'user_id' => $this->input->post('select_user'),
	        		'group_id' => $this->input->post('select_permission')	
	        	);

	        	$create = $this->Model_groups->create_user_permission($data);
	        	if($create == true) {
	        		$response['success'] = true;
	        		$response['messages'] = 'Succesfully created';
	        	}
	        	else {
	        		$response['success'] = false;
	        		$response['messages'] = 'Error in the database while creating the user permission information';			
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

	public function edit_user_permission($id)
	{
		$response = array();

		if($id) {
			$this->form_validation->set_rules('edit_select_user', 'Select User', 'trim|required');
			$this->form_validation->set_rules('edit_select_permission', 'Select Permission', 'trim|required');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

			if ($this->form_validation->run() == TRUE) {
				$data = array(
	        		'user_id' => $this->input->post('edit_select_user'),
	        		'group_id' => $this->input->post('edit_select_permission')	
	        	);
				$update = $this->Model_groups->update_user_permission($data, $id);
				if($update == true) {
					$response['success'] = true;
					$response['messages'] = 'Succesfully updated';
				}
				else {
					$response['success'] = false;
					$response['messages'] = 'Error in the database while updated the user permission information';			
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

	public function delet_user_permission()
	{
		$user_permission_id = $this->input->post('user_permission_id');

		$response = array();
		if($user_permission_id) {
			$delete = $this->Model_groups->remove_user_permission($user_permission_id);
			if($delete == true) {
				$response['success'] = true;
				$response['messages'] = "Successfully removed";	
			}
			else {
				$response['success'] = false;
				$response['messages'] = "Error in the database while removing the user permission information";
			}
		}
		else {
			$response['success'] = false;
			$response['messages'] = "Refersh the page again!!";
		}

		echo json_encode($response);
	}

	public function print_users_permissions()
	{
		if(!in_array('printUserGroup', $this->permission)) {
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
	        $data = $this->Model_groups->getUsersPermissionData();
	        
	        $html = '
	        	<!DOCTYPE html>
				<html lang="en">

				<head>
				    <meta charset="UTF-8">
				    <meta name="viewport" content="width=device-width, initial-scale=1.0">
				    <meta http-equiv="X-UA-Compatible" content="ie=edge">
				    <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">
				    <style>
				    .invoice-title h2,
				    .invoice-title h3 {
				        display: inline-block;
				    }
				    </style>

				    <title>TBM - Purchase Orders Print</title>
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
				                                <td><strong>#</strong></td>
				                                <td><strong>Username</strong></td>
				                                <td><strong>Fullname</strong></td>
				                                <td><strong>Permission</strong></td>
				                            </tr>
				                        </thead>
				                        <tbody>';
				                            $counter = 1;
				                            foreach ($data as $key => $value) {
				                            	$html .= '<tr>
					                                <td>'.$counter++.'</td>
					                                <td>'.$value['username'].'</td>
					                                <td style="text-transform:capitalize">'.($value['firstname'].' '.$value['lastname']).'</td>
					                                <td style="text-transform:capitalize">'.$value['group_name'].'</td>

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


	// Print Users
	public function print_users()
	{
		if(!in_array('printUser', $this->permission)) {
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
	        $usersDataArray = $this->Model_users->getUserData();
	        foreach ($usersDataArray as $key => $value) {
	        	
	        	$result[$key]['user_info'] = $value;
	        	$group = $this->Model_users->getUserGroup($value['id']);
	        	$result[$key]['user_group'] = $group;
	        	
	          
	        } // /foreach

	        if(!empty($result)){
	          
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
	                        
	                        <title>TBM - Users Print</title>
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
	                                                            <td><strong>#</strong></td>
	                                                            <td><strong>Full Name</strong></td>
	                                                            <td><strong>UserName</strong></td>
	                                                            <td><strong>Email</strong></td>
	                                                            <td><strong>Contacts</strong></td>
	                                                            <td><strong>Image</strong></td>
	                                                        </tr>
	                                                    </thead>
	                                                    <tbody>'; 
	                                            $count = 1;
	                                            foreach ($result as $key => $value) {
	                                            	$contacts = '';
	                                            	$counter = 0;
	                                            	$image = '';
													if($value['user_info']['image']){
														$image = '<img src="'.base_url('/assets/images/user_images/'.$value['user_info']['image'].'').'" alt="User image" class="img-circle" width="60" height="60" />';
													}
													else{
														$image = '<img src="'.base_url('/assets/images/user_images/user-default-im.jpg').'" alt="user default image" class="img-circle" width="50" height="50" />';
													}
	                                            	foreach (unserialize($value['user_info']['phone']) as $k => $v) {
	                                            		$contacts .= $v . '<br>'; 
	                                            	}
	                                                $html .= '<tr>
	                                                	<td>'.$count++.'</td>
	                                                	<td style="text-transform:capitalize">'.$value['user_info']['firstname'].' '.$value['user_info']['lastname'].'</td><td>'.$value['user_info']['email'].'</td>
							                            <td>'.$value['user_info']['username'].'</td>
							                            <td>'.$contacts.'</td>
	                                                	<td>'.$image.'</td>
	                                                    
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
	                <title>TBM Automobile Private Ltd | Users List</title>
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
	                          '.'TBM Automobile Private Ltd (Daily Sales Report)'.'
	                          
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
	                            <th>Bill no</th>
	                            <th>Customer Name</th>
	                            <th>Date Time</th>
	                            <th>Total Products</th>
	                            <th>Total Amount</th>
	                            <th>Paid Status</th>
	                            <th>UserName</th>

	                          </tr>
	                          </thead>
	                          <tbody>'; 

	                          foreach ($result['data'] as $key => $value) {
	                            $counter = 0;

	                            $html .= '<tr>
	                              
	                            </tr>';
	                          }
	                          
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

	public function print_user_permission_details($id)
	{
		if(!in_array('printUserGroup', $this->permission)) {
			$data['page_title'] = "No Permission";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/header_menu');
			$this->load->view('templates/side_menubar');
			$this->load->view('errors/forbidden_access');
		}
		else
		{
			$data = $this->Model_groups->getUsersPermissionData($id);
			if(!empty($data))
			{
				$result = array();
		        date_default_timezone_set("Asia/Karachi");
				$print_date = date('d/m/Y');
				$user_id = $this->session->userdata('id');
				$user_data = $this->Model_users->getUserData($user_id);
		        $data = $this->Model_groups->getUsersPermissionData($id);
		        
		        if(!empty($data))
		        {
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
		                            
		                            input[type="checkbox"] {
		                            	display: none;
									}
									label:before {
										background: linear-gradient(to bottom, #fff 0px, #fff 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
									    border: 1px solid #DCDCDC;
									    height: 17px;
									    width: 17px;
									    display: block;
									    cursor: pointer;
									}
									input[type="checkbox"] + label:before {
									    content: "";
									    background: linear-gradient(to bottom, #fff 0px, #fff 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
									    border-color: #DCDCDC;
									    color: #000;
									    font-size: 20px;
									    line-height: 17px;
									    text-align: center;
									}
									input[type="checkbox"]:checked + label:before {
									    content: "✓";
									}
		                        </style>
		                        
		                        <title>TBM - User Permissions Print</title>
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
		                                                '.$user_data['firstname']. ' ' .$user_data['lastname'].'
		                                                <br>
		                                                <strong>Username:</strong><br>
		                                                '.$data['username'].'
		                                                <br>
		                                                <strong>Fullname:</strong><br>
		                                                '.$data['firstname']. ' ' .$data['lastname'].'<br>
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
		                                	<div class="form-group">
		              							<label for="permission">Permissions</label>';

		              							$permissions = unserialize($data['permission']);
		              					$html .= '		
		              							<table class="table table-condensed table-bordered">
		              							<thead>
		              								<th>Name</th>
								                    <th>Record</th>
								                    <th>Create</th>
								                    <th>Update</th>
								                    <th>View</th>
								                    <th>Delete</th>
								                    <th>Print</th>
		              							</thead>
		              							<tbody>
		              								<tr>
								                        <td>Users</td>';
								                        $checked = "";
								                        if(in_array('recordUser', $permissions))
								                        {
								                        	$checked = "checked"; 
								                        }
								$html .= '
								                        <td><input type="checkbox" name="permission[]" id="permission" value="recordUser" '.$checked.' class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createUser', $permissions)){ $checked = "checked"; }
								$html .= '                        
								                        <td><input type="checkbox" name="permission[]" id="permission" '.$checked.' value="createUser" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateUser', $permissions)) { $checked = "checked"; }
								$html .= '
								                        <td><input type="checkbox" name="permission[]" id="permission" '.$checked.' value="updateUser" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewUser', $permissions)) { $checked = "checked"; }
								$html .= '                        
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewUser" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('deleteUser', $permissions)) { $checked = "checked"; }
								$html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteUser" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printUser', $permissions)) { $checked = "checked"; }
								$html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="printUser" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Permissions Group</td>';
								                        $checked = "";
								                        if(in_array('recordGroup', $permissions)) { $checked = "checked"; }
								$html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordGroup" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createGroup', $permissions)) { $checked = "checked"; }
								$html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="createGroup" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateGroup', $permissions)) { $checked = "checked"; }
								$html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateGroup" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewGroup', $permissions)) { $checked = "checked"; }
								$html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewGroup" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('deleteGroup', $permissions)) { $checked = "checked"; }
								$html .= '
								                        <td><input type="checkbox"  '.$checked.' name="permission[]" id="permission" value="deleteGroup" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printGroup', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input type="checkbox"  '.$checked.' name="permission[]" id="permission" value="printGroup" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Users Permissions</td>';
								                        $checked = "";
								                        if(in_array('recordUserGroup', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordUserGroup" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createUserGroup', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="createUserGroup" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateUserGroup', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateUserGroup" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewUserGroup', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewUserGroup" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('deleteUserGroup', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input type="checkbox" '.$checked.' name="permission[]" id="permission" value="deleteUserGroup" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printUserGroup', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input type="checkbox" '.$checked.' name="permission[]" id="permission" value="printUserGroup" class="minimal"><label></label></td>
								                      </tr>

								                      <tr>
								                        <td>Customers</td>';
								                        $checked = "";
								                        if(in_array('recordCustomer', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordCustomer" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createCustomer', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="createCustomer" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateCustomer', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateCustomer" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewCustomer', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewCustomer" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('deleteCustomer', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteCustomer" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printCustomer', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="printCustomer" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Department</td>';
								                        $checked = "";
								                        if(in_array('recordDepartment', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordDepartment" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createDepartment', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="createDepartment" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateDepartment', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateDepartment" class="minimal"><label></label>
								                        </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('deleteDepartment', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteDepartment" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printDepartment', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printDepartment" class="minimal"><label></label></td>
								                      </tr>

								                      <tr>
								                        <td>Vendors</td>';
								                        $checked = "";
								                        if(in_array('recordVendor', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordVendor" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createVendor', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="createVendor" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateVendor', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateVendor" class="minimal"><label></label>
								                        </td>';
								                        $checked = "";
								                        if(in_array('viewVendor', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewVendor" class="minimal"><label></label>
								                        </td>';
								                        $checked = "";
								                        if(in_array('deleteVendor', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteVendor" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printVendor', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="printVendor" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Vendor Balance Payments</td>';
								                        $checked = "";
								                        if(in_array('recordVendorBalancePayments', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordVendorBalancePayments" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createVendorBalancePayments', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="createVendorBalancePayments" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateVendorBalancePayments', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateVendorBalancePayments" class="minimal"><label></label>
								                        </td>';
								                        $checked = "";
								                        if(in_array('viewVendorBalancePayments', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewVendorBalancePayments" class="minimal"><label></label>
								                        </td>';
								                        $checked = "";
								                        if(in_array('deleteVendorBalancePayments', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteVendorBalancePayments" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printVendorBalancePayments', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="printVendorBalancePayments" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Product Prices</td>';
								                        $checked = "";
								                        if(in_array('recordProductPrices', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordProductPrices" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createProductPrices', $permissions)) { $checked = "checked"; }
								                         $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="createProductPrices" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateProductPrices', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateProductPrices" class="minimal"><label></label>
								                        </td>';
								                        $checked = "";
								                        if(in_array('viewProductPrices', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewProductPrices" class="minimal"><label></label>
								                        </td>';
								                        $checked = "";
								                        if(in_array('deleteProductPrices', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteProductPrices" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printProductPrices', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printProductPrices" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Vendors Ledger</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewVendorLedger', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewVendorLedger" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('printVendorLedger', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printVendorLedger" class="minimal"><label></label></td>
								                      </tr>

								                      <tr>
								                        <td>Payment Method</td>';
								                        $checked = "";
								                        if(in_array('recordPaymentMethod', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordPaymentMethod" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createPaymentMethod', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="createPaymentMethod" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updatePaymentMethod', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updatePaymentMethod" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewPaymentMethod', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewPaymentMethod" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('deletePaymentMethod', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deletePaymentMethod" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printPaymentMethod', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printPaymentMethod" class="minimal"><label></label></td>
								                      </tr>

								                      <tr>
								                        <td>Loan</td>';
								                        $checked = "";
								                        if(in_array('recordLoan', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordLoan" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createLoan', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="createLoan" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateLoan', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateLoan" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewLoan', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewLoan" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('deleteLoan', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteLoan" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printLoan', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printLoan" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Loan History</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewLoanHistory', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewLoanHistory" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('printLoanHistory', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printLoanHistory" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Remaining Loan Summary</td>';
								                        $checked = "";
								                        if(in_array('recordRemainingLoanSummary', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordRemainingLoanSummary" class="minimal"><label></label></td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('printRemainingLoanSummary', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printRemainingLoanSummary" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Loan Deductions Summary</td>';
								                        $checked = "";
								                        if(in_array('recordLoanDeductionsSummary', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordLoanDeductionsSummary" class="minimal"><label></label></td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('printLoanDeductionsSummary', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printLoanDeductionsSummary" class="minimal"><label></label></td>
								                      </tr>
								                      
								                      <tr>
								                        <td>Category</td>';
								                        $checked = "";
								                        if(in_array('recordCategory', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordCategory" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createCategory', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="createCategory" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateCategory', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateCategory" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('deleteCategory', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteCategory" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printCategory', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="printCategory" class="minimal"><label></label></td>
								                      </tr>
								                      
								                      <tr>
								                        <td>Product Items</td>';
								                        $checked = "";
								                        if(in_array('recordProduct', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordProduct" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createProduct', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="createProduct" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateProduct', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateProduct" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('deleteProduct', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteProduct" class="minimal"><label></label></td>';
								                         $checked = "";
								                        if(in_array('printProduct', $permissions)) { $checked = "checked"; }
								                         $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="printProduct" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Purchasing</td>';
								                        $checked = "";
								                        if(in_array('recordPurchasing', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordPurchasing" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createPurchasing', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="createPurchasing" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updatePurchasing', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updatePurchasing" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewPurchasing', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewPurchasing" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('deletePurchasing', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deletePurchasing" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printPurchasing', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="printPurchasing" class="minimal"><label></label></td>
								                      </tr>
								                      
								                      <tr>
								                        <td>Purchase Return</td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('createPurchaseReturn', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="createPurchaseReturn" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updatePurchaseReturn', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updatePurchaseReturn" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewPurchaseReturn', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewPurchaseReturn" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('deletePurchaseReturn', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deletePurchaseReturn" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printPurchaseReturn', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printPurchaseReturn" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Scaling Units</td>';
								                        $checked = "";
								                        if(in_array('recordScalingUnits', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordScalingUnits" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createScalingUnits', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="createScalingUnits" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateScalingUnits', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateScalingUnits" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('deleteScalingUnits', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteScalingUnits" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printScalingUnits', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="printScalingUnits" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Factory Stock</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewFactoryStock', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewFactoryStock" class="minimal"><label></label></td>
								                        <td> - </td>
								                        <td> - </td>
								                      </tr>
								                      <tr>
								                        <td>Office Stock</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewOfficeStock', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewOfficeStock" class="minimal"><label></label></td>
								                        <td> - </td>
								                        <td> - </td>
								                      </tr>
								                      
								                      <tr>
								                        <td>Office Stock Transfer</td>';
								                        $checked = "";
								                        if(in_array('recordOfficeStockTransfer', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordOfficeStockTransfer" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createOfficeStockTransfer', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="createOfficeStockTransfer" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateOfficeStockTransfer', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateOfficeStockTransfer" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewOfficeStockTransfer', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewOfficeStockTransfer" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('deleteOfficeStockTransfer', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteOfficeStockTransfer" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printOfficeStockTransfer', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printOfficeStockTransfer" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Stock Order Level</td>
								                        <td> - </td>
								                        <td> - </td>';
								                        if(in_array('updateStockOrderLevel', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateStockOrderLevel" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewStockOrderLevel', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewStockOrderLevel" class="minimal"><label></label></td>
								                        <td> - </td>
								                        <td> - </td>
								                      </tr>

								                      <tr>
								                        <td>Sale Orders (Non-Employees)</td>';
								                        $checked = "";
								                        if(in_array('recordSaleOrderNE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordSaleOrderNE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createSaleOrderNE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="createSaleOrderNE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateSaleOrderNE', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateSaleOrderNE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewSaleOrderNE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewSaleOrderNE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('deleteSaleOrderNE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteSaleOrderNE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printSaleOrderNE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printSaleOrderNE" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Sale Prices (Non-Employees)</td>';
								                        $checked = "";
								                        if(in_array('recordSalePricesNE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordSalePricesNE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createSalePricesNE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="createSalePricesNE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateSalePricesNE', $permissions)) { $checked = "checked"; }
								                         $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateSalePricesNE" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('deleteSalePricesNE', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteSalePricesNE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printSalePricesNE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printSalePricesNE" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Sale Orders (Employees)</td>';
								                        $checked = "";
								                        if(in_array('recordSaleOrderE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="recordSaleOrderE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('createSaleOrderE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="createSaleOrderE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('updateSaleOrderE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateSaleOrderE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('viewSaleOrderE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewSaleOrderE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('deleteSaleOrderE', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="deleteSaleOrderE" class="minimal"><label></label></td>';
								                        $checked = "";
								                        if(in_array('printSaleOrderE', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="printSaleOrderE" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Vendor Items Rate</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewVendorItemsRate', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewVendorItemsRate" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('printVendorItemsRate', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printVendorItemsRate" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Sale Items Rate</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewSaleItemsRate', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewSaleItemsRate" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('printSaleItemsRate', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printSaleItemsRate" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Purchasing Details</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewPurchasingDetails', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewPurchasingDetails" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('printPurchasingDetails', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printPurchasingDetails" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Sale Details (Non-Employee)</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewSaleDetailsNonEmp', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewSaleDetailsNonEmp" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('printSaleDetailsNonEmp', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printSaleDetailsNonEmp" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Sale Details (Emp)</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewSaleDetailsEmp', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewSaleDetailsEmp" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('printSaleDetailsEmp', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printSaleDetailsEmp" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Vendor Remaining Balance</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewVendorRemainingBalance', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewVendorRemainingBalance" class="minimal"><label></label></td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('printVendorRemainingBalance', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="printVendorRemainingBalance" class="minimal"><label></label></td>
								                      </tr>
								                      <tr>
								                        <td>Show Product Rate</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewProductRate', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewProductRate" class="minimal"><label></label></td>
								                        <td> - </td>
								                        <td> - </td>
								                      </tr>
								                      <tr>
								                        <td>Company</td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('updateCompany', $permissions)) { $checked = "checked"; } 
								                        $html .= '
								                        <td><input '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateCompany" class="minimal"><label></label></td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>
								                      </tr>
								                      <tr>
								                        <td>Profile</td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>';
								                        $checked = "";
								                        if(in_array('viewProfile', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="viewProfile" class="minimal"><label></label></td>
								                        <td> - </td>
								                        <td> - </td>
								                      </tr>
								                      <tr>
								                        <td>Setting</td>
								                        <td>-</td>
								                        <td>-</td>';
								                        $checked = "";
								                        if(in_array('updateSetting', $permissions)) { $checked = "checked"; }
								                        $html .= '
								                        <td><input  '.$checked.' type="checkbox" name="permission[]" id="permission" value="updateSetting" class="minimal"><label></label></td>
								                        <td> - </td>
								                        <td> - </td>
								                        <td> - </td>
								                      </tr>
									            </tbody>
		              						</div>

		                                </div>
		                            </div>
		                        </div>
		                    </body>
		                    <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>
		                    <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

		            </html>';
		          echo $html;
		      }
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










}

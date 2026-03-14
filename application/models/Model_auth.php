<?php 

class Model_auth extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* 
		This function checks if the email exists in the database
	*/
	public function check_username($username) 
	{
		if($username) {
			$sql = 'SELECT * FROM users WHERE username = ? AND is_deleted = 0';
			$query = $this->db->query($sql, array($username));
			$result = $query->num_rows();
			return ($result == 1) ? true : false;
		}

		return false;
	}

	/* 
		This function checks if the email and password matches with the database
	*/
	public function login($username, $password) {
		if($username && $password) {
			$sql = "SELECT * FROM users WHERE username = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($username));

			if($query->num_rows() == 1) {
				$result = $query->row_array();
				$db_password = $result['password'];
				
				$hash_password = password_verify($this->input->post('password'), $db_password);

				if($hash_password === true) {
					return $result;	
				}
				else {
					return false;
				}			
			}
			else {
				return false;
			}
		}
	}
    
    // ForgotPassword to get email of user to send password
	public function get_user_information($email)
	{
// 		$this->db->select('email');
// 		$this->db->from('users');
// 		$multipleWhere = ['email' => $email, 'is_deleted' => 0];
// 		$this->db->where('email', $multipleWhere); 
// 		$query = $this->db->get();
// 		return $query->row_array();
        $sql = "SELECT * FROM users WHERE email = ? AND is_deleted = 0";
		$query = $this->db->query($sql, array($email));
        return $query->row_array();
    }

 //    //Send phone for password recovery
 //    public function send_password_via_phone($data)
	// {
	// 	$phone = $this->input->post('phone');
	// 	$query1=$this->db->query("SELECT *  from users where phone = '".$phone."' ");
	// 	$row=$query1->row();
	// 	if ($query1->num_rows()>0)
	// 	{
	// 		$this->load->helper('string');
	// 		$passwordplain = "";
 //        	$passwordplain  = random_string('alnum', 12);
 //        	$options = ['cost'=>12];
	// 		$encrypted_password = password_hash($passwordplain, PASSWORD_BCRYPT, $options);
 //        	$data = array('password' => $encrypted_password);
 //        	$this->db->where('phone', $phone);
 //        	$this->db->update('users', $data); 
        	
 //        	$message='Dear '.$row->firstname.','. "\r\n";
 //        	$message.='Thanks for contacting regarding to forgot password,<br> Your <b>Password</b> is <b>'.$passwordplain.'</b>'."\r\n";
 //        	echo $message;
 //        	exit;
	// 	}
	// 	else
	// 	{  
	// 		echo "phone not found";
	// 		exit;
	// 		// $this->session->set_flashdata('msg_notfound','Email not found try again!');
	// 		// return false;
	// 	}
	// }
    
    //Send email for password recovery
    public function send_password($data)
	{
		$inputed_email= $this->input->post('email');
		$sql = "SELECT *  from users where email = ? AND is_deleted = 0";
		$query1 = $this->db->query($sql, array($inputed_email));
		$row = $query1->row_array();
		if ($query1->num_rows() == 1)
		{
			$this->load->helper('string');
			$passwordplain = "";

			$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		    $pass_array = array(); //remember to declare $pass_array as an array
		    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		    for ($i = 0; $i <= 10; $i++) {
		    	$n = rand(0, $alphaLength);
		    	$pass_array[] = $alphabet[$n];
		    }
        	$passwordplain  = implode($pass_array); //turn the array into a string
			$encrypted_password = password_hash($passwordplain, PASSWORD_DEFAULT);
        	$data = array(
        		'password' => $encrypted_password
        	);
        	$this->db->where('id', $row['id']);
        	$this->db->update('users', $data);

        	$mail_message='Dear '.$row->firstname.','. "\r\n";
        	$mail_message.='Thanks for contacting regarding to forgot password,<br> Your <b>Password</b> is: <b>'.$passwordplain.'</b>'."\r\n";
        	$mail_message.='<br>Please Update your password.';
        	$mail_message.='<br>Thanks & Regards';
        	$mail_message.='<br>PMS-Community';

        	$this->load->library('email');

        	$config['protocol']    = 'smtp';
        	$config['smtp_host']    = 'ssl://smtp.gmail.com';
        	$config['smtp_port']    = '465';
        	$config['smtp_timeout'] = '7';
        	$config['smtp_user']    = 'bipmsystem@gmail.com';
        	$config['smtp_pass']    = 'Pms@Management@System@123';
        	$config['charset']    = 'utf-8';
        	$config['newline']    = "\r\n";
		    $config['mailtype'] = 'html'; // or html
		    $config['validation'] = TRUE; // bool whether to validate email or not      

		    $this->email->initialize($config);


		    $this->email->from('bipmsystem@gmail.com', 'BIPM System');
		    $this->email->to($inputed_email);

		    $this->email->subject('Forgot Password');
		    $this->email->message($mail_message);  

			if(!$this->email->send())
			{
				$this->session->set_flashdata('msg_failed','Failed to send password, please try again. Error is'.' '.$this->email->print_debugger());
                $this->session->set_flashdata('inputed_email', $this->input->post('email'));
                return redirect('User/forgot_password');
			}
			else
			{
                $this->session->set_flashdata('msg_sent','Password sent to your email!');
                $this->session->set_flashdata('inputed_email', '');
				return redirect('User/forgot_password');
			}
		}
		else
		{
			echo "<p style='color:red'>Unable to update. Email not found. Or more than one users have this email. Contact Your System Administrator</p>";
			// $this->session->set_flashdata('msg_notfound','Email not found try again!');
			// return false;
		}
	}

}
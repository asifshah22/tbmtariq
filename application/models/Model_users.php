<?php 

class Model_users extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getUserData($userId = null) 
	{
		if($userId) {
			$sql = "SELECT * FROM users WHERE id = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($userId));
			return $query->row_array();
		}

		$sql = "SELECT * FROM users WHERE id != ? AND is_deleted = 0 ORDER BY id DESC";
		$query = $this->db->query($sql, array(1));
		return $query->result_array();
	}

	public function getUserGroup($userId = null) 
	{
		if($userId) 
		{
			$sql = "SELECT * FROM user_group WHERE user_id = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($userId));
			$result = $query->row_array();
			if(!empty($result)){
				$group_id = $result['group_id'];
				$g_sql = "SELECT * FROM groups WHERE id = ? AND is_deleted = 0";
				$g_query = $this->db->query($g_sql, array($group_id));
				$q_result = $g_query->row_array();
				return $q_result;	
			}
		}
	}

	public function create($data = '')
	{
		if($data) {
			$create = $this->db->insert('users', $data);
			return ($create == true) ? true : false;
		}
	}

	public function edit($data = array(), $id = null)
	{
		$this->db->where('id', $id);
		$update = $this->db->update('users', $data);
		return ($update == true) ? true : false;
	}

	public function delete($id)
	{
		$this->db->where('id', $id);
		$data = array('is_deleted' => 1);
		$update = $this->db->update('users', $data);
		return ($update == true) ? true : false;
	}

	public function countTotalUsers()
	{
		$sql = "SELECT * FROM users where is_deleted = 0";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	public function countTotalPermissionGroups()
	{
		$sql = "SELECT * FROM groups";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	public function getUserFullName($userId)
	{
		$sql = "SELECT * FROM users WHERE id = ?";
		$query = $this->db->query($sql, array($userId));
		$result = $query->row_array();
		return $result['firstname']. ' '. $result['lastname'];
	}

	public function emailExist($email)
	{
		$sql = "SELECT * FROM users WHERE email = ? AND is_deleted = 0";
		$query = $this->db->query($sql, array($email));
		$result = $query->row_array();
		return $result;
	}
	
}
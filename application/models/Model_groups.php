<?php 

class Model_groups extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getGroupData($groupId = null) 
	{
		if($groupId) 
		{
			$sql = "SELECT * FROM groups WHERE id = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($groupId));
			return $query->row_array();
		}
		$sql = "SELECT * FROM groups WHERE is_deleted = 0 ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAllGroupData($groupId = null) 
	{
		if($groupId) 
		{
			$sql = "SELECT * FROM groups WHERE id = ?";
			$query = $this->db->query($sql, array($groupId));
			return $query->row_array();
		}
		$sql = "SELECT * FROM groups ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getUsersPermissionData($id = null)
	{
		if($id) 
		{
			$sql = "SELECT *, user_group.id AS user_group_id FROM user_group JOIN users ON users.id=user_group.user_id JOIN groups ON groups.id=user_group.group_id WHERE user_group.id = ? AND users.is_deleted = 0 ORDER BY user_group.id DESC";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "SELECT *, user_group.id AS user_group_id, users.id AS user_id, groups.id AS group_id FROM user_group JOIN users ON users.id=user_group.user_id JOIN groups ON groups.id=user_group.group_id  WHERE users.is_deleted = 0 ORDER BY user_group.id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAllUsersPermissionData($id = null)
	{
		if($id) 
		{
			$sql = "SELECT *, user_group.id AS user_group_id FROM user_group JOIN users ON users.id=user_group.user_id JOIN groups ON groups.id=user_group.group_id WHERE user_group.id = ? AND users.is_deleted = 0 AND groups.is_deleted = 0 ORDER BY user_group.id DESC";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "SELECT *, user_group.id AS user_group_id, users.id AS user_id, groups.id AS group_id FROM user_group JOIN users ON users.id=user_group.user_id JOIN groups ON groups.id=user_group.group_id  WHERE users.is_deleted = 0 AND groups.is_deleted = 0 ORDER BY user_group.id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function create($data = '')
	{
		$create = $this->db->insert('groups', $data);
		return ($create == true) ? true : false;
	}
	public function create_user_permission($data = '')
	{
		$create = $this->db->insert('user_group', $data);
		return ($create == true) ? true : false;
	}

	public function edit($data, $id)
	{
		$this->db->where('id', $id);
		$update = $this->db->update('groups', $data);
		return ($update == true) ? true : false;	
	}
	public function update_user_permission($data, $id)
	{
		$this->db->where('id', $id);
		$update = $this->db->update('user_group', $data);
		return ($update == true) ? true : false;	
	}

	public function delete($id)
	{
		$this->db->where('id', $id);
		$data = array('is_deleted' => 1);
		$delete = $this->db->update('groups', $data);
		return ($delete == true) ? true : false;
	}

	public function remove_user_permission($id)
	{
		$this->db->where('id', $id);
		$data = array('is_deleted' => 1);
		$delete = $this->db->update('user_group', $data);
		return ($delete == true) ? true : false;
	}

	public function existInUserGroup($id)
	{
		$sql = "SELECT * FROM user_group WHERE group_id = ? AND is_deleted = 0";
		$query = $this->db->query($sql, array($id));
		return ($query->num_rows() == 1) ? true : false;
	}

	public function getUserGroupByUserId($user_id) 
	{
		$sql = "SELECT *, user_group.id as user_group_id FROM user_group 
		INNER JOIN groups ON groups.id = user_group.group_id 
		WHERE user_group.user_id = ? AND user_group.is_deleted = 0 AND groups.is_deleted = 0";
		$query = $this->db->query($sql, array($user_id));
		$result = $query->row_array();
		return $result;
	}
}
<?php 

class Model_department extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the department data */
	public function getDepartmentData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM department WHERE id = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM department WHERE is_deleted = 0 ORDER BY id Desc";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getCustomerDeparment($customer_id = null)
	{
		if($customer_id) {
			$sql = "SELECT *, customer_department.id as customer_department_table_id FROM customer_department JOIN department ON customer_department.department_id = department.id WHERE customer_id = ? AND department.is_deleted = 0 AND customer_department.is_deleted = 0";
			$query = $this->db->query($sql, array($customer_id));
			return $query->row_array();
		}
		$sql = "SELECT *, department.id as department_table_id, customer_department.id as customer_department_table_id FROM customer_department JOIN department ON customer_department.department_id = department.id WHERE department.is_deleted = 0 AND customer_department.is_deleted = 0";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAllCustomerDeparment($customer_id = null)
	{
		if($customer_id) {
			$sql = "SELECT *, customer_department.id as customer_department_table_id FROM customer_department JOIN department ON customer_department.department_id = department.id WHERE customer_id = ?";
			$query = $this->db->query($sql, array($customer_id));
			return $query->row_array();
		}
		$sql = "SELECT *, department.id as department_table_id, customer_department.id as customer_department_table_id FROM customer_department JOIN department ON customer_department.department_id = department.id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function fecthCustomerDeparmentRows($department_id)
	{		
		$sql = "SELECT * FROM customer_department WHERE department_id = ? AND is_deleted = 0";
		$query = $this->db->query($sql, array($department_id));
		return $query->result_array();
	}

	public function create($data)
	{
		if($data) {
			$insert = $this->db->insert('department', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function update($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('department', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id)
	{
		if($id) {
			$data = array('is_deleted' => 1);
			$this->db->where('id', $id);
			$delete = $this->db->update('department', $data);
			return ($delete == true) ? true : false;
		}
	}

	public function departmentExist($department_name)
	{
		if($department_name) {
			$sql = "SELECT * FROM department WHERE department_name = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($department_name));
			return $query->num_rows();
		}
	}
	public function departmentExistRow($department_name)
	{
		if($department_name) {
			$sql = "SELECT * FROM department WHERE department_name = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($department_name));
			return $query->row_array();
		}
	}

}
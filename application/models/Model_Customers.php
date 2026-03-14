<?php 

class Model_Customers extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the customers data */
	public function getCustomerData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM customers WHERE id = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "SELECT * FROM customers WHERE is_deleted = 0 ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAllCustomerData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM customers WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "SELECT * FROM customers ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function customerExist($fullname)
	{
		if($fullname) {
			$sql = "SELECT * FROM customers WHERE full_name = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($fullname));
			return $query->num_rows();
		}
	}

	public function customerExistRow($fullname)
	{
		if($fullname) {
			$sql = "SELECT * FROM customers WHERE full_name = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($fullname));
			return $query->row_array();
		}
	}

	public function create($data)
	{
		if($data) {
			$insert = $this->db->insert('customers', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function update($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('customers', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id)
	{
		if($id) {
			$data = array('is_deleted' => 1);
			$this->db->where('id', $id);
			$update = $this->db->update('customers', $data);
			return ($update == true) ? true : false;
		}
	}

	public function countTotalCustomers()
	{
		$sql = "SELECT * FROM customers WHERE is_deleted = 0";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	public function countTotalDepartments()
	{
		$sql = "SELECT * FROM customer_department where is_deleted = 0";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	public function getCustomerOrderRows($customer_id)
	{
		$sql = "SELECT * FROM company_sales_order WHERE customer_id = ?";
		$query = $this->db->query($sql, array($customer_id));
		return $query->num_rows();
	}
}
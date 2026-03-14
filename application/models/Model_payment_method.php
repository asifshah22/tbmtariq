<?php 

class Model_payment_method extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getPaymentMethodData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM payment_method WHERE id = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM payment_method WHERE is_deleted = 0 ORDER BY id desc";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function create($data)
	{
		if($data) {
			$insert = $this->db->insert('payment_method', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function update($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('payment_method', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id)
	{
		if($id) {
			$data = array('is_deleted' => 1);
			$this->db->where('id', $id);
			$delete = $this->db->update('payment_method', $data);
			return ($delete == true) ? true : false;
		}
	}

}
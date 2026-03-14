<?php 

class Model_category extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the brand data */
	public function getCategoryData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM categories WHERE id = ? AND is_deleted = ?";
			$query = $this->db->query($sql, array($id, 0));
			return $query->row_array();
		}
		$sql = "SELECT * FROM categories where is_deleted = 0 ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	// for report if that has been deleted but has price though for record
	public function getAllCategoryData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM categories WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "SELECT * FROM categories ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function create($data)
	{
		if($data) {
			$insert = $this->db->insert('categories', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function update($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('categories', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id)
	{
		if($id) {
			$data = array('is_deleted' => 1);
			$this->db->where('id', $id);
			$delete = $this->db->update('categories', $data);
			return ($delete == true) ? true : false;
		}
	}

	public function countTotalCategories()
	{
		$sql = "SELECT * FROM categories WHERE is_deleted = 0";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

}
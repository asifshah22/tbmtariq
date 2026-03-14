<?php 

class Model_loan extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the brand data */
	public function getLoanData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM loan WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM loan ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function create($data)
	{
		if($data) {
			$insert = $this->db->insert('loan', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function checkExistingUnpaidLoan($vendor_id)
	{
		if($vendor_id) {
			$sql = "SELECT * FROM loan WHERE supplier_id = ? AND paid_status = 0";
			$query = $this->db->query($sql, array($vendor_id));
			return $query->row_array();
		}
	}

	public function update($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('loan', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('loan');
			return ($delete == true) ? true : false;
		}
	}
	public function getVendorsLoanById($id)
	{
		if($id) {
			$sql = "SELECT * FROM vendor_loan WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
	}

	public function getVendorLatestLoan($loan_id)
	{
		if($loan_id) {
			$sql = "SELECT MAX(id) as latest_vendor_loan_id FROM vendor_loan GROUP BY loan_id HAVING loan_id = ?";
			$query = $this->db->query($sql, array($loan_id));
			return $query->row_array();
		}
	}
	public function getVendorSecondLatestLoan($loan_id)
	{
		if($loan_id) {
			$sql = "SELECT MAX(id) as latest_vendor_loan_id FROM vendor_loan WHERE id < (SELECT MAX(id) FROM vendor_loan GROUP BY loan_id HAVING loan_id = ?) GROUP BY loan_id HAVING loan_id = ?";
			$query = $this->db->query($sql, array($loan_id, $loan_id));
			return $query->row_array();
		}
	}

	public function getVendorsLoan($loan_id)
	{
		if($loan_id) {
			$sql = "SELECT *, vendor_loan.id as vendor_loan_id, vendor_loan.amount as loan_amount, vendor_loan.installment_amount as vendor_installment_amount FROM vendor_loan JOIN loan ON loan.id = vendor_loan.loan_id WHERE loan_id = ? ORDER BY vendor_loan.id DESC";
			$query = $this->db->query($sql, array($loan_id));
			return $query->result_array();
		}
	}

	public function fetchVendorsLoan($vendor_id)
	{
		if($vendor_id) {
			$sql = "SELECT *, vendor_loan.id as vendor_loan_id, vendor_loan.amount as loan_amount, vendor_loan.installment_amount as vendor_installment_amount FROM vendor_loan JOIN loan ON loan.id = vendor_loan.loan_id WHERE loan.supplier_id = ? ORDER BY vendor_loan.id DESC";
			$query = $this->db->query($sql, array($vendor_id));
			return $query->result_array();
		}
	}

	public function getVendorData($vendor_id=null)
	{
		if($vendor_id) {
			$sql = "SELECT *, loan.id as loan_id FROM loan JOIN supplier ON loan.supplier_id = supplier.id WHERE supplier_id = ?";
			$query = $this->db->query($sql, array($vendor_id));
			return $query->result_array();
		}
		$sql = "SELECT * FROM loan JOIN supplier ON loan.supplier_id = supplier.id WHERE paid_status = 0 ORDER BY loan.id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getVendorRemainingLoanData($vendor_id)
	{
		if($vendor_id) {
			$sql = "SELECT *, loan.id AS loan_id FROM loan JOIN supplier ON loan.supplier_id = supplier.id WHERE loan.remaining_amount > 0 AND supplier_id = ?";
			$query = $this->db->query($sql, array($vendor_id));
			return $query->row_array();
		}
	}

	public function checkVendor($supplier_id)
	{
		if($supplier_id) {
			$sql = "SELECT * FROM supplier WHERE id = ?";
			$query = $this->db->query($sql, array($supplier_id));
			return $query->row_array();
		}
	}
	public function getLoanDeductions($order_id)
	{
		if($order_id)
		{
			$sql = "SELECT * FROM loan_deduction WHERE order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->row_array();
		}
	}
	public function fetchLoanDeductions($loan_id)
	{
		if($loan_id){
			$sql = "SELECT * FROM loan_deduction WHERE loan_id = ?";
			$query = $this->db->query($sql, array($loan_id));
			return $query->result_array();
		}
	}

	public function countGivenLoanAmount()
	{
		$sql = "SELECT SUM(amount) as given_loan_amount FROM loan";
		$query = $this->db->query($sql);
		return $query->row_array();
	}
	public function countRemainingLoanAmount()
	{
		$sql = "SELECT SUM(remaining_amount) as remaining_loan_amount FROM loan where paid_status = 0";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

}
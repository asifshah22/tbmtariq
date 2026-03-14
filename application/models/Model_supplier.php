<?php 

class Model_supplier extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/*get the active suppliers information*/
	// public function getActiveSuppliers()
	// {
	// 	$sql = "SELECT * FROM supplier WHERE active = ?";
	// 	$query = $this->db->query($sql, array(1));
	// 	return $query->result_array();
	// }

	public function checkVendor($fname, $lname)
	{
		if($fname != "" && $lname != "") {
			$sql = "SELECT * FROM supplier WHERE first_name = ? AND last_name = ?";
			$query = $this->db->query($sql, array($fname, $lname));
			return $query->row_array();
		}
	}

	/* get the supplier data */
	public function getSupplierData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM supplier WHERE id = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM supplier WHERE is_deleted = 0 ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAllSupplierData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM supplier WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM supplier WHERE ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getSupplierOBPaymentsData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM supplier_ob_payments WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM supplier_ob_payments ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function fetchSupplierPaymentsData($vendor_id, $most_recent_order_id = 0)
	{
		if($vendor_id) {
			$sql = "SELECT * FROM supplier_ob_payments WHERE vendor_id = ? AND most_recent_order_id = ?";
			$query = $this->db->query($sql, array($vendor_id, $most_recent_order_id));
			return $query->result_array();
		}
	}

	public function getSupplierPaymentsData($order_id)
	{
		if($order_id) {
			$sql = "SELECT * FROM supplier_ob_payments WHERE most_recent_order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->result_array();
		}
	}

	public function fetchSupplierOBPayments($supplier_id)
	{
		if($supplier_id) {
			$sql = "SELECT * FROM supplier_ob_payments WHERE vendor_id = ?";
			$query = $this->db->query($sql, array($supplier_id));
			return $query->result_array();
		}
	}

	public function getSortedSupplierData()
	{
		$sql = "SELECT * FROM supplier ORDER BY first_name";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getPhoneData($id)
	{
		$sql = "SELECT * FROM phone WHERE suplier_id = ?";
		$query = $this->db->query($sql, array($id));
		return $query->result();
	}

	public function create($data)
	{
		if($data) {
			$insert = $this->db->insert('supplier', $data);
			return ($insert == true) ? true : false;
		}
	}
	

	public function add_phone($data)
	{
		if($data) {
			$insert = $this->db->insert('phone', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function update($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('supplier', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id)
	{
		if($id) {
			$data = array('is_deleted' => 1);
			$this->db->where('id', $id);
			$delete = $this->db->update('supplier', $data);
			return ($delete == true) ? true : false;
		}
	}

	// For Loan
	public function create_account($data)
	{
		if($data) {
			$insert = $this->db->insert('accounts', $data);
			return ($insert == true) ? true : false;
		}
	}
	/* get the customer account data */
	public function getSupplierAccountData($vendor_id)
	{
		if($vendor_id) {
			$sql = "SELECT * FROM accounts WHERE vendor_id = ?";
			$query = $this->db->query($sql, array($vendor_id));
			return $query->row_array();
		}
	}
	public function updateAccount($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('accounts', $data);
			return ($update == true) ? true : false;
		}
	}

	// For Ledger
	public function create_ledger($data)
	{
		if($data) {
			$insert = $this->db->insert('vendor_ledger', $data);
			return ($insert == true) ? true : false;
		}
	}
	/* get the ledger data from LoanID */
	public function getLedgerData($loan_id)
	{
		if($loan_id) {
			$sql = "SELECT * FROM vendor_ledger WHERE loan_id = ?";
			$query = $this->db->query($sql, array($loan_id));
			return $query->row_array();
		}
	}
	public function getLedgerData2($cash_id)
	{
		if($cash_id) {
			$sql = "SELECT * FROM vendor_ledger WHERE cash_id = ?";
			$query = $this->db->query($sql, array($cash_id));
			return $query->row_array();
		}
	}
	public function removeLedger($id)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('vendor_ledger');
			return ($delete == true) ? true : false;
		}
	}
	public function updateLedger($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('vendor_ledger', $data);
			return ($update == true) ? true : false;
		}
	}
	public function countTotalVendors()
	{
		$sql = "SELECT * FROM supplier";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	public function supplierExist($firstname, $lastname)
	{
		$sql = "SELECT * FROM supplier WHERE first_name = ? AND last_name = ?";
		$query = $this->db->query($sql, array($firstname, $lastname));
		return $query->num_rows();
	}
	public function supplierExistRow($firstname, $lastname)
	{
		$sql = "SELECT * FROM supplier WHERE first_name = ? AND last_name = ?";
		$query = $this->db->query($sql, array($firstname, $lastname));
		return $query->row_array();
	}

	public function fetchOpeningBalancePayment($order_id, $supplier_id)
	{
		if($order_id && $supplier_id) {
			$sql = "SELECT * FROM supplier_ob_payments WHERE most_recent_order_id = ? AND vendor_id = ?";
			$query = $this->db->query($sql, array($order_id, $supplier_id));
			return $query->result_array();
		}
	}

	public function getVendorMostRecentOrderId($supplier_id)
	{
		if($supplier_id){
			$sql = "SELECT purchase_orders.id
			FROM purchase_orders INNER JOIN
			(SELECT id, MAX(datetime_created) as most_recent_order FROM purchase_orders GROUP BY vendor_id) tab1 
			ON purchase_orders.datetime_created = tab1.most_recent_order WHERE vendor_id = ?";
			$query = $this->db->query($sql, array($supplier_id));
			return $query->row_array();
		}
	}

	public function getVendorPreviousOrderId($supplier_id, $datetime)
	{
		if($supplier_id && $datetime)
		{
			$sql = "SELECT purchase_orders.id
			FROM purchase_orders INNER JOIN
			(SELECT id, MAX(datetime_created) as most_recent_order FROM purchase_orders WHERE datetime_created < ? AND vendor_id = ? GROUP BY vendor_id) tab1 ON purchase_orders.datetime_created = tab1.most_recent_order";
			$query = $this->db->query($sql, array($datetime, $supplier_id));
			return $query->row_array();
		}
	}
}
<?php 

class Model_vendor_ledger extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getVendorPurchasedOrders($vendor_id)
	{
		if($vendor_id){
			$sql = "SELECT * FROM purchase_orders JOIN purchase_items ON purchase_orders.id = purchase_items.purchase_order_id WHERE purchase_items.vendor_id = ? ORDER BY purchase_orders.id";
			$query = $this->db->query($sql, array($vendor_id));
			return $query->result_array();
		}
	}


}
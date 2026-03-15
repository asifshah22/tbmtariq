<?php

class Model_purchase_order extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createOrder($order_data, $items)
    {
        if (empty($order_data) || empty($items)) {
            return false;
        }

        $this->db->trans_start();

        $this->db->insert('purchase_orders_custom', $order_data);
        $order_id = $this->db->insert_id();

        foreach ($items as $item) {
            $item['purchase_order_id'] = $order_id;
            $this->db->insert('purchase_order_items_custom', $item);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return false;
        }

        return $order_id;
    }

    public function getOrder($order_id)
    {
        if (!$order_id) {
            return null;
        }

        $sql = 'SELECT * FROM purchase_orders_custom WHERE id = ?';
        $query = $this->db->query($sql, array($order_id));
        return $query->row_array();
    }

    public function getOrders()
    {
        $sql = "SELECT poc.*, supplier.first_name, supplier.last_name
                FROM purchase_orders_custom AS poc
                LEFT JOIN supplier ON supplier.id = poc.vendor_id
                ORDER BY poc.id DESC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getOrderItems($order_id)
    {
        if (!$order_id) {
            return array();
        }

        $sql = 'SELECT * FROM purchase_order_items_custom WHERE purchase_order_id = ? ORDER BY id ASC';
        $query = $this->db->query($sql, array($order_id));
        return $query->result_array();
    }

    public function updateOrder($order_id, $order_data, $items)
    {
        if (!$order_id || empty($order_data) || empty($items)) {
            return false;
        }

        $this->db->trans_start();

        $this->db->where('id', $order_id);
        $this->db->update('purchase_orders_custom', $order_data);

        $this->db->where('purchase_order_id', $order_id);
        $this->db->delete('purchase_order_items_custom');

        foreach ($items as $item) {
            $item['purchase_order_id'] = $order_id;
            $this->db->insert('purchase_order_items_custom', $item);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return false;
        }

        return true;
    }

    public function removeOrder($order_id)
    {
        if (!$order_id) {
            return false;
        }

        $this->db->trans_start();
        $this->db->where('purchase_order_id', $order_id);
        $this->db->delete('purchase_order_items_custom');

        $this->db->where('id', $order_id);
        $this->db->delete('purchase_orders_custom');
        $this->db->trans_complete();

        return $this->db->trans_status() !== false;
    }

    public function getVendorProductsWithLatestPrice($vendor_id)
    {
        if (!$vendor_id) {
            return array();
        }

        $sql = "
            SELECT tab1.product_id
                , tab1.category_id
                , tab1.price
                , products.name AS product_name
                , categories.name AS category_name
            FROM product_prices tab1
            INNER JOIN (
                SELECT product_id, category_id, vendor_id, MAX(date_time) as max_date_time
                FROM product_prices
                WHERE vendor_id = ? AND is_deleted = 0
                GROUP BY product_id, category_id, vendor_id
            ) tab2 ON tab1.product_id = tab2.product_id
                AND tab1.category_id = tab2.category_id
                AND tab1.vendor_id = tab2.vendor_id
                AND tab1.date_time = tab2.max_date_time
            INNER JOIN products ON tab1.product_id = products.id
            INNER JOIN product_category ON tab1.product_id = product_category.product_id
                AND tab1.category_id = product_category.category_id
            LEFT JOIN categories ON categories.id = product_category.category_id
            WHERE tab1.is_deleted = 0
            ORDER BY products.name ASC
        ";

        $query = $this->db->query($sql, array($vendor_id));
        return $query->result_array();
    }
}

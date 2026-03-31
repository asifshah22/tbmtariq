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

    public function generatePoNumber()
    {
        $sql = "SELECT MAX(id) AS max_id FROM purchase_orders_custom";
        $query = $this->db->query($sql);
        $row = $query->row_array();
        $next = isset($row['max_id']) ? (int)$row['max_id'] + 1 : 1;
        return 'PO-' . date('Ymd') . '-' . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
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

    public function getOrdersForDropdown()
    {
        $sql = "SELECT poc.id, poc.po_number, poc.supply_status, poc.payment_status, poc.vendor_id, supplier.first_name, supplier.last_name
                FROM purchase_orders_custom AS poc
                LEFT JOIN supplier ON supplier.id = poc.vendor_id
                ORDER BY poc.id DESC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function markSupplyComplete($order_id)
    {
        if (!$order_id) {
            return false;
        }

        $this->db->where('id', $order_id);
        return $this->db->update('purchase_orders_custom', array('supply_status' => 'Complete'));
    }

    public function markPaymentComplete($order_id)
    {
        if (!$order_id) {
            return false;
        }

        $this->db->where('id', $order_id);
        return $this->db->update('purchase_orders_custom', array('payment_status' => 'Complete'));
    }

    public function updateSupplyStatusFromPurchasing($po_id, $purchase_order_id = null)
    {
        if (!$po_id) {
            return null;
        }

        $link = $this->getPurchasingLinkField();
        if (!$link) {
            return null;
        }

        $po_number = $this->getPoNumber($po_id);
        if (!$po_number) {
            return null;
        }

        if ($purchase_order_id) {
            $this->linkPurchasingToPoAll($purchase_order_id, $po_number, $po_id);
        }

        $ordered_items = $this->getOrderItems($po_id);
        if (empty($ordered_items)) {
            return null;
        }

        $link_value = $this->getPurchasingLinkValue($link, $po_number, $po_id);
        if ($link_value === null || $link_value === '') {
            return null;
        }

        $purchased_totals = $this->getPurchasedTotalsByName($link_value, $link);
        $ordered_totals = array();
        foreach ($ordered_items as $item) {
            $name = isset($item['part_name']) ? $this->normalizeItemName($item['part_name']) : '';
            $ordered_qty = isset($item['qty']) ? (float)$item['qty'] : 0.0;
            if ($ordered_qty <= 0 || $name === '') {
                continue;
            }
            if (!isset($ordered_totals[$name])) {
                $ordered_totals[$name] = 0.0;
            }
            $ordered_totals[$name] += $ordered_qty;
        }

        $complete = true;
        foreach ($ordered_totals as $name => $ordered_qty_total) {
            $purchased_qty = isset($purchased_totals[$name]) ? (float)$purchased_totals[$name] : 0.0;
            if ($purchased_qty + 1e-9 < $ordered_qty_total) {
                $complete = false;
                break;
            }
        }

        if ($complete) {
            $this->markSupplyComplete($po_id);
        }

        return $complete;
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

    private function getPurchasingLinkField()
    {
        if ($this->db->field_exists('po_number_custom', 'purchase_orders')) {
            return array('table' => 'purchase_orders', 'field' => 'po_number_custom');
        }
        if ($this->db->field_exists('purchase_order_custom_id', 'purchase_orders')) {
            return array('table' => 'purchase_orders', 'field' => 'purchase_order_custom_id');
        }
        if ($this->db->field_exists('po_id', 'purchase_orders')) {
            return array('table' => 'purchase_orders', 'field' => 'po_id');
        }
        if ($this->db->field_exists('po_number_custom', 'purchase_items')) {
            return array('table' => 'purchase_items', 'field' => 'po_number_custom');
        }
        if ($this->db->field_exists('purchase_order_custom_id', 'purchase_items')) {
            return array('table' => 'purchase_items', 'field' => 'purchase_order_custom_id');
        }
        if ($this->db->field_exists('po_id', 'purchase_items')) {
            return array('table' => 'purchase_items', 'field' => 'po_id');
        }

        return null;
    }

    private function linkPurchasingToPoAll($purchase_order_id, $po_number, $po_id = null)
    {
        if (!$purchase_order_id || !$po_number) {
            return;
        }

        if ($this->db->field_exists('po_number_custom', 'purchase_orders')) {
            $this->db->where('id', $purchase_order_id);
            $this->db->update('purchase_orders', array('po_number_custom' => $po_number));
        }

        if ($po_id && $this->db->field_exists('purchase_order_custom_id', 'purchase_orders')) {
            $this->db->where('id', $purchase_order_id);
            $this->db->update('purchase_orders', array('purchase_order_custom_id' => $po_id));
        }

        if ($po_id && $this->db->field_exists('po_id', 'purchase_orders')) {
            $this->db->where('id', $purchase_order_id);
            $this->db->update('purchase_orders', array('po_id' => $po_id));
        }

        if ($this->db->field_exists('po_number_custom', 'purchase_items')) {
            $this->db->where('purchase_order_id', $purchase_order_id);
            $this->db->update('purchase_items', array('po_number_custom' => $po_number));
        }

        if ($po_id && $this->db->field_exists('purchase_order_custom_id', 'purchase_items')) {
            $this->db->where('purchase_order_id', $purchase_order_id);
            $this->db->update('purchase_items', array('purchase_order_custom_id' => $po_id));
        }

        if ($po_id && $this->db->field_exists('po_id', 'purchase_items')) {
            $this->db->where('purchase_order_id', $purchase_order_id);
            $this->db->update('purchase_items', array('po_id' => $po_id));
        }
    }

    private function getPurchasedTotalsByName($link_value, $link)
    {
        $totals = array();

        if (empty($link['table']) || empty($link['field'])) {
            return $totals;
        }

        if ($link['table'] === 'purchase_orders') {
            $sql = "SELECT products.name AS product_name, SUM(purchase_items.qty) AS qty
                    FROM purchase_orders
                    INNER JOIN purchase_items ON purchase_items.purchase_order_id = purchase_orders.id
                    INNER JOIN products ON products.id = purchase_items.product_id
                    WHERE purchase_orders.".$link['field']." = ?
                    GROUP BY products.name";
            $query = $this->db->query($sql, array($link_value));
        } else {
            $sql = "SELECT products.name AS product_name, SUM(purchase_items.qty) AS qty
                    FROM purchase_items
                    INNER JOIN products ON products.id = purchase_items.product_id
                    WHERE purchase_items.".$link['field']." = ?
                    GROUP BY products.name";
            $query = $this->db->query($sql, array($link_value));
        }

        $rows = $query ? $query->result_array() : array();
        foreach ($rows as $row) {
            $name = isset($row['product_name']) ? $this->normalizeItemName($row['product_name']) : '';
            if ($name === '') {
                continue;
            }
            $totals[$name] = isset($row['qty']) ? (float)$row['qty'] : 0.0;
        }

        return $totals;
    }

    private function getPurchasingLinkValue($link, $po_number, $po_id)
    {
        if (empty($link['field'])) {
            return null;
        }

        $id_fields = array('purchase_order_custom_id', 'po_id');
        if (in_array($link['field'], $id_fields, true)) {
            return $po_id ? (int)$po_id : null;
        }

        return $po_number;
    }

    private function normalizeItemName($name)
    {
        if ($name === null) {
            return '';
        }

        $name = trim((string)$name);
        if ($name === '') {
            return '';
        }

        $name = str_replace(array('–', '—', '−'), '-', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return strtolower($name);
    }

    private function getPoNumber($po_id)
    {
        if (!$po_id) {
            return null;
        }

        $order = $this->getOrder($po_id);
        if (!$order || empty($order['po_number'])) {
            return null;
        }

        return $order['po_number'];
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
                , tab1.unit_id
                , tab1.price
                , products.name AS product_name
                , categories.name AS category_name
                , units.unit_name AS unit_name
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
            LEFT JOIN units ON units.id = tab1.unit_id
            LEFT JOIN categories ON categories.id = product_category.category_id
            WHERE tab1.is_deleted = 0
            ORDER BY products.name ASC
        ";

        $query = $this->db->query($sql, array($vendor_id));
        return $query->result_array();
    }
}

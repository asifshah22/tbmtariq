<?php 

class Model_products extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the brand data */
	public function getProductData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM products where id = ? AND is_deleted = ?";
			$query = $this->db->query($sql, array($id, 0));
			return $query->row_array();
		}

		$sql = "SELECT * FROM products WHERE is_deleted = 0 ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAllProductData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM products where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM products ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getVendorProductsData($vendor_id)
	{
		if($vendor_id)
		{
		    // * is removed // updated 
			$sql = "
                SELECT tab1.id as product_prices_id
                , products.name AS product_name
                , tab1.category_id as category_id
                , categories.name as category_name
                , tab1.product_id AS select_product_price 
                FROM product_prices tab1 
                INNER JOIN (
                    SELECT product_prices.category_id, product_prices.product_id, product_prices.vendor_id, MAX(date_time) as max_date_time 
                    FROM product_prices 
                    GROUP BY product_prices.product_id, product_prices.vendor_id, product_prices.category_id 
                    HAVING product_prices.vendor_id = ?
                ) tab2 ON tab1.date_time = tab2.max_date_time AND tab1.vendor_id=tab2.vendor_id 
                INNER JOIN products ON tab1.product_id = products.id 
                JOIN supplier ON tab1.vendor_id = supplier.id 
                INNER JOIN product_category ON products.id = product_category.product_id 
                    AND tab1.category_id = product_category.category_id 
                LEFT JOIN categories ON categories.id = product_category.category_id 
                WHERE tab1.is_deleted = 0
            ";
			$query = $this->db->query($sql, array($vendor_id));
			return $query->result_array();
		}
	}

	public function getCurrentVendorProductsRate()
	{
        $sql = "
            SELECT *
            , tab1.id as product_prices_id
            , products.name AS product_name
            , categories.id as category_id
            , categories.name as category_name
            , tab1.product_id AS select_product_price 
            FROM product_prices tab1 
            INNER JOIN (
                SELECT product_prices.category_id, product_prices.product_id, product_prices.vendor_id, MAX(date_time) as max_date_time 
                FROM product_prices 
                GROUP BY product_prices.product_id, product_prices.vendor_id, product_prices.category_id
            ) tab2 ON tab1.date_time = tab2.max_date_time 
                AND tab1.vendor_id=tab2.vendor_id 
            INNER JOIN products ON tab1.product_id = products.id 
            JOIN supplier ON tab1.vendor_id = supplier.id 
            INNER JOIN product_category ON products.id = product_category.product_id 
                AND tab1.category_id = product_category.category_id 
            LEFT JOIN categories ON categories.id =product_category.category_id
        ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getGroupedCurrentVendorProductsRate()
	{
		$sql = "
            SELECT *
            , tab1.id as product_prices_id
            , products.name AS product_name
            , categories.id as category_id
            , categories.name as category_name
            , tab1.product_id AS select_product_price 
            FROM product_prices tab1 
            INNER JOIN (
                SELECT product_prices.category_id, product_prices.product_id, product_prices.vendor_id, MAX(date_time) as max_date_time 
                FROM product_prices 
                GROUP BY product_prices.product_id, product_prices.vendor_id, product_prices.category_id
            ) tab2 ON tab1.date_time = tab2.max_date_time 
                AND tab1.vendor_id=tab2.vendor_id 
            INNER JOIN products ON tab1.product_id = products.id 
            INNER JOIN supplier ON tab1.vendor_id = supplier.id 
            INNER JOIN product_category ON products.id = product_category.product_id 
                AND tab1.category_id = product_category.category_id 
            LEFT JOIN categories ON categories.id = product_category.category_id 
            ORDER BY supplier.first_name, tab1.id
        ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getProductPricesData($id = null)
	{
		if($id)
		{
			$sql = "
                SELECT *
                , product_prices.date_time AS product_prices_datetime
                , product_prices.id AS product_prices_id
                , products.id AS product_id, supplier.id AS supplier_id
                , products.name AS product_name 
                FROM product_prices JOIN products ON product_prices.product_id = products.id 
                INNER JOIN supplier ON product_prices.vendor_id = supplier.id 
                INNER JOIN product_category ON product_prices.product_id = product_category.product_id
                    AND product_prices.category_id = product_category.category_id
                WHERE product_prices.id = ? 
                ORDER BY product_prices.id DESC
            ";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "
            SELECT *
            , product_prices.date_time AS product_prices_datetime
            , product_prices.id AS product_prices_id
            , products.id AS product_id, supplier.id AS supplier_id
            , products.name AS product_name 
            FROM product_prices 
            INNER JOIN products ON product_prices.product_id = products.id 
            INNER JOIN supplier ON product_prices.vendor_id = supplier.id 
            INNER JOIN product_category ON product_prices.product_id = product_category.product_id 
                AND product_prices.category_id = product_category.category_id
            ORDER BY product_prices.id DESC
        ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getGroupedProductPricesData()
	{
		$sql = "
            SELECT first_name, last_name
            , product_prices.category_id category_id
            , unit_id, price
            , product_prices.date_time AS product_prices_datetime
            , product_prices.id AS product_prices_id
            , products.id AS product_id, supplier.id AS supplier_id
            , products.name AS product_name 
            FROM product_prices 
            INNER JOIN products ON product_prices.product_id = products.id 
            INNER JOIN supplier ON product_prices.vendor_id = supplier.id 
            INNER JOIN product_category ON product_prices.product_id = product_category.product_id 
                AND product_prices.category_id  = product_category.category_id 
            where product_prices.is_deleted = 0 
            ORDER BY supplier.first_name, product_prices.id DESC
        ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function getVendorProductPricesData($vendor_id)
	{
		if($vendor_id)
		{
			$sql = "
                SELECT first_name, last_name
                , product_prices.category_id category_id
                , unit_id, price
                , product_prices.date_time AS product_prices_datetime
                , product_prices.id AS product_prices_id
                , products.id AS product_id, supplier.id AS supplier_id
                , products.name AS product_name 
                FROM product_prices 
                INNER JOIN products ON product_prices.product_id = products.id 
                INNER JOIN supplier ON product_prices.vendor_id = supplier.id 
                INNER JOIN product_category ON product_prices.product_id = product_category.product_id 
                    AND product_prices.category_id = product_category.category_id
                LEFT JOIN categories ON product_category.category_id = categories.id 
                WHERE supplier.id = ? 
                    AND product_prices.is_deleted = 0 
                ORDER BY product_prices.id DESC
            ";
			$query = $this->db->query($sql, array($vendor_id));
			return $query->result_array();
		}
	}
	function getGroupedVendorProductPricesData($vendor_id)
	{
		if($vendor_id)
		{
			$sql = "
                SELECT *
                , products.name AS product_name
                , product_prices.date_time AS product_prices_datetime 
                FROM product_prices 
                INNER JOIN products ON product_prices.product_id = products.id 
                INNER JOIN supplier ON product_prices.vendor_id = supplier.id 
                INNER JOIN product_category ON product_prices.product_id = product_category.product_id 
                    AND product_prices.category_id = product_category.category_id
                WHERE supplier.id = ? 
                ORDER BY supplier.first_name, product_prices.id DESC
            ";
			$query = $this->db->query($sql, array($vendor_id));
			return $query->result_array();
		}
	}

	function getSalePricesData($id = null)
	{
		if($id)
		{
			$sql = "
                SELECT *
                , sale_prices.date_time AS sale_prices_datetime
                , sale_prices.id AS sale_prices_id
                , products.id AS product_id
                , products.name AS product_name 
                FROM sale_prices 
                INNER JOIN products ON sale_prices.product_id = products.id 
                INNER JOIN product_category ON sale_prices.product_id = product_category.product_id 
                    AND sale_prices.category_id = product_category.category_id
                WHERE sale_prices.id = ? AND sale_prices.is_deleted = ? 
                ORDER BY sale_prices.id DESC
            ";
			$query = $this->db->query($sql, array($id, 0));
			return $query->row_array();
		}
		$sql = "
            SELECT *
            , sale_prices.date_time AS sale_prices_datetime
            , sale_prices.id AS sale_prices_id
            , products.id AS product_id
            , products.name AS product_name 
            FROM sale_prices 
            INNER JOIN products ON sale_prices.product_id = products.id 
            INNER JOIN product_category ON sale_prices.product_id = product_category.product_id 
                AND sale_prices.category_id = product_category.category_id
            WHERE sale_prices.is_deleted = 0 
            ORDER BY sale_prices.id DESC
        ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getAllSalePricesData($id = null)
	{
		if($id)
		{
			$sql = "
                SELECT *
                , sale_prices.date_time AS sale_prices_datetime
                , sale_prices.id AS sale_prices_id
                , products.id AS product_id
                , products.name AS product_name 
                FROM sale_prices 
                INNER JOIN products ON sale_prices.product_id = products.id 
                INNER JOIN product_category ON sale_prices.product_id = product_category.product_id 
                    AND sale_prices.category_id = product_category.category_id
                WHERE sale_prices.id = ? 
                ORDER BY sale_prices.id DESC
            ";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "
            SELECT *
            , sale_prices.date_time AS sale_prices_datetime
            , sale_prices.id AS sale_prices_id
            , products.id AS product_id
            , products.name AS product_name 
            FROM sale_prices 
            INNER JOIN products ON sale_prices.product_id = products.id 
            INNER JOIN product_category ON sale_prices.product_id = product_category.product_id 
                AND sale_prices.category_id = product_category.category_id
            ORDER BY sale_prices.id DESC
        ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCurrentSalePrices()
	{
		$sql = "
            SELECT *
            , products.name AS product_name
            , categories.id as category_id
            , categories.name as category_name 
            FROM sale_prices tab1 
            INNER JOIN (
                SELECT sale_prices.category_id, sale_prices.product_id, sale_prices.unit_id, MAX(date_time) as max_date_time 
                FROM sale_prices 
                GROUP BY sale_prices.product_id, sale_prices.unit_id, sale_prices.category_id
            ) tab2 ON tab1.date_time = tab2.max_date_time 
                AND tab1.unit_id=tab2.unit_id 
            INNER JOIN products ON tab1.product_id = products.id 
            INNER JOIN product_category ON products.id = product_category.product_id 
                AND tab1.category_id = product_category.category_id 
            LEFT JOIN categories ON categories.id = product_category.category_id
        ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getSalePrices($product_id, $category_id, $unit_id)
	{
		if($product_id && $unit_id)
		{
			$sql = "
                SELECT *
                , tab1.date_time AS sale_prices_datetime
                , tab1.id AS sale_prices_id
                , products.id AS product_id
                , products.name AS product_name 
                FROM sale_prices tab1 
                INNER JOIN (
                    SELECT sale_prices.product_id, sale_prices.unit_id, MAX(date_time) as max_date_time 
                    FROM sale_prices 
                    GROUP BY sale_prices.product_id, sale_prices.unit_id
                ) tab2 ON tab1.date_time = tab2.max_date_time 
                    AND tab1.product_id=tab2.product_id 
                    AND tab1.unit_id=tab2.unit_id 
                INNER JOIN products ON tab1.product_id = products.id 
                INNER JOIN product_category ON tab1.product_id = product_category.product_id 
                    AND tab1.category_id = product_category.category_id 
                WHERE tab1.product_id = ? AND tab1.category_id = ? AND tab1.unit_id = ? 
                ORDER BY tab1.id DESC
            ";
			$query = $this->db->query($sql, array($product_id, $category_id, $unit_id));
			return $query->row_array();
		}
	}

	public function getProductPrices($category_id, $product_id, $vendor_id, $unit_id)
	{
		$sql = "
            SELECT *
            , tab1.date_time AS product_prices_datetime
            , tab1.id AS product_prices_id
            , products.id AS product_id
            , supplier.id AS supplier_id
            , products.name AS product_name
			FROM product_prices tab1 
			INNER JOIN (
                SELECT product_prices.product_id, product_prices.category_id, product_prices.vendor_id, product_prices.unit_id, MAX(date_time) as max_date_time 
                FROM product_prices 
                GROUP BY product_prices.product_id, product_prices.category_id, product_prices.vendor_id, product_prices.unit_id
            ) tab2 ON tab1.date_time = tab2.max_date_time 
                AND tab1.product_id = tab2.product_id 
                AND tab1.category_id = tab2.category_id 
                AND tab1.vendor_id=tab2.vendor_id 
                AND tab1.unit_id = tab2.unit_id 
            INNER JOIN products ON tab1.product_id = products.id 
            INNER JOIN supplier ON tab1.vendor_id = supplier.id 
            INNER JOIN product_category ON tab1.product_id = product_category.product_id 
                AND tab1.category_id = product_category.category_id 
            WHERE tab1.product_id = ? 
                AND tab1.category_id = ? 
                AND tab1.vendor_id = ? 
                AND tab1.unit_id = ? 
            ORDER BY tab1.id DESC";
		$query = $this->db->query($sql, array($product_id, $category_id, $vendor_id, $unit_id));
		return $query->row_array();
	}

	public function getPurchaseReturnData($product_id, $vendor_id, $category_id, $unit_id, $order_id)
	{
		if($product_id && $vendor_id)
		{
			$sql = "
                SELECT purchase_returns.id as purchase_returns_id
                , purchase_returns.category_id
                , purchase_returns.product_id
                , purchase_returns.vendor_id
                , purchase_returns.unit_id
                , purchase_returns.product_order_id 
                FROM purchase_returns 
                WHERE purchase_returns.product_paid_order_id = 0 
                    AND purchase_returns.status = 0 
                GROUP BY purchase_returns_id
                    , purchase_returns.category_id
                    , purchase_returns.product_id
                    ,  purchase_returns.vendor_id
                    , purchase_returns.unit_id
                    , purchase_returns.product_order_id 
                HAVING purchase_returns.product_id = ? 
                    AND purchase_returns.vendor_id = ? 
                    AND purchase_returns.category_id = ? 
                    AND purchase_returns.unit_id = ? 
                    AND purchase_returns.product_order_id = ?
            ";
			$query = $this->db->query($sql, array($product_id, $vendor_id, $category_id, $unit_id, $order_id));
			return $query->row_array();
		}
	}

	public function getPurchaseReturns($product_id, $vendor_id, $category_id)
	{
		if($product_id && $vendor_id)
		{
			$sql = "
                SELECT *
                , COUNT(*) AS num_rows
                , SUM(qty) AS total_qty
                , purchase_returns.id AS purchase_returns_id 
                FROM purchase_returns 
                GROUP BY purchase_returns_id
                    , purchase_returns.category_id
                    , purchase_returns.product_id
                    ,  purchase_returns.vendor_id 
                HAVING purchase_returns.product_id = ? 
                    AND purchase_returns.vendor_id = ? 
                    AND purchase_returns.category_id = ? 
                    AND purchase_returns.product_paid_order_id = 0 
                    AND purchase_returns.status = 0
            ";
			$query = $this->db->query($sql, array($product_id, $vendor_id, $category_id));
			return $query->row_array();
		}
	}
	public function getPurchaseReturnTotalQTY($product_id, $vendor_id, $category_id)
	{
		if($product_id && $vendor_id)
		{
			$sql = "SELECT COUNT(*) AS num_rows, SUM(qty) AS total_qty, purchase_returns.category_id, purchase_returns.product_id,  purchase_returns.vendor_id FROM purchase_returns WHERE purchase_returns.product_paid_order_id = 0 AND purchase_returns.status = 0 GROUP BY purchase_returns.category_id, purchase_returns.product_id,  purchase_returns.vendor_id HAVING purchase_returns.product_id = ? AND purchase_returns.vendor_id = ? AND purchase_returns.category_id = ?";
			$query = $this->db->query($sql, array($product_id, $vendor_id, $category_id));
			return $query->row_array();
		}
	}
	public function getPurchaseReturnRows($product_id, $vendor_id, $category_id)
	{
		if($product_id && $vendor_id)
		{
			$sql = "SELECT *, purchase_returns.id AS purchase_returns_id FROM purchase_returns WHERE purchase_returns.product_id = ? AND purchase_returns.vendor_id = ? AND purchase_returns.category_id = ? AND purchase_returns.product_paid_order_id = 0 AND purchase_returns.status = 0";
			$query = $this->db->query($sql, array($product_id, $vendor_id, $category_id));
			return $query->result_array();
		}
	}
	public function getProductCategoryData($product_id = null, $category_id = null)
	{
		if($product_id) {
			$sql = "
                SELECT products.id AS product_id
                , categories.id AS category_id
                , products.name AS product_name
                , categories.name AS category_name 
                , image
                , date_time
                , description
                FROM products 
                INNER JOIN product_category ON products.id = product_category.product_id 
                LEFT JOIN categories ON product_category.category_id = categories.id 
                WHERE products.id = ? 
                    AND product_category.category_id = ? 
                    AND products.is_deleted = ? 
                    AND product_category.is_deleted = ? 
                ORDER BY products.id DESC
            ";
			$query = $this->db->query($sql, array($product_id, $category_id, 0, 0));
			return $query->row_array();
		}
		$sql = "
            SELECT *
            , products.id AS product_id
            , categories.id AS category_id
            , products.name AS product_name
            , categories.name AS category_name 
            FROM products 
            INNER JOIN product_category ON products.id = product_category.product_id
            LEFT JOIN categories ON product_category.category_id = categories.id 
            where products.is_deleted = 0 
                AND product_category.is_deleted = 0 
            ORDER BY products.id DESC
        ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAllProductCategoryData($product_id = null, $category_id = null)
	{
		if($product_id) {
			$sql = "
                SELECT *
                , products.id AS product_id
                , categories.id AS category_id
                , products.name AS product_name
                , categories.name AS category_name 
                FROM products 
                INNER JOIN product_category ON products.id = product_category.product_id 
                LEFT JOIN categories ON product_category.category_id = categories.id 
                WHERE products.id = ? AND product_category.category_id = ? 
                ORDER BY products.id DESC
            ";
			$query = $this->db->query($sql, array($product_id, $category_id));
			return $query->row_array();
		}
		$sql = "
            SELECT *
            , products.id AS product_id
            , categories.id AS category_id
            , products.name AS product_name
            , categories.name AS category_name 
            FROM products 
            INNER JOIN product_category ON products.id = product_category.product_id 
            LEFT JOIN categories ON product_category.category_id = categories.id 
            ORDER BY products.id DESC
        ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getProductCategory($product_id, $category_id)
	{
		if($product_id) {
			$sql = "
                SELECT *
                , products.id AS product_id
                , categories.id AS category_id
                , products.name AS product_name
                , categories.name AS category_name 
                FROM products 
                INNER JOIN product_category ON products.id = product_category.product_id 
                LEFT JOIN categories ON product_category.category_id = categories.id 
                WHERE products.id = ? AND product_category.category_id = ? AND products.is_deleted = ? AND product_category.is_deleted = ?
            ";
			$query = $this->db->query($sql, array($product_id, $category_id, 0, 0));
			return $query->row_array();
		}
	}

	public function getProductsBillData($id = null)
	{
		if($id) {
			$sql = "SELECT *, product_order.id AS product_order_id, product_order.qty AS product_order_qty FROM product_order JOIN products ON products.id=product_order.product_id WHERE product_order.id = ? ORDER BY product_order.date_time DESC";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "SELECT *, product_order.id AS product_order_id, product_order.qty AS product_order_qty FROM products JOIN product_order ON products.id=product_order.product_id ORDER BY product_order.date_time DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getPurchaseReturnsData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM purchase_returns where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "SELECT * FROM purchase_returns ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function purchasedOrderPaymentsData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM purchase_order_payment where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "SELECT * FROM purchase_order_payment ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getPurchasedPaymentsData($order_id = null)
	{
		if($order_id) {
			$sql = "SELECT * FROM purchase_order_payment where purchase_order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->result_array();
		}
	}
	public function fetchPurchaseReturnsData($order_id = null)
	{
		if($order_id) {
			$sql = "SELECT * FROM purchase_returns where product_order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->result_array();
		}
	}
	public function getPurchaseReturnsAmount($order_id = null)
	{
		if($order_id) {
			$sql = "SELECT SUM(amount) as returns_amount FROM purchase_returns where product_order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->row_array();
		}
	}

	public function getStockProductData()
	{
		$sql = "
            SELECT *
            , tab1.name AS product_name
            , categories.name AS category_name 
            FROM products tab1 
            JOIN (
                SELECT COUNT(*) AS counter, items_stock.category_id, items_stock.product_id, items_stock.unit_id 
                FROM items_stock 
                GROUP BY items_stock.category_id, items_stock.product_id, items_stock.unit_id HAVING counter > 0
            ) tab2 ON tab1.id = tab2.product_id 
            LEFT JOIN categories ON tab2.category_id = categories.id JOIN units ON tab2.unit_id = units.id
        ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getFactoryStockData()
	{
		$sql = "
            SELECT *
            , tab1.name AS product_name
            , categories.name AS category_name 
            FROM products tab1 
            JOIN (
                SELECT items_stock.category_id, items_stock.product_id, items_stock.unit_id 
                FROM items_stock 
                GROUP BY items_stock.category_id, items_stock.product_id, items_stock.unit_id
            ) tab2 ON tab1.id = tab2.product_id 
            LEFT JOIN categories ON tab2.category_id = categories.id
        ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getStockOrderLevelData($id = null)
	{
		if($id) {
			$sql = "SELECT * From stock_order_level where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "SELECT * From stock_order_level";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getFactoryStockOrderLevelData($stock_type = 1)
	{
		if($stock_type) {
			$sql = "SELECT * From stock_order_level where stock_type = ?";
			$query = $this->db->query($sql, array($stock_type));
			return $query->row_array();
		}
	}
	public function getOfficeStockOrderLevelData($stock_type = 2)
	{
		if($stock_type) {
			$sql = "SELECT * From stock_order_level where stock_type = ?";
			$query = $this->db->query($sql, array($stock_type));
			return $query->row_array();
		}
	}

	public function getOfficeStockItemsData()
	{
		$sql = "SELECT *, tab1.name AS product_name, categories.name AS category_name FROM products tab1 JOIN 
		(SELECT office_items_stock.category_id, office_items_stock.product_id, office_items_stock.unit_id FROM office_items_stock GROUP BY office_items_stock.category_id, office_items_stock.product_id, office_items_stock.unit_id) tab2 ON tab1.id = tab2.product_id LEFT JOIN categories ON tab2.category_id = categories.id JOIN units ON tab2.unit_id = units.id";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getOfficeStockData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM office_items_stock where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM office_items_stock ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();	
	}

	public function getOfficeStockWithUnitsData($id = null)
	{
		$sql = "SELECT COUNT(*), SUM(quantity) as sum_qty, unit_id, product_id, category_id FROM office_items_stock GROUP BY unit_id, product_id, category_id ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();	
	}

	public function getOfficeStockTransferData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM office_stock_transfer where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM office_stock_transfer ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();	
	}

	public function countOfficeStockTransferItems($id)
	{
		if($id) {
			$sql = "SELECT * FROM office_stock_transfer_items WHERE office_stock_transfer_id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->num_rows();
		}
	}
	public function getOfficeStockTransferedItems($id)
	{
		if($id) {
			$sql = "SELECT * FROM office_stock_transfer_items WHERE office_stock_transfer_id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->result_array();
		}
	}

	public function getOfficeStockTransferedData($transfer_id)
	{
		if($transfer_id) {
			$sql = "SELECT * FROM office_stock_transfer_items WHERE office_stock_transfer_id = ?";
			$query = $this->db->query($sql, array($transfer_id));
			return $query->result_array();
		}
	}

	public function create($data)
	{
		if($data) {
			$insert = $this->db->insert('products', $data);
			$product_id = $this->db->insert_id();
			return ($product_id) ? $product_id : false;
		}
	}

	public function getProductsByName($product_name)
	{
		if($product_name) {
			$sql = "SELECT * FROM products WHERE name = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($product_name));
			return $query->result_array();
		}
	}

	public function createProductPrice($data)
	{
		if($data) {
			$insert = $this->db->insert('product_prices', $data);
			$product_price_id = $this->db->insert_id();
			return ($product_price_id) ? $product_price_id : false;
		}
	}

	public function updateProductPrice($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('product_prices', $data);
			return ($update == true) ? true : false;
		}
	}
	public function removeProductPrice($id)
	{
		if($id) {
			$data = array('is_deleted' => 1);
			$this->db->where('id', $id);
			$delete = $this->db->update('product_prices', $data);
			return ($delete == true) ? true : false;
		}
		
	}

	public function createProductCategory($data)
	{
		if($data) {
			$insert = $this->db->insert('product_category', $data);
			$insert_id = $this->db->insert_id();
			return ($insert_id) ? $insert_id : false;
		}
	}

	public function checkProductCategory($product_id, $category_id)
	{
		if($product_id && $category_id) {
			$sql = "SELECT * FROM product_category WHERE product_id = ? AND category_id = ?";
			$query = $this->db->query($sql, array($product_id, $category_id));
			return $query->row_array();
		}	
	}
	public function ProductCategory($product_id, $category_id)
	{
		if($product_id) {
			$sql = "SELECT * FROM product_category WHERE product_id = ? AND category_id = ? AND is_deleted = 0";
			$query = $this->db->query($sql, array($product_id, $category_id));
			return $query->row_array();
		}	
	}
	public function ProductCategoryCount($product_id, $category_id)
	{
		if($product_id) {
			$sql = "SELECT * FROM product_category WHERE product_id = ? AND category_id = ?";
			$query = $this->db->query($sql, array($product_id, $category_id));
			return $query->num_rows();
		}	
	}
	public function existProductCategory($product_id, $category_id)
	{
		if($product_id) {
			$sql = "SELECT * FROM product_category WHERE product_id = ? AND category_id = ? AND is_deleted = ?";
			$query = $this->db->query($sql, array($product_id, $category_id, 0));
			return $query->row_array();
		}	
	}
	public function removeProductCategory($product_id, $category_id)
	{
		if($product_id) {
			$array = array('product_id' => $product_id, 'category_id' => $category_id);
			$this->db->where($array);
			$delete = $this->db->delete('product_category');
			return ($delete == true) ? true : false;
		}
	}

	public function updateProductCategory($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('product_category', $data);
			return ($update == true) ? true : false;
		}
	}

	public function update($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('products', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('products');
			return ($delete == true) ? true : false;
		}
	}

	public function productExist($product_name)
	{
		if($product_name) {
			$sql = "SELECT * FROM products WHERE name = ?";
			$query = $this->db->query($sql, array($product_name));
			return $query->row_array();
		}
	}
	
	public function removeDamageProduct($id)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('damage_record');
			return ($delete == true) ? true : false;
		}
	}

	public function countTotalProducts()
	{
		$sql = "SELECT * FROM products";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	// damage products
	public function checkProduct($id)
	{
		if($id) {
			$sql = "SELECT * FROM products WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
	}
	public function addDamageProducts($data)
	{
		if($data) {
			$insert = $this->db->insert('damage_record', $data);
			return ($insert == true) ? true : false;
		}
	}
	public function getDamageProductData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM damage_record where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
	}
	public function addAdjustProductRate($data)
	{
		if($data) {
			$insert = $this->db->insert('price_rate', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function getUnitsData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM units where id = ? AND is_deleted = ?";
			$query = $this->db->query($sql, array($id, 0));
			return $query->row_array();
		}

		$sql = "SELECT * FROM units WHERE is_deleted = 0 ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();

	}

	public function getAllUnitsData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM units where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM units ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();

	}
	public function getUnitValuesData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM unit_values where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM unit_values ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();

	}
	public function fetchUnitValueData($unit_id)
	{
		if($unit_id) {
			$sql = "SELECT * FROM unit_values where unit_id = ? AND is_deleted = ?";
			$query = $this->db->query($sql, array($unit_id, 0));
			return $query->row_array();
		}
	}

	public function getPurchaseOrdersData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM purchase_orders where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM purchase_orders ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}


    public function getPurchaseOrdersWthData($from="",$to="",$selected_vendor="")
    {
        if($from != "" && $to != ""){
        $sql = "SELECT * from purchase_orders where  w_h_t != 0 and datetime_created between '$from' and '$to'" ; 

        }
        else if($selected_vendor != ""){
        $sql = "SELECT * from purchase_orders where  w_h_t != 0 and vendor_id=".$selected_vendor;       
        }else{
        $sql = "SELECT * FROM purchase_orders  WHERE  w_h_t != 0 ORDER BY id DESC";
        }    
        

        $query = $this->db->query($sql);
        return $query->result_array();
    }

	public function getVendorPurchaseOrdersData($supplier_id)
	{
		if($supplier_id) {
			$sql = "SELECT * FROM purchase_orders JOIN purchase_items ON purchase_orders.id = purchase_items.purchase_order_id GROUP BY purchase_orders.id HAVING purchase_items.vendor_id = ?";
			$query = $this->db->query($sql, array($supplier_id));
			return $query->result_array();
		}
	}

	public function vendorPurchaseOrders($supplier_id)
	{
		if($supplier_id) {
			$sql = "SELECT *, purchase_orders.id purchase_order_id FROM purchase_orders JOIN purchase_items ON purchase_orders.id = purchase_items.purchase_order_id GROUP BY purchase_orders.id HAVING purchase_items.vendor_id = ?";
			$query = $this->db->query($sql, array($supplier_id));
			return $query->result_array();
		}
	}

	public function getPurchaseItemsData($order_id = null)
	{
		if($order_id)
		{
			$sql = "SELECT * FROM purchase_items where purchase_order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->result_array();
		}
	}

	public function countOrderItem($order_id)
	{
		if($order_id) {
			$sql = "SELECT * FROM purchase_items WHERE purchase_order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->num_rows();
		}
	}

	public function removePurchaseOrder($order_id)
	{
		if($order_id) {
			$this->db->where('id', $order_id);
			$delete = $this->db->delete('purchase_orders');
			return ($delete == true) ? true : false;
		}
	}

	public function itemExistInStock($category_id, $product_id, $unit_id)
	{
		if($product_id) {
			$sql = "
			    SELECT * 
			    FROM items_stock 
			    WHERE category_id = ? 
			    AND product_id = ? 
			    AND unit_id = ?
            ";
			$query = $this->db->query($sql, array($category_id, $product_id, $unit_id));
			return $query->row_array();
		}
	}
	public function itemExistInOfficeStock($category_id, $product_id, $unit_id)
	{
		if($product_id) {
			$sql = "SELECT * FROM office_items_stock WHERE category_id = ? AND product_id = ? AND unit_id = ?";
			$query = $this->db->query($sql, array($category_id, $product_id, $unit_id));
			return $query->row_array();
		}
	}
	public function getStockData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM items_stock where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM items_stock ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();	
	}

	public function getStockWithUnitsData()
	{
		$sql = "SELECT COUNT(*), SUM(quantity) as sum_qty, unit_id, product_id, category_id,id FROM items_stock GROUP BY unit_id, product_id, category_id,id ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();	
	}

    public function getStockWithUnitsData2()
    {
        $sql = "SELECT COUNT(*), SUM(quantity) as sum_qty, unit_id, product_id, category_id,id FROM items_stock GROUP BY unit_id, product_id, category_id,id ORDER BY id DESC";
        $query = $this->db->query($sql);
        return $query->result_array();  
    }
	
	public function getFactoryStockByItemName()
	{
		$sql = "
            SELECT products.name item_name, units.id, units.unit_name unit_name, SUM(items_stock.quantity/unit_values.unit_value) qty 
            FROM items_stock
            INNER JOIN products ON products.id = items_stock.product_id
            INNER JOIN unit_values ON unit_values.unit_id=items_stock.unit_id
            INNER JOIN units ON units.id = unit_values.unit_id
            GROUP BY products.name, units.id, units.unit_name
		";
		$query = $this->db->query($sql);
		return $query->result_array();	
	}

	public function getSaleOrdersData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM sales_order where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM sales_order ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function countSaleOrderItem($order_id)
	{
		if($order_id) {
			$sql = "SELECT * FROM sale_order_items WHERE sale_order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->num_rows();
		}
	}
	public function getSaleItemsData($order_id = null)
	{
		if($order_id)
		{
			$sql = "SELECT * FROM sale_order_items where sale_order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->result_array();
		}
		$sql = "SELECT * FROM sale_order_items";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getCompanySaleOrdersData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM company_sales_order where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM company_sales_order ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function getCompanySaleItemsData($order_id)
	{
		if($order_id)
		{
			$sql = "SELECT * FROM company_sales_order_items where sale_order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->result_array();
		}
	}
	public function countCompanySaleOrderItem($order_id)
	{
		if($order_id) {
			$sql = "SELECT * FROM company_sales_order_items WHERE sale_order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->num_rows();
		}
	}
	public function getCustomerOrdersData($order_id)
	{
		if($order_id) {
			$sql = "SELECT * FROM customer_order WHERE sale_order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->row_array();
		}
	}

	// public function getDistictSoldItems()
	// {
	// 	$sql = "SELECT sale_order_items.product_id, sale_order_items.category_id FROM sale_order_items GROUP BY sale_order_items.product_id, sale_order_items.category_id";
	// 	$query = $this->db->query($sql);
	// 	return $query->result_array();
	// }
	// public function getSaleOrdersWithItems()
	// {
	// 	$sql = "SELECT *, sales_order.id as sale_order_id, sale_order_items.id as sale_order_item_id FROM sales_order JOIN sale_order_items ON sales_order.id = sale_order_items.sale_order_id";
	// 	$query = $this->db->query($sql);
	// 	return $query->result_array();
	// }

	// public function getDistictCompanySoldItems()
	// {
	// 	$sql = "SELECT company_sales_order_items.product_id, company_sales_order_items.category_id FROM company_sales_order_items GROUP BY company_sales_order_items.product_id, company_sales_order_items.category_id";
	// 	$query = $this->db->query($sql);
	// 	return $query->result_array();
	// }

	// public function getCompanySaleOrdersWithItems()
	// {
	// 	$sql = "SELECT *, company_sales_order.id as company_sale_order_id, company_sales_order_items.id as company_sale_order_item_id FROM company_sales_order JOIN company_sales_order_items ON company_sales_order.id = company_sales_order_items.sale_order_id JOIN customer_order ON company_sales_order.id = customer_order.sale_order_id";
	// 	$query = $this->db->query($sql);
	// 	return $query->result_array();
	// }

	// public function getDistictPurchasedItems()
	// {
	// 	$sql = "SELECT purchase_items.product_id, purchase_items.category_id FROM purchase_items GROUP BY purchase_items.product_id, purchase_items.category_id";
	// 	$query = $this->db->query($sql);
	// 	return $query->result_array();
	// }

	// public function getPurchaseOrdersWithItems()
	// {
	// 	$sql = "SELECT *, purchase_orders.id as purchase_order_id, purchase_items.id as purchase_item_id FROM purchase_orders JOIN purchase_items ON purchase_orders.id = purchase_items.purchase_order_id";
	// 	$query = $this->db->query($sql);
	// 	return $query->result_array();
	// }

	public function countTotalUnits()
	{
		$sql = "SELECT * FROM units";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	public function countItemsInStock()
	{
		$sql = "SELECT * FROM items_stock";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	public function countItemsInOfficeStock()
	{
		$sql = "SELECT * FROM office_items_stock";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
	public function countDailyOfficeStockTransfers()
	{
		date_default_timezone_set("Asia/Karachi");
		$date_time = date('Y-m-d');
		$sql = "SELECT * FROM office_stock_transfer WHERE date(date_time) = ?";
		$query = $this->db->query($sql, array($date_time));
		return $query->num_rows();
	}

	public function countPurchaseDailyOrder()
	{
		date_default_timezone_set("Asia/Karachi");
		$date_time = date('Y-m-d');
		$sql = "SELECT * FROM purchase_orders WHERE date(datetime_created) = ?";
		$query = $this->db->query($sql, array($date_time));
		return $query->num_rows();
	}
	public function countDailySaleOrders()
	{
		date_default_timezone_set("Asia/Karachi");
		$date_time = date('Y-m-d');
		$sql = "SELECT * FROM sales_order WHERE date(date_time) = ?";
		$query = $this->db->query($sql, array($date_time));
		return $query->num_rows();
	}
	public function countDailySalesAmount()
	{
		date_default_timezone_set("Asia/Karachi");
		$date_time = date('Y-m-d');
		$sql = "SELECT SUM(net_amount) as daily_sales_amount FROM sales_order WHERE date(date_time) = ?";
		$query = $this->db->query($sql, array($date_time));
		return $query->row_array();
	}
	public function countDailySaleOrdersEmp()
	{
		date_default_timezone_set("Asia/Karachi");
		$date_time = date('Y-m-d');
		$sql = "SELECT * FROM company_sales_order WHERE date(date_time) = ?";
		$query = $this->db->query($sql, array($date_time));
		return $query->num_rows();
	}

	public function countStockItemAmount()
	{
		$sql = "SELECT SUM(quantity) as stock_item_amount FROM items_stock";
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	public function vendorOrderExist($supplier_id)
	{
		$sql = "SELECT * FROM purchase_orders WHERE vendor_id = ?";
		$query = $this->db->query($sql, $supplier_id);
		return $query->num_rows();
	}

	public function fetchVendorOBPaymentData($order_id)
	{
		if($order_id){
			$sql = "SELECT * FROM supplier_ob_payments WHERE most_recent_order_id = ?";
			$query = $this->db->query($sql, $order_id);
			return $query->result_array();
		}
	}

	public function getSupplierData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM supplier WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
	}
}
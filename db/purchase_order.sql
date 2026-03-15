-- Purchase Order module tables

CREATE TABLE IF NOT EXISTS purchase_orders_custom (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vendor_id INT NOT NULL,
  po_number VARCHAR(50) NOT NULL,
  po_date DATE NOT NULL,
  contact_person VARCHAR(100) NULL,
  contact_no VARCHAR(50) NULL,
  terms_of_payment VARCHAR(150) NULL,
  delivery VARCHAR(150) NULL,
  remarks VARCHAR(255) NULL,
  total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  sales_tax_percent DECIMAL(6,2) NOT NULL DEFAULT 0,
  sales_tax_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  grand_total DECIMAL(15,2) NOT NULL DEFAULT 0,
  created_by INT NULL,
  created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS purchase_order_items_custom (
  id INT AUTO_INCREMENT PRIMARY KEY,
  purchase_order_id INT NOT NULL,
  part_name VARCHAR(200) NOT NULL,
  model VARCHAR(100) NULL,
  qty DECIMAL(15,2) NOT NULL DEFAULT 0,
  unit VARCHAR(50) NULL,
  rate DECIMAL(15,2) NOT NULL DEFAULT 0,
  amount DECIMAL(15,2) NOT NULL DEFAULT 0,
  remarks VARCHAR(255) NULL,
  INDEX (purchase_order_id)
);

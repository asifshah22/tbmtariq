<?php

class Dashboard extends CI_Controller 
{
	public function __construct()
	{
                parent::__construct();
                if(!$this->session->userdata('logged_in')){
                        redirect('User/index');		
                }
        }

	/* 
	* It only redirects to the manage category page
	* It passes the total product, total paid orders, total users, and total stores information
	into the frontend.
	*/

	public function index()
	{		
                $data['page_title'] = "Dashoard";
                // Different Type of Products we have added

                $data['count_items_in_office_stock'] = $this->Model_products->countItemsInOfficeStock();
                $data['daily_sales_amount'] = $this->Model_products->countDailySalesAmount()['daily_sales_amount'];
                
                $data['count_daily_office_stock_transfers'] = $this->Model_products->countDailyOfficeStockTransfers();
                $data['count_remaining_loan_amount'] = $this->Model_loan->countRemainingLoanAmount()['remaining_loan_amount'];

                $data['count_items_in_stock'] = $this->Model_products->countItemsInStock();
                $data['daily_purchase_orders'] = $this->Model_products->countPurchaseDailyOrder();
                $data['count_daily_sale_orders'] = $this->Model_products->countDailySaleOrders();
                $data['count_daily_sale_orders_emp'] = $this->Model_products->countDailySaleOrdersEmp();

                $data['count_products'] = $this->Model_products->countTotalProducts();
                $data['count_categories'] = $this->Model_category->countTotalCategories();
                
                $data['count_users'] = $this->Model_users->countTotalUsers();
                $data['count_permission_groups'] = $this->Model_users->countTotalPermissionGroups();
                
                $data['total_vendors'] = $this->Model_supplier->countTotalVendors();
                
                $data['total_trusted_customers'] = $this->Model_Customers->countTotalCustomers();
                $data['count_departments'] = $this->Model_Customers->countTotalDepartments();
                $data['scaling_units'] = $this->Model_products->countTotalUnits();
                
                
                $this->load->view('templates/header', $data);
                $this->load->view('templates/header_menu');
                $this->load->view('templates/side_menubar');
                $this->load->view('dashboard');
                $this->load->view('templates/footer');
        }

} 

<?php 

  $user_id = $this->session->userdata('id');
  $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
  $permissions = unserialize($group_data['permission']);

?>

<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">

      <li id="dashboardMainMenu">
        <a href="<?php echo base_url()?>index.php/Dashboard/index">
          <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>

      <!-- Manage User & Permissions Tree -->
      <?php 
        $mainUserNav = false;
        if(in_array('recordUser', $permissions)){
          $mainUserNav = true;
        }else if(in_array('recordGroup', $permissions)){
          $mainUserNav = true;
        }else if(in_array('recordUserGroup', $permissions)){
          $mainUserNav = true;
        }
      ?>
      <?php if($mainUserNav): ?>
        <li class="treeview" id="mainUserPermissionsNav">
          <a href="#">
            <i class="fa fa-lock"></i>
            <span>Users & Permissions</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(in_array('recordUser', $permissions)): ?>
              <li id="manageUserNav">
                <a title="Manage Users Page" href="<?php echo base_url()?>index.php/User/manage_users">
                  <i class="glyphicon glyphicon-user"></i> Manage Users
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('recordGroup', $permissions)): ?>
              <li id="manageGroupNav">
                <a title="Manage Permission Groups" href="<?php echo base_url()?>index.php/User/manage_groups">
                  <i class="fa fa-unlock-alt"></i> Manage Permissions
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('recordUserGroup', $permissions)): ?>
              <li id="userPermissionNav">
                <a title="Manage Users Permissions Page" href="<?php echo base_url()?>index.php/User/manage_user_permissions">
                  <i class="fa fa-unlock"></i> Users Permissions
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <!-- Customers Tree -->
      <?php 
        $mainCustomerNav = false;
        if(in_array('recordCustomer', $permissions)){
          $mainCustomerNav = true;
        }else if(in_array('recordDepartment', $permissions)){
          $mainCustomerNav = true;
        }
      ?>
      <?php if($mainCustomerNav): ?>
        <li class="treeview" id="mainCustomersNav">
          <a href="#">
            <i class="fa fa-user-circle"></i>
            <span>Customers</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(in_array('recordCustomer', $permissions)): ?>
              <li id="customersNav">
                <a title="Manage Customers page" href="<?php echo base_url()?>index.php/Customers/index">
                  <i class="fa fa-circle-o"></i> Customers
                </a>
              </li>
            <?php endif ?>
            <?php if(in_array('recordDepartment', $permissions)): ?>
              <li id="departmentsNav">
                <a title="Manage Customer Departments" href="<?php echo base_url()?>index.php/Department/index">
                  <i class="fa fa-circle-o"></i><span>Departments</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>


      <!-- Vendor Tree -->
      <?php 
        $mainVendorNav = false;
        if(in_array('recordVendor', $permissions)){
          $mainVendorNav = true;
        }else if(in_array('recordProductPrices', $permissions)){
          $mainVendorNav = true;
        }
      ?>
      <?php if($mainVendorNav): ?>
        <li class="treeview" id="mainVendorNav">
          <a href="#">
            <i class="fa fa-user-circle-o"></i>
            <span>Vendors</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(in_array('recordVendor', $permissions)): ?>
              <li id="vendorsNav">
                <a title="Manage Vendors Page" href="<?php echo base_url()?>index.php/Supplier/index">
                  <i class="fa fa-circle-o"></i><span>Manage Vendors</span>
                </a>
              </li>
            <?php endif ?>
            <?php if(in_array('recordVendorBalancePayments', $permissions)): ?>
              <li id="ManageSupllierOBPaymentsNav">
                <a title="Manage Supplier OB Payments" href="<?php echo base_url()?>index.php/Supplier/manage_supllier_ob_payments">
                  <i class="fa fa-circle-o"></i> <span>Payments</span>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('recordProductPrices', $permissions)): ?>
              <li id="productPricesNav">
                <a title="Manage Product Item Prices" href="<?php echo base_url()?>index.php/Product/product_prices">
                  <i class="fa fa-circle-o"></i> <span>Product Prices</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <?php if(in_array('viewVendorLedger', $permissions)): ?>
        <li class="treeview" id="mainVendorLedgerNav">
          <a href="#">
            <i class="fa fa-book"></i>
            <span>Vendor Ledger</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="vendorsLedgerNav">
              <a title="Stock List" href="<?php echo base_url()?>index.php/Vendor_ledger/index">
                <i class="fa fa-circle-o"></i> Vendors Ledger
              </a>
            </li>
          </ul>
        </li>
      <?php endif; ?>

      <?php if(in_array('recordPaymentMethod', $permissions)): ?>
        <li class="treeview" id="mainPaymentMethodNav">
          <a href="#">
            <i class="fa fa-credit-card"></i>
            <span>Payment Method</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li id="paymentMethodNav">
              <a title="Payment Method" href="<?php echo base_url()?>index.php/Payment_Method/index">
                <i class="fa fa-circle-o"></i> Payment Method
              </a>
            </li>
          </ul>
        </li>
      <?php endif; ?>
      

      <!-- Loan Tree -->
      <?php 
        $mainLoanNav = false;
        if(in_array('recordLoan', $permissions)){
          $mainLoanNav = true;
        }else if(in_array('viewLoanHistory', $permissions)){
          $mainLoanNav = true;
        }else if(in_array('recordRemainingLoanSummary', $permissions)){
          $mainLoanNav = true;
        }else if(in_array('recordLoanDeductionsSummary', $permissions)){
          $mainLoanNav = true;
        }
      ?>
      <?php if($mainLoanNav): ?>
        <li class="treeview" id="mainLoanNav">
          <a href="#">
            <i class="fa fa-money"></i>
            <span>Loan</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(in_array('recordLoan', $permissions)): ?>
              <li id="manageLoanNav">
                <a title="Manage Vendors Loan" href="<?php echo base_url()?>index.php/Loan/index">
                  <i class="fa fa-circle-o"></i> <span>Manage Loan</span>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('viewLoanHistory', $permissions)): ?>
              <li id="loanHistoryNav">
                <a title="Vendor Loan History" href="<?php echo base_url()?>index.php/Loan/loan_history">
                  <i class="fa fa-circle-o"></i> <span>Vendor Loan History</span>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('recordRemainingLoanSummary', $permissions)): ?>
              <li id="loanRemainingNav">
                <a title="Remaining Loan Summary Of Vendors" href="<?php echo base_url()?>index.php/Loan/loan_remaining_summary">
                  <i class="fa fa-circle-o"></i> <span>Remaining Loan</span>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('recordLoanDeductionsSummary', $permissions)): ?>
              <li id="loanDeductionsNav">
                <a title="List of Loan Deductions of Vendors" href="<?php echo base_url()?>index.php/Loan/loan_deductions">
                  <i class="fa fa-circle-o"></i> <span>Loan Deductions</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <!-- Product Tree -->
      <?php 
        $mainProductNav = false;
        if(in_array('recordProduct', $permissions)){
          $mainProductNav = true;
        }else if(in_array('recordCategory', $permissions)){
          $mainProductNav = true;
        }
      ?>
      <?php if($mainProductNav): ?>
        <li class="treeview" id="mainProductNav">
          <a href="#">
            <i class="fa fa-product-hunt"></i>
            <span>Products</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(in_array('recordProduct', $permissions)): ?>
              <li id="productsNav">
                <a title="Manage Product Items Page" href="<?php echo base_url()?>index.php/Product/index">
                  <i class="fa fa-circle-o"></i> <span>Product Items</span>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('recordCategory', $permissions)): ?>
              <li id="categoryNav">
                <a title="Manage Items Categories Page" href="<?php echo base_url()?>index.php/Category/index">
                  <i class="fa fa-circle-o"></i> <span>Item Categories</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <!-- Purchasing Tree -->
      <?php 
        $mainPurchasingNav = false;
        if(in_array('createPurchasing', $permissions)){
          $mainPurchasingNav = true;
        }else if(in_array('recordPurchasing', $permissions)){
          $mainPurchasingNav = true;
        }else if(in_array('recordScalingUnits', $permissions)){
          $mainPurchasingNav = true;
        }
      ?>
      <?php if($mainPurchasingNav): ?>
        <li class="treeview" id="mainPurchasingNav">
          <a href="#">
            <i class="fa fa-cart-plus"></i>
            <span>Purchasing</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(in_array('createPurchasing', $permissions)): ?>
              <li id="purchasingNav">
                <a title="Purchasing Page To Buy From Vendors" href="<?php echo base_url()?>index.php/Product/purchasing">
                  <i class="fa fa-circle-o"></i> <span>Purchasing Page</span>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('recordPurchasing', $permissions)): ?>
              <li id="managePurchasingsNav">
                <a title="Manage Purchasings Page" href="<?php echo base_url()?>index.php/Product/manage_purchase_orders">
                  <i class="fa fa-circle-o"></i> <span>Manage Purchasing</span>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('recordScalingUnits', $permissions)): ?>
              <li id="manageUnitsNav">
                <a title="Scaling Units for Purchasing Product Items" href="<?php echo base_url()?>index.php/Product/manage_units">
                  <i class="fa fa-circle-o"></i> <span>Scaling Units</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <?php 
        $mainPurchasingNav = false;
        if(in_array('viewFactoryStock', $permissions)){
          $mainPurchasingNav = true;
        }else if(in_array('viewOfficeStock', $permissions)){
          $mainPurchasingNav = true;
        }else if(in_array('recordOfficeStockTransfer', $permissions)){
          $mainPurchasingNav = true;
        }else if(in_array('viewStockOrderLevel', $permissions)){
          $mainPurchasingNav = true;
        }
      ?>
      <?php if($mainPurchasingNav): ?>
        <!-- Stock Tree -->
        <li class="treeview" id="mainStockNav">
          <a href="#">
            <i class="fa fa-cube"></i>
            <span>Stock</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(in_array('viewFactoryStock', $permissions)): ?>
              <li id="manageStockNav">
                <a title="Factory Stock List" href="<?php echo base_url()?>index.php/Product/manage_stock">
                  <i class="fa fa-circle-o"></i> Factory Stock
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('viewFactoryStock', $permissions)): ?>
              <li id="manageStockNav">
                <a title="Factory Stock List" href="<?php echo base_url()?>index.php/Product/factory_stock_by_item">
                  <i class="fa fa-circle-o"></i> Factory Stock <small>(By Item)</small>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('viewOfficeStock', $permissions)): ?>
              <li id="viewOfficeStockNav">
                <a title="Office Stock List" href="<?php echo base_url()?>index.php/Product/view_office_stock">
                  <i class="fa fa-circle-o"></i> Office Stock
                </a>
              </li>
            <?php endif; ?> 
            <?php if(in_array('recordOfficeStockTransfer', $permissions)): ?> 
              <li id="manageOfficeStockNav">
                <a title="Manage Office Stock Transfer" href="<?php echo base_url()?>index.php/Product/manage_office_stock">
                  <i class="fa fa-circle-o"></i> Office Stock Transfer
                </a>
              </li>
            <?php endif; ?> 
            <?php if(in_array('viewStockOrderLevel', $permissions)): ?> 
              <li id="stockOrderLevelNav">
                <a title="Stock List" href="<?php echo base_url()?>index.php/Product/stock_order_level">
                  <i class="fa fa-circle-o"></i> Stock Order Level
                </a>
              </li>
            <?php endif; ?> 
          </ul>
        </li>
      <?php endif ?>


      <!-- Sales Tree -->
      <?php 
        $mainSaleNav = false;
        if(in_array('createSaleOrderNE', $permissions)){
          $mainSaleNav = true;
        }else if(in_array('recordSaleOrderNE', $permissions)){
          $mainSaleNav = true;
        }else if(in_array('recordSalePricesNE', $permissions)){
          $mainSaleNav = true;
        }else if(in_array('createSaleOrderE', $permissions)){
          $mainSaleNav = true;
        }else if(in_array('recordSaleOrderE', $permissions)){
          $mainSaleNav = true;
        }
      ?>
      <?php if($mainSaleNav): ?>
        <li class="treeview" id="mainSalesNav">
          <a href="#">
            <i class="fa fa-cart-arrow-down"></i>
            <span>Sales</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(in_array('createSaleOrderNE', $permissions)): ?>
              <li id="salesPageNav">
                <a title="Sales Page Of Non-Employees" href="<?php echo base_url()?>index.php/Product/create_sale_order">
                  <i class="fa fa-circle-o"></i> <span>Sales Page (Non-Emp)</span>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('recordSaleOrderNE', $permissions)): ?>
              <li id="manageSalesNav">
                <a title="Sales List Of Non-Employees" href="<?php echo base_url()?>index.php/Product/manage_sales">
                  <i class="fa fa-circle-o"></i> <span>Sales List (Non-Emp)</span>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('recordSalePricesNE', $permissions)): ?>
              <li id="manageSalePricesNav">
                <a title="Sale Prices Of Non-Employees" href="<?php echo base_url()?>index.php/Product/manage_sale_prices">
                  <i class="fa fa-circle-o"></i> <span>Sale Prices (Non-Emp)</span>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('createSaleOrderE', $permissions)): ?>
              <li id="salesPageTrustedCustomerNav">
                <a title="Sales Page Of Company Employees" href="<?php echo base_url()?>index.php/Product/create_company_sale_order">
                  <i class="fa fa-circle-o"></i> <span>Sales (Emp)</span>
                </a>
              </li>
            <?php endif; ?>
            <?php if(in_array('recordSaleOrderE', $permissions)): ?>
              <li id="manageCompanySalesNav">
                <a title="Sales List Of Company Employees" href="<?php echo base_url()?>index.php/Product/company_sales">
                  <i class="fa fa-circle-o"></i> <span>Sales List (Emp)</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
      <?php endif; ?>

      <!-- Reports tree -->
      <?php 
        $mainReportNav = false;
        if(in_array('viewVendorItemsRate', $permissions)){
          $mainReportNav = true;
        }else if(in_array('viewSaleItemsRate', $permissions)){
          $mainSaleNav = true;
        }else if(in_array('viewPurchasingDetails', $permissions)){
          $mainSaleNav = true;
        }else if(in_array('viewSaleDetailsNonEmp', $permissions)){
          $mainSaleNav = true;
        }else if(in_array('viewSaleDetailsEmp', $permissions)){
          $mainSaleNav = true;
        }else if(in_array('viewVendorRemainingBalance', $permissions)){
          $mainSaleNav = true;
        }
      ?>

      <?php if($mainReportNav): ?>
        <li class="treeview" id="mainReportsNav">
          <a href="#">
            <i class="fa fa-area-chart"></i>
            <span>Reports</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if(in_array('viewVendorItemsRate', $permissions)): ?>
              <li id="itemsRateNav">
                <a title="Items Rate of Vendors" href="<?php echo base_url()?>index.php/Reports/items_rate">
                  <i class="fa fa-circle-o"></i> <span>Vendor Items Rate</span>
                </a>
              </li>
            <?php endif; ?>

            <?php if(in_array('viewSaleItemsRate', $permissions)): ?>
              <li id="saleItemsRateNav">
                <a title="Sale Items Rate" href="<?php echo base_url()?>index.php/Reports/sale_items_rate">
                  <i class="fa fa-circle-o"></i> <span>Sale Items Rate</span>
                </a>
              </li>
            <?php endif; ?>

            <?php if(in_array('viewPurchasingDetails', $permissions)): ?>
              <li id="purchasedOrdersDetailNav">
                <a title="Purchased Orders Detail" href="<?php echo base_url()?>index.php/Reports/purchased_orders_details">
                  <i class="fa fa-circle-o"></i> <span>Purchasing Detail</span>
                </a>
              </li>
            <?php endif; ?>

            <?php if(in_array('viewSaleDetailsNonEmp', $permissions)): ?>  
              <li id="saleOrdersDetailNav">
                <a title="Sale Orders Detail of Non-Employees" href="<?php echo base_url()?>index.php/Reports/sale_orders_detail">
                  <i class="fa fa-circle-o"></i> <span>Sale Details (Non-Emp)</span>
                </a>
              </li>
            <?php endif; ?>

            <?php if(in_array('viewSaleDetailsEmp', $permissions)): ?>  
              <li id="companySaleOrdersDetailNav">
                <a title="Sale Orders Detail of Employees" href="<?php echo base_url()?>index.php/Reports/company_sale_orders_detail">
                  <i class="fa fa-circle-o"></i> <span>Sale Details (Emp)</span>
                </a>
              </li>
            <?php endif; ?>

            <?php if(in_array('viewSaleDetailsEmp', $permissions)): ?>  
              <li id="companySaleOrdersDetailNav">
                <a title="Factory Stock Detail of Employees" href="<?php echo base_url()?>index.php/Reports/factory_stock_details">
                  <i class="fa fa-circle-o"></i> <span>Factory Stock Details</span>
                </a>
              </li>
            <?php endif; ?>
            
            <?php if(in_array('viewVendorRemainingBalance', $permissions)): ?> 
              <li id="vendorsRemainingBalance">
                <a title="Vendors Remaining Balance" href="<?php echo base_url()?>index.php/Reports/vendors_remaining_balance">
                  <i class="fa fa-circle-o"></i> <span>Vendors Remaining Balance</span>
                </a>
              </li>
            <?php endif; ?>

               <?php if(in_array('viewVendorRemainingBalance', $permissions)): ?> 
              <li id="vendorsRemainingBalance">
                <a title="Vendors Remaining Balance" href="<?php echo base_url()?>index.php/Reports/wht">
                  <i class="fa fa-circle-o"></i> <span>W.H.T</span>
                </a>
              </li>
            <?php endif; ?>

          </ul>
        </li>      
      <?php endif; ?>

      <!-- System Tree -->
      
      <li class="treeview" id="mainSystemNav">
        <a href="#">
          <i class="fa fa-industry"></i>
          <span>System</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <?php if(in_array('updateCompany', $permissions)): ?>
            <li id="companyNav">
              <a title="Company Information" href="<?php echo base_url()?>index.php/Company/index">
                <i class="fa fa-building"></i> <span>Company</span>
              </a>
            </li>
          <?php endif; ?>
          <?php if(in_array('viewProfile', $permissions)): ?>
            <li id="profileNav">
              <a title="Your Profile" href="<?php echo base_url()?>index.php/User/user_profile">
                <i class="fa fa-user-o"></i> <span>Profile</span>
              </a>
            </li>
          <?php endif; ?>
          <?php if(in_array('updateSetting', $permissions)): ?>
            <li id="settingsNav">
              <a title="System Settings" href="<?php echo base_url()?>index.php/User/setting">
                <i class="fa fa-gear"></i> <span>Setting</span>
              </a>
            </li>
          <?php endif; ?>
          <li>
            <a title="Logout" href="<?php echo base_url()?>index.php/User/logout">
              <i class="glyphicon glyphicon-log-out"></i> <span>Logout</span>
            </a>
          </li>
        </ul>
      </li>


    </ul>
  </section>
  <!-- /.sidebar -->
</aside>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Manage
        <small>Permissions Groups</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Permissions Groups</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12 col-xs-12">
          
          <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo $this->session->flashdata('success'); ?>
            </div>
          <?php elseif($this->session->flashdata('error')): ?>
            <div class="alert alert-error alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo $this->session->flashdata('error'); ?>
            </div>
          <?php elseif(validation_errors()): ?>
            <div class="alert alert-error alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <?php echo validation_errors(); ?>
            </div>
          <?php endif; ?>

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Add Permission Group</h3>
            </div>
            <form role="form" action="create_group" method="post">
              <div class="box-body">
                <div class="form-group">
                  <label class="required-field" for="group_name">Permisions Group Name</label>
                  <input type="text" class="form-control" value="<?php if($group_name){echo $group_name;} ?>" id="group_name" name="group_name" placeholder="Enter group name">
                </div>
                <div class="form-group">
                  <label class="required-field" for="permission">Permissions</label>

                  <table class="table table-responsive">
                    <thead>
                      <tr>
                        <th>
                          <input type="checkbox" class="minimal" id="check_all"> <label for="check_all">Select All</label>
                        </th>
                        <th>Record</th>
                        <th>Create</th>
                        <th>Update</th>
                        <th>View</th>
                        <th>Delete</th>
                        <th>Print</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Users</td>
                        <td><input type="checkbox" name="permission[]" id="permission" <?php if(in_array('recordUser', $permissions)){ echo "checked"; } ?> value="recordUser" class="minimal"></td>
                        <td><input type="checkbox" name="permission[]" id="permission" <?php if(in_array('createUser', $permissions)){ echo "checked"; } ?> value="createUser" class="minimal"></td>
                        <td><input <?php if(in_array('updateUser', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateUser" class="minimal"></td>
                        <td><input <?php if(in_array('viewUser', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewUser" class="minimal"></td>
                        <td><input <?php if(in_array('deleteUser', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteUser" class="minimal"></td>
                        <td><input <?php if(in_array('printUser', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printUser" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Permissions Group</td>
                        <td><input <?php if(in_array('recordGroup', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordGroup" class="minimal"></td>
                        <td><input <?php if(in_array('createGroup', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createGroup" class="minimal"></td>
                        <td><input <?php if(in_array('updateGroup', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateGroup" class="minimal"></td>
                        <td><input <?php if(in_array('viewGroup', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewGroup" class="minimal"></td>
                        <td><input type="checkbox" <?php if(in_array('deleteGroup', $permissions)) { echo "checked"; } ?> name="permission[]" id="permission" value="deleteGroup" class="minimal"></td>
                        <td><input type="checkbox" <?php if(in_array('printGroup', $permissions)) { echo "checked"; } ?> name="permission[]" id="permission" value="printGroup" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Users Permissions</td>
                        <td><input <?php if(in_array('recordUserGroup', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordUserGroup" class="minimal"></td>
                        <td><input <?php if(in_array('createUserGroup', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createUserGroup" class="minimal"></td>
                        <td><input <?php if(in_array('updateUserGroup', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateUserGroup" class="minimal"></td>
                        <td><input <?php if(in_array('viewUserGroup', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewUserGroup" class="minimal"></td>
                        <td><input type="checkbox" <?php if(in_array('deleteUserGroup', $permissions)) { echo "checked"; } ?> name="permission[]" id="permission" value="deleteUserGroup" class="minimal"></td>
                        <td><input type="checkbox" <?php if(in_array('printUserGroup', $permissions)) { echo "checked"; } ?> name="permission[]" id="permission" value="printUserGroup" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Customers</td>
                        <td><input <?php if(in_array('recordCustomer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordCustomer" class="minimal"></td>
                        <td><input <?php if(in_array('createCustomer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createCustomer" class="minimal"></td>
                        <td><input <?php if(in_array('updateCustomer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateCustomer" class="minimal"></td>
                        <td><input <?php if(in_array('viewCustomer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewCustomer" class="minimal"></td>
                        <td><input <?php if(in_array('deleteCustomer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteCustomer" class="minimal"></td>
                        <td><input <?php if(in_array('printCustomer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printCustomer" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Department</td>
                        <td><input <?php if(in_array('recordDepartment', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordDepartment" class="minimal"></td>
                        <td><input <?php if(in_array('createDepartment', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createDepartment" class="minimal"></td>
                        <td><input <?php if(in_array('updateDepartment', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateDepartment" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('deleteDepartment', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteDepartment" class="minimal"></td>
                        <td><input <?php if(in_array('printDepartment', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printDepartment" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Vendors</td>
                        <td><input <?php if(in_array('recordVendor', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordVendor" class="minimal"></td>
                        <td><input <?php if(in_array('createVendor', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createVendor" class="minimal"></td>
                        <td><input <?php if(in_array('updateVendor', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateVendor" class="minimal"></td>
                        <td><input <?php if(in_array('viewVendor', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewVendor" class="minimal"></td>
                        <td><input <?php if(in_array('deleteVendor', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteVendor" class="minimal"></td>
                        <td><input <?php if(in_array('printVendor', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printVendor" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Vendor Balance Payments</td>
                        <td><input <?php if(in_array('recordVendorBalancePayments', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordVendorBalancePayments" class="minimal"></td>
                        <td><input <?php if(in_array('createVendorBalancePayments', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createVendorBalancePayments" class="minimal"></td>
                        <td><input <?php if(in_array('updateVendorBalancePayments', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateVendorBalancePayments" class="minimal"></td>
                        <td><input <?php if(in_array('viewVendorBalancePayments', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewVendorBalancePayments" class="minimal"></td>
                        <td><input <?php if(in_array('deleteVendorBalancePayments', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteVendorBalancePayments" class="minimal"></td>
                        <td><input <?php if(in_array('printVendorBalancePayments', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printVendorBalancePayments" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Product Prices</td>
                        <td><input <?php if(in_array('recordProductPrices', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordProductPrices" class="minimal"></td>
                        <td><input <?php if(in_array('createProductPrices', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createProductPrices" class="minimal"></td>
                        <td><input <?php if(in_array('updateProductPrices', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateProductPrices" class="minimal"></td>
                        <td><input <?php if(in_array('viewProductPrices', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewProductPrices" class="minimal"></td>
                        <td><input <?php if(in_array('deleteProductPrices', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteProductPrices" class="minimal"></td>
                        <td><input <?php if(in_array('printProductPrices', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printProductPrices" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Vendors Ledger</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewVendorLedger', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewVendorLedger" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printVendorLedger', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printVendorLedger" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Payment Method</td>
                        <td><input <?php if(in_array('recordPaymentMethod', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordPaymentMethod" class="minimal"></td>
                        <td><input <?php if(in_array('createPaymentMethod', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createPaymentMethod" class="minimal"></td>
                        <td><input <?php if(in_array('updatePaymentMethod', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updatePaymentMethod" class="minimal"></td>
                        <td><input <?php if(in_array('viewPaymentMethod', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewPaymentMethod" class="minimal"></td>
                        <td><input <?php if(in_array('deletePaymentMethod', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deletePaymentMethod" class="minimal"></td>
                        <td><input <?php if(in_array('printPaymentMethod', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printPaymentMethod" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Loan</td>
                        <td><input <?php if(in_array('recordLoan', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordLoan" class="minimal"></td>
                        <td><input <?php if(in_array('createLoan', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createLoan" class="minimal"></td>
                        <td><input <?php if(in_array('updateLoan', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateLoan" class="minimal"></td>
                        <td><input <?php if(in_array('viewLoan', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewLoan" class="minimal"></td>
                        <td><input <?php if(in_array('deleteLoan', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteLoan" class="minimal"></td>
                        <td><input <?php if(in_array('printLoan', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printLoan" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Loan History</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewLoanHistory', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewLoanHistory" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printLoanHistory', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printLoanHistory" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Remaining Loan Summary</td>
                        <td><input <?php if(in_array('recordRemainingLoanSummary', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordRemainingLoanSummary" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('printRemainingLoanSummary', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printRemainingLoanSummary" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Loan Deductions Summary</td>
                        <td><input <?php if(in_array('recordLoanDeductionsSummary', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordLoanDeductionsSummary" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('printLoanDeductionsSummary', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printLoanDeductionsSummary" class="minimal"></td>
                      </tr>
                      
                      <tr>
                        <td>Category</td>
                        <td><input <?php if(in_array('recordCategory', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordCategory" class="minimal"></td>
                        <td><input <?php if(in_array('createCategory', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createCategory" class="minimal"></td>
                        <td><input <?php if(in_array('updateCategory', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateCategory" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('deleteCategory', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteCategory" class="minimal"></td>
                        <td><input <?php if(in_array('printCategory', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printCategory" class="minimal"></td>
                      </tr>
                      
                      <tr>
                        <td>Product Items</td>
                        <td><input <?php if(in_array('recordProduct', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordProduct" class="minimal"></td>
                        <td><input <?php if(in_array('createProduct', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createProduct" class="minimal"></td>
                        <td><input <?php if(in_array('updateProduct', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateProduct" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('deleteProduct', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteProduct" class="minimal"></td>
                        <td><input <?php if(in_array('printProduct', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printProduct" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Purchasing</td>
                        <td><input <?php if(in_array('recordPurchasing', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordPurchasing" class="minimal"></td>
                        <td><input <?php if(in_array('createPurchasing', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createPurchasing" class="minimal"></td>
                        <td><input <?php if(in_array('updatePurchasing', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updatePurchasing" class="minimal"></td>
                        <td><input <?php if(in_array('viewPurchasing', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewPurchasing" class="minimal"></td>
                        <td><input <?php if(in_array('deletePurchasing', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deletePurchasing" class="minimal"></td>
                        <td><input <?php if(in_array('printPurchasing', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printPurchasing" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Purchase Return</td>
                        <td> - </td>
                        <td><input <?php if(in_array('createPurchaseReturn', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createPurchaseReturn" class="minimal"></td>
                        <td><input <?php if(in_array('updatePurchaseReturn', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updatePurchaseReturn" class="minimal"></td>
                        <td><input <?php if(in_array('viewPurchaseReturn', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewPurchaseReturn" class="minimal"></td>
                        <td><input <?php if(in_array('deletePurchaseReturn', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deletePurchaseReturn" class="minimal"></td>
                        <td><input <?php if(in_array('printPurchaseReturn', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printPurchaseReturn" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Scaling Units</td>
                        <td><input <?php if(in_array('recordScalingUnits', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordScalingUnits" class="minimal"></td>
                        <td><input <?php if(in_array('createScalingUnits', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createScalingUnits" class="minimal"></td>
                        <td><input <?php if(in_array('updateScalingUnits', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateScalingUnits" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('deleteScalingUnits', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteScalingUnits" class="minimal"></td>
                        <td><input <?php if(in_array('printScalingUnits', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printScalingUnits" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Factory Stock</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewFactoryStock', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewFactoryStock" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Office Stock</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewOfficeStock', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewOfficeStock" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                      
                      <tr>
                        <td>Office Stock Transfer</td>
                        <td><input <?php if(in_array('recordOfficeStockTransfer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordOfficeStockTransfer" class="minimal"></td>
                        <td><input <?php if(in_array('createOfficeStockTransfer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createOfficeStockTransfer" class="minimal"></td>
                        <td><input <?php if(in_array('updateOfficeStockTransfer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateOfficeStockTransfer" class="minimal"></td>
                        <td><input <?php if(in_array('viewOfficeStockTransfer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewOfficeStockTransfer" class="minimal"></td>
                        <td><input <?php if(in_array('deleteOfficeStockTransfer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteOfficeStockTransfer" class="minimal"></td>
                        <td><input <?php if(in_array('printOfficeStockTransfer', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printOfficeStockTransfer" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Stock Order Level</td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('updateStockOrderLevel', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateStockOrderLevel" class="minimal"></td>
                        <td><input <?php if(in_array('viewStockOrderLevel', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewStockOrderLevel" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Sale Orders (Non-Employees)</td>
                        <td><input <?php if(in_array('recordSaleOrderNE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordSaleOrderNE" class="minimal"></td>
                        <td><input <?php if(in_array('createSaleOrderNE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createSaleOrderNE" class="minimal"></td>
                        <td><input <?php if(in_array('updateSaleOrderNE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateSaleOrderNE" class="minimal"></td>
                        <td><input <?php if(in_array('viewSaleOrderNE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewSaleOrderNE" class="minimal"></td>
                        <td><input <?php if(in_array('deleteSaleOrderNE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteSaleOrderNE" class="minimal"></td>
                        <td><input <?php if(in_array('printSaleOrderNE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSaleOrderNE" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Sale Prices (Non-Employees)</td>
                        <td><input <?php if(in_array('recordSalePricesNE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordSalePricesNE" class="minimal"></td>
                        <td><input <?php if(in_array('createSalePricesNE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createSalePricesNE" class="minimal"></td>
                        <td><input <?php if(in_array('updateSalePricesNE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateSalePricesNE" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('deleteSalePricesNE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteSalePricesNE" class="minimal"></td>
                        <td><input <?php if(in_array('printSalePricesNE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSalePricesNE" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Sale Orders (Employees)</td>
                        <td><input <?php if(in_array('recordSaleOrderE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordSaleOrderE" class="minimal"></td>
                        <td><input <?php if(in_array('createSaleOrderE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createSaleOrderE" class="minimal"></td>
                        <td><input <?php if(in_array('updateSaleOrderE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateSaleOrderE" class="minimal"></td>
                        <td><input <?php if(in_array('viewSaleOrderE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewSaleOrderE" class="minimal"></td>
                        <td><input <?php if(in_array('deleteSaleOrderE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteSaleOrderE" class="minimal"></td>
                        <td><input <?php if(in_array('printSaleOrderE', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSaleOrderE" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Vendor Items Rate</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewVendorItemsRate', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewVendorItemsRate" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printVendorItemsRate', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printVendorItemsRate" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Sale Items Rate</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewSaleItemsRate', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewSaleItemsRate" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printSaleItemsRate', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSaleItemsRate" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Purchasing Details</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewPurchasingDetails', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewPurchasingDetails" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printPurchasingDetails', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printPurchasingDetails" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Sale Details (Non-Employee)</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewSaleDetailsNonEmp', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewSaleDetailsNonEmp" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printSaleDetailsNonEmp', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSaleDetailsNonEmp" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Sale Details (Emp)</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewSaleDetailsEmp', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewSaleDetailsEmp" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printSaleDetailsEmp', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSaleDetailsEmp" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Vendor Remaining Balance</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewVendorRemainingBalance', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewVendorRemainingBalance" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printVendorRemainingBalance', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printVendorRemainingBalance" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Show Product Rate</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewProductRate', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewProductRate" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>

                      <tr>
                        <td>Company</td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('updateCompany', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateCompany" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Profile</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewProfile', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewProfile" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Setting</td>
                        <td>-</td>
                        <td>-</td>
                        <td><input <?php if(in_array('updateSetting', $permissions)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateSetting" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                    </tbody>
                  </table>
                  
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?php echo base_url() ?>index.php/User/manage_groups" class="btn btn-warning">Back</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
        <!-- col-md-12 -->
      </div>
      <!-- /.row -->
      

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


<script type="text/javascript">
  $(document).ready(function() {

    $( ".required-field" ).append('<label style="color:red" for="name">*</label>');
    $("#mainUserPermissionsNav").addClass('active');

    $('input[type="checkbox"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    });
    var checkboxes = $('input.minimal');
    $('#check_all').on('ifChecked ifUnchecked', function(event) {        
      if (event.type == 'ifChecked') {
        checkboxes.iCheck('check');
      } else {
        checkboxes.iCheck('uncheck');
      }
    });
  });
</script>


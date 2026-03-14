
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        User
        <small>View Permission</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">View Permission</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12 col-xs-12">

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">View Permission</h3>

            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="form-group">
                <label for="group_name">Permission Group Name: </label>
                <input class="form-control" readonly type="text" name="group_name" value="<?php echo $groups_data['group_name']; ?>">
              </div>
              <div class="form-group">
                <label for="permission">Permissions</label>
                <?php $serialize_permission = unserialize($groups_data['permission']); ?>
                <table class="table table-responsive">
                  <thead>
                    <tr>
                      <th></th>
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
                        <td><input type="checkbox" name="permission[]" id="permission" <?php if(in_array('recordUser', $serialize_permission)){ echo "checked"; } ?> value="recordUser" class="minimal"></td>
                        <td><input type="checkbox" name="permission[]" id="permission" <?php if(in_array('createUser', $serialize_permission)){ echo "checked"; } ?> value="createUser" class="minimal"></td>
                        <td><input <?php if(in_array('updateUser', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateUser" class="minimal"></td>
                        <td><input <?php if(in_array('viewUser', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewUser" class="minimal"></td>
                        <td><input <?php if(in_array('deleteUser', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteUser" class="minimal"></td>
                        <td><input <?php if(in_array('printUser', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printUser" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Permissions Group</td>
                        <td><input <?php if(in_array('recordGroup', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordGroup" class="minimal"></td>
                        <td><input <?php if(in_array('createGroup', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createGroup" class="minimal"></td>
                        <td><input <?php if(in_array('updateGroup', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateGroup" class="minimal"></td>
                        <td><input <?php if(in_array('viewGroup', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewGroup" class="minimal"></td>
                        <td><input type="checkbox" <?php if(in_array('deleteGroup', $serialize_permission)) { echo "checked"; } ?> name="permission[]" id="permission" value="deleteGroup" class="minimal"></td>
                        <td><input type="checkbox" <?php if(in_array('printGroup', $serialize_permission)) { echo "checked"; } ?> name="permission[]" id="permission" value="printGroup" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Users Permissions</td>
                        <td><input <?php if(in_array('recordUserGroup', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordUserGroup" class="minimal"></td>
                        <td><input <?php if(in_array('createUserGroup', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createUserGroup" class="minimal"></td>
                        <td><input <?php if(in_array('updateUserGroup', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateUserGroup" class="minimal"></td>
                        <td><input <?php if(in_array('viewUserGroup', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewUserGroup" class="minimal"></td>
                        <td><input type="checkbox" <?php if(in_array('deleteUserGroup', $serialize_permission)) { echo "checked"; } ?> name="permission[]" id="permission" value="deleteUserGroup" class="minimal"></td>
                        <td><input type="checkbox" <?php if(in_array('printUserGroup', $serialize_permission)) { echo "checked"; } ?> name="permission[]" id="permission" value="printUserGroup" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Customers</td>
                        <td><input <?php if(in_array('recordCustomer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordCustomer" class="minimal"></td>
                        <td><input <?php if(in_array('createCustomer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createCustomer" class="minimal"></td>
                        <td><input <?php if(in_array('updateCustomer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateCustomer" class="minimal"></td>
                        <td><input <?php if(in_array('viewCustomer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewCustomer" class="minimal"></td>
                        <td><input <?php if(in_array('deleteCustomer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteCustomer" class="minimal"></td>
                        <td><input <?php if(in_array('printCustomer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printCustomer" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Department</td>
                        <td><input <?php if(in_array('recordDepartment', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordDepartment" class="minimal"></td>
                        <td><input <?php if(in_array('createDepartment', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createDepartment" class="minimal"></td>
                        <td><input <?php if(in_array('updateDepartment', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateDepartment" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('deleteDepartment', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteDepartment" class="minimal"></td>
                        <td><input <?php if(in_array('printDepartment', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printDepartment" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Vendors</td>
                        <td><input <?php if(in_array('recordVendor', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordVendor" class="minimal"></td>
                        <td><input <?php if(in_array('createVendor', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createVendor" class="minimal"></td>
                        <td><input <?php if(in_array('updateVendor', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateVendor" class="minimal"></td>
                        <td><input <?php if(in_array('viewVendor', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewVendor" class="minimal"></td>
                        <td><input <?php if(in_array('deleteVendor', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteVendor" class="minimal"></td>
                        <td><input <?php if(in_array('printVendor', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printVendor" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Vendor Balance Payments</td>
                        <td><input <?php if(in_array('recordVendorBalancePayments', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordVendorBalancePayments" class="minimal"></td>
                        <td><input <?php if(in_array('createVendorBalancePayments', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createVendorBalancePayments" class="minimal"></td>
                        <td><input <?php if(in_array('updateVendorBalancePayments', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateVendorBalancePayments" class="minimal"></td>
                        <td><input <?php if(in_array('viewVendorBalancePayments', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewVendorBalancePayments" class="minimal"></td>
                        <td><input <?php if(in_array('deleteVendorBalancePayments', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteVendorBalancePayments" class="minimal"></td>
                        <td><input <?php if(in_array('printVendorBalancePayments', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printVendorBalancePayments" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Product Prices</td>
                        <td><input <?php if(in_array('recordProductPrices', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordProductPrices" class="minimal"></td>
                        <td><input <?php if(in_array('createProductPrices', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createProductPrices" class="minimal"></td>
                        <td><input <?php if(in_array('updateProductPrices', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateProductPrices" class="minimal"></td>
                        <td><input <?php if(in_array('viewProductPrices', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewProductPrices" class="minimal"></td>
                        <td><input <?php if(in_array('deleteProductPrices', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteProductPrices" class="minimal"></td>
                        <td><input <?php if(in_array('printProductPrices', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printProductPrices" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Vendors Ledger</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewVendorLedger', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewVendorLedger" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printVendorLedger', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printVendorLedger" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Payment Method</td>
                        <td><input <?php if(in_array('recordPaymentMethod', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordPaymentMethod" class="minimal"></td>
                        <td><input <?php if(in_array('createPaymentMethod', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createPaymentMethod" class="minimal"></td>
                        <td><input <?php if(in_array('updatePaymentMethod', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updatePaymentMethod" class="minimal"></td>
                        <td><input <?php if(in_array('viewPaymentMethod', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewPaymentMethod" class="minimal"></td>
                        <td><input <?php if(in_array('deletePaymentMethod', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deletePaymentMethod" class="minimal"></td>
                        <td><input <?php if(in_array('printPaymentMethod', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printPaymentMethod" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Loan</td>
                        <td><input <?php if(in_array('recordLoan', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordLoan" class="minimal"></td>
                        <td><input <?php if(in_array('createLoan', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createLoan" class="minimal"></td>
                        <td><input <?php if(in_array('updateLoan', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateLoan" class="minimal"></td>
                        <td><input <?php if(in_array('viewLoan', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewLoan" class="minimal"></td>
                        <td><input <?php if(in_array('deleteLoan', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteLoan" class="minimal"></td>
                        <td><input <?php if(in_array('printLoan', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printLoan" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Loan History</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewLoanHistory', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewLoanHistory" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printLoanHistory', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printLoanHistory" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Remaining Loan Summary</td>
                        <td><input <?php if(in_array('recordRemainingLoanSummary', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordRemainingLoanSummary" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('printRemainingLoanSummary', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printRemainingLoanSummary" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Loan Deductions Summary</td>
                        <td><input <?php if(in_array('recordLoanDeductionsSummary', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordLoanDeductionsSummary" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('printLoanDeductionsSummary', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printLoanDeductionsSummary" class="minimal"></td>
                      </tr>
                      
                      <tr>
                        <td>Category</td>
                        <td><input <?php if(in_array('recordCategory', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordCategory" class="minimal"></td>
                        <td><input <?php if(in_array('createCategory', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createCategory" class="minimal"></td>
                        <td><input <?php if(in_array('updateCategory', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateCategory" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('deleteCategory', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteCategory" class="minimal"></td>
                        <td><input <?php if(in_array('printCategory', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printCategory" class="minimal"></td>
                      </tr>
                      
                      <tr>
                        <td>Product Items</td>
                        <td><input <?php if(in_array('recordProduct', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordProduct" class="minimal"></td>
                        <td><input <?php if(in_array('createProduct', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createProduct" class="minimal"></td>
                        <td><input <?php if(in_array('updateProduct', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateProduct" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('deleteProduct', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteProduct" class="minimal"></td>
                        <td><input <?php if(in_array('printProduct', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printProduct" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Purchasing</td>
                        <td><input <?php if(in_array('recordPurchasing', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordPurchasing" class="minimal"></td>
                        <td><input <?php if(in_array('createPurchasing', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createPurchasing" class="minimal"></td>
                        <td><input <?php if(in_array('updatePurchasing', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updatePurchasing" class="minimal"></td>
                        <td><input <?php if(in_array('viewPurchasing', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewPurchasing" class="minimal"></td>
                        <td><input <?php if(in_array('deletePurchasing', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deletePurchasing" class="minimal"></td>
                        <td><input <?php if(in_array('printPurchasing', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printPurchasing" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Purchase Return</td>
                        <td> - </td>
                        <td><input <?php if(in_array('createPurchaseReturn', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createPurchaseReturn" class="minimal"></td>
                        <td><input <?php if(in_array('updatePurchaseReturn', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updatePurchaseReturn" class="minimal"></td>
                        <td><input <?php if(in_array('viewPurchaseReturn', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewPurchaseReturn" class="minimal"></td>
                        <td><input <?php if(in_array('deletePurchaseReturn', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deletePurchaseReturn" class="minimal"></td>
                        <td><input <?php if(in_array('printPurchaseReturn', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printPurchaseReturn" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Scaling Units</td>
                        <td><input <?php if(in_array('recordScalingUnits', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordScalingUnits" class="minimal"></td>
                        <td><input <?php if(in_array('createScalingUnits', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createScalingUnits" class="minimal"></td>
                        <td><input <?php if(in_array('updateScalingUnits', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateScalingUnits" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('deleteScalingUnits', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteScalingUnits" class="minimal"></td>
                        <td><input <?php if(in_array('printScalingUnits', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printScalingUnits" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Factory Stock</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewFactoryStock', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewFactoryStock" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Office Stock</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewOfficeStock', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewOfficeStock" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Office Stock Transfer</td>
                        <td><input <?php if(in_array('recordOfficeStockTransfer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordOfficeStockTransfer" class="minimal"></td>
                        <td><input <?php if(in_array('createOfficeStockTransfer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createOfficeStockTransfer" class="minimal"></td>
                        <td><input <?php if(in_array('updateOfficeStockTransfer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateOfficeStockTransfer" class="minimal"></td>
                        <td><input <?php if(in_array('viewOfficeStockTransfer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewOfficeStockTransfer" class="minimal"></td>
                        <td><input <?php if(in_array('deleteOfficeStockTransfer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteOfficeStockTransfer" class="minimal"></td>
                        <td><input <?php if(in_array('printOfficeStockTransfer', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printOfficeStockTransfer" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Stock Order Level</td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('updateStockOrderLevel', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateStockOrderLevel" class="minimal"></td>
                        <td><input <?php if(in_array('viewStockOrderLevel', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewStockOrderLevel" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Sale Orders (Non-Employees)</td>
                        <td><input <?php if(in_array('recordSaleOrderNE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordSaleOrderNE" class="minimal"></td>
                        <td><input <?php if(in_array('createSaleOrderNE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createSaleOrderNE" class="minimal"></td>
                        <td><input <?php if(in_array('updateSaleOrderNE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateSaleOrderNE" class="minimal"></td>
                        <td><input <?php if(in_array('viewSaleOrderNE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewSaleOrderNE" class="minimal"></td>
                        <td><input <?php if(in_array('deleteSaleOrderNE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteSaleOrderNE" class="minimal"></td>
                        <td><input <?php if(in_array('printSaleOrderNE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSaleOrderNE" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Sale Prices (Non-Employees)</td>
                        <td><input <?php if(in_array('recordSalePricesNE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordSalePricesNE" class="minimal"></td>
                        <td><input <?php if(in_array('createSalePricesNE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createSalePricesNE" class="minimal"></td>
                        <td><input <?php if(in_array('updateSalePricesNE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateSalePricesNE" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('deleteSalePricesNE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteSalePricesNE" class="minimal"></td>
                        <td><input <?php if(in_array('printSalePricesNE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSalePricesNE" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Sale Orders (Employees)</td>
                        <td><input <?php if(in_array('recordSaleOrderE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="recordSaleOrderE" class="minimal"></td>
                        <td><input <?php if(in_array('createSaleOrderE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="createSaleOrderE" class="minimal"></td>
                        <td><input <?php if(in_array('updateSaleOrderE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateSaleOrderE" class="minimal"></td>
                        <td><input <?php if(in_array('viewSaleOrderE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewSaleOrderE" class="minimal"></td>
                        <td><input <?php if(in_array('deleteSaleOrderE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="deleteSaleOrderE" class="minimal"></td>
                        <td><input <?php if(in_array('printSaleOrderE', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSaleOrderE" class="minimal"></td>
                      </tr>
                      <tr>
                        <td>Vendor Items Rate</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewVendorItemsRate', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewVendorItemsRate" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printVendorItemsRate', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printVendorItemsRate" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Sale Items Rate</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewSaleItemsRate', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewSaleItemsRate" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printSaleItemsRate', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSaleItemsRate" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Purchasing Details</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewPurchasingDetails', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewPurchasingDetails" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printPurchasingDetails', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printPurchasingDetails" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Sale Details (Non-Employee)</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewSaleDetailsNonEmp', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewSaleDetailsNonEmp" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printSaleDetailsNonEmp', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSaleDetailsNonEmp" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Sale Details (Emp)</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewSaleDetailsEmp', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewSaleDetailsEmp" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printSaleDetailsEmp', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printSaleDetailsEmp" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Vendor Remaining Balance</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewVendorRemainingBalance', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewVendorRemainingBalance" class="minimal"></td>
                        <td> - </td>
                        <td><input <?php if(in_array('printVendorRemainingBalance', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="printVendorRemainingBalance" class="minimal"></td>
                      </tr>

                      <tr>
                        <td>Show Product Rate</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewProductRate', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewProductRate" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>

                      <tr>
                        <td>Company</td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('updateCompany', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateCompany" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Profile</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td><input <?php if(in_array('viewProfile', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="viewProfile" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                      <tr>
                        <td>Setting</td>
                        <td>-</td>
                        <td>-</td>
                        <td><input <?php if(in_array('updateSetting', $serialize_permission)) { echo "checked"; } ?> type="checkbox" name="permission[]" id="permission" value="updateSetting" class="minimal"></td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                      </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.box-body -->
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
      $("#mainUserPermissionsNav").addClass('active');
      $('input[type="checkbox"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass   : 'iradio_minimal-blue'
      });
      var checkboxes = $('input.minimal');
      checkboxes.attr("disabled", "disabled");
    });
  </script>
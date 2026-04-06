<?php

class Product extends CI_Controller {



    var $permission = array();



    public function __construct()

    {

        parent::__construct();



        $group_data = array();



        if(!$this->session->userdata('logged_in')){

            redirect('User/index');

         }

         else {

            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $this->data['user_permission'] = unserialize($group_data['permission']);

            $this->permission = unserialize($group_data['permission']);

        }

        $this->load->model('Model_purchase_order');

    }



    public function index()

    {

        if(!in_array('recordProduct', $this->permission)) {



            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/error_no_permission');



        }

        else{

            $data['page_title'] = "Manage Products";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $data['category_data'] = $this->Model_category->getCategoryData();



            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/index', $data);



            $this->load->view('templates/footer');

        }



    }

    /*

    * It Fetches the products data from the product table

    * this function is called from the datatable ajax function

    */



    public function fetchProductData()

    {

        $result = array('data' => array());

        $data = $this->Model_products->getProductCategoryData();



        $counter = 1;

        foreach ($data as $key => $value) {



            $category_name = '';

            $category_id = 0;

            if($value['category_id'] == NULL)

            {

                $category_name = 'Nill';

            }

            else

            {

                $category_name = $value['category_name'];

                $category_id = $value['category_id'];

            }

            // button

            $buttons = '';

            $button_edit_photo = '';

            if(in_array('updateProduct', $this->permission))

            {

                $product_id = $value['product_id'];

                $buttons .= '<a title="Edit Product" onclick="editFunc('.$product_id.', '.$category_id.')" data-toggle="modal" href="#editModal"><i class="glyphicon glyphicon-pencil"></i></a>';

                $button_edit_photo = '<a href="#edit_photo" data-toggle="modal" class="pull-right" onclick="editPhoto('.$value['product_id'].')"><span class="fa fa-edit"></span></a>';

            }

            if(in_array('deleteProduct', $this->permission))

            {

                $product_id = $value['product_id'];

                $buttons .= ' <a title="Delete Product" onclick="removeFunc('.$product_id.', '.$category_id.')" data-toggle="modal" href="#removeModal"><i class="glyphicon glyphicon-trash"></i></a>';

            }



            $img = "";

            if($value['image'] == "<p>You did not select a file to upload.</p>")

            {

                $img = '<a target="_blank" href="'.base_url().'assets/images/default-product-img.png" title="default product image"><img src="'.base_url('/assets/images/default-product-img.png').'" alt="'.$value['name'].'" class="img-circle" width="50" height="50" /></a>';

            }

            else

            {

                $img = '<a target="_blank" href="'.base_url($value['image']).'" title="Vendor default image"><img src="'.base_url($value['image']).'" alt="'.$value['product_name'].'" class="img-circle" width="50" height="50" />';

            }



            $img .= $button_edit_photo;



            $result['data'][$key] = array(

                $counter++,

                $img,

                $value['date_time'],

                $category_name,

                $value['product_name'],

                $value['description'],

                $buttons

            );

        } // /foreach



        echo json_encode($result);

    }



    /*

    * If the validation is not valid, then it redirects to the create page.

    * If the validation for each input field is valid then it inserts the data into the database

    * and it stores the operation message into the session flashdata and display on the manage product page

    */



    public function create_product()

    {

        if(!in_array('createProduct', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/error_no_permission');

        }

        else

        {

            $response = array();

            $this->form_validation->set_rules('product_name', 'Product name', 'trim|required');



            if ($this->form_validation->run() == TRUE)

            {

                // if product name exist

                $productName = $this->input->post('product_name');

                $category_id = $this->input->post('select_category');

                if(empty($category_id))

                {

                    $productArray = $this->Model_products->getProductsByName($productName);

                    $count = 0;

                    if(!empty($productArray)){

                        $count = count($productArray);

                    }

                    if($count > 0){

                        foreach ($productArray as $key => $value) {

                            $productCategoryData = $this->Model_products->getProductCategory($product_id = $value['id'], $category_id = 0);

                            if(!empty($productCategoryData)){

                                // exist product with no category

                                $response['success'] = false;

                                $response['messages'] = 'This Item already exist. Try different combination';

                                echo json_encode($response);

                                return;

                            }

                        }

                        // product with no category does not exist

                        // now add one time

                        date_default_timezone_set("Asia/Karachi");

                        $data = array(

                            'name' => $productName,

                            'image' => "<p>You did not select a file to upload.</p>",

                            'description' => $this->input->post('description'),

                            'date_time' => date('Y-m-d'),

                            'user_id' => $this->session->userdata('id'),

                            'is_deleted' => 0

                        );

                        $create = $this->db->insert('products', $data);

                        $last_id = $this->db->insert_id();

                        if($create == true) {

                            $data = array(

                                'category_id' => 0,

                                'product_id' => $last_id

                            );

                            $this->Model_products->createProductCategory($data);

                            $response['success'] = true;

                            $response['messages'] = 'Succesfully created';

                        }

                        else {

                            $response['success'] = false;

                            $response['messages'] = 'Error in the database while creating the brand information';

                        }

                    }

                    else

                    {

                        // add new item with no category

                        date_default_timezone_set("Asia/Karachi");

                        $data = array(

                            'name' => $productName,

                            'image' => "<p>You did not select a file to upload.</p>",

                            'description' => $this->input->post('description'),

                            'date_time' => date('Y-m-d'),

                            'user_id' => $this->session->userdata('id'),

                            'is_deleted' => 0

                        );

                        $create = $this->db->insert('products', $data);

                        $last_id = $this->db->insert_id();

                        if($create == true) {

                            $data = array(

                                'category_id' => 0,

                                'product_id' => $last_id

                            );

                            $this->Model_products->createProductCategory($data);

                            $response['success'] = true;

                            $response['messages'] = 'Succesfully created';

                        }

                        else {

                            $response['success'] = false;

                            $response['messages'] = 'Error in the database while creating the brand information';

                        }

                    }

                }

                else

                {

                    // category is not empty

                    $productArray = $this->Model_products->getProductsByName($productName);

                    $count = 0;

                    if(!empty($productArray)){

                        $count = count($productArray);

                        // $productCategoryData =

                    }

                    if($count > 0){

                        foreach ($productArray as $key => $value) {

                            $productCategoryData = $this->Model_products->getProductCategory($product_id = $value['id'], $category_id);

                            if(!empty($productCategoryData)){

                                // exist product with category

                                $response['success'] = false;

                                $response['messages'] = 'This Item already exist. Try different combination';

                                echo json_encode($response);

                                return;

                            }

                        }

                        // product with category does not exist

                        // now add one time

                        date_default_timezone_set("Asia/Karachi");

                        $data = array(

                            'name' => $productName,

                            'image' => "<p>You did not select a file to upload.</p>",

                            'description' => $this->input->post('description'),

                            'date_time' => date('Y-m-d'),

                            'user_id' => $this->session->userdata('id'),

                            'is_deleted' => 0

                        );

                        $create = $this->db->insert('products', $data);

                        $last_id = $this->db->insert_id();

                        if($create == true) {

                            $data = array(

                                'category_id' => $category_id,

                                'product_id' => $last_id

                            );

                            $this->Model_products->createProductCategory($data);

                            $response['success'] = true;

                            $response['messages'] = 'Succesfully created';

                        }

                        else {

                            $response['success'] = false;

                            $response['messages'] = 'Error in the database while creating the brand information';

                        }

                    }

                    else

                    {

                        // add new item with no category

                        date_default_timezone_set("Asia/Karachi");

                        $data = array(

                            'name' => $productName,

                            'image' => "<p>You did not select a file to upload.</p>",

                            'description' => $this->input->post('description'),

                            'date_time' => date('Y-m-d'),

                            'user_id' => $this->session->userdata('id'),

                            'is_deleted' => 0

                        );

                        $create = $this->db->insert('products', $data);

                        $last_id = $this->db->insert_id();

                        if($create == true) {

                            $data = array(

                                'category_id' => $category_id,

                                'product_id' => $last_id

                            );

                            $this->Model_products->createProductCategory($data);

                            $response['success'] = true;

                            $response['messages'] = 'Succesfully created';

                        }

                        else {

                            $response['success'] = false;

                            $response['messages'] = 'Error in the database while creating the brand information';

                        }

                    }

                }

            }

            else {

                // false case

                $response['success'] = false;

                foreach ($_POST as $key => $value) {

                    $response['messages'][$key] = form_error($key);

                }

            }

            echo json_encode($response);

        }

    }



    public function fetchProductCategoryDataById($product_id, $category_id)

    {

        if($product_id) {

            $data = $this->Model_products->getProductCategoryData($product_id, $category_id);

            echo json_encode($data);

        }

        return false;

    }



    public function get_vendor_row()

    {

        if(isset($_POST['id'])){

            $id = $_POST['id'];

            $output['data'] = $this->Model_products->getProductData($id);

            echo json_encode($output);

        }

    }



    public function edit_photo()

    {

        if(isset($_POST['upload'])){

            $id = $this->input->post('product_id');

            $filename = $_FILES['input_edit_photo']['name'];

            if(!empty($filename)){

                move_uploaded_file($_FILES['input_edit_photo']['tmp_name'], 'assets/images/product_image/'.$filename);

                $data = array(

                    'image' => 'assets/images/product_image/'.$_FILES['input_edit_photo']['name']

                );

                if($this->Model_products->update($data, $id)){

                    $this->session->set_flashdata('success', 'Photo updated successfully!');

                    return redirect('/Product/index');

                }

            }

        }

        else{

            $this->session->set_flashdata('error', 'File does not exist!');

            return redirect('/Product/index');

        }

    }



    /*

    * If the validation is not valid, then it redirects to the edit product page

    * If the validation is successfully then it updates the data into the database

    * and it stores the operation message into the session flashdata and display on the manage product page

    */



    public function update_product($product_category_id)

    {

        if(!in_array('updateProduct', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/error_no_permission');

        }

        else

        {

            $this->form_validation->set_rules('edit_product_name', 'Product name', 'trim|required');

            $p_product_id = explode('-', $product_category_id)[0];

            $p_category_id = explode('-', $product_category_id)[1];

            $productName = $this->input->post('edit_product_name');

            if ($this->form_validation->run() == TRUE)

            {

                // if product name exist

                $productName = $this->input->post('edit_product_name');

                $category_id = $this->input->post('edit_select_category');



                if(empty($category_id))

                {

                    $productArray = $this->Model_products->getProductsByName($productName);

                    // products withot current one which is being edited

                    $productArrayNew = array();

                    foreach ($productArray as $key => $value) {

                        if($value['id'] !== $p_product_id){

                            array_push($productArrayNew, $value);

                        }

                    }

                    $count = 0;

                    if(!empty($productArrayNew)){

                        $count = count($productArrayNew);

                    }

                    if($count > 0){

                        foreach ($productArrayNew as $key => $value) {

                            $productCategoryData = $this->Model_products->getProductCategory($product_id = $value['id'], $category_id = 0);

                            if(!empty($productCategoryData)){

                                // exist product with category

                                $response['success'] = false;

                                $response['messages'] = 'This Item already exist. Try different combination';

                                echo json_encode($response);

                                return;

                            }

                        }

                        // product with no category does not exist except which being edited currently

                        // now update

                        $data = array(

                            'name' => $productName,

                            'description' => $this->input->post('edit_description')

                        );

                        // if category exist delete it

                        $product_category_data = $this->Model_products->existProductCategory($p_product_id, $p_category_id);

                        if(!empty($product_category_data))

                        {

                            $this->Model_products->removeProductCategory($p_product_id, $p_category_id);

                        }

                        $update = $this->Model_products->update($data, $p_product_id);

                        if($update == true) {

                            $data = array(

                                'category_id' => 0,

                                'product_id' => $p_product_id

                            );

                            $this->Model_products->createProductCategory($data);

                            $response['success'] = true;

                            $response['messages'] = 'Succesfully updated';

                        }

                        else {

                            $response['success'] = false;

                            $response['messages'] = 'Error in the database while creating the brand information';

                        }

                    }

                    else

                    {

                        // product with name not exist add new row

                        $data = array(

                            'name' => $productName,

                            'description' => $this->input->post('edit_description')

                        );

                        // if category exist delete it

                        $product_category_data = $this->Model_products->existProductCategory($p_product_id, $p_category_id);

                        if(!empty($product_category_data))

                        {

                            $this->Model_products->removeProductCategory($p_product_id, $p_category_id);

                        }

                        $update = $this->Model_products->update($data, $p_product_id);

                        if($update == true) {

                            $data = array(

                                'category_id' => 0,

                                'product_id' => $p_product_id

                            );

                            $this->Model_products->createProductCategory($data);

                            $response['success'] = true;

                            $response['messages'] = 'Succesfully updated';

                        }

                        else {

                            $response['success'] = false;

                            $response['messages'] = 'Error in the database while creating the brand information';

                        }

                    }

                }

                else

                {

                    // category is not empty

                    $productArray = $this->Model_products->getProductsByName($productName);

                    // products withot current one which is being edited

                    $productArrayNew = array();

                    foreach ($productArray as $key => $value) {

                        if($value['id'] !== $p_product_id){

                            array_push($productArrayNew, $value);

                        }

                    }

                    $count = 0;

                    if(!empty($productArrayNew)){

                        $count = count($productArrayNew);

                    }

                    if($count > 0){

                        foreach ($productArrayNew as $key => $value) {

                            $productCategoryData = $this->Model_products->getProductCategory($product_id = $value['id'], $category_id);

                            if(!empty($productCategoryData)){

                                // exist product with category

                                $response['success'] = false;

                                $response['messages'] = 'This Item already exist. Try different combination';

                                echo json_encode($response);

                                return;

                            }

                        }

                        // product with category does not exist except which being edited currently

                        // now update

                        $data = array(

                            'name' => $productName,

                            'description' => $this->input->post('edit_description')

                        );

                        // if category exist delete it

                        $product_category_data = $this->Model_products->existProductCategory($p_product_id, $p_category_id);

                        if(!empty($product_category_data))

                        {

                            $this->Model_products->removeProductCategory($p_product_id, $p_category_id);

                        }

                        $update = $this->Model_products->update($data, $p_product_id);

                        if($update == true) {

                            $data = array(

                                'category_id' => $this->input->post('edit_select_category'),

                                'product_id' => $p_product_id

                            );

                            $this->Model_products->createProductCategory($data);

                            $response['success'] = true;

                            $response['messages'] = 'Succesfully updated';

                        }

                        else {

                            $response['success'] = false;

                            $response['messages'] = 'Error in the database while creating the brand information';

                        }

                    }

                    else

                    {

                        // product with name does not exist

                        $data = array(

                            'name' => $productName,

                            'description' => $this->input->post('edit_description')

                        );

                        // if category exist delete it

                        $product_category_data = $this->Model_products->existProductCategory($p_product_id, $p_category_id);

                        if(!empty($product_category_data))

                        {

                            $this->Model_products->removeProductCategory($p_product_id, $p_category_id);

                        }

                        $update = $this->Model_products->update($data, $p_product_id);

                        if($update == true) {

                            $data = array(

                                'category_id' => $this->input->post('edit_select_category'),

                                'product_id' => $p_product_id

                            );

                            $this->Model_products->createProductCategory($data);

                            $response['success'] = true;

                            $response['messages'] = 'Succesfully updated';

                        }

                        else {

                            $response['success'] = false;

                            $response['messages'] = 'Error in the database while creating the brand information';

                        }

                    }

                }//category exist

            }

            else

            {

                // false case

                $response['success'] = false;

                foreach ($_POST as $key => $value) {

                    $response['messages'][$key] = form_error($key);

                }

            }

            echo json_encode($response);

        }

    }



    public function remove_product()

    {

        $product_id = $this->input->post('product_id');

        $category_id = $this->input->post('category_id');

        $response = array();

        if($product_id)

        {

            $data = array('is_deleted' => 1);

            $delete = $this->Model_products->update($data, $product_id);

            $product_category_data = $this->Model_products->existProductCategory($product_id, $category_id);

            if(!empty($product_category_data))

            {

                $data = array('is_deleted' => 1);

                $delete = $this->Model_products->updateProductCategory($data, $product_category_data['id']);

            }

            if($delete == true) {

                $response['success'] = true;

                $response['messages'] = "Successfully removed";

            }

            else {

                $response['success'] = false;

                $response['messages'] = "Error in the database while removing the product information";

            }

        }

        else {

            $response['success'] = false;

            $response['messages'] = "Refersh the page again!!";

        }

        echo json_encode($response);

    }



    public function print_product_items()

    {

        if(!in_array('printProduct', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data = $this->Model_products->getProductCategoryData();

            $user_id = $this->session->userdata('id');

            $user_data = $this->Model_users->getUserData($user_id);

            date_default_timezone_set("Asia/Karachi");

            $print_date = date('d-m-Y');



            $html = '<!DOCTYPE html>

            <html lang="en">

            <head>

            <meta charset="UTF-8">

            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <meta http-equiv="X-UA-Compatible" content="ie=edge">

            <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

            <style>

            .invoice-title h2, .invoice-title h3 {

                display: inline-block;

            }



            .table > tbody > tr > .no-line {

                border-top: none;

            }



            .table > thead > tr > .no-line {

                border-bottom: none;

            }



            .table > tbody > tr > .thick-line {

                border-top: 2px solid;

            }

            </style>



            <title>TBM - Product Items Print</title>

            </head>

            <body onload="window.print();">

            <div class="container">

            <div class="row">

            <div class="col-xs-12">

            <div class="invoice-title text-center">

            <h3>TBM Automobile Private Ltd</h3>

            </div>

            <hr>

            <div class="row">

            <div class="col-xs-6">

            <address style="text-transform:capitalize">

            <strong>Printed By:</strong><br>

            '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

            </address>

            </div>

            <div class="col-xs-6 text-right">

            <address>

            <strong>Print Date:</strong><br>

            '.date("d-m-Y").'<br>



            </address>

            </div>

            </div>

            </div>

            </div>



            <div class="row">

            <div class="col-md-12">



            <div class="table-responsive">

            <table style="width: 100%;" class="table table-condensed table-bordered">

            <thead>

            <tr>

            <td style="width:5%"><strong>#</strong></td>

            <td style="width:15%"><strong>Picture</strong></td>

            <td style="width:15%"><strong>Date</strong></td>

            <td style="width:15%"><strong>Category</strong></td>

            <td style="width:15%"><strong>Item Name</strong></td>

            <td style="width:35%"><strong>Description</strong></td>

            </tr>

            </thead>

            <tbody>';

            $counter = 1;

            foreach ($data as $key => $value) {

                $category_name = '';



                if($value['category_id'] == NULL)

                {

                    $category_name = 'Nill';

                }

                else

                {

                    $category_name = $value['category_name'];

                }

                $img = "";

                if($value['image'] == "<p>You did not select a file to upload.</p>")

                {

                    $img = '<img src="'.base_url('/assets/images/default-product-img.png').'" alt="'.$value['name'].'" class="img-circle" width="50" height="50" />';

                }

                else

                {

                    $img = '<img src="'.base_url($value['image']).'" alt="'.$value['product_name'].'" class="img-circle" width="50" height="50" />';

                }

                $html .= '<tr>

                <td>'.$counter++.'</td>

                <td>'.$img.'</td>

                <td>'.$value['date_time'].'</td>

                <td>'.$category_name.'</td>

                <td>'.$value['product_name'].'</td>

                <td>'.$value['description'].'</td>



                </tr>';

            }

            $html .='



            </tbody>



            </table>

            </div>

            </div>

            </div>

            </div>



            </body>



            <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

            <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



            </html>';

            echo $html;

        }

    }



    public function product_prices()

    {

        if(!in_array('recordProductPrices', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['page_title'] = "Product Prices";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $data['product_category_data'] = $this->Model_products->getProductCategoryData();

            $data['vendor_data'] = $this->Model_supplier->getSupplierData();

            $data['units_data'] = $this->Model_products->getUnitsData();



            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/product_prices', $data);

            $this->load->view('templates/footer');

        }



    }



    public function fetchProductPricesData($selected_supplier = null)

    {

        $result = array('data' => array());

        if($selected_supplier != ""){

            $data = $this->Model_products->getVendorProductPricesData($selected_supplier);

        }

        else{

            $data = $this->Model_products->getGroupedProductPricesData();

        }



        $counter = 1;

        foreach ($data as $key => $value)

        {

            $category_name = '';

            if($value['category_id'])

            {

                $category_data = $this->Model_category->getAllCategoryData($value['category_id']);

                if(!empty($category_data)){

                    $category_name = $category_data['name'];

                }

            }

            else

            {

                $category_name = 'Nill';

            }

            $vendor_name = $value['first_name']. ' '. $value['last_name'];

            // button

            $buttons = '';

            if(in_array('viewProductPrices', $this->permission))

            {

                $buttons .= ' <a title="View Product Price" href="'.base_url("index.php/Product/view_product_price/".$value['product_prices_id']).'"><i class="glyphicon glyphicon-eye-open"></i></a>';

            }

            if(in_array('updateProductPrices', $this->permission))

            {

                $buttons .= ' <a title="Edit Product Price" onclick="editFunc('.$value['product_prices_id'].')" data-toggle="modal" href="#editModal"><i class="glyphicon glyphicon-pencil"></i></a>';

            }

            if(in_array('deleteProductPrices', $this->permission))

            {

                $buttons .= ' <a title="Delete Product Price" onclick="removeFunc('.$value['product_prices_id'].')" data-toggle="modal" href="#removeModal"><i class="glyphicon glyphicon-trash"></i></button>';

            }



            $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];

            $date = date('d-m-Y', strtotime($value['product_prices_datetime']));

            $time = date('h:i a', strtotime($value['product_prices_datetime']));



            $date_time = $date . ' ' . $time;



            $result['data'][$key] = array(

                $counter++,

                $vendor_name,

                $category_name,

                $value['product_name'],

                $unit_name,

                floatval($value['price']),

                $buttons

            );

        } // /foreach



        echo json_encode($result);

    }



    public function view_product_price($productPriceId)

    {

        if(!in_array('viewProductPrices', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else{

            $data['product_price_data'] = $this->Model_products->getProductPricesData($productPriceId);

            if(!empty($data['product_price_data'])){

                $data['page_title'] = "View Product Price";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $data['product_category_data'] = $this->Model_products->getProductCategoryData();

                $data['vendor_data'] = $this->Model_supplier->getSupplierData();

                $data['units_data'] = $this->Model_products->getUnitsData();

                $data['product_price_data'] = $this->Model_products->getProductPricesData($productPriceId);



                $user_id = $this->session->userdata('id');

                $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

                $data['user_permission'] = unserialize($group_data['permission']);



                $this->load->view('products/view_product_price', $data);

                $this->load->view('templates/footer');

            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }

    }



    public function create_product_new_price()

    {

        $response = array();

        $this->form_validation->set_rules('product_price', 'Product Pirce', 'trim|required');



        if ($this->form_validation->run() == TRUE)

        {

            // true case

            $product_id = explode("-",$this->input->post('select_product'))[0];

            $category_id = explode("-",$this->input->post('select_product'))[1];

            date_default_timezone_set("Asia/Karachi");

            $data = array(

                'category_id' => $category_id,

                'product_id' => $product_id,

                'vendor_id' => $this->input->post('select_vendor'),

                'unit_id' => $this->input->post('select_unit'),

                'price' => $this->input->post('product_price'),

                'date_time' => date('Y-m-d H:i:s a'),

                'is_deleted' => 0

            );

            $create = $this->Model_products->createProductPrice($data);

            if($create == true) {

                $response['success'] = true;

                $response['messages'] = 'Succesfully created';

            }

            else {

                $response['success'] = false;

                $response['messages'] = 'Error in the database while creating the brand information';

            }

        }

        else

        {

            // false case

            $response['success'] = false;

            foreach ($_POST as $key => $value) {

                $response['messages'][$key] = form_error($key);

            }

        }

        echo json_encode($response);

    }



    public function fetchProductPriceDataById($id)

    {

        if($id) {

            $data = $this->Model_products->getProductPricesData($id);

            echo json_encode($data);

        }

        return false;

    }



    public function update_product_price($id)

    {

        $response = array();



        if($id) {

            $this->form_validation->set_rules('edit_product_price', 'Product Pirce', 'trim|required');



            $this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');



            if ($this->form_validation->run() == TRUE) {

                date_default_timezone_set("Asia/Karachi");

                $data = array(

                    'product_id' => $this->input->post('edit_select_product'),

                    'vendor_id' => $this->input->post('edit_select_vendor'),

                    'unit_id' => $this->input->post('edit_select_unit'),

                    'price' => $this->input->post('edit_product_price'),

                    'date_time' =>date('Y-m-d H:i:s a')

                );



                $update = $this->Model_products->updateProductPrice($data, $id);

                if($update == true) {

                    $response['success'] = true;

                    $response['messages'] = 'Succesfully updated';

                }

                else {

                    $response['success'] = false;

                    $response['messages'] = 'Error in the database while updated the brand information';

                }

            }

            else {

                $response['success'] = false;

                foreach ($_POST as $key => $value) {

                    $response['messages'][$key] = form_error($key);

                }

            }

        }

        else {

            $response['success'] = false;

            $response['messages'] = 'Error please refresh the page again!!';

        }



        echo json_encode($response);

    }



    public function remove_product_price()

    {

        $product_price_id = $this->input->post('product_price_id');



        $response = array();

        if($product_price_id) {



            $product_price_data = $this->Model_products->getProductPricesData($product_price_id);



            $delete = $this->Model_products->removeProductPrice($product_price_id);

            if($delete == true) {



                $response['success'] = true;

                $response['messages'] = "Successfully removed";

            }

            else {

                $response['success'] = false;

                $response['messages'] = "Error in the database while removing the product information";

            }

        }

        else {

            $response['success'] = false;

            $response['messages'] = "Refersh the page again!!";

        }

        echo json_encode($response);

    }



    public function print_product_prices()

    {

        if(isset($_GET['selected_vendor']) && $_GET['selected_vendor'] != ""){

            $data = $this->Model_products->getVendorProductPricesData($_GET['selected_vendor']);

        }

        else{

            $data = $this->Model_products->getGroupedProductPricesData();

        }

        $user_id = $this->session->userdata('id');

        $user_data = $this->Model_users->getUserData($user_id);

        date_default_timezone_set("Asia/Karachi");

        $print_date = date('d-m-Y');



        $html = '<!DOCTYPE html>

        <html lang="en">

        <head>

        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

        <style>

        .invoice-title h2, .invoice-title h3 {

            display: inline-block;

        }





        </style>



        <title>TBM- Product Prices Print</title>

        </head>

        <body onload="window.print();">

        <div class="container">

        <div class="row">

        <div class="col-xs-12">

        <div class="invoice-title text-center">

        <h3>TBM Automobile Private Ltd</h3>

        </div>

        <hr>

        <div class="row">

        <div class="col-xs-6">

        <address style="text-transform:capitalize">

        <strong>Printed By:</strong><br>

        '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

        </address>

        </div>

        <div class="col-xs-6 text-right">

        <address>

        <strong>Print Date:</strong><br>

        '.date("d-m-Y").'<br>



        </address>

        </div>

        </div>

        </div>

        </div>



        <div class="row">

        <div class="col-md-12">



        <div class="table-responsive">

        <table style="width: 100%;" class="table table-condensed table-bordered">

        <thead>

        <tr>

        <td style="width:5%"><strong>#</strong></td>

        <td style="width:20%"><strong>DateTime</strong></td>

        <td style="width:15%"><strong>Vendor Name</strong></td>

        <td style="width:15%"><strong>Category</strong></td>

        <td style="width:15%"><strong>Item</strong></td>

        <td style="width:15%"><strong>Unit</strong></td>

        <td style="width:5%"><strong>Pirce</strong></td>

        </tr>

        </thead>

        <tbody>';

        $counter = 1;

        foreach ($data as $key => $value) {

            $category_name = '';

            if($value['category_id'])

            {

                $category_data = $this->Model_category->getAllCategoryData($value['category_id']);

                $category_name = '';

                if(!empty($category_data)){

                    $category_name = $category_data['name'];

                }

            }

            else

            {

                $category_name = 'Nill';

            }

            $vendor_name = $value['first_name']. ' '. $value['last_name'];

            $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];

            $html .= '<tr>

            <td>'.$counter++.'</td>

            <td>'.$value['product_prices_datetime'].'</td>

            <td>'.$vendor_name.'</td>

            <td>'.$category_name.'</td>

            <td>'.$value['product_name'].'</td>

            <td>'.$unit_name.'</td>

            <td>'.floatval($value['price']).'</td>



            </tr>';

        }

        $html .='



        </tbody>



        </table>

        </div>

        </div>

        </div>

        </div>



        </body>



        <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

        <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



        </html>';

        echo $html;

    }



    public function purchasing()

    {

        if(!in_array('createPurchasing', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['page_title'] = "Purchasing Page";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $data['product_category_data'] = $this->Model_products->getProductPricesData();

            $data['vendor_data'] = $this->Model_supplier->getSupplierData();

            $data['units_data'] = $this->Model_products->getUnitsData();

            $data['unit_data_values'] = $this->Model_products->getUnitValuesData();
            $data['po_orders'] = $this->Model_purchase_order->getOrdersForDropdown();



            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/purchasing', $data);

            $this->load->view('templates/footer');

        }

    }



    public function getTablesData()

    {

        $vendor_id = $this->input->post('vendor_id');

        if($vendor_id)

        {

            $data['vendor_data'] = $this->Model_supplier->getSupplierData($vendor_id);

            $data['vendor_products'] = $this->Model_products->getVendorProductsData($vendor_id);

            $response['data'] = $data;

            $response['success'] = true;

            echo json_encode($response);

        }

    }



    public function getVendorProductsById()

    {

        $vendor_id = $this->input->post('vendor_id');

        if($vendor_id) {

            $data['data'] = $this->Model_products->getVendorProductsData($vendor_id);

            $data['loan_data'] = $this->Model_loan->getVendorRemainingLoanData($vendor_id);

            echo json_encode($data);

        }

    }


    public function getPurchaseOrderItems()

    {

        if(!in_array('createPurchasing', $this->permission) && !in_array('updatePurchasing', $this->permission)) {
            echo json_encode(array('success' => false, 'message' => 'Access denied'));
            return;
        }

        $po_id = (int)$this->input->post('po_id');
        if(!$po_id) {
            echo json_encode(array('success' => false, 'message' => 'Missing PO ID'));
            return;
        }

        $order = $this->Model_purchase_order->getOrder($po_id);
        if(!$order) {
            echo json_encode(array('success' => false, 'message' => 'PO not found'));
            return;
        }

        $items = $this->Model_purchase_order->getOrderItems($po_id);
        $normalized = array();
        foreach ($items as $item) {
            $product_id = isset($item['product_id']) ? (int)$item['product_id'] : 0;
            $part_name = isset($item['part_name']) ? trim($item['part_name']) : '';
            if ($product_id <= 0 && $part_name !== '') {
                $product_id = (int)$this->Model_purchase_order->getProductIdByName($part_name);
            }

            $qty = isset($item['qty']) ? (float)$item['qty'] : 0.0;

            if ($qty <= 0) {
                continue;
            }

            $normalized[] = array(
                'product_id' => $product_id,
                'part_name' => $part_name,
                'qty' => $qty,
                'unit' => isset($item['unit']) ? $item['unit'] : '',
                'rate' => isset($item['rate']) ? (float)$item['rate'] : 0.0,
                'amount' => isset($item['amount']) ? (float)$item['amount'] : 0.0,
                'item_date' => isset($item['item_date']) ? $item['item_date'] : ''
            );
        }

        echo json_encode(array(
            'success' => true,
            'order' => array('vendor_id' => isset($order['vendor_id']) ? (int)$order['vendor_id'] : 0),
            'items' => $normalized
        ));

    }



    public function getProductPriceDataById()

    {

        $product_id = $this->input->post('product_id');

        $category_id = $this->input->post('category_id');

        if(empty($category_id)){

            $category_id = 0;

        }

        $vendor_id = $this->input->post('vendor_id');

        $unit_id = $this->input->post('unit_id');

        if($product_id)

        {

            $data['data'] = $this->Model_products->getProductPrices($category_id, $product_id, $vendor_id, $unit_id);

            $data['success'] = true;

            echo json_encode($data);

        }

        else

        {

            $data['success'] = false;

            echo json_encode($data);

        }

    }



    function confirm_vender()

    {

        $response = array();



        $this->form_validation->set_rules('select_vendor', 'Vendor name', 'trim|required');



        $this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');



        if ($this->form_validation->run() == TRUE) {

            $supplier_id = $this->input->post('select_vendor');

            $data['vendor_data'] = $this->Model_supplier->getSupplierData($supplier_id);



            $data['vendor_products'] = $this->Model_products->getVendorProductsData($supplier_id);

            $data['loan_data'] = $this->Model_loan->getVendorRemainingLoanData($supplier_id);

            $response['success'] = true;

            $response['data'] = $data;



            $response['messages'] = 'Succesfully loaded';

        }

        echo json_encode($response);

    }



    public function create_order()

    {

        $this->form_validation->set_rules('product[]', 'Product name', 'trim|required');

        $this->form_validation->set_rules('vender_is_selected', 'Vendor', 'trim|required');

        $this->form_validation->set_rules('qty[]', 'Quantity', 'trim|required');



        if ($this->form_validation->run() == TRUE) {

            // inputed payment info

            $selected_payment = $this->input->post('select_payment');

            $inputed_amount_paid = $this->input->post('amount_paid');

            $inputed_payment_date = $this->input->post('payment_date');

            $inputed_payment_note = $this->input->post('payment_note');



            // inputed order info

            date_default_timezone_set("Asia/Karachi");

            $datetime_created = date('Y-m-d H:i:s a');

            $user_id = $this->session->userdata('id');

            $bill_no = 'BILPR-'.strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));

            $gross_amount =  $this->input->post('gross_amount_value');

            $net_amount = $this->input->post('net_amount_value');

            $discount = $this->input->post('discount');

            // for tax

            $w_h_t = $this->input->post('w_h_t');

            $w_h_t_tax_value = $this->input->post('w_h_t_tax_value');
            $w_h_t_tax_value_total = $this->input->post('w_h_t_tax_value_total');



            $sales_tax = $this->input->post('sales_tax');

            $sales_tax_value = $this->input->post('sales_tax_value');
            $sales_tax_value_total = $this->input->post('sales_tax_value_total');



            $affair_loading = $this->input->post('affair_loading');

            $fine_deduction = $this->input->post('fine_deduction');

            $other_deduction = $this->input->post('other_deduction');

            $vendor_id = $this->input->post('vender_is_selected');

            $remarks = $this->input->post('remarks');

            $opening_balance = $this->input->post('opening_balance');

            // Order ki total payment jo hui ha

            $paid = '';

            $total_paid = 0;

            $temp = "!@#$";

            $payment_method = '';

            $payment_date = '';

            $payment_note = '';

            for($i = 0; $i < count($selected_payment); $i++)

            {

                $payment_method .= $selected_payment[$i];

                if(!empty($inputed_payment_note[$i])){

                    $payment_note .= $inputed_payment_note[$i];

                }

                else{

                    $payment_note .= 'Nill';

                }

                $payment_date .= $inputed_payment_date[$i];



                $paid .= $inputed_amount_paid[$i];

                if($i + 1 < count($selected_payment)){

                    $payment_method .= $temp;

                    $payment_date .= $temp;

                    $payment_note .= $temp;

                    $paid .= $temp;



                }

                $total_paid += $inputed_amount_paid[$i];

            }



            $data = array(

                'bill_no' => $bill_no,

                'vendor_id' => $vendor_id,

                'datetime_created' => $datetime_created,

                'datetime_modified' => $datetime_created,

                'gross_amount' => $gross_amount,

                'net_amount' => $net_amount,

                'w_h_t' => $w_h_t,

                'w_h_t_value' => $w_h_t_tax_value,

                'sales_tax' => $sales_tax,

                'sales_tax_value' => $sales_tax_value,
                'w_h_t_value_total' => $w_h_t_tax_value_total,
                'sales_tax_value_total' => $sales_tax_value_total,

                'discount' => $discount,

                'loading_or_affair' => $affair_loading,

                'fine_deduction' => $fine_deduction,

                'other_deduction' => $other_deduction,

                'opening_balance' => $opening_balance,

                'remarks' => $remarks,

                'paid' => $paid,

                'total_paid' => $total_paid,

                'payment_method' => $payment_method,

                'payment_date' => $payment_date,

                'payment_note' => $payment_note,

                'user_id' => $user_id

            );



            $insert = $this->db->insert('purchase_orders', $data);

            if($insert)

            {

                $purchase_order_id = $this->db->insert_id();

                $count_product = count($this->input->post('product'));
                $item_dates = $this->input->post('item_date');
                $has_item_date = $this->db->field_exists('item_date', 'purchase_items');

                for($x = 0; $x < $count_product; $x++)

                {

                    $product_id = explode("-",$this->input->post('product')[$x])[0];

                    $category_id = explode("-",$this->input->post('product')[$x])[1];

                    if(empty($category_id)){

                        $category_id = 0;

                    }

                    $unit_id = $this->input->post('select_unit')[$x];



                    $product_prices_data = $this->Model_products->getProductPrices($category_id, $product_id, $vendor_id, $unit_id);

                    $items = array(

                        'purchase_order_id' => $purchase_order_id,

                        'category_id' => $category_id,

                        'product_id' => $product_id,

                        'vendor_id' => $vendor_id,

                        'product_price' => $product_prices_data['price'],

                        'qty' => $this->input->post('qty')[$x],

                        'unit_id' => $this->input->post('select_unit')[$x]

                    );
                    if ($has_item_date) {
                        $item_date = isset($item_dates[$x]) && $item_dates[$x] ? $item_dates[$x] : date('Y-m-d');
                        $items['item_date'] = $item_date;
                    }



                    $insert = $this->db->insert('purchase_items', $items);

                    // if item already exist in stock

                    $item_exist = $this->Model_products->itemExistInStock($category_id, $product_id, $unit_id);

                    if($item_exist){

                        $quantity = $item_exist['quantity'];

                        $unit_value_data = $this->Model_products->fetchUnitValueData($unit_id);

                        $unit_value = $unit_value_data['unit_value'];

                        $quantity += ($this->input->post('qty')[$x] * $unit_value);



                        $stock = array(

                            'quantity' => $quantity

                        );

                        $this->db->where('id', $item_exist['id']);

                        $this->db->update('items_stock', $stock);

                    }

                    else

                    {

                        // else if item does not exist in stock

                        // as it does not exist in stock it means it could not have any return data also

                        $unit_value_data = $this->Model_products->fetchUnitValueData($unit_id);

                        $unit_value = $unit_value_data['unit_value'];



                        $quantity = ($this->input->post('qty')[$x] * $unit_value);

                        $stock = array(

                            'category_id' => $category_id,

                            'product_id' => $product_id,

                            'unit_id' => $unit_id,

                            'quantity' => $quantity

                        );

                        $this->db->insert('items_stock', $stock);

                    }



                }//end for items

                $selected_po_id = (int)$this->input->post('purchase_order_id');
                if($selected_po_id > 0) {
                    $payment_update = $this->Model_purchase_order->updatePaymentStatusFromPurchasing($selected_po_id, $purchase_order_id);
                    if($payment_update === null) {
                        log_message('error', 'Failed to update PO payment status. PO ID: ' . $selected_po_id . ', Purchase Order ID: ' . $purchase_order_id);
                    }

                    $supply_update = $this->Model_purchase_order->updateSupplyStatusFromPurchasing($selected_po_id, $purchase_order_id);
                    if($supply_update === null) {
                        log_message('error', 'Failed to update PO supply status. PO ID: ' . $selected_po_id . ', Purchase Order ID: ' . $purchase_order_id);
                    }
                }



                // update vendor balance

                $vendor_data = $this->Model_supplier->getSupplierData($vendor_id);

                $data = array(

                    'balance' => $vendor_data['balance'] + $net_amount - $total_paid

                );



                $this->db->where('id', $vendor_id);

                $this->db->update('supplier', $data);



                $loan_deduction = $this->input->post('loan_deduction');

                $loan_data = $this->Model_loan->getVendorRemainingLoanData($vendor_id);

                if($loan_deduction && (!empty($loan_data)))

                {

                    $loanDeduction = array(

                        'vendor_id' => $vendor_id,

                        'order_id' => $purchase_order_id,

                        'loan_id' => $loan_data['loan_id'],

                        'deduction_amount' => $loan_deduction,

                        'remaining_amount' => $loan_data['remaining_amount'] - $loan_deduction

                    );

                    $insert = $this->db->insert('loan_deduction', $loanDeduction);

                    if($insert)

                    {

                        $amount = $loan_data['amount'];

                        $paid_amount = $loan_data['paid_amount'] + $loan_deduction;

                        $remaining_amount = $loan_data['remaining_amount'] - $loan_deduction;

                        $paid_status = 0;



                        if($amount == $paid_amount)

                        {

                            $paid_status = 1;

                        }



                        $loan = array(

                            'paid_amount' => $paid_amount,

                            'remaining_amount' => $remaining_amount,

                            'paid_status' => $paid_status

                        );

                        $this->db->where('id', $loan_data['loan_id']);

                        $update = $this->db->update('loan', $loan);

                    }

                }

                $this->session->set_flashdata('success', 'Successfully created');

                redirect('/Product/view_order_items/'.$purchase_order_id, 'refresh');

            }

        }

        else

        {

            $this->session->set_flashdata('errors', 'Error occurred!!');

            redirect('/Product/purchasing/', 'refresh');

        }

    }





    public function update_purchase_order($order_id)

    {

        if(!in_array('updatePurchasing', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['purchase_order_data'] = $this->Model_products->getPurchaseOrdersData($order_id);

            $data['purchase_items_data'] = $this->Model_products->getPurchaseItemsData($order_id);

            if(!empty($data['purchase_order_data']) && !empty($data['purchase_items_data']))

            {
                $data['po_orders'] = $this->Model_purchase_order->getOrdersForDropdownAll();
                $data['selected_po_id'] = 0;
                $po_link = $this->Model_purchase_order->getPurchasingLinkFieldInfo();
                $po_link_value = $this->Model_purchase_order->getPurchasingLinkValueByPurchaseOrderId($order_id);
                if ($po_link && $po_link_value !== null) {
                    if ($po_link['field'] === 'purchase_order_custom_id' || $po_link['field'] === 'po_id') {
                        $data['selected_po_id'] = (int)$po_link_value;
                    } else {
                        $row = $this->db->query("SELECT id FROM purchase_orders_custom WHERE po_number = ? LIMIT 1", array($po_link_value))->row_array();
                        if ($row && isset($row['id'])) {
                            $data['selected_po_id'] = (int)$row['id'];
                        }
                    }
                }

                $this->form_validation->set_rules('product[]', 'Product name', 'trim|required');

                $this->form_validation->set_rules('qty[]', 'Quantity', 'trim|required');

                if($this->form_validation->run() == TRUE)

                {

                    // inputed payment info

                    $selected_payment = $this->input->post('select_payment');

                    $inputed_amount_paid = $this->input->post('amount_paid');

                    $inputed_payment_date = $this->input->post('payment_date');

                    $inputed_payment_note = $this->input->post('payment_note');



                    $purchase_order_data = $this->Model_products->getPurchaseOrdersData($order_id);


                    date_default_timezone_set("Asia/Karachi");

                    $datetime_modified = date('Y-m-d H:i:s a');

                    $user_id = $this->session->userdata('id');

                    $gross_amount =  $this->input->post('gross_amount_value');

                    $net_amount = $this->input->post('net_amount_value');

                    $discount = $this->input->post('discount');

                    $w_h_t = $this->input->post('w_h_t');
                    $w_h_t_tax_value = $this->input->post('w_h_t_tax_value');
                    $w_h_t_tax_value_total = $this->input->post('w_h_t_tax_value_total');
                    $sales_tax = $this->input->post('sales_tax');
                    $sales_tax_value = $this->input->post('sales_tax_value');
                    $sales_tax_value_total = $this->input->post('sales_tax_value_total');

                    $affair_loading = $this->input->post('affair_loading');

                    $fine_deduction = $this->input->post('fine_deduction');

                    $other_deduction = $this->input->post('other_deduction');

                    $remarks = $this->input->post('remarks');

                    $vendor_id = $this->input->post('vendor_id');

                    $selected_products = $this->input->post('product');

                    $opening_balance = $purchase_order_data['opening_balance'];

                    // $opening_balance = $this->input->post('opening_balance');



                    // Order ki total payment jo hui ha

                    $paid = '';

                    $total_paid = 0;

                    $temp = "!@#$";

                    $payment_method = '';

                    $payment_date = '';

                    $payment_note = '';

                    for($i = 0; $i < count($selected_payment); $i++)

                    {

                        if(!empty($selected_payment[$i]))

                        {

                            $payment_method .= $selected_payment[$i];

                            if(!empty($inputed_payment_note[$i])){

                                $payment_note .= $inputed_payment_note[$i];

                            }

                            else{

                                $payment_note .= 'Nill';

                            }

                            $payment_date .= $inputed_payment_date[$i];



                            $paid .= $inputed_amount_paid[$i];

                            if($i + 1 < count($selected_payment)){

                                $payment_method .= $temp;

                                $payment_date .= $temp;

                                $payment_note .= $temp;

                                $paid .= $temp;



                            }

                            $total_paid += $inputed_amount_paid[$i];

                        }

                    }

                    // end inputed payment data



                    $purchase_items_data = $this->Model_products->getPurchaseItemsData($order_id);

                    $purchase_return_data = $this->Model_products->fetchPurchaseReturnsData($order_id);

                    // if return is marked for this order and now adding less than retuned items amount

                    if(!empty($purchase_return_data)){

                        foreach ($purchase_items_data as $key => $value)

                        {

                            $count_product = count($selected_products);

                            for($x = 0; $x < $count_product; $x++)

                            {

                                $product_id = explode("-",$this->input->post('product')[$x])[0];

                                $category_id = explode("-",$this->input->post('product')[$x])[1];

                                $u_id = $this->input->post('select_unit')[$x];

                                if($value['product_id'] == $product_id && $value['category_id'] == $category_id && $value['unit_id'] == $u_id)

                                {

                                    // selected items are = purchased items

                                    foreach ($purchase_return_data as $k => $v)

                                    {

                                        if($v['product_id'] == $product_id && $v['category_id'] == $category_id && $v['unit_id'] == $u_id)

                                        {

                                            // slected items has returned items

                                            $order_retuns_qty = $v['qty']; //order returns qty has unit but it's qty is added without taking units into account



                                            $unit_data_values = $this->Model_products->getUnitValuesData();

                                            $unit_value = 1;

                                            foreach($unit_data_values as $k => $v){

                                                if($v['unit_id'] == $value['unit_id']){

                                                    $unit_value = $v['unit_value'];

                                                }

                                            }

                                            if(($this->input->post('qty')[$x] * $unit_value) < $order_retuns_qty)

                                            {

                                                // return -> entered product amount is not valid

                                                $this->session->set_flashdata('error', 'Order has returns marked and inputed quantity is less than the returned quantity for one of item. Check Marked Returns of this Order.');

                                                redirect('/Product/update_purchase_order/'.$order_id, 'refresh');

                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }



                    // update order

                    $purchase_return_amount = 0;

                    $purchase_return_qty = 0;

                    foreach ($purchase_return_data as $key => $value) {

                        $purchase_return_amount += $value['amount'];

                    }



                    $data = array(

                        'datetime_modified' => $datetime_modified,

                        'gross_amount' => $gross_amount + $purchase_return_amount,

                        'net_amount' => $net_amount + $purchase_return_amount,

                        'discount' => $discount,
                        'w_h_t' => $w_h_t,

                'w_h_t_value' => $w_h_t_tax_value,

                'sales_tax' => $sales_tax,

                'sales_tax_value' => $sales_tax_value,
                'w_h_t_value_total' => $w_h_t_tax_value_total,
                'sales_tax_value_total' => $sales_tax_value_total,

                        'loading_or_affair' => $affair_loading,

                        'fine_deduction' => $fine_deduction,

                        'other_deduction' => $other_deduction,

                        'remarks' => $remarks,

                        'opening_balance' => $opening_balance,

                        'paid' => $paid,

                        'total_paid' => $total_paid,

                        'payment_method' => $payment_method,

                        'payment_date' => $payment_date,

                        'payment_note' => $payment_note,

                        'user_id' => $user_id

                    );



                    $vendor_data = $this->Model_supplier->getSupplierData($vendor_id);

                    $balance = $vendor_data['balance'] - ($purchase_order_data['net_amount'] - $purchase_order_data['total_paid']);



                    $this->db->where('id', $order_id);

                    $update = $this->db->update('purchase_orders', $data);
                    $selected_po_id = (int)$this->input->post('purchase_order_id');
                    $po_link = $this->Model_purchase_order->getPurchasingLinkFieldInfo();
                    $po_link_value = $this->Model_purchase_order->getPurchasingLinkValueByPurchaseOrderId($order_id);
                    if ($po_link && $po_link_value !== null && $po_link['table'] === 'purchase_orders') {
                        $this->db->where('id', $order_id);
                        $this->db->update('purchase_orders', array($po_link['field'] => $po_link_value));
                    }
                    // remove the existing items

                    // subtract from stock also

                    if($update)

                    {

                        // delete items

                        foreach ($purchase_items_data as $key => $value)

                        {

                            foreach ($purchase_return_data as $k => $v)

                            {

                                if($v['product_id'] == $value['product_id'] && $v['category_id'] == $value['category_id'] && $v['unit_id'] == $value['unit_id'])

                                {

                                    $purchase_return_qty = $v['qty'];

                                }

                            }



                            $stock = $this->Model_products->itemExistInStock($value['category_id'], $value['product_id'], $value['unit_id']);



                            $unit_data_values = $this->Model_products->getUnitValuesData();

                            $unit_value = 1;

                            foreach($unit_data_values as $k => $v){

                                if($v['unit_id'] == $value['unit_id']){

                                    $unit_value = $v['unit_value'];

                                }

                            }

                            $stock_data = array(

                                'quantity' => $stock['quantity'] + $purchase_return_qty - ($value['qty'] * $unit_value)

                            );

                            // update stock

                            $this->db->where('id', $stock['id']);

                            $this->db->update('items_stock', $stock_data);



                            // delete items

                            $this->db->where('id', $value['id']);

                            $this->db->delete('purchase_items');

                        }

                        // insert new items

                        $count_product = count($selected_products);
                        $item_dates = $this->input->post('item_date');
                        $has_item_date = $this->db->field_exists('item_date', 'purchase_items');

                        for($x = 0; $x < $count_product; $x++)

                        {

                            $product_id = explode("-",$this->input->post('product')[$x])[0];

                            $category_id = explode("-",$this->input->post('product')[$x])[1];

                            if(empty($category_id)){

                                $category_id = 0;

                            }

                            $unit_id = $this->input->post('select_unit')[$x];

                            $product_price = 0;

                            // have previous price id or not

                            if(isset($this->input->post('rate_value')[$x]))

                            {

                                $product_price = $this->input->post('rate_value')[$x];

                            }

                            else

                            {

                                $product_prices_data = $this->Model_products->getProductPrices($category_id, $product_id, $vendor_id, $unit_id);

                                $product_price = $product_prices_data['price'];

                            }



                            $items = array(

                                'purchase_order_id' => $order_id,

                                'category_id' => $category_id,

                                'product_id' => $product_id,

                                'vendor_id' => $vendor_id,

                                'product_price' => $product_price,

                                'qty' => $this->input->post('qty')[$x],

                                'unit_id' => $this->input->post('select_unit')[$x]

                            );
                            if ($has_item_date) {
                                $item_date = isset($item_dates[$x]) && $item_dates[$x] ? $item_dates[$x] : date('Y-m-d');
                                $items['item_date'] = $item_date;
                            }

                            if ($po_link && $po_link_value !== null && $po_link['table'] === 'purchase_items') {
                                $items[$po_link['field']] = $po_link_value;
                            }

                            $insert = $this->db->insert('purchase_items', $items);

                            // if item already exist in stock

                            $item_exist = '';

                            if($category_id)

                            {

                                $item_exist = $this->Model_products->itemExistInStock($category_id, $product_id, $this->input->post('select_unit')[$x]);

                            }

                            else

                            {

                                $category_id = 0;

                                $item_exist = $this->Model_products->itemExistInStock($category_id, $product_id, $this->input->post('select_unit')[$x]);

                            }



                            // check for returns

                            foreach ($purchase_return_data as $k => $v)

                            {

                                if($v['product_id'] == $product_id && $v['category_id'] == $category_id && $v['unit_id'] == $this->input->post('select_unit')[$x])

                                {

                                    $purchase_return_qty = $v['qty'];

                                }

                            }



                            if($item_exist){

                                $quantity = $item_exist['quantity'];



                                $unit_data_values = $this->Model_products->getUnitValuesData();

                                $unit_value = 1;

                                foreach($unit_data_values as $k => $v){

                                    if($v['unit_id'] == $this->input->post('select_unit')[$x]){

                                        $unit_value = $v['unit_value'];

                                    }

                                }

                                $quantity += ($this->input->post('qty')[$x] * $unit_value);

                                $stock = array(

                                    'quantity' => $quantity - $purchase_return_qty

                                );

                                $this->db->where('id', $item_exist['id']);

                                $this->db->update('items_stock', $stock);

                            }

                            else

                            {

                                // else if item does not exist in stock

                                $unit_data_values = $this->Model_products->getUnitValuesData();

                                $unit_value = 1;

                                foreach($unit_data_values as $k => $v){

                                    if($v['unit_id'] == $this->input->post('select_unit')[$x]){

                                        $unit_value = $v['unit_value'];

                                    }

                                }

                                $stock = array(

                                    'category_id' => $category_id,

                                    'product_id' => $product_id,

                                    'unit_id' => $this->input->post('select_unit')[$x],

                                    'quantity' => ($this->input->post('qty')[$x] * $unit_value)

                                );

                                $this->db->insert('items_stock', $stock);

                            }

                        }//end for items



                        // update vendor balance

                        $vendor_data = $this->Model_supplier->getSupplierData($vendor_id);

                        $balance = $vendor_data['balance'] - ($purchase_order_data['net_amount'] - $purchase_return_amount - $purchase_order_data['total_paid']);

                        $data = array(

                            'balance' => $balance + ($net_amount - $total_paid)

                        );

                        $this->db->where('id', $vendor_id);

                        $this->db->update('supplier', $data);



                        $prev_loan_deduction = $this->Model_loan->getLoanDeductions($order_id);

                        if(empty($prev_loan_deduction))//previous loan does not exist

                        {

                            $loan_deduction = $this->input->post('loan_deduction');

                            $loan_data = $this->Model_loan->getVendorRemainingLoanData($vendor_id);

                            if($loan_deduction && !(empty($loan_data))) //$loan_deduction > 0

                            {

                                $loanDeduction = array(

                                    'vendor_id' => $vendor_id,

                                    'order_id' => $order_id,

                                    'loan_id' => $loan_data['loan_id'],

                                    'deduction_amount' => $loan_deduction,

                                    'remaining_amount' => $loan_data['remaining_amount'] - $loan_deduction

                                );

                                $insert = $this->db->insert('loan_deduction', $loanDeduction);

                                if($insert)

                                {

                                    $amount = $loan_data['amount'];

                                    $paid_amount = $loan_data['paid_amount'] + $loan_deduction;

                                    $remaining_amount = $loan_data['remaining_amount'] - $loan_deduction;

                                    $paid_status = 0;

                                    if($amount == $paid_amount)

                                    {

                                        $paid_status = 1;

                                    }

                                    $loan = array(

                                        'paid_amount' => $paid_amount,

                                        'remaining_amount' => $remaining_amount,

                                        'paid_status' => $paid_status

                                    );

                                    $this->db->where('id', $loan_data['loan_id']);

                                    $update = $this->db->update('loan', $loan);

                                }

                            }

                        }

                        else//previous loan deduction do exist

                        {

                            $loan_deduction = $this->input->post('loan_deduction');

                            if($loan_deduction == 0)

                            {

                                $prev_amount = $prev_loan_deduction['deduction_amount'];

                                if($prev_amount > 0)

                                {

                                    $loan_id = $prev_loan_deduction['loan_id'];

                                    $loan_data = $this->Model_loan->getLoanData($loan_id);



                                    $paid_amount = $loan_data['paid_amount'] - $prev_amount;

                                    $remaining_amount = $loan_data['remaining_amount'] + $prev_amount;

                                    $paid_status = $loan_data['paid_status'];

                                    if($paid_status == 1)

                                    {

                                        $paid_status = 0;

                                    }



                                    $loan = array(

                                        'paid_amount' => $paid_amount,

                                        'remaining_amount' => $remaining_amount,

                                        'paid_status' => $paid_status

                                    );

                                    $this->db->where('id', $loan_id);

                                    $update = $this->db->update('loan', $loan);

                                }

                                $this->db->where('id', $prev_loan_deduction['id']);

                                $this->db->delete('loan_deduction');

                            }

                            else if($loan_deduction > 0)

                            {

                                if($loan_deduction < $prev_loan_deduction['deduction_amount'])

                                {

                                    $previous_loan_amount = $prev_loan_deduction['deduction_amount'];

                                    $loan_id = $prev_loan_deduction['loan_id'];

                                    $loan_data = $this->Model_loan->getLoanData($loan_id);

                                    $temp = $previous_loan_amount - $loan_deduction;

                                    $paid_amount = $loan_data['paid_amount'] - $temp;

                                    $remaining_amount = $loan_data['remaining_amount'] + $temp;

                                    $paid_status = $loan_data['paid_status'];

                                    if($paid_status == 1)

                                    {

                                        $paid_status = 0;

                                    }

                                    $loan = array(

                                        'paid_amount' => $paid_amount,

                                        'remaining_amount' => $remaining_amount,

                                        'paid_status' => $paid_status

                                    );

                                    //update loan

                                    $this->db->where('id', $loan_id);

                                    $update = $this->db->update('loan', $loan);

                                    //update loan deduction

                                    $loan = array(

                                        'deduction_amount' => $loan_deduction,

                                        'remaining_amount' => $temp

                                    );

                                    $this->db->where('id', $prev_loan_deduction['id']);

                                    $update = $this->db->update('loan_deduction', $loan);

                                }

                                else if($loan_deduction > $prev_loan_deduction['deduction_amount'])

                                {

                                    $previous_loan_amount = $prev_loan_deduction['deduction_amount'];

                                    $loan_id = $prev_loan_deduction['loan_id'];

                                    $loan_data = $this->Model_loan->getLoanData($loan_id);

                                    $amount = $loan_data['amount'];

                                    // 500 now - 400 prev

                                    $temp = $loan_deduction - $previous_loan_amount;

                                    $paid_amount = $loan_data['paid_amount'] + $temp;

                                    $remaining_amount = $loan_data['remaining_amount'] - $temp;

                                    $paid_status = 0;

                                    if($amount == $paid_amount)

                                    {

                                        $paid_status = 1;

                                    }

                                    $loan = array(

                                        'paid_amount' => $paid_amount,

                                        'remaining_amount' => $remaining_amount,

                                        'paid_status' => $paid_status

                                    );

                                    $this->db->where('id', $loan_id);

                                    $update = $this->db->update('loan', $loan);

                                    //update loan deduction

                                    $loan = array(

                                        'deduction_amount' => $loan_deduction,

                                        'remaining_amount' => $prev_loan_deduction['deduction_amount'] + $temp

                                    );

                                    $this->db->where('id', $prev_loan_deduction['id']);

                                    $update = $this->db->update('loan_deduction', $loan);

                                }

                            }

                        }

                        // end

                        if($selected_po_id > 0) {
                            $supply_update = $this->Model_purchase_order->updateSupplyStatusFromPurchasing($selected_po_id, $order_id);
                            if($supply_update === null) {
                                log_message('error', 'Failed to update PO supply status after purchasing update. PO ID: ' . $selected_po_id . ' Purchase Order ID: ' . $order_id);
                            }
                            $payment_update = $this->Model_purchase_order->updatePaymentStatusFromPurchasing($selected_po_id, $order_id);
                            if($payment_update === null) {
                                log_message('error', 'Failed to update PO payment status after purchasing update. PO ID: ' . $selected_po_id . ' Purchase Order ID: ' . $order_id);
                            }
                        } else {
                            $supply_update = $this->Model_purchase_order->updateSupplyStatusFromPurchasingOrder($order_id);
                            if($supply_update === null) {
                                log_message('error', 'Failed to update PO supply status after purchasing update. Purchase Order ID: ' . $order_id);
                            }
                            $payment_update = $this->Model_purchase_order->updatePaymentStatusFromPurchasingOrder($order_id);
                            if($payment_update === null) {
                                log_message('error', 'Failed to update PO payment status after purchasing update. Purchase Order ID: ' . $order_id);
                            }
                        }

                        $this->session->set_flashdata('success', 'Successfully updated');

                        redirect('/Product/view_order_items/'.$order_id, 'refresh');

                    }

                    else{

                        // we have a problem while updating the order

                    }

                }

                else

                {
                    /*print_r($data);
                    exit();*/
                    $data['page_title'] = "Update Purchase Page";

                    $this->load->view('templates/header', $data);

                    $this->load->view('templates/header_menu');

                    $this->load->view('templates/side_menubar');



                    $data['purchase_order_data'] = $this->Model_products->getPurchaseOrdersData($order_id);

                    $data['purchase_items_data'] = $this->Model_products->getPurchaseItemsData($order_id);

                    $data['purchase_return_data'] = $this->Model_products->fetchPurchaseReturnsData($order_id);

                    $vendor_id = $data['purchase_order_data']['vendor_id'];

                    $data['vendor_data'] = $this->Model_supplier->getAllSupplierData($vendor_id);

                    $data['vendor_products'] = $this->Model_products->getVendorProductsData($vendor_id);

                    $data['units_data'] = $this->Model_products->getUnitsData();

                    $data['unit_data_values'] = $this->Model_products->getUnitValuesData();
                    $data['po_orders'] = $this->Model_purchase_order->getOrdersForDropdownAll();
                    $data['selected_po_id'] = 0;
                    $po_link = $this->Model_purchase_order->getPurchasingLinkFieldInfo();
                    $po_link_value = $this->Model_purchase_order->getPurchasingLinkValueByPurchaseOrderId($order_id);
                    if ($po_link && $po_link_value !== null) {
                        if ($po_link['field'] === 'purchase_order_custom_id' || $po_link['field'] === 'po_id') {
                            $data['selected_po_id'] = (int)$po_link_value;
                        } else {
                            $row = $this->db->query("SELECT id FROM purchase_orders_custom WHERE po_number = ? LIMIT 1", array($po_link_value))->row_array();
                            if ($row && isset($row['id'])) {
                                $data['selected_po_id'] = (int)$row['id'];
                            }
                        }
                    }

                    $data['loan_data'] = $this->Model_loan->getVendorRemainingLoanData($vendor_id);

                    $data['loan_deductions'] = $this->Model_loan->getLoanDeductions($order_id);



                    $user_id = $this->session->userdata('id');

                    $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

                    $data['user_permission'] = unserialize($group_data['permission']);



                    $this->load->view('products/update_purchase_order', $data);

                    $this->load->view('templates/footer');

                }

            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }

    }



    public function remove_purchase_order()

    {

        $order_id = $this->input->post('order_id');

        $response = array();

        if($order_id)

        {

            // check if stock has that much items

            $purchase_order_data = $this->Model_products->getPurchaseOrdersData($order_id);

            $purchase_items_data = $this->Model_products->getPurchaseItemsData($order_id);

            $purchase_return_data = $this->Model_products->fetchPurchaseReturnsData($order_id);

            if(!empty($purchase_return_data)){

                foreach ($purchase_items_data as $key => $value)

                {

                    foreach ($purchase_return_data as $k => $v)

                    {

                        if($value['product_id'] == $v['product_id'] && $value['category_id'] == $v['category_id'] && $value['unit_id'] == $v['unit_id'])

                        {

                            $unit_data_values = $this->Model_products->getUnitValuesData();

                            $unit_value = 1;

                            foreach($unit_data_values as $u_k => $u_v){

                                if($value['unit_id'] == $u_v['unit_id']){

                                    $unit_value = $u_v['unit_value'];

                                }

                            }

                            $order_retuns_qty = $v['qty'];

                            $stock_data = $this->Model_products->itemExistInStock($value['category_id'], $value['product_id'], $value['unit_id']);

                            $stock_quantity = $stock_data['quantity'] + $order_retuns_qty - ($value['qty'] * $unit_value);

                            if($stock_quantity < 0)

                            {

                                $response['success'] = false;

                                $response['location'] = "";

                                $response['messages'] = "Invalid Transaction. Contact with system administration. Or check your stock Quantity!";

                                echo json_encode($response);

                                return 0;

                            }

                        }

                    }

                }

            }

            else

            {

                foreach ($purchase_items_data as $key => $value)

                {

                    $unit_data_values = $this->Model_products->getUnitValuesData();

                    $unit_value = 1;

                    foreach($unit_data_values as $k => $v){

                        if($v['unit_id'] == $value['unit_id']){

                            $unit_value = $v['unit_value'];

                        }

                    }

                    $stock_data = $this->Model_products->itemExistInStock($value['category_id'], $value['product_id'], $value['unit_id']);

                    $stock_quantity  = $stock_data['quantity'] - ($value['qty'] * $unit_value);

                    if($stock_quantity < 0)

                    {

                        $response['success'] = false;

                        $response['location'] = "";

                        $response['messages'] = "Invalid Transaction. Contact with system administration. Or check your stock Quantity !";

                        echo json_encode($response);

                        return;

                    }

                }

            }

            // if both case gives false

            // now you can delete

            $supplier_id = '';

            foreach ($purchase_items_data as $key => $value)

            {

                $supplier_id = $value['vendor_id'];

                $purchase_return_qty = 0;

                foreach ($purchase_return_data as $k => $v)

                {

                    if($v['product_id'] == $value['product_id'] && $v['category_id'] == $value['category_id'] && $v['unit_id'] == $value['unit_id'])

                    {

                        $purchase_return_qty = $v['qty'];

                        // delete this purchase return

                        $this->db->where('id', $v['id']);

                        $this->db->delete('purchase_returns');

                    }

                }



                $stock = $this->Model_products->itemExistInStock($value['category_id'], $value['product_id'], $value['unit_id']);

                $unit_data_values = $this->Model_products->getUnitValuesData();

                $unit_value = 1;

                foreach($unit_data_values as $k => $v){

                    if($v['unit_id'] == $value['unit_id']){

                        $unit_value = $v['unit_value'];

                    }

                }

                $qty = ($stock['quantity'] + $purchase_return_qty) - ($value['qty'] * $unit_value);

                $stock_data = array(

                    'quantity' => $qty

                );

                $this->db->where('id', $stock['id']);

                $this->db->update('items_stock', $stock_data);

                $this->db->where('id', $value['id']);

                $this->db->delete('purchase_items');

            }

            // update the vendor balance

            $vendor_data = $this->Model_supplier->getSupplierData($supplier_id);

            $balance = $vendor_data['balance'] - ($purchase_order_data['net_amount'] - $purchase_order_data['total_paid']);

            $data = array(

                'balance' => $balance

            );

            $this->db->where('id', $supplier_id);

            $this->db->update('supplier', $data);



            // if supplier payment have this order id

            $supplier_payment_data = $this->Model_supplier->fetchOpeningBalancePayment($order_id, $purchase_order_data['vendor_id']);

            if(!empty($supplier_payment_data))

            {

                $prevoius_order_id = 0;

                $prevoius_order_data = $this->Model_supplier->getVendorPreviousOrderId($supplier_id, $purchase_order_data['datetime_created']);

                if(!empty($prevoius_order_data)){

                    $prevoius_order_id = $prevoius_order_data['id'];

                }

                foreach($supplier_payment_data as $key => $value) {

                    $data = array(

                        'most_recent_order_id' => $prevoius_order_id

                    );

                    $this->db->where('id', $value['id']);

                    $this->db->update('supplier_ob_payments', $data);

                }

            }



            // Now you can delete purchase order

            $delete = $this->Model_products->removePurchaseOrder($order_id);

            if($delete)

            {

                $prev_loan_deduction = $this->Model_loan->getLoanDeductions($order_id);

                if(!empty($prev_loan_deduction))

                {

                    // $this->db->where('id', $prev_loan_deduction['id']);

                    // $this->db->delete('loan_deduction');

                    $loan_id = $prev_loan_deduction['loan_id'];

                    $loan_data = $this->Model_loan->getLoanData($loan_id);

                    $paid_amount = $loan_data['paid_amount'] - $prev_loan_deduction['deduction_amount'];

                    $remaining_amount = $loan_data['remaining_amount'] + $prev_loan_deduction['deduction_amount'];

                    $paid_status = $loan_data['paid_status'];

                    if($paid_status == 1)

                    {

                        $paid_status = 0;

                    }

                    $loan = array(

                        'paid_amount' => $paid_amount,

                        'remaining_amount' => $remaining_amount,

                        'paid_status' => $paid_status

                    );

                    $this->db->where('id', $loan_id);

                    $this->db->update('loan', $loan);

                }

                $response['success'] = true;

                $response['location'] = "manage_purchase_orders";

                $this->session->set_flashdata('success', 'deleted Successfully');

                $response['messages'] = "Successfully removed";

            }

            else

            {

                $response['success'] = false;

                $response['location'] = "";

                $response['messages'] = "Error in the database while removing the purchased order information";

            }

        }

        else {

            $response['success'] = false;

            $response['location'] = "";

            $response['messages'] = "Refersh the page again!!";

        }

        echo json_encode($response);

    }



    public function manage_purchase_orders()

    {

        if(!in_array('recordPurchasing', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['page_title'] = "Manage Purchasing";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');



            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/manage_purchase_orders', $data);

            $this->load->view('templates/footer');

        }



    }



    public function fetchPurchaseOrders()

    {

        $result = array('data' => array());

        $data = $this->Model_products->getPurchaseOrdersData();

        $counter = 1;

        foreach ($data as $key => $value)

        {

            $count_total_item = $this->Model_products->countOrderItem($value['id']);

            $buttons = '';

            if(in_array('viewPurchasing', $this->permission))

            {

                $buttons .= '<a title="View Purchase Order" href="'.base_url().'index.php/Product/view_order_items/'.$value['id'].'"><span class="glyphicon glyphicon-eye-open"><span></a>';

            }

            if(in_array('updatePurchasing', $this->permission))

            {

                $buttons .= ' <a title="Edit Purchased Order" href="'.base_url().'index.php/Product/update_purchase_order/'.$value['id'].'"><span class="glyphicon glyphicon-pencil"><span></a>';

            }

            if(in_array('deletePurchasing', $this->permission))

            {

                $buttons .= ' <a title="Delete Purchased Order" onclick="removePurchaseOrder('.$value['id'].')" data-toggle="modal" href="#removeModal"><span class="glyphicon glyphicon-trash"><span></a>';

            }



            if(in_array('viewPurchaseReturn', $this->permission))

            {

                $buttons .= ' <a title="View Order Returns" href="'.base_url().'index.php/Product/mark_purchase_order_returns/'.$value['id'].'" ><span class="glyphicon glyphicon-new-window"><span></a>';

            }



            $vendor_data = $this->Model_products->getSupplierData($value['vendor_id']);

            $returns_amount = 0;

            $purchase_return_data = $this->Model_products->getPurchaseReturnsAmount($value['id']);

            if($purchase_return_data){

                $returns_amount = $purchase_return_data['returns_amount'];

            }

            $count_total_item_link = '';

            if(in_array('viewPurchasing', $this->permission))

            {

                $count_total_item_link = '<a href="'.base_url().'index.php/Product/view_order_items/'.$value['id'].'">'.$count_total_item.'</a>';

            }

            else

            {

                $count_total_item_link .= $count_total_item;

            }

            $vendor_name = '<span style="text-transform:capitalize">'.$vendor_data['first_name']. ' '. $vendor_data['last_name'].'</span>';



            $date = date('d-m-Y', strtotime($value['datetime_created']));

            $time = date('h:i a', strtotime($value['datetime_created']));

            $datetime = $date . ' ' . $time;

            //

            $userdata = $this->Model_users->getUserData($value['user_id']);

            $created_by = '<span style="text-transform:capitalize">'.$userdata['firstname'].' '.$userdata['lastname'].'</span>';

            $result['data'][$key] = array(

                $counter++,

                $datetime,

                $value['bill_no'],

                $vendor_name,

                $created_by,

                floatval($value['net_amount'] - $returns_amount),

                floatval(($value['opening_balance'] + $value['net_amount'] - $value['total_paid'] - $returns_amount)),

                $buttons

            );

        }

        echo json_encode($result);

    }



    public function view_order_items($order_id)

    {

        if(!in_array('viewPurchasing', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['purchase_order_data'] = $this->Model_products->getPurchaseOrdersData($order_id);

            if(!empty($data['purchase_order_data'])){



                $data['page_title'] = "View Purchased Order";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');



                $data['purchase_order_data'] = $this->Model_products->getPurchaseOrdersData($order_id);

                $data['purchase_items_data'] = $this->Model_products->getPurchaseItemsData($order_id);

                $data['purchase_return_data'] = $this->Model_products->fetchPurchaseReturnsData($order_id);

                $vendor_id = $data['purchase_order_data']['vendor_id'];

                $data['vendor_data'] = $this->Model_products->getSupplierData($vendor_id);



                $data['vendor_products'] = $this->Model_products->getVendorProductsData($vendor_id);

                $data['units_data'] = $this->Model_products->getUnitsData();

                $data['unit_data_values'] = $this->Model_products->getUnitValuesData();
                $data['po_orders'] = $this->Model_purchase_order->getOrdersForDropdownAll();
                $data['selected_po_id'] = 0;
                $po_link = $this->Model_purchase_order->getPurchasingLinkFieldInfo();
                $po_link_value = $this->Model_purchase_order->getPurchasingLinkValueByPurchaseOrderId($order_id);
                if ($po_link && $po_link_value !== null) {
                    if ($po_link['field'] === 'purchase_order_custom_id' || $po_link['field'] === 'po_id') {
                        $data['selected_po_id'] = (int)$po_link_value;
                    } else {
                        $row = $this->db->query("SELECT id FROM purchase_orders_custom WHERE po_number = ? LIMIT 1", array($po_link_value))->row_array();
                        if ($row && isset($row['id'])) {
                            $data['selected_po_id'] = (int)$row['id'];
                        }
                    }
                }

                $data['loan_data'] = $this->Model_loan->getVendorRemainingLoanData($vendor_id);

                $data['loan_deductions'] = $this->Model_loan->getLoanDeductions($order_id);



                $user_id = $this->session->userdata('id');

                $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

                $data['user_permission'] = unserialize($group_data['permission']);



                $this->load->view('products/view_order_items', $data);

                $this->load->view('templates/footer');

            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }

    }



    public function fetchPurchasePayments($order_id)

    {

        $result = array('data' => array());

        $data = $this->Model_products->getPurchaseOrdersData($order_id);

        $temp = '!@#$';

        $payment_method_array = explode($temp, $data['payment_method']);

        $payment_date_array = explode($temp, $data['payment_date']);

        $paid_array = explode($temp, $data['paid']);

        $payment_note_array = explode($temp, $data['payment_note']);

        $x = 0;

        foreach ($payment_method_array as $key => $value)

        {

            if(!empty($payment_method_array[$x]))

            {

                $payment_method = $payment_method_array[$x];

                $paid_amount = $paid_array[$x];

                $payment_note = $payment_note_array[$x];

                $payment_date = $payment_date_array[$x];

                $x++;

                $result['data'][$key] = array(

                    $x,

                    $payment_method,

                    floatval($paid_amount),

                    $payment_note,

                    $payment_date

                );

            }

        }

        echo json_encode($result);

    }



    public function fetchPurchaseItems($order_id)

    {

        $result = array('data' => array());

        $data = $this->Model_products->getPurchaseItemsData($order_id);

        $counter = 1;

        foreach ($data as $key => $value)

        {

            $return_items = 0;

            $purchase_return_data = $this->Model_products->fetchPurchaseReturnsData($value['purchase_order_id']);

            foreach ($purchase_return_data as $k => $v)

            {

              if($value['product_id'] == $v['product_id'] && $value['category_id'] == $v['category_id'] && $value['unit_id'] == $v['unit_id'])

              {

                $return_items = $v['qty'];

              }

            }



            $product_data = $this->Model_products->getAllProductData($value['product_id']);

            $product_name = $product_data['name'];

            $category_name = '';

            if($value['category_id'])

            {

                $category_data = $this->Model_category->getAllCategoryData($value['category_id']);

                if(!empty($category_data)){

                    $category_name = $category_data['name'];

                }

            }

            else

            {

                $category_name = 'Nill';

            }

            $unit_name = $this->Model_products->getAllUnitsData($value['unit_id'])['unit_name'];

            if(!empty($purchase_return_data)){

                $result['data'][$key] = array(

                    $counter,

                    $category_name,

                    $product_name,

                    $unit_name,

                    floatval($value['qty']),

                    floatval($return_items),

                    floatval($value['product_price']),

                    floatval(($value['qty'] * $value['product_price']))

                );

            }

            else{

                $result['data'][$key] = array(

                    $counter,

                    $category_name,

                    $product_name,

                    $unit_name,

                    floatval($value['qty']),

                    floatval($value['product_price']),

                    floatval(($value['qty'] * $value['product_price']))

                );

            }

        }

        echo json_encode($result);

    }

    public function manage_stock1()

    {

        if(!in_array('viewFactoryStock', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['page_title'] = "Factory Stock";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');

            $user_id = $this->session->userdata('id');
            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
            $data['user_permission'] = unserialize($group_data['permission']);
            $this->load->view('products/manage_stock', $data);
            $this->load->view('templates/footer');
        }



    }

    public function manage_stock()

    {

        if(!in_array('viewFactoryStock', $this->permission)) {

            $data['page_title'] = "No Permission";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/forbidden_access');
        }
        else
        {

            $data['page_title'] = "Factory Stock";
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');

            $user_id = $this->session->userdata('id');
            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
            $data['user_permission'] = unserialize($group_data['permission']);
            $this->load->view('products/manage_stock', $data);
            $this->load->view('templates/footer');
        }



    }

    public function duplicaterecord()
    {
        $data = $this->Model_products->getStockWithUnitsData2();
        $product_data = [];
        foreach ($data as $key => $value)
        $product_data['products'][$key] = $this->Model_products->getAllProductData($value['product_id']);
        $product_data['id'][$key] =$value['id'];
        {

        }
        echo "<pre>";
        print_r($product_data);

    }

    public function fetchStockData()

    {
        $result = array('data' => array());
        $date_time = isset($_GET['date_time']) ? $_GET['date_time'] : ''; // Get the date from form submission
        //$data = $this->Model_products->getStockWithUnitsData();
        $data = $this->Model_products->getStockWithUnitsProductsData($date_time);
        /*  echo "<pre>";
        print_r($data);
        echo "</pre>";
        exit; */
        $counter = 1;
        foreach ($data as $key => $value)

        {

            $product_data = $this->Model_products->getAllProductData($value['product_id']);
            //print_r( $data);//exit;
            $is_deleted = $value['is_deleted'];
            $product_name = $product_data['name'];
            //$date_time = $product_data['date_time'];
            $category_name = '';
            if($value['category_id'])
            {
                $category_data = $this->Model_category->getAllCategoryData($value['category_id']);

                if(!empty($category_data)){

                    $category_name = $category_data['name'];
                }

            }

            else

            {

                $category_name = 'Nill';

            }

            $unit_name = $this->Model_products->getAllUnitsData($value['unit_id'])['unit_name'];

            $unit_value = $this->Model_products->fetchUnitValueData($value['unit_id'])['unit_value'];

            $unit_quantity = intval($value['sum_qty'] / $unit_value);

            $non_unit_qty = $value['sum_qty'] % $unit_value;

            $points_unit_qty = 0;

            if($value['sum_qty'] - $unit_quantity < 1 && $value['sum_qty'] - $unit_quantity > 0){

                $points_unit_qty = $value['sum_qty'] - $unit_quantity;

                $non_unit_qty += $points_unit_qty;

            }



            $qty = '';
            $remarks = 0;

            $stock_order_level = $this->Model_products->getFactoryStockOrderLevelData()['value'];

            if($unit_quantity < $stock_order_level)

            {

                if($non_unit_qty > 0){

                    $qty = $unit_quantity.' — ( '. number_format($non_unit_qty, 3) .' - Other )'.' &nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-warning">Low</span>';

                }else{

                    $qty = $unit_quantity.' &nbsp;&nbsp;&nbsp;&nbsp; <span class="label label-warning">Low</span>';

                }

            }

            else

            {

                if($non_unit_qty > 0){

                    $qty = $unit_quantity.' — ( '. number_format($non_unit_qty, 3) .' - Other )';

                }

                else{

                    $qty = $unit_quantity;

                }

            }
             $user_id = $this->session->userdata('id');
            if($user_id == 6){

                if ($is_deleted == 1) {
                    $is_deleted = "Deleted";


                } else {
                    $is_deleted = "";
                }
                      $result['data'][$key] = array(

                $is_deleted,
                $counter++,

                $category_name,

                $product_name,

                $unit_name,

              //  $date_time,


              /*   $qty,
                "<a href='https://tbmtariq.tbmengineering.com/index.php/Product/manage_stock_remove?id=".$value['id']."'> delete </a>"
 */
                $qty,
                $remarks,
                "<a href='#' class='delete-record' data-id='".$value['id']."'>delete</a>"

            );
                }else{
                      $result['data'][$key] = array(

                $counter++,

                $category_name,

                $product_name,

                $unit_name,

                //$date_time,

                $qty,
                $remarks,
                $is_deleted,

              );
                }


        }
        //print_r($result);
        echo json_encode($result);

    }



    public function manage_stock_removeBk()
    {
        $id = $_REQUEST['id'];
        $data = $this->Model_products->getStockWithUnitsDataremove($id);


    }

    public function manage_stock_remove()
{
    //echo $_POST['id'];
    if(isset($_POST['id'])) {
        $id = $_POST['id'];
        $result = $this->Model_products->getStockWithUnitsDataremove($id);
        if ($result) {

            $response['success'] = true;
            $response['messages'] = "Deleted successfully";
            $this->session->set_flashdata('success', 'Deleted Successfully');
            //echo json_encode(array('success' => true));
            echo json_encode($response);
        } else {
            echo json_encode(array('success' => false));
        }
    }
}

    public function factory_stock_by_item()

    {

        if(!in_array('viewFactoryStock', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['page_title'] = "Factory Stock";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');



            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/factory_stock_by_item', $data);

            $this->load->view('templates/footer');

        }



    }



    public function fetchFactoryStockByItem()

    {

        $result = array('data' => array());

        $data = $this->Model_products->getFactoryStockByItemName();

        $counter = 1;

        foreach ($data as $key => $value)

        {

            $product_name = $value['item_name'];

            $unit_name = $value['unit_name'];

            $qty = intval($value['qty']);

            //

            $result['data'][$key] = array(

                $counter++,

                $product_name,

                $unit_name,

                $qty

            );

        }

        echo json_encode($result);

    }



    public function view_office_stock()

    {

        if(!in_array('viewOfficeStock', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['page_title'] = "Office Stock";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');



            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/view_office_stock', $data);

            $this->load->view('templates/footer');

        }



    }



    public function fetchOfficeStockData()

    {

        $result = array('data' => array());

        $data = $this->Model_products->getOfficeStockWithUnitsData();

        $counter = 1;

        foreach ($data as $key => $value)

        {

            $product_data = $this->Model_products->getAllProductData($value['product_id']);

            $product_name = $product_data['name'];

            $category_name = '';

            if($value['category_id'])

            {

                $category_data = $this->Model_category->getAllCategoryData($value['category_id']);

                if(!empty($category_data)){

                    $category_name = $category_data['name'];

                }

            }

            else

            {

                $category_name = 'Nill';

            }

            $unit_name = $this->Model_products->getAllUnitsData($value['unit_id'])['unit_name'];

            $unit_value = $this->Model_products->fetchUnitValueData($value['unit_id'])['unit_value'];



            $unit_quantity = intval($value['sum_qty'] / $unit_value);

            $non_unit_qty = $value['sum_qty'] % $unit_value;

            $points_unit_qty = 0;

            if($value['sum_qty'] - $unit_quantity < 1 && $value['sum_qty'] - $unit_quantity > 0){

                $points_unit_qty = $value['sum_qty'] - $unit_quantity;

                $non_unit_qty += $points_unit_qty;

            }



            $qty = '';

            $stock_order_level = $this->Model_products->getFactoryStockOrderLevelData()['value'];

            if($unit_quantity < $stock_order_level)

            {

                if($non_unit_qty > 0){

                    $qty = $unit_quantity.' — ( '. number_format($non_unit_qty, 2) .' - Other )'.' &nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-warning">Low</span>';

                }else{

                    $qty = $unit_quantity.' &nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-warning">Low</span>';

                }

            }

            else

            {

                if($non_unit_qty > 0){

                    $qty = $unit_quantity.' — ( '. number_format($non_unit_qty, 3) .' - Other )';

                }

                else{

                    $qty = $unit_quantity;

                }

            }

            $result['data'][$key] = array(

                $counter++,

                $category_name,

                $product_name,

                $unit_name,

                $qty

            );

        }

        echo json_encode($result);

    }



    public function stock_order_level()

    {

        if(!in_array('viewStockOrderLevel', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['page_title'] = "Stock Order Level";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');



            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/stock_order_level', $data);

            $this->load->view('templates/footer');

        }



    }



    public function fetchStockOrderLevelData()

    {

        $result = array('data' => array());

        $data = $this->Model_products->getStockOrderLevelData();

        $counter = 1;

        foreach ($data as $key => $value)

        {

            $button = '';

            if(in_array('updateStockOrderLevel', $this->permission)) {

                $button .= '<a title="Edit Stock Order Level" onclick="editFunc('.$value['id'].')" data-toggle="modal" href="#editModal"><i class="glyphicon glyphicon-pencil"></i></a>';

            }

            $stock_type = "";

            if($value['stock_type'] == 1){

                $stock_type = "Factory Stock";

            }

            else if($value['stock_type'] == 2){

                $stock_type = "Office Stock";

            }

            $result['data'][$key] = array(

                $counter++,

                $stock_type,

                $value['value'],

                $button

            );

        }

        echo json_encode($result);

    }



    public function get_stock_order_level_row()

    {

        if(isset($_POST['id'])){

            $id = $_POST['id'];

            $output['data'] = $this->Model_products->getStockOrderLevelData($id);

            echo json_encode($output);

        }

    }



    public function edit_stock_order_level()

    {

        if(isset($_POST['submit'])){

            $id = $this->input->post('stock_order_level_id');

            $data = array(

                'value' => $this->input->post('order_level_value')

            );

            $this->db->where('id', $id);

            $update = $this->db->update('stock_order_level', $data);

            if($update){

                $this->session->set_flashdata('success', 'Stock Order Level updated successfully!');

                return redirect('/Product/stock_order_level');

            }

            else{

                $this->session->set_flashdata('error', 'Error occurred while updating Stock Order Level!');

                return redirect('/Product/stock_order_level');

            }

        }

        else{

            return redirect('/Product/stock_order_level');

        }

    }



    public function manage_office_stock()

    {

        if(!in_array('recordOfficeStockTransfer', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['page_title'] = "Manage Office Stock";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');



            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/manage_office_stock', $data);

            $this->load->view('templates/footer');

        }

    }



    public function fetchOfficeStockTransferData()

    {

        $result = array('data' => array());

        $data = $this->Model_products->getOfficeStockTransferData();

        $counter = 1;

        foreach ($data as $key => $value)

        {

            $buttons = '';

            if(in_array('viewOfficeStockTransfer', $this->permission))

            {

                $buttons .= ' <a href="'.base_url().'index.php/Product/view_office_stock_transfered_items/'.$value['id'].'" title="View Office Stock Transfered Items"><i class="glyphicon glyphicon-eye-open"></i></a>';

            }

            if(in_array('updateOfficeStockTransfer', $this->permission))

            {

                $buttons .= ' <a href="'.base_url().'index.php/Product/update_office_stock/'.$value['id'].'" title="Edit Office Stock Transfered Items"><i class="glyphicon glyphicon-pencil"></i></a>';

            }

            if(in_array('deleteOfficeStockTransfer', $this->permission))

            {

                $buttons .= ' <a title="Delete Office Stock Transfered Items" onclick="removeFunc('.$value['id'].')" data-toggle="modal" href="#removeModal"><i class="glyphicon glyphicon-trash"></i></a>';

            }



            $count_total_item = $this->Model_products->countOfficeStockTransferItems($value['id']);



            $date = date('d-m-Y', strtotime($value['date_time']));

            $time = date('h:i a', strtotime($value['date_time']));

            $date_time = $date . ' ' . $time;

            $user_data = $this->Model_users->getUserData($value['user_id']);



            $result['data'][$key] = array(

                $counter++,

                $value['id'],

                $date_time,

                ($user_data['firstname'].' '.$user_data['lastname']),

                $count_total_item,

                $buttons

            );

        }

        echo json_encode($result);

    }



    public function add_office_stock()

    {

        if(!in_array('createOfficeStockTransfer', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $this->form_validation->set_rules('product[]', 'Product', 'trim|required');

            if($this->form_validation->run() == TRUE)

            {

                date_default_timezone_set("Asia/Karachi");

                $date = date('Y-m-d');



                // check if data in stock exist

                $mainInputArray = array();

                // fill this array with factory stock data

                $stock_product_data = $this->Model_products->getFactoryStockData();

                for($i = 0; $i < count($stock_product_data); $i++){

                    $temp = array();

                    $temp[0] = $stock_product_data[$i]['category_id'];

                    $temp[1] = $stock_product_data[$i]['product_id'];

                    $temp[2] = $stock_product_data[$i]['unit_id'];

                    $temp[3] = $this->Model_products->itemExistInStock($stock_product_data[$i]['category_id'], $stock_product_data[$i]['product_id'], $stock_product_data[$i]['unit_id'])['quantity'];

                    array_push($mainInputArray, $temp);

                }



                $count_product = count($this->input->post('product'));

                for($x = 0; $x < $count_product; $x++)

                {

                    // inputed product data from factory stock

                    $product_id = explode("-",$this->input->post('product')[$x])[0];

                    $category_id = explode("-",$this->input->post('product')[$x])[1];

                    $unit_id = explode("-",$this->input->post('product')[$x])[2];



                    // inputed transfer data unit

                    $inputed_unit_id = $this->input->post('unit')[$x];

                    if(empty($category_id)){

                        $category_id = 0;

                    }

                    for ($i = 0; $i < count($mainInputArray); $i++)

                    {

                        if($mainInputArray[$i][0] == $category_id){

                            if($mainInputArray[$i][1] == $product_id)

                            {

                                if($mainInputArray[$i][2] == $unit_id)

                                {

                                    // To macth this product with factory stock product

                                    //  MATCHED

                                    // Reduce Factory Stcok amount using transfer inputed unit value

                                    $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                                    $mainInputArray[$i][3] -= ($this->input->post('qty')[$x] * $unit_value);

                                }

                            }

                        }

                    }

                }

                // first check if stock data exist

                if(empty($mainInputArray)){

                    // stock data doesnt exit

                    $this->session->set_flashdata('errors', 'Factory Stock do not have these items!');

                    redirect('/Product/create_sale_order', 'refresh');

                }

                else{

                    // now check if in new mainInput array any qty is negitive

                    foreach ($mainInputArray as $key => $value) {

                        if($value[3] < 0){

                            $data['input_products'] = $this->input->post('product');

                            $data['input_qty'] = $this->input->post('qty');

                            $data['input_units'] = $this->input->post('unit');

                            $data['input_s_qty'] = $this->input->post('s_qty_value');



                            $product_data = $this->Model_products->getProductData($value[1]);

                            $category_name = '';

                            if($value[0]){

                                $category_name = '&#8212 '.$this->Model_category->getCategoryData($value[0])['name'];

                            }

                            $unit_data = $this->Model_products->getUnitsData($value[2]);

                            $this->session->set_flashdata('errors', 'Insufficient Stock data for '.$product_data['name']. ' ' . $category_name .' &#8212&#8212&#8212 '.'('.$unit_data['unit_name'].')');

                            $this->session->set_flashdata('input_data_array', $data);

                            redirect('/Product/add_office_stock', 'refresh');

                        }

                    }



                    // otherwise proceed the order

                    date_default_timezone_set("Asia/Karachi");

                    $date_time = date('Y-m-d H:i:s a');

                    $user_id = $this->session->userdata('id');

                    $data = array(

                        'date_time' => $date_time,

                        'user_id' => $user_id

                    );



                    $insert = $this->db->insert('office_stock_transfer', $data);

                    $office_stock_transfer_id = $this->db->insert_id();

                    if($insert)

                    {

                        $count_product = count($this->input->post('product'));

                        for($x = 0; $x < $count_product; $x++)

                        {

                            $product_id = explode("-",$this->input->post('product')[$x])[0];

                            $category_id = explode("-",$this->input->post('product')[$x])[1];

                            $stock_unit_id = explode("-",$this->input->post('product')[$x])[2];



                            if(empty($category_id)){

                                $category_id = 0;

                            }



                            $inputed_unit_id = $this->input->post('unit')[$x];

                            date_default_timezone_set("Asia/Karachi");

                            $items = array(

                                'category_id' => $category_id,

                                'product_id' => $product_id,

                                'unit_id' => $inputed_unit_id,

                                'factory_stock_unit_id' => $stock_unit_id,

                                'quantity' => $this->input->post('qty')[$x],

                                'office_stock_transfer_id' => $office_stock_transfer_id

                            );

                            // insert into office_items_stock and office_stock_transfer_items

                            $insert = $this->db->insert('office_stock_transfer_items', $items);

                            // if item already exist in stock

                            $item_exist = $this->Model_products->itemExistInOfficeStock($category_id, $product_id, $inputed_unit_id);

                            if($item_exist)

                            {

                                $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                                $quantity = $item_exist['quantity'];

                                $quantity += $this->input->post('qty')[$x] * $unit_value;



                                $stock = array(

                                    'quantity' => $quantity

                                );

                                $this->db->where('id', $item_exist['id']);

                                $this->db->update('office_items_stock', $stock);

                            }

                            else

                            {

                                // else if item does not exist in stock

                                $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                                $quantity = $this->input->post('qty')[$x] * $unit_value;

                                $stock = array(

                                    'category_id' => $category_id,

                                    'product_id' => $product_id,

                                    'unit_id' => $inputed_unit_id,

                                    'quantity' => $quantity

                                );

                                $this->db->insert('office_items_stock', $stock);

                            }

                            // reduce the amount from the factory stock

                            $stock_data = $this->Model_products->itemExistInStock($category_id, $product_id, $stock_unit_id);

                            if($stock_data && $insert)

                            {

                                $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                                $quantity = $stock_data['quantity'];

                                $quantity -= $this->input->post('qty')[$x] * $unit_value;

                                $stock = array(

                                    'quantity' => $quantity

                                );

                                $this->db->where('id', $stock_data['id']);

                                $this->db->update('items_stock', $stock);

                            }

                            else{

                                $this->session->set_flashdata('error', 'Not Successfully created. Contact System Configuration Manager!');

                                redirect('/Product/add_office_stock', 'refresh');

                            }

                        }//end for items

                        if($insert){

                            $this->session->set_flashdata('success', 'Added Successfully');

                            redirect('/Product/view_office_stock_transfered_items/'.$office_stock_transfer_id, 'refresh');

                        }

                        else

                        {

                            $this->session->set_flashdata('error', 'Not Successfully created. Contact System Configuration Manager');

                            redirect('/Product/add_office_stock', 'refresh');

                        }

                    }

                    else

                    {

                        $this->session->set_flashdata('error', 'Not Successfully created. Contact System Configuration Manager');

                        redirect('/Product/add_office_stock', 'refresh');

                    }

                }

            }

            else{

                $data['page_title'] = "Add Office Stock";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $data['input_data_array'] = '';



                $data['products'] = $this->Model_products->getFactoryStockData();

                $data['units_data'] = $this->Model_products->getUnitsData();

                $user_id = $this->session->userdata('id');

                $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

                $data['user_permission'] = unserialize($group_data['permission']);



                $this->load->view('products/add_office_stock', $data);

                $this->load->view('templates/footer');

            }

        }

    }



    public function view_office_stock_transfered_items($transfer_id)

    {

        if(!in_array('viewOfficeStockTransfer', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['office_stock_transfered_items'] = $this->Model_products->getOfficeStockTransferedData($transfer_id);

            if(!empty($data['office_stock_transfered_items'])){



                $data['page_title'] = "view office stock transfered items";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');



                $data['office_stock_transfered_items'] = $this->Model_products->getOfficeStockTransferedData($transfer_id);

                $data['products'] = $this->Model_products->getStockProductData();

                $data['units_data'] = $this->Model_products->getUnitsData();

                $user_id = $this->session->userdata('id');

                $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

                $data['user_permission'] = unserialize($group_data['permission']);



                $this->load->view('products/view_office_stock_transfered_items', $data);

                $this->load->view('templates/footer');

            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }



    }



    public function update_office_stock($id)

    {

        if(!in_array('updateSaleOrderE', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['transfered_data'] = $this->Model_products->getOfficeStockTransferedItems($id);

            if(!empty($data['transfered_data']))

            {

                $this->form_validation->set_rules('product[]', 'Product', 'trim|required');

                if($this->form_validation->run() == TRUE){



                    $user_id = $this->session->userdata('id');



                    // check if data in stock exist

                    $mainInputArray = array();

                    // fill this array with stock data

                    $stock_product_data = $this->Model_products->getFactoryStockData();

                    for($i = 0; $i < count($stock_product_data); $i++){

                        $temp = array();

                        $temp[0] = $stock_product_data[$i]['category_id'];

                        $temp[1] = $stock_product_data[$i]['product_id'];

                        $temp[2] = $stock_product_data[$i]['unit_id'];

                        $temp[3] = $this->Model_products->itemExistInStock($stock_product_data[$i]['category_id'], $stock_product_data[$i]['product_id'], $stock_product_data[$i]['unit_id'])['quantity'];

                        array_push($mainInputArray, $temp);

                    }

                    $count_product = count($this->input->post('product'));

                    for($x = 0; $x < $count_product; $x++)

                    {

                        $product_id = explode("-",$this->input->post('product')[$x])[0];

                        $category_id = explode("-",$this->input->post('product')[$x])[1];

                        $factory_unit_id = explode("-",$this->input->post('product')[$x])[2];



                        $inputed_unit_id = $this->input->post('unit')[$x];

                        if(empty($category_id)){

                            $category_id = 0;

                        }

                        for ($i = 0; $i < count($mainInputArray); $i++) {

                            if($mainInputArray[$i][0] == $category_id){

                                if($mainInputArray[$i][1] == $product_id)

                                {

                                    if($mainInputArray[$i][2] == $factory_unit_id)

                                    {

                                        $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                                        $mainInputArray[$i][3] -= ($this->input->post('qty')[$x] * $unit_value);

                                    }

                                }

                            }

                        }

                    }

                    $current_transfered_data = $this->Model_products->getOfficeStockTransferedItems($id);

                    foreach ($current_transfered_data as $key => $value) {

                        for ($i = 0; $i < count($mainInputArray); $i++) {

                            if($mainInputArray[$i][0] == $value['category_id']){

                                if($mainInputArray[$i][1] == $value['product_id'])

                                {

                                    if($mainInputArray[$i][2] == $value['factory_stock_unit_id'])

                                    {

                                        $unit_value = $this->Model_products->fetchUnitValueData($value['unit_id'])['unit_value'];

                                        $mainInputArray[$i][3] += $value['quantity'] * $unit_value;

                                    }

                                }

                            }

                        }

                    }

                    // now check if in new mainInput array any qty is negitive



                    foreach ($mainInputArray as $key => $value) {

                        if($value[3] < 0)

                        {

                            $category_name = '';

                            $product_data = $this->Model_products->getProductData($value[1]);

                            if($value[0])

                            {

                                $category_name = '&#8212 ' .$this->Model_category->getCategoryData($value[0])['name'];

                            }

                            $unit_data = $this->Model_products->getUnitsData($value[2]);



                            $this->session->set_flashdata('errors', 'Insufficient Stock data for '.$product_data['name']. ' ' . $category_name .' &#8212&#8212&#8212 '.'('.$unit_data['unit_name'].')');



                            redirect('/Product/update_office_stock/'.$id, 'refresh');

                        }

                    }



                    // otherwise proceed the order

                    $data = array(

                        'user_id' => $user_id

                    );



                    $this->db->where('id', $id);

                    $update = $this->db->update('office_stock_transfer', $data);

                    // remove the existing items

                    // add into factory stock and remove from office stock

                    $transfered_data = $this->Model_products->getOfficeStockTransferedItems($id);

                    foreach ($transfered_data as $key => $value)

                    {

                        $categ_id = 0;

                        if($value['category_id']){

                            $categ_id = $value['category_id'];

                        }

                        // factory stock data

                        $stock_data_row = $this->Model_products->itemExistInStock($categ_id, $value['product_id'], $value['factory_stock_unit_id']);

                        $unit_value = $this->Model_products->fetchUnitValueData($value['unit_id'])['unit_value'];

                        $data = array(

                            'quantity' => $stock_data_row['quantity'] + ($value['quantity'] * $unit_value)

                        );

                        // update factory stock

                        $this->db->where('id', $stock_data_row['id']);

                        $this->db->update('items_stock', $data);

                        // update office stock

                        $office_stock_data = $this->Model_products->itemExistInOfficeStock($categ_id, $value['product_id'], $value['unit_id']);

                        $unit_value = $this->Model_products->fetchUnitValueData($value['unit_id'])['unit_value'];

                        $data = array(

                            'quantity' => $office_stock_data['quantity'] - ($value['quantity'] * $unit_value)

                        );

                        $this->db->where('id', $office_stock_data['id']);

                        $this->db->update('office_items_stock', $data);

                        // delete items

                        $this->db->where('id', $value['id']);

                        $this->db->delete('office_stock_transfer_items');

                    }

                    //insert new items

                    $count_product = count($this->input->post('product'));

                    for($x = 0; $x < $count_product; $x++)

                    {

                        $product_id = explode("-",$this->input->post('product')[$x])[0];

                        $category_id = explode("-",$this->input->post('product')[$x])[1];

                        $unit_id = explode("-",$this->input->post('product')[$x])[2];



                        if(empty($category_id)){

                            $category_id = 0;

                        }



                        $inputed_unit_id = $this->input->post('unit')[$x];



                        $items = array(

                            'category_id' => $category_id,

                            'product_id' => $product_id,

                            'unit_id' => $inputed_unit_id,

                            'factory_stock_unit_id' => $unit_id,

                            'quantity' => $this->input->post('qty')[$x],

                            'office_stock_transfer_id' => $id

                        );

                        // insert into office_items_stock and office_stock_transfer_items

                        $insert = $this->db->insert('office_stock_transfer_items', $items);

                        // reduce the amount from the factory stock and add into office stock

                        $item_exist = $this->Model_products->itemExistInOfficeStock($category_id, $product_id, $inputed_unit_id);

                        if($item_exist)

                        {

                            $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                            $quantity = $item_exist['quantity'];

                            $quantity += $this->input->post('qty')[$x] * $unit_value;



                            $stock = array(

                                'quantity' => $quantity

                            );

                            $this->db->where('id', $item_exist['id']);

                            $this->db->update('office_items_stock', $stock);



                        }

                        else

                        {

                            // else if item does not exist in stock

                            $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                            $quantity = $this->input->post('qty')[$x] * $unit_value;

                            $stock = array(

                                'category_id' => $category_id,

                                'product_id' => $product_id,

                                'unit_id' => $inputed_unit_id,

                                'quantity' => $quantity

                            );

                            $this->db->insert('office_items_stock', $stock);

                        }

                        // reduce the amount from the factory stock

                        $stock_data = $this->Model_products->itemExistInStock($category_id, $product_id, $unit_id);

                        if($stock_data && $insert)

                        {

                            $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                            $quantity = $stock_data['quantity'];

                            $quantity -= $this->input->post('qty')[$x] * $unit_value;

                            $stock = array(

                                'quantity' => $quantity

                            );

                            $this->db->where('id', $stock_data['id']);

                            $this->db->update('items_stock', $stock);

                        }

                        else

                        {

                            $this->session->set_flashdata('error', 'Not Successfully created. Contact System Configuration Manager!');

                            redirect('/Product/update_office_stock/'.$id, 'refresh');

                        }

                    }//end for items

                    if($insert){

                        $this->session->set_flashdata('success', 'updated Successfully');

                        redirect('/Product/view_office_stock_transfered_items/'.$id, 'refresh');

                    }

                    else

                    {

                        $this->session->set_flashdata('error', 'Not Successfully created. Contact System Configuration Manager');

                        redirect('/Product/update_office_stock/'.$id, 'refresh');

                    }

                }

                else

                {

                    $data['page_title'] = "Edit Office Stock Transfer";

                    $this->load->view('templates/header', $data);

                    $this->load->view('templates/header_menu');

                    $this->load->view('templates/side_menubar');



                    $data['transfered_data'] = $this->Model_products->getOfficeStockTransferedItems($id);

                    $data['products'] = $this->Model_products->getStockProductData();

                    // store factory stock qtys for inserted items for showing it in s.qty

                    $x = 0;

                    $stock_data_array = array();

                    foreach ($data['transfered_data'] as $key => $value)

                    {



                        if($value['category_id']){

                            // Factory Stock

                            $stock_data_row = $this->Model_products->itemExistInStock($value['category_id'], $value['product_id'], $value['factory_stock_unit_id']);

                            $stock_data_array[$x++] = $stock_data_row;

                        }

                        else{

                            $stock_data_row = $this->Model_products->itemExistInStock(0, $value['product_id'], $value['factory_stock_unit_id']);

                            $stock_data_array[$x++] = $stock_data_row;

                        }

                    }

                    $data['stock_data_array'] = $stock_data_array;

                    $data['units_data'] = $this->Model_products->getUnitsData();

                    $user_id = $this->session->userdata('id');

                    $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

                    $data['user_permission'] = unserialize($group_data['permission']);

                    $this->load->view('products/update_office_stock', $data);

                    $this->load->view('templates/footer');

                }

            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }

    }



    public function delete_office_stock()

    {

        $response = array();

        $transfer_id = $this->input->post('id');

        if($transfer_id)

        {

            // check if office stock has that much amount to delete

            $transfered_data = $this->Model_products->getOfficeStockTransferedItems($transfer_id);

            $office_stock_items = $this->Model_products->getOfficeStockData();

            foreach ($transfered_data as $key => $value)

            {

                foreach ($office_stock_items as $k => $v)

                {

                    if($value['category_id'] == $v['category_id'] && $value['product_id'] == $v['product_id'] && $value['unit_id'] == $v['unit_id'])

                    {

                        if($v['quantity'] < $value['quantity'])

                        {

                            // error

                            $product_data = $this->Model_products->getProductData($value['product_id']);

                            $category_name = '';

                            if($value['category_id']){

                                $category_data = $this->Model_category->getCategoryData($value['category_id']);

                                if(!empty($category_data)){

                                    $category_name = '&#8212 '.$category_data['name'];

                                }

                            }

                            $unit_data = $this->Model_products->getUnitsData($value['unit_id']);

                            $response['success'] = false;

                            $response['location'] = "";

                            $this->session->set_flashdata('errors', 'Insufficient Stock data for '.$product_data['name']. ' ' . $category_name .' &#8212&#8212&#8212 '.'('.$unit_data['unit_name'].')');

                            echo json_encode($response);

                            return;

                        }

                    }

                }

            }

            // remove from office stock and add into factory stock

            // remove stock transfer and its items

            $this->db->where('id', $transfer_id);

            $delete = $this->db->delete('office_stock_transfer');

            $transfered_data = $this->Model_products->getOfficeStockTransferedItems($transfer_id);

            foreach ($transfered_data as $key => $value)

            {

                $category_id = 0;

                if($value['category_id']){

                    $category_id = $value['category_id'];

                }

                $stock_data_row = $this->Model_products->itemExistInStock($category_id, $value['product_id'], $value['factory_stock_unit_id']);

                $unit_value = $this->Model_products->fetchUnitValueData($value['unit_id'])['unit_value'];

                $data = array(

                    'quantity' => $stock_data_row['quantity'] + ($value['quantity'] * $unit_value)

                );

                // update factory stock

                $this->db->where('id', $stock_data_row['id']);

                $this->db->update('items_stock', $data);

                // update office stock

                $office_stock_data = $this->Model_products->itemExistInOfficeStock($category_id, $value['product_id'], $value['unit_id']);

                $unit_value = $this->Model_products->fetchUnitValueData($value['unit_id'])['unit_value'];

                $data = array(

                    'quantity' => $office_stock_data['quantity'] - ( $value['quantity'] * $unit_value )

                );

                $this->db->where('id', $office_stock_data['id']);

                $this->db->update('office_items_stock', $data);

                // delete items

                $this->db->where('id', $value['id']);

                $delete = $this->db->delete('office_stock_transfer_items');

            }

            if($delete) {

                $response['success'] = true;

                $response['location'] = "manage_office_stock";

                $this->session->set_flashdata('success', 'deleted Successfully');

                $response['messages'] = "Successfully removed";

            }

            else {

                $response['success'] = false;

                $response['location'] = "";

                $response['messages'] = "Error in the database while removing the product information";

            }

        }

        else {

            $response['success'] = false;

            $response['location'] = "";

            $response['messages'] = "Office Stock Transfer Id Does not exist. Refersh the page again!!";

        }

        echo json_encode($response);

    }



    public function getTableFactoryStockData()

    {

        $data['products'] = $this->Model_products->getStockProductData();

        $data['units_data'] = $this->Model_products->getUnitsData();

        $data['unit_data_values'] = $this->Model_products->getUnitValuesData();

        $response['data'] = $data;

        $response['success'] = true;

        echo json_encode($response);

    }



    public function print_office_stock_transfered_order($transfer_id)

    {

        if(!in_array('printOfficeStockTransfer', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['transfered_data'] = $this->Model_products->getOfficeStockTransferedItems($transfer_id);

            if(!empty($data['transfered_data'])) {



                $transfered_data = $this->Model_products->getOfficeStockTransferData($transfer_id);

                $transfered_data_items = $this->Model_products->getOfficeStockTransferedItems($transfer_id);

                $products = $this->Model_products->getStockProductData();

                $user_id = $this->session->userdata('id');

                $user_data = $this->Model_users->getUserData($user_id);

                $order_date = $transfered_data['date_time'];



                $html = '<!DOCTYPE html>

                <html lang="en">

                <head>

                <meta charset="UTF-8">

                <meta name="viewport" content="width=device-width, initial-scale=1.0">

                <meta http-equiv="X-UA-Compatible" content="ie=edge">

                <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

                <style>

                .invoice-title h2, .invoice-title h3 {

                    display: inline-block;

                }



                </style>



                <title>Office Stock Transfer Print</title>

                </head>

                <body onload="window.print();">

                <div class="container">

                <div class="row">

                <div class="col-xs-12">

                <div class="invoice-title text-center">

                <h3>Office Stock Transfer Order</h3>

                </div>

                <hr>

                <div class="row">

                <div class="col-xs-6">

                <address style="text-transform:capitalize">

                <strong>Created By:</strong><br>

                '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

                <strong>Company:</strong><br>

                TBM Automobile Private Ltd<br>

                </address>

                </div>

                <div class="col-xs-6 text-right">

                <address>

                <strong>Transfer Date:</strong><br>

                '.date('d/m/Y', strtotime($order_date)).'<br>



                </address>

                </div>

                </div>

                </div>

                </div>



                <div class="row">

                <div class="col-md-12">

                <div class="table-responsive">

                <table class="table table-condensed table-bordered">

                <thead>

                <tr>

                <td><strong>Factory Stock Items</strong></td>

                <td><strong>Transered Quantity</strong></td>

                </tr>

                </thead>

                <tbody>';

                foreach ($transfered_data_items as $key => $value)

                {

                    $td_value = '';

                    foreach ($products as $k => $v) {

                        if($value['product_id'] == $v['product_id']){

                            if($value['category_id'] == $v['category_id']){

                                $unit_name = $this->Model_products->getUnitsData($v['unit_id'])['unit_name'];

                                if($v['category_name'] == null)

                                {

                                    $td_value = $v['product_name'] . ' &nbsp;&nbsp;&nbsp;&nbsp;( '.$unit_name.' )';

                                }

                                else

                                {

                                    $category_name = ' &#8212 ' . $v['category_name'];

                                    $td_value = $v['product_name'] .' '.$category_name. '&nbsp;&nbsp;&nbsp;&nbsp;( '.$unit_name.' )';

                                }

                            }

                        }

                    }



                    $html .= '

                    <tr>

                        <td>'.$td_value.'</td>

                        <td>'.$value['quantity'].'</td>



                    </tr>';

                }



                $html .='



                </tbody>



                </table>

                </div>

                </div>

                </div>



                </div>

                </body>



                <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

                <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



                </html>';

                echo $html;

            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }

    }



    public function print_stock_items()

    {

        if(!in_array('printFactoryStock', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $result = array();

            date_default_timezone_set("Asia/Karachi");

            $print_date = date('d/m/Y');

            $user_id = $this->session->userdata('id');

            $user_data = $this->Model_users->getUserData($user_id);

            $data = $this->Model_products->getStockWithUnitsData();



            $sorted_data = array();



            if(!empty($data)){



              $html = '<!DOCTYPE html>

              <html lang="en">

              <head>

              <meta charset="UTF-8">

              <meta name="viewport" content="width=device-width, initial-scale=1.0">

              <meta http-equiv="X-UA-Compatible" content="ie=edge">

              <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

              <style>

              .invoice-title h2, .invoice-title h3 {

                display: inline-block;

            }



            .table > tbody > tr > .no-line {

                border-top: none;

            }



            .table > thead > tr > .no-line {

                border-bottom: none;

            }



            .table > tbody > tr > .thick-line {

                border-top: 2px solid;

            }

            </style>



            <title>TBM - Factory Stock Items Print</title>

            </head>

            <body onload="window.print();">

            <div class="container">

            <div class="row">

            <div class="col-xs-12">

            <div class="invoice-title text-center">

            <h3>TBM Automobile Private Ltd</h3>

            </div>

            <hr>

            <div class="row">

            <div class="col-xs-6">

            <address style="text-transform:capitalize">

            <strong>Printed By:</strong><br>

            '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

            </address>

            </div>

            <div class="col-xs-6 text-right">

            <address>

            <strong>Print Date:</strong><br>

            '.date("d-m-Y").'<br>



            </address>

            </div>

            </div>

            </div>

            </div>



            <div class="row">

            <div class="col-md-12">



            <div class="table-responsive">

            <table class="table table-condensed table-bordered">

            <thead>

            <tr>

            <th style="width:10%"><strong>#</strong></th>

            <th><strong>Category Name</strong></th>

            <th><strong>Item Name</strong></th>

            <th><strong>Unit</strong></th>

            <th><strong>Quantity</strong></th>

            </tr>

            </thead>

            <tbody>';

            $counter = 1;

            foreach ($data as $key => $value) {

                $product_data = $this->Model_products->getAllProductData($value['product_id']);

                $product_name = $product_data['name'];

                $category_name = '';

                if($value['category_id'])

                {

                    $category_data = $this->Model_category->getAllCategoryData($value['category_id']);

                    if(!empty($category_data)){

                        $category_name = $category_data['name'];

                    }

                }

                else

                {

                    $category_name = 'Nill';

                }

                $unit_name = $this->Model_products->getAllUnitsData($value['unit_id'])['unit_name'];

                $unit_value = $this->Model_products->fetchUnitValueData($value['unit_id'])['unit_value'];

                $unit_quantity = intval($value['sum_qty'] / $unit_value);

                $non_unit_qty = $value['sum_qty'] % $unit_value;

                $points_unit_qty = 0;

                if($value['sum_qty'] - $unit_quantity < 1 && $value['sum_qty'] - $unit_quantity > 0){

                    $points_unit_qty = $value['sum_qty'] - $unit_quantity;

                    $non_unit_qty += $points_unit_qty;

                }



                $qty = '';

                $stock_order_level = $this->Model_products->getFactoryStockOrderLevelData()['value'];

                if($unit_quantity < $stock_order_level)

                {

                    if($non_unit_qty > 0){

                        $qty = $unit_quantity.' — ( '. number_format($non_unit_qty, 3) .' - Other )'.' &nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-warning">Low</span>';

                    }else{

                        $qty = $unit_quantity.' &nbsp;&nbsp;&nbsp;&nbsp; <span class="label label-warning">Low</span>';

                    }

                }

                else

                {

                    if($non_unit_qty > 0){

                        $qty = $unit_quantity.' — ( '. number_format($non_unit_qty, 3) .' - Other )';

                    }

                    else{

                        $qty = $unit_quantity;

                    }

                }



                $html .= '<tr>

                <td>'.$counter++.'</td>

                <td style="text-transform:capitalize">'.$category_name.'</td>

                <td style="text-transform:capitalize">'.$product_name.'</td>

                <td style="text-transform:capitalize">'.$unit_name.'</td>

                <td>'.$qty.'</td>

                </tr>';

            }

            $html .='



            </tbody>



            </table>

            </div>

            </div>

            </div>



            </div>

            </body>



            <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

            <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



            </html>';

            echo $html;

        }

        else{

            $html = '<!-- Main content -->

            <!DOCTYPE html>

            <html>

            <head>

            <meta charset="utf-8">

            <meta http-equiv="X-UA-Compatible" content="IE=edge">

            <title>TBM Automobile Private Ltd | Stock Items Print</title>

            <!-- Tell the browser to be responsive to screen width -->

            <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

            <!-- Bootstrap 3.3.7 -->

            <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">

            <!-- Font Awesome -->

            <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">

            <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">

            </head>

            <body onload="window.print();">



            <div class="wrapper">

            <section class="invoice">

            <!-- title row -->

            <div class="row">

            <div class="col-xs-12">

            <h2 class="page-header">

            TBM Automobile Private Ltd

            </h2>

            </div>

            <!-- /.col -->

            </div>

            <!-- Table row -->

            <div class="row">

            <div class="col-xs-12 table-responsive">

            <table class="table table-striped">

            <thead>

            <tr>

            <th style="width:10%"><strong>#</strong></th>

            <th style="width:30%"><strong>Category Name</strong></th>

            <th style="width:30%"><strong>Item Name</strong></th>

            <th style="width:30%"><strong>Quantity</strong></th>

            </tr>

            </thead>

            <tbody>';



            $html .= '<tr>



            </tr>';



            $html .= '</tbody>

            </table>

            </div>

            <!-- /.col -->

            </div>

            <!-- /.row -->

            </section>

            <!-- /.content -->

            </div>

            </body>

            </html>';

            echo $html;

        }

    }

}



public function print_office_stock_items()

{

    if(!in_array('printOfficeStock', $this->permission)) {

        $data['page_title'] = "No Permission";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');

        $this->load->view('errors/forbidden_access');

    }

    else

    {

        $result = array();

        date_default_timezone_set("Asia/Karachi");

        $print_date = date('d/m/Y');

        $user_id = $this->session->userdata('id');

        $user_data = $this->Model_users->getUserData($user_id);

        $data = $this->Model_products->getOfficeStockWithUnitsData();





        if(!empty($data)){



          $html = '<!DOCTYPE html>

          <html lang="en">

          <head>

          <meta charset="UTF-8">

          <meta name="viewport" content="width=device-width, initial-scale=1.0">

          <meta http-equiv="X-UA-Compatible" content="ie=edge">

          <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

          <style>

          .invoice-title h2, .invoice-title h3 {

            display: inline-block;

        }



        .table > tbody > tr > .no-line {

            border-top: none;

        }



        .table > thead > tr > .no-line {

            border-bottom: none;

        }



        .table > tbody > tr > .thick-line {

            border-top: 2px solid;

        }

        </style>



        <title>TBM - Office Stock Items Print</title>

        </head>

        <body onload="window.print();">

        <div class="container">

        <div class="row">

        <div class="col-xs-12">

        <div class="invoice-title text-center">

        <h3>TBM Automobile Private Ltd</h3>

        </div>

        <hr>

        <div class="row">

        <div class="col-xs-6">

        <address style="text-transform:capitalize">

        <strong>Printed By:</strong><br>

        '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

        </address>

        </div>

        <div class="col-xs-6 text-right">

        <address>

        <strong>Print Date:</strong><br>

        '.date("d-m-Y").'<br>



        </address>

        </div>

        </div>

        </div>

        </div>



        <div class="row">

        <div class="col-md-12">



        <div class="table-responsive">

        <table class="table table-condensed table-bordered">

        <thead>

        <tr>

        <th><strong>#</strong></th>

        <th><strong>Category Name</strong></th>

        <th><strong>Item Name</strong></th>

        <th><strong>Unit Name</strong></th>

        <th><strong>Quantity</strong></th>

        </tr>

        </thead>

        <tbody>';

        $counter = 1;

        foreach ($data as $key => $value) {

            $product_data = $this->Model_products->getAllProductData($value['product_id']);

            $product_name = $product_data['name'];

            $category_name = '';

            if($value['category_id'])

            {

                $category_data = $this->Model_category->getAllCategoryData($value['category_id']);

                if(!empty($category_data)){

                    $category_name = $category_data['name'];

                }

            }

            else

            {

                $category_name = 'Nill';

            }



            $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];

            $unit_value = $this->Model_products->fetchUnitValueData($value['unit_id'])['unit_value'];



            $unit_quantity = intval($value['sum_qty'] / $unit_value);

            $non_unit_qty = $value['sum_qty'] % $unit_value;

            $points_unit_qty = 0;

            if($value['sum_qty'] - $unit_quantity < 1 && $value['sum_qty'] - $unit_quantity > 0){

                $points_unit_qty = $value['sum_qty'] - $unit_quantity;

                $non_unit_qty += $points_unit_qty;

            }



            $qty = '';

            $stock_order_level = $this->Model_products->getFactoryStockOrderLevelData()['value'];

            if($unit_quantity <= $stock_order_level)

            {

                if($non_unit_qty > 0){

                    $qty = $unit_quantity.' — ( '. $non_unit_qty .' - Other )'.' <span class="label label-warning">Low</span>';

                }else{

                    $qty = $unit_quantity.' <span class="label label-warning">Low</span>';

                }

            }

            else

            {

                if($non_unit_qty > 0){

                    $qty = $unit_quantity.' — ( '. $non_unit_qty .' - Other )';

                }

                else{

                    $qty = $unit_quantity;

                }

            }



            $html .= '<tr>

            <td>'.$counter++.'</td>

            <td style="text-transform:capitalize">'.$category_name.'</td>

            <td style="text-transform:capitalize">'.$product_name.'</td>

            <td style="text-transform:capitalize">'.$unit_name.'</td>

            <td>'.$qty.'</td>

            </tr>';

        }

        $html .='



        </tbody>



        </table>

        </div>

        </div>

        </div>



        </div>

        </body>



        <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

        <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



        </html>';

        echo $html;

    }

    else{

        $html = '<!-- Main content -->

        <!DOCTYPE html>

        <html>

        <head>

        <meta charset="utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>TBM Automobile Private Ltd | Stock Items Print</title>

        <!-- Tell the browser to be responsive to screen width -->

        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- Bootstrap 3.3.7 -->

        <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">

        <!-- Font Awesome -->

        <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">

        <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">

        </head>

        <body onload="window.print();">



        <div class="wrapper">

        <section class="invoice">

        <!-- title row -->

        <div class="row">

        <div class="col-xs-12">

        <h2 class="page-header">

        TBM Automobile Private Ltd

        </h2>

        </div>

        <!-- /.col -->

        </div>

        <!-- Table row -->

        <div class="row">

        <div class="col-xs-12 table-responsive">

        <table class="table table-striped">

        <thead>

        <tr>

        <th style="width:10%"><strong>#</strong></th>

        <th style="width:30%"><strong>Category Name</strong></th>

        <th style="width:30%"><strong>Item Name</strong></th>

        <th style="width:30%"><strong>Quantity</strong></th>

        </tr>

        </thead>

        <tbody>';



        $html .= '<tr>



        </tr>';



        $html .= '</tbody>

        </table>

        </div>

        <!-- /.col -->

        </div>

        <!-- /.row -->

        </section>

        <!-- /.content -->

        </div>

        </body>

        </html>';

        echo $html;

    }

}

}



public function print_office_stock()

{

    if(!in_array('printOfficeStockTransfer', $this->permission)) {

        $data['page_title'] = "No Permission";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');

        $this->load->view('errors/forbidden_access');

    }

    else

    {

        $result = array();

        date_default_timezone_set("Asia/Karachi");

        $print_date = date('d/m/Y');

        $user_id = $this->session->userdata('id');

        $user_data = $this->Model_users->getUserData($user_id);

        $data = $this->Model_products->getOfficeStockTransferData();





        if(!empty($data)){



          $html = '<!DOCTYPE html>

          <html lang="en">

          <head>

          <meta charset="UTF-8">

          <meta name="viewport" content="width=device-width, initial-scale=1.0">

          <meta http-equiv="X-UA-Compatible" content="ie=edge">

          <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

          <style>

          .invoice-title h2, .invoice-title h3 {

            display: inline-block;

        }



        .table > tbody > tr > .no-line {

            border-top: none;

        }



        .table > thead > tr > .no-line {

            border-bottom: none;

        }



        .table > tbody > tr > .thick-line {

            border-top: 2px solid;

        }

        </style>



        <title>TBM - Office Stock Transfers Print</title>

        </head>

        <body onload="window.print();">

        <div class="container">

        <div class="row">

        <div class="col-xs-12">

        <div class="invoice-title text-center">

        <h3>TBM Automobile Private Ltd</h3>

        </div>

        <hr>

        <div class="row">

        <div class="col-xs-6">

        <address style="text-transform:capitalize">

        <strong>Printed By:</strong><br>

        '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

        </address>

        </div>

        <div class="col-xs-6 text-right">

        <address>

        <strong>Print Date:</strong><br>

        '.date("d-m-Y").'<br>



        </address>

        </div>

        </div>

        </div>

        </div>



        <div class="row">

        <div class="col-md-12">



        <div class="table-responsive">

        <table class="table table-condensed table-bordered">

        <thead>

        <tr>

        <th style="width:10%"><strong>#</strong></th>

        <th style="width:30%"><strong>Transfer Date</strong></th>

        <th><strong>Total Items</strong></th>

        </tr>

        </thead>

        <tbody>';

        $counter = 1;

        foreach ($data as $key => $value) {

            $count_total_item = $this->Model_products->countOfficeStockTransferItems($value['id']);



            $date = date('d-m-Y', strtotime($value['date_time']));

            $time = date('h:i a', strtotime($value['date_time']));

            $date_time = $date . ' ' . $time;



            $html .= '<tr>

            <td>'.$counter++.'</td>

            <td style="text-transform:capitalize">'.$date_time.'</td>

            <td style="text-transform:capitalize">'.$count_total_item.'</td>

            </tr>';

        }

        $html .='



        </tbody>



        </table>

        </div>

        </div>

        </div>



        </div>

        </body>



        <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

        <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



        </html>';

        echo $html;

    }

    else{

        $html = '<!-- Main content -->

        <!DOCTYPE html>

        <html>

        <head>

        <meta charset="utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>TBM Automobile Private Ltd | Stock Items Print</title>

        <!-- Tell the browser to be responsive to screen width -->

        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- Bootstrap 3.3.7 -->

        <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">

        <!-- Font Awesome -->

        <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">

        <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">

        </head>

        <body onload="window.print();">



        <div class="wrapper">

        <section class="invoice">

        <!-- title row -->

        <div class="row">

        <div class="col-xs-12">

        <h2 class="page-header">

        TBM Automobile Private Ltd

        </h2>

        </div>

        <!-- /.col -->

        </div>

        <!-- Table row -->

        <div class="row">

        <div class="col-xs-12 table-responsive">

        <table class="table table-striped">

        <thead>

        <tr>

        <th style="width:10%"><strong>#</strong></th>

        <th style="width:30%"><strong>Category Name</strong></th>

        <th style="width:30%"><strong>Item Name</strong></th>

        <th style="width:30%"><strong>Quantity</strong></th>

        </tr>

        </thead>

        <tbody>';



        $html .= '<tr>



        </tr>';



        $html .= '</tbody>

        </table>

        </div>

        <!-- /.col -->

        </div>

        <!-- /.row -->

        </section>

        <!-- /.content -->

        </div>

        </body>

        </html>';

        echo $html;

    }

}

}



public function print_invoice($order_id)

{

    if(!in_array('printPurchasing', $this->permission)) {

        $data['page_title'] = "No Permission";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');

        $this->load->view('errors/forbidden_access');

    }

    else

    {

        $purchase_order_data = $this->Model_products->getPurchaseOrdersData($order_id);

        $purchase_items_data = $this->Model_products->getPurchaseItemsData($order_id);

        if(!empty($purchase_order_data) && !empty($purchase_items_data)){

            $purchase_order_data = $this->Model_products->getPurchaseOrdersData($order_id);

            $purchase_items_data = $this->Model_products->getPurchaseItemsData($order_id);

            $vendor_id = $purchase_order_data['vendor_id'];

            $vendor_data = $this->Model_products->getSupplierData($vendor_id);



            $vendor_products = $this->Model_products->getVendorProductsData($vendor_id);

            $units_data = $this->Model_products->getAllUnitsData();

            $unit_data_values = $this->Model_products->getUnitValuesData();

            $loan_data = $this->Model_loan->getVendorRemainingLoanData($vendor_id);

            $loan_deductions = $this->Model_loan->getLoanDeductions($order_id);

            $loan_deduction = 0;

            if(!empty($loan_deductions))

            {

                $loan_deduction = $loan_deductions['deduction_amount'];

            }

            $user_data = $this->Model_users->getUserData($purchase_order_data['user_id']);

            $purchase_return_data = $this->Model_products->fetchPurchaseReturnsData($order_id);

            $returns_amount = 0;

            if($this->Model_products->getPurchaseReturnsAmount($order_id))

            {

                $returns_amount = $this->Model_products->getPurchaseReturnsAmount($order_id)['returns_amount'];

            }

            $company_info = $this->Model_company->getCompanyData(1);



            $order_date = $purchase_order_data['datetime_created'];



            $total_paid_amount = $purchase_order_data['total_paid'];

            $temp = '!@#$';

            $payment_method_array = explode($temp, $purchase_order_data['payment_method']);

            $payment_date_array = explode($temp, $purchase_order_data['payment_date']);

            $paid_array = explode($temp, $purchase_order_data['paid']);

            $payment_note_array = explode($temp, $purchase_order_data['payment_note']);

            $x = 0;



            $html = '

                <!DOCTYPE html>

                <html lang="en">

                    <head>

                        <meta charset="UTF-8">

                        <meta name="viewport" content="width=device-width, initial-scale=1.0">

                        <meta http-equiv="X-UA-Compatible" content="ie=edge">

                        <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

                        <style>

                          .invoice-title h2, .invoice-title h3 {

                            display: inline-block;

                          }

                        </style>

                        <title>Print Order Info</title>

                    </head>

                    <body onload="window.print();">

                        <div class="container">

                          <div class="row">

                            <div class="col-xs-12">

                              <div class="invoice-title" style="margin-top:40px; text-align: center">

                                <h2>TBM Engineering</h2>

                                <h4 class="pull-right"># '.$purchase_order_data['bill_no'].'</h4>

                              </div>

                              <hr>

                              <div class="row">

                                <div class="col-xs-6">

                                  <address style="text-transform:capitalize">

                                    <strong>Created By:</strong><br>

                                    '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

                                    <strong>Vendor Name:</strong><br>

                                    '.$vendor_data['first_name'].' '.$vendor_data['last_name'].' <br>

                                  </address>

                                </div>

                              </div>

                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-bordered" id="manageTable">

                                <thead>

                                    <tr>

                                        <th><strong>#</strong></th>

                                          <th><strong>Payment Method</strong></th>

                                          <th><strong>Paid Amount</strong></th>

                                          <th><strong>Payment Note</strong></th>

                                          <th><strong>Payment Date</strong></th>

                                    </tr>

                                </thead>

                                <tbody>';

                                $count_payment = 1;

                                foreach ($payment_method_array as $key => $value)

                                {

                                    if(!empty($payment_method_array[$x]))

                                    {

                                        $payment_method = $payment_method_array[$x];

                                        $paid_amount = $paid_array[$x];

                                        $payment_note = $payment_note_array[$x];

                                        $payment_date = $payment_date_array[$x];

                                        $x++;

                                        $html .='

                                        <tr>

                                          <td class="no-line">'.$x.'</td>

                                          <td class="no-line">'.$payment_method.'</td>

                                          <td class="no-line">'.floatval($paid_amount).'</td>

                                          <td style="white-space:pre-wrap; word-wrap:break-word" class="no-line">'.$payment_note.'</td>

                                          <td class="no-line">'.$payment_date.'</td>

                                        </tr>';

                                    }

                                    else

                                    {

                                        $html .='

                                        <tr>

                                          <td class="no-line">0</td>

                                          <td class="no-line">Nill</td>

                                          <td class="no-line">Nill</td>

                                          <td class="no-line">Nill</td>

                                          <td class="no-line">Nill</td>

                                        </tr>';

                                    }

                                }

                            $html .='

                                </tbody>

                            </table>

                            <div class="row">

                                <div class="col-md-12">

                                    <b>Total Paid Amount: '.floatval($total_paid_amount).'</b>

                                </div>

                            </div>

                        </div>';



                        if(isset($loan_data) && !empty($loan_data)){

                            $html .= '

                                <div class="table-responsive" style="margin-top: 20px;">

                                    <table class="table table-bordered">

                                        <tr>

                                            <th>Remaining Loan Amount</th>

                                            <th>Loan Deduction</th>

                                        </tr>

                                        <tbody>

                                            <tr>

                                                <td style="background-color: #e6e6e6;">'.$loan_data['remaining_amount'].'</td>

                                                <td style="background-color: #e6e6e6;">'.$loan_deduction.'</td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            ';

                        }



                $html .= '<div class="row">

                            <div class="col-md-12">

                                <div class="table-responsive">

                                    <table class="table table-bordered">

                                        <thead>

                                            <tr>

                                              <th><strong>#</strong></th>

                                              <th><strong>Item</strong></th>

                                              <th><strong>Unit</strong></th>

                                              <th><strong>Price</strong></th>

                                              <th><strong>Quantity</strong></th>

                                              <th><strong>Total Price</strong></th>';

                                              if(!empty($purchase_return_data)){

                                                $html .= '<th style="width:12%"><strong>Return Pieces</strong></th>';

                                                $html .= '<th style="width:12%"><strong>Amount</strong></th>';

                                              }

                                              $html .= '



                                            </tr>

                                        </thead>

                                        <br>

                                        <tbody>';

                                        $total_retun_amount = 0;

                                        $counter = 1;

                                        foreach ($purchase_items_data as $k => $v) {



                                          $product_data = $this->Model_products->getAllProductData($v['product_id']);

                                          $category_data = $this->Model_products->getAllProductCategoryData($v['product_id'], $v['category_id']);

                                          $product_price_data = $this->Model_products->getProductPrices($v['category_id'], $v['product_id'], $v['vendor_id'], $v['unit_id']);



                                          $unit_name = '';

                                          $units_data = $this->Model_products->getAllUnitsData();

                                          foreach ($units_data as $key => $value)

                                          {

                                            $unit_id = $v['unit_id'];

                                            $unit_data_values = $this->Model_products->getUnitValuesData();

                                            if($value['id'] == $unit_id)

                                            {

                                              foreach ($unit_data_values as $key_2 => $value_2)

                                              {

                                                if($value['id'] == $value_2['unit_id'])

                                                {

                                                  $unit_name = $value['unit_name'];

                                                  break;

                                                }

                                              }

                                            }

                                          }

                                          $item_name = '';

                                          if(!empty($category_data) && $category_data['category_name'])

                                          {

                                            $item_name = $product_data['name']. ' - ' .$category_data['category_name'];

                                          }

                                          else

                                          {

                                            $item_name = $product_data['name'];

                                          }

                                          $return_qty = 0;

                                          $return_amount = 0;

                                          foreach ($purchase_return_data as $returnKey => $returnValue) {

                                            if($returnValue['product_id'] == $v['product_id'] && $returnValue['category_id'] == $v['category_id'] && $returnValue['unit_id'] == $v['unit_id']){

                                              $return_qty = $returnValue['qty'];

                                              $return_amount = $returnValue['amount'];

                                              $total_retun_amount += $return_amount;

                                              break;

                                            }

                                            else{

                                              $return_qty = 0;

                                              $return_amount = 0;

                                            }

                                          }

                                            $html .= '

                                            <tr>

                                                <td>'.$counter++.'</td>

                                                <td>'.$item_name.'</td>

                                                <td>'.$unit_name.'</td>

                                                <td class="text-center">'.floatval($product_price_data['price']).'</td>

                                                <td>'.$v['qty'].'</td>

                                                <td>'.floatval(($product_price_data['price'] * $v['qty'])).'</td>';

                                                if(!empty($purchase_return_data)){

                                                  $html .= '<td>'. $return_qty.'</td>

                                                  <td>'.floatval($return_amount).'</td>';

                                                }

                                            $html .= '</tr>';

                                        }

                        $html.='    <table>

                                </div>

                            </div>

                        </div>





                        <div class="">

                           <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Gross Amount</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['gross_amount']).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Returns Amount</button><div class="input-group" style="width: 20%;float: right" ><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: nonee; border-color: transparent transparent black transparent; box-shadow: none;" class="form-control" value="'.floatval($returns_amount).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >New Gross Amount</button><div class="input-group" style="width: 20%;float: right" ><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['gross_amount'] - $returns_amount).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Discount</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['discount']).'"></div>

                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Sales tax ('.floatval($purchase_order_data['sales_tax']).'%)</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['sales_tax_value']).'"></div>

                               <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Sales tax total</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['sales_tax_value_total']).'"></div>

                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >W.H.T ('.floatval($purchase_order_data['w_h_t']).'%)</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['w_h_t_value']).'"></div>

                             <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >W.H.T total</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['w_h_t_value_total']).'"></div>

                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Loan Deduction</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($loan_deduction).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Freight</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: nonee; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['loading_or_affair']).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Fine Deduction</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: nonee; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['fine_deduction']).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Other Deduction</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: nonee; border-color: transparent transparent black transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['other_deduction']).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Net Amount</button><div class="input-group" style="width: 20%;float: right" ><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['net_amount'] - $returns_amount).'"></div>





                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Opening Balance</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: nonee; border-color: transparent transparent black transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['opening_balance']).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Remaining</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['opening_balance'] + ($purchase_order_data['net_amount'] - $returns_amount)).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Total Paid</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: nonee; border-color: transparent transparent black transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['total_paid']).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Total</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($purchase_order_data['opening_balance'] + ($purchase_order_data['net_amount'] - $returns_amount) - $purchase_order_data['total_paid']).'"></div>

                            <label><br>Remarks: </label>'.$purchase_order_data['remarks'].'

                            <br>

                            <div style="width: 50%; float: right;"><label style="width: 30%; float: left">Seller Signature:</label><div style="width: 70%; float: right"><br><hr></div></div>

                            <div style="width: 50%; float: right"><label style="width: 30%; float: left">Customer Signature:</label><div style="width: 70%; float: right"><br><hr></div></div>

                            <div style="width: 30%; float: left"><label style="width: 30%; float: left">Date:</label><div style="width: 70%; float: right">'.date("Y/m/d", strtotime($purchase_order_data['datetime_created'])).'</div></div>

                        </div>

                    <br>

                </body>

            </html>';

            echo $html;

        }

        else{

            $data['page_title'] = "404 - Not Found";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/404_not_found');

        }

    }

}





public function print_purchase_orders()

{

    if(!in_array('printPurchasing', $this->permission)) {

        $data['page_title'] = "No Permission";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');

        $this->load->view('errors/forbidden_access');

    }

    else

    {



        $data = $this->Model_products->getPurchaseOrdersData();

        $user_id = $this->session->userdata('id');

        $user_data = $this->Model_users->getUserData($user_id);

        date_default_timezone_set("Asia/Karachi");

        $print_date = date('d-m-Y');



        $html = '<!DOCTYPE html>

        <html lang="en">

        <head>

        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

        <style>

        .invoice-title h2, .invoice-title h3 {

            display: inline-block;

        }



        .table > tbody > tr > .no-line {

            border-top: none;

        }



        .table > thead > tr > .no-line {

            border-bottom: none;

        }



        .table > tbody > tr > .thick-line {

            border-top: 2px solid;

        }

        </style>



        <title>TBM - Purchase Orders Print</title>

        </head>

        <body onload="window.print();">

        <div class="container">

        <div class="row">

        <div class="col-xs-12">

        <div class="invoice-title text-center">

        <h3>TBM Automobile Private Ltd</h3>

        </div>

        <hr>

        <div class="row">

        <div class="col-xs-6">

        <address style="text-transform:capitalize">

        <strong>Printed By:</strong><br>

        '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

        </address>

        </div>

        <div class="col-xs-6 text-right">

        <address>

        <strong>Print Date:</strong><br>

        '.date("d-m-Y").'<br>



        </address>

        </div>

        </div>

        </div>

        </div>



        <div class="row">

        <div class="col-md-12">



        <div class="table-responsive">

        <table style="width: 100%;" class="table table-condensed table-bordered">

        <thead>

        <tr>

        <td><strong>#</strong></td>

        <td><strong>DateTime</strong></td>

        <td><strong>Bill no</strong></td>

        <td><strong>Vendor</strong></td>

        <td><strong>Created By</strong></td>

        <td><strong>Total Bill Amount</strong></td>

        <td><strong>Bill Remaining Amount</strong></td>

        </tr>

        </thead>

        <tbody>';

        $counter = 1;

        foreach ($data as $key => $value) {

            $count_total_item = $this->Model_products->countOrderItem($value['id']);

            $vendor_data = $this->Model_products->getSupplierData($value['vendor_id']);

            $vendor_name = '<span style="text-transform:capitalize">'.$vendor_data['first_name']. ' '. $vendor_data['last_name'].'</span>';



            $returns_amount = 0;

            $purchase_return_data = $this->Model_products->getPurchaseReturnsAmount($value['id']);

            if($purchase_return_data){

                $returns_amount = $purchase_return_data['returns_amount'];

            }



            $date = date('d-m-Y', strtotime($value['datetime_created']));

            $time = date('h:i a', strtotime($value['datetime_created']));

            $datetime = $date . ' ' . $time;

            //

            $userdata = $this->Model_users->getUserData($value['user_id']);

            $created_by = '<span style="text-transform:capitalize">'.$userdata['firstname'].' '.$userdata['lastname'].'</span>';



            $html .= '<tr>

            <td>'.$counter++.'</td>

            <td>'.$datetime.'</td>

            <td>'.$value['bill_no'].'</td>

            <td>'.$vendor_name.'</td>

            <td>'.$created_by.'</td>

            <td>'.floatval($value['net_amount'] - $returns_amount).'</td>

            <td>'.floatval(($value['opening_balance'] + $value['net_amount'] - $value['total_paid'] - $returns_amount)).'</td>



            </tr>';

        }

        $html .='



        </tbody>



        </table>

        </div>

        </div>

        </div>



        </div>

        </body>



        <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

        <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



        </html>';

        echo $html;

    }

}



public function manage_units()

{

    if(!in_array('recordScalingUnits', $this->permission)) {

        $data['page_title'] = "No Permission";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');

        $this->load->view('errors/forbidden_access');

    }

    else

    {

        $data['page_title'] = "Manage Units";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');



        $user_id = $this->session->userdata('id');

        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

        $data['user_permission'] = unserialize($group_data['permission']);



        $this->load->view('products/manage_units', $data);

        $this->load->view('templates/footer');

    }



}



public function fetchUnitsData()

{

    $result = array('data' => array());



    $data = $this->Model_products->getUnitsData();

    $counter = 1;

    foreach ($data as $key => $value) {

        $unit_data_values = $this->Model_products->getUnitValuesData();

        $unit_value = "";

        foreach ($unit_data_values as $k => $v) {

            if($value['id'] == $v['unit_id'])

            {

                $unit_value = $v['unit_value'];

            }

        }

            // button

        $buttons = '';

        if(in_array('updateScalingUnits', $this->permission))

        {

            $buttons .= ' <a title="Edit Unit" onclick="editFunc('.$value['id'].')" data-toggle="modal" href="#editModal"><i class="glyphicon glyphicon-pencil"></i></a>';

        }

        if(in_array('deleteScalingUnits', $this->permission))

        {

            $buttons .= ' <a title="Delete Unit" onclick="removeFunc('.$value['id'].')" data-toggle="modal" href="#removeModal"><i class="glyphicon glyphicon-trash"></i></a>';

        }



        $result['data'][$key] = array(

            $counter++,

            $value['unit_name'],

            $unit_value,

            $buttons

        );

        } // /foreach



        echo json_encode($result);

    }



    public function create_unit()

    {

        $response = array();



        $this->form_validation->set_rules('unit_name', 'Unit Name', 'trim|required');

        $this->form_validation->set_rules('unit_value', 'Unit Value', 'trim|required');

        $this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');



        if ($this->form_validation->run() == TRUE) {

            $data = array(

                'unit_name' => $this->input->post('unit_name')

            );

            $create = $this->db->insert('units', $data);

            $unit_id = $this->db->insert_id();

            $data = array(

                'unit_value' => $this->input->post('unit_value'),

                'unit_id' => $unit_id

            );

            $create = $this->db->insert('unit_values', $data);

            if($create == true) {

                $response['success'] = true;

                $response['messages'] = 'Succesfully created';

            }

            else {

                $response['success'] = false;

                $response['messages'] = 'Error in the database while creating the units information';

            }

        }

        else {

            $response['success'] = false;

            foreach ($_POST as $key => $value) {

                $response['messages'][$key] = form_error($key);

            }

        }



        echo json_encode($response);

    }



    public function update_unit($id)

    {

        $response = array();



        if($id) {

            $this->form_validation->set_rules('edit_unit_name', 'Unit Name', 'trim|required');

            $this->form_validation->set_rules('edit_unit_value', 'Unit Value', 'trim|required');



            $this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');



            if ($this->form_validation->run() == TRUE) {

                $data = array(

                    'unit_name' => $this->input->post('edit_unit_name')

                );

                $this->db->where('id', $id);

                $update = $this->db->update('units', $data);

                $unit_value_data = $this->Model_products->fetchUnitValueData($id);

                $data = array(

                    'unit_value' => $this->input->post('edit_unit_value')

                );

                $this->db->where('id', $unit_value_data['id']);

                $update = $this->db->update('unit_values', $data);

                if($update == true) {

                    $response['success'] = true;

                    $response['messages'] = 'Succesfully updated';

                }

                else {

                    $response['success'] = false;

                    $response['messages'] = 'Error in the database while updated the brand information';

                }

            }

            else {

                $response['success'] = false;

                foreach ($_POST as $key => $value) {

                    $response['messages'][$key] = form_error($key);

                }

            }

        }

        else {

            $response['success'] = false;

            $response['messages'] = 'Error please refresh the page again!!';

        }



        echo json_encode($response);

    }



    public function fetchUnitValueDataById($id)

    {

        if($id) {

            $data['unit_data'] = $this->Model_products->getUnitsData($id);

            $data['unit_value_data'] = $this->Model_products->fetchUnitValueData($id);

            echo json_encode($data);

        }



        return false;

    }



    public function remove_unit()

    {

        $unit_id = $this->input->post('unit_id');



        $response = array();

        if($unit_id) {



            $this->db->where('id', $unit_id);

            $data = array('is_deleted' => 1);

            $delete = $this->db->update('units', $data);



            $unit_value_data = $this->Model_products->fetchUnitValueData($unit_id);

            $this->db->where('id', $unit_value_data['id']);

            $delete = $this->db->update('unit_values', $data);

            if($delete == true) {

                $response['success'] = true;

                $response['messages'] = "Successfully removed";

            }

            else {

                $response['success'] = false;

                $response['messages'] = "Error in the database while removing the brand information";

            }

        }

        else {

            $response['success'] = false;

            $response['messages'] = "Refersh the page again!!";

        }

        echo json_encode($response);

    }





    public function getTableOfficeStockItemsData()

    {

        $data['products'] = $this->Model_products->getOfficeStockItemsData();

        $data['units_data'] = $this->Model_products->getUnitsData();

        $data['unit_data_values'] = $this->Model_products->getUnitValuesData();

        $response['data'] = $data;

        $response['success'] = true;

        echo json_encode($response);

    }



    public function print_units()

    {

        $result = array();

        date_default_timezone_set("Asia/Karachi");

        $print_date = date('d/m/Y');

        $user_id = $this->session->userdata('id');

        $user_data = $this->Model_users->getUserData($user_id);

        $data = $this->Model_products->getUnitsData();





        if(!empty($data)){



          $html = '<!DOCTYPE html>

          <html lang="en">

          <head>

          <meta charset="UTF-8">

          <meta name="viewport" content="width=device-width, initial-scale=1.0">

          <meta http-equiv="X-UA-Compatible" content="ie=edge">

          <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

          <style>

          .invoice-title h2, .invoice-title h3 {

            display: inline-block;

        }



        .table > tbody > tr > .no-line {

            border-top: none;

        }



        .table > thead > tr > .no-line {

            border-bottom: none;

        }



        .table > tbody > tr > .thick-line {

            border-top: 2px solid;

        }

        </style>



        <title>TBM - Units Print</title>

        </head>

        <body onload="window.print();">

        <div class="container">

        <div class="row">

        <div class="col-xs-12">

        <div class="invoice-title">

        <h3>TBM Automobile Private Ltd</h3>

        </div>

        <hr>

        <div class="row">

        <div class="col-xs-6">

        <address style="text-transform:capitalize">

        <strong>Printed By:</strong><br>

        '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

        </address>

        </div>

        <div class="col-xs-6 text-right">

        <address>

        <strong>Print Date:</strong><br>

        '.date("d-m-Y").'<br>



        </address>

        </div>

        </div>

        </div>

        </div>



        <div class="row">

        <div class="col-md-12">



        <div class="table-responsive">

        <table class="table table-condensed table-bordered">

        <thead>

        <tr>

        <th style="width:10%"><strong>#</strong></th>

        <th style="width:45%"><strong>Unit Name</strong></th>

        <th style="width:45%"><strong>Unit Value</strong></th>

        </tr>

        </thead>

        <tbody>';

        $counter = 1;

        foreach ($data as $key => $value) {

            $unit_data_values = $this->Model_products->getUnitValuesData();

            $unit_value = "";

            foreach ($unit_data_values as $k => $v) {

                if($value['id'] == $v['unit_id'])

                {

                    $unit_value = $v['unit_value'];

                }

            }

            $html .= '<tr>

            <td>'.$counter++.'</td>

            <td style="text-transform:capitalize">'.$value['unit_name'].'</td>

            <td>'.$unit_value.'</td>

            </tr>';

        }

        $html .='



        </tbody>



        </table>

        </div>

        </div>

        </div>

        </div>



        </body>



        <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

        <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



        </html>';

        echo $html;

    }

    else{

        $html = '<!-- Main content -->

        <!DOCTYPE html>

        <html>

        <head>

        <meta charset="utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>TBM Automobile Private Ltd | Units Print</title>

        <!-- Tell the browser to be responsive to screen width -->

        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- Bootstrap 3.3.7 -->

        <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">

        <!-- Font Awesome -->

        <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">

        <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">

        </head>

        <body onload="window.print();">



        <div class="wrapper">

        <section class="invoice">

        <!-- title row -->

        <div class="row">

        <div class="col-xs-12">

        <h2 class="page-header">

        TBM Automobile Private Ltd

        </h2>

        </div>

        <!-- /.col -->

        </div>

        <!-- Table row -->

        <div class="row">

        <div class="col-xs-12 table-responsive">

        <table class="table table-striped">

        <thead>

        <tr>

        <th style="width:10%"><strong>#</strong></th>

        <th style="width:45%"><strong>Unit Name</strong></th>

        <th style="width:45%"><strong>Unit Value</strong></th>

        </tr>

        </thead>

        <tbody>';



        $html .= '<tr>



        </tr>';



        $html .= '</tbody>

        </table>

        </div>

        <!-- /.col -->

        </div>

        <!-- /.row -->

        </section>

        <!-- /.content -->

        </div>

        </body>

        </html>';

        echo $html;

    }

}



public function getCustomerValueById()

{

    $customer_id = $this->input->post('customer_id');

    if($customer_id) {

        $customer_data = $this->Model_Customers->getCustomerData($customer_id);

        echo json_encode($customer_data);

    }

}



public function create_sale_order()

{

    if(!in_array('createSaleOrderNE', $this->permission)) {

        $data['page_title'] = "No Permission";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');

        $this->load->view('errors/forbidden_access');

    }

    else

    {

        $this->form_validation->set_rules('product[]', 'Product', 'trim|required');

        if($this->form_validation->run() == TRUE)

        {

            $pay_method = $this->input->post('payment_method');

            $pay_note = $this->input->post('payment_note');

            date_default_timezone_set("Asia/Karachi");

            $date_time = date('Y-m-d H:i:s a');

            $user_id = $this->session->userdata('id');

            $gross_amount =  $this->input->post('gross_amount_value');

            $net_amount = $this->input->post('net_amount_value');

            $discount = $this->input->post('discount');

            $affair_loading = $this->input->post('affair_loading');

            $paid_amount = $this->input->post('paid_amount');

            $customer_name = $this->input->post('customer_name');

            $customer_address = $this->input->post('customer_address');

            $customer_contact = $this->input->post('customer_contact');

            $customer_cnic = $this->input->post('customer_cnic');



            // check if data in stock exist

            $mainInputArray = array();

            // fill this array with stock data

            $selected_stock = $this->input->post('select_stock');

            $stock_product_data = array();

            $stock_name = "";

            if($selected_stock == 1)

            {

                // factory data

                $stock_product_data = $this->Model_products->getStockProductData();

                $stock_name = "Factory Stock";

            }

            else if($selected_stock == 2){

                // office data

                $stock_product_data = $this->Model_products->getOfficeStockItemsData();

                $stock_name = "Office Stock";

            }

            if($selected_stock == 1)

            {

                // factory

                for($i = 0; $i < count($stock_product_data); $i++)

                {

                    $temp = array();

                    $temp[0] = $stock_product_data[$i]['category_id'];

                    $temp[1] = $stock_product_data[$i]['product_id'];

                    $temp[2] = $stock_product_data[$i]['unit_id'];

                    $temp[3] = $this->Model_products->itemExistInStock($stock_product_data[$i]['category_id'], $stock_product_data[$i]['product_id'], $stock_product_data[$i]['unit_id'])['quantity'];

                    array_push($mainInputArray, $temp);

                }

            }

            else if($selected_stock == 2){

                // office data

                for($i = 0; $i < count($stock_product_data); $i++)

                {

                    $temp = array();

                    $temp[0] = $stock_product_data[$i]['category_id'];

                    $temp[1] = $stock_product_data[$i]['product_id'];

                    $temp[2] = $stock_product_data[$i]['unit_id'];

                    $temp[3] = $this->Model_products->itemExistInOfficeStock($stock_product_data[$i]['category_id'], $stock_product_data[$i]['product_id'], $stock_product_data[$i]['unit_id'])['quantity'];

                    array_push($mainInputArray, $temp);

                }

            }



            $count_product = count($this->input->post('product'));

            for($x = 0; $x < $count_product; $x++)

            {

                $unit_value = 1;

                $inputed_unit_id = $this->input->post('unit')[$x];

                if($inputed_unit_id)

                {

                    $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                }

                $product_id = explode("-",$this->input->post('product')[$x])[0];

                $category_id = explode("-",$this->input->post('product')[$x])[1];

                $stock_unit_id = explode("-",$this->input->post('product')[$x])[2];



                if(empty($category_id)){

                    $category_id = 0;

                }

                for ($i = 0; $i < count($mainInputArray); $i++) {

                    if($mainInputArray[$i][0] == $category_id){

                        if($mainInputArray[$i][1] == $product_id)

                        {

                            if($mainInputArray[$i][2] == $stock_unit_id)

                            {

                                $mainInputArray[$i][3] -= ($this->input->post('qty')[$x] * $unit_value);

                            }

                        }

                    }

                }

            }

            // first check if stock data exist

            if(empty($mainInputArray)){

                    // stock data doesnt exit

                $this->session->set_flashdata('errors', $stock_name.' do not have these items!');

                redirect('/Product/create_sale_order', 'refresh');

            }

            else{

                // now check if in new mainInput array any qty is negitive

                foreach ($mainInputArray as $key => $value)

                {

                    if($value[3] < 0)

                    {

                        $data['paid_amount'] = $paid_amount;

                        $data['payment_method'] = $pay_method;

                        $data['payment_note'] = $pay_note;

                        $data['customer_name'] = $customer_name;

                        $data['customer_address'] = $customer_address;

                        $data['customer_contact'] = $customer_contact;

                        $data['customer_cnic'] = $customer_cnic;

                        $data['input_products'] = $this->input->post('product');

                        $data['input_units'] = $this->input->post('unit');

                        $data['input_qty'] = $this->input->post('qty');

                        $data['input_rate'] = $this->input->post('rate_value');

                        $data['gross_amount'] = $gross_amount;

                        $data['net_amount'] = $net_amount;

                        $data['discount'] = $discount;

                        $data['loading_or_affair'] = $affair_loading;

                        $data['remarks'] = $this->input->post('remarks');

                        $data['input_s_qty'] = $this->input->post('s_qty_value');

                        $data['input_selected_stock'] = $this->input->post('select_stock');

                        if($selected_stock == 1)

                        {

                            // factory data

                            $data['products'] = $this->Model_products->getStockProductData();

                        }

                        else if($selected_stock == 2){

                            // office data

                            $data['products'] = $this->Model_products->getOfficeStockItemsData();

                        }

                        $product_data = $this->Model_products->getProductData($value[1]);

                        $category_name = '';

                        if($value[0]){

                            $category_name = '&#8212 '.$this->Model_category->getCategoryData($value[0])['name'];

                        }

                        $unit_data = $this->Model_products->getUnitsData($value[2]);

                        $this->session->set_flashdata('errors', 'Insufficient data in '. $stock_name .' for '.$product_data['name']. ' ' . $category_name .' &#8212&#8212&#8212 '.'('.$unit_data['unit_name'].')');

                        $this->session->set_flashdata('input_data_array', $data);

                        redirect('/Product/create_sale_order', 'refresh');

                    }

                }

            }



            // otherwise proceed the order

            $data = array(

                'bill_no' => 'BILPR-'.strtoupper(substr(md5(uniqid(mt_rand(), true)), 4, 9)),

                'date_time' => $date_time,

                'gross_amount' => $gross_amount,

                'net_amount' => $net_amount,

                'discount' => $discount,

                'loading_or_affair' => $affair_loading,

                'remarks' => $this->input->post('remarks'),

                'paid_amount' => $paid_amount,

                'payment_method' => $pay_method,

                'payment_note' => $pay_note,

                'customer_name' => $customer_name,

                'customer_address' => $customer_address,

                'customer_contact' => $customer_contact,

                'customer_cnic' => $customer_cnic,

                'stock_type' => $selected_stock,

                'user_id' => $user_id

            );



            $insert = $this->db->insert('sales_order', $data);

            if($insert)

            {

                $sale_order_id = $this->db->insert_id();

                $count_product = count($this->input->post('product'));

                for($x = 0; $x < $count_product; $x++)

                {

                    $product_id = explode("-",$this->input->post('product')[$x])[0];

                    $category_id = explode("-",$this->input->post('product')[$x])[1];

                    $stock_unit_id = explode("-",$this->input->post('product')[$x])[2];



                    if(empty($category_id)){

                        $category_id = 0;

                    }



                    if($this->input->post('unit')[$x])

                    {

                        $unit_value = 1;

                        $inputed_unit_id = $this->input->post('unit')[$x];

                        if($inputed_unit_id){

                            $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                        }



                        $items = array(

                            'sale_order_id' => $sale_order_id,

                            'category_id' => $category_id,

                            'unit_id' => $inputed_unit_id,

                            'stock_unit_id' => $stock_unit_id,

                            'product_id' => $product_id,

                            'product_price' => $this->input->post('rate_value')[$x],

                            'qty' => ($this->input->post('qty')[$x] * $unit_value)

                        );

                        $insert = $this->db->insert('sale_order_items', $items);

                        // reduce the amount from the stock

                        $stock_data = array();

                        if($selected_stock == 1)

                        {

                            // factory data

                            $stock_data = $this->Model_products->itemExistInStock($category_id, $product_id, $stock_unit_id);

                        }

                        else if($selected_stock == 2)

                        {

                            // office data

                            $stock_data = $this->Model_products->itemExistInOfficeStock($category_id, $product_id, $stock_unit_id);

                        }

                        if($stock_data)

                        {

                            $quantity = $stock_data['quantity'];

                            $quantity -= ($this->input->post('qty')[$x] * $unit_value);

                            $stock = array(

                                'quantity' => $quantity

                            );

                            if($selected_stock == 1){

                                $this->db->where('id', $stock_data['id']);

                                $this->db->update('items_stock', $stock);

                            }

                            else if($selected_stock == 2){

                                $this->db->where('id', $stock_data['id']);

                                $this->db->update('office_items_stock', $stock);

                            }

                        }

                    }

                    else

                    {

                        $items = array(

                            'sale_order_id' => $sale_order_id,

                            'category_id' => $category_id,

                            'product_id' => $product_id,

                            'unit_id' => 0,

                            'stock_unit_id' => $stock_unit_id,

                            'product_price' => $this->input->post('rate_value')[$x],

                            'qty' => $this->input->post('qty')[$x]

                        );



                        $insert = $this->db->insert('sale_order_items', $items);

                        // reduce the amount from the stock

                        $stock_data = array();

                        if($selected_stock == 1)

                        {

                            // factory data

                            $stock_data = $this->Model_products->itemExistInStock($category_id, $product_id, $stock_unit_id);

                        }

                        else if($selected_stock == 2){

                            // office data

                            $stock_data = $this->Model_products->itemExistInOfficeStock($category_id, $product_id, $stock_unit_id);

                        }



                        if($stock_data)

                        {

                            $quantity = $stock_data['quantity'];

                            $quantity -= $this->input->post('qty')[$x];

                            $stock = array(

                                'quantity' => $quantity

                            );

                            if($selected_stock == 1){

                                $this->db->where('id', $stock_data['id']);

                                $this->db->update('items_stock', $stock);

                            }

                            else if($selected_stock == 2){

                                $this->db->where('id', $stock_data['id']);

                                $this->db->update('office_items_stock', $stock);

                            }

                        }

                    }

                }//end for items

                $this->session->set_flashdata('success', 'Successfully created');

                redirect('/Product/view_sale_order_items/'.$sale_order_id, 'refresh');

            }

            else

            {

                $this->session->set_flashdata('errors', 'Error occurred while inserting the sale order information!');

                redirect('/Product/update_sale_order/'.$sale_order_id, 'refresh');

            }

        }

        else

        {



            $data['page_title'] = "Sales Page";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');



            $data['input_data_array'] = '';

            $data['paid_amount'] = '';

            $data['payment_method'] = '';

            $data['payment_note'] = '';

            $data['customer_name'] = '';

            $data['customer_address'] = '';

            $data['customer_contact'] = '';

            $data['customer_cnic'] = '';

            $data['input_products'] = '';

            $data['input_units'] = '';

            $data['input_qty'] = '';

            $data['input_rate'] = '';

            $data['gross_amount'] = '';

            $data['net_amount'] = '';

            $data['discount'] = '';

            $data['loading_or_affair'] = '';

            $data['remarks'] = '';

            $data['input_selected_stock'] = '';



            $data['products'] = $this->Model_products->getFactoryStockData();

            $data['units_data'] = $this->Model_products->getUnitsData();

            $data['unit_data_values'] = $this->Model_products->getUnitValuesData();



            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/sales_page', $data);

            $this->load->view('templates/footer');

        }

    }

}



public function getProductFromFactoryStock()

{

    $product_id = $this->input->post('product_id');

    $category_id = $this->input->post('category_id');

    $unit_id = $this->input->post('unit_id');

    if($product_id)

    {

        $data['data'] = $this->Model_products->itemExistInStock($category_id, $product_id, $unit_id);

        $data['success'] = true;

        echo json_encode($data);

    }

    else

    {

        $data['success'] = false;

        echo json_encode($data);

    }

}

public function getProductFromOfficeStock()

{

    $product_id = $this->input->post('product_id');

    $category_id = $this->input->post('category_id');

    $unit_id = $this->input->post('unit_id');

    if($product_id)

    {

        $data['data'] = $this->Model_products->itemExistInOfficeStock($category_id, $product_id, $unit_id);

        $data['success'] = true;

        echo json_encode($data);

    }

    else

    {

        $data['success'] = false;

        echo json_encode($data);

    }

}



public function update_sale_order($order_id)

{

    if(!in_array('updateSaleOrderNE', $this->permission)) {

        $data['page_title'] = "No Permission";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');

        $this->load->view('errors/forbidden_access');

    }

    else

    {

        $data['sale_order_data'] = $this->Model_products->getSaleOrdersData($order_id);

        if(!empty($data['sale_order_data']))

        {

            $this->form_validation->set_rules('product[]', 'Product name', 'trim|required');

            $this->form_validation->set_rules('qty[]', 'Quantity', 'trim|required');

            if($this->form_validation->run() == TRUE)

            {

                $pay_method = $this->input->post('payment_method');

                $pay_note = $this->input->post('payment_note');

                // previous order data

                $previous_sale_order_data = $this->Model_products->getSaleOrdersData($order_id);



                $user_id = $this->session->userdata('id');

                $gross_amount =  $this->input->post('gross_amount_value');

                $net_amount = $this->input->post('net_amount_value');

                $discount = $this->input->post('discount');

                $affair_loading = $this->input->post('affair_loading');

                $paid_amount = $this->input->post('paid_amount');

                $customer_name = $this->input->post('customer_name');

                $customer_address = $this->input->post('customer_address');

                $customer_contact = $this->input->post('customer_contact');

                $customer_cnic = $this->input->post('customer_cnic');





                // check if data in stock exist

                $mainInputArray = array();

                $stock_name = "";

                    // fill this array with stock data

                $selected_stock = $this->input->post('select_stock');



                $stock_product_data = array();

                if($selected_stock == 1)

                {

                        // factory data

                    $stock_product_data = $this->Model_products->getStockProductData();

                    $stock_name = "Factory Stock";

                }

                else if($selected_stock == 2){

                        // office data

                    $stock_product_data = $this->Model_products->getOfficeStockItemsData();

                    $stock_name = "Office Stock";

                }

                if($selected_stock == 1)

                {

                        // factory

                    for($i = 0; $i < count($stock_product_data); $i++)

                    {

                        $temp = array();

                        $temp[0] = $stock_product_data[$i]['category_id'];

                        $temp[1] = $stock_product_data[$i]['product_id'];

                        $temp[2] = $stock_product_data[$i]['unit_id'];

                        $temp[3] = $this->Model_products->itemExistInStock($stock_product_data[$i]['category_id'], $stock_product_data[$i]['product_id'], $stock_product_data[$i]['unit_id'])['quantity'];

                        array_push($mainInputArray, $temp);

                    }

                }

                else if($selected_stock == 2){

                        // office

                    for($i = 0; $i < count($stock_product_data); $i++)

                    {

                        $temp = array();

                        $temp[0] = $stock_product_data[$i]['category_id'];

                        $temp[1] = $stock_product_data[$i]['product_id'];

                        $temp[2] = $stock_product_data[$i]['unit_id'];

                        $temp[3] = $this->Model_products->itemExistInOfficeStock($stock_product_data[$i]['category_id'], $stock_product_data[$i]['product_id'], $stock_product_data[$i]['unit_id'])['quantity'];

                        array_push($mainInputArray, $temp);

                    }

                }

                    // increase the quantity from the previous order items

                $sale_items_data = $this->Model_products->getSaleItemsData($order_id);

                for($i = 0; $i < count($sale_items_data); $i++) {



                    $category_id = 0;

                    if($sale_items_data[$i]['category_id']){

                        $category_id = $sale_items_data[$i]['category_id'];

                    }

                    for($j = 0; $j < count($mainInputArray); $j++)

                    {

                        if($category_id == $mainInputArray[$j][0])

                        {

                            if($sale_items_data[$i]['product_id'] == $mainInputArray[$j][1])

                            {

                                if($sale_items_data[$i]['stock_unit_id'] == $mainInputArray[$j][2])

                                {

                                        // already unit value is multiplied in items qty

                                    $mainInputArray[$j][3] += ($sale_items_data[$i]['qty']);

                                    break;

                                }

                            }

                        }

                    }

                }

                    // decrease it from input products

                $count_product = count($this->input->post('product'));

                for($x = 0; $x < $count_product; $x++)

                {

                    $unit_value = 1;

                    $inputed_unit_id = $this->input->post('unit')[$x];

                    if($inputed_unit_id)

                    {

                        $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                    }

                    $product_id = explode("-",$this->input->post('product')[$x])[0];

                    $category_id = explode("-",$this->input->post('product')[$x])[1];

                    $stock_unit_id = explode("-",$this->input->post('product')[$x])[2];



                    if(empty($category_id)){

                        $category_id = 0;

                    }

                    for ($i = 0; $i < count($mainInputArray); $i++) {

                        if($mainInputArray[$i][0] == $category_id){

                            if($mainInputArray[$i][1] == $product_id)

                            {

                                if($mainInputArray[$i][2] == $stock_unit_id)

                                {

                                    $mainInputArray[$i][3] -= ($this->input->post('qty')[$x] * $unit_value);

                                }

                            }

                        }

                    }

                }

                    // now check if in new mainInput array any qty is negitive

                    // first check if stock data exist

                if(empty($mainInputArray)){

                    // stock data doesnt exit

                    $this->session->set_flashdata('errors', $stock_name.' do not have these items!');

                    redirect('/Product/create_sale_order', 'refresh');

                }

                else

                {

                    foreach ($mainInputArray as $key => $value)

                    {

                        if($value[3] < 0){



                            $product_data = $this->Model_products->getProductData($value[1]);

                            $category_name = '';

                            if($value[0]){

                                $category_name = '&#8212 '.$this->Model_category->getCategoryData($value[0])['name'];

                            }

                            $unit_data = $this->Model_products->getUnitsData($value[2]);

                            $this->session->set_flashdata('errors', 'Insufficient data in '. $stock_name .' for '.$product_data['name']. ' ' . $category_name .' &#8212&#8212&#8212 '.'('.$unit_data['unit_name'].')');

                            redirect('/Product/update_sale_order/'.$order_id, 'refresh');

                        }

                    }

                }

                    // otherwise proceed the order

                $data = array(

                    'gross_amount' => $gross_amount,

                    'net_amount' => $net_amount,

                    'discount' => $discount,

                    'loading_or_affair' => $affair_loading,

                    'remarks' => $this->input->post('remarks'),

                    'paid_amount' => $paid_amount,

                    'payment_method' => $pay_method,

                    'payment_note' => $pay_note,

                    'customer_name' => $customer_name,

                    'customer_address' => $customer_address,

                    'customer_contact' => $customer_contact,

                    'customer_cnic' => $customer_cnic,

                    'stock_type' => $selected_stock,

                    'user_id' => $user_id

                );

                $previous_sale_order_stock_type = $previous_sale_order_data['stock_type'];

                $this->db->where('id', $order_id);

                $update = $this->db->update('sales_order', $data);

                    // remove the existing items

                    // add into previous sale order stock type, remove items

                $sale_items_data = $this->Model_products->getSaleItemsData($order_id);

                foreach ($sale_items_data as $key => $value) {

                    $categ_id = 0;

                    if($value['category_id']){

                        $categ_id = $value['category_id'];

                    }

                    $stock_data_row = array();

                    if($previous_sale_order_stock_type == 1){

                        $stock_data_row = $this->Model_products->itemExistInStock($categ_id, $value['product_id'], $value['stock_unit_id']);

                    }

                    else if($previous_sale_order_stock_type == 2){

                        $stock_data_row = $this->Model_products->itemExistInOfficeStock($categ_id, $value['product_id'], $value['stock_unit_id']);

                    }

                        // items qty are already having qty multiplied with unit value

                    $data = array(

                        'quantity' => $stock_data_row['quantity'] + $value['qty']

                    );

                        // update stock

                    if($previous_sale_order_stock_type == 1){

                        $this->db->where('id', $stock_data_row['id']);

                        $this->db->update('items_stock', $data);

                    }

                    else if($previous_sale_order_stock_type == 2){

                        $this->db->where('id', $stock_data_row['id']);

                        $this->db->update('office_items_stock', $data);

                    }

                        // delete items

                    $this->db->where('id', $value['id']);

                    $this->db->delete('sale_order_items');

                }

                    //insert new items

                $count_product = count($this->input->post('product'));

                for($x = 0; $x < $count_product; $x++)

                {

                    $product_id = explode("-",$this->input->post('product')[$x])[0];

                    $category_id = explode("-",$this->input->post('product')[$x])[1];

                    $stock_unit_id = explode("-",$this->input->post('product')[$x])[2];



                    if(empty($category_id)){

                        $category_id = 0;

                    }

                    $unit_value = 1;

                    $inputed_unit_id = $this->input->post('unit')[$x];

                    if($inputed_unit_id){

                        $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                    }



                    $items = array(

                        'sale_order_id' => $order_id,

                        'category_id' => $category_id,

                        'product_id' => $product_id,

                        'unit_id' => $inputed_unit_id,

                        'stock_unit_id' => $stock_unit_id,

                        'product_price' => $this->input->post('rate_value')[$x],

                        'qty' => $this->input->post('qty')[$x] * $unit_value

                    );

                    $insert = $this->db->insert('sale_order_items', $items);

                    if($insert)

                    {

                        $stock_data_row = array();

                        $categ_id = 0;

                        if($category_id){

                            $categ_id = $category_id;

                        }



                        if($selected_stock == 1){

                            $stock_data_row = $this->Model_products->itemExistInStock($categ_id, $product_id, $stock_unit_id);

                        }

                        else if($selected_stock == 2){

                            $stock_data_row = $this->Model_products->itemExistInOfficeStock($categ_id, $product_id, $stock_unit_id);

                        }

                        if($stock_data_row)

                        {

                            $unit_value = 1;

                            if($inputed_unit_id)

                            {

                                $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                            }



                            $data = array(

                                'quantity' => $stock_data_row['quantity'] - ($this->input->post('qty')[$x] * $unit_value)

                            );

                            if($selected_stock == 1){

                                    // update stock

                                $this->db->where('id', $stock_data_row['id']);

                                $this->db->update('items_stock', $data);

                            }

                            else if($selected_stock == 2){

                                    // update stock

                                $this->db->where('id', $stock_data_row['id']);

                                $this->db->update('office_items_stock', $data);

                            }

                        }

                    }

                    }//end for items

                    $this->session->set_flashdata('success', 'Successfully updated');

                    redirect('/Product/update_sale_order/'.$order_id, 'refresh');

                }

                else

                {

                    $data['page_title'] = "Edit Sale Order";

                    $this->load->view('templates/header', $data);

                    $this->load->view('templates/header_menu');

                    $this->load->view('templates/side_menubar');



                    $data['sale_order_data'] = $this->Model_products->getSaleOrdersData($order_id);

                    $data['sale_items_data'] = $this->Model_products->getSaleItemsData($order_id);

                    $products = array();

                    if($data['sale_order_data']['stock_type'] == 1){

                        $products = $this->Model_products->getStockProductData();

                    }

                    else if($data['sale_order_data']['stock_type'] == 2){

                        $products = $this->Model_products->getOfficeStockItemsData();

                    }

                    $data['products'] = $products;

                    $data['units_data'] = $this->Model_products->getUnitsData();

                    $data['unit_data_values'] = $this->Model_products->getUnitValuesData();



                    // store it for showing it in s.qty

                    $x = 0;

                    $stock_data_array = array();

                    foreach ($data['sale_items_data'] as $key => $value)

                    {

                        $stock_data_row = array();

                        if($data['sale_order_data']['stock_type'] == 1)

                        {

                            // factory stock

                            $stock_data_row = $this->Model_products->itemExistInStock($value['category_id'], $value['product_id'], $value['stock_unit_id']);

                        }

                        else if($data['sale_order_data']['stock_type'] == 2)

                        {

                            // office stock

                            $stock_data_row = $this->Model_products->itemExistInOfficeStock($value['category_id'], $value['product_id'], $value['stock_unit_id']);

                        }

                        $stock_data_array[$x] = $stock_data_row;

                        $x += 1;

                    }

                    $data['stock_data_array'] = $stock_data_array;

                    $user_id = $this->session->userdata('id');

                    $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

                    $data['user_permission'] = unserialize($group_data['permission']);



                    $this->load->view('products/update_sale_order', $data);

                    $this->load->view('templates/footer');

                }

            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }

    }



    public function remove_sale_order()

    {

        $sale_order_id = $this->input->post('sale_order_id');

        $response = array();

        if($sale_order_id)

        {

            $previous_sale_order_stock_type = $this->Model_products->getSaleOrdersData($sale_order_id)['stock_type'];

            $this->db->where('id', $sale_order_id);

            $this->db->delete('sales_order');

            $sale_items_data = $this->Model_products->getSaleItemsData($sale_order_id);

            foreach ($sale_items_data as $key => $value) {

                $categ_id = 0;

                if($value['category_id']){

                    $categ_id = $value['category_id'];

                }

                $stock_data_row = array();

                if($previous_sale_order_stock_type == 1){

                    $stock_data_row = $this->Model_products->itemExistInStock($categ_id, $value['product_id'], $value['stock_unit_id']);

                }

                elseif ($previous_sale_order_stock_type == 2) {

                    $stock_data_row = $this->Model_products->itemExistInOfficeStock($categ_id, $value['product_id'], $value['stock_unit_id']);

                }

                $data = array('quantity' => $stock_data_row['quantity'] + $value['qty']);

                // update stock

                if($previous_sale_order_stock_type == 1){

                    $this->db->where('id', $stock_data_row['id']);

                    $this->db->update('items_stock', $data);

                }

                else if($previous_sale_order_stock_type == 2){

                    $this->db->where('id', $stock_data_row['id']);

                    $this->db->update('office_items_stock', $data);

                }

                // delete items

                $this->db->where('id', $value['id']);

                $delete = $this->db->delete('sale_order_items');

            }//endforeach

            if($delete == true) {

                $response['success'] = true;

                $response['location'] = "manage_sales";

                $this->session->set_flashdata('success', 'deleted Successfully');

                $response['messages'] = "Successfully removed";

            }

            else {

                $response['success'] = false;

                $response['location'] = "";

                $response['messages'] = "Error in the database while removing the product information";

            }

        }

        else {

            $response['success'] = false;

            $response['location'] = "";

            $response['messages'] = "Sale Order Id Does not exist. Refersh the page again!!";

        }

        echo json_encode($response);

    }



    public function manage_sales()

    {

        if(!in_array('recordSaleOrderNE', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['page_title'] = "Manage Sales";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');



            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/manage_sales', $data);

            $this->load->view('templates/footer');

        }

    }



    public function fetchSaleOrders()

    {

        $result = array('data' => array());

        $data = $this->Model_products->getSaleOrdersData();

        $counter = 1;

        foreach ($data as $key => $value)

        {

            $view_items = '';

            $count_total_item = $this->Model_products->countSaleOrderItem($value['id']);

            $buttons = '';

            if(in_array('viewSaleOrderNE', $this->permission))

            {

                $view_items = '<a title="View Sale Order" href="'.base_url().'index.php/Product/view_sale_order_items/'.$value['id'].'">'.$count_total_item.'</a>';

            }

            else{

                $view_items .= $count_total_item;

            }

            if(in_array('viewSaleOrderNE', $this->permission))

            {

                $buttons .= '<a title="View Sale Order" href="'.base_url().'index.php/Product/view_sale_order_items/'.$value['id'].'"><i class="glyphicon glyphicon-eye-open"></i></a>';

            }

            if(in_array('updateSaleOrderNE', $this->permission))

            {

                $buttons .= ' <a title="Edit Sale Order" href="'.base_url().'index.php/Product/update_sale_order/'.$value['id'].'"><i class="glyphicon glyphicon-pencil"></i></a>';

            }

            if(in_array('deleteSaleOrderNE', $this->permission))

            {

                $buttons .= ' <a title="Delete Sale Order" onclick="removeSaleOrder('.$value['id'].')" data-toggle="modal" href="#removeModal"><i class="glyphicon glyphicon-trash"></i></a>';

            }



            $payment_method = '';

            if($value['payment_method'] == 1){

                $payment_method = '<span class="label label-success">Cash / Paid</span>';

            }

            else if($value['payment_method'] == 2){

                $payment_method = '<span class="label label-warning">Check / Paid</span>';

            }

            $stock_type = '';

            if ($value['stock_type'] == 1) {

                $stock_type = 'Factory Stock';

            }

            else if ($value['stock_type'] == 2) {

                $stock_type = 'Office Stock';

            }

            //

            $userdata = $this->Model_users->getUserData($value['user_id']);

            $created_by = '<span style="text-transform:capitalize">'.$userdata['firstname'].' '.$userdata['lastname'].'</span>';



            $result['data'][$key] = array(

                $counter++,

                $value['bill_no'],

                $value['date_time'],

                $value['customer_name'],

                $view_items,

                $stock_type,

                $created_by,

                $value['net_amount'],

                $value['paid_amount'],

                $value['net_amount'] - $value['paid_amount'],

                $buttons

            );

        }

        echo json_encode($result);

    }



    public function print_sales_order()

    {

        if(!in_array('printSaleOrderNE', $this->permission))

        {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $result = array();

            date_default_timezone_set("Asia/Karachi");

            $print_date = date('d/m/Y');

            $user_id = $this->session->userdata('id');

            $user_data = $this->Model_users->getUserData($user_id);

            $data = $this->Model_products->getSaleOrdersData();



            if(!empty($data))

            {

              $html = '<!DOCTYPE html>

              <html lang="en">

              <head>

              <meta charset="UTF-8">

              <meta name="viewport" content="width=device-width, initial-scale=1.0">

              <meta http-equiv="X-UA-Compatible" content="ie=edge">

              <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

              <style>

              .invoice-title h2, .invoice-title h3 {

                display: inline-block;

            }



            .table > tbody > tr > .no-line {

                border-top: none;

            }



            .table > thead > tr > .no-line {

                border-bottom: none;

            }



            .table > tbody > tr > .thick-line {

                border-top: 2px solid;

            }

            </style>



            <title>TBM - Sale Orders Print</title>

            </head>

            <body onload="window.print();">

            <div class="container">

            <div class="row">

            <div class="col-xs-12">

            <div class="invoice-title text-center">

            <h3>TBM Automobile Private Ltd</h3>

            </div>

            <hr>

            <div class="row">

            <div class="col-xs-6">

            <address style="text-transform:capitalize">

            <strong>Printed By:</strong><br>

            '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

            </address>

            </div>

            <div class="col-xs-6 text-right">

            <address>

            <strong>Print Date:</strong><br>

            '.date("d-m-Y").'<br>



            </address>

            </div>

            </div>

            </div>

            </div>



            <div class="row">

            <div class="col-md-12">



            <div class="table-responsive">

            <table class="table table-condensed table-bordered">

            <thead>

            <tr>



            <th><strong>#</strong></th>

            <th><strong>Bill no</strong></th>

            <th><strong>DateTime</strong></th>

            <th><strong>Customer</strong></th>

            <th><strong>Products</strong></th>

            <th><strong>Stock</strong></th>

            <th><strong>Created By</strong></th>

            <th><strong>Net Amount</strong></th>

            <th><strong>Received</strong></th>

            <th><strong>Remaining</strong></th>

            </tr>

            </thead>

            <tbody>';

            $counter = 1;

            foreach ($data as $key => $value)

            {

                $count_total_item = $this->Model_products->countSaleOrderItem($value['id']);

                $payment_method = '';

                if($value['payment_method'] == 1){

                    $payment_method = '<span class="label label-success">Cash / Paid</span>';

                }

                else if($value['payment_method'] == 2){

                    $payment_method = '<span class="label label-warning">Check / Paid</span>';

                }

                $stock_type = '';

                if($value['stock_type'] == 1){

                    $stock_type = "Factory Stock";

                }

                else if($value['stock_type'] == 2){

                    $stock_type = "Office Stock";

                }

                //

                $userdata = $this->Model_users->getUserData($value['user_id']);

                $created_by = '<span style="text-transform:capitalize">'.$userdata['firstname'].' '.$userdata['lastname'].'</span>';

                $html .= '<tr>

                <td>'.$counter++.'</td>

                <td>'.$value['bill_no'].'</td>

                <td>'.$value['date_time'].'</td>

                <td>'.$value['customer_name'].'</td>

                <td>'.$count_total_item.'</td>

                <td>'.$stock_type.'</td>

                <td>'.$created_by.'</td>

                <td>'.$value['net_amount'].'</td>

                <td>'.$value['paid_amount'].'</td>

                <td>'.($value['net_amount'] - $value['paid_amount']).'</td>



                </tr>';

            }

            $html .='



            </tbody>



            </table>

            </div>

            </div>

            </div>



            </div>

            </body>



            <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

            <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



            </html>';

            echo $html;

        }

        else

        {

            $html = '<!-- Main content -->

            <!DOCTYPE html>

            <html>

            <head>

            <meta charset="utf-8">

            <meta http-equiv="X-UA-Compatible" content="IE=edge">

            <title>TBM Automobile Private Ltd | Sale Orders Print</title>

            <!-- Tell the browser to be responsive to screen width -->

            <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

            <!-- Bootstrap 3.3.7 -->

            <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">

            <!-- Font Awesome -->

            <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">

            <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">

            </head>

            <body onload="window.print();">



            <div class="wrapper">

            <section class="invoice">

            <!-- title row -->

            <div class="row">

            <div class="col-xs-12">

            <h2 class="page-header">

            TBM Automobile Private Ltd

            </h2>

            </div>

            <!-- /.col -->

            </div>

            <!-- Table row -->

            <div class="row">

            <div class="col-xs-12 table-responsive">

            <table class="table table-striped">

            <thead>

            <tr>

            <th><strong>#</strong></th>

            <th><strong>Bill no</strong></th>

            <th><strong>DateTime</strong></th>

            <th><strong>Customer</strong></th>

            <th><strong>Products</strong></th>

            <th><strong>Stock</strong></th>

            <th><strong>Net Amount</strong></th>

            <th><strong>Paid</strong></th>

            <th><strong>Remaining</strong></th>

            <th><strong>Payment</strong></th>

            </tr>

            </thead>

            <tbody>';



            $html .= '<tr>



            </tr>';



            $html .= '</tbody>

            </table>

            </div>

            <!-- /.col -->

            </div>

            <!-- /.row -->

            </section>

            <!-- /.content -->

            </div>

            </body>

            </html>';

            echo $html;

        }

    }

}



public function view_sale_order_items($order_id)

{

    if(!in_array('viewSaleOrderNE', $this->permission)) {

        $data['page_title'] = "No Permission";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');

        $this->load->view('errors/forbidden_access');

    }

    else

    {

        $data['sale_order_data'] = $this->Model_products->getSaleOrdersData($order_id);

        if(!empty($data['sale_order_data'])){



            $data['page_title'] = "Sale Order Items";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');



            $data['sale_order_data'] = $this->Model_products->getSaleOrdersData($order_id);

            $data['sale_items_data'] = $this->Model_products->getSaleItemsData($order_id);



            $products = array();

            if($data['sale_order_data']['stock_type'] == 1){

                $products = $this->Model_products->getStockProductData();

            }

            else if($data['sale_order_data']['stock_type'] == 2){

                $products = $this->Model_products->getOfficeStockItemsData();

            }

            $data['products'] = $products;

            $data['units_data'] = $this->Model_products->getUnitsData();

            $data['unit_data_values'] = $this->Model_products->getUnitValuesData();



            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/view_sale_order_items', $data);

            $this->load->view('templates/footer');

        }

        else{

            $data['page_title'] = "404 - Not Found";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/404_not_found');

        }

    }

}



public function print_sale_invoice($order_id)

{

    if(!in_array('printSaleOrderNE', $this->permission)) {

        $data['page_title'] = "No Permission";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');

        $this->load->view('errors/forbidden_access');

    }

    else

    {

        $data['sale_order_data'] = $this->Model_products->getSaleOrdersData($order_id);

        if(!empty($data['sale_order_data'])){

            $sale_order_data = $this->Model_products->getSaleOrdersData($order_id);

            $stock_type = '';

            if ($sale_order_data['stock_type'] == 1) {

                $stock_type = 'Factory Stock';

            }

            else if ($sale_order_data['stock_type'] == 2) {

                $stock_type = 'Office Stock';

            }

            $sale_items_data = $this->Model_products->getSaleItemsData($order_id);

            $units_data = $this->Model_products->getAllUnitsData();

            $unit_data_values = $this->Model_products->getUnitValuesData();

            $user_data = $this->Model_users->getUserData($sale_order_data['user_id']);



            $company_info = $this->Model_company->getCompanyData(1);



            $order_date = $sale_order_data['date_time'];



            $html = '<!DOCTYPE html>

            <html lang="en">

            <head>

            <meta charset="UTF-8">

            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <meta http-equiv="X-UA-Compatible" content="ie=edge">

            <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

            <style>

            .invoice-title h2, .invoice-title h3 {

                display: inline-block;

            }



            </style>



            <title>Sales Invoice</title>

            </head>

            <body onload="window.print();">

            <div class="container">

            <div class="row">

            <div class="col-xs-12">

            <div class="invoice-title text-center">

            <h3>TBM Automobile Private Ltd</h3><h6 class="pull-right">'.$sale_order_data['bill_no'].'</h6>

            </div>

            <hr>

            <div class="row">

            <div class="col-xs-6">

            <address>

            <strong>Customer Name:</strong><br>

            '.$sale_order_data['customer_name'].'<br>

            <strong>Customer CNIC:</strong><br>

            '.$sale_order_data['customer_cnic'].'<br>

            <strong>Customer Address:</strong><br>

            '.$sale_order_data['customer_address'].'<br>

            <strong>Customer Contact:</strong><br>

            '.$sale_order_data['customer_contact'].'<br>

            </address>

            </div>

            <div class="col-xs-6 text-right">

            <address style="text-transform:capitalize">

            <strong>Created By:</strong><br>

            '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

            </address>

            </div>

            </div>

            </div>

            </div>



            <div class="row">

            <div class="col-md-12">



            <div class="table-responsive">

            <table class="table table-condensed table-bordered">

            <thead>

            <tr>

            <td><strong>Item</strong></td>

            <td><strong>Unit</strong></td>

            <td><strong>Stock</strong></td>

            <td class="text-center"><strong>Price</strong></td>

            <td class="text-center"><strong>Quantity</strong></td>

            <td class="text-right"><strong>Totals</strong></td>

            </tr>

            </thead>

            <tbody>';

            foreach ($sale_items_data as $k => $v) {



                $product_data = $this->Model_products->getAllProductData($v['product_id']);

                $product_category = $this->Model_products->getAllProductCategoryData($v['product_id'], $v['category_id']);

                $category_data = '';

                if($product_category['category_id']){

                    $category_data = $this->Model_category->getAllCategoryData($product_category['category_id']);

                }



                $unit_name = 'Not Mentioned';

                $units_data = $this->Model_products->getAllUnitsData();

                foreach ($units_data as $key => $value)

                {

                    $unit_id = $v['unit_id'];

                    $unit_data_values = $this->Model_products->getUnitValuesData();

                    if($value['id'] == $unit_id)

                    {

                        foreach ($unit_data_values as $key_2 => $value_2)

                        {

                            if($value['id'] == $value_2['unit_id'])

                            {

                                $unit_name = $value['unit_name'];

                                break;

                            }

                        }

                    }

                }

                $item_name = '';

                if(!empty($category_data))

                {

                    $item_name = $product_data['name']. ' &#8212 ' .$category_data['name'];

                }

                else

                {

                    $item_name = $product_data['name'];

                }

                $html .= '<tr>

                <td>'.$item_name.'</td>

                <td>'.$unit_name.'</td>

                <td>'.$stock_type.'</td>

                <td class="text-center">'.$v['product_price'].'</td>

                <td class="text-center">'.$v['qty'].'</td>

                <td class="text-right">'. $v['product_price'] * $v['qty'].'</td>

                </tr>';

            }

            $html.='

            </tbody>

            </table>

            </div>

            <div class="">

                           <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Gross Amount</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($sale_order_data['gross_amount']).'"></div>



                           <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Discount</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($sale_order_data['discount']).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Freight</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: nonee; border-color: transparent transparent black transparent; box-shadow: none;" class="form-control" value="'.floatval($sale_order_data['loading_or_affair']).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Net Amount</button><div class="input-group" style="width: 20%;float: right" ><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($sale_order_data['net_amount']).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Received Amount</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: nonee; border-color: transparent transparent black transparent; box-shadow: none;" class="form-control" value="'.floatval($sale_order_data['paid_amount']).'"></div>



                            <button type="button" class="btn btn-info form-control" style="text-align: left; border: none; border-color: transparent; box-shadow: none; width: 80%;float: left" >Remaining</button><div class="input-group"  style="width: 20%;float: right"><span style="border: none; border-color: transparent; box-shadow: none;" class="input-group-addon" id="basic-addon1">Rs.</span><input style="border: none; border-color: transparent; box-shadow: none;" class="form-control" value="'.floatval($sale_order_data['net_amount'] - $sale_order_data['paid_amount']).'"></div>



                            <label><br>Payment Method: </label>'.$sale_order_data['payment_method'].'

                            <br>

                            <label><br>Payment Note: </label>'.$sale_order_data['payment_note'].'

                            <br>

                            <label><br>Remarks: </label>'.$sale_order_data['remarks'].'

                            <br>

                            <div style="width: 50%; float: right;"><label style="width: 30%; float: left">Seller Signature:</label><div style="width: 70%; float: right"><br><hr></div></div>

                            <div style="width: 50%; float: right"><label style="width: 30%; float: left">Customer Signature:</label><div style="width: 70%; float: right"><br><hr></div></div>

                            <div style="width: 30%; float: left"><label style="width: 30%; float: left">Date:</label><div style="width: 70%; float: right">'.date("Y/m/d", strtotime($sale_order_data['date_time'])).'</div></div>

                    </div>

            </body>

            <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

            <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



            </html>';

            echo $html;

        }

        else{

            $data['page_title'] = "404 - Not Found";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/404_not_found');

        }

    }

}



public function company_sales()

{

    if(!in_array('recordSaleOrderE', $this->permission)) {

        $data['page_title'] = "No Permission";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');

        $this->load->view('errors/forbidden_access');

    }

    else

    {

        $data['page_title'] = "Company Sales";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');



        $user_id = $this->session->userdata('id');

        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

        $data['user_permission'] = unserialize($group_data['permission']);



        $this->load->view('products/company_sales', $data);

        $this->load->view('templates/footer');

    }

}



public function fetchCompanySaleOrders()

{

    $result = array('data' => array());

    $data = $this->Model_products->getCompanySaleOrdersData();

    $counter = 1;

    foreach ($data as $key => $value)

    {

        $customer_data = $this->Model_Customers->getCustomerData($value['customer_id']);

        $department_data = $this->Model_department->getDepartmentData($value['department_id']);

        $count_total_item = $this->Model_products->countCompanySaleOrderItem($value['id']);



        $buttons = '';

        if(in_array('viewSaleOrderE', $this->permission)) {

            $buttons .= '<a title="View Sale Order" href="'.base_url().'index.php/Product/view_company_sale_order_items/'.$value['id'].'"><span class="glyphicon glyphicon-eye-open"></span></a>';

        }

        if(in_array('updateSaleOrderE', $this->permission)) {

            $buttons .= ' <a title="Edit Sale Order" href="'.base_url().'index.php/Product/update_company_sale_order/'.$value['id'].'"><i class="glyphicon glyphicon-pencil"></i></a>';

        }

        if(in_array('deleteSaleOrderE', $this->permission)) {

            $buttons .= ' <a title="Delete Sale Order" onclick="removeCompanySaleOrder('.$value['id'].')" data-toggle="modal" href="#removeModal"><i class="glyphicon glyphicon-trash"></i></a>';

        }

        $stock_type = '';

        if ($value['stock_type'] == 1) {

            $stock_type = 'Factory Stock';

        }

        else if ($value['stock_type'] == 2) {

            $stock_type = 'Office Stock';

        }



        $date = date('d-m-Y', strtotime($value['date_time']));

        $time = date('h:i a', strtotime($value['date_time']));

        $datetime = $date.' '.$time;

        $remarks = 'Not Mentioned';

        if($value['remarks']){

            $remarks = $value['remarks'];

        }

        //

        $userdata = $this->Model_users->getUserData($value['user_id']);

        $created_by = '<span style="text-transform:capitalize">'.$userdata['firstname'].' '.$userdata['lastname'].'</span>';

        $result['data'][$key] = array(

            $counter++,

            $value['bill_no'],

            $datetime,

            $customer_data['full_name'],

            $department_data['department_name'],

            $count_total_item,

            $stock_type,

            $created_by,

            $remarks,

            $buttons

        );

    }

    echo json_encode($result);

}



public function getCustomerDepartmentData()

{

    $customer_id = $this->input->post('customer_id');

    $department_id = $this->input->post('department_id');

    if($customer_id && $department_id){

        $data['customer_data'] = $this->Model_Customers->getCustomerData($customer_id);

        $data['department_data'] = $this->Model_department->getDepartmentData($department_id);

        $response['data'] = $data;

        $response['success'] = true;

        echo json_encode($response);

    }

    else{

        $response['success'] = false;

        echo json_encode($response);

    }

}



public function create_company_sale_order()

{

    if(!in_array('createSaleOrderE', $this->permission)) {

        $data['page_title'] = "No Permission";

        $this->load->view('templates/header', $data);

        $this->load->view('templates/header_menu');

        $this->load->view('templates/side_menubar');

        $this->load->view('errors/forbidden_access');

    }

    else

    {

        $this->form_validation->set_rules('trusted_customer', 'Customer', 'trim|required');

        $this->form_validation->set_rules('product[]', 'Product', 'trim|required');

        if($this->form_validation->run() == TRUE)

        {

            $customer_id = explode("-",$this->input->post('trusted_customer'))[0];

            $department_id = explode("-",$this->input->post('trusted_customer'))[1];



            $customer_data = $this->Model_Customers->getCustomerData($customer_id);

            $department_data = $this->Model_department->getDepartmentData($department_id);



            $customer_name = $customer_data['full_name'];

            $customer_address = $customer_data['address'];

            $customer_contact = $customer_data['phone_number'];

            $customer_deparment = $department_data['department_name'];



            date_default_timezone_set("Asia/Karachi");

            $date_time = date('Y-m-d H:i:s a');

            $user_id = $this->session->userdata('id');



                // check if data in stock exist

            $mainInputArray = array();

            $selected_stock = $this->input->post('select_stock');

            $stock_product_data = array();

            $stock_name = "";

            if($selected_stock == 1)

            {

                    // factory data

                $stock_product_data = $this->Model_products->getStockProductData();

                $stock_name = "Factory Stock";

            }

            else if($selected_stock == 2){

                    // office data

                $stock_product_data = $this->Model_products->getOfficeStockItemsData();

                $stock_name = "Office Stock";

            }

                    // fill this array with stock data

            if($selected_stock == 1)

            {

                    // factory

                for($i = 0; $i < count($stock_product_data); $i++)

                {

                    $temp = array();

                    $temp[0] = $stock_product_data[$i]['category_id'];

                    $temp[1] = $stock_product_data[$i]['product_id'];

                    $temp[2] = $stock_product_data[$i]['unit_id'];

                    $temp[3] = $this->Model_products->itemExistInStock($stock_product_data[$i]['category_id'], $stock_product_data[$i]['product_id'], $stock_product_data[$i]['unit_id'])['quantity'];

                    array_push($mainInputArray, $temp);

                }

            }

            else if($selected_stock == 2){

                    // office data

                for($i = 0; $i < count($stock_product_data); $i++)

                {

                    $temp = array();

                    $temp[0] = trim($stock_product_data[$i]['category_id']);

                    $temp[1] = trim($stock_product_data[$i]['product_id']);

                    $temp[2] = trim($stock_product_data[$i]['unit_id']);

                    $temp[3] = $this->Model_products->itemExistInOfficeStock(trim($stock_product_data[$i]['category_id']), trim($stock_product_data[$i]['product_id']), trim($stock_product_data[$i]['unit_id']))['quantity'];

                    array_push($mainInputArray, $temp);

                }

            }



            $count_product = count($this->input->post('product'));

            for($x = 0; $x < $count_product; $x++)

            {

                $unit_value = 1;

                $inputed_unit_id = $this->input->post('unit')[$x];

                if($inputed_unit_id)

                {

                    $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                }

                $product_id = trim(explode("-",$this->input->post('product')[$x])[0]);

                $category_id = trim(explode("-",$this->input->post('product')[$x])[1]);

                $stock_unit_id = trim(explode("-",$this->input->post('product')[$x])[2]);



                if(empty($category_id)){

                    $category_id = 0;

                }

                for ($i = 0; $i < count($mainInputArray); $i++) {

                    if($mainInputArray[$i][0] == $category_id){

                        if($mainInputArray[$i][1] == $product_id)

                        {

                            if($mainInputArray[$i][2] == $stock_unit_id)

                            {

                                $mainInputArray[$i][3] -= ($this->input->post('qty')[$x] * $unit_value);

                            }

                        }

                    }

                }

            }

                // first check if stock data exist

            if(empty($mainInputArray)){

                    // stock data doesnt exit

                $this->session->set_flashdata('errors', $stock_name.' do not have these items!');

                redirect('/Product/create_sale_order', 'refresh');

            }

            else{

                    // now check if in new mainInput array any qty is negitive

                foreach ($mainInputArray as $key => $value)

                {

                    if($value[3] < 0){



                        $data['customer_name'] = $customer_name;

                        $data['customer_address'] = $customer_address;

                        $data['customer_contact'] = $customer_contact;

                        $data['customer_deparment'] = $customer_deparment;

                        $data['remarks'] = $this->input->post('remarks');

                        $data['input_products'] = $this->input->post('product');

                        $data['input_customer'] = $this->input->post('trusted_customer');

                        $data['input_units'] = $this->input->post('unit');

                        $data['input_qty'] = $this->input->post('qty');

                        $data['input_s_qty'] = $this->input->post('s_qty_value');

                        $data['input_selected_stock'] = $this->input->post('select_stock');

                        if($selected_stock == 1)

                        {

                                // factory data

                            $data['products'] = $this->Model_products->getStockProductData();

                        }

                        else if($selected_stock == 2){

                                // office data

                            $data['products'] = $this->Model_products->getOfficeStockItemsData();

                        }



                        $product_data = $this->Model_products->getProductData($value[1]);

                        $category_name = '';

                        if($value[0]){

                            $category_name = '&#8212 '.$this->Model_category->getCategoryData($value[0])['name'];

                        }

                        $unit_data = $this->Model_products->getUnitsData($value[2]);

                        $this->session->set_flashdata('errors', 'Insufficient data in '. $stock_name .' for '.$product_data['name']. ' ' . $category_name .' &#8212&#8212&#8212 '.'('.$unit_data['unit_name'].')');

                        $this->session->set_flashdata('input_data_array', $data);

                        redirect('/Product/create_company_sale_order', 'refresh');

                    }

                }

            }



                // otherwise proceed the order

            $data = array(

                'bill_no' => 'BILPR-'.strtoupper(substr(md5(uniqid(mt_rand(), true)), 4, 9)),

                'date_time' => $date_time,

                'stock_type' => $selected_stock,

                'remarks' => $this->input->post('remarks'),

                'customer_id' => $customer_id,

                'department_id' => $department_id,

                'user_id' => $user_id

            );



            $insert = $this->db->insert('company_sales_order', $data);

            if($insert)

            {

                $sale_order_id = $this->db->insert_id();

                $count_product = count($this->input->post('product'));

                for($x = 0; $x < $count_product; $x++)

                {

                    $product_id = explode("-",$this->input->post('product')[$x])[0];

                    $category_id = explode("-",$this->input->post('product')[$x])[1];

                    $stock_unit_id = explode("-",$this->input->post('product')[$x])[2];

                    $qty = 0;

                    $inputed_unit_id = 0;

                    if($this->input->post('unit')[$x])

                    {

                        $inputed_unit_id = $this->input->post('unit')[$x];

                        $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                        $qty = $this->input->post('qty')[$x] * $unit_value;

                    }

                    else{

                        $qty = $this->input->post('qty')[$x];

                    }



                    $items = array(

                        'sale_order_id' => $sale_order_id,

                        'category_id' => $category_id,

                        'product_id' => $product_id,

                        'unit_id' => $inputed_unit_id,

                        'stock_unit_id' => $stock_unit_id,

                        'qty' => $qty

                    );



                    $insert = $this->db->insert('company_sales_order_items', $items);

                        // reduce the amount from the stock

                    $stock_data = array();

                    if($selected_stock == 1)

                    {

                            // factory data

                        $stock_data = $this->Model_products->itemExistInStock($category_id, $product_id, $stock_unit_id);

                    }

                    else if($selected_stock == 2){

                            // office data

                        $stock_data = $this->Model_products->itemExistInOfficeStock($category_id, $product_id, $stock_unit_id);

                    }



                    if($stock_data && $insert){



                        $quantity = $stock_data['quantity'];

                        $quantity -= $qty;

                        $data = array(

                            'quantity' => $quantity

                        );

                        if($selected_stock == 1){

                            $this->db->where('id', $stock_data['id']);

                            $this->db->update('items_stock', $data);

                        }

                        else if($selected_stock == 2){

                            $this->db->where('id', $stock_data['id']);

                            $this->db->update('office_items_stock', $data);

                        }

                    }

                    else{

                        $this->session->set_flashdata('error', 'Not Successfully created. Contact System Configuration Manager!');

                        redirect('/Product/create_company_sale_order', 'refresh');

                    }



                    }//end for items

                    if($insert){

                        $this->session->set_flashdata('success', 'Successfully created');

                        redirect('/Product/view_company_sale_order_items/'.$sale_order_id, 'refresh');

                    }

                    else{

                        $this->session->set_flashdata('error', 'Not Successfully created. Contact System Configuration Manager');

                        redirect('/Product/create_company_sale_order', 'refresh');

                    }



                }

                else{

                    $this->session->set_flashdata('error', 'Not Successfully created. Contact System Configuration Manager');

                    redirect('/Product/create_company_sale_order', 'refresh');

                }

            }

            else{

                $data['page_title'] = "Sales Page Trusted Customers";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $data['input_data_array'] = '';



                $data['products'] = $this->Model_products->getFactoryStockData();

                $data['customers'] = $this->Model_Customers->getCustomerData();

                $data['customer_deparment'] = $this->Model_department->getCustomerDeparment();

                $data['units_data'] = $this->Model_products->getUnitsData();

                $data['unit_data_values'] = $this->Model_products->getUnitValuesData();

                $data['input_selected_stock'] = '';



                $user_id = $this->session->userdata('id');

                $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

                $data['user_permission'] = unserialize($group_data['permission']);



                $this->load->view('products/sales_page_trusted_customers', $data);

                $this->load->view('templates/footer');

            }

        }

    }



    public function update_company_sale_order($order_id)

    {

        if(!in_array('updateSaleOrderE', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            if($order_id)

            {

                $this->form_validation->set_rules('trusted_customer', 'Customer', 'trim|required');

                $this->form_validation->set_rules('product[]', 'Product', 'trim|required');

                if($this->form_validation->run() == TRUE){



                    $customer_id = explode("-",$this->input->post('trusted_customer'))[0];

                    $department_id = explode("-",$this->input->post('trusted_customer'))[1];

                    $user_id = $this->session->userdata('id');



                    // check if data in stock exist

                    $mainInputArray = array();

                    // fill this array with stock data

                    $selected_stock = $this->input->post('select_stock');

                    $stock_product_data = array();

                    if($selected_stock == 1)

                    {

                        // factory data

                        $stock_product_data = $this->Model_products->getStockProductData();

                        $stock_name = "Factory Stock";

                    }

                    else if($selected_stock == 2){

                        // office data

                        $stock_product_data = $this->Model_products->getOfficeStockItemsData();

                        $stock_name = "Office Stock";

                    }

                    if($selected_stock == 1)

                    {

                        // factory

                        for($i = 0; $i < count($stock_product_data); $i++)

                        {

                            $temp = array();

                            $temp[0] = $stock_product_data[$i]['category_id'];

                            $temp[1] = $stock_product_data[$i]['product_id'];

                            $temp[2] = $stock_product_data[$i]['unit_id'];

                            $temp[3] = $this->Model_products->itemExistInStock($stock_product_data[$i]['category_id'], $stock_product_data[$i]['product_id'], $stock_product_data[$i]['unit_id'])['quantity'];

                            array_push($mainInputArray, $temp);

                        }

                    }

                    else if($selected_stock == 2){

                        // office data

                        for($i = 0; $i < count($stock_product_data); $i++)

                        {

                            $temp = array();

                            $temp[0] = $stock_product_data[$i]['category_id'];

                            $temp[1] = $stock_product_data[$i]['product_id'];

                            $temp[2] = $stock_product_data[$i]['unit_id'];

                            $temp[3] = $this->Model_products->itemExistInOfficeStock($stock_product_data[$i]['category_id'], $stock_product_data[$i]['product_id'], $stock_product_data[$i]['unit_id'])['quantity'];

                            array_push($mainInputArray, $temp);

                        }

                    }

                    // increase the quantity from the previous order items

                    $sale_items_data = $this->Model_products->getCompanySaleItemsData($order_id);

                    for($i = 0; $i < count($sale_items_data); $i++) {

                        $category_id = 0;

                        if($sale_items_data[$i]['category_id']){

                            $category_id = $sale_items_data[$i]['category_id'];

                        }

                        for($j = 0; $j < count($mainInputArray); $j++)

                        {

                            if($category_id == $mainInputArray[$j][0])

                            {

                                if($sale_items_data[$i]['product_id'] == $mainInputArray[$j][1])

                                {

                                    if($sale_items_data[$i]['stock_unit_id'] == $mainInputArray[$j][2])

                                    {

                                        $mainInputArray[$j][3] += ($sale_items_data[$i]['qty']);

                                        break;

                                    }

                                }

                            }

                        }

                    }

                    // decrease it from input products

                    $count_product = count($this->input->post('product'));

                    for($x = 0; $x < $count_product; $x++)

                    {

                        $unit_value = 1;

                        $inputed_unit_id = $this->input->post('unit')[$x];

                        if($inputed_unit_id)

                        {

                            $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                        }

                        $product_id = explode("-",$this->input->post('product')[$x])[0];

                        $category_id = explode("-",$this->input->post('product')[$x])[1];

                        $stock_unit_id = explode("-",$this->input->post('product')[$x])[2];



                        if(empty($category_id)){

                            $category_id = 0;

                        }

                        for ($i = 0; $i < count($mainInputArray); $i++)

                        {

                            if($mainInputArray[$i][0] == $category_id)

                            {

                                if($mainInputArray[$i][1] == $product_id)

                                {

                                    if($mainInputArray[$i][2] == $stock_unit_id)

                                    {

                                        $mainInputArray[$i][3] -= ($this->input->post('qty')[$x] * $unit_value);

                                    }

                                }

                            }

                        }

                    }

                    // first check if stock data exist

                    if(empty($mainInputArray)){

                    // stock data doesnt exit

                        $this->session->set_flashdata('errors', $stock_name.' do not have these items!');

                        redirect('/Product/update_company_sale_order/'.$order_id, 'refresh');

                    }

                    else

                    {

                        // now check if in new mainInput array any qty is negitive

                        foreach ($mainInputArray as $key => $value) {

                            if($value[3] < 0)

                            {

                                $product_data = $this->Model_products->getProductData($value[1]);

                                $category_name = '';

                                if($value[0]){

                                    $category_name = '&#8212 '.$this->Model_category->getCategoryData($value[0])['name'];

                                }

                                $unit_data = $this->Model_products->getUnitsData($value[2]);

                                $this->session->set_flashdata('errors', 'Insufficient data in '. $stock_name .' for '.$product_data['name']. ' ' . $category_name .' &#8212&#8212&#8212 '.'('.$unit_data['unit_name'].')');

                                redirect('/Product/update_company_sale_order/'.$order_id, 'refresh');

                            }

                        }

                    }



                    // otherwise proceed the order

                    $previous_sale_order_data = $this->Model_products->getCompanySaleOrdersData($order_id);

                    $previous_sale_order_stock_type = $previous_sale_order_data['stock_type'];



                    $data = array(

                        'stock_type' => $selected_stock,

                        'remarks' => $this->input->post('remarks'),

                        'customer_id' => $customer_id,

                        'department_id' => $department_id,

                        'user_id' => $user_id

                    );



                    $this->db->where('id', $order_id);

                    $update = $this->db->update('company_sales_order', $data);



                    // remove the existing items

                    // add into stock=previousselected the removed items

                    $sale_items_data = $this->Model_products->getCompanySaleItemsData($order_id);

                    foreach ($sale_items_data as $key => $value)

                    {

                        $category_id = 0;

                        if($value['category_id']){

                            $category_id = $value['category_id'];

                        }

                        $stock_data_row = array();

                        if($previous_sale_order_stock_type == 1)

                        {

                            // facctory

                            $stock_data_row = $this->Model_products->itemExistInStock($category_id, $value['product_id'], $value['stock_unit_id']);

                        }

                        else if($previous_sale_order_stock_type == 2){

                            $stock_data_row = $this->Model_products->itemExistInOfficeStock($category_id, $value['product_id'], $value['stock_unit_id']);

                        }

                        // items qty are already having qty multiplied with unit value

                        $data = array(

                            'quantity' => $stock_data_row['quantity'] + $value['qty']

                        );



                        // update stock

                        if($previous_sale_order_stock_type == 1){

                            $this->db->where('id', $stock_data_row['id']);

                            $this->db->update('items_stock', $data);

                        }

                        else if($previous_sale_order_stock_type == 2){

                            $this->db->where('id', $stock_data_row['id']);

                            $this->db->update('office_items_stock', $data);

                        }

                        // delete items

                        $this->db->where('id', $value['id']);

                        $this->db->delete('company_sales_order_items');

                    }

                    //insert new items

                    $count_product = count($this->input->post('product'));

                    for($x = 0; $x < $count_product; $x++)

                    {

                        $product_id = explode("-",$this->input->post('product')[$x])[0];

                        $category_id = explode("-",$this->input->post('product')[$x])[1];

                        $stock_unit_id = explode("-",$this->input->post('product')[$x])[2];



                        $inputed_unit_id = $this->input->post('unit')[$x];



                        $unit_value = 1;

                        if($inputed_unit_id)

                        {

                            $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                        }



                        $items = array(

                            'sale_order_id' => $order_id,

                            'category_id' => $category_id,

                            'product_id' => $product_id,

                            'unit_id' => $inputed_unit_id,

                            'stock_unit_id' => $stock_unit_id,

                            'qty' => $this->input->post('qty')[$x] * $unit_value

                        );

                        $insert = $this->db->insert('company_sales_order_items', $items);

                        if($insert)

                        {

                            $stock_data_row = array();

                            $categ_id = 0;

                            if($category_id){

                                $categ_id = $category_id;

                            }



                            if($selected_stock == 1){

                                $stock_data_row = $this->Model_products->itemExistInStock($categ_id, $product_id, $stock_unit_id);

                            }

                            else if($selected_stock == 2){

                                $stock_data_row = $this->Model_products->itemExistInOfficeStock($categ_id, $product_id, $stock_unit_id);

                            }



                            if($stock_data_row)

                            {

                                $unit_value = 1;

                                if($inputed_unit_id){

                                    $unit_value = $this->Model_products->fetchUnitValueData($inputed_unit_id)['unit_value'];

                                }

                                $data = array(

                                    'quantity' => $stock_data_row['quantity'] - ($this->input->post('qty')[$x] * $unit_value)

                                );

                                // update stock

                                if($selected_stock == 1){

                                    // update stock

                                    $this->db->where('id', $stock_data_row['id']);

                                    $this->db->update('items_stock', $data);

                                }

                                else if($selected_stock == 2){

                                    // update stock

                                    $this->db->where('id', $stock_data_row['id']);

                                    $this->db->update('office_items_stock', $data);

                                }

                            }

                        }

                    }//end for items

                    $this->session->set_flashdata('success', 'Successfully updated');

                    redirect('/Product/update_company_sale_order/'.$order_id, 'refresh');

                }

                else

                {

                    $data['page_title'] = "Update Company Sale Order";

                    $this->load->view('templates/header', $data);

                    $this->load->view('templates/header_menu');

                    $this->load->view('templates/side_menubar');



                    $data['sale_order_data'] = $this->Model_products->getCompanySaleOrdersData($order_id);

                    $data['sale_items_data'] = $this->Model_products->getCompanySaleItemsData($order_id);

                    $data['customer_data'] = $this->Model_Customers->getCustomerData($data['sale_order_data']['customer_id']);

                    $data['department_data'] = $this->Model_department->getDepartmentData($data['sale_order_data']['department_id']);

                    $products = array();

                    if($data['sale_order_data']['stock_type'] == 1){

                        $products = $this->Model_products->getStockProductData();

                    }

                    else if($data['sale_order_data']['stock_type'] == 2){

                        $products = $this->Model_products->getOfficeStockItemsData();

                    }

                    $data['products'] = $products;

                    $data['customers'] = $this->Model_Customers->getCustomerData();

                    $data['customer_deparment'] = $this->Model_department->getCustomerDeparment();

                    $data['units_data'] = $this->Model_products->getUnitsData();

                    $data['unit_data_values'] = $this->Model_products->getUnitValuesData();

                    // store it for showing it in s.qty

                    $x = 0;

                    $stock_data_array = array();

                    foreach ($data['sale_items_data'] as $key => $value) {

                        $stock_data_row = array();

                        if($data['sale_order_data']['stock_type'] == 1){

                            $stock_data_row = $this->Model_products->itemExistInStock($value['category_id'], $value['product_id'], $value['stock_unit_id']);

                        }

                        else if($data['sale_order_data']['stock_type'] == 2){

                            $stock_data_row = $this->Model_products->itemExistInOfficeStock($value['category_id'], $value['product_id'], $value['stock_unit_id']);

                        }

                        $stock_data_array[$x] = $stock_data_row;

                        $x += 1;

                    }

                    $data['stock_data_array'] = $stock_data_array;



                    $user_id = $this->session->userdata('id');

                    $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

                    $data['user_permission'] = unserialize($group_data['permission']);



                    $this->load->view('products/update_company_sale_order', $data);

                    $this->load->view('templates/footer');

                }

            }

            else{

                echo "<p style='background-color:red'>Not Found</p>";

            }

        }

    }



    public function remove_company_sale_order()

    {

         if(!in_array('deleteSaleOrderE', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $company_sale_order_id = $this->input->post('company_sale_order_id');

            $response = array();

            if($company_sale_order_id)

            {

                $previous_sale_order_stock_type = $this->Model_products->getCompanySaleOrdersData($company_sale_order_id)['stock_type'];



                $this->db->where('id', $company_sale_order_id);

                $this->db->delete('company_sales_order');

                $sale_items_data = $this->Model_products->getCompanySaleItemsData($company_sale_order_id);

                foreach ($sale_items_data as $key => $value)

                {

                    $categ_id = 0;

                    if($value['category_id']){

                        $categ_id = $value['category_id'];

                    }

                    $stock_data_row = array();

                    if($previous_sale_order_stock_type == 1)

                    {

                        $stock_data_row = $this->Model_products->itemExistInStock($categ_id, $value['product_id'], $value['stock_unit_id']);

                    }

                    elseif ($previous_sale_order_stock_type == 2)

                    {

                        $stock_data_row = $this->Model_products->itemExistInOfficeStock($categ_id, $value['product_id'], $value['stock_unit_id']);

                    }

                    $data = array(

                        'quantity' => $stock_data_row['quantity'] + $value['qty']

                    );



                    if($previous_sale_order_stock_type == 1){

                        $this->db->where('id', $stock_data_row['id']);

                        $this->db->update('items_stock', $data);

                    }

                    else if($previous_sale_order_stock_type == 2){

                        $this->db->where('id', $stock_data_row['id']);

                        $this->db->update('office_items_stock', $data);

                    }

                    // delete items

                    $this->db->where('id', $value['id']);

                    $delete = $this->db->delete('company_sales_order_items');

                }//endforeach

                if($delete == true) {

                    $response['success'] = true;

                    $response['location'] = "company_sales";

                    $this->session->set_flashdata('success', 'deleted Successfully');

                    $response['messages'] = "Successfully removed";

                }

                else {

                    $response['success'] = false;

                    $response['location'] = "";

                    $response['messages'] = "Error in the database while removing the product information";

                }

            }

            else {

                $response['success'] = false;

                $response['location'] = "";

                $response['messages'] = "Sale Order Id Does not exist. Refersh the page again!!";

            }

            echo json_encode($response);

        }

    }



    public function view_company_sale_order_items($order_id)

    {

        if(!in_array('viewSaleOrderE', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['sale_order_data'] = $this->Model_products->getCompanySaleOrdersData($order_id);

            if(!empty($data['sale_order_data'])){



                $data['page_title'] = "Sale Order Items";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');



                $data['sale_order_data'] = $this->Model_products->getCompanySaleOrdersData($order_id);

                $data['sale_items_data'] = $this->Model_products->getCompanySaleItemsData($order_id);



                $data['customer_data'] = $this->Model_Customers->getCustomerData($data['sale_order_data']['customer_id']);

                $data['department_data'] = $this->Model_department->getDepartmentData($data['sale_order_data']['department_id']);

                $products = array();

                if($data['sale_order_data']['stock_type'] == 1){

                    $products = $this->Model_products->getStockProductData();

                }

                else if($data['sale_order_data']['stock_type'] == 2){

                    $products = $this->Model_products->getOfficeStockItemsData();

                }

                $data['products'] = $products;

                $data['customers'] = $this->Model_Customers->getCustomerData();

                $data['customer_deparment'] = $this->Model_department->getCustomerDeparment();

                $data['units_data'] = $this->Model_products->getUnitsData();

                $data['unit_data_values'] = $this->Model_products->getUnitValuesData();



                // store it for showing it in s.qty

                $x = 0;

                $stock_data_array = array();

                foreach ($data['sale_items_data'] as $key => $value) {

                    $stock_data_row = array();

                    if($data['sale_order_data']['stock_type'] == 1){

                        $stock_data_row = $this->Model_products->itemExistInStock($value['category_id'], $value['product_id'], $value['stock_unit_id']);

                    }

                    else if($data['sale_order_data']['stock_type'] == 2){

                        $stock_data_row = $this->Model_products->itemExistInOfficeStock($value['category_id'], $value['product_id'], $value['stock_unit_id']);

                    }

                    $stock_data_array[$x] = $stock_data_row;

                    $x += 1;

                }

                $data['stock_data_array'] = $stock_data_array;



                $user_id = $this->session->userdata('id');

                $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

                $data['user_permission'] = unserialize($group_data['permission']);



                $this->load->view('products/view_company_sale_order_items', $data);

                $this->load->view('templates/footer');

            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }



    }



    public function print_company_sales_order()

    {

        if(!in_array('printSaleOrderE', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $result = array();

            date_default_timezone_set("Asia/Karachi");

            $print_date = date('d/m/Y');

            $user_id = $this->session->userdata('id');

            $user_data = $this->Model_users->getUserData($user_id);

            $data = $this->Model_products->getCompanySaleOrdersData();



            if(!empty($data)){



              $html = '<!DOCTYPE html>

              <html lang="en">

              <head>

              <meta charset="UTF-8">

              <meta name="viewport" content="width=device-width, initial-scale=1.0">

              <meta http-equiv="X-UA-Compatible" content="ie=edge">

              <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

              <style>

              .invoice-title h2, .invoice-title h3 {

                display: inline-block;

            }



            .table > tbody > tr > .no-line {

                border-top: none;

            }



            .table > thead > tr > .no-line {

                border-bottom: none;

            }



            .table > tbody > tr > .thick-line {

                border-top: 2px solid;

            }

            </style>



            <title>TBM - Company Sale Orders Print</title>

            </head>

            <body onload="window.print();">

            <div class="container">

            <div class="row">

            <div class="col-xs-12">

            <div class="invoice-title text-center">

            <h3>TBM Automobile Private Ltd</h3>

            </div>

            <hr>

            <div class="row">

            <div class="col-xs-6">

            <address style="text-transform:capitalize">

            <strong>Printed By:</strong><br>

            '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

            </address>

            </div>

            <div class="col-xs-6 text-right">

            <address>

            <strong>Print Date:</strong><br>

            '.date("d-m-Y").'<br>



            </address>

            </div>

            </div>

            </div>

            </div>



            <div class="row">

            <div class="col-md-12">



            <div class="table-responsive">

            <table class="table table-condensed table-bordered">

            <thead>

            <tr>

            <th><strong>#</strong></th>

            <th><strong>Bill no</strong></th>

            <th><strong>DateTime</strong></th>

            <th><strong>Customer</strong></th>

            <th><strong>Department</strong></th>

            <th><strong>Products</strong></th>

            <th><strong>Stock</strong></th>

            <th><strong>Created By</strong></th>

            <th><strong>Remarks</strong></th>

            </tr>

            </thead>

            <tbody>';

            $counter = 1;

            foreach ($data as $key => $value) {

                $customer_data = $this->Model_Customers->getCustomerData($value['customer_id']);

                $department_data = $this->Model_department->getDepartmentData($value['department_id']);

                $count_total_item = $this->Model_products->countCompanySaleOrderItem($value['id']);



                $date = date('d-m-Y', strtotime($value['date_time']));

                $time = date('h:i a', strtotime($value['date_time']));

                $date_time = $date.' '.$time;

                $stock_type = '';

                if ($value['stock_type'] == 1) {

                    $stock_type = 'Factory Stock';

                }

                else if ($value['stock_type'] == 2) {

                    $stock_type = 'Office Stock';

                }



                $remarks = '';

                if($value['remarks']){

                    $remarks = $value['remarks'];

                }

                else{

                    $remarks = 'Not Mentioned';

                }

                //

                $userdata = $this->Model_users->getUserData($value['user_id']);

                $created_by = '<span style="text-transform:capitalize">'.$userdata['firstname'].' '.$userdata['lastname'].'</span>';



                $html .= '<tr>

                <td>'.$counter++.'</td>

                <td>'.$value['bill_no'].'</td>

                <td>'.$date_time.'</td>

                <td>'.$customer_data['full_name'].'</td>

                <td>'.$department_data['department_name'].'</td>

                <td>'.$count_total_item.'</td>

                <td>'.$stock_type.'</td>

                <td>'.$created_by.'</td>

                <td>'.$remarks.'</td>



                </tr>';

            }

            $html .='



            </tbody>



            </table>

            </div>

            </div>

            </div>



            </div>

            </body>



            <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

            <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



            </html>';

            echo $html;

        }

        else{

            $html = '<!-- Main content -->

            <!DOCTYPE html>

            <html>

            <head>

            <meta charset="utf-8">

            <meta http-equiv="X-UA-Compatible" content="IE=edge">

            <title>TBM Automobile Private Ltd | Company Sale Orders Print</title>

            <!-- Tell the browser to be responsive to screen width -->

            <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

            <!-- Bootstrap 3.3.7 -->

            <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">

            <!-- Font Awesome -->

            <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">

            <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">

            </head>

            <body onload="window.print();">



            <div class="wrapper">

            <section class="invoice">

            <!-- title row -->

            <div class="row">

            <div class="col-xs-12">

            <h2 class="page-header">

            TBM Automobile Private Ltd

            </h2>

            </div>

            <!-- /.col -->

            </div>

            <!-- Table row -->

            <div class="row">

            <div class="col-xs-12 table-responsive">

            <table class="table table-striped">

            <thead>

            <tr>

            <th><strong>#</strong></th>

            <th><strong>Bill no</strong></th>

            <th><strong>DateTime</strong></th>

            <th><strong>Customer</strong></th>

            <th><strong>Department</strong></th>

            <th><strong>Products</strong></th>

            </tr>

            </thead>

            <tbody>';



            $html .= '<tr>



            </tr>';



            $html .= '</tbody>

            </table>

            </div>

            <!-- /.col -->

            </div>

            <!-- /.row -->

            </section>

            <!-- /.content -->

            </div>

            </body>

            </html>';

            echo $html;

        }

    }

}



    public function print_company_sale_invoice($order_id)

    {

        if(!in_array('printSaleOrderE', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['sale_order_data'] = $this->Model_products->getCompanySaleOrdersData($order_id);

            if(!empty($data['sale_order_data']))

            {



                $sale_order_data = $this->Model_products->getCompanySaleOrdersData($order_id);

                $stock_type = "";

                if($sale_order_data['stock_type'] == 1){

                    $stock_type = "Factory Stock";

                }

                else if($sale_order_data['stock_type'] == 2){

                    $stock_type = "Office Stock";

                }

                $sale_items_data = $this->Model_products->getCompanySaleItemsData($order_id);

                $units_data = $this->Model_products->getUnitsData();

                $unit_data_values = $this->Model_products->getUnitValuesData();

                $user_data = $this->Model_users->getUserData($sale_order_data['user_id']);



                $customer_data = $this->Model_Customers->getCustomerData($sale_order_data['customer_id']);

                $department_data = $this->Model_department->getDepartmentData($sale_order_data['department_id']);



                $company_info = $this->Model_company->getCompanyData(1);



                $order_date = $sale_order_data['date_time'];

                if($sale_order_data['remarks']){

                    $remarks = $sale_order_data['remarks'];

                }

                else{

                    $remarks = "No remarks Available!";

                }



                $html = '<!DOCTYPE html>

                <html lang="en">

                <head>

                <meta charset="UTF-8">

                <meta name="viewport" content="width=device-width, initial-scale=1.0">

                <meta http-equiv="X-UA-Compatible" content="ie=edge">

                <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

                <style>

                .invoice-title h2, .invoice-title h3 {

                    display: inline-block;

                }

                </style>



                <title>Company Sale Invoice</title>

                </head>

                <body onload="window.print();">

                <div class="container">

                <div class="row">

                <div class="col-xs-12">

                <div class="invoice-title text-center">

                <h3>TBM Automobile Private Ltd</h3><h4 class="pull-right">'.$sale_order_data['bill_no'].'</h4>

                </div>

                <hr>

                <div class="row">

                <div class="col-xs-6">

                <address>

                <strong>Customer Name:</strong></br>

                '.$customer_data['full_name'].'<br>

                <strong>Customer Department:</strong><br>

                '.$department_data['department_name'].'<br>

                <strong>Customer Address:</strong><br>

                '.$customer_data['address'].'<br>

                <strong>Customer Contact:</strong><br>

                '.$customer_data['phone_number'].'<br>

                </address>

                </div>

                <div class="col-xs-6 text-right">

                <address style="text-transform:capitalize">

                <strong>Created By:</strong><br>

                '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

                </address>

                </div>

                </div>

                </div>

                </div>



                <div class="row">

                <div class="col-md-12">



                <div class="table-responsive">

                <table class="table table-condensed table-bordered">

                <thead>

                <tr>

                <td><strong>Item</strong></td>

                <td><strong>Unit</strong></td>

                <td><strong>Stock</strong></td>

                <td><strong>Quantity</strong></td>

                </tr>

                </thead>

                <tbody>';

                foreach ($sale_items_data as $k => $v) {



                    $product_data = $this->Model_products->getAllProductData($v['product_id']);

                    $product_category = $this->Model_products->ProductCategory($v['product_id'], $v['category_id']);

                    $category_data = '';

                    if(!empty($product_category) && $product_category['category_id'] > 0){

                        $category_data = $this->Model_category->getAllCategoryData($product_category['category_id']);

                    }

                    $unit_name = 'Not Mentioned';

                    $unit_value = 1;

                    $units_data = $this->Model_products->getUnitsData();

                    foreach ($units_data as $key => $value)

                    {

                        $unit_id = $v['unit_id'];

                        $unit_data_values = $this->Model_products->getUnitValuesData();

                        if($value['id'] == $unit_id)

                        {

                            foreach ($unit_data_values as $key_2 => $value_2)

                            {

                                if($value['id'] == $value_2['unit_id'])

                                {

                                    $unit_value = $value_2['unit_value'];

                                    $unit_name = $value['unit_name'];

                                    break;

                                }

                            }

                        }

                    }

                    $item_name = '';

                    if($category_data)

                    {

                        $item_name = $product_data['name']. ' &#8212 ' .$category_data['name'];

                    }

                    else

                    {

                        $item_name = $product_data['name'];

                    }

                    $html .= '<tr>

                    <td>'.$item_name.'</td>

                    <td>'.$unit_name.'</td>

                    <td>'.$stock_type.'</td>

                    <td>'.($v['qty']/$unit_value).'</td>



                    </tr>';

                }

                $html .='

                </tbody>

                </table>

                </div>

                <div class="">

                            <label><br>Remarks: </label>'.$remarks.'

                            <br>

                            <div style="width: 50%; float: right;"><label style="width: 30%; float: left">Seller Signature:</label><div style="width: 70%; float: right"><br><hr></div></div>

                            <div style="width: 50%; float: right"><label style="width: 30%; float: left">Customer Signature:</label><div style="width: 70%; float: right"><br><hr></div></div>

                            <div style="width: 30%; float: left"><label style="width: 30%; float: left">Date:</label><div style="width: 70%; float: right">'.date("Y/m/d", strtotime($sale_order_data['date_time'])).'</div></div>

                    </div>

                </body>



                <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

                <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



                </html>';

                echo $html;

            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }

    }



    public function mark_purchase_order_returns($order_id)

    {

        if(!in_array('viewPurchaseReturn', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $purchase_order_data = $this->Model_products->getPurchaseOrdersData($order_id);

            if(!empty($purchase_order_data))

            {

                $data['page_title'] = "Purchase Product Returns";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');



                $user_id = $this->session->userdata('id');

                $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

                $data['user_permission'] = unserialize($group_data['permission']);



                $data['vendor_data'] = $this->Model_supplier->getSupplierData();

                $data['order_id'] = $order_id;



                $this->load->view('products/mark_purchase_order_returns', $data);

                $this->load->view('templates/footer');

            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }

    }



    public function fetchPurchaseReturnsData($order_id)

    {

        $result = array('data' => array());

        $data = $this->Model_products->fetchPurchaseReturnsData($order_id);

        $counter = 1;

        foreach ($data as $key => $value) {



            $product_data = $this->Model_products->getAllProductData($value['product_id']);

            $category_id = $value['category_id'];

            $category_name = '';

            if($category_id == 0)

            {

                $category_name = "Nill";

            }

            else

            {

                $category_data = $this->Model_category->getAllCategoryData($category_id);

                $category_name = $category_data['name'];

            }

            $supplier_data = $this->Model_supplier->getAllSupplierData($value['vendor_id']);



            // button

            $buttons = '';

            if(in_array('updatePurchaseReturn', $this->permission))

            {

                $buttons .= ' <a href="'.base_url().'index.php/Product/edit_purchase_return/'.$value['id'].'" title="Edit Purchase Returns"><i class="glyphicon glyphicon-pencil"></i></a>';

            }

            if(in_array('deletePurchaseReturn', $this->permission))

            {

                $buttons .= ' <a title="Delete Purchase Returns" data-toggle="modal" href="#removeModal" onclick="removeFunc('.$value['id'].')"><i class="glyphicon glyphicon-trash"></i></a>';

            }



            $status = '<span class="label label-warning">Active</span>';

            $reason = (empty($value['reason'])) ? 'Not Specified': $value['reason'];

            $result['data'][$key] = array(

                $counter++,

                $category_name,

                $product_data['name'],

                $supplier_data['first_name']. ' '. $supplier_data['last_name'],

                $value['qty'],

                $reason,

                $value['amount'],

                $buttons

            );

        } // /foreach

        echo json_encode($result);

    }



    // add purchase return form

    public function purchase_products_return($order_id)

    {

        if(!in_array('createPurchaseReturn', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['purchase_order_data'] = $this->Model_products->getPurchaseOrdersData($order_id);

            if(!empty($data['purchase_order_data']))

            {

                $data['page_title'] = "Purchase Product Returns";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $data['purchase_order_data'] = $this->Model_products->getPurchaseOrdersData($order_id);

                $data['purchase_items_data'] = $this->Model_products->getPurchaseItemsData($order_id);

                $data['purchase_returns_data'] = $this->Model_products->fetchPurchaseReturnsData($order_id);

                $vendor_id = $data['purchase_items_data'][0]['vendor_id'];

                $data['loan_deductions'] = $this->Model_loan->getLoanDeductions($order_id);

                $data['vendor_data'] = $this->Model_supplier->getAllSupplierData($vendor_id);

                $data['vendor_products'] = $this->Model_products->getVendorProductsData($vendor_id);

                $data['units_data'] = $this->Model_products->getUnitsData();

                $data['unit_data_values'] = $this->Model_products->getUnitValuesData();



                $this->load->view('products/purchase_products_return', $data);

                $this->load->view('templates/footer');



            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }



    }

    // add purchase return method

    public function add_purchase_products_return($order_id)

    {

        if($order_id){

            $purchase_items_data = $this->Model_products->getPurchaseItemsData($order_id);

            $input_return_items =  $this->input->post('return_qty');

            $count_return_product = count($input_return_items);

            // list of all products where return qty is set

            // check if this order id has this much amount of the item

            for($i = 0; $i < $count_return_product; $i++)

            {

                $product_id = explode("-",$this->input->post('product')[$i])[0];

                $category_id = explode("-",$this->input->post('product')[$i])[1];

                if($category_id == "" || $category_id == " "){

                    $category_id = 0;

                }

                $return_qty = $input_return_items[$i];

                if(empty($return_qty)){

                    continue;

                }

                else

                {

                    if(!empty($product_id) && $return_qty)

                    {

                        $unit_id = $this->input->post('selected_unit')[$i];

                        foreach ($purchase_items_data as $key => $value)

                        {

                            if($value['product_id'] == $product_id && $value['category_id'] == $category_id && $value['unit_id'] == $unit_id)

                            {

                                $unit_data_values = $this->Model_products->getUnitValuesData();

                                $unit_value = 1;

                                foreach($unit_data_values as $k => $v){

                                    if($v['unit_id'] == $unit_id){

                                        $unit_value = $v['unit_value'];

                                        break;

                                    }

                                }

                                $order_items_qty = $value['qty'] * $unit_value;

                                if($return_qty > $order_items_qty)

                                {

                                    $product_data = $this->Model_products->getProductData($value['product_id']);

                                    $category_name = '';

                                    if($value['category_id'] == 0)

                                    {

                                        $category_name = "";

                                    }

                                    else

                                    {

                                        $category_data = $this->Model_category->getCategoryData($category_id);

                                        $category_name = ' — '.$category_data['name'];

                                    }

                                    $this->session->set_flashdata('errors', 'The item amount you entered is larger than the amount of item in the order for '. $product_data['name'] . $category_name);

                                    redirect('/Product/purchase_products_return/'.$order_id.'', 'refresh');

                                }

                            }

                        }

                    }

                }

            }

            // list of all products where return qty is set

            $return_products_amount = 0;

            for($i = 0; $i < $count_return_product; $i++)

            {

                $product_id = explode("-",$this->input->post('product')[$i])[0];

                $category_id = explode("-",$this->input->post('product')[$i])[1];

                if($category_id == "" || $category_id == " "){

                    $category_id = 0;

                }

                $return_qty = $input_return_items[$i];

                if(empty($return_qty)){

                    continue;

                }

                else

                {

                    if(!empty($product_id) && $return_qty)

                    {

                        $unit_id = $this->input->post('selected_unit')[$i];

                        // insert into returns table

                        $data = array(

                            'category_id' => $category_id,

                            'product_id' => $product_id,

                            'vendor_id' => $this->input->post('vendor_id'),

                            'product_order_id' => $order_id,

                            'product_paid_order_id' => 0,

                            'qty' => $return_qty,

                            'unit_id' => $unit_id,

                            'amount' => $this->input->post('amount_value')[$i],

                            'reason' => $this->input->post('reason')[$i],

                            'status' => 0

                        );

                        $return_products_amount += $this->input->post('amount_value')[$i];



                        $item_exist = $this->Model_products->itemExistInStock($category_id, $product_id, $unit_id);

                        if($item_exist['quantity'] >= $return_qty)

                        {

                            $insert = $this->db->insert('purchase_returns', $data);

                            if($insert)

                            {

                                // remove from stock

                                $data = array(

                                    'quantity' => $item_exist['quantity'] - $return_qty

                                );

                                $this->db->where('id', $item_exist['id']);

                                $this->db->update('items_stock', $data);

                            }

                        }

                        else

                        {

                            // Insufficient amount in stock

                            $this->session->set_flashdata('errors', 'Insufficient amount in stock!');

                            redirect('/Product/purchase_products_return/'.$order_id.'', 'refresh');

                        }

                    }

                }

            }

            // end foreach

            if($insert){

                // update vendor balance

                $vendor_data = $this->Model_supplier->getSupplierData($this->input->post('vendor_id'));

                $balance = $vendor_data['balance'] - $return_products_amount;

                $data = array(

                    'balance' => $balance

                );

                $this->db->where('id', $this->input->post('vendor_id'));

                $this->db->update('supplier', $data);

            }



            $this->session->set_flashdata('success', 'Successfully added!');

            redirect('/Product/mark_purchase_order_returns/'.$order_id.'', 'refresh');

        }

        else{

            echo "<p style='color:red'>Insufficient Order information</p>";

        }

    }

    // update purchase return

    public function edit_purchase_return($id)

    {

        if(!in_array('updatePurchaseReturn', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $purchase_return_data = $this->Model_products->getPurchaseReturnsData($id);

            if(!empty($purchase_return_data))

            {

                //purchase_return_row = id=>$id

                $purchase_return_data = $this->Model_products->getPurchaseReturnsData($id);

                $this->form_validation->set_rules('product[]', 'Product name', 'trim|required');

                $this->form_validation->set_rules('return_qty[]', 'Return Quantity', 'trim|required');

                if($this->form_validation->run() == TRUE)

                {

                    $purchase_items_data = $this->Model_products->getPurchaseItemsData($purchase_return_data['product_order_id']);

                    // check if this order id has this much amount of the item

                    $product_id = explode("-",$this->input->post('product')[0])[0];

                    $category_id = explode("-",$this->input->post('product')[0])[1];

                    if($category_id == "" || $category_id == " "){

                        $category_id = 0;

                    }

                    $return_qty = $this->input->post('return_qty')[0];

                    $unit_id = $this->input->post('selected_unit')[0];

                    foreach ($purchase_items_data as $key => $value)

                    {

                        if($value['product_id'] == $product_id && $value['category_id'] == $category_id && $value['unit_id'] == $unit_id)

                        {

                            $unit_data_values = $this->Model_products->getUnitValuesData();

                            $unit_value = 1;

                            foreach($unit_data_values as $k => $v){

                                if($v['unit_id'] == $unit_id){

                                    $unit_value = $v['unit_value'];

                                    break;

                                }

                            }

                            $order_item_qty = $value['qty'] * $unit_value;

                            if($return_qty > $order_item_qty)

                            {

                                $product_data = $this->Model_products->getProductData($value['product_id']);

                                $category_name = '';

                                if($value['category_id'] == 0)

                                {

                                    $category_name = "";

                                }

                                else

                                {

                                    $category_data = $this->Model_category->getCategoryData($category_id);

                                    $category_name = ' — '.$category_data['name'];

                                }

                                $this->session->set_flashdata('errors', 'The item amount you entered is larger than the amount of item in the order for '. $product_data['name'] . $category_name);

                                redirect('/Product/edit_purchase_return/'.$id, 'refresh');

                            }

                        }

                    }



                    // else no problem update

                    $prev_qty = $purchase_return_data['qty'];

                    // check if stock have that much qty

                    $item_exist = $this->Model_products->itemExistInStock($category_id, $product_id, $unit_id);

                    if(($item_exist['quantity'] + $prev_qty) >= $return_qty)

                    {

                        $data = array(

                            'qty' => $return_qty,

                            'reason' => $this->input->post('reason')[0],

                            'amount' => $this->input->post('amount_value')[0]

                        );

                        $this->db->where('id', $purchase_return_data['id']);

                        $update = $this->db->update('purchase_returns', $data);

                        if($update){

                            // update stock

                            $data = array(

                                'quantity' => $item_exist['quantity'] + $prev_qty - $return_qty

                            );

                            $this->db->where('id', $item_exist['id']);

                            $this->db->update('items_stock', $data);



                            if($update){

                                // update vendor balance

                                $vendor_data = $this->Model_supplier->getSupplierData($purchase_return_data['vendor_id']);

                                $balance = $vendor_data['balance'] + $purchase_return_data['amount'];

                                $data = array(

                                    'balance' => $balance - $this->input->post('amount_value')[0]

                                );

                                $this->db->where('id', $purchase_return_data['vendor_id']);

                                $this->db->update('supplier', $data);

                            }



                            $this->session->set_flashdata('success', 'Successfully updated!');

                            redirect('/Product/edit_purchase_return/'.$id, 'refresh');

                        }

                    }

                    else

                    {

                        // Insufficient amount in stock

                        $this->session->set_flashdata('errors', 'Insufficient amount in stock!');

                        redirect('/Product/edit_purchase_return/'.$id, 'refresh');

                    }

                }

                else

                {

                    $data['page_title'] = "Edit Purchase Return";

                    $this->load->view('templates/header', $data);

                    $this->load->view('templates/header_menu');

                    $this->load->view('templates/side_menubar');



                    $data['purchase_return_data'] = $purchase_return_data;

                    $data['purchase_order_data'] = $this->Model_products->getPurchaseOrdersData($purchase_return_data['product_order_id']);

                    $data['purchase_items_data'] = $this->Model_products->getPurchaseItemsData($purchase_return_data['product_order_id']);

                    $vendor_id = $data['purchase_items_data'][0]['vendor_id'];

                    $data['vendor_data'] = $this->Model_supplier->getSupplierData($vendor_id);

                    $data['vendor_products'] = $this->Model_products->getVendorProductsData($vendor_id);

                    $data['units_data'] = $this->Model_products->getUnitsData();

                    $data['unit_data_values'] = $this->Model_products->getUnitValuesData();

                    $this->load->view('products/edit_purchase_return', $data);

                    $this->load->view('templates/footer');

                }

            }

            else

            {

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }

    }

    // delete purchase return

    public function remove_purchase_return_item()

    {

        $return_item_id = $this->input->post('id');

        $response = array();

        if($return_item_id) {

            $purchase_return_data = $this->Model_products->getPurchaseReturnsData($return_item_id);

            $item_exist = $this->Model_products->itemExistInStock($purchase_return_data['category_id'], $purchase_return_data['product_id'], $purchase_return_data['unit_id']);

            if($item_exist)

            {

                $data = array(

                    'quantity' => $item_exist['quantity'] + $purchase_return_data['qty']

                );

                $this->db->where('id', $item_exist['id']);

                $this->db->update('items_stock', $data);



                $this->db->where('id', $return_item_id);

                $delete = $this->db->delete('purchase_returns');

                if($delete) {

                    if($delete){

                        // update vendor balance

                        $vendor_data = $this->Model_supplier->getSupplierData($purchase_return_data['vendor_id']);

                        $balance = $vendor_data['balance'] + $purchase_return_data['amount'];

                        $data = array(

                            'balance' => $balance

                        );

                        $this->db->where('id', $purchase_return_data['vendor_id']);

                        $this->db->update('supplier', $data);

                    }

                    $response['success'] = true;

                    $response['messages'] = "Successfully removed";

                }

                else {

                    $response['success'] = false;

                    $response['messages'] = "Error in the database while removing the product information";

                }

            }

            else

            {

                $response['success'] = false;

                $response['messages'] = "This item does not exist in stock!";

            }

        }

        else {

            $response['success'] = false;

            $response['messages'] = "Refersh the page again!!";

        }

        echo json_encode($response);

    }



    public function print_purchase_order_returns($order_id)

    {

        if(!in_array('printPurchaseReturn', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data = $this->Model_products->fetchPurchaseReturnsData($order_id);

            if(!empty($data)){

                $data = $this->Model_products->fetchPurchaseReturnsData($order_id);

                $user_id = $this->session->userdata('id');

                $user_data = $this->Model_users->getUserData($user_id);

                date_default_timezone_set("Asia/Karachi");

                $print_date = date('d-m-Y');



                $html = '<!DOCTYPE html>

                <html lang="en">

                <head>

                <meta charset="UTF-8">

                <meta name="viewport" content="width=device-width, initial-scale=1.0">

                <meta http-equiv="X-UA-Compatible" content="ie=edge">

                <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

                <style>

                .invoice-title h2, .invoice-title h3 {

                    display: inline-block;

                }



                .table > tbody > tr > .no-line {

                    border-top: none;

                }



                .table > thead > tr > .no-line {

                    border-bottom: none;

                }



                .table > tbody > tr > .thick-line {

                    border-top: 2px solid;

                }

                </style>



                <title>TBM - Product Items Print</title>

                </head>

                <body onload="window.print();">

                <div class="container">

                <div class="row">

                <div class="col-xs-12">

                <div class="invoice-title text-center">

                <h3>TBM Automobile Private Ltd</h3>

                </div>

                <hr>

                <div class="row">

                <div class="col-xs-6">

                <address style="text-transform:capitalize">

                <strong>Printed By:</strong><br>

                '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

                </address>

                </div>

                <div class="col-xs-6 text-right">

                <address>

                <strong>Print Date:</strong><br>

                '.date("d-m-Y").'<br>



                </address>

                </div>

                </div>

                </div>

                </div>



                <div class="row">

                <div class="col-md-12">

                <div class="table-responsive">

                <table style="width: 100%;" class="table table-condensed table-bordered">

                <thead>

                <tr>

                <td><strong>#</strong></td>

                <td><strong>OrderId</strong></td>

                <td><strong>Category</strong></td>

                <td><strong>Item Name</strong></td>

                <td><strong>Vendor Name</strong></td>

                <td><strong>Return Quantity</strong></td>

                <td><strong>Reason</strong></td>

                <td><strong>Amount</strong></td>

                </tr>

                </thead>

                <tbody>';

                $counter = 1;

                foreach ($data as $key => $value) {

                    $product_data = $this->Model_products->getAllProductData($value['product_id']);

                    $category_id = $value['category_id'];

                    $category_name = '';

                    if($category_id == 0)

                    {

                        $category_name = "Nill";

                    }

                    else

                    {

                        $category_data = $this->Model_category->getAllCategoryData($category_id);

                        $category_name = $category_data['name'];

                    }

                    $supplier_data = $this->Model_supplier->getSupplierData($value['vendor_id']);

                    $reason = (empty($value['reason'])) ? 'Not Specified': $value['reason'];

                    $html .= '<tr>

                    <td>'.$counter++.'</td>

                    <td>'.$order_id.'</td>

                    <td>'.$category_name.'</td>

                    <td>'.$product_data['name'].'</td>

                    <td>'.$supplier_data['first_name']. ' '. $supplier_data['last_name'].'</td>

                    <td>'.$value['qty'].'</td>

                    <td>'.$reason.'</td>

                    <td>'.$value['amount'].'</td>



                    </tr>';

                }

                $html .='



                </tbody>



                </table>

                </div>

                </div>

                </div>

                </div>

                </body>



                <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

                <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



                </html>';

                echo $html;

            }

            else{

                $data['page_title'] = "404 - Not Found";

                $this->load->view('templates/header', $data);

                $this->load->view('templates/header_menu');

                $this->load->view('templates/side_menubar');

                $this->load->view('errors/404_not_found');

            }

        }

    }



    public function manage_sale_prices()

    {

        if(!in_array('recordSalePricesNE', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $data['page_title'] = "Sale Prices";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $data['product_category_data'] = $this->Model_products->getProductCategoryData();

            $data['units_data'] = $this->Model_products->getUnitsData();

            $user_id = $this->session->userdata('id');

            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);

            $data['user_permission'] = unserialize($group_data['permission']);



            $this->load->view('products/manage_sale_prices', $data);



            $this->load->view('templates/footer');

        }



    }



    public function fetchSalePricesData()

    {

        $result = array('data' => array());

        $data = $this->Model_products->getSalePricesData();



        $counter = 1;

        foreach ($data as $key => $value) {



            $category_name = '';

            if($value['category_id'])

            {

                $category_data = $this->Model_category->getCategoryData($value['category_id']);

                if(!empty($category_data)){

                    $category_name = $category_data['name'];

                }

            }

            else

            {

                $category_name = 'Nill';

            }

            // button

            $buttons = '';

            if(in_array('updateSalePricesNE', $this->permission)){

                $buttons .= '<a title="Edit Sale Price" onclick="editFunc('.$value['sale_prices_id'].')" data-toggle="modal" href="#editModal"><i class="glyphicon glyphicon-pencil"></i></a>';

            }

            if(in_array('deleteSalePricesNE', $this->permission)){

                $buttons .= ' <a title="Delete Sale Price" onclick="removeFunc('.$value['sale_prices_id'].')" data-toggle="modal" href="#removeModal"><i class="glyphicon glyphicon-trash"></i></a>';

            }



            $date = date('d-m-Y', strtotime($value['sale_prices_datetime']));

            $time = date('h:i a', strtotime($value['sale_prices_datetime']));

            $date_time = $date . ' ' . $time;

            $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];

            $result['data'][$key] = array(

                $counter++,

                $date_time,

                $category_name,

                $value['product_name'],

                $unit_name,

                $value['price'],

                $buttons

            );

        } // /foreach



        echo json_encode($result);

    }



    public function create_sale_price()

    {

        $response = array();

        $this->form_validation->set_rules('sale_price', 'Sale Pirce', 'trim|required');



        if ($this->form_validation->run() == TRUE)

        {

            // true case

            $product_id = explode("-",$this->input->post('select_product'))[0];

            $category_id = explode("-",$this->input->post('select_product'))[1];

            date_default_timezone_set("Asia/Karachi");

            $data = array(

                'category_id' => $category_id,

                'product_id' => $product_id,

                'unit_id' => $this->input->post('select_unit'),

                'price' => $this->input->post('sale_price'),

                'date_time' => date('Y-m-d H:i:s a')

            );



            $create = $this->db->insert('sale_prices', $data);

            if($create == true) {

                $response['success'] = true;

                $response['messages'] = 'Succesfully created';

            }

            else {

                $response['success'] = false;

                $response['messages'] = 'Error in the database while creating the sale price information';

            }

        }

        else

        {

            // false case

            $response['success'] = false;

            foreach ($_POST as $key => $value) {

                $response['messages'][$key] = form_error($key);

            }

        }

        echo json_encode($response);

    }



    public function update_sale_price($id)

    {

        $response = array();



        if($id) {

            $this->form_validation->set_rules('edit_sale_price', 'Sale Pirce', 'trim|required');



            $this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');



            if ($this->form_validation->run() == TRUE) {

                $data = array(

                    'product_id' => $this->input->post('edit_select_product'),

                    'unit_id' => $this->input->post('edit_select_unit'),

                    'price' => $this->input->post('edit_sale_price')

                );

                $this->db->where('id', $id);

                $update = $this->db->update('sale_prices', $data);

                if($update == true) {

                    $response['success'] = true;

                    $response['messages'] = 'Succesfully updated';

                }

                else {

                    $response['success'] = false;

                    $response['messages'] = 'Error in the database while updated the sale price information';

                }

            }

            else {

                $response['success'] = false;

                foreach ($_POST as $key => $value) {

                    $response['messages'][$key] = form_error($key);

                }

            }

        }

        else {

            $response['success'] = false;

            $response['messages'] = 'Error please refresh the page again!!';

        }



        echo json_encode($response);

    }



    public function fetchSalePriceDataById($id)

    {

        if($id) {

            $data = $this->Model_products->getSalePricesData($id);

            echo json_encode($data);

        }

        return false;

    }



    public function remove_sale_price()

    {

        $sale_price_id = $this->input->post('sale_price_id');

        $response = array();

        if($sale_price_id) {



            $sale_price_data = $this->Model_products->getSalePricesData($sale_price_id);

            $this->db->where('id', $sale_price_id);

            $data = array('is_deleted' => 1);

            $delete = $this->db->update('sale_prices');

            if($delete == true)

            {

                $response['success'] = true;

                $response['messages'] = "Successfully removed";

            }

            else {

                $response['success'] = false;

                $response['messages'] = "Error in the database while removing the sale price information";

            }

        }

        else {

            $response['success'] = false;

            $response['messages'] = "Refresh the page again!!";

        }

        echo json_encode($response);

    }



    public function print_sale_prices()

    {

        if(!in_array('printSalePricesNE', $this->permission)) {

            $data['page_title'] = "No Permission";

            $this->load->view('templates/header', $data);

            $this->load->view('templates/header_menu');

            $this->load->view('templates/side_menubar');

            $this->load->view('errors/forbidden_access');

        }

        else

        {

            $result = array();

            date_default_timezone_set("Asia/Karachi");

            $print_date = date('d/m/Y');

            $user_id = $this->session->userdata('id');

            $user_data = $this->Model_users->getUserData($user_id);

            $data = $this->Model_products->getSalePricesData();



            if(!empty($data)){



              $html = '<!DOCTYPE html>

              <html lang="en">

              <head>

              <meta charset="UTF-8">

              <meta name="viewport" content="width=device-width, initial-scale=1.0">

              <meta http-equiv="X-UA-Compatible" content="ie=edge">

              <link href="'.base_url('assets/dist/css/invoice_bootstrap.css').'" rel="stylesheet" id="bootstrap-css">

              <style>

              .invoice-title h2, .invoice-title h3 {

                display: inline-block;

            }



            .table > tbody > tr > .no-line {

                border-top: none;

            }



            .table > thead > tr > .no-line {

                border-bottom: none;

            }



            .table > tbody > tr > .thick-line {

                border-top: 2px solid;

            }

            </style>



            <title>TBM - Sale Prices Print</title>

            </head>

            <body onload="window.print();">

            <div class="container">

            <div class="row">

            <div class="col-xs-12">

            <div class="invoice-title text-center">

            <h3>TBM Automobile Private Ltd</h3>

            </div>

            <hr>

            <div class="row">

            <div class="col-xs-6">

            <address style="text-transform:capitalize">

            <strong>Printed By:</strong><br>

            '.$user_data['firstname']. ' ' .$user_data['lastname'].'<br>

            </address>

            </div>

            <div class="col-xs-6 text-right">

            <address>

            <strong>Print Date:</strong><br>

            '.date("d-m-Y").'<br>



            </address>

            </div>

            </div>

            </div>

            </div>



            <div class="row">

            <div class="col-md-12">



            <div class="table-responsive">

            <table class="table table-condensed table-bordered">

            <thead>

            <tr>

            <th><strong>#</strong></th>

            <th><strong>DateTime</strong></th>

            <th><strong>Category</strong></th>

            <th><strong>Item Name</strong></th>

            <th><strong>Unit</strong></th>

            <th><strong>Price</strong></th>

            </tr>

            </thead>

            <tbody>';

            $counter = 1;

            foreach ($data as $key => $value) {

                $category_name = '';

                if($value['category_id'])

                {

                    $category_data = $this->Model_category->getCategoryData($value['category_id']);

                    if(!empty($category_data)){

                        $category_name = $category_data['name'];

                    }

                }

                else

                {

                    $category_name = 'Nill';

                }

                $unit_name = $this->Model_products->getUnitsData($value['unit_id'])['unit_name'];

                $html .= '<tr>

                <td>'.$counter++.'</td>

                <td>'.$value['sale_prices_datetime'].'</td>

                <td>'.$category_name.'</td>

                <td>'.$value['product_name'].'</td>

                <td>'.$unit_name.'</td>

                <td>'.$value['price'].'</td>



                </tr>';

            }

            $html .='



            </tbody>



            </table>

            </div>

            </div>

            </div>



            </div>

            </body>



            <script src="'.base_url('assets/dist/js/invoice_bootstrap.js').'"></script>

            <script src="'.base_url('assets/dist/js/invoice_jQuery.js').'"></script>



            </html>';

            echo $html;

        }

        else{

            $html = '<!-- Main content -->

            <!DOCTYPE html>

            <html>

            <head>

            <meta charset="utf-8">

            <meta http-equiv="X-UA-Compatible" content="IE=edge">

            <title>TBM Automobile Private Ltd | Sale Prices Print</title>

            <!-- Tell the browser to be responsive to screen width -->

            <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

            <!-- Bootstrap 3.3.7 -->

            <link rel="stylesheet" href="'.base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css').'">

            <!-- Font Awesome -->

            <link rel="stylesheet" href="'.base_url('assets/bower_components/font-awesome/css/font-awesome.min.css').'">

            <link rel="stylesheet" href="'.base_url('assets/dist/css/AdminLTE.min.css').'">

            </head>

            <body onload="window.print();">



            <div class="wrapper">

            <section class="invoice">

            <!-- title row -->

            <div class="row">

            <div class="col-xs-12">

            <h2 class="page-header">

            TBM Automobile Private Ltd

            </h2>

            </div>

            <!-- /.col -->

            </div>

            <!-- Table row -->

            <div class="row">

            <div class="col-xs-12 table-responsive">

            <table class="table table-striped">

            <thead>

            <tr>

            <th><strong>#</strong></th>

            <th><strong>DateTime</strong></th>

            <th><strong>Category</strong></th>

            <th><strong>Item Name</strong></th>

            <th><strong>Unit</strong></th>

            <th><strong>Price</strong></th>

            </tr>

            </thead>

            <tbody>';



            $html .= '<tr>



            </tr>';



            $html .= '</tbody>

            </table>

            </div>

            <!-- /.col -->

            </div>

            <!-- /.row -->

            </section>

            <!-- /.content -->

            </div>

            </body>

            </html>';

            echo $html;

        }

    }

}



public function getSalePriceDataById()

{

    $product_id = $this->input->post('product_id');

    $category_id = $this->input->post('category_id');

    $unit_id = $this->input->post('unit_id');

    if($product_id)

    {

        $data['data'] = $this->Model_products->getSalePrices($product_id, $category_id, $unit_id);

        $data['success'] = true;

        echo json_encode($data);

    }

    else

    {

        $data['success'] = false;

        echo json_encode($data);

    }

}







































}








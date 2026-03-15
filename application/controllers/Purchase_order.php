<?php

class Purchase_order extends CI_Controller
{
    var $permission = array();

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('User/index');
        } else {
            $user_id = $this->session->userdata('id');
            $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
            $this->data['user_permission'] = unserialize($group_data['permission']);
            $this->permission = unserialize($group_data['permission']);
        }

        $this->load->model('Model_purchase_order');
    }

    public function index()
    {
        if (!in_array('recordPurchasing', $this->permission) && !in_array('viewPurchasing', $this->permission)) {
            $data['page_title'] = 'No Permission';
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/error_no_permission');
            return;
        }

        $data['page_title'] = 'Purchase Order';
        $user_id = $this->session->userdata('id');
        $group_data = $this->Model_groups->getUserGroupByUserId($user_id);
        $data['user_permission'] = unserialize($group_data['permission']);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/header_menu');
        $this->load->view('templates/side_menubar');
        $this->load->view('purchase_order/index', $data);
        $this->load->view('templates/footer');
    }

    public function create_form()
    {
        if (!in_array('createPurchasing', $this->permission)) {
            $data['page_title'] = 'No Permission';
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/error_no_permission');
            return;
        }

        $data['page_title'] = 'Purchase Order';
        $data['vendor_data'] = $this->Model_supplier->getSupplierData();
        $data['is_edit'] = false;
        $data['order'] = array();
        $data['items'] = array();
        $data['action_url'] = base_url() . 'index.php/Purchase_order/create';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/header_menu');
        $this->load->view('templates/side_menubar');
        $this->load->view('purchase_order/create', $data);
        $this->load->view('templates/footer');
    }

    public function create()
    {
        if (!in_array('createPurchasing', $this->permission)) {
            show_error('Access denied', 403);
            return;
        }

        $this->form_validation->set_rules('vendor_id', 'Vendor', 'trim|required');
        $this->form_validation->set_rules('po_number', 'PO Number', 'trim|required');
        $this->form_validation->set_rules('po_date', 'PO Date', 'trim|required');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('Purchase_order/index');
            return;
        }

        $items = $this->buildItemsFromPost();
        if (empty($items)) {
            $this->session->set_flashdata('error', 'Please add at least one item.');
            redirect('Purchase_order/index');
            return;
        }

        $order_data = array(
            'vendor_id' => $this->input->post('vendor_id'),
            'po_number' => $this->input->post('po_number'),
            'po_date' => $this->input->post('po_date'),
            'delivery' => $this->input->post('delivery'),
            'terms_of_payment' => $this->input->post('terms_of_payment'),
            'contact_person' => $this->input->post('contact_person'),
            'contact_no' => $this->input->post('contact_no'),
            'remarks' => $this->input->post('remarks'),
            'total_amount' => $this->input->post('total_amount'),
            'sales_tax_percent' => $this->input->post('sales_tax_percent'),
            'sales_tax_amount' => $this->input->post('sales_tax_amount'),
            'grand_total' => $this->input->post('grand_total'),
            'created_by' => $this->session->userdata('id'),
            'created_at' => date('Y-m-d H:i:s')
        );

        $order_id = $this->Model_purchase_order->createOrder($order_data, $items);

        if (!$order_id) {
            $this->session->set_flashdata('error', 'Error while creating purchase order.');
            redirect('Purchase_order/index');
            return;
        }

        $this->session->set_flashdata('success', 'Purchase order created.');
        redirect('Purchase_order/print_view/' . $order_id);
    }

    public function edit($order_id)
    {
        if (!in_array('updatePurchasing', $this->permission)) {
            $data['page_title'] = 'No Permission';
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/error_no_permission');
            return;
        }

        $order = $this->Model_purchase_order->getOrder($order_id);
        if (!$order) {
            show_404();
            return;
        }

        $data['page_title'] = 'Edit Purchase Order';
        $data['vendor_data'] = $this->Model_supplier->getSupplierData();
        $data['is_edit'] = true;
        $data['order'] = $order;
        $data['items'] = $this->Model_purchase_order->getOrderItems($order_id);
        $data['action_url'] = base_url() . 'index.php/Purchase_order/update/' . $order_id;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/header_menu');
        $this->load->view('templates/side_menubar');
        $this->load->view('purchase_order/create', $data);
        $this->load->view('templates/footer');
    }

    public function update($order_id)
    {
        if (!in_array('updatePurchasing', $this->permission)) {
            show_error('Access denied', 403);
            return;
        }

        $this->form_validation->set_rules('vendor_id', 'Vendor', 'trim|required');
        $this->form_validation->set_rules('po_number', 'PO Number', 'trim|required');
        $this->form_validation->set_rules('po_date', 'PO Date', 'trim|required');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('Purchase_order/edit/' . $order_id);
            return;
        }

        $items = $this->buildItemsFromPost();
        if (empty($items)) {
            $this->session->set_flashdata('error', 'Please add at least one item.');
            redirect('Purchase_order/edit/' . $order_id);
            return;
        }

        $order_data = array(
            'vendor_id' => $this->input->post('vendor_id'),
            'po_number' => $this->input->post('po_number'),
            'po_date' => $this->input->post('po_date'),
            'delivery' => $this->input->post('delivery'),
            'terms_of_payment' => $this->input->post('terms_of_payment'),
            'contact_person' => $this->input->post('contact_person'),
            'contact_no' => $this->input->post('contact_no'),
            'remarks' => $this->input->post('remarks'),
            'total_amount' => $this->input->post('total_amount'),
            'sales_tax_percent' => $this->input->post('sales_tax_percent'),
            'sales_tax_amount' => $this->input->post('sales_tax_amount'),
            'grand_total' => $this->input->post('grand_total')
        );

        $updated = $this->Model_purchase_order->updateOrder($order_id, $order_data, $items);
        if (!$updated) {
            $this->session->set_flashdata('error', 'Error while updating purchase order.');
            redirect('Purchase_order/edit/' . $order_id);
            return;
        }

        $this->session->set_flashdata('success', 'Purchase order updated.');
        redirect('Purchase_order/index');
    }

    public function print_view($order_id)
    {
        if (!in_array('createPurchasing', $this->permission)) {
            $data['page_title'] = 'No Permission';
            $this->load->view('templates/header', $data);
            $this->load->view('templates/header_menu');
            $this->load->view('templates/side_menubar');
            $this->load->view('errors/error_no_permission');
            return;
        }

        $order = $this->Model_purchase_order->getOrder($order_id);
        if (!$order) {
            show_404();
            return;
        }

        $items = $this->Model_purchase_order->getOrderItems($order_id);
        $vendor = $this->Model_supplier->getSupplierData($order['vendor_id']);

        $data['page_title'] = 'Purchase Order Print';
        $data['order'] = $order;
        $data['items'] = $items;
        $data['vendor'] = $vendor;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/header_menu');
        $this->load->view('templates/side_menubar');
        $this->load->view('purchase_order/print', $data);
        $this->load->view('templates/footer');
    }

    public function fetchOrders()
    {
        if (!in_array('recordPurchasing', $this->permission) && !in_array('viewPurchasing', $this->permission)) {
            $result = array('data' => array());
            echo json_encode($result);
            return;
        }

        $data = $this->Model_purchase_order->getOrders();
        $result = array('data' => array());

        $counter = 1;
        foreach ($data as $value) {
            $vendor_name = trim($value['first_name'] . ' ' . $value['last_name']);

            $buttons = '';
            if (in_array('updatePurchasing', $this->permission)) {
                $buttons .= '<a title="Edit Purchase Order" href="' . base_url() . 'index.php/Purchase_order/edit/' . $value['id'] . '"><i class="glyphicon glyphicon-pencil"></i></a> ';
            }
            $buttons .= '<a title="Print Purchase Order" target="__blank" href="' . base_url() . 'index.php/Purchase_order/print_view/' . $value['id'] . '"><i class="glyphicon glyphicon-print"></i></a> ';
            if (in_array('deletePurchasing', $this->permission)) {
                $buttons .= '<a title="Delete Purchase Order" onclick="removePurchaseOrder(' . $value['id'] . ')" data-toggle="modal" href="#removeModal"><i class="glyphicon glyphicon-trash"></i></a>';
            }

            $result['data'][] = array(
                $counter++,
                $value['po_date'],
                $value['po_number'],
                $vendor_name,
                number_format((float)$value['total_amount'], 2),
                number_format((float)$value['sales_tax_amount'], 2),
                number_format((float)$value['grand_total'], 2),
                $buttons
            );
        }

        echo json_encode($result);
    }

    public function remove()
    {
        if (!in_array('deletePurchasing', $this->permission)) {
            $response = array('success' => false, 'messages' => 'Access denied');
            echo json_encode($response);
            return;
        }

        $order_id = $this->input->post('order_id');
        if (!$order_id) {
            $response = array('success' => false, 'messages' => 'Invalid order');
            echo json_encode($response);
            return;
        }

        $deleted = $this->Model_purchase_order->removeOrder($order_id);
        if ($deleted) {
            $response = array('success' => true, 'messages' => 'Purchase order removed.');
        } else {
            $response = array('success' => false, 'messages' => 'Error while removing purchase order.');
        }

        echo json_encode($response);
    }

    private function buildItemsFromPost()
    {
        $items = array();

        $part_names = $this->input->post('part_name');
        $models = $this->input->post('model');
        $qtys = $this->input->post('qty');
        $units = $this->input->post('unit');
        $rates = $this->input->post('rate');
        $amounts = $this->input->post('amount');
        $remarks = $this->input->post('item_remarks');

        if (!is_array($part_names)) {
            return $items;
        }

        $count = count($part_names);
        for ($i = 0; $i < $count; $i++) {
            $part_name = trim($part_names[$i]);
            if ($part_name === '') {
                continue;
            }

            $items[] = array(
                'part_name' => $part_name,
                'model' => isset($models[$i]) ? trim($models[$i]) : '',
                'qty' => isset($qtys[$i]) ? floatval($qtys[$i]) : 0,
                'unit' => isset($units[$i]) ? trim($units[$i]) : '',
                'rate' => isset($rates[$i]) ? floatval($rates[$i]) : 0,
                'amount' => isset($amounts[$i]) ? floatval($amounts[$i]) : 0,
                'remarks' => isset($remarks[$i]) ? trim($remarks[$i]) : ''
            );
        }

        return $items;
    }
}


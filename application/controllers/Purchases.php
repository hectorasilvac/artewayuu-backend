<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchases extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchases_model');
    }

    public function show_info(string $product_id, string $user_id)
    {        
        $result = $this->purchases_model->show_info(
            product_id: filter_var($product_id, FILTER_SANITIZE_STRING),
            user_id: filter_var($user_id, FILTER_SANITIZE_STRING)
        );

       echo json_encode($result);
       exit();
    }

    public function add_order()
    {
        $result = $this->purchases_model->add_order(
            address: $this->input->post('address'),
            buyer_id: $this->input->post('buyerId'),
            payment_method: $this->input->post('payment'),
            seller_id: $this->input->post('sellerId'),
            total: $this->input->post('total'),
            products: json_decode($this->input->post('products'), TRUE),
        );

       echo json_encode($result);
       exit();
    }

    public function show_order(string $user_id)
    {
        $result = $this->purchases_model->show_order(
            user_id: filter_var($user_id, FILTER_SANITIZE_STRING),
        );

       echo json_encode($result);
       exit();
    }

    public function show_order_detail(string $order_id)
    {
        $result = $this->purchases_model->show_order_detail(
            order_id: filter_var($order_id, FILTER_SANITIZE_STRING),
        );

       echo json_encode($result);
       exit();
    }

    public function show_traceability(string $order_id)
    {
        $result = $this->purchases_model->show_traceability(
            order_id: filter_var($order_id, FILTER_SANITIZE_STRING),
        );

       echo json_encode($result);
       exit();
    }

    public function insert_traceability()
    {
        $image = json_decode($this->input->post('image'), TRUE);
        $insert_image = $this->purchases_model->insert_voucher(
            order_id: $this->input->post('orderId'),
            uri: $image['uri'],
            base64: $image['base64'],
        );

        if ( ! $insert_image['data'])
        {
            echo json_encode($insert_image);
            exit();
        }

        $result = $this->purchases_model->insert_traceability(
            user_id: $this->input->post('userId'),
            order_id: $this->input->post('orderId'),
            status_id: $this->input->post('statusId'),
            url_attached: $insert_image['data'],
            comment: $this->input->post('comment'),
        );

       echo json_encode($result);
       exit();
    }

    public function show_status(string $role_value)
    {
        $result = $this->purchases_model->show_status(
            role_value: filter_var($role_value, FILTER_SANITIZE_STRING),
        );

       echo json_encode($result);
       exit();
    }
}

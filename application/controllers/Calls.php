<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Calls extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('calls_model');
    }

    public function all()
    {
/*         $result = $this->purchases_model->add_order(
            address: $this->input->post('address'),
            buyer_id: $this->input->post('buyerId'),
            payment_method: $this->input->post('payment'),
            seller_id: $this->input->post('sellerId'),
            total: $this->input->post('total'),
            total_profit: $this->input->post('totalProfit'),
            products: json_decode($this->input->post('products'), TRUE),
        );

       echo json_encode($result);
       exit(); */
    }

//     $result = $this->purchases_model->show_info(
//         product_id: filter_var($product_id, FILTER_SANITIZE_STRING),
//         user_id: filter_var($user_id, FILTER_SANITIZE_STRING)
//     );

//    echo json_encode($result);
//    exit();
}

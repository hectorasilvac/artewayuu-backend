<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchases extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('purchases_model');
        $this->load->helper('utility');
    }

    public function info()
    {        
        $methods = [
            'GET' => function() {
                return $this->show_info();
            }
        ];

        $valid_method = http_method_exists($methods, $this->input->method(TRUE));

        echo json_encode(result($methods, $this->input->method(TRUE), $valid_method));
    }

    public function show_info(): array
    {
        $result = $this->purchases_model->show_info(
            product_id: $this->input->get('productId'),
            user_id: $this->input->get('userId'),
        );

       return $result;
    }

    public function order()
    {
        $methods = [
            'GET' => function() {
                return $this->show_order();
            },
            'POST' => function() {
                return $this->add_order();
            },
            'PUT' => function() {
                return $this->update_order();
            },
            'DELETE' => function() {
                return $this->delete_order();
            },
        ];

        $valid_method = http_method_exists($methods, $this->input->method(TRUE));

        echo json_encode(result($methods, $this->input->method(TRUE), $valid_method));
    }

    public function show_order()
    {
       return [
           'Show Order'
       ];
    }

    public function add_order()
    {
       return [
           'Add order'
        ];
    }

    public function update_order()
    {
       return 'Update order';
    }

    public function delete_order()
    {
       return 'Delete order';
    }
}

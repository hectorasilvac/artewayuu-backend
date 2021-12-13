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
        $result = $this->calls_model->add(
            name: $this->input->post('name'),
            description: $this->input->post('description'),
            startDate: $this->input->post('startDate'),
            endDate: $this->input->post('endDate'),
            image: $this->input->post('image'),
            url: $this->input->post('url')
        );

       echo json_encode($result);
       exit();
    }

//     $result = $this->purchases_model->show_info(
//         product_id: filter_var($product_id, FILTER_SANITIZE_STRING),
//         user_id: filter_var($user_id, FILTER_SANITIZE_STRING)
//     );

//    echo json_encode($result);
//    exit();
}

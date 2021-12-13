<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Calls extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('calls_model');
    }

    public function show_all()
    {
        $result = $this->calls_model->show_all();

       echo json_encode($result);
       exit();
    }
}

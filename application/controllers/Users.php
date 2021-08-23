<?php

class Users extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
        // $this->load->helper('url_helper');
    }

    public function view()
    {
        // print_r('Funcionando');
        print_r($this->users_model->get_all());
    }
    
}

<?php

class Categories extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('categories_model');
    }

    public function add()
    {
        if ($this->input->method(true) === 'POST')
        {
            echo $this->categories_model->add();
        }
        else
        {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
        }
    }

    public function all()
    {
        $query = $this->categories_model->get_all();

        $result['data']    = $query;
        $result['message'] = NULL;

        header('Content-type:application/json');
        echo json_encode($result);
    }
}

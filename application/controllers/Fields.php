<?php

class Fields extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('fields_model');
    }

    public function get_by_value(int $value)
    {
        header('Content-type:application/json');

        if ($this->input->method(true) !== 'GET')
        {
            echo json_encode([
                'data' => FALSE, 
                'message' => 'Método de solicitud no válido.'
            ]);
            exit();
        }

        $query = $this->fields_model->get_by_value(value:$value);

        echo json_encode($query);
        exit();
    }
}

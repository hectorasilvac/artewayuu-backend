<?php

class Products extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('products_model');
    }

    public function add()
    {
        if ($this->input->method(TRUE) !== 'POST')
        {
            echo json_encode(['data' => FALSE, 'message' => 'Método de solicitud no válido.']);
            exit();
        }

        $params = json_decode(file_get_contents("php://input"), FILE_USE_INCLUDE_PATH);

        $query = $this->products_model->add($params);

        if ( ! $query)
        {
            echo json_encode(['data' => FALSE, 'message' => 'Error al agregar producto.']);
            exit();
        }

        echo json_encode(['data' => TRUE, 'message' => 'Producto agregado correctamente.']);
        exit();
    }

    public function get_by_value(int $value)
    {
        header('Content-type:application/json');

        if ($this->input->method(true) !== 'GET')
        {
            echo json_encode(['data' => FALSE, 'message' => 'Método de solicitud no válido.']);
            exit();
        }

        $query = $this->fields_model->get_by_value(value:$value);

        if (count($query) === 0)
        {
            echo json_encode(['data' => FALSE, 'message' => 'No se pudo encontrar ningún resultado.']);
            exit();
        }

        echo json_encode(['data' => $query, 'message' => NULL]);
        exit();
    }
}

<?php

class Discounts extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('discounts_model');
		// $this->load->helper('url_helper');
	}

	public function add()
	{
		// if ($this->input->method(TRUE) !== 'POST')
		// {
		// 	echo json_encode(['data' => FALSE, 'message' => 'Método de solicitud no válido.']);
		// 	exit();
		// }

		// $params = json_decode(file_get_contents("php://input"), FILE_USE_INCLUDE_PATH);
		// $query = $this->discounts_model->add($params);

		// if ( ! $query)
		// {
			echo json_encode(['data' => FALSE, 'message' => 'Error al agregar descuento.']);
			exit();
	// 	}

	// 	echo json_encode(['data' => TRUE, 'message' => 'Descuento agregado correctamente.']);
	// 	exit();
	// }
}

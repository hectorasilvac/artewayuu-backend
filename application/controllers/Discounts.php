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
		echo $this->input->raw_input_stream;
		exit();
		// $result = [];

		// if ($this->input->method(TRUE) !== 'POST')
		// {
		// 	$result['data'] = FALSE;
		// 	$result['message'] = 'Método de solicitud no válido.';

		// 	echo json_encode($result);
		// 	exit();
		// }

		// $query = $this->discounts_model->add();

		// if ( ! $query)
		// {
		// 	$result['data'] = FALSE;
		// 	$result['message'] = 'No se ha podido agregar el descuento.';

		// 	echo json_encode($result);
		// 	exit();
		// }

		// $result['data'] = TRUE;
		// $result['message'] = 'Descuento agregado correctamente.';

		// echo json_encode($result);
		// exit();
	}

	// public function all()
	// {
	// 	$query = $this->discounts_model->get_all();

	// 	$result['data'] = $query;
	// 	$result['message'] = NULL;

	// 	echo json_encode($result);
	// }
}

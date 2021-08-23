<?php

class Categories extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('categories_model');
		// $this->load->helper('url_helper');
	}

	public function add()
	{
		if ($this->input->method(TRUE) === 'POST')
		{
			echo $this->categories_model->add();
		}
		else
		{
			echo json_encode(array('status' => 'error', 'message' => 'Método no permitido.'));
		}
	}

	public function all()
	{
		print_r($this->users_model->get_all());
	}
}

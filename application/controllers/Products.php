<?php

class Products extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('categories_model');
		// $this->load->helper('url_helper');
	}

	public function all()
	{
		$query = $this->categories_model->get_all();

		$result['data'] = $query;
		$result['message'] = NULL;

		echo json_encode($result);
	}
}

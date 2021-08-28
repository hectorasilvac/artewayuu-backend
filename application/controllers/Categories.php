<?php

	class Categories extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->model('categories_model');
			// $this->load->helper('url_helper');
			print_r('Holaaaa');
		}

		public function add()
		{
			if ($this->input->method(true) === 'POST')
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
			$query = $this->categories_model->get_all();

			$result['data']    = $query;
			$result['message'] = null;

			echo json_encode($result);
		}
	}

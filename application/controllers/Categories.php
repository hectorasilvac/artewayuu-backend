<?php

class Categories extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('users_model');
		// $this->load->helper('url_helper');
	}

	public function all()
	{
		print_r($this->users_model->get_all());
	}
}

<?php

class Categories_model extends CI_Model
{
	public function __construct()
	{
	}

	public function add()
	{
		$data = array(
			'cat_nombre' => $this->input->post('cat_nombre'),
			'cat_valor' => $this->input->post('cat_valor'),
			'cat_imagen' => $this->input->post('cat_imagen'),
		);

		return $this->db->insert('categoria', $data);
	}

	public function get_all() 
	{
		$this->db->select('*');
		$query = $this->db->get('usuario');
		return $query->result_array();
	}
}

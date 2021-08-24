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
		$this->db->select('cat_id AS id, cat_nombre AS name, cat_valor AS value, cat_imagen AS image');
		$query = $this->db->get('categoria');
		return $query->result_array();
	}
}

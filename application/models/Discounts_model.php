<?php

class Discounts_model extends CI_Model
{
	public function __construct()
	{
	}

	public function add()
	{
		$data = [
			'des_cantidad_minima' => $this->input->post('minimum'),
			'des_cantidad_maxima' => $this->input->post('maximum'),
			'des_porcentaje' => $this->input->post('percentage')
		];

		return $this->db->insert('discounts', $data);
	}

	public function get_all() 
	{
		$this->db->select('cat_id AS id, cat_nombre AS name, cat_valor AS value, cat_imagen AS image');
		$query = $this->db->get('categoria');
		return $query->result_array();
	}
}

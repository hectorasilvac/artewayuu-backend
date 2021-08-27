<?php

class Discounts_model extends CI_Model
{
	public function __construct()
	{
	}

	public function add($params)
	{
		$data = [
			'des_cantidad_minima' => $params['minimum'],
			'des_cantidad_maxima' => $params['maximum'],
			'des_porcentaje' =>  $params['percentage'],
		];

		return $this->db->insert('descuento', $data);
	}

	// public function get_all() 
	// {
	// 	$this->db->select('cat_id AS id, cat_nombre AS name, cat_valor AS value, cat_imagen AS image');
	// 	$query = $this->db->get('categoria');
	// 	return $query->result_array();
	// }
}

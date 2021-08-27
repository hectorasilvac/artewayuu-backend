<?php

class Discounts_model extends CI_Model
{
	public function __construct()
	{
	}

	public function add($params)
	{
		print_r($params);
		// $data = [
		// 	'des_cantidad_minima' => $this->input->input_stream('minimum'),
		// 	'des_cantidad_maxima' => $this->input->input_stream('maximum'),
		// 	'des_porcentaje' => $this->input->input_stream('percentage')
		// ];

		// return $this->db->insert('descuento', $data);
	}

	// public function get_all() 
	// {
	// 	$this->db->select('cat_id AS id, cat_nombre AS name, cat_valor AS value, cat_imagen AS image');
	// 	$query = $this->db->get('categoria');
	// 	return $query->result_array();
	// }
}

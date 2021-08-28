<?php

	class Costs_model extends CI_Model
	{
		public function __construct()
		{
		}

		public function add($params)
		{
			$data = [
				'cos_descripcion'    => $params['description'],
				'cos_unidad_medida'  => $params['unit'],
				'cos_cantidad'       => $params['quantity'],
				'cos_valor_unitario' => $params['value'],
				'cos_unidad_mensual' => $params['monthlyUnit'] ?: NULL,
				'cos_total'          => $params['total'],
			];

			return $this->db->insert('costo', $data);
		}

//		public function get_all()
//		{
//			$this->db->select('cat_id AS id, cat_nombre AS name, cat_valor AS value, cat_imagen AS image');
//			$query = $this->db->get('categoria');
//			return $query->result_array();
//		}
	}

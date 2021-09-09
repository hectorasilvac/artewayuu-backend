<?php

class Fields_model extends CI_Model
{
    public function __construct()
    {
    }

    public function get_by_value(int $value)
    {
        $this->db->select('caracteristica.car_id AS id, car_nombre AS label, car_texto AS placeholder, car_tipo AS type, car_minimo AS min, car_maximo AS max, car_teclado AS keyboard, car_multiple AS multiple, car_Valor AS _value');
        $this->db->where('cat_valor &', $value);
        $query = $this->db->get('caracteristica')->result_array();

        if (count($query) > 0)
        {
            foreach ($query as $key => $row)
            {
                if ($row['type'] === 'select')
                {
                    $this->db->select('opc_id AS value, opc_nombre AS label');
                    $this->db->where('car_valor &', $row['_value']);
                    $result = $this->db->get('opcion');

					$query[$key]['options'] = $result->result_array();
                }
            }
        }

        return $query;
        exit();
    }
}

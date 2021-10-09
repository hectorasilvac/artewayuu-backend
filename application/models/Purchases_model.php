<?php

class Purchases_model extends CI_Model
{
    public function __construct()
    {
        $this->load->helper('url');
    }

    public function show_info(string $product_id, string $user_id): array
    {
        $this->db->select('car_etiqueta AS label, dta_valor AS value');
        $this->db->select("(SELECT concat(ubi_direccion, ', ', ubi_ciudad, ', ', ubi_departamento) FROM ubicacion LEFT JOIN usuario ON ubicacion.ubi_id = usuario.ubi_id WHERE usu_id = {$user_id}) AS address");
        $this->db->where('detalle.pro_id' , $product_id);
        $this->db->where('car_etiqueta' , 'payments');
        
        $query = $this->db->get('detalle');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'Información no encontrada.',
            ];
            exit();
        }
        
        $data = [
            'payments' => [],
            'address'  => '',
        ];
        foreach ($query->result_array() as $key => $value)
        {
            if($key === 0)
            {
                $data['address'] = $value['address'];
            }
            
            if($key === 0)
            {
                $data['address'] = $value['address'];
            }

            unset($value['address']);
            $data['payments'][] = $value;
        }

        return [
            'data'    => $data,
            'message' => NULL,
        ];
        exit();
    }
}
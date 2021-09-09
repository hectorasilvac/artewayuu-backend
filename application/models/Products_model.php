<?php

class Products_model extends CI_Model
{
    public function __construct()
    {
    }

    public function add(array $params)
    {
        // Organizing the information before sending it to the database
        if (in_array('images', $params))
        {
            $images = [];

            foreach ($params['images'] as $key => $value)
            {
             $formatted_data = [
                 'img_url' => $value['img_url'],
                 'pro_id' => $params['pro_id']
             ];
            }
            unset($params['images']);
        }

        if (in_array('price', $params))
        {
            $price = [
                'prc_valor_agregado' => $params['price']['Valor agregado'],
                'prc_valor_total'    => $params['price']['Valor total'],
            ];

            unset($params['price']);
        }

        $data = [];

        foreach ($params as $key => $value)
        {
            if (is_array($value))
            {
                foreach ($value as $item)
                {
                    $data[] = ['dta_nombre' => $key, 'dta_valor' => $item];
                }
                continue;
            }
            $data[] = ['dta_nombre' => $key, 'dta_valor' => $value];
        }

        // $created_product = $this->db->insert();
        // $query = $this->db->insert_batch('detalle', $data);

        // echo '<pre>';
        // print_r($query);
        // echo '</pre>';

        // return $this->db->insert('descuento', $data);
        // return $query;
        // exit();
    }
}

<?php

class Ratings_model extends CI_Model
{
    public function add(
        string $comment,
        string $rating,
        string $order_id,
    ): array
    {
        $this->db->trans_begin();

        $rating_result = $this->insert_in_order(
            comment:$comment,
            rating:$rating,
            order_id:$order_id,
        );

        $product_result = $this->insert_in_products(
            rating_id:$rating_result['data'],
            order_id:$order_id,
        );

        if ( ! $this->db->trans_status() || ! $rating_result['data'] || ! $product_result['data'])
        {
            $this->db->trans_rollback();

            return [
                'data'    => FALSE,
                'message' => 'No fue posible califar el producto.',
            ];
            exit();
        }

        $this->db->trans_commit();

        return [
            'data'    => TRUE,
            'message' => 'Calificación agregada correctamente.',
        ];
    }

    private function insert_in_products(
        string $order_id,
        string $rating_id,
    ): array
    {
        $this->db->select('pro_id');
        $this->db->where('ord_id', $order_id);
        $get_products = $this->db->get('detalleorden');

        if ($get_products->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se han encontrados productos en esta orden.',
            ];
            exit();
        }

        $products = $get_products->result_array();

        foreach ($products as $key => $value)
        {
            $products[$key]['cal_id'] = $rating_id;
        }

        $insert_products = $this->db->insert_batch('calificacionproducto', $products);

        if ($insert_products === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se han podido insertar los productos en la tabla calificaciones.',
            ];
            exit();
        }

        return [
            'data'    => TRUE,
            'message' => 'Calificación agregada a productos correctamente.',
        ];
        exit();
    }

    private function insert_in_order(
        string $comment,
        string $rating,
        string $order_id,
    ): array
    {
        $data = [
            'cal_puntaje'    => strtolower($rating),
            'cal_comentario' => $comment,
            'ord_id'         => $order_id,
        ];

        $query = $this->db->insert('calificacion', $data);

        if ( ! $query)
        {
            return [
                'data'    => FALSE,
                'message' => 'No fue posible agregar la calificación.',
            ];
        }

        return [
            'data'    => $this->db->insert_id(),
            'message' => 'Calificación agregada correctamente.',
        ];
        exit();
    }
}

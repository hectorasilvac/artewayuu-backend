<?php

class Images_model extends CI_Model
{
    public function __construct()
    {
    }

    public function add(string $url): bool
    {
        $data = [
            'img_url' => $url,
        ];

        return $this->db->insert('imagen', $data);
    }

    // public function get_all()
    // {
    //     $this->db->select('cat_id AS id, cat_nombre AS name, cat_valor AS value, cat_imagen AS image');
    //     $query = $this->db->get('categoria');
    //     return $query->result_array();
    // }
}

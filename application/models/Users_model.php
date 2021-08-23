<?php

class Users_model extends CI_Model
{

    public function __construct()
    {
    }

    public function get_all()
    {
        $this->db->select('*');
        $query = $this->db->get('usuario');

        return $query->result_array();
    }
}

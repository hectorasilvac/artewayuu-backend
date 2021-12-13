<?php

class Calls_model extends CI_Model
{
    public function __construct()
    {
        $this->load->helper('url');
    }


 
    public function show_all(): array
    {
        $this->db->select('con_nombre AS name, con_descripcion AS description, con_fecha_inicio AS start, con_fecha_final AS end, con_image AS image, con_url AS url');
        $query = $this->db->get('convocatoria');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se han encontrado convocatorias.',
            ];
            exit();
        }

        return [
            'data'    => $query->result_array(),
            'message' => NULL,
        ];
        exit();
    }
}
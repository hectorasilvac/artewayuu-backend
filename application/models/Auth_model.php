<?php

class Auth_model extends CI_Model
{

    public function __construct()
    {
    }

    public function session_info(int $user_id): bool|array
    {
        $this->db->select('usuario.usu_id AS id, usuario.rol_id AS rol');
        $this->db->select('informacionpersonal.inf_nombre AS name, informacionpersonal.inf_apellido AS lastName, informacionpersonal.inf_correo AS email');
        $this->db->select('empresa.emp_nombre AS companyName, empresa.emp_nombre AS companyName, empresa.emp_logo AS logo');
        $this->db->select('rol.rol_valor AS rolValue');
        $this->db->join('informacionpersonal', 'usuario.inf_id = informacionpersonal.inf_id', 'left');
        $this->db->join('empresa', 'usuario.usu_id = empresa.usu_id', 'left');
        $this->db->join('rol', 'usuario.rol_id = rol.rol_id', 'left');
        
        $query = $this->db->get_where('usuario', ['usuario.usu_id' => $user_id]);

        if ($query->num_rows() === 0)
        {
            return FALSE;
            exit();
        }

        return array_filter($query->row_array());
        exit();
    }
}
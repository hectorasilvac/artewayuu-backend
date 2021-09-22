<?php

class Login_model extends CI_Model
{
    public function __construct()
    {
        $this->load->helper('url');
        $this->load->model('users_model');
    }

    public function verify_auth(string $email, string $password): array
    {
        $this->db->select('usu_id AS id, inf_correo AS email, inf_contrasena AS password');
        $this->db->where('inf_correo', $email);
        $this->db->join('usuario', 'usuario.inf_id = informacionpersonal.inf_id');
        $query = $this->db->get('informacionpersonal');
        
        if ($query->num_rows() === 0) {
            return [
                'data' => FALSE,
                'message' => 'Correo electrónico y/o contraseña incorrectos.',
            ];
            exit();
        }

        $data = $query->row_array();

        if (! password_verify($password, $data['password'])) {
            return [
                'data' => FALSE,
                'message' => 'Correo electrónico y/o contraseña incorrectos.',
            ];
            exit();
        }

        $session_info = $this->users_model->session_info(
            user_id:$data['id']
        );

        if ( ! $session_info)
        {
            return [
                'data' => FALSE,
                'message' => 'Ha ocurrido un error al recuperar información del usuario.',
            ];
            exit();
        }

        return [
            'data' => $session_info,
            'message' => NULL,
        ];
        exit();
    }
}
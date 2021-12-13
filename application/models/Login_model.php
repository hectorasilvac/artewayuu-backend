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

    public function verify_recovery_data(
        string $id,
        string $name,
        string $last_name,
        string $email,
        string $phone_number,
    ): array
    {

        $this->db->select('inf_cedula, inf_nombre, inf_apellido, inf_correo, inf_celular');
        $this->db->where('inf_cedula', $id);
        $this->db->where('inf_nombre', $name);
        $this->db->where('inf_apellido', $last_name);
        $this->db->where('inf_correo', $email);
        $this->db->where('inf_celular', $phone_number);

        $query = $this->db->get('informacionpersonal');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'Información no encontrada.',
            ];
        }

        return [
            'data'    => TRUE,
            'message' => NULL,
        ];
    }

    public function update_password(
        string $id,
        string $email,
        string $password,
        string $phone_number,
    )
    {
        $this->db->set('inf_contrasena', password_hash($password, PASSWORD_DEFAULT));
        $this->db->where('inf_cedula', $id);
        $this->db->where('inf_correo', $email);
        $this->db->where('inf_celular', $phone_number);
        $this->db->update('informacionpersonal');

        if ($this->db->affected_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'Ha ocurrido un error al actualizar la contraseña.',
            ];
        }

        return [
            'data'    => TRUE,
            'message' => NULL,
        ];
    }
}
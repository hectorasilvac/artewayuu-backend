<?php

class Users_model extends CI_Model
{

    public function __construct()
    {
    }

    public function add_info(): array
    {
        $data = [
            'inf_nombre'     => strtolower($this->input->post('name')),
            'inf_apellido'   => strtolower($this->input->post('lastName')),
            'inf_correo'     => strtolower($this->input->post('email')),
            'inf_celular'    => strtolower($this->input->post('phoneNumber')),
            'inf_contrasena' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
        ];

        return [
            'result' => $this->db->insert('informacionpersonal', $data),
            'id'     => $this->db->insert_id(),
        ];
    }

    public function add_location(): array
    {
        $data = [
            'ubi_departamento' => strtolower($this->input->post('department')),
            'ubi_ciudad'       => strtolower($this->input->post('city')),
            'ubi_direccion'    => strtolower($this->input->post('address')),
        ];

        return [
            'result' => $this->db->insert('ubicacion', $data),
            'id'     => $this->db->insert_id(),
        ];
    }

    public function add_user(int $inf_id, int $ubi_id, int $rol_id): array
    {
        $data = [
            'inf_id' => $inf_id,
            'ubi_id' => $ubi_id,
            'rol_id' => $rol_id,
        ];

        return [
            'result' => $this->db->insert('usuario', $data),
            'id'     => $this->db->insert_id(),
        ];
    }

    public function add_company(int $usu_id): array
    {
        $data = [
            'emp_nombre' => strtolower($this->input->post('companyName')),
            'emp_nit'    => strtolower($this->input->post('nit')),
            'usu_id'     => $usu_id,
        ];

        return [
            'result' => $this->db->insert('empresa', $data),
            'id'     => $this->db->insert_id(),
        ];
    }

    public function add_entrepreneur(): array
    {
        $this->db->trans_start();
        $create_info     = $this->add_info();
        $create_location = $this->add_location();
        $create_user     = $this->add_user(
            inf_id:$create_info['id'],
            ubi_id:$create_location['id'],
            rol_id:(int) $this->input->post('rolId')
        );
        $create_company = $this->add_company(
            usu_id:$create_user['id']
        );

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return [
                'data'    => FALSE,
                'message' => 'Las transacciones no se han ejecutado correctamente.',
            ];
            exit();
        }

        $user_info = $this->session_info($create_user['id']);

        if (count($user_info) === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se ha encontrado información del usuario.',
            ];
            exit();
        }

        return [
            'data'    => $user_info,
            'message' => 'Cuenta creada exitosamente.',
        ];
        exit();
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

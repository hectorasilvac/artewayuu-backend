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
            'inf_celular'    => $this->input->post('phoneNumber'),
            'inf_contrasena' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'inf_cedula' => strtolower($this->input->post('idCard')),
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

    public function add_user(int $inf_id, ?int $ubi_id = NULL, int $rol_id): array
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

    public function add_client(): array
    {
        $this->db->trans_start();
        $create_info     = $this->add_info();
        $create_user     = $this->add_user(
            inf_id:$create_info['id'],
            rol_id:(int) $this->input->post('rolId')
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

    public function delete(string $id): array
    {
        $this->db->where('usu_id', $id);
        $query = $this->db->delete('usuario');

        if ( ! $query)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se ha podido eliminar la cuenta.',
            ];
            exit();
        }

        return [
            'data'    => TRUE,
            'message' => 'Cuenta eliminada correctamente.',
        ];
        exit();
    }

    public function edit(
        string $id,
        string $name,
        string $last_name,
        string $phone_number,
        string $email,
        ?string $password,
    )
    {
        $data = [
            'inf_nombre'     => strtolower($name),
            'inf_apellido'   => strtolower($last_name),
            'inf_correo'     => strtolower($email),
            'inf_celular'    => strtolower($phone_number),
        ];

        if ($password) {
            $data['inf_contrasena'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $data_string = '';

        foreach ($data as $key => $value)
        {
            $data_string .= "{$key} = '{$value}', ";
        }

        $data_string = rtrim($data_string, ', ');

        $query = $this->db->query("UPDATE informacionpersonal
        LEFT JOIN usuario
        ON informacionpersonal.inf_id = usuario.inf_id
        SET {$data_string}
        WHERE usu_id = {$id}");

        if ( ! $query)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se pudo actualizar la información.',
            ];
            exit();
        }

        return [
            'data' => TRUE,
            'message' => 'Información actualizada correctamente.',
        ];
    }

    public function edit_location(
        string $id,
        string $department,
        string $city,
        string $address,
    )
    {
        $data = [
            'ubi_departamento' => strtolower($department),
            'ubi_ciudad'      => strtolower($city),
            'ubi_direccion'   => strtolower($address),
        ];

        $data_string = '';

        foreach ($data as $key => $value)
        {
            $data_string .= "{$key} = '{$value}', ";
        }

        $data_string = rtrim($data_string, ', ');

        $query = $this->db->query("UPDATE ubicacion
        LEFT JOIN usuario
        ON ubicacion.ubi_id = usuario.ubi_id
        SET {$data_string}
        WHERE usu_id = {$id}");

        if ( ! $query)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se pudo actualizar la dirección.',
            ];
            exit();
        }

        return [
            'data' => TRUE,
            'message' => 'Dirección actualizada correctamente.',
        ];
    }

    public function session_info(int $user_id): bool|array
    {
        $this->db->select('usuario.usu_id AS id, usuario.rol_id AS role');
        $this->db->select('informacionpersonal.inf_nombre AS name, informacionpersonal.inf_apellido AS lastName, informacionpersonal.inf_correo AS email');
        $this->db->select('empresa.emp_id AS companyId, empresa.emp_nombre AS companyName, empresa.emp_nombre AS companyName, empresa.emp_logo AS companyLogo');
        $this->db->select('rol.rol_valor AS roleValue');
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

    public function view(string $user_id)
    {
        $this->db->select('inf_nombre AS name, inf_apellido AS lastName, inf_correo AS email, inf_celular AS phoneNumber');
        $this->db->join('usuario', 'informacionpersonal.inf_id = usuario.inf_id');
        $this->db->where('usu_id', $user_id);
        $query = $this->db->get('informacionpersonal');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se encontró ningún usuario con esta ID.',
            ];
            exit();
        }

        return [
            'data'    => $query->row_array(),
            'message' => NULL,
        ];
        exit();
    }
    
    public function view_location(string $user_id)
    {
        $this->db->select('ubi_departamento AS department, ubi_ciudad AS city, ubi_direccion AS address');
        $this->db->join('usuario', 'ubicacion.ubi_id = usuario.ubi_id');
        $this->db->where('usu_id', $user_id);
        $query = $this->db->get('ubicacion');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'Dirección no encontrada.',
            ];
            exit();
        }

        return [
            'data'    => $query->row_array(),
            'message' => NULL,
        ];
        exit();
    }
}

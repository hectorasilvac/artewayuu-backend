<?php

class Company_model extends CI_Model
{

    public function __construct()
    {
    }

    public function get_by_id(int $company_id): array
    {
        $this->db->select('emp_id AS id, emp_nombre AS name, emp_nit AS nit, emp_logo AS logo, emp_eslogan AS slogan, emp_descripcion AS description, cat_valor AS categories');
        $this->db->where('emp_id', $company_id);
        $query = $this->db->get('empresa');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se ha encontrado información de esta empresa.',
            ];
            exit();
        }

        return [
            'data'    => $query->row_array(),
            'message' => NULL,
        ];
    }

    public function edit(): array
    {
        if ($this->input->post('logo') !== 'undefined')
        {
            $name          = time() . '_' . str_replace(' ', '', basename($this->input->post('name'))) . '.jpg';
            $image         = base64_decode($this->input->post('logo'));
            $file_path     = dirname(__DIR__, 2) . '/resources/images/logos/' . $name;
            $path          = "http://{$_SERVER['SERVER_NAME']}/resources/images/logos/{$name}";
            $uploaded_file = file_put_contents($file_path, $image, LOCK_EX);

            if ( ! $uploaded_file)
            {
                return [
                    'data'    => false,
                    'message' => 'Se ha producido un error al cargar la imagen.',
                ];
                exit();
            }
        }

        $data = [
            'emp_nombre'      => trim(strtolower($this->input->post('name'))),
            'emp_nit'         => trim($this->input->post('nit')),
            'emp_eslogan'     => trim(strtolower($this->input->post('slogan'))),
            'emp_descripcion' => trim(strtolower($this->input->post('description'))),
            'cat_valor'       => trim($this->input->post('categories')),
        ];

        if (isset($path))
        {
            $data['emp_logo'] = strtolower($path);
        }

        $this->db->where('emp_id', $this->input->post('id'));
        $query = $this->db->update('empresa', $data);

        if ( ! $query)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se ha podido actualizar la información.',
            ];
            exit();
        }

        return [
            'data'    => TRUE,
            'message' => 'Información actualizada correctamente.',
        ];
        exit();
    }

    public function get_social_media(int $company_id): array
    {
        $this->db->select('red_facebook AS facebook, red_instagram AS instagram, red_twitter AS twitter, red_youtube AS youtube');
        $this->db->where('emp_id', $company_id);
        $query = $this->db->get('redsocial');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se han encontrado redes sociales de esta empresa.',
            ];
            exit();
        }

        return [
            'data'    => $query->row_array(),
            'message' => NULL,
        ];
    }

    public function edit_social_media(): array
    {
        $data = [
            'red_facebook'  => trim(strtolower($this->input->post('facebook'))),
            'red_twitter'   => trim(strtolower($this->input->post('twitter'))),
            'red_instagram' => trim(strtolower($this->input->post('instagram'))),
            'red_youtube'   => trim(strtolower($this->input->post('youtube'))),
        ];

        $this->db->where('emp_id', $this->input->post('id'));
        $query = $this->db->update('redsocial', $data);

        if ( ! $query)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se han podido actualizar las redes sociales.',
            ];
            exit();
        }

        return [
            'data'    => TRUE,
            'message' => 'Información actualizada correctamente.',
        ];
        exit();
    }
    public function get_by_category(int $category_value): array
    {
        $this->db->select('emp_id AS id, emp_logo AS logo');
        $this->db->where('cat_valor &', $category_value);
        $query = $this->db->get('empresa');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se han encontrado empresas en esta categoría.',
            ];
            exit();
        }

        return [
            'data'    => $query->result_array(),
            'message' => NULL,
        ];
    }

    public function get_detail(int $company_id): array
    {
        $this->db->select('empresa.emp_id AS id, emp_nombre AS name, emp_logo AS logo');
        $this->db->select('inf_nombre AS owner, CONCAT(ubi_direccion, ubi_ciudad, ubi_departamento) AS address, inf_correo AS email, inf_celular AS phone');
        $this->db->select('red_facebook AS facebook, red_twitter AS twitter, red_youtube AS youtube');
        $this->db->join('usuario', 'empresa.usu_id = usuario.usu_id', 'left');
        $this->db->join('informacionpersonal', 'usuario.inf_id = informacionpersonal.inf_id', 'left');
        $this->db->join('ubicacion', 'usuario.ubi_id = ubicacion.ubi_id', 'left');
        $this->db->join('redsocial', 'empresa.emp_id = redsocial.emp_id', 'left');
        $this->db->where('empresa.emp_id', $company_id);

        $query = $this->db->get('empresa');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se han encontrado información de esta empresa',
            ];
            exit();
        }

        return [
            'data'    => $query->row_array(),
            'message' => NULL,
        ];  
    }
}
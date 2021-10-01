<?php

class Company extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('company_model');
        $this->load->library('form_validation');
    }

    public function get_by_id()
    {
        if ($this->input->method(TRUE) !== 'POST')
        {
            echo json_encode(['data' => FALSE, 'message' => 'Método de solicitud no válido.']);
            exit();
        }
        
        $this->form_validation->set_rules('companyId', 'ID de empresa', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            echo json_encode(['data' => FALSE, 'message' => 'Completa todos los campos.']);
            exit();
        }

        $get_info = $this->company_model->get_by_id((int)$this->input->post('companyId'));
        
        echo json_encode($get_info);
        exit();
    }

    public function edit()
    {
        if ($this->input->method(TRUE) !== 'POST')
        {
            echo json_encode(['data' => FALSE, 'message' => 'Método de solicitud no válido.']);
            exit();
        }
    
        $rules = $this->edit_rules();
    
        $this->form_validation->set_rules($rules);
    
        if ($this->form_validation->run() === FALSE)
        {
            echo json_encode(['data' => FALSE, 'message' => 'Completa todos los campos.']);
            exit();
        }
        
        echo json_encode($this->company_model->edit());
        exit();
    }

    public function get_social_media()
    {
        if ($this->input->method(TRUE) !== 'POST')
        {
            echo json_encode(['data' => FALSE, 'message' => 'Método de solicitud no válido.']);
            exit();
        }
    
        $this->form_validation->set_rules('companyId', 'ID de empresa', 'required');
    
        if ($this->form_validation->run() === FALSE)
        {
            echo json_encode(['data' => FALSE, 'message' => 'Completa todos los campos.']);
            exit();
        }
        
        echo json_encode($this->company_model->get_social_media($this->input->post('companyId')));
        exit();
    }

    public function edit_social_media()
    {
        if ($this->input->method(true) !== 'POST') {
            echo json_encode(['data' => false, 'message' => 'Método de solicitud no válido.']);
            exit();
        }
        
        $rules = $this->social_media_rules();
        $this->form_validation->set_rules($rules);
    
        if ($this->form_validation->run() === FALSE)
        {
            echo json_encode(['data' => FALSE, 'message' => 'Completa todos los campos.']);
            exit();
        }
        
        echo json_encode($this->company_model->edit_social_media());
        exit();
    }

    private function social_media_rules(): array
    {
        return [
            [
                'field' => 'id',
                'label' => 'ID de empresa',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'El campo %s es obligatorio.',
                    'numeric' => 'El campo %s debe ser numérico.'
                ],
            ],
            [
                'field' => 'facebook',
                'label' => 'Facebook',
                'rules' => 'valid_url',
                'errors' => [
                    'valid_url' => 'La dirección URL de %s no es válida.',
                ],
            ],
            [
                'field' => 'twitter',
                'label' => 'Twitter',
                'rules' => 'valid_url',
                'errors' => [
                    'valid_url' => 'La dirección URL de %s no es válida.',
                ],
            ],
            [
                'field' => 'instagram',
                'label' => 'Instagram',
                'rules' => 'valid_url',
                'errors' => [
                    'valid_url' => 'La dirección URL de %s no es válida.',
                ],
            ],
            [
                'field' => 'youtube',
                'label' => 'YouTube',
                'rules' => 'valid_url',
                'errors' => [
                    'valid_url' => 'La dirección URL de %s no es válida.',
                ],
            ],
        ];
    }

    public function get_by_category(): void
    {
        if ($this->input->method(true) !== 'POST')
        {
            echo json_encode([
                'data' => FALSE, 
                'message' => 'Método de solicitud no válido.'
            ]);
            exit();
        }

        $query = $this->company_model->get_by_category(
            category_value: $this->input->post('categoryValue'),
        );

        echo json_encode($query);
        exit();
    }

    public function get_detail()
    {
        if ($this->input->method(true) !== 'POST')
        {
            echo json_encode([
                'data' => FALSE, 
                'message' => 'Método de solicitud no válido.'
            ]);
            exit();
        }

        $query = $this->company_model->get_detail(
            company_id: $this->input->post('companyId'),
        );

        echo json_encode($query);
        exit();
    }

    private function edit_rules(): array
    {
        return [
            [
                'field' => 'id',
                'label' => 'ID de empresa',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'El campo %s es obligatorio.',
                    'numeric' => 'El campo %s debe ser numérico.'
                ],
            ],
            [
                'field'  => 'name',
                'label'  => 'Nombre de empresa',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'El campo %s es obligatorio.',
                ],
            ],
            [
                'field'  => 'nit',
                'label'  => 'NIT de empresa',
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'El campo %s es obligatorio.',
                    'numeric' => 'El campo %s debe ser numérico.'
                ],
            ],
            [
                'field'  => 'slogan',
                'label'  => 'Eslogan de empresa',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'El campo %s es obligatorio.',
                ],
            ],
            [
                'field'  => 'description',
                'label'  => 'Descripción de empresa',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'El campo %s es obligatorio.',
                ],
            ],
            [
                'field'  => 'categories',
                'label'  => 'Categorías de empresa',
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'El campo %s es obligatorio.',
                    'numeric' => 'El campo %s debe ser numérico.'
                ],
            ],
            [
                'field'  => 'logo',
                'label'  => 'Logo de empresa',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'El campo %s es obligatorio.',
                ],
            ],
        ];
    }

}

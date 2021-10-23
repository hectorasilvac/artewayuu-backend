<?php

class Users extends CI_Controller
{
    const CLIENT       = 1;
    const ENTREPRENEUR = 2;
    const ADMIN        = 3;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->helper(['form']);
        $this->load->library('form_validation');
    }

    public function add()
    {
        if ($this->input->method(TRUE) !== 'POST')
        {
            echo json_encode(['data' => FALSE, 'message' => 'Método de solicitud no válido.']);
            exit();
        }

        if ( ! isset($_POST['rolId']) || strlen($_POST['rolId']) === 0)
        {
            echo json_encode(['data' => FALSE, 'message' => 'Ha ocurrido un error en el sistema.']);
            exit();
        }

        $rules = $this->add_rules((int)$this->input->post('rolId'));

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() === FALSE)
        {
            echo json_encode(['data' => FALSE, 'message' => 'Completa todos los campos.']);
            exit();
        }

        if ( (int)$this->input->post('rolId') === self::CLIENT)
        {
            echo json_encode($this->users_model->add_client());
            exit();
        }
        elseif ( (int)$this->input->post('rolId') === self::ENTREPRENEUR)
        {
            echo json_encode($this->users_model->add_entrepreneur());
            exit();
        }

        exit();
    }

    public function delete(string $id)
    {
        $result = $this->users_model->delete(
            id: filter_var($id, FILTER_SANITIZE_STRING),
        );

       echo json_encode($result);
       exit();
    }

    public function edit()
    {
        $info = json_decode(file_get_contents('php://input'), TRUE);


        $result = $this->users_model->edit(
            id: filter_var($info['id'], FILTER_SANITIZE_NUMBER_INT),
            name: filter_var($info['name'], FILTER_SANITIZE_STRING),
            last_name: filter_var($info['lastName'], FILTER_SANITIZE_STRING),
            email: filter_var($info['email'], FILTER_SANITIZE_EMAIL),
            phone_number: filter_var($info['phoneNumber'], FILTER_SANITIZE_NUMBER_INT),
            password: isset($info['password']) ? filter_var($info['password'], FILTER_SANITIZE_STRING) : NULL,
        );

       echo json_encode($result);
       exit();
    }

    public function edit_location(string $id)
    {
        $info = json_decode(file_get_contents('php://input'), TRUE);


        $result = $this->users_model->edit_location(
            id: filter_var($id, FILTER_SANITIZE_NUMBER_INT),
            department: filter_var($info['department'], FILTER_SANITIZE_STRING),
            city: filter_var($info['city'], FILTER_SANITIZE_STRING),
            address: filter_var($info['address'], FILTER_SANITIZE_STRING),
        );

       echo json_encode($result);
       exit();
    }

    public function view(string $user_id)
    {
        $result = $this->users_model->view(
            user_id: filter_var($user_id, FILTER_SANITIZE_STRING),
        );

       echo json_encode($result);
       exit();
    }

    public function view_location(string $user_id)
    {
        $result = $this->users_model->view_location(
            user_id: filter_var($user_id, FILTER_SANITIZE_STRING),
        );

       echo json_encode($result);
       exit();
    }

    private function add_rules(int $rol_id): array
    {
        $all_validations = [
            [
                'field' => 'idCard',
                'label' => 'Cédula de ciudadanía',
                'rules' => 'required',
            ],
            [
                'field'  => 'name',
                'label'  => 'Nombre',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'You must provide a %s.',
                ],
            ],
            [
                'field' => 'lastName',
                'label' => 'Apellido',
                'rules' => 'required',
            ],
            [
                'field' => 'phoneNumber',
                'label' => 'Número de celular',
                'rules' => 'required',
            ],
            [
                'field' => 'email',
                'label' => 'Correo electrónico',
                'rules' => 'required',
            ],
            [
                'field' => 'password',
                'label' => 'Contraseña',
                'rules' => 'required',
            ],
            [
                'field' => 'passwordConfirmation',
                'label' => 'Confirmación de contraseña',
                'rules' => 'required',
            ],
            [
                'field' => 'companyName',
                'label' => 'Nombre de empresa',
                'rules' => 'required',
            ],
            [
                'field' => 'nit',
                'label' => 'NIT',
                'rules' => 'required',
            ],
            [
                'field' => 'department',
                'label' => 'Departamento',
                'rules' => 'required',
            ],
            [
                'field' => 'city',
                'label' => 'Ciudad',
                'rules' => 'required',
            ],
            [
                'field' => 'address',
                'label' => 'Dirección',
                'rules' => 'required',
            ],
        ];

        $client_entries = [
            'idCard',
            'name',
            'lastName',
            'email',
            'phoneNumber',
            'email',
            'password',
            'passwordConfirmation',
        ];

        $entrepreneur_entries = [
            ...$client_entries,
            'companyName',
            'nit',
            'address',
            'city',
            'department',
        ];

        $entries_by_rol = [
            self::CLIENT       => $client_entries,
            self::ENTREPRENEUR => $entrepreneur_entries,
        ];

        $rules_by_rol = [];

        foreach ($all_validations as $validation)
        {
            if (in_array($validation['field'], $entries_by_rol[$rol_id]))
            {
                $rules_by_rol[] = $validation;
            }
        }

        return $rules_by_rol;
    }

}

<?php

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('login_model');
    }

    public function auth()
    {
        if ($this->input->method(TRUE) !== 'POST')
        {
            echo json_encode([
                'data' => FALSE, 
                'message' => 'Método de solicitud no válido.'
            ]);
            exit();
        }

        $auth_rules = $this->auth_rules();

        $this->form_validation->set_rules($auth_rules);

        if ($this->form_validation->run() === FALSE)
        {
            echo json_encode([
                'data' => FALSE, 
                'message' => 'Completa todos los campos.'
            ]);
            exit();
        }

        $valid_auth = $this->login_model->verify_auth(
            email: $this->input->post('email'),
            password: $this->input->post('password'),
        );

        echo json_encode($valid_auth);
        exit();
    }

    private function auth_rules(): array
    {
        return [
            [
                'field' => 'email',
                'label' => 'Correo electrónico',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'El campo %s es obligatorio.',
                    'valid_email' => 'El correo electrónico no es válido.'
                ],
            ],
            [
                'field'  => 'password',
                'label'  => 'Contraseña',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'El campo %s es obligatorio.',
                ],
            ],
        ];
    }
}

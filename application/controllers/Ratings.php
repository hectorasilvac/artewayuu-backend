<?php

class Ratings extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ratings_model');
        $this->load->library('form_validation');
    }

    public function add()
    {
        $rules = $this->rules_to_add();

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() === FALSE)
        {
            echo json_encode([
                'data'    => FALSE,
                'message' => 'Completa todos los campos.',
            ]);
            exit();
        }

        $result = $this->ratings_model->add(
            comment:$this->input->post('comment'),
            rating:$this->input->post('rating'),
            order_id:$this->input->post('orderId'),
        );

        echo json_encode($result);
        exit();
    }

    private function rules_to_add(): array
    {
        return [
            [
                'field' => 'rating',
                'label' => 'calificación',
                'rules' => 'required',
            ],
            [
                'field' => 'comment',
                'label' => 'comentario',
                'rules' => 'required',
            ],
            [
                'field' => 'orderId',
                'label' => 'ID de orden',
                'rules' => 'required',
            ],
        ];
    }

}

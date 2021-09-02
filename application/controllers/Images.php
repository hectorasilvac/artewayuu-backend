<?php

class Images extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('images_model');
    }

    public function add()
    {
        header('Content-type:application/json');

        if ($this->input->method(true) !== 'POST')
        {
            echo json_encode(['data' => false, 'message' => 'Método de solicitud no válido.']);
            exit();
        }

        if ( ! file_get_contents('php://input'))
        {
            echo json_encode(['data' => false, 'message' => 'Información incompleta para ejecutar solicitud.']);
            exit();
        }

        $file          = json_decode(file_get_contents('php://input'));
        $name          = time() . '_' . basename($file->uri);
        $image         = base64_decode($file->base64);
        $file_path     = dirname(__DIR__, 2) . '/resources/images/products/uploads/' . $name;
        $uploaded_file = file_put_contents($file_path, $image, LOCK_EX);

        if ( ! $uploaded_file)
        {
            echo json_encode(['data' => false, 'message' => 'Se ha producido un error al cargar la imagen.']);
            exit();
        }

        $query = $this->images_model->add(url:$file_path);

        if ( ! $query)
        {
            echo json_encode(['data' => false, 'message' => 'Error al agregar descuento.']);
            exit();
        }
        
        echo json_encode(['data' => true, 'message' => 'Imagen agregada correctamente.']);
        exit();
    }
}

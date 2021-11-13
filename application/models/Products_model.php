<?php

class Products_model extends CI_Model
{
    public function __construct()
    {
        $this->load->helper('url');
    }

    public function check_user(string $user_id, string $user_email): array
    {
        $this->db->select('usu_id');
        $this->db->join('informacionpersonal', 'usuario.inf_id = informacionpersonal.inf_id', 'left');
        $this->db->where('usu_id', $user_id);
        $this->db->where('inf_correo', $user_email);

        $query = $this->db->get('usuario');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'Usuario no válido.',
            ];
            exit();
        }

        return [
            'data'    => TRUE,
            'message' => 'Usuario válido.',
        ];
        exit();
    }

    public function add(array $params)
    {
        $user_id = $params['user'];
        unset($params['user']);

        unset($params['discounts']);

        $price_id = NULL;

        if (in_array('price', array_keys($params)))
        {
            $price_id = $this->insert_price($params['price']);
            unset($params['price']);
        }

        $product_id = $this->insert_product(
            price_id:$price_id,
            category_id:$params['category'],
            usu_id:$user_id,
        );
        unset($params['category']);

        $this->insert_images($params['images'], $product_id);

        unset($params['images']);

        $data = [];

        foreach ($params as $key => $value)
        {
            if (is_array($value))
            {
                foreach ($value as $item)
                {
                    $data[] = [
                        'car_etiqueta' => $key,
                        'dta_valor'    => $item,
                        'pro_id'       => $product_id,
                    ];
                }
                continue;
            }

            $data[] = [
                'car_etiqueta' => $key,
                'dta_valor'    => $value,
                'pro_id'       => $product_id,
            ];
        }

        $query = $this->db->insert_batch('detalle', $data);

        if ($query === FALSE)
        {
            return [
                'data'    => FALSE,
                'message' => 'Error al agregar producto.',
            ];
            exit();
        }

        return [
            'data'    => TRUE,
            'message' => 'Producto agregado correctamente.',
        ];
        exit();
    }

    public function get_all(int $limit)
    {
        $this->db->select('producto.pro_id AS id, dta_valor AS name, precio.prc_valor_total AS price');
        $this->db->select('(SELECT img_url FROM imagen WHERE imagen.pro_id = id LIMIT 1) AS image');
        $this->db->join('detalle', 'detalle.pro_id = producto.pro_id');
        $this->db->join('precio', 'precio.prc_id = producto.prc_id');
        $this->db->where('dta_nombre', 'Nombre');
        $this->db->limit($limit);
        $query = $this->db->get('producto');

        return $query->result_array();
    }

    public function get_by_id(string $id): array
    {
        $data = [];

        $this->db->select('dta_id AS id, LOWER("feature") AS name, caracteristica.car_nombre AS label, dta_valor AS value');
        $this->db->join('caracteristica', 'detalle.car_etiqueta = caracteristica.car_etiqueta', 'left');
        $this->db->where('detalle.pro_id', $id);
        $get_features = $this->db->get('detalle');

        if ($get_features->num_rows() > 0)
        {
            $data[] = [
                'title' => 'Características',
                'key'   => 'features',
                'data'  => $get_features->result_array(),
            ];
        }

        $this->db->select('usu_id AS id');
        $this->db->where('pro_id', $id);
        $get_user_id = $this->db->get('producto');

        if ($get_user_id->num_rows() > 0)
        {
            $data[] = [
                'title' => 'Vendedor',
                'key'   => 'seller',
                'data'  => $get_user_id->row_array(),
            ];
        }

        $this->db->select('des_id AS id, LOWER("discount") AS name, des_cantidad_minima AS min, des_cantidad_maxima AS max, des_porcentaje AS percentage');
        $this->db->where('pro_id', $id);
        $get_discounts = $this->db->get('descuento');

        if ($get_discounts->num_rows() > 0)
        {
            $data[] = [
                'title' => 'Descuentos',
                'key'   => 'discounts',
                'data'  => $get_discounts->result_array(),
            ];
        }

        $this->db->select('precio.prc_id AS id, LOWER("price") AS name, prc_valor_agregado AS addedValue, prc_valor_total AS totalCost');
        $this->db->join('producto', 'precio.prc_id = producto.prc_id');
        $this->db->where('producto.pro_id', $id);
        $get_discounts = $this->db->get('precio');

        if ($get_discounts->num_rows() > 0)
        {
            $data[] = [
                'title' => 'Precio',
                'key'   => 'prices',
                'data'  => $get_discounts->result_array(),
            ];
        }

        $this->db->select('img_id AS id, img_url AS url, LOWER("image") AS name');
        $this->db->where('pro_id', $id);
        $get_images = $this->db->get('imagen');

        if ($get_images->num_rows() > 0)
        {
            $data[] = [
                'title' => 'Imágenes',
                'key'   => 'images',
                'data'  => $get_images->result_array(),
            ];
        }

        if (count($data) === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'Producto no encontrado.',
            ];
            exit();
        }

        return [
            'data'    => $data,
            'message' => NULL,
        ];
        exit();
    }

    public function get_by_user(
        string $user_id,
        string $hidden,
        ): array
    {
        $this->db->select('producto.pro_id AS id, producto.pro_visibilidad AS visibility');
        $this->db->select('precio.prc_valor_total AS price');
        $this->db->select('(SELECT detalle.dta_valor FROM detalle WHERE detalle.car_etiqueta = "name" AND detalle.pro_id = producto.pro_id) AS name');
        $this->db->select('(SELECT imagen.img_url FROM imagen WHERE imagen.pro_id = producto.pro_id LIMIT 1) AS image');
        $this->db->select("(SELECT (ROUND((AVG(calificacion.cal_puntaje) / 10), 2) / 2) FROM calificacion LEFT JOIN detalleorden ON calificacion.ord_id = detalleorden.ord_id WHERE detalleorden.pro_id = producto.pro_id) AS rating");
        $this->db->join('precio', 'producto.prc_id = precio.prc_id');
        $this->db->join('detalle', 'producto.pro_id = detalle.pro_id');
        $this->db->where('detalle.car_etiqueta', 'quantity');
        $this->db->where('producto.usu_id', $user_id);

        if ($hidden === 'true')
        {
            $this->db->where('detalle.dta_valor', '0');
        }
        else
        {
            $this->db->where('detalle.dta_valor >', '0');
        }

        $query = $this->db->get('producto');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se han encontrado productos.',
            ];
        }

        return [
            'data'    => $query->result_array(),
            'message' => NULL,
        ];
    }

    public function delete(string $id): array
    {
        $this->db->where('pro_id', $id);
        $query = $this->db->delete('producto');

        if ( ! $query)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se ha podido eliminar el producto.',
            ];
            exit();
        }

        return [
            'data'    => TRUE,
            'message' => 'Producto eliminado correctamente.',
        ];
        exit();
    }

    private function insert_images(array $images, int $product_id): int | bool
    {
        $batch_data = [];

        foreach ($images as $data)
        {
            if (empty($data) || strlen($data['uri']) === 0)
            {
                continue;
            }

            $name          = time() . '_' . basename($data['uri']);
            $image         = base64_decode($data['base64']);
            $file_path     = dirname(__DIR__, 2) . '/resources/images/products/uploads/' . $name;
            $path          = "http://{$_SERVER['SERVER_NAME']}/resources/images/products/uploads/{$name}";
            $uploaded_file = file_put_contents($file_path, $image, LOCK_EX);

            if ( ! $uploaded_file)
            {
                echo json_encode(['data' => false, 'message' => 'Se ha producido un error al cargar la imagen.']);
                exit();
            }

            $batch_data[] = [
                'img_url' => $path,
                'pro_id'  => $product_id,
            ];
        }

        return $this->db->insert_batch('imagen', $batch_data);
    }

    private function insert_price(array $data): int
    {
        $price = [
            'prc_valor_agregado' => $data['addedValue'],
            'prc_valor_total'    => $data['totalCost'],
        ];

        return $this->run_query('precio', $price);
    }

    private function insert_product(int $price_id, int $category_id, int $usu_id): int
    {
        $product = [
            'prc_id' => $price_id,
            'usu_id' => $usu_id,
            'cat_id' => $category_id,
        ];

        return $this->run_query('producto', $product);
    }

    private function run_query(string $table_name, array $data): int
    {
        $this->db->insert($table_name, $data);

        if ($this->db->affected_rows() !== 1)
        {
            return 0;
        }

        return $this->db->insert_id();
    }
}
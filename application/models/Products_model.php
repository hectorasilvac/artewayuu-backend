<?php

class Products_model extends CI_Model
{
    public function __construct()
    {
        $this->load->helper('url');
    }

    public function add(array $params)
    {
        unset($params['Descuentos']);

        $price_id = NULL;

        if (in_array('price', array_keys($params)))
        {
            $price_id = $this->insert_price($params['price']);
            unset($params['price']);
        }

        $product_id = $this->insert_product($price_id, $params['category']);
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
                        'dta_nombre' => $key,
                        'dta_valor'  => $item,
                        'pro_id'     => $product_id,
                    ];
                }
                continue;
            }

            $data[] = [
                'dta_nombre' => $key,
                'dta_valor'  => $value,
                'pro_id'     => $product_id,
            ];
        }

        return $this->db->insert_batch('detalle', $data);
    }

    public function get_all(int $limit)
    {
        // $this->db->select('cat_id AS id, cat_nombre AS name, cat_valor AS value, cat_imagen AS image');
        // $query = $this->db->get('categoria');
        // return $query->result_array();

        $this->db->select('producto.pro_id AS id, dta_valor AS name, precio.prc_valor_total AS price');
        $this->db->select('(SELECT img_url FROM imagen WHERE imagen.pro_id = id LIMIT 1) AS image');
        $this->db->join('detalle', 'detalle.pro_id = producto.pro_id');
        $this->db->join('precio', 'precio.prc_id = producto.prc_id');
        $this->db->where('dta_nombre', 'Nombre');
        $this->db->limit($limit);
        $query = $this->db->get('producto');

        return $query->result_array();

        // {
        //     id: 1,
        //     name: 'Mochila Wayu Mini Naranja en Laberinto fabricada a Mano',
        //     rating: 4,
        //     price: '23450',
        //     image:
        //       'https://cardoli.com/164-thickbox_default/mochila-wayuu-naranja-en-laberinto.jpg',
        //   },
    }

    private function insert_images(array $images, int $product_id): int | bool
    {
        $batch_data = [];

        foreach ($images as $data)
        {
            if (empty($data))
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
            'prc_valor_agregado' => $data['Valor agregado'],
            'prc_valor_total'    => $data['Valor total'],
        ];

        return $this->run_query('precio', $price);
    }

    private function insert_product(int $price_id, int $category_id): int
    {
        $product = [
            'prc_id' => $price_id,
            'cat_id' => $category_id,
        ];

        return $this->run_query('producto', $product);
    }

    private function run_query(string $table_name, array $data): int
    {
        $query = $this->db->insert($table_name, $data);

        if ($this->db->affected_rows() !== 1)
        {
            return 0;
        }

        return $this->db->insert_id();
    }
}

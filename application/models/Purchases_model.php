<?php

class Purchases_model extends CI_Model
{
    CONST ENTREPRENEUR = 2;
    CONST CUSTOMER     = 1;

    public function __construct()
    {
        $this->load->helper('url');
    }

    public function add_order(
        string $address,
        string $buyer_id,
        string $payment_method,
        string $seller_id,
        string $total,
        string $total_profit,
        array $products
    ): array
    {
        $insert_shipping = $this->insert_shipping($address);

        if ( ! $insert_shipping['data'])
        {
            return $insert_shipping;
            exit();
        }

        $shipping_id = $insert_shipping['data'];

        $order = [
            'comprado_por'    => $buyer_id,
            'ord_metodo_pago' => $payment_method,
            'vendido_por'     => $seller_id,
            'ord_total'       => $total,
            'ord_ganancia'    => $total_profit,
            'ord_creado'      => date('Y-m-d H:i:s'),
            'env_id'          => $shipping_id,
        ];

        $this->db->insert('orden', $order);

        if ($this->db->affected_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'Error al registrar el pedido.',
            ];
            exit();
        }

        $order_id            = $this->db->insert_id();
        $insert_order_detail = $this->insert_order_detail($order_id, $products);

        if ( ! $insert_order_detail['data'])
        {
            return $insert_order_detail;
            exit();
        }

        $insert_traceability = $this->insert_traceability(
            user_id:$buyer_id,
            order_id:$order_id,
            status_id:'1',
        );

        if ( ! $insert_traceability['data'])
        {
            return $insert_traceability;
            exit();
        }

        return [
            'data'    => TRUE,
            'message' => 'Pedido registrado correctamente.',
        ];
    }

    public function show_info(string $product_id, string $user_id): array
    {
        $this->db->select('car_etiqueta AS label, dta_valor AS value');
        $this->db->select("(SELECT concat(ubi_direccion, ', ', ubi_ciudad, ', ', ubi_departamento) FROM ubicacion LEFT JOIN usuario ON ubicacion.ubi_id = usuario.ubi_id WHERE usu_id = {$user_id}) AS address");
        $this->db->where('detalle.pro_id', $product_id);
        $this->db->where('car_etiqueta', 'payments');

        $query = $this->db->get('detalle');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'Información no encontrada.',
            ];
            exit();
        }

        $data = [
            'payments' => [],
            'address'  => '',
        ];
        foreach ($query->result_array() as $key => $value)
        {
            if ($key === 0)
            {
                $data['address'] = $value['address'];
            }

            if ($key === 0)
            {
                $data['address'] = $value['address'];
            }

            unset($value['address']);
            $data['payments'][] = $value;
        }

        return [
            'data'    => $data,
            'message' => NULL,
        ];
        exit();
    }

    public function show_order(
        string $user_id,
        string $completed,
        int $role_id,
    ): array
    {
        $this->db->select('ord_id AS id, ord_total AS total, DATE_FORMAT(ord_creado, "%b %d %Y") AS date');
        $this->db->order_by('id', 'DESC');

        if ($role_id === self::CUSTOMER)
        {
            $this->db->where('comprado_por', $user_id);
        }
        else
        {
            $this->db->where('vendido_por', $user_id);
            $this->db->select('ord_ganancia AS profit');
        }

        if ($completed === 'true')
        {
            $this->db->where('est_id', '5');
        }
        else
        {
            $this->db->where('est_id !=', '5');
        }

        $query = $this->db->get('orden');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se han encontrado pedidos.',
            ];
            exit();
        }

        return [
            'data'    => $query->result_array(),
            'message' => NULL,
        ];
        exit();
    }

    public function show_order_detail(string $order_id)
    {
        $this->db->select('ord_id AS id, CONCAT(comprador.inf_nombre, " ", comprador.inf_apellido) AS buyer, CONCAT(vendedor.inf_nombre, " ", vendedor.inf_apellido) AS seller, ord_ganancia AS profit');
        $this->db->select('DATE_FORMAT(ord_creado, "%d %b %y %h:%i") AS date, ord_total AS total, env_origen AS origin, env_destino AS destination, env_fecha_envio AS shipmentDate, env_guia AS guideNumber');
        $this->db->select('env_nombre_transportadora AS carrierName, env_fecha_entrega AS deliveryDate');
        $this->db->select("(SELECT estado.est_nombre FROM trazabilidad LEFT JOIN estado ON trazabilidad.est_id = estado.est_id WHERE trazabilidad.ord_id = {$order_id} ORDER BY trazabilidad.creado_en DESC LIMIT 1) AS status");
        $this->db->select("(SELECT estado.est_id FROM trazabilidad LEFT JOIN estado ON trazabilidad.est_id = estado.est_id WHERE trazabilidad.ord_id = {$order_id} ORDER BY trazabilidad.creado_en DESC LIMIT 1) AS statusId");
        $this->db->join('usuario', 'orden.comprado_por = usuario.usu_id');
        $this->db->join('informacionpersonal comprador', 'usuario.inf_id = comprador.inf_id', 'left');
        $this->db->join('usuario usuvendedor', 'orden.vendido_por = usuvendedor.usu_id', 'left');
        $this->db->join('informacionpersonal vendedor', 'usuvendedor.inf_id = vendedor.inf_id', 'left');
        $this->db->join('envio', 'orden.env_id = envio.env_id', 'left');
        $this->db->where('orden.ord_id', $order_id);
        $query = $this->db->get('orden');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se ha encontrado información de esta orden.',
            ];
            exit();
        }

        $data     = [];
        $detail   = ['id', 'buyer', 'seller', 'date', 'total', 'status', 'statusId', 'profit'];
        $shipping = ['origin', 'destination', 'shipmentDate', 'guideNumber', 'carrierName', 'deliveryDate'];

        foreach ($query->row_array() as $key => $value)
        {
            if (in_array($key, $detail))
            {
                $data['detail'][$key] = $value;
                continue;
            }

            if (in_array($key, $shipping))
            {
                $data['shipping'][$key] = $value;
                continue;
            }
        }

        $this->db->select('det_id AS id, det_nombre_producto AS name, det_costo_unitario AS unitValue, det_cantidad AS quantity, det_subtotal AS totalValue, detalleorden.pro_id AS productId, imagen.img_url AS image');
        $this->db->join('imagen', 'detalleorden.pro_id = imagen.pro_id', 'left');
        $this->db->where('detalleorden.ord_id', $order_id);
        $this->db->group_by('detalleorden.det_id');
        $get_order_detail = $this->db->get('detalleorden');

        if ($get_order_detail->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se han encontrado productos en esta orden.',
            ];
            exit();
        }

        $data['products'] = $get_order_detail->result_array();

        return [
            'data'    => $data,
            'message' => NULL,
        ];
        exit();
    }

    public function show_traceability(string $order_id): array
    {
        $this->db->select('tra_id AS id, tra_url AS url, tra_comentario AS comment, est_nombre AS status, DATE_FORMAT(creado_en, "%d %b %y %h:%i") AS date, CONCAT(informacionpersonal.inf_nombre, " ", informacionpersonal.inf_apellido) AS user');
        $this->db->join('estado', 'trazabilidad.est_id = estado.est_id', 'left');
        $this->db->join('usuario', 'trazabilidad.creado_por = usuario.usu_id', 'left');
        $this->db->join('informacionpersonal', 'usuario.inf_id = informacionpersonal.inf_id', 'left');
        $this->db->where('ord_id', $order_id);
        $this->db->order_by('date', 'DESC');
        $query = $this->db->get('trazabilidad');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se ha encontrado la trazabilidad de esta orden.',
            ];
            exit();
        }

        return [
            'data'    => $query->result_array(),
            'message' => NULL,
        ];
        exit();
    }

    public function show_status(string $role_value): array
    {
        $this->db->select('est_id AS id, est_nombre AS value');
        $this->db->where('role_value &', $role_value);
        $query = $this->db->get('estado');

        if ($query->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se han encontrado estados.',
            ];
            exit();
        }

        return [
            'data'    => $query->result_array(),
            'message' => NULL,
        ];
        exit();
    }

    public function insert_traceability(
        ?string $comment = NULL,
        ?string $url_attached = NULL,
        string $user_id,
        string $order_id,
        string $status_id
    ): array
    {
        $data = [
            'tra_comentario' => $comment,
            'tra_url'        => $url_attached,
            'creado_por'     => $user_id,
            'creado_en'      => date('Y-m-d H:i:s'),
            'ord_id'         => $order_id,
            'est_id'         => $status_id,
        ];

        $result = $this->db->insert('trazabilidad', $data);

        if ($result === FALSE)
        {
            return [
                'data'    => FALSE,
                'message' => 'Error al registrar trazabilidad.',
            ];
            exit();
        }

        $update_order = [
            'est_id' => $status_id,
        ];

        $this->db->where('ord_id', $order_id);
        $result_update = $this->db->update('orden', $update_order);

        if ($result_update === FALSE)
        {
            return [
                'data'    => FALSE,
                'message' => 'Error al actualizar estado de la orden.',
            ];
            exit();
        }

        return [
            'data'    => TRUE,
            'message' => 'Estado registrado correctamente',
        ];
    }

    public function insert_voucher(
        string $uri,
        string $base64,
        string $order_id,
    )
    {
        $name          = time() . '_' . basename($uri);
        $image         = base64_decode($base64);
        $file_path     = dirname(__DIR__, 2) . '/resources/images/vouchers/' . $name;
        $path          = "http://{$_SERVER['SERVER_NAME']}/resources/images/vouchers/{$name}";
        $uploaded_file = file_put_contents($file_path, $image, LOCK_EX);

        if ( ! $uploaded_file)
        {
            echo json_encode([
                'data'    => FALSE,
                'message' => 'Se ha producido un error al cargar la imagen.',
            ]);
            exit();
        }

        $data = [
            'img_url' => $path,
            'ord_id'  => $order_id,
        ];

        $query = $this->db->insert('imagen', $data);

        if ($query === FALSE)
        {
            echo json_encode([
                'data'    => FALSE,
                'message' => 'Se ha producido un error al agregar la imagen.',
            ]);
            exit();
        }

        return [
            'data'    => $path,
            'message' => NULL,
        ];
        exit();
    }

    private function insert_order_detail(int $order_id, array $products): array
    {
        $data          = [];
        $products_data = [];

        foreach ($products as $value)
        {
            $data[] = [
                'det_nombre_producto' => $value['name'],
                'det_costo_unitario'  => $value['unitValue'],
                'det_cantidad'        => $value['quantity'],
                'det_subtotal'        => $value['totalValue'],
                'pro_id'              => $value['id'],
                'ord_id'              => $order_id,
            ];

            $products_data[$value['id']] = $value['quantity'];
        }

        $this->db->select('pro_id, dta_id, dta_valor');
        $this->db->where('car_etiqueta', 'quantity');
        $this->db->where_in('detalle.pro_id', array_keys($products_data));
        $current_quantity = $this->db->get('detalle');

        if ($current_quantity->num_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se ha podido recuperar las unidades en stock.',
            ];
            exit();
        }

        $arr_current_quantity = $current_quantity->result_array();

        foreach ($arr_current_quantity as $key => $value)
        {
            if (isset($products_data[$value['pro_id']]))
            {
                $arr_current_quantity[$key]['dta_valor'] = $arr_current_quantity[$key]['dta_valor'] - $products_data[$value['pro_id']];
            }
        }

        $updated_quantity = $this->db->update_batch('detalle', $arr_current_quantity, 'dta_id');

        if ($updated_quantity === FALSE)
        {
            return [
                'data'    => FALSE,
                'message' => 'No se ha podido actualizar las unidades en stock.',
            ];
            exit();
        }

        $result = $this->db->insert_batch('detalleorden', $data);

        if ($result === FALSE)
        {
            return [
                'data'    => FALSE,
                'message' => 'Error al registrar detalles del pedido.',
            ];
            exit();
        }

        return [
            'data'    => TRUE,
            'message' => NULL,
        ];
    }

    private function insert_shipping(string $address): array
    {
        $shipping = [
            'env_destino' => $address,
            'env_creado'  => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('envio', $shipping);

        if ($this->db->affected_rows() === 0)
        {
            return [
                'data'    => FALSE,
                'message' => 'Error al registrar dirección de envío.',
            ];
            exit();
        }

        return [
            'data'    => $this->db->insert_id(),
            'message' => NULL,
        ];
    }
}
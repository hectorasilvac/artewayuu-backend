<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function http_method_exists(array $methods, string $method): array
{
    if ( ! isset($methods[$method]))
    {
        return [
            'data'    => FALSE,
            'message' => 'Método de solicitud no válido.',
        ];
        exit();
    }

    return [
        'data'    => TRUE,
        'message' => 'Método de solicitud válido.',
    ];
    exit();
}

function result(array $methods, string $method, array $valid_method): array
{
    if ( ! $valid_method['data'])
    {
        return $valid_method;
        exit();
    }

    return $methods[$method]();
    exit();
}

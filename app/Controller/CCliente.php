<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once './Model/Cliente.php';

class CCliente
{
    public static function AltaCliente(Request $request, Response $response, $args)
    {
        #HACER VALIDACIONES
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $idProducto = $parametros['idProducto'];
        $cantidad = $parametros['cantidad'];
        try
        {
            Cliente::AltaCliente($nombre, $idProducto, $cantidad);
            $payload = json_encode("Cliente creado");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        catch (Exception $e)
        {
            $payload = json_encode("Error al crear cliente. {$e->getMessage()}");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}

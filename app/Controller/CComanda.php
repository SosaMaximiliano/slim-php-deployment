<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once './Model/Comanda.php';
require_once './Model/Pedido.php';
require_once './Model/Mesa.php';

class CComanda
{
    public static function AltaComanda(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idMesa = $parametros['ID_Mesa'];
        $idPedido = Mesa::TraerIDPedido($idMesa);
        try
        {
            if (Pedido::ExistePedido($idPedido))
            {
                $pedido = Pedido::TraerPedido($idPedido);
                $productos = $pedido->Productos;
                $cliente = 'NADIE';
                $estado = "En preparacion";
                $idEmpleado = Mesa::TraerIDEmpleado($idMesa);
                //$cliente = $pedido->Nombre;
                if (!Comanda::ExistePedidoEnComanda($idPedido))
                {
                    Comanda::AltaComanda($idMesa, $cliente, $idEmpleado, $idPedido, $productos);
                    Pedido::CambiarEstadoPedido($idPedido, $estado);
                    $payload = json_encode("Comanda creada correctamente");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
                else
                {
                    $payload = json_encode("El pedido ya fue pasado a la comanda");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
            }
            else
            {
                $payload = json_encode("El numero de pedido no existe");
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        }
        catch (Exception $e)
        {
            $payload = json_encode("Error al crear comanda. {$e->getMessage()}");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function CambiarEstadoComanda(Request $request, Response $response, $args)
    {
    }
}

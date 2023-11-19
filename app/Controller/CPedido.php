<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once './Model/Pedido.php';

class CPedido
{
    private static $estados = array(
        "Pedido",
        "En preparacion",
        "Listo",
        "Entregado"
    );

    public static function AltaPedido(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $productos = $parametros['productos'];
        $idCliente = $parametros['idCliente'];
        #CONFIRMAR QUE EL CLIENTE EXISTA
        #REVISO QUE HAYA STOCK DEL PRODUCTO
        if (Producto::HayStock($productos))
        {
            try
            {
                Pedido::AltaPedido($productos, $idCliente);
                $payload = json_encode("Pedido creado");
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
            catch (Exception $e)
            {
                $payload = json_encode("No se pudo tomar el pedido. {$e->getMessage()}");
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
            #PASO EL PEDIDO A LA COMANDA Y CAMBIO EL ESTADO A "EN PREPARACION"
        }
    }

    public static function ListarPedidos(Request $request, Response $response, $args)
    {
        $payload = json_encode(Pedido::ListarPedidos());
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function CambiarEstadoPedido(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['idPedido'];
        $estado = $parametros['estado'];

        if (Pedido::ExistePedido($idPedido))
        {
            if (in_array($estado, self::$estados))
            {
                try
                {
                    Pedido::CambiarEstadoPedido($idPedido, $estado);
                    $payload = json_encode("Estado del pedido cambiado a {$estado}");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
                catch (Exception $e)
                {
                    $payload = json_encode("Error al cambiar de estado. {$e->getMessage()}");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
            }
            else
                throw new Exception("Estado incorrecto", 200);
        }
        else
            throw new Exception("Pedido inexistente", 200);
    }
}

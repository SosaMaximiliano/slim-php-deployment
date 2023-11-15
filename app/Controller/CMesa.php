<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once './Clases/Mesa.php';

class CMesa
{
    private static $estados = array(
        "Con cliente esperando pedido",
        "Con cliente comiendo",
        "Con cliente pagando",
        "Cerrada"
    );

    public static function AltaMesa(Request $request, Response $response, $args)
    {
        #HACER VALIDACIONES
        #UN CLIENTE NO PUEDE TENER MAS DE UNA MESA

        $parametros = $request->getParsedBody();
        $idCliente = $parametros['idCliente'];

        if (Cliente::ExisteCliente($idCliente))
        {
            if (!Mesa::ExisteReserva($idCliente))
            {
                $mozo = Mesa::TraerMozo();
                $pedido = Cliente::TraerPedido($idCliente);
                $idMozo = $mozo->id;
                $idPedido = $pedido->idPedido;
                if ($idPedido != NULL)
                    try
                    {
                        Mesa::AltaMesa($idPedido, $idMozo, $idCliente);
                        $payload = json_encode("La mesa fue dada de alta");
                        $response->getBody()->write($payload);
                        return $response->withHeader('Content-Type', 'application/json');
                    }
                    catch (Exception $e)
                    {
                        $payload = json_encode("No se pudo abrir la mesa. {$e->getMessage()}");
                        $response->getBody()->write($payload);
                        return $response->withHeader('Content-Type', 'application/json');
                    }
                else
                {
                    $payload = json_encode("No existe pedido.");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
            }
            else
            {
                $payload = json_encode("Ya existe una reserva para ese cliente.");
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        }
        else
        {
            $payload = json_encode("No existe cliente.");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function ListarMesas(Request $request, Response $response)
    {
        $payload = json_encode(Mesa::ListarMesas());
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function CambiarEstadoMesa(Request $request, Response $response)
    {
        $parametros = $request->getParsedBody();
        $idMesa = $parametros['idMesa'];
        $estado = $parametros['estado'];
        if (in_array(self::$estados, $estado))
        {
            try
            {
                Mesa::CambiarEstadoMesa($idMesa, $estado);
                $payload = json_encode("El estado de la mesa cambio a {$estado}");
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
            catch (Exception $e)
            {
                $payload = json_encode("Error al cambiar estado de la mesa. {$e->getMessage()}");
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        }
        else
        {
            $payload = json_encode("Estado no permitido.");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}

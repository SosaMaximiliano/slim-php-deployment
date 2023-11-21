<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once './Model/Mesa.php';

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
        try
        {
            $idMesa = Mesa::AltaMesa();
            $payload = json_encode("La mesa {$idMesa} fue dada de alta");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        catch (Exception $e)
        {
            $payload = json_encode("No se pudo agregar la mesa. {$e->getMessage()}");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function AbrirMesa(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idMesa = $parametros['ID_Mesa'];
        $idPedido = $parametros['ID_Pedido'];
        if (Mesa::ExisteMesa($idMesa) && Mesa::MesaLibre($idMesa))
        {
            if (Pedido::ExistePedido($idPedido))
            {
                $mozo = Utils::DameUnMozo();
                $idEmpleado = $mozo->ID;
                try
                {
                    Mesa::AbrirMesa($idMesa, $idEmpleado, $idPedido);
                    Empleado::SumarMesaMozo($idEmpleado);
                    $payload = json_encode("La mesa {$idMesa} esta siendo atendida por {$mozo->Nombre} {$mozo->Apellido}");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
                catch (Exception $e)
                {
                    $payload = json_encode("Error al abrir la mesa. {$e->getMessage()}");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
            }
            else
            {
                $payload = json_encode("No existe el pedido indicado.");
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        }
        else
        {
            $payload = json_encode("La mesa no esta disponible.");
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
        $idMesa = $parametros['ID_Mesa'];
        $estado = $parametros['Estado'];
        if (in_array($estado, self::$estados))
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

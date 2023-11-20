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
            Mesa::AltaMesa();
            $payload = json_encode("Una mesa fue dada de alta");
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
        $idMesa = $parametros['ID'];
        $mozo = Utils::DameUnMozo();
        $idMozo = $mozo->ID;
        try
        {
            Mesa::AbrirMesa($idMesa, $idMozo);
            Empleado::SumarMesaMozo($idMozo);
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

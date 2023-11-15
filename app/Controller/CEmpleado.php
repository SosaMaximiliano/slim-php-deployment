<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once './Clases/Empleado.php';

class CEmpleado
{
    public static function IngresarEmpleado(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        var_dump($parametros);
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        try
        {
            Empleado::AltaEmpleado($nombre, $apellido);
            $payload = json_encode("Empleado creado exitosamente.");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        catch (Exception $e)
        {
            $payload = json_encode("Error al crear empleado. {$e->getMessage()}");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function ListarEmpleados(Request $request, Response $response, $args)
    {
        $lista = Empleado::ListarEmpleados();
        $payload = json_encode(array("listaDeEmpleados" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ListarEmpleadosPorSector(Request $request, Response $response, $args)
    {
        $parametros = $request->getQueryParams();
        $sector = $parametros['sector'];
        $lista = Empleado::ListarPorSector($sector);
        $payload = json_encode(array("listaDeEmpleados" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function AsignarSector(Request $request, Response $response, $args)
    {
        $sectores = array("Cocinero", "Bartender", "Mozo", "Cervecero");

        #VALIDAR QUE EL EMPLEADO EXISTA
        $parametros = $request->getParsedBody();
        $idEmpleado = $parametros['id'];
        $sector = $parametros['sector'];

        if (Empleado::BuscarEmpleadoPorID($idEmpleado) != NULL)
        {
            if (!in_array($sector, $sectores))
            {
                throw new Exception("Sector no valido", 300);
                return false;
            }
            else
            {
                try
                {
                    Empleado::AsignarSector($idEmpleado, $sector);
                    $payload = json_encode("Sector asignado correctamente");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
                catch (Exception $e)
                {
                    throw new Exception("No se pudo asignar el sector. {$e->getMessage()}", 300);
                }
            }
        }
        else
            throw new Exception("ID de empleado inexistente.", 300);
    }
}

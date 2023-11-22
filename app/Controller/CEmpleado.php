<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once './Model/Empleado.php';

class CEmpleado
{
    public static function IngresarEmpleado(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $nombre = $parametros['Nombre'];
        $apellido = $parametros['Apellido'];
        $clave = $parametros['Clave'];
        try
        {
            Empleado::AltaEmpleado($nombre, $apellido, $clave);
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
        $sector = $parametros['Sector'];
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
        $idEmpleado = $parametros['ID'];
        $sector = $parametros['Sector'];

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

    public static function BajaEmpleado(Request $request, Response $response, $args)
    {
        $parametros = $request->getQueryParams();
        $idEmpleado = $parametros['ID'];
        try
        {
            Empleado::BajaEmpleado($idEmpleado);
            $payload = json_encode("Empleado dado de baja.");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        catch (Exception $e)
        {
            $payload = json_encode("Error al dar de baja empleado. {$e->getMessage()}");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function SubirFoto(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['ID'];
        var_dump($request);
        $extension = explode(".", $_FILES["imagen"]["full_path"]);
        $destino = "ImagenesDePedidos/" . $idPedido . "." . $extension[1];
        move_uploaded_file($_FILES['imagen']['tmp_name'], $destino);
    }
}

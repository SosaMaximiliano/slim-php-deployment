<?php
require_once './Clases/Empleado.php';

class CEmpleado extends Empleado
{
    public static function CargarEmpleado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if (self::ValidarDatos($parametros))
        {
            $nombre = $parametros['nombre'];
            $apellido = $parametros['apellido'];
            #VALIDAR QUE NO EXISTA
            if (self::BuscarEmpleadoPorNombre($nombre, $apellido) == NULL)
                try
                {
                    self::AltaEmpleado($nombre, $apellido);
                }
                catch (Exception $e)
                {
                    echo "Error al crear empleado. {$e->getMessage()}";
                    return;
                }
            else
                throw new Exception("El empleado ya se encuentra dado de alta", 1);
        }
    }

    public static function Listar()
    {
        return self::ListarEmpleados();
    }

    public static function ListarSector($e)
    {
        $parametros = $e->getParsedBody();
        var_dump($parametros);
        $sector = $parametros['sector'];
        return self::ListarPorSector($sector);
    }

    public static function AsignarPuesto($idEmpleado, $sector)
    {
        $sectores = array("Cocinero", "Bartender", "Mozo", "Cervecero");

        if (!in_array($sector, $sectores))
        {
            throw new Exception("Sector no valido", 100);
            return false;
        }
        else
        {
            try
            {
                return self::AsignarSector($idEmpleado, $sector);
            }
            catch (Exception $e)
            {
                echo "No se pudo asignar el sector. {$e->getMessage()}";
                return false;
            }
        }
    }
}

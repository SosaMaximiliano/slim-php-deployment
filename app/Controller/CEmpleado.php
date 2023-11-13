<?php
require_once './Clases/Empleado.php';

class CEmpleado
{
    public static function IngresarEmpleado($request)
    {
        $parametros = $request->getParsedBody();
        if (Empleado::ValidarDatos($parametros))
        {
            $nombre = $parametros['nombre'];
            $apellido = $parametros['apellido'];
            #VALIDAR QUE NO EXISTA
            if (Empleado::BuscarEmpleadoPorNombre($nombre, $apellido) == NULL)
                try
                {
                    Empleado::AltaEmpleado($nombre, $apellido);
                    echo "Empleado creado exitosamente.";
                    return true;
                }
                catch (Exception $e)
                {
                    echo "Error al crear empleado. {$e->getMessage()}";
                    return false;
                }
            else
                throw new Exception("El empleado ya se encuentra dado de alta", 300);
        }
    }

    public static function ListarEmpleados()
    {
        return Empleado::ListarEmpleados();
    }

    public static function ListarSector($sector)
    {
        return Empleado::ListarPorSector($sector);
    }

    public static function AsignarPuesto($idEmpleado, $sector)
    {
        $sectores = array("Cocinero", "Bartender", "Mozo", "Cervecero");

        #VALIDAR QUE EL EMPLEADO EXISTA

        if (Empleado::BuscarEmpleadoPorID($idEmpleado))
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
                    return Empleado::AsignarSector($idEmpleado, $sector);
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

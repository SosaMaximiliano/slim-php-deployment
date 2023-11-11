<?php


class Empleado
{
    protected static function AltaEmpleado($nombre, $apellido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO empleado (nombre,apellido) 
            VALUES (:nombre, :apellido)"
        );
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $consulta->execute();
    }

    protected static function ListarEmpleados()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM empleado"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    protected static function ListarPorSector($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM empleado WHERE sector = :sector"
        );
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    protected static function AsignarSector($idEmpleado, $sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE empleado 
                    SET sector = :sector 
                    WHERE id = :idEmpleado"
        );
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->execute();
        return true;
    }

    protected static function ValidarDatos($parametros)
    {
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        if (isset($nombre) && isset($apellido))
        {
            if (is_string($nombre) && is_string($apellido))
            {
                if (preg_match('/^[a-zA-Z ]+$/', $nombre) && preg_match('/^[a-zA-Z ]+$/', $apellido))
                    return true;
            }
            else
                return false;
        }
    }

    protected static function BuscarEmpleadoPorID($idEMpleado)
    {
    }

    protected static function BuscarEmpleadoPorNombre($nombre, $apellido)
    {
        $empleados = self::ListarEmpleados();
        foreach ($empleados as $e)
            if ($e->nombre == $nombre && $e->apellido == $apellido)
                return $e;
        return null;
    }
}

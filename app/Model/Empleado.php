<?php


class Empleado
{
    public static function AltaEmpleado($nombre, $apellido, $clave)
    {
        $fecha = date('Y-m-d');
        $pass = password_hash($clave, PASSWORD_DEFAULT);
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO Empleado (Nombre,Apellido,Clave,FechaAlta) 
            VALUES (:nombre,:apellido,:clave,:fecha)"
        );
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $pass, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function ListarEmpleados()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM Empleado WHERE Estado != 'Baja'"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function ListarPorSector($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM Empleado WHERE Sector = :sector"
        );
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function AsignarSector($idEmpleado, $sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE Empleado 
                    SET Sector = :sector 
                    WHERE ID = :idEmpleado"
        );
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ValidarDatos($parametros)
    {
        $nombre = $parametros['Nombre'];
        $apellido = $parametros['Apellido'];
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

    public static function BuscarEmpleadoPorID($idEMpleado)
    {
        $empleados = self::ListarEmpleados();
        foreach ($empleados as $e)
            if ($e->ID == $idEMpleado)
                return $e;
        return null;
    }

    public static function BuscarEmpleadoPorNombre($nombre, $apellido)
    {
        $empleados = self::ListarEmpleados();
        foreach ($empleados as $e)
            if ($e->Nombre == $nombre && $e->Apellido == $apellido)
                return $e;
        return null;
    }

    // public static function SumarMesaMozo($idEmpleado)
    // {
    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     $consultaUpdate = $objAccesoDatos->prepararConsulta(
    //         "UPDATE Empleado SET Operaciones = Operaciones + 1 WHERE ID = :idEmpleado"
    //     );
    //     $consultaUpdate->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
    //     $consultaUpdate->execute();
    // }

    public static function SumarOperacion($idEmpleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaUpdate = $objAccesoDatos->prepararConsulta(
            "UPDATE Empleado SET Operaciones = Operaciones + 1 WHERE ID = :idEmpleado"
        );
        $consultaUpdate->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consultaUpdate->execute();
    }

    public static function BajaEmpleado($idEmpleado)
    {
        $estado = "Baja";
        $fecha = date('Y-m-d');
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE Empleado 
                    SET Estado = :estado, FechaBaja = :fecha 
                    WHERE ID = :idEmpleado"
        );
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function SuspenderEmpleado($idEmpleado)
    {
        $estado = "Suspendido";
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE Empleado 
                    SET Estado = :estado
                    WHERE ID = :idEmpleado"
        );
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->execute();
    }
}

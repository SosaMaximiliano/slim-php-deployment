<?php


class Empleado
{
    public static function AltaEmpleado($nombre, $apellido)
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

    public static function ListarEmpleados()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM empleado"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function ListarPorSector($sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM empleado WHERE sector = :sector"
        );
        $consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function AsignarSector($idEmpleado, $sector)
    {
        $sectores = array("Cocinero", "Bartender", "Mozo", "Cervecero");

        if (!in_array($sector, $sectores))
            throw new Exception("Sector no valido", 100);
        else
        {
            try
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
            catch (PDOException $e)
            {
                echo $e->getMessage();
                return false;
            }
        }
    }
}

<?php

include_once 'Empleado.php';
include_once 'Cliente.php';
include_once '../app/Utils/Utils.php';
//include_once 'Pedido.php';

class Mesa
{
    public static function AltaMesa()
    {
        $estado = 'Libre';
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaInsert = $objAccesoDatos->prepararConsulta(
            "INSERT INTO Mesa (Estado) 
            VALUES (:estado)",
        );
        $consultaInsert->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consultaInsert->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function AbrirMesa($idMesa, $idEmpleado, $idPedido)
    {
        $estado = 'Con cliente esperando pedido';
        $codigo = Utils::GenerarCodigo();
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaUpdate = $objAccesoDatos->prepararConsulta(
            "UPDATE Mesa 
            SET ID_Empleado = :idEmpleado, Estado = :estado, CodigoUnico = :codigoUnico ,ID_Pedido = :idPedido
            WHERE ID = :id"
        );
        $consultaUpdate->bindValue(':id', $idMesa, PDO::PARAM_INT);
        $consultaUpdate->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consultaUpdate->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consultaUpdate->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consultaUpdate->bindValue(':codigoUnico', $codigo, PDO::PARAM_STR);
        $consultaUpdate->execute();
    }

    public static function ListarMesas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM Mesa"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function CambiarEstadoMesa($idMesa, $estado)
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
            if ($e->ID == $idMesa)
            {
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta(
                    "UPDATE Mesa SET Estado = :estado WHERE ID = :idMesa"
                );
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
                $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
                $consulta->execute();
                return;
            }
    }

    public static function CerrarMesa($idMesa)
    {
        $mesas = self::ListarMesas();
        $estado = "Cerrada";
        foreach ($mesas as $e)
            if ($e->ID == $idMesa)
            {
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta(
                    "UPDATE Mesa SET Estado = :estado WHERE ID = :idMesa"
                );
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
                $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
                $consulta->execute();
                return;
            }
    }

    public static function ExisteReserva($idCliente)
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
        {
            if ($e->ID_CLiente == $idCliente)
                return true;
        }
        return false;
    }

    public static function TraerIDCliente($idMesa)
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
        {
            if ($e->ID == $idMesa)
                return $e->ID_Cliente;
        }
        return NULL;
    }

    public static function TraerIDPedido($idMesa)
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
        {
            if ($e->ID == $idMesa)
                return $e->ID_Pedido;
        }
        return NULL;
    }

    public static function TraerIDEmpleado($idMesa)
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
        {
            if ($e->ID == $idMesa)
                return $e->ID_Empleado;
        }
        return NULL;
    }

    public static function ExisteMesa($idMesa)
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
        {
            if ($e->ID == $idMesa)
                return true;
        }
        return false;
    }

    public static function MesaLibre($idMesa)
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
        {
            if ($e->ID == $idMesa)
                if ($e->Estado == "Libre")
                    return true;
        }
        return false;
    }
}

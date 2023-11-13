<?php

include_once 'Empleado.php';
include_once 'Cliente.php';
//include_once 'Pedido.php';

class Mesa
{
    public static function AltaMesa($idPedido, $idMozo, $idCliente)
    {
        $estado = "Con cliente esperando pedido";
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaInsert = $objAccesoDatos->prepararConsulta(
            "INSERT INTO mesa (idMozo,idPedido,estado,idCliente) VALUES (:idMozo,:idPedido,:estado,:idCliente)",
        );
        $consultaInsert->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consultaInsert->bindValue(':idMozo', $idMozo, PDO::PARAM_INT);
        $consultaInsert->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consultaInsert->bindValue(':idCliente', $idCliente, PDO::PARAM_INT);
        $consultaInsert->execute();

        #ACTUALIZO MESAS MOZO
        $consultaUpdate = $objAccesoDatos->prepararConsulta(
            "UPDATE empleado SET mesasAcargo = mesasAcargo + 1 WHERE id = :idMozo"
        );
        $consultaUpdate->bindValue(':idMozo', $idMozo, PDO::PARAM_INT);
        $consultaUpdate->execute();
    }


    public static function ListarMesas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM mesa"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function CambiarEstadoMesa($idMesa, $estado)
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
            if ($e->id == $idMesa)
            {
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta(
                    "UPDATE mesa SET estado = :estado WHERE id = :idMesa"
                );
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
                $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
                $consulta->execute();
                echo "Estado de la mesa actualizado";
                return;
            }
    }

    public static function TraerMozo()
    {
        $empleados = Empleado::ListarPorSector("Mozo");
        #SI EL MOZO ESTA ATENDIENDO MENOS DE CINCO MESAS
        $disponibles = array_filter($empleados, function ($mozo)
        {
            return $mozo->mesasACargo < 5;
        });

        if (count($disponibles) > 0)
        {
            $random = rand(0, (count($empleados) - 1));
            $mozo = $empleados[$random];
            return $mozo;
        }
        return NULL;
    }

    public static function ExisteReserva($idCliente)
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
        {
            if ($e->idCliente == $idCliente)
                return true;
        }
        return false;
    }

    public static function TraerIDCliente($idMesa): int
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
        {
            if ($e->id == $idMesa)
                return $e->idCliente;
        }
        return NULL;
    }

    public static function TraerIDPedido($idMesa): int
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
        {
            if ($e->id == $idMesa)
                return $e->idPedido;
        }
        return NULL;
    }

    public static function TraerIDEmpleado($idMesa): int
    {
        $mesas = self::ListarMesas();
        foreach ($mesas as $e)
        {
            if ($e->id == $idMesa)
                return $e->idMozo;
        }
        return NULL;
    }
}

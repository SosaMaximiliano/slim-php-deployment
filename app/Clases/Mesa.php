<?php

include_once 'Empleado.php';
include_once 'Cliente.php';
include_once 'Manejador.php';
//include_once 'Pedido.php';

class Mesa
{
    private static $estados = array("Con cliente esperando pedido", "Con cliente comiendo", "Con cliente pagando", "Cerrada");
    public static function AltaMesa($idPedido)
    {
        #REVISAR QUE EL PEDIDO EXISTA
        // if (Manejador::ExistePedido($idPedido))
        // {
        #REVISAR QUE HAYA MOZOS
        // $empleados = Empleado::ListarEmpleados();
        $empleados = Empleado::ListarPorSector("Mozo");
        #SI EL MOZO ESTA ATENDIENDO MENOS DE CINCO MESAS
        $random = rand(0, (count($empleados) - 1));
        $mozo = $empleados[$random];
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO mesa (idMozo,idPedido) 
            VALUES (:idMozo,:idPedido)"
        );
        //$consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idMozo', $mozo->id, PDO::PARAM_INT);
        //$consulta->bindValue(':idCliente', $mozo->id, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_STR);
        $consulta->execute();
        // }
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
}

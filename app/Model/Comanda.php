<?php


class Comanda
{
    public static function AltaComanda($idMesa, $idCliente, $cliente, $idEmpleado, $idPedido, $pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO comanda (idMesa, idCliente, cliente, idEmpleado, idPedido, pedido,fecha,hora) 
        VALUES (:idMesa, :idCliente, :cliente, :idEmpleado, :idPedido, :pedido,:fecha,:hora)"
        );
        $fecha = date('Y-m-d');
        $hora = date('H:i:sa');
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':hora', $hora, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idCliente', $idCliente, PDO::PARAM_INT);
        $consulta->bindValue(':cliente', $cliente, PDO::PARAM_STR);
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':pedido', $pedido, PDO::PARAM_STR);
        //$consulta->bindValue(':URLimagen', $URLimagen, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
}

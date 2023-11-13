<?php


class Comanda
{
    public static function AltaComanda($idMesa, $idCliente, $idEmpleado, $idPedido, $pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO comanda (idMesa, idCliente, idEmpleado, idPedido, pedido) 
        VALUES (:idMesa, :idCliente, :idEmpleado, :idPedido, :pedido)"
        );
        //$consulta->bindValue(':fecha', $fecha);
        //$consulta->bindValue(':hora', $hora);
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idCliente', $idCliente, PDO::PARAM_INT);
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':pedido', $pedido, PDO::PARAM_STR);
        //$consulta->bindValue(':URLimagen', $URLimagen, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
}

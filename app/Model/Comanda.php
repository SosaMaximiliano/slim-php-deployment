<?php


class Comanda
{
    public static function AltaComanda($idMesa, $cliente, $idEmpleado, $idPedido, $pedidos)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO Comanda (Fecha,Hora,ID_Mesa,ID_Empleado,ID_Pedido,Pedidos,NombreCliente,Estado) 
        VALUES (:fecha, :hora, :idMesa, :idEmpleado, :idPedido, :pedidos,:nombreCliente,:estado)"
        );
        $fecha = date('Y-m-d');
        $hora = date('H:i:sa');
        $estado = "En preparacion";
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':hora', $hora, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':pedidos', $pedidos, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $cliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        //$consulta->bindValue(':URLimagen', $URLimagen, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ListarComandas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM Comanda"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function ExistePedidoEnComanda($idPedido)
    {
        $comandas = self::ListarComandas();
        foreach ($comandas as $e)
        {
            if ($e->ID_Pedido == $idPedido)
                return true;
        }
        return false;
    }

    public static function ExisteComanda($idComanda)
    {
        $comandas = self::ListarComandas();
        foreach ($comandas as $e)
        {
            if ($e->ID == $idComanda)
                return true;
        }
        return false;
    }

    public static function CambiarEstadoComanda($idComanda, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE Comanda SET Estado = :estado WHERE ID = :idComanda"
        );
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idComanda', $idComanda, PDO::PARAM_INT);
        $consulta->execute();
    }
}

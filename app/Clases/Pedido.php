<?php

include_once 'Producto.php';

class Pedido
{
    // public static function AltaPedido($productos, $idCliente)
    // {
    //     $date = new DateTime();
    //     $fecha = $date->format('Y-m-d');
    //     $hora = $date->format('H:i:sa');
    //     $tiempo = '00:30:00';

    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     foreach ($productos as $e)
    //     {
    //         $idProducto = $e['id'];
    //         $cantidad = $e['cantidad'];

    //         #PREPARO LA QUERY DEL PEDIDO
    //         $consultaInsert = $objAccesoDatos->prepararConsulta(
    //             "INSERT INTO pedido (idProducto,fecha,tiempo,cantidad,estado,idCliente) 
    //                     VALUES (:idProducto,:fecha,:tiempo,:cantidad,:estado,:idCliente)"
    //         );
    //         $consultaInsert->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
    //         $consultaInsert->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
    //         $consultaInsert->bindValue(':fecha', $fecha, PDO::PARAM_STR);
    //         $consultaInsert->bindValue(':estado', "En preparacion", PDO::PARAM_STR);
    //         $consultaInsert->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
    //         $consultaInsert->bindValue(':idCliente', $idCliente, PDO::PARAM_INT);
    //         $consultaInsert->execute();

    //         self::ActualizoStock($idProducto, $cantidad);
    //     }

    //     // #TRAIGO EL ID DEL PEDIDO
    //     // $ultimoId = $objAccesoDatos->obtenerUltimoId();
    //     // return $ultimoId;
    // }

    public static function AltaPedido($idProducto, $cantidad)
    {
        $date = new DateTime();
        $fecha = $date->format('Y-m-d');
        $hora = $date->format('H:i:sa');
        $tiempo = '00:30:00';

        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        #PREPARO LA QUERY DEL PEDIDO
        $consultaInsert = $objAccesoDatos->prepararConsulta(
            "INSERT INTO pedido (idProducto,fecha,tiempo,cantidad,estado,idCliente) 
                        VALUES (:idProducto,:fecha,:tiempo,:cantidad,:estado,:idCliente)"
        );
        $consultaInsert->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
        $consultaInsert->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
        $consultaInsert->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consultaInsert->bindValue(':estado', "En preparacion", PDO::PARAM_STR);
        $consultaInsert->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        //
        $idCliente = 0;
        //
        $consultaInsert->bindValue(':idCliente', $idCliente, PDO::PARAM_INT);
        $consultaInsert->execute();

        self::ActualizoStock($idProducto, $cantidad);

        #TRAIGO EL ID DEL PEDIDO
        $ultimoId = $objAccesoDatos->obtenerUltimoId();
        return $ultimoId;
    }

    public static function ListarPedidos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM pedido"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }


    public static function TraerPedido($idPedido)
    {
        $pedidos = self::ListarPedidos();
        foreach ($pedidos as $e)
        {
            if ($e->id == $idPedido)
                return $e;
        }
    }

    public static function CambiarEstadoPedido($idPedido, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE pedido SET estado = :estado WHERE id = :id"
        );
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function ExistePedido($idPedido)
    {
        $pedidos = self::ListarPedidos();
        foreach ($pedidos as $e)
        {
            if ($e->id == $idPedido)
                return true;
        }
        return false;
    }

    public static function TraerCliente($idPedido)
    {
        $pedidos = self::ListarPedidos();
        foreach ($pedidos as $e)
        {
            if ($e->id == $idPedido)
                return $e;
        }
        return NULL;
    }

    private static function ActualizoStock($idProducto, $cantidad)
    {
        #ACTUALIZO LA CANTIDAD DE PRODUCTOS
        $producto = Producto::BuscarProductoID($idProducto);
        $cantAux = $producto->cantidad - $cantidad;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaUpdate = $objAccesoDatos->prepararConsulta(
            "UPDATE producto SET cantidad = :cantidad WHERE id = :id"
        );
        $consultaUpdate->bindValue(':cantidad', $cantAux, PDO::PARAM_INT);
        $consultaUpdate->bindValue(':id', $idProducto, PDO::PARAM_INT);
        $consultaUpdate->execute();
    }
}

<?php

include_once 'Producto.php';

class Pedido
{
    public static function AltaPedido($productos, $idCliente)
    {
        $pedido = array();
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        foreach ($productos as $e)
        {
            $idProducto = $e['id'];
            $producto = Producto::BuscarProductoID($idProducto);
            $producto = $producto->nombre;
            $cantidad = $e['cantidad'];
            $pedido[] = [$producto => $cantidad];
            self::ActualizoStock($idProducto, $cantidad);
        }

        #PREPARO LA QUERY DEL PEDIDO
        $codigo = Pedido::GenerarCodigo();
        $consultaInsert = $objAccesoDatos->prepararConsulta(
            "INSERT INTO pedido2 (idProducto,producto,cantidad,estado,idCliente,codigo) 
                        VALUES (:idProducto,:producto,:cantidad,:estado,:idCliente,:codigo)"
        );
        $consultaInsert->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
        //$consultaInsert->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
        $consultaInsert->bindValue(':producto', json_encode($pedido), PDO::PARAM_STR);
        $consultaInsert->bindValue(':estado', "Pedido", PDO::PARAM_STR);
        $consultaInsert->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $consultaInsert->bindValue(':idCliente', $idCliente, PDO::PARAM_INT);
        $consultaInsert->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consultaInsert->execute();

        return $codigo;
    }

    public static function ListarPedidos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM pedido2"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function ListarPedidosPorEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM pedido WHERE estado = :estado"
        );
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
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

    public static function TraerPedidoPorNombre($pedido)
    {
        $pedidos = self::ListarPedidos();
        foreach ($pedidos as $e)
        {
            if ($e->producto == $pedido)
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

    public static function GenerarCodigo()
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = '';

        for ($i = 0; $i < 5; $i++)
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];

        return $codigo;
    }
}

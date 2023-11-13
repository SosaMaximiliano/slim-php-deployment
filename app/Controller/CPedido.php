<?php
require_once './Clases/Pedido.php';

class CPedido
{
    private static $estados = array(
        "Pedido",
        "En preparacion",
        "Listo",
        "Entregado"
    );

    // public static function AltaPedido($productos, $idCliente)
    // {
    //     #REVISO QUE HAYA STOCK DEL PRODUCTO
    //     if (Producto::HayStock($productos))
    //     {
    //         Pedido::AltaPedido($productos, $idCliente);
    //     }
    // }

    public static function AltaPedido($idProducto, $cantidad)
    {
        #REVISO QUE HAYA STOCK DEL PRODUCTO
        Pedido::AltaPedido($idProducto, $cantidad);
    }

    public static function ListarPedidos()
    {
        return Pedido::ListarPedidos();
    }

    public static function CambiarEstadoPedido($idPedido, $estado)
    {
        if (Pedido::ExistePedido($idPedido))
        {
            if (in_array($estado, self::$estados))
            {
                Pedido::CambiarEstadoPedido($idPedido, $estado);
            }
            else
                throw new Exception("Estado incorrecto", 200);
        }
        else
            throw new Exception("Pedido inexistente", 200);
    }
}

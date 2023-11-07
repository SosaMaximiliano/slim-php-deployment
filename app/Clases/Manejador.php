<?php

class Manejador
{
    public static function ExistePedido($idPedido)
    {
        $pedidos = Pedido::ListarPedidos();
        var_dump($pedidos);
        foreach ($pedidos as $e)
            if ($e->id === $idPedido)
                if ($e->estado === "En preparacion")
                    return true;
        return false;
    }

    public static function ExisteProducto($idProducto)
    {
        $productos = Producto::ListarProductos();
        foreach ($productos as $e)
            if ($e->id === $idProducto)
                if ($e->cantidad > 0)
                    return true;
        return false;
    }

    public static function ExisteEmpleado($idEmpleado)
    {
        $empleados = Empleado::ListarEmpleados();
        foreach ($empleados as $e)
            if ($e->id === $idEmpleado)
                return $e->sector;
        return false;
    }

    public static function ExisteMesa($idMesa)
    {
        $mesas = Mesa::ListarMesas();
        foreach ($mesas as $e)
            if ($e->id === $idMesa)
                if ($e->estado != "")
                    return $e->estado;
        return false;
    }

    // public static function ExisteCliente($idCliente)
    // {
    //     $clientes = Pedido::ListarClientes();
    //         foreach ($clientes as $e)
    //             if ($e->estado === "En preparacion")
    //                 return true;
    //     return false;
    // }
}

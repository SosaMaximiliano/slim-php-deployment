<?php
require_once './Model/Comanda.php';
require_once './Model/Mesa.php';

class CComanda
{
    public static function AltaComanda($idMesa)
    {
        $idCliente = Mesa::TraerIDCliente($idMesa);
        $idEmpleado = Mesa::TraerIDEmpleado($idMesa);
        $pedido = Cliente::TraerPedido($idCliente);
        var_dump($pedido);
        $producto = $pedido->pedido;
        $idPedido = $pedido->idPedido;
        $cliente = $pedido->nombre;
        Comanda::AltaComanda($idMesa, $idCliente, $cliente, $idEmpleado, $idPedido, $producto);
    }
}

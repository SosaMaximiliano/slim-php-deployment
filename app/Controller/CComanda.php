<?php
require_once './Clases/Comanda.php';
require_once './Clases/Mesa.php';

class CComanda
{
    public static function AltaComanda($idMesa)
    {
        $idCliente = Mesa::TraerIDCliente($idMesa);
        $idPedido = Mesa::TraerIDPedido($idMesa);
        $idEmpleado = Mesa::TraerIDEmpleado($idMesa);
        //$pedido = Cliente::TraerPedido($idPedido);
        $pedido = "pedido";
        //$fecha = date('Y-m-d');
        //$hora = time("H:i:sa");
        Comanda::AltaComanda($idMesa, $idCliente, $idEmpleado, $idPedido, $pedido);
    }
}

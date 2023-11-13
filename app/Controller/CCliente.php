<?php
require_once './Clases/Cliente.php';

class CCliente
{
    public static function AltaCliente($nombre, $idProducto, $cantidad)
    {
        #HACER VALIDACIONES
        Cliente::AltaCliente($nombre, $idProducto, $cantidad);
    }
}

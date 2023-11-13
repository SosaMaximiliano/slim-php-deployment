<?php
require_once './Clases/Mesa.php';

class CMesa
{
    private static $estados = array(
        "Con cliente esperando pedido",
        "Con cliente comiendo",
        "Con cliente pagando",
        "Cerrada"
    );

    public static function AltaMesa($idCliente)
    {
        #HACER VALIDACIONES
        #UN CLIENTE NO PUEDE TENER MAS DE UNA MESA
        if (Cliente::ExisteCliente($idCliente))
        {
            if (!Mesa::ExisteReserva($idCliente))
            {
                $mozo = Mesa::TraerMozo();
                $pedido = Cliente::TraerPedido($idCliente);
                $idMozo = $mozo->id;
                $idPedido = $pedido->idPedido;
                if ($idPedido != NULL)
                    Mesa::AltaMesa($idPedido, $idMozo, $idCliente);
                else
                    echo "No existe pedido";
            }
            else
                echo "Ya existe una reserva para ese cliente";
        }
        else
            echo "No existe cliente";
    }

    public static function ListarMesas()
    {
        return Mesa::ListarMesas();
    }

    public static function CambiarEstadoMesa($idMesa, $estado)
    {
        Mesa::CambiarEstadoMesa($idMesa, $estado);
    }
}

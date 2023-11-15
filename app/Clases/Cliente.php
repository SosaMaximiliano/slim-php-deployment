<?php

class Cliente
{
    public static function AltaCliente($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaInsert = $objAccesoDatos->prepararConsulta(
            "INSERT INTO cliente (nombre) 
                        VALUES (:nombre)"
        );
        $consultaInsert->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consultaInsert->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ListarClientes()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM cliente"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cliente');
    }

    public static function TraerPedido($idCliente)
    {
        $clientes = self::ListarClientes();
        foreach ($clientes as $e)
        {
            if ($e->id == $idCliente)
                return $e;
        }
    }

    public static function  ExisteCliente($idCliente)
    {
        $clientes = self::ListarClientes();
        foreach ($clientes as $e)
        {
            if ($e->id == $idCliente)
                return true;
        }
        return false;
    }
}

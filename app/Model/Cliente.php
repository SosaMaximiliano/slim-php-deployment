<?php

class Cliente
{

    public static $clientes = array(
        "Adriana",
        "Benjamín",
        "Camila",
        "Diego",
        "Estefanía",
        "Federico",
        "Gabriela",
        "Héctor",
        "Inés",
        "Juan",
        "Karina",
        "Leonardo",
        "Marcela",
        "Natalia",
        "Óscar",
        "Paola",
        "Quirino",
        "Rocío",
        "Santiago",
        "Valeria"

    );

    public static function AltaCliente($nombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaInsert = $objAccesoDatos->prepararConsulta(
            "INSERT INTO Cliente (Nombre) 
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
            "SELECT * FROM Cliente"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cliente');
    }

    public static function TraerPedido($idCliente)
    {
        $clientes = self::ListarClientes();
        foreach ($clientes as $e)
        {
            if ($e->ID == $idCliente)
                return $e;
        }
    }

    public static function  ExisteCliente($idCliente)
    {
        $clientes = self::ListarClientes();
        foreach ($clientes as $e)
        {
            if ($e->ID == $idCliente)
                return true;
        }
        return false;
    }
}

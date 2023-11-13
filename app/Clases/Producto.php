<?php


class Producto
{
    public static function AltaProducto($nombre, $cantidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO producto (nombre,cantidad) 
            VALUES (:nombre,:cantidad)"
        );
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function ListarProductos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM producto");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function BuscarProductoID($idProducto)
    {
        $productos = self::ListarProductos();
        foreach ($productos as $e)
            if ($e->id == $idProducto)
                return $e;
        return null;
    }

    public static function BuscarProductoNombre($nombre)
    {
        $productos = self::ListarProductos();
        foreach ($productos as $e)
            if ($e->nombre === $nombre)
                return $e;
        return null;
    }

    public static function HayStock($productos)
    {
        $listado = self::ListarProductos();
        $sinStock = [];

        foreach ($productos as $e)
        {
            $idProducto = $e['id'];
            $cantidad = $e['cantidad'];
            $hayStock = false;

            foreach ($listado as $f)
                if ($f['id'] == $idProducto)
                    if ($f['cantidad'] >= $cantidad)
                    {
                        $hayStock = true;
                        break;
                    }

            if (!$hayStock)
                $sinStock[] = $idProducto;
        }

        if (!empty($sinStock))
        {
            throw new Exception("No hay stock suficiente", 1);
            return false;
        }
        else
            return true;
    }
}

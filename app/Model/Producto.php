<?php


class Producto
{
    public static function AltaProducto($nombre, $cantidad, $precio, $tiempo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO Producto (Nombre,Cantidad,Precio,Tiempo) 
            VALUES (:nombre,:cantidad,:precio,:tiempo)"
        );
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':precio', $precio, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function ListarProductos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Producto");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function BuscarProductoID($idProducto)
    {
        $productos = self::ListarProductos();
        foreach ($productos as $e)
            if ($e->ID == $idProducto)
                return $e;
        return null;
    }

    public static function BuscarProductoNombre($nombre)
    {
        $productos = self::ListarProductos();
        foreach ($productos as $e)
            if ($e->Nombre === $nombre)
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
                if ($f->ID == $idProducto)
                    if ($f->Cantidad >= $cantidad)
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

    public static function ActualizarStock($nombre, $cantidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE Producto SET Cantidad = Cantidad + :cantidad WHERE Nombre = :nombre"
        );
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_STR);
        $consulta->execute();
    }
}

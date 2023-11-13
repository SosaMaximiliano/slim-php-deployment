<?php
require_once './Clases/Producto.php';

class CProducto
{
    public static function AgregarProducto($nombre, $cantidad)
    {
        #VALIDAR CAMPOS

        #SI EL PRODUCTO EXISTE LO SUMO
        $p = Producto::BuscarProductoNombre($nombre);
        if ($p === NULL)
        {
            try
            {
                Producto::AltaProducto($nombre, $cantidad);
            }
            catch (Exception $e)
            {
                throw new Exception("No se pudo ingresar el producto. {$e->getMessage()}");
            }
        }
        else
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
                "UPDATE producto SET cantidad = cantidad + :cantidad WHERE nombre = :nombre"
            );
            $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_STR);
            $consulta->execute();
        }
    }

    public static function ListarProductos()
    {
        return Producto::ListarProductos();
    }

    public static function BuscarProductoID($idProducto)
    {
        return Producto::BuscarProductoID($idProducto);
    }

    public static function BuscarProductoNombre($nombre)
    {
        return Producto::BuscarProductoNombre($nombre);
    }

    public static function HayStock($productos)
    {
    }
}

<?php

include_once 'Producto.php';

class Pedido
{
    public static function AltaPedido($idProducto, $cantidad)
    {
        //DEPENDE EL PEDIDO ASIGNAR UN TIEMPO
        $fecha = new DateTime();
        $fecha = $fecha->format('Y-m-d');
        // $tiempo = time() + (30 * 60);
        $tiempo = '00:30:00';
        $cantAux = 0;
        $productos = array();

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO pedido (idProducto,fecha,tiempo,cantidad,estado) 
            VALUES (:idProducto,:fecha,:tiempo,:cantidad,:estado)"
        );
        $consulta->bindValue(':idProducto', $idProducto, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "En preparacion", PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);

        $productos[] = Producto::ListarProductos();
        foreach ($productos as $producto)
        {
            foreach ($producto as $e)
            {
                if ($e->id == $idProducto)
                {
                    if ($e->cantidad > 0)
                    {
                        if ($e->cantidad >= $cantidad)
                        {
                            $cantAux = $e->cantidad - $cantidad;
                            $consulta->execute();
                            $consulta = $objAccesoDatos->prepararConsulta(
                                "UPDATE producto SET cantidad = :cantidad WHERE id = :idProducto"
                            );
                            $consulta->bindValue(':cantidad', $cantAux, PDO::PARAM_INT);
                            $consulta->bindValue(':idProducto', $idProducto, PDO::PARAM_INT);
                            $consulta->execute();
                            return;
                        }
                        else
                        {
                            echo "No hay stock suficiente";
                            return;
                        }
                    }
                    else
                    {
                        echo "No hay stock";
                        return;
                    }
                }
            }
        }
    }

    public static function ListarPedidos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM pedido"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    // public static function CambiarEstadoPedido($idPedido, $estado)
    // {
    //     #CONFIRMAR QUE EXISTE EL PEDIDO
    //     $pedidos
    // }
}

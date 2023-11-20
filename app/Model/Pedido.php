<?php

include_once 'Producto.php';
include_once '../app/Utils/Utils.php';

class Pedido
{
    public static function AltaPedido($productos, $idMesa)
    {
        $pedido = array();
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $tiempoEstimado = '00:00';
        $valorTotal = 0;
        foreach ($productos as $e)
        {
            $idProducto = $e['id'];
            $producto = Producto::BuscarProductoID($idProducto);
            $productoNombre = $producto->Nombre;
            $cantidad = $e['cantidad'];
            $pedido[] = [$productoNombre => $cantidad];
            self::ActualizoStock($idProducto, $cantidad);

            $tiempoEstimado = self::CalcularTiempoEstimado($tiempoEstimado, $producto->Tiempo);
            $valorTotal += $producto->Precio;
        }

        #PREPARO LA QUERY DEL PEDIDO
        $codigo = Utils::GenerarCodigo();
        $consultaInsert = $objAccesoDatos->prepararConsulta(
            "INSERT INTO Pedido (Productos,ID_Mesa,CodigoUnico,TiempoEstimado,ValorTotal) 
                        VALUES (:productos,:idMesa,:codigo,:tiempo,:valorTotal)"
        );
        $consultaInsert->bindValue(':tiempo', $tiempoEstimado, PDO::PARAM_STR);
        $consultaInsert->bindValue(':productos', json_encode($pedido), PDO::PARAM_STR);
        $consultaInsert->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consultaInsert->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consultaInsert->bindValue(':valorTotal', $valorTotal, PDO::PARAM_INT);
        $consultaInsert->execute();

        return $codigo;
    }

    public static function ListarPedidos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM Pedido"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function ListarPedidosPorEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM Pedido WHERE Estado = :estado"
        );
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function TraerPedido($idPedido)
    {
        $pedidos = self::ListarPedidos();
        foreach ($pedidos as $e)
        {
            if ($e->ID == $idPedido)
                return $e;
        }
    }

    public static function TraerPedidoPorNombre($pedido)
    {
        $pedidos = self::ListarPedidos();
        foreach ($pedidos as $e)
        {
            if ($e->Producto == $pedido)
                return $e;
        }
    }

    public static function CambiarEstadoPedido($idPedido, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE Pedido SET Estado = :estado WHERE ID = :id"
        );
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function ExistePedido($idPedido)
    {
        $pedidos = self::ListarPedidos();
        foreach ($pedidos as $e)
        {
            if ($e->ID == $idPedido)
                return true;
        }
        return false;
    }

    public static function TraerCliente($idPedido)
    {
        $pedidos = self::ListarPedidos();
        foreach ($pedidos as $e)
        {
            if ($e->ID == $idPedido)
                return $e;
        }
        return NULL;
    }

    private static function ActualizoStock($idProducto, $cantidad)
    {
        #ACTUALIZO LA CANTIDAD DE PRODUCTOS
        $producto = Producto::BuscarProductoID($idProducto);
        $cantAux = $producto->Cantidad - $cantidad;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaUpdate = $objAccesoDatos->prepararConsulta(
            "UPDATE Producto SET Cantidad = :cantidad WHERE ID = :id"
        );
        $consultaUpdate->bindValue(':cantidad', $cantAux, PDO::PARAM_INT);
        $consultaUpdate->bindValue(':id', $idProducto, PDO::PARAM_INT);
        $consultaUpdate->execute();
    }

    private static function CalcularTiempoEstimado($tPedido, $tProducto)
    {
        $tiempoPr = explode(':', $tProducto);
        $horasPr = intval($tiempoPr[0]);
        $minutosPr = intval($tiempoPr[1]);

        $tiempoPd = explode(':', $tPedido);
        $horasPd = intval($tiempoPd[0]);
        $minutosPd = intval($tiempoPd[1]);

        $tprAux = ($horasPr * 60 + $minutosPr);
        $tpdAux = ($horasPd * 60 + $minutosPd);

        if ($tprAux > $tpdAux)
            return sprintf("%02d:%02d", floor($tprAux / 60), $tprAux % 60);
        else
            return sprintf("%02d:%02d", floor($tpdAux / 60), $tpdAux % 60);
    }
}

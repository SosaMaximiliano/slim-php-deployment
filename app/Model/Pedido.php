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
            $idProducto = $e['Producto'];
            $producto = Producto::BuscarProductoID($idProducto);
            $productoNombre = $producto->Nombre;
            $sector = $producto->Sector;
            $estado = 'Pedido';
            $cantidad = $e['Cantidad'];
            $tiempoEstimado = self::CalcularTiempoEstimado($tiempoEstimado, $producto->Tiempo);
            $pedido[] = [
                'Producto' => $productoNombre,
                'Cantidad' => $cantidad,
                'Sector'   => $sector,
                'Tiempo'   => $tiempoEstimado,
                'Estado'   => $estado
            ];
            self::ActualizoStock($idProducto, $cantidad);

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

    public static function ListarPedidosPorSector($sector)
    {
        $psector = [];
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT Productos FROM Pedido;"
        );
        $consulta->execute();

        $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');

        foreach ($pedidos as $pedido)
        {
            $productos = json_decode($pedido->Productos);
            foreach ($productos as $producto)
            {
                if ($producto->Sector == $sector)
                    $psector[] = $producto;
            }
        }

        return $psector;
    }

    public static function ListarPedidosObj()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM Pedido"
        );
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function ListarPedidos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM Pedido"
        );
        $consulta->execute();

        $pedidos = $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');

        foreach ($pedidos as $pedido)
        {
            $productos = json_decode($pedido->Productos);
            foreach ($productos as $producto)
            {
                $psector[] = $producto;
            }
        }

        return $psector;
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
        $pedidos = self::ListarPedidosObj();
        foreach ($pedidos as $e)
        {
            if ($e->ID == $idPedido)
                return $e;
        }
    }

    public static function TraerPedidoPorID($idPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM Pedido WHERE ID = :idPedido"
        );
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function TraerPedidoPorClave($clave)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM Pedido WHERE CodigoUnico = :clave"
        );
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function TraerIdMesaPorPedido($idPedido)
    {
        $pedidos = self::ListarPedidosObj();
        foreach ($pedidos as $e)
        {
            if ($e->ID == $idPedido)
                return $e->ID_Mesa;
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

    public static function CambiarEstadoPedidoPorSector($idPedido, $estado, $sector)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        # OBTENER EL PEDIDO POR ID
        $pedido = self::TraerPedido($idPedido);

        if (!$pedido)
        {
            return;
        }

        # OBTENER LOS PRODUCTOS DEL PEDIDO
        $productos = json_decode($pedido->Productos);

        foreach ($productos as &$producto)
        {
            if ($producto->Sector == $sector)
            {
                $producto->Estado = $estado;
            }
        }

        $aux = Utils::DameUnEmpleado($sector);
        Empleado::SumarOperacion($aux->ID);

        # ACTUALIZAR EL PEDIDO EN LA BASE DE DATOS
        $consulta = $objAccesoDatos->prepararConsulta(
            "UPDATE Pedido SET Productos = :productos WHERE ID = :id"
        );
        $consulta->bindValue(':id', $idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':productos', json_encode($productos), PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function ExistePedido($idPedido)
    {
        $pedidos = self::ListarPedidosObj();
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

<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once './Model/Producto.php';

class CProducto
{
    public static function AgregarProducto(Request $request, Response $response, $args)
    {
        #VALIDAR CAMPOS
        $parametros = $request->getParsedBody();
        $nombre = $parametros['Nombre'];
        $cantidad = $parametros['Cantidad'];
        $precio = $parametros['Precio'];
        $tiempo = $parametros['Tiempo'];

        $p = Producto::BuscarProductoNombre($nombre);
        if ($p === NULL)
        {
            try
            {
                Producto::AltaProducto($nombre, $cantidad, $precio, $tiempo);
                $payload = json_encode("{$nombre} agregado al stock.");
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
            catch (Exception $e)
            {
                $payload = json_encode("No se pudo ingresar el producto. {$e->getMessage()}");
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        }
        else
        #SI EL PRODUCTO EXISTE LO SUMO
        {
            Producto::ActualizarStock($nombre, $cantidad);
            $payload = json_encode("Producto agregado al stock.");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function ListarProductos(Request $request, Response $response, $args)
    {
        $payload = json_encode(Producto::ListarProductos());
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function BuscarProductoID(Request $request, Response $response, $args)
    {
        $parametros = $request->getQueryParams();
        $idProducto = $parametros['ID'];
        $payload = json_encode(Producto::BuscarProductoID($idProducto));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function BuscarProductoNombre(Request $request, Response $response, $args)
    {
        $parametros = $request->getQueryParams();
        $nombre = $parametros['Nombre'];
        $payload = json_encode(Producto::BuscarProductoNombre($nombre));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

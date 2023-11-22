<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once './Model/Pedido.php';

class CPedido
{
    private static $estados = array(
        "Pedido",
        "En preparacion",
        "Listo para servir",
        "Entregado"
    );

    public static function AltaPedido(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $productos = $parametros['Productos'];
        $idMesa = $parametros['ID_Mesa'];
        if (Mesa::ExisteMesa($idMesa))
        {
            if (Mesa::MesaLibre($idMesa))
            {
                #REVISO QUE HAYA STOCK DEL PRODUCTO
                if (Producto::HayStock($productos))
                {
                    try
                    {
                        Pedido::AltaPedido($productos, $idMesa);
                        $payload = json_encode("Pedido creado");
                        $response->getBody()->write($payload);
                        return $response->withHeader('Content-Type', 'application/json');
                    }
                    catch (Exception $e)
                    {
                        $payload = json_encode("No se pudo tomar el pedido. {$e->getMessage()}");
                        $response->getBody()->write($payload);
                        return $response->withHeader('Content-Type', 'application/json');
                    }
                }
            }
            else
            {
                $payload = json_encode("La mesa {$idMesa} esta ocupada.");
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        }
        else
        {
            $payload = json_encode("La mesa {$idMesa} no se encuentra disponible.");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function ListarPedidos(Request $request, Response $response, $args)
    {
        $payload = json_encode(Pedido::ListarPedidosObj());
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ListarPedidosPorSector(Request $request, Response $response, $args)
    {
        $parametros = $request->getQueryParams();
        $sector = $parametros['Sector'];
        $payload = json_encode(Pedido::ListarPedidosPorSector($sector));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerPedidoPorClave(Request $request, Response $response, $args)
    {
        $parametros = $request->getQueryParams();
        $clave = $parametros['Clave_Unica'];
        $payload = json_encode(Pedido::TraerPedidoPorClave($clave));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerPedidoPorID(Request $request, Response $response, $args)
    {
        $parametros = $request->getQueryParams();
        $idPedido = $parametros['ID'];
        $payload = json_encode(Pedido::TraerPedido($idPedido));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function CambiarEstadoPedido(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['ID_Pedido'];
        $estado = $parametros['Estado'];

        if (Pedido::ExistePedido($idPedido))
        {
            if (in_array($estado, self::$estados))
            {
                try
                {
                    Pedido::CambiarEstadoPedido($idPedido, $estado);
                    $payload = json_encode("Estado del pedido cambiado a {$estado}");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
                catch (Exception $e)
                {
                    $payload = json_encode("Error al cambiar de estado. {$e->getMessage()}");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
            }
            else
                throw new Exception("Estado incorrecto", 200);
        }
        else
            throw new Exception("Pedido inexistente", 200);
    }

    public static function CambiarEstadoPedidoPorSector(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['ID_Pedido'];
        $estado = $parametros['Estado'];
        $sector = $parametros['Sector'];

        if (Pedido::ExistePedido($idPedido))
        {
            if (in_array($estado, self::$estados))
            {
                try
                {
                    Pedido::CambiarEstadoPedidoPorSector($idPedido, $estado, $sector);
                    $payload = json_encode("Estado del pedido cambiado a {$estado}");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
                catch (Exception $e)
                {
                    $payload = json_encode("Error al cambiar de estado. {$e->getMessage()}");
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
            }
            else
                throw new Exception("Estado incorrecto", 200);
        }
        else
            throw new Exception("Pedido inexistente", 200);
    }

    public static function CargarImagen(Request $request, Response $response, $args)
    {
        $filename = $request->getParsedBody()['ID_Pedido'];
        $uploadedFile = $request->getUploadedFiles()['Imagen'];
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        if ($uploadedFile->getError() === UPLOAD_ERR_OK)
        {
            $uploadedFile->moveTo("./ImagenesDePedidos/ID_Pedido-$filename.$extension");
            $response->getBody()->write('Archivo cargado correctamente.');
        }
        else
        {
            $response->getBody()->write('Error al cargar el archivo.');
        }
        return $response;
    }
}

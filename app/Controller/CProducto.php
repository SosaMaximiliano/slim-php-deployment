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
        $sector = $parametros['Sector'];

        $p = Producto::BuscarProductoNombre($nombre);
        if ($p === NULL)
        {
            try
            {
                Producto::AltaProducto($nombre, $cantidad, $precio, $tiempo, $sector);
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

    public static function CargarProductosCSV(Request $request, Response $response, $args)
    {
        $uploadedFile = $request->getUploadedFiles()['Archivo'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK)
        {
            $filename = $uploadedFile->getClientFilename();
            $uploadedFile->moveTo("$filename");
            Producto::CargarCSV($filename);
            $response->getBody()->write('Archivo cargado correctamente.');
        }
        else
        {
            $response->getBody()->write('Error al cargar el archivo.');
        }
        return $response;
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

    public function ExportarTabla(Request $request, Response $response, $args)
    {
        try
        {
            $archivo = CSV::ExportarTabla("Productos.csv");
            if (file_exists($archivo) && filesize($archivo) > 0)
            {
                $payload = json_encode(array("mensaje" => "http://localhost/COMANDA/app/" . $archivo));
            }
            else
            {
                $payload = json_encode(array("mensaje" => "Error, verifique la informacion ingresada"));
            }

            $response->getBody()->write($payload);

            return $response->withHeader('Content-Type', 'application/json');
        }
        catch (Throwable $mensaje)
        {
            printf("Error al listar: <br> $mensaje .<br>");
        }
        finally
        {
            return $response->withHeader('Content-Type', 'text/csv');
        }
    }

    public function ImportarTabla(Request $request, Response $response, $args)
    {
        try
        {
            $archivo = ($_FILES["archivoCSV"]);
            Producto::CargarCSV($archivo["tmp_name"]);
            $payload = json_encode(array("mensaje" => "La base de datos fue actualizada correctamente"));
        }
        catch (Throwable $mensaje)
        {
            $payload = json_encode(array("mensaje" => $mensaje->getMessage()));
        }
        finally
        {
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'text/csv');
        }
    }
}

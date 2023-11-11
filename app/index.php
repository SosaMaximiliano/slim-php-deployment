<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/./db/AccesoDatos.php';
require __DIR__ . '/./Controller/CEmpleado.php';
require __DIR__ . '/./Clases/Producto.php';
require __DIR__ . '/./Clases/Mesa.php';
require __DIR__ . '/./Clases/Pedido.php';


// Instantiate App
$app = AppFactory::create();

// Set base path
$app->setBasePath('/COMANDA/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

////////////////////////////////
// $app->group('/empleados', function (RouteCollectorProxy $group)
// {
//     $group->post('/AltaEmpleado',  \CEmpleado::class . ':CargarEmpleado');
//     $group->get('/Listar',  \CEmpleado::class . ':Listar');
//     $group->delete('/borrarUsuario', \CEmpleado::class . ':BorrarUno');
// });
////////////////////////////////

#region PRODUCTOS
$app->post('/AltaProducto', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $nombre = $data['nombre'];
    $cantidad = $data['cantidad'];
    Producto::AltaProducto($nombre, $cantidad);
    $payload = json_encode(array('message' => 'Producto agregado con exito'));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/ListarProductos', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    //$idProducto = $data['idProducto'];
    $salida = Producto::ListarProductos();
    $payload = json_encode(array('message' => $salida));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});
#endregion

#region EMPLEADOS
$app->post('/AltaEmpleado', function (Request $request, Response $response, $args)
{
    try
    {
        CEmpleado::CargarEmpleado($request, $response, $args);
        $payload = json_encode(array('message' => 'Empleado agregado con exito'));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $e)
    {
        echo "El empleado no pudo ser creado. {$e->getMessage()}";
    }
});

$app->get('/ListarEmpleados', function (Request $request, Response $response, $args)
{
    $salida = CEmpleado::Listar();
    $payload = json_encode(array('message' => $salida));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

#VERSION 1
$app->get('/ListarPorSector', function (Request $request, Response $response, $args)
{
    $salida = CEmpleado::ListarSector($request);
    $payload = json_encode(array('message' => $salida));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});
#VERSION 2
// $app->get('/ListarPorSector/{sector}', function (Request $request, Response $response, $args)
// {
//     $salida = CEmpleado::ListarSector($args['sector']);
//     var_dump($salida);
//     $payload = json_encode(array('message' => $salida));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });


$app->put('/AsignarSector', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $idEmpleado = $data['id'];
    $sector = $data['sector'];
    CEmpleado::AsignarPuesto($idEmpleado, $sector);
    $payload = json_encode(array('message' => "Sector asignado"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

#endregion

#region PEDIDOS
$app->post('/AltaPedido', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $idProducto = $data['idProducto'];
    $cantidad = $data['cantidad'];
    Pedido::AltaPedido($idProducto, $cantidad);
    $payload = json_encode(array('message' => 'Pedido agregado con exito'));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/ListarPedidos', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    //$idProducto = $data['idProducto'];
    $salida = Pedido::ListarPedidos();
    $payload = json_encode(array('message' => $salida));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

#endregion

#region MESAS
$app->post('/AltaMesa', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $idPedido = $data['idPedido'];
    Mesa::AltaMesa($idPedido);
    $payload = json_encode(array('message' => 'Mesa agregada con exito'));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/ListarMesas', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    //$idProducto = $data['idProducto'];
    $salida = Mesa::ListarMesas();
    $payload = json_encode(array('message' => $salida));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/CambiarEstadoMesa', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $idMesa = $data['id'];
    $estado = $data['estado'];
    Mesa::CambiarEstadoMesa($idMesa, $estado);
    $payload = json_encode(array('message' => "Estado cambiado"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

#endregion



$app->run();

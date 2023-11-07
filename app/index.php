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
require __DIR__ . '/./Clases/Empleado.php';
require __DIR__ . '/./Clases/Producto.php';
require __DIR__ . '/./Clases/Mesa.php';
require __DIR__ . '/./Clases/Pedido.php';


$_ENV['MYSQL_DB'] = 'comanda';
$_ENV['MYSQL_HOST'] = 'localhost';
$_ENV['MYSQL_USER'] = 'root';
$_ENV['MYSQL_PASS'] = '';


// Instantiate App
$app = AppFactory::create();

// Set base path
$app->setBasePath('/COMANDA/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

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

$app->post('/AltaEmpleado', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $nombre = $data['nombre'];
    $apellido = $data['apellido'];
    Empleado::AltaEmpleado($nombre, $apellido);
    $payload = json_encode(array('message' => 'Empleado agregado con exito'));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/AltaMesa', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $idPedido = $data['idPedido'];
    Mesa::AltaMesa($idPedido);
    $payload = json_encode(array('message' => 'Mesa agregada con exito'));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

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

$app->get('/ListarProductos', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    //$idProducto = $data['idProducto'];
    $salida = Producto::ListarProductos();
    $payload = json_encode(array('message' => $salida));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/ListarEmpleados', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    //$idProducto = $data['idProducto'];
    $salida = Empleado::ListarEmpleados();
    $payload = json_encode(array('message' => $salida));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/ListarPorSector', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $sector = $data['sector'];
    $salida = Empleado::ListarPorSector($sector);
    $payload = json_encode(array('message' => $salida));
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


/**************** */
$app->put('/AsignarSector', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $idEmpleado = $data['id'];
    $sector = $data['sector'];
    Empleado::AsignarSector($idEmpleado, $sector);
    $payload = json_encode(array('message' => "Sector asignado"));
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

$app->run();

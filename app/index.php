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
require __DIR__ . '/./Controller/CProducto.php';
require __DIR__ . '/./Controller/CMesa.php';
require __DIR__ . '/./Controller/CPedido.php';
require __DIR__ . '/./Controller/CCliente.php';
require __DIR__ . '/./Controller/CComanda.php';
require __DIR__ . '/./Middleware/AuthMiddleware.php';
require __DIR__ . '/./Middleware/ValidadorMiddleware.php';


// Instantiate App
$app = AppFactory::create();

// Set base path
$app->setBasePath('/COMANDA/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->group('/empleados', function (RouteCollectorProxy $group)
{
    $group->post('/alta', \CEmpleado::class . ':IngresarEmpleado')
        ->add(new ValidadorMiddleware());
    $group->get('/listar', \CEmpleado::class . ':ListarEmpleados');
    $group->get('/listarporsector', \CEmpleado::class . ':ListarEmpleadosPorSector');
    $group->put('/asignarsector', \CEmpleado::class . ':AsignarSector');
    //$group->delete('/borrarUsuario', \CEmpleado::class . ':BorrarUno');
});
// })->add(new AuthMiddleware());

$app->group('/productos', function (RouteCollectorProxy $group)
{
    $group->post('/alta', \CProducto::class . ':AgregarProducto');
    $group->get('/listar', \CProducto::class . ':ListarProductos');
    $group->get('/buscarporid', \CProducto::class . ':BuscarProductoID');
    $group->get('/buscarpornombre', \CProducto::class . ':BuscarProductoNombre');
});

$app->group('/pedidos', function (RouteCollectorProxy $group)
{
    $group->post('/alta', \CPedido::class . ':AltaPedido');
    $group->get('/listar', \CPedido::class . ':ListarPedidos');
    $group->get('/listarporcliente', \CPedido::class . ':ListarPedidosPorCliente');
    $group->put('/cambiarestado', \CPedido::class . ':CambiarEstadoPedido');
});

$app->group('/mesas', function (RouteCollectorProxy $group)
{
    $group->post('/alta', \CMesa::class . ':AltaMesa');
    $group->get('/listar', \CMesa::class . ':ListarMesas');
    //$group->get('/listarporcliente', \CMesa::class . ':ListarPedidosPorCliente');
    $group->put('/cambiarestado', \CMesa::class . ':CambiarEstadoMesa');
});

#region MESAS
// $app->post('/AltaMesa', function (Request $request, Response $response)
// {
//     $data = $request->getParsedBody();
//     $idCliente = $data['idCliente'];
//     CMesa::AltaMesa($idCliente);
//     $payload = json_encode(array('message' => 'Mesa agregada con exito'));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->get('/ListarMesas', function (Request $request, Response $response)
// {
//     $salida = CMesa::ListarMesas();
//     $payload = json_encode(array('message' => $salida));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->put('/CambiarEstadoMesa', function (Request $request, Response $response)
// {
//     $data = $request->getParsedBody();
//     $idMesa = $data['id'];
//     $estado = $data['estado'];
//     CMesa::CambiarEstadoMesa($idMesa, $estado);
//     $payload = json_encode(array('message' => "Estado cambiado"));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });

#endregion

#region CLIENTES
$app->post('/AltaCliente', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $nombre = $data['nombre'];
    $idProducto = $data['idProducto'];
    $cantidad = $data['cantidad'];
    CCliente::AltaCliente($nombre, $idProducto, $cantidad);
    $payload = json_encode(array('message' => 'Cliente realizó pedido'));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});
#endregion

#region COMANDAS
$app->post('/AltaComanda', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $idMesa = $data['idMesa'];
    CComanda::ALtaComanda($idMesa);
    $payload = json_encode(array('message' => 'Comanda creada'));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

#endregion

$app->run();

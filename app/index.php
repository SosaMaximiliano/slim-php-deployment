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
require __DIR__ . '/./Controller/CAuthJWT.php';
require __DIR__ . '/./Middleware/AuthMiddleware.php';
require __DIR__ . '/./Middleware/ValidadorMiddleware.php';
require __DIR__ . '/./Middleware/CheckTokenMiddleware.php';
require __DIR__ . '/./Middleware/CheckMozoMiddleware.php';
require __DIR__ . '/./Middleware/CheckSocioMiddleware.php';


// Instantiate App
$app = AppFactory::create();

// Set base path
$app->setBasePath('/COMANDA/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes

$app->post('/login', \CAuthJWT::class . ':CrearTokenLogin');

$app->group('/usuarios', function (RouteCollectorProxy $group)
{
    $group->post('/empleado_alta', \CEmpleado::class . ':IngresarEmpleado')->add(new ValidadorMiddleware())->add(new CheckSocioMiddleware());
    $group->get('/empleado_listar', \CEmpleado::class . ':ListarEmpleados')->add(new CheckSocioMiddleware());
    $group->get('/empleado_listarporsector', \CEmpleado::class . ':ListarEmpleadosPorSector')->add(new CheckSocioMiddleware());
    $group->put('/empleado_asignarsector', \CEmpleado::class . ':AsignarSector')->add(new CheckSocioMiddleware());
    //$group->delete('/borrarUsuario', \CEmpleado::class . ':BorrarUno');

    $group->post('/producto_alta', \CProducto::class . ':AgregarProducto');
    $group->get('/producto_listar', \CProducto::class . ':ListarProductos');
    $group->get('/producto_buscarporid', \CProducto::class . ':BuscarProductoID');
    $group->get('/producto_buscarpornombre', \CProducto::class . ':BuscarProductoNombre');

    $group->post('/pedido_alta', \CPedido::class . ':AltaPedido')->add(new CheckMozoMiddleware());
    $group->get('/pedido_listar', \CPedido::class . ':ListarPedidos')->add(new CheckMozoMiddleware());
    $group->get('/pedido_listarporcliente', \CPedido::class . ':ListarPedidosPorCliente')->add(new CheckMozoMiddleware());
    $group->put('/pedido_cambiarestado', \CPedido::class . ':CambiarEstadoPedido')->add(new CheckMozoMiddleware());

    $group->post('/mesa_alta', \CMesa::class . ':AltaMesa');
    $group->put('/mesa_abrir', \CMesa::class . ':AbrirMesa');
    $group->get('/mesa_listar', \CMesa::class . ':ListarMesas');
    //$group->get('/mesa_listarporcliente', \CMesa::class . ':ListarPedidosPorCliente');
    $group->put('/mesa_cambiarestado', \CMesa::class . ':CambiarEstadoMesa');
})->add(new CheckTokenMiddleware());

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

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
require __DIR__ . '/./Controller/CEncuesta.php';
require __DIR__ . '/./Middleware/AuthMiddleware.php';
require __DIR__ . '/./Middleware/ValidadorMiddleware.php';
require __DIR__ . '/./Middleware/CheckTokenMiddleware.php';
require __DIR__ . '/./Middleware/CheckMozoMiddleware.php';
require __DIR__ . '/./Middleware/CheckSocioMiddleware.php';
require __DIR__ . '/./Middleware/CheckCocineroMiddleware.php';


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

$app->group('/empleado', function (RouteCollectorProxy $group)
{
    $group->post('/producto_alta', \CProducto::class . ':AgregarProducto');
    $group->get('/producto_listar', \CProducto::class . ':ListarProductos');
    $group->get('/producto_buscarporid', \CProducto::class . ':BuscarProductoID');
    $group->get('/producto_buscarpornombre', \CProducto::class . ':BuscarProductoNombre');

    $group->post('/pedido_alta', \CPedido::class . ':AltaPedido')->add(new CheckMozoMiddleware());
    $group->get('/pedido_listar', \CPedido::class . ':ListarPedidos')->add(new CheckMozoMiddleware());
    $group->get('/pedido_listarporsector', \CPedido::class . ':ListarPedidosPorSector');
    $group->get('/pedido_listarporcliente', \CPedido::class . ':ListarPedidosPorCliente')->add(new CheckMozoMiddleware());
    $group->put('/pedido_cambiarestado', \CPedido::class . ':CambiarEstadoPedido')->add(new CheckMozoMiddleware());

    $group->post('/mesa_abrir', \CMesa::class . ':AbrirMesa')->add(new CheckMozoMiddleware());
    $group->get('/mesa_listar', \CMesa::class . ':ListarMesas')->add(new CheckMozoMiddleware());
    $group->put('/mesa_cambiarestado', \CMesa::class . ':CambiarEstadoMesa')->add(new CheckMozoMiddleware());
    //$group->get('/mesa_listarporcliente', \CMesa::class . ':ListarPedidosPorCliente');

    $group->post('/comanda_alta', \CComanda::class . ':AltaComanda')->add(new CheckMozoMiddleware());
    $group->post('/comanda_cambiarestado', \CComanda::class . ':CambiarEstado')->add(new CheckCocineroMiddleware());

    $group->post('/subirfotos', \CPedido::class . ':SubirFoto')->add(new CheckCocineroMiddleware());
})->add(new CheckTokenMiddleware());


$app->group('/admin', function (RouteCollectorProxy $group)
{
    $group->post('/cliente_alta', \CCliente::class . ':AltaCliente');
    $group->post('/empleado_alta', \CEmpleado::class . ':IngresarEmpleado')->add(new ValidadorMiddleware());
    $group->delete('/empleado_baja', \CEmpleado::class . ':BajaEmpleado');
    $group->get('/empleado_listar', \CEmpleado::class . ':ListarEmpleados');
    $group->get('/empleado_listarporsector', \CEmpleado::class . ':ListarEmpleadosPorSector');
    $group->put('/empleado_asignarsector', \CEmpleado::class . ':AsignarSector');
    $group->post('/mesa_alta', \CMesa::class . ':AltaMesa');
    $group->put('/mesa_cerrar', \CMesa::class . ':CerrarEstadoMesa');
})
    ->add(new CheckSocioMiddleware())
    ->add(new CheckTokenMiddleware());

$app->group('/usuario', function (RouteCollectorProxy $group)
{
    $group->get('/cliente_pedido', \CPedido::class . ':TraerPedidoPorClave');
    $group->post('/cliente_encuesta', \CEncuesta::class . ':RealizarEncuesta');
});

$app->run();

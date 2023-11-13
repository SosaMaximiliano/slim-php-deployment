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


#region EMPLEADOS
$app->post('/AltaEmpleado', function (Request $request, Response $response, $args)
{
    $payload = "";
    try
    {
        if (CEmpleado::IngresarEmpleado($request))
            $payload = json_encode('Empleado agregado con exito');
    }
    catch (Exception $e)
    {
        $payload = json_encode("El empleado no pudo ser creado. {$e->getMessage()}");
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/ListarEmpleados', function (Request $request, Response $response, $args)
{
    $salida = CEmpleado::ListarEmpleados();
    $payload = json_encode($salida);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
})/*->add(AuthMiddleware::class)*/;


$app->get('/ListarEmpleados/{sector}', function (Request $request, Response $response, $args)
{
    $salida = CEmpleado::ListarSector($args['sector']);
    $payload = json_encode($salida);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});


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

#region PRODUCTOS
$app->post('/AltaProducto', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $nombre = $data['nombre'];
    $cantidad = $data['cantidad'];

    try
    {
        CProducto::AgregarProducto($nombre, $cantidad);
        $payload = json_encode(array('message' => 'Producto agregado con éxito'));
    }
    catch (Exception $e)
    {
        $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/ListarProductos', function (Request $request, Response $response)
{
    try
    {
        $productos = CProducto::ListarProductos();
        $payload = json_encode(array('productos' => $productos));
    }
    catch (Exception $e)
    {
        $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/BuscarProductoID/{id}', function (Request $request, Response $response, $args)
{
    $idProducto = $args['id'];

    try
    {
        $producto = CProducto::BuscarProductoID($idProducto);
        $payload = json_encode(array('producto' => $producto));
    }
    catch (Exception $e)
    {
        $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/BuscarProductoNombre/{nombre}', function (Request $request, Response $response, $args)
{
    $nombreProducto = $args['nombre'];

    try
    {
        $producto = CProducto::BuscarProductoNombre($nombreProducto);
        $payload = json_encode(array('producto' => $producto));
    }
    catch (Exception $e)
    {
        $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

#endregion

#region PEDIDOS
$app->post('/AltaPedido', function (Request $request, Response $response)
{
    #TIENE QUE RECIBIR UN ARRAY DE PRODUCTOS => CANTIDAD
    $data = $request->getParsedBody();
    $idProducto = $data['idProducto'];
    $cantidad = $data['cantidad'];
    $payload = "";
    try
    {
        if (CPedido::AltaPedido($idProducto, $cantidad))
            $payload = json_encode(array('message' => 'Pedido agregado con exito'));
    }
    catch (Exception $e)
    {
        $payload = json_encode(array('message' => 'No se pudo tomar el pedido ' . $e->getMessage()));
    }
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/ListarPedidos', function (Request $request, Response $response)
{
    $salida = CPedido::ListarPedidos();
    $payload = json_encode(array('message' => $salida));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/CambiarEstado/{id}/{estado}', function (Request $request, Response $response, $args)
{
    $salida = CPedido::CambiarEstadoPedido($args['id'], $args['estado']);
    $payload = json_encode(array('message' => $salida));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

#endregion

#region MESAS
$app->post('/AltaMesa', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $idCliente = $data['idCliente'];
    CMesa::AltaMesa($idCliente);
    $payload = json_encode(array('message' => 'Mesa agregada con exito'));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/ListarMesas', function (Request $request, Response $response)
{
    $salida = CMesa::ListarMesas();
    $payload = json_encode(array('message' => $salida));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/CambiarEstadoMesa', function (Request $request, Response $response)
{
    $data = $request->getParsedBody();
    $idMesa = $data['id'];
    $estado = $data['estado'];
    CMesa::CambiarEstadoMesa($idMesa, $estado);
    $payload = json_encode(array('message' => "Estado cambiado"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

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

<?php

//use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ValidadorMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        if (Empleado::ValidarDatos($parametros))
        {
            #VALIDAR QUE NO EXISTA
            if (Empleado::BuscarEmpleadoPorNombre($nombre, $apellido) == NULL)
                $response = $handler->handle($request);
            else
                throw new Exception("El empleado ya se encuentra dado de alta", 300);
        }
        else
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'Los datos no son validos'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}

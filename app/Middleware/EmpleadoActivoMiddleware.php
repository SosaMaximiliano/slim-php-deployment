<?php

//use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class EmpleadoActivoMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['ID'];

        $empleado = Empleado::BuscarEmpleadoPorID($id);
        #VALIDAR QUE EXISTA
        if ($empleado != NULL)
            if ($empleado->Estado != "Inactivo")
                $response = $handler->handle($request);
            else
                throw new Exception("No tiene permitido login", 300);


        return $response->withHeader('Content-Type', 'application/json');
    }
}

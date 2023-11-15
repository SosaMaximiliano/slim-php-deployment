<?php

//use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getQueryParams();
        $sector = $parametros['sector'];

        if ($sector === 'Mozo')
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'No sos Admin'));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}

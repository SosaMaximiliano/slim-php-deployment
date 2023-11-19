<?php
require_once './Model/AuthJWT.php';

class CAuthJWT
{
    public function CrearTokenLogin($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = Empleado::BuscarEmpleadoPorID($parametros['id']);
        if ($usuario != null)
        {
            if ($parametros['clave'] == $usuario->clave)
            {
                $datos = array(
                    'id' => $usuario->id,
                    'usuario' => $usuario->nombre,
                    'clave' => $usuario->clave,
                    'sector' => $usuario->sector
                );
                $token = AuthJWT::CrearToken($datos);
                $payload = json_encode($token);
                $response->getBody()->write($payload);
            }
            else
            {
                $response->getBody()->write(json_encode(array("mensaje" => "Error, verifique la información")));
            }
        }
        else
        {
            $response->getBody()->write(json_encode(array("mensaje" => "Error, verifique la información")));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}

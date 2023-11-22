<?php
require_once './Model/AuthJWT.php';

class CAuthJWT
{
    public function CrearTokenLogin($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = Empleado::BuscarEmpleadoPorID($parametros['ID']);
        if ($usuario != null)
        {
            if (password_verify($parametros["Clave"], $usuario->Clave))
            {
                $datos = array(
                    'ID' => $usuario->ID,
                    'Nombre' => $usuario->Nombre,
                    'Apellido' => $usuario->Apellido,
                    'Clave' => $usuario->Clave,
                    'Sector' => $usuario->Sector
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

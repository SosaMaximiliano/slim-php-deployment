<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once './Model/Encuesta.php';

class CEncuesta
{
    public static function RealizarEncuesta(Request $request, Response $response, $args)
    {
        $parametros = $request->getParsedBody();
        $pMesa = $parametros['Puntuacion_Mesa'];
        $pMozo = $parametros['Puntuacion_Mozo'];
        $pCocinero = $parametros['Puntuacion_Cocinero'];
        $pRest = $parametros['Puntuacion_Restaurant'];
        $comentarios = $parametros['Comentarios'];
        $idComanda = $parametros['ID_Comanda'];
        if (Encuesta::ValidarCampos($idComanda, $pMesa, $pRest, $pMozo, $pCocinero, $comentarios))
        {
            Encuesta::RealizarEncuesta($idComanda, $pMesa, $pRest, $pMozo, $pCocinero, $comentarios);
            $payload = json_encode("Encuesta creada correctamente");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        else
        {
            $payload = json_encode("Verifique los valores ingresados");
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
}

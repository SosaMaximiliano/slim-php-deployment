<?php


class Encuesta
{
    public static function RealizarEncuesta($idComanda, $pMesa, $pRest, $pMozo, $pCocinero, $comentarios)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "INSERT INTO Encuesta 
            (ID_Comanda,PuntuacionMesa,PuntuacionRestaurante,PuntuacionMozo,PuntuacionCocinero,Fecha,Comentarios) 
        VALUES 
        (:idComanda,:puntuacionMesa,:puntuacionRestaurante,:puntuacionMozo,:puntuacionCocinero,:fecha,:comentarios)"
        );
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':idComanda', $idComanda, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacionMesa', $pMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionRestaurante', $pRest, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionMozo', $pMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionCocinero', $pCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', date_format($fecha, 'Y-m-d'));
        $consulta->bindValue(':comentarios', $comentarios, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function TraerEncuestas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Encuesta");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function ValidarCampos($idComanda, $pMesa, $pRest, $pMozo, $pCocinero, $comentarios)
    {
        if (!Comanda::ExisteComanda($idComanda))
        {
            return false;
        }
        else
        {
            if (
                intval(($pMesa) > 10 || intval($pMesa) < 1)
                || (intval($pRest) > 10 || intval($pRest) < 1)
                || (intval($pMozo) > 10 || intval($pMozo) < 1)
                || (intval($pCocinero) > 10 || intval($pCocinero) < 1)
            )
            {
                return false;
            }
            if (strlen($comentarios) > 66)
            {
                return false;
            }

            return true;
        }
    }
}

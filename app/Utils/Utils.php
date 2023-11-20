<?php

class Utils
{
    public static function GenerarCodigo()
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = '';

        for ($i = 0; $i < 5; $i++)
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];

        return $codigo;
    }

    public static function DameUnMozo()
    {
        $empleados = Empleado::ListarPorSector("Mozo");
        #SI EL MOZO ESTA ATENDIENDO MENOS DE CINCO MESAS
        $disponibles = array_filter($empleados, function ($mozo)
        {
            return $mozo->Mesas_A_Cargo < 5;
        });

        if (count($disponibles) > 0)
        {
            $random = rand(0, (count($empleados) - 1));
            $mozo = $empleados[$random];
            return $mozo;
        }
        return NULL;
    }
}

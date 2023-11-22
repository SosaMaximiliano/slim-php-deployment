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
        $disponibles = array_filter($empleados, function ($mozo)
        {
            return $mozo->Estado != 'Baja';
        });

        if (count($disponibles) > 0)
        {
            $random = rand(0, (count($empleados) - 1));
            $mozo = $empleados[$random];
            return $mozo;
        }
        return NULL;
    }

    public static function DameUnEmpleado($sector)
    {
        $empleados = Empleado::ListarPorSector($sector);
        $disponibles = array_filter($empleados, function ($empleado)
        {
            return $empleado;
        });

        if (count($disponibles) > 0)
        {
            $random = rand(0, (count($empleados) - 1));
            $empleado = $empleados[$random];
            return $empleado;
        }
        return NULL;
    }
}

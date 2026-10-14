<?php
class Configuracion
{
    private static $datos = null;

    public static function obtener()
    {
        if (self::$datos === null) {
            self::$datos = require __DIR__ . '/../config/config.php';
        }
        return self::$datos;
    }
}

<?php
class Conexion
{
    private static $conexion = null;

    public static function obtener()
    {
        if (self::$conexion === null) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $datos = Configuracion::obtener()['base_datos'];
            self::$conexion = new mysqli(
                $datos['host'],
                $datos['usuario'],
                $datos['clave'],
                $datos['nombre'],
                (int) $datos['puerto']
            );
            self::$conexion->set_charset('utf8mb4');
        }
        return self::$conexion;
    }
}

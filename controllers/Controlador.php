<?php
class Controlador
{
    protected function renderizar($vista, $datos = [])
    {
        extract($datos);
        require __DIR__ . '/../views/' . $vista . '.php';
    }

    protected function obtenerTabla($clave)
    {
        if (!Tabla::existe($clave)) {
            http_response_code(404);
            exit('Tabla no permitida');
        }
        return new Tabla($clave);
    }

    protected function redirigirALista($clave, $tipo, $mensaje, $resaltar = null)
    {
        header('Location: index.php?accion=listar&tabla=' . urlencode($clave) . '&tipo=' . urlencode($tipo) . '&mensaje=' . urlencode($mensaje) . ($resaltar !== null ? '&resaltar=' . urlencode($resaltar) : ''));
        exit;
    }
}

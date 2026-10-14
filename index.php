<?php
spl_autoload_register(function ($clase) {
    foreach (['models', 'controllers'] as $carpeta) {
        $ruta = __DIR__ . '/' . $carpeta . '/' . $clase . '.php';
        if (is_file($ruta)) {
            require_once $ruta;
            return;
        }
    }
});

require_once __DIR__ . '/views/ayudas.php';

$accion = $_GET['accion'] ?? 'inicio';

try {
    switch ($accion) {
        case 'explorar':
            (new InicioController())->explorar();
            break;
        case 'listar':
            (new TablaController())->listar();
            break;
        case 'formulario':
            (new TablaController())->formulario();
            break;
        case 'guardar':
            (new TablaController())->guardar();
            break;
        case 'eliminar':
            (new TablaController())->eliminar();
            break;
        default:
            (new InicioController())->inicio();
    }
} catch (mysqli_sql_exception $excepcion) {
    http_response_code(500);
    echo 'No se pudo conectar o consultar la base de datos: ' . htmlspecialchars($excepcion->getMessage());
}

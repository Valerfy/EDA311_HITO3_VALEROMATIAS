<?php
class InicioController extends Controlador
{
    public function inicio()
    {
        $tablas = array_keys(Configuracion::obtener()['tablas']);
        $this->renderizar('inicio', ['titulo' => 'Inicio', 'tablas' => $tablas]);
    }

    public function explorar()
    {
        $estructuras = [];
        foreach (array_keys(Configuracion::obtener()['tablas']) as $clave) {
            $estructuras[] = new Tabla($clave);
        }
        $this->renderizar('explorar', ['titulo' => 'Estructura de la base de datos', 'estructuras' => $estructuras]);
    }
}

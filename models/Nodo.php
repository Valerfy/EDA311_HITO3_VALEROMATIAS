<?php
class Nodo
{
    public $datos;
    public $anterior = null;
    public $siguiente = null;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }
}

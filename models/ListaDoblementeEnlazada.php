<?php
require_once __DIR__ . '/Nodo.php';

class ListaDoblementeEnlazada
{
    private $cabeza = null;
    private $cola = null;
    private $tamano = 0;

    public function obtenerCabeza()
    {
        return $this->cabeza;
    }

    public function obtenerCola()
    {
        return $this->cola;
    }

    public function obtenerTamano()
    {
        return $this->tamano;
    }

    public function estaVacia()
    {
        return $this->tamano === 0;
    }

    public function insertarAlInicio($datos)
    {
        $nuevo = new Nodo($datos);
        if ($this->cabeza === null) {
            $this->cabeza = $nuevo;
            $this->cola = $nuevo;
        } else {
            $nuevo->siguiente = $this->cabeza;
            $this->cabeza->anterior = $nuevo;
            $this->cabeza = $nuevo;
        }
        $this->tamano++;
    }

    public function insertarAlFinal($datos)
    {
        $nuevo = new Nodo($datos);
        if ($this->cola === null) {
            $this->cabeza = $nuevo;
            $this->cola = $nuevo;
        } else {
            $nuevo->anterior = $this->cola;
            $this->cola->siguiente = $nuevo;
            $this->cola = $nuevo;
        }
        $this->tamano++;
    }

    public function recorrer()
    {
        $nodo = $this->cabeza;
        while ($nodo !== null) {
            yield $nodo->datos;
            $nodo = $nodo->siguiente;
        }
    }

    public function recorrerAtras()
    {
        $nodo = $this->cola;
        while ($nodo !== null) {
            yield $nodo->datos;
            $nodo = $nodo->anterior;
        }
    }

    public function ordenarBurbuja($campo, $ascendente = true)
    {
        $estadisticas = ['comparaciones' => 0, 'intercambios' => 0];
        if ($this->tamano < 2) {
            return $estadisticas;
        }

        $limite = null;
        do {
            $huboIntercambio = false;
            $actual = $this->cabeza;
            while ($actual->siguiente !== $limite) {
                $siguiente = $actual->siguiente;
                $estadisticas['comparaciones']++;
                $resultado = $this->compararValores($actual->datos[$campo], $siguiente->datos[$campo]);
                if (!$ascendente) {
                    $resultado = -$resultado;
                }
                if ($resultado > 0) {
                    $auxiliar = $actual->datos;
                    $actual->datos = $siguiente->datos;
                    $siguiente->datos = $auxiliar;
                    $huboIntercambio = true;
                    $estadisticas['intercambios']++;
                }
                $actual = $siguiente;
            }
            $limite = $actual;
        } while ($huboIntercambio && $limite !== $this->cabeza);

        return $estadisticas;
    }

    private function compararValores($a, $b)
    {
        if ($a === null && $b === null) {
            return 0;
        }
        if ($a === null) {
            return -1;
        }
        if ($b === null) {
            return 1;
        }
        if (is_numeric($a) && is_numeric($b)) {
            return $a <=> $b;
        }
        return strcasecmp((string) $a, (string) $b);
    }
}

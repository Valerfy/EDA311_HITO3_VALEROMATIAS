<?php
class Tabla
{
    private $conexion;
    private $clave;
    private $nombre;
    private $columnas = [];
    private $clavePrimaria;

    public function __construct($clave)
    {
        $this->conexion = Conexion::obtener();
        $this->clave = $clave;
        $this->nombre = Configuracion::obtener()['tablas'][$clave];

        $resultado = $this->conexion->query("SHOW COLUMNS FROM `{$this->nombre}`");
        while ($fila = $resultado->fetch_assoc()) {
            $this->columnas[] = $fila;
        }

        $this->clavePrimaria = $this->columnas[0]['Field'];
        foreach ($this->columnas as $columna) {
            if ($columna['Key'] === 'PRI') {
                $this->clavePrimaria = $columna['Field'];
                break;
            }
        }
    }

    public static function existe($clave)
    {
        return isset(Configuracion::obtener()['tablas'][$clave]);
    }

    public static function esAutoincremental($columna)
    {
        return strpos(strtolower($columna['Extra']), 'auto_increment') !== false;
    }

    public static function clasificarTipo($tipo)
    {
        $tipo = strtolower($tipo);
        if (preg_match('/^(tinyint|smallint|mediumint|int|bigint)/', $tipo)) {
            return 'entero';
        }
        if (preg_match('/^(decimal|numeric|float|double)/', $tipo)) {
            return 'decimal';
        }
        if (preg_match('/^date$/', $tipo)) {
            return 'fecha';
        }
        if (preg_match('/text/', $tipo)) {
            return 'texto_largo';
        }
        return 'texto';
    }

    public function obtenerClave()
    {
        return $this->clave;
    }

    public function obtenerNombre()
    {
        return $this->nombre;
    }

    public function obtenerColumnas()
    {
        return $this->columnas;
    }

    public function obtenerNombresColumnas()
    {
        return array_column($this->columnas, 'Field');
    }

    public function obtenerClavePrimaria()
    {
        return $this->clavePrimaria;
    }

    public function obtenerForaneas()
    {
        $sql = "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL";
        $consulta = $this->conexion->prepare($sql);
        $consulta->bind_param('s', $this->nombre);
        $consulta->execute();
        return $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function cargarLista()
    {
        $resultado = $this->conexion->query("SELECT * FROM `{$this->nombre}`");
        $lista = new ListaDoblementeEnlazada();
        while ($fila = $resultado->fetch_assoc()) {
            $lista->insertarAlFinal($fila);
        }
        return $lista;
    }

    public function obtenerPorId($id)
    {
        $consulta = $this->conexion->prepare("SELECT * FROM `{$this->nombre}` WHERE `{$this->clavePrimaria}` = ?");
        $consulta->bind_param('s', $id);
        $consulta->execute();
        return $consulta->get_result()->fetch_assoc();
    }

    public function prepararDatos($recibidos, $esEdicion)
    {
        $campos = [];
        $valores = [];

        foreach ($this->columnas as $columna) {
            $nombre = $columna['Field'];
            if (!array_key_exists($nombre, $recibidos)) {
                continue;
            }
            if ($esEdicion && $nombre === $this->clavePrimaria) {
                continue;
            }

            $valor = trim($recibidos[$nombre]);
            $error = $this->validarValor($columna, $valor);
            if ($error !== null) {
                throw new InvalidArgumentException($error);
            }

            if ($valor === '') {
                if ($columna['Null'] === 'YES') {
                    $valor = null;
                } elseif ($columna['Default'] !== null || self::esAutoincremental($columna)) {
                    continue;
                }
            }

            $campos[] = $nombre;
            $valores[] = $valor;
        }

        if (count($campos) === 0) {
            throw new InvalidArgumentException('No hay datos para guardar');
        }

        return ['campos' => $campos, 'valores' => $valores];
    }

    public function insertar($datos)
    {
        $listaCampos = implode(', ', array_map(function ($campo) {
            return "`$campo`";
        }, $datos['campos']));
        $marcadores = implode(', ', array_fill(0, count($datos['campos']), '?'));

        $consulta = $this->conexion->prepare("INSERT INTO `{$this->nombre}` ($listaCampos) VALUES ($marcadores)");
        $valores = $datos['valores'];
        $consulta->bind_param(str_repeat('s', count($valores)), ...$valores);
        $consulta->execute();
        return (int) $this->conexion->insert_id;
    }

    public function actualizar($id, $datos)
    {
        $asignaciones = implode(', ', array_map(function ($campo) {
            return "`$campo` = ?";
        }, $datos['campos']));

        $consulta = $this->conexion->prepare("UPDATE `{$this->nombre}` SET $asignaciones WHERE `{$this->clavePrimaria}` = ?");
        $valores = $datos['valores'];
        $valores[] = $id;
        $consulta->bind_param(str_repeat('s', count($valores)), ...$valores);
        $consulta->execute();
    }

    public function eliminar($id)
    {
        $consulta = $this->conexion->prepare("DELETE FROM `{$this->nombre}` WHERE `{$this->clavePrimaria}` = ?");
        $consulta->bind_param('s', $id);
        $consulta->execute();
    }

    private function validarValor($columna, $valor)
    {
        $nombre = $columna['Field'];
        $obligatorio = $columna['Null'] === 'NO' && $columna['Default'] === null && !self::esAutoincremental($columna);

        if ($valor === '') {
            return $obligatorio ? "El campo $nombre es obligatorio" : null;
        }

        if (preg_match('/^(?:var)?char\((\d+)\)/i', $columna['Type'], $coincidencia)
            && mb_strlen($valor) > (int) $coincidencia[1]) {
            return "El campo $nombre admite maximo {$coincidencia[1]} caracteres";
        }

        $tipo = self::clasificarTipo($columna['Type']);
        if ($tipo === 'entero' && filter_var($valor, FILTER_VALIDATE_INT) === false) {
            return "El campo $nombre debe ser un numero entero";
        }
        if ($tipo === 'decimal' && !is_numeric($valor)) {
            return "El campo $nombre debe ser un numero";
        }
        if ($tipo === 'fecha') {
            $fecha = DateTime::createFromFormat('Y-m-d', $valor);
            if (!$fecha || $fecha->format('Y-m-d') !== $valor) {
                return "El campo $nombre debe ser una fecha valida";
            }
        }
        return null;
    }
}

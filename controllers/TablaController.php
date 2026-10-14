<?php
class TablaController extends Controlador
{
    public function listar()
    {
        $clave = $_GET['tabla'] ?? '';
        $tabla = $this->obtenerTabla($clave);

        $campoOrden = $_GET['campo'] ?? '';
        $direccion = ($_GET['direccion'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $ordenSolicitado = in_array($campoOrden, $tabla->obtenerNombresColumnas(), true);

        $resaltar = isset($_GET['resaltar']) ? (string) $_GET['resaltar'] : null;
        $original = $tabla->cargarLista();
        $ordenada = null;
        $estadisticas = null;

        if ($ordenSolicitado) {
            $ordenada = $tabla->cargarLista();
            $estadisticas = $ordenada->ordenarBurbuja($campoOrden, $direccion === 'asc');
        }

        $this->renderizar('listar', [
            'titulo' => ucfirst($clave),
            'tabla' => $tabla,
            'original' => $original,
            'ordenada' => $ordenada,
            'estadisticas' => $estadisticas,
            'campoOrden' => $campoOrden,
            'direccion' => $direccion,
            'ordenSolicitado' => $ordenSolicitado,
            'resaltar' => $resaltar
        ]);
    }

    public function formulario()
    {
        $clave = $_GET['tabla'] ?? '';
        $tabla = $this->obtenerTabla($clave);
        $id = $_GET['id'] ?? null;
        $esEdicion = $id !== null;
        $registro = [];

        if ($esEdicion) {
            $registro = $tabla->obtenerPorId($id);
            if (!$registro) {
                $this->redirigirALista($clave, 'error', 'El registro no existe');
            }
        }

        $this->renderizar('formulario', [
            'titulo' => $esEdicion ? 'Modificar registro' : 'Nuevo registro',
            'tabla' => $tabla,
            'registro' => $registro,
            'esEdicion' => $esEdicion,
            'id' => $id
        ]);
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $clave = $_POST['tabla'] ?? '';
        $tabla = $this->obtenerTabla($clave);
        $id = $_POST['id'] ?? null;
        $esEdicion = $id !== null;

        try {
            $datos = $tabla->prepararDatos($_POST['valores'] ?? [], $esEdicion);
            if ($esEdicion) {
                $tabla->actualizar($id, $datos);
                $mensaje = 'Registro modificado correctamente (id ' . $id . ')';
                $resaltar = $id;
            } else {
                $nuevoId = $tabla->insertar($datos);
                $mensaje = 'Registro creado correctamente';
                $resaltar = $nuevoId > 0 ? (string) $nuevoId : null;
            }
            $this->redirigirALista($clave, 'exito', $mensaje, $resaltar);
        } catch (InvalidArgumentException $excepcion) {
            $this->redirigirALista($clave, 'error', $excepcion->getMessage());
        } catch (mysqli_sql_exception $excepcion) {
            $this->redirigirALista($clave, 'error', 'Error de base de datos: ' . $excepcion->getMessage());
        }
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $clave = $_POST['tabla'] ?? '';
        $tabla = $this->obtenerTabla($clave);
        $id = $_POST['id'] ?? '';

        try {
            $tabla->eliminar($id);
            $this->redirigirALista($clave, 'exito', 'Registro eliminado correctamente');
        } catch (mysqli_sql_exception $excepcion) {
            $this->redirigirALista($clave, 'error', 'No se pudo eliminar: ' . $excepcion->getMessage());
        }
    }
}

<?php require __DIR__ . '/layout/encabezado.php'; ?>
<h2><?= h(ucfirst($tabla->obtenerClave())) ?></h2>

<?php if (!empty($_GET['mensaje'])): ?>
    <p class="mensaje <?= ($_GET['tipo'] ?? '') === 'error' ? 'error' : 'exito' ?>"><?= h($_GET['mensaje']) ?></p>
<?php endif; ?>

<p><a class="boton" href="index.php?accion=formulario&tabla=<?= urlencode($tabla->obtenerClave()) ?>">Registrar nuevo</a></p>

<h3>Registros cargados en la lista doblemente enlazada</h3>
<p class="info">
    Nodos: <?= $original->obtenerTamano() ?>
    <?php if (!$original->estaVacia()): ?>
        | Cabeza: <?= h($original->obtenerCabeza()->datos[$tabla->obtenerClavePrimaria()]) ?>
        | Cola: <?= h($original->obtenerCola()->datos[$tabla->obtenerClavePrimaria()]) ?>
    <?php endif; ?>
</p>
<?php
$lista = $original;
$conAcciones = true;
require __DIR__ . '/parciales/tabla.php';
?>

<h3>Ordenamiento Bubble Sort</h3>
<form method="get" action="index.php" class="barra">
    <input type="hidden" name="accion" value="listar">
    <input type="hidden" name="tabla" value="<?= h($tabla->obtenerClave()) ?>">
    <label>Ordenar por
        <select name="campo">
            <?php foreach ($tabla->obtenerNombresColumnas() as $nombre): ?>
                <option value="<?= h($nombre) ?>" <?= $nombre === $campoOrden ? 'selected' : '' ?>><?= h($nombre) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Direccion
        <select name="direccion">
            <option value="asc" <?= $direccion === 'asc' ? 'selected' : '' ?>>Ascendente</option>
            <option value="desc" <?= $direccion === 'desc' ? 'selected' : '' ?>>Descendente</option>
        </select>
    </label>
    <button type="submit" class="boton">Ordenar</button>
</form>

<?php if ($ordenSolicitado): ?>
    <p class="info">
        Criterio: <?= h($campoOrden) ?> (<?= $direccion === 'asc' ? 'ascendente' : 'descendente' ?>)
        | Comparaciones: <?= $estadisticas['comparaciones'] ?>
        | Intercambios: <?= $estadisticas['intercambios'] ?>
    </p>
    <?php
    $lista = $ordenada;
    $conAcciones = false;
    require __DIR__ . '/parciales/tabla.php';
    ?>
<?php endif; ?>
<?php require __DIR__ . '/layout/pie.php'; ?>

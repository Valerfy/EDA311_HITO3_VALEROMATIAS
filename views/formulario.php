<?php require __DIR__ . '/layout/encabezado.php'; ?>
<h2><?= $esEdicion ? 'Modificar' : 'Registrar' ?> en <?= h($tabla->obtenerClave()) ?></h2>

<form method="post" action="index.php?accion=guardar" class="formulario">
    <input type="hidden" name="tabla" value="<?= h($tabla->obtenerClave()) ?>">
    <?php if ($esEdicion): ?>
        <input type="hidden" name="id" value="<?= h($id) ?>">
    <?php endif; ?>

    <?php foreach ($tabla->obtenerColumnas() as $columna): ?>
        <?php
        $nombre = $columna['Field'];
        $valor = $registro[$nombre] ?? '';
        $tipo = Tabla::clasificarTipo($columna['Type']);
        $obligatorio = $columna['Null'] === 'NO' && $columna['Default'] === null && !Tabla::esAutoincremental($columna);
        $esClave = $nombre === $tabla->obtenerClavePrimaria();
        if (Tabla::esAutoincremental($columna) && !$esEdicion) {
            continue;
        }
        ?>
        <label>
            <?= h($nombre) ?> <small>(<?= h($columna['Type']) ?>)</small>
            <?php if ($esClave && $esEdicion): ?>
                <input type="text" value="<?= h($valor) ?>" readonly>
            <?php elseif ($tipo === 'texto_largo'): ?>
                <textarea name="valores[<?= h($nombre) ?>]" rows="3" <?= $obligatorio ? 'required' : '' ?>><?= h($valor) ?></textarea>
            <?php elseif ($tipo === 'entero'): ?>
                <input type="number" name="valores[<?= h($nombre) ?>]" value="<?= h($valor) ?>" <?= $obligatorio ? 'required' : '' ?>>
            <?php elseif ($tipo === 'decimal'): ?>
                <input type="number" step="any" name="valores[<?= h($nombre) ?>]" value="<?= h($valor) ?>" <?= $obligatorio ? 'required' : '' ?>>
            <?php elseif ($tipo === 'fecha'): ?>
                <input type="date" name="valores[<?= h($nombre) ?>]" value="<?= h($valor) ?>" <?= $obligatorio ? 'required' : '' ?>>
            <?php else: ?>
                <input type="text" name="valores[<?= h($nombre) ?>]" value="<?= h($valor) ?>" <?= $obligatorio ? 'required' : '' ?>>
            <?php endif; ?>
        </label>
    <?php endforeach; ?>

    <div class="barra">
        <button type="submit" class="boton"><?= $esEdicion ? 'Guardar cambios' : 'Registrar' ?></button>
        <a class="boton secundario" href="index.php?accion=listar&tabla=<?= urlencode($tabla->obtenerClave()) ?>">Cancelar</a>
    </div>
</form>
<?php require __DIR__ . '/layout/pie.php'; ?>

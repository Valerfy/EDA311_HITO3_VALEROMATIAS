<div class="contenedor-tabla">
    <table>
        <thead>
            <tr>
                <?php foreach ($tabla->obtenerColumnas() as $columna): ?>
                    <th><?= h($columna['Field']) ?></th>
                <?php endforeach; ?>
                <?php if ($conAcciones): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($lista->estaVacia()): ?>
                <tr><td colspan="<?= count($tabla->obtenerColumnas()) + 1 ?>">Sin registros</td></tr>
            <?php endif; ?>
            <?php foreach ($lista->recorrer() as $datosFila): ?>
                <?php $esResaltado = $resaltar !== null && (string) $datosFila[$tabla->obtenerClavePrimaria()] === (string) $resaltar; ?>
                <tr<?= $esResaltado ? ' class="resaltado"' : '' ?>>
                    <?php foreach ($tabla->obtenerColumnas() as $columna): ?>
                        <?php $valor = $datosFila[$columna['Field']]; ?>
                        <td><?= $valor === null ? '<em>NULL</em>' : h($valor) ?></td>
                    <?php endforeach; ?>
                    <?php if ($conAcciones): ?>
                        <?php $idFila = $datosFila[$tabla->obtenerClavePrimaria()]; ?>
                        <td class="acciones">
                            <a class="boton" href="index.php?accion=formulario&tabla=<?= urlencode($tabla->obtenerClave()) ?>&id=<?= urlencode($idFila) ?>">Editar</a>
                            <form method="post" action="index.php?accion=eliminar" onsubmit="return confirm('Eliminar este registro?');">
                                <input type="hidden" name="tabla" value="<?= h($tabla->obtenerClave()) ?>">
                                <input type="hidden" name="id" value="<?= h($idFila) ?>">
                                <button type="submit" class="boton peligro">Eliminar</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

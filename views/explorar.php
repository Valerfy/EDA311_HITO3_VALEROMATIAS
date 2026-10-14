<?php require __DIR__ . '/layout/encabezado.php'; ?>
<h2>Estructura de la base de datos</h2>
<?php foreach ($estructuras as $estructura): ?>
    <h3>Tabla <?= h($estructura->obtenerNombre()) ?></h3>
    <div class="contenedor-tabla">
        <table>
            <thead>
                <tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th><th>Extra</th></tr>
            </thead>
            <tbody>
                <?php foreach ($estructura->obtenerColumnas() as $columna): ?>
                    <tr>
                        <td><?= h($columna['Field']) ?></td>
                        <td><?= h($columna['Type']) ?></td>
                        <td><?= h($columna['Null']) ?></td>
                        <td><?= h($columna['Key']) ?></td>
                        <td><?= h($columna['Default']) ?></td>
                        <td><?= h($columna['Extra']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $foraneas = $estructura->obtenerForaneas(); ?>
    <?php if ($foraneas): ?>
        <ul>
            <?php foreach ($foraneas as $foranea): ?>
                <li>
                    <?= h($foranea['COLUMN_NAME']) ?> referencia a
                    <?= h($foranea['REFERENCED_TABLE_NAME']) ?>.<?= h($foranea['REFERENCED_COLUMN_NAME']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Esta tabla no tiene claves foraneas declaradas.</p>
    <?php endif; ?>
<?php endforeach; ?>
<?php require __DIR__ . '/layout/pie.php'; ?>

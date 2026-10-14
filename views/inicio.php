<?php require __DIR__ . '/layout/encabezado.php'; ?>
<h2>Panel principal</h2>
<p>Seleccione la tabla que desea gestionar.</p>
<div class="tarjetas">
    <?php foreach ($tablas as $clave): ?>
        <a class="tarjeta" href="index.php?accion=listar&tabla=<?= urlencode($clave) ?>">
            <h3><?= h(ucfirst($clave)) ?></h3>
            <p>Registrar, consultar, modificar, eliminar y ordenar</p>
        </a>
    <?php endforeach; ?>
</div>
<?php require __DIR__ . '/layout/pie.php'; ?>

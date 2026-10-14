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
    <a class="tarjeta" href="index.php?accion=explorar">
        <h3>Estructura de la BD</h3>
        <p>Campos, claves primarias y claves foraneas</p>
    </a>
</div>
<?php require __DIR__ . '/layout/pie.php'; ?>

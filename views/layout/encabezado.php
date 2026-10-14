<?php $clavesMenu = array_keys(Configuracion::obtener()['tablas']); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($titulo) ?></title>
    <link rel="stylesheet" href="styles/estilos.css">
</head>
<body>
<header>
    <h1>Gestion de datos</h1>
    <nav>
        <a href="index.php">Inicio</a>
        <?php foreach ($clavesMenu as $claveMenu): ?>
            <a href="index.php?accion=listar&tabla=<?= urlencode($claveMenu) ?>"><?= h(ucfirst($claveMenu)) ?></a>
        <?php endforeach; ?>
        
    </nav>
</header>
<main>

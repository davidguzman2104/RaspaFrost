<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra confirmada</title>
    <link rel="stylesheet" href="CSS/estilos.css">
</head>
<body>

<h1>Compra realizada correctamente</h1>

<?php if ($pedido_id): ?>
    <p>Tu pago fue aprobado.</p>
    <p>Número de pedido: <strong><?php echo $pedido_id; ?></strong></p>
<?php else: ?>
    <p>No se encontró información del pedido.</p>
<?php endif; ?>

<a href="index.php">Volver al catálogo</a>

</body>
</html>
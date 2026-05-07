<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="css/estilos.css?v=<?php echo time(); ?>">
</head>
<body>

<h1>Carrito de Compras</h1>

<a href="index.php">Volver al catálogo</a>

<br><br>

<?php if (empty($carrito)): ?>

    <p>El carrito está vacío.</p>

<?php else: ?>

    <table border="1" cellpadding="10">
        <tr>
            <th>Imagen</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Acción</th>
        </tr>

        <?php 
        $total = 0;
        foreach ($carrito as $item): 
            $subtotal = $item['precio'] * $item['cantidad'];
            $total += $subtotal;
        ?>

        <tr>
            <td>
                <img src="<?php echo htmlspecialchars($item['imagen']); ?>" width="80">
            </td>

            <td><?php echo htmlspecialchars($item['nombre']); ?></td>

            <td>$<?php echo number_format($item['precio'], 2); ?></td>

            <td>
                <div class="cantidad-control">
                    <a href="index.php?accion=disminuir_carrito&id=<?php echo $item['id']; ?>">−</a>

                    <span><?php echo $item['cantidad']; ?></span>

                    <a href="index.php?accion=aumentar_carrito&id=<?php echo $item['id']; ?>">+</a>
                </div>
            </td>

            <td>$<?php echo number_format($subtotal, 2); ?></td>

            <td>
                <a href="index.php?accion=eliminar_carrito&id=<?php echo $item['id']; ?>">
                    Eliminar
                </a>
            </td>
        </tr>

        <?php endforeach; ?>

        <tr>
            <td colspan="4"><strong>Total</strong></td>
            <td colspan="2">
                <strong>$<?php echo number_format($total, 2); ?></strong>
            </td>
        </tr>
    </table>

    <div class="acciones-carrito">
        <a href="index.php?accion=vaciar_carrito">Vaciar carrito</a>

        <a href="index.php?accion=checkout">
            Finalizar compra
        </a>
    </div>

<?php endif; ?>

</body>
</html>
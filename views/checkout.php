<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$total = 0;
foreach ($carrito as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar compra</title>
    <link rel="stylesheet" href="css/estilos.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="checkout-contenedor">

    <h1>💳 Finalizar compra</h1>

    <a href="index.php?accion=ver_carrito" class="btn-regresar-carrito">
        ← Regresar al carrito
    </a>

    <table class="tabla-checkout" border="1" cellpadding="10">
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>

        <?php foreach ($carrito as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                <td><?php echo $item['cantidad']; ?></td>
                <td>$<?php echo number_format($item['precio'], 2); ?></td>
                <td>$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
        </tr>
    </table>

    <?php if (isset($_SESSION['error'])): ?>
        <p style="color:red;">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']); 
            ?>
        </p>
    <?php endif; ?>

    <form action="index.php?accion=procesar_pago" method="POST" class="form-checkout">

        <div class="campo-form">
            <label>Nombre completo</label>
            <input type="text" name="nombre_completo" required>
        </div>

        <div class="campo-form">
            <label>Método de pago</label>
            <select name="metodo_pago" required>
                <option value="">Seleccione</option>
                <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                <option value="Tarjeta de débito">Tarjeta de débito</option>
                <option value="PayPal simulado">PayPal simulado</option>
            </select>
        </div>

        <div class="campo-form campo-completo">
            <label>Número de tarjeta</label>
            <input type="text" name="numero_tarjeta" maxlength="16" placeholder="1234567812345678" required>
        </div>

        <div class="campo-form">
            <label>Mes de vencimiento</label>
            <select name="mes_vencimiento" required>
                <option value="">Mes</option>
                <option value="01">01</option>
                <option value="02">02</option>
                <option value="03">03</option>
                <option value="04">04</option>
                <option value="05">05</option>
                <option value="06">06</option>
                <option value="07">07</option>
                <option value="08">08</option>
                <option value="09">09</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
            </select>
        </div>

        <div class="campo-form">
            <label>Año de vencimiento</label>
            <select name="anio_vencimiento" required>
                <option value="">Año</option>
                <option value="2026">2026</option>
                <option value="2027">2027</option>
                <option value="2028">2028</option>
                <option value="2029">2029</option>
                <option value="2030">2030</option>
                <option value="2031">2031</option>
            </select>
        </div>

        <div class="campo-form campo-completo">
            <label>CVV</label>
            <input type="text" name="cvv" maxlength="3" placeholder="123" required>
        </div>

        <button type="submit" class="btn-pagar-checkout">
            Pagar ahora 💳
        </button>

    </form>

</div>

</body>
</html>
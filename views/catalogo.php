<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo</title>
    <link rel="stylesheet" href="css/estilos.css?v=<?php echo time(); ?>">
</head>
<body>

<header class="header">
    <img src="assets/tecnm.png" class="logo-izq" alt="Logo TecNM">
    <h1>RASPAFROST</h1>
    <img src="assets/itp.png" class="logo-der" alt="Logo ITP">
</header>

<div class="encabezado-catalogo">

    <img src="assets/LOGO.png" class="logo-catalogo" alt="Logo Catálogo">

    <h1>Catálogo de Productos</h1>

    <div class="menu-catalogo">

        <?php if (isset($_SESSION['usuario'])): ?>
            <p>
                Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?>
            </p>

            <div class="botones-menu-catalogo">
                <a href="index.php?accion=ver_carrito">Ver carrito</a>
                <a href="index.php?accion=logout">Cerrar sesión</a>
            </div>

        <?php else: ?>

            <div class="botones-menu-catalogo">
                <a href="index.php?accion=login">Iniciar sesión</a>
                <a href="index.php?accion=registro">Registrarse</a>
            </div>

        <?php endif; ?>

    </div>

</div>

<div class="contenedor-productos">
    <?php foreach ($productos as $producto): ?>
        <div class="producto">
            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                 alt="<?php echo htmlspecialchars($producto['nombre']); ?>">

            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>

            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>

            <p>
                <strong>Stock disponible:</strong> 
                <?php echo htmlspecialchars($producto['stock']); ?>
            </p>

            <p>$<?php echo number_format($producto['precio'], 2); ?></p>

            <?php if (isset($_SESSION['usuario'])): ?>

                <?php if ($producto['stock'] > 0): ?>
                    <a href="index.php?accion=agregar_carrito&id=<?php echo $producto['id']; ?>">
                        Agregar al carrito
                    </a>
                <?php else: ?>
                    <a href="#" onclick="return false;">
                        Sin stock
                    </a>
                <?php endif; ?>

            <?php else: ?>
                <a href="index.php?accion=login">
                    Inicia sesión para comprar
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<footer class="footer">
    <p>© 2026 Ecommerce - Todos los derechos reservados Negocios Electrónicos II - EQUIPO 4</p>
</footer>

</body>
</html>
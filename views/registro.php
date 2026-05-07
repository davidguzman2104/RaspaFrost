<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="CSS/estilos.css">
</head>
<body>

<h1>Registro de cliente</h1>

<?php if (isset($_SESSION['error'])): ?>
    <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
<?php endif; ?>

<form action="index.php?accion=guardar_usuario" method="POST">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>Correo electrónico:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Registrarme</button>
</form>

<p>
    ¿Ya tienes cuenta?
    <a href="index.php?accion=login">Inicia sesión</a>
</p>

</body>
</html>
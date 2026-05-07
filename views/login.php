<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="CSS/estilos.css">
</head>
<body>

<h1>Iniciar sesión</h1>

<?php if (isset($_SESSION['error'])): ?>
    <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
<?php endif; ?>

<?php if (isset($_SESSION['mensaje'])): ?>
    <p style="color:green;"><?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></p>
<?php endif; ?>

<form action="index.php?accion=validar_login" method="POST">
    <label>Correo electrónico:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Entrar</button>
</form>

<p>
    ¿No tienes cuenta?
    <a href="index.php?accion=registro">Regístrate aquí</a>
</p>

</body>
</html>
<?php
// Se incluye el modelo Usuario, ya que este controlador necesita utilizar
// sus métodos para registrar usuarios y buscar usuarios por correo electrónico.
require_once __DIR__ . '/../models/Usuario.php';

// Controlador encargado de gestionar la autenticación de usuarios.
// Aquí se controla el registro, inicio de sesión, cierre de sesión
// y la carga de las vistas de login y registro.
class AuthController {

    // Método que muestra la vista del formulario de inicio de sesión.
    public function mostrarLogin() {
        require __DIR__ . '/../views/login.php';
    }

    // Método que muestra la vista del formulario de registro de usuarios.
    public function mostrarRegistro() {
        require __DIR__ . '/../views/registro.php';
    }

    // Método encargado de registrar un nuevo usuario en el sistema.
    public function registrar() {
        // Se inicia la sesión para poder guardar mensajes de error o confirmación.
        session_start();

        // Se obtienen los datos enviados desde el formulario de registro.
        // Si algún campo no existe, se asigna una cadena vacía para evitar errores.
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Se valida que ningún campo obligatorio esté vacío.
        // Si falta algún dato, se guarda un mensaje de error en la sesión
        // y se redirige nuevamente al formulario de registro.
        if (empty($nombre) || empty($email) || empty($password)) {
            $_SESSION['error'] = "Todos los campos son obligatorios";
            header("Location: index.php?accion=registro");
            exit;
        }

        // Se llama al modelo Usuario para guardar el nuevo usuario en la base de datos.
        // En el modelo se debe aplicar password_hash() para almacenar la contraseña de forma segura.
        Usuario::registrar($nombre, $email, $password);

        // Si el registro fue correcto, se guarda un mensaje de confirmación.
        $_SESSION['mensaje'] = "Usuario registrado correctamente";

        // Después del registro, se redirige al usuario al formulario de inicio de sesión.
        header("Location: index.php?accion=login");
        exit;
    }

    // Método encargado de validar el inicio de sesión del usuario.
    public function login() {
        // Se inicia la sesión para poder guardar los datos del usuario autenticado.
        session_start();

        // Se obtienen el correo y la contraseña enviados desde el formulario de login.
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Se busca en la base de datos un usuario que tenga el correo ingresado.
        $usuario = Usuario::buscarPorEmail($email);

        // Se valida que el usuario exista y que la contraseña ingresada coincida
        // con la contraseña encriptada almacenada en la base de datos.
        if ($usuario && password_verify($password, $usuario['password'])) {

            // Si las credenciales son correctas, se guarda la información principal
            // del usuario en la sesión. Esto permite mantenerlo autenticado
            // mientras navega por el sistema.
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'email' => $usuario['email'],
                'rol' => $usuario['rol']
            ];

            // Se redirige al usuario a la página principal del sistema.
            header("Location: index.php");
            exit;
        }

        // Si el usuario no existe o la contraseña es incorrecta,
        // se guarda un mensaje de error en la sesión.
        $_SESSION['error'] = "Correo o contraseña incorrectos";

        // Se redirige nuevamente al formulario de inicio de sesión.
        header("Location: index.php?accion=login");
        exit;
    }

    // Método encargado de cerrar la sesión del usuario.
    public function logout() {
        // Se inicia la sesión para poder acceder a la sesión actual.
        session_start();

        // Se destruye la sesión, eliminando los datos del usuario autenticado.
        session_destroy();

        // Después de cerrar sesión, se redirige al formulario de login.
        header("Location: index.php?accion=login");
        exit;
    }
}
?>
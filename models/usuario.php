<?php
// Se incluye el archivo de conexión a la base de datos.
// Este archivo contiene la variable $conexion, utilizada para realizar consultas mediante PDO.
require_once __DIR__ . '/../config/db.php';

// Modelo Usuario.
// Esta clase se encarga de gestionar las operaciones relacionadas con los usuarios,
// como registrar nuevos usuarios y buscar usuarios por correo electrónico.
class Usuario {

    // Método estático encargado de registrar un nuevo usuario en la base de datos.
    // Recibe como parámetros el nombre, correo electrónico y contraseña del usuario.
    public static function registrar($nombre, $email, $password) {

        // Se utiliza la conexión global definida en el archivo db.php.
        global $conexion;

        // Se encripta la contraseña antes de guardarla en la base de datos.
        // PASSWORD_DEFAULT utiliza el algoritmo recomendado por PHP.
        // Esto permite proteger la contraseña y evitar almacenarla en texto plano.
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Consulta SQL para insertar un nuevo usuario.
        // Se guardan el nombre, correo electrónico y la contraseña encriptada.
        $sql = "INSERT INTO usuarios (nombre, email, password) 
                VALUES (:nombre, :email, :password)";

        // Se prepara la consulta para evitar inyecciones SQL.
        $stmt = $conexion->prepare($sql);

        // Se enlazan los valores recibidos con los parámetros de la consulta.
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $passwordHash);

        // Se ejecuta la consulta.
        // Retorna true si el usuario fue registrado correctamente,
        // o false si ocurrió algún error.
        return $stmt->execute();
    }

    // Método estático encargado de buscar un usuario por su correo electrónico.
    // Se utiliza principalmente durante el inicio de sesión.
    public static function buscarPorEmail($email) {

        // Se utiliza la conexión global a la base de datos.
        global $conexion;

        // Consulta SQL para obtener la información de un usuario activo
        // a partir de su correo electrónico.
        // La condición estado = 'activo' evita que usuarios inactivos puedan iniciar sesión.
        $sql = "SELECT * FROM usuarios 
                WHERE email = :email AND estado = 'activo'";

        // Se prepara la consulta SQL.
        $stmt = $conexion->prepare($sql);

        // Se enlaza el correo electrónico recibido con el parámetro :email.
        $stmt->bindParam(':email', $email);

        // Se ejecuta la consulta.
        $stmt->execute();

        // Se devuelve el usuario encontrado como arreglo asociativo.
        // Si no existe un usuario con ese correo o no está activo, devuelve false.
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
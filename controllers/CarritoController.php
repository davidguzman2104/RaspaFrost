<?php
// Se incluye el modelo Producto, ya que este controlador necesita consultar
// información de productos y actualizar el stock al finalizar una compra.
require_once __DIR__ . '/../models/Producto.php';

// Controlador encargado de gestionar todas las acciones relacionadas
// con el carrito de compras del sistema.
class CarritoController {

    // Método encargado de agregar un producto al carrito.
    public function agregar($id) {

        // Se verifica si la sesión aún no ha sido iniciada.
        // Si no existe una sesión activa, se inicia una nueva.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se valida que el usuario haya iniciado sesión.
        // Si no está autenticado, se redirige al formulario de login.
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?accion=login");
            exit;
        }

        // Se obtiene la información del producto desde la base de datos
        // utilizando el ID recibido como parámetro.
        $producto = Producto::obtenerPorId($id);

        // Si el producto no existe, se redirige al inicio del sistema.
        if (!$producto) {
            header("Location: index.php");
            exit;
        }

        // Si aún no existe el carrito en la sesión, se crea como un arreglo vacío.
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Si el producto ya existe dentro del carrito, únicamente se aumenta la cantidad.
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad']++;
        } else {

            // Si el producto no existe en el carrito, se agrega con sus datos principales.
            // La cantidad inicial se establece en 1.
            $_SESSION['carrito'][$id] = [
                'id' => $producto['id'],
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'imagen' => $producto['imagen'],
                'cantidad' => 1
            ];
        }

        // Después de agregar el producto, se redirige a la vista del carrito.
        header("Location: index.php?accion=ver_carrito");
        exit;
    }

    // Método encargado de mostrar el contenido actual del carrito.
    public function verCarrito() {

        // Se inicia la sesión en caso de que aún no esté activa.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se valida que el usuario esté autenticado antes de acceder al carrito.
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?accion=login");
            exit;
        }

        // Se obtiene el carrito almacenado en la sesión.
        // Si no existe, se asigna un arreglo vacío para evitar errores.
        $carrito = $_SESSION['carrito'] ?? [];

        // Se carga la vista encargada de mostrar los productos del carrito.
        require __DIR__ . '/../views/carrito.php';
    }

    // Método encargado de aumentar la cantidad de un producto dentro del carrito.
    public function aumentar($id) {

        // Se inicia la sesión si aún no está activa.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se valida que el usuario haya iniciado sesión.
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?accion=login");
            exit;
        }

        // Se verifica que el ID recibido no sea nulo y que el producto exista en el carrito.
        // Si existe, se incrementa su cantidad en una unidad.
        if ($id !== null && isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad']++;
        }

        // Se redirige nuevamente a la vista del carrito.
        header("Location: index.php?accion=ver_carrito");
        exit;
    }

    // Método encargado de disminuir la cantidad de un producto en el carrito.
    public function disminuir($id) {

        // Se inicia la sesión si no existe una sesión activa.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se valida que el usuario esté autenticado.
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?accion=login");
            exit;
        }

        // Se verifica que el producto exista en el carrito antes de modificar su cantidad.
        if ($id !== null && isset($_SESSION['carrito'][$id])) {

            // Se disminuye la cantidad del producto en una unidad.
            $_SESSION['carrito'][$id]['cantidad']--;

            // Si la cantidad llega a cero o menos, el producto se elimina del carrito.
            if ($_SESSION['carrito'][$id]['cantidad'] <= 0) {
                unset($_SESSION['carrito'][$id]);
            }
        }

        // Se redirige nuevamente a la vista del carrito.
        header("Location: index.php?accion=ver_carrito");
        exit;
    }

    // Método encargado de eliminar completamente un producto del carrito.
    public function eliminar($id) {

        // Se inicia la sesión en caso de ser necesario.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se valida que el usuario haya iniciado sesión.
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?accion=login");
            exit;
        }

        // Si el producto existe dentro del carrito, se elimina usando unset().
        if (isset($_SESSION['carrito'][$id])) {
            unset($_SESSION['carrito'][$id]);
        }

        // Se redirige a la vista del carrito después de eliminar el producto.
        header("Location: index.php?accion=ver_carrito");
        exit;
    }

    // Método encargado de vaciar completamente el carrito de compras.
    public function vaciar() {

        // Se inicia la sesión si aún no está activa.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se valida que el usuario esté autenticado.
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?accion=login");
            exit;
        }

        // Se elimina todo el carrito almacenado en la sesión.
        unset($_SESSION['carrito']);

        // Se redirige nuevamente a la vista del carrito.
        header("Location: index.php?accion=ver_carrito");
        exit;
    }

    // Método encargado de finalizar la compra.
    public function finalizarCompra() {

        // Se inicia la sesión si aún no existe una sesión activa.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se valida que el usuario haya iniciado sesión.
        // Esto protege la compra para que solo usuarios autenticados puedan finalizarla.
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?accion=login");
            exit;
        }

        // Se verifica que el carrito no esté vacío.
        // Si no hay productos agregados, se redirige nuevamente al carrito.
        if (empty($_SESSION['carrito'])) {
            header("Location: index.php?accion=ver_carrito");
            exit;
        }

        // Se recorre cada producto almacenado en el carrito.
        // Por cada producto comprado, se actualiza el stock en la base de datos.
        foreach ($_SESSION['carrito'] as $item) {
            Producto::actualizarStock($item['id'], $item['cantidad']);
        }

        // Una vez finalizada la compra, se elimina el carrito de la sesión.
        unset($_SESSION['carrito']);

        // Finalmente, se redirige al catálogo de productos.
        header("Location: index.php?accion=catalogo");
        exit;
    }
}
?>
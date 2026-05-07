<?php
// Se incluye el modelo Pedido, ya que este controlador necesita crear
// pedidos cuando el usuario finaliza el proceso de compra.
require_once __DIR__ . '/../models/Pedido.php';

// Controlador encargado de gestionar el proceso de checkout,
// validación de pago simulado y confirmación del pedido.
class CheckoutController {

    // Método encargado de mostrar la vista de checkout.
    public function mostrarCheckout() {

        // Se inicia la sesión para poder acceder a los datos del usuario
        // y al carrito de compras almacenado en $_SESSION.
        session_start();

        // Se valida que el usuario haya iniciado sesión.
        // Si no está autenticado, se redirige al formulario de login.
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?accion=login");
            exit;
        }

        // Se verifica que el carrito no esté vacío.
        // Si el usuario no tiene productos en el carrito,
        // se redirige nuevamente a la vista del carrito.
        if (empty($_SESSION['carrito'])) {
            header("Location: index.php?accion=ver_carrito");
            exit;
        }

        // Se obtiene el carrito almacenado en la sesión
        // para enviarlo a la vista de checkout.
        $carrito = $_SESSION['carrito'];

        // Se carga la vista donde el usuario revisa su compra
        // e ingresa los datos del método de pago.
        require __DIR__ . '/../views/checkout.php';
    }

    // Método encargado de procesar el pago del pedido.
    public function procesarPago() {

        // Se inicia la sesión para acceder al usuario autenticado,
        // al carrito y para guardar mensajes de error o confirmación.
        session_start();

        // Se valida que el usuario haya iniciado sesión antes de pagar.
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?accion=login");
            exit;
        }

        // Se valida que exista un carrito con productos.
        // No se permite procesar un pago si el carrito está vacío.
        if (empty($_SESSION['carrito'])) {
            header("Location: index.php?accion=ver_carrito");
            exit;
        }

        // Se obtienen los datos enviados desde el formulario de pago.
        // Si algún dato no existe, se asigna una cadena vacía para evitar errores.
        $metodo_pago = $_POST['metodo_pago'] ?? '';
        $numero_tarjeta = $_POST['numero_tarjeta'] ?? '';
        $cvv = $_POST['cvv'] ?? '';

        // Se valida que todos los datos de pago hayan sido capturados.
        // Si falta algún campo, se guarda un mensaje de error en la sesión
        // y se redirige nuevamente al formulario de checkout.
        if (empty($metodo_pago) || empty($numero_tarjeta) || empty($cvv)) {
            $_SESSION['error'] = "Todos los datos de pago son obligatorios";
            header("Location: index.php?accion=checkout");
            exit;
        }

        /*
            Simulación de pago:
            En este proyecto, el pago se aprueba únicamente si:
            - El número de tarjeta tiene 16 dígitos.
            - El CVV tiene 3 dígitos.

            Esta validación funciona como una simulación básica,
            ya que no se conecta con una pasarela de pago real.
        */

        // Se valida la longitud del número de tarjeta y del CVV.
        if (strlen($numero_tarjeta) == 16 && strlen($cvv) == 3) {

            // Si la validación del pago es correcta, se crea el pedido.
            // Se envía al modelo Pedido el ID del usuario, el carrito completo
            // y el método de pago seleccionado.
            $pedido_id = Pedido::crearPedido(
                $_SESSION['usuario']['id'],
                $_SESSION['carrito'],
                $metodo_pago
            );

            // Si el pedido fue creado correctamente, se elimina el carrito,
            // se guarda el ID del pedido en sesión y se redirige a la confirmación.
            if ($pedido_id) {
                unset($_SESSION['carrito']);
                $_SESSION['pedido_id'] = $pedido_id;

                header("Location: index.php?accion=confirmacion");
                exit;
            }
        }

        // Si el pago no cumple con las condiciones establecidas
        // o el pedido no pudo crearse, se muestra un mensaje de rechazo.
        $_SESSION['error'] = "Pago rechazado. Verifique los datos.";

        // Se redirige nuevamente al checkout para que el usuario revise los datos.
        header("Location: index.php?accion=checkout");
        exit;
    }

    // Método encargado de mostrar la pantalla de confirmación del pedido.
    public function confirmacion() {

        // Se inicia la sesión para obtener el ID del pedido generado.
        session_start();

        // Se obtiene el ID del pedido almacenado en sesión.
        // Si no existe, se asigna null para evitar errores.
        $pedido_id = $_SESSION['pedido_id'] ?? null;

        // Se carga la vista de confirmación, donde se puede mostrar
        // el resultado de la compra y el número de pedido.
        require __DIR__ . '/../views/confirmacion.php';
    }
}
?>
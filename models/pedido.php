<?php
// Se incluye el archivo de conexión a la base de datos.
// Este archivo contiene la variable $conexion, que permite ejecutar consultas mediante PDO.
require_once __DIR__ . '/../config/db.php';

// Modelo encargado de gestionar la información relacionada con los pedidos.
class Pedido {

    // Método estático encargado de crear un nuevo pedido en la base de datos.
    // Recibe el ID del usuario, los productos del carrito y el método de pago seleccionado.
    public static function crearPedido($usuario_id, $carrito, $metodo_pago) {

        // Se utiliza la conexión global definida en el archivo db.php.
        global $conexion;

        try {
            // Se inicia una transacción para asegurar que todas las operaciones
            // del pedido se realicen correctamente.
            // Si ocurre un error, se podrán revertir los cambios.
            $conexion->beginTransaction();

            // Variable utilizada para calcular el total general del pedido.
            $total = 0;

            // Se recorre el carrito para calcular el total de la compra.
            // Cada subtotal se obtiene multiplicando el precio del producto
            // por la cantidad seleccionada.
            foreach ($carrito as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }

            // Consulta SQL para registrar el pedido principal.
            // Se guarda el usuario que realizó la compra, el total,
            // el estado del pedido y el método de pago.
            $sql = "INSERT INTO pedidos (usuario_id, total, estado, metodo_pago)
                    VALUES (:usuario_id, :total, 'pagado', :metodo_pago)";

            // Se prepara la consulta para evitar inyecciones SQL.
            $stmt = $conexion->prepare($sql);

            // Se enlazan los valores recibidos con los parámetros de la consulta.
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':metodo_pago', $metodo_pago);

            // Se ejecuta la consulta para guardar el pedido.
            $stmt->execute();

            // Se obtiene el ID del pedido recién creado.
            // Este ID se usará para registrar los productos en detalle_pedido.
            $pedido_id = $conexion->lastInsertId();

            // Se recorre nuevamente el carrito para guardar cada producto
            // como parte del detalle del pedido.
            foreach ($carrito as $item) {

                // Se calcula el subtotal individual de cada producto.
                $subtotal = $item['precio'] * $item['cantidad'];

                // Consulta SQL para registrar el detalle del pedido.
                // Aquí se guarda qué productos pertenecen al pedido,
                // su precio, cantidad y subtotal.
                $sqlDetalle = "INSERT INTO detalle_pedido 
                (pedido_id, producto_id, nombre_producto, precio, cantidad, subtotal)
                VALUES 
                (:pedido_id, :producto_id, :nombre_producto, :precio, :cantidad, :subtotal)";

                // Se prepara la consulta del detalle del pedido.
                $stmtDetalle = $conexion->prepare($sqlDetalle);

                // Se enlazan los datos del pedido y del producto.
                $stmtDetalle->bindParam(':pedido_id', $pedido_id);
                $stmtDetalle->bindParam(':producto_id', $item['id']);
                $stmtDetalle->bindParam(':nombre_producto', $item['nombre']);
                $stmtDetalle->bindParam(':precio', $item['precio']);
                $stmtDetalle->bindParam(':cantidad', $item['cantidad']);
                $stmtDetalle->bindParam(':subtotal', $subtotal);

                // Se ejecuta la inserción del detalle del pedido.
                $stmtDetalle->execute();

                // Consulta SQL para actualizar el stock del producto comprado.
                // Se descuenta del stock la cantidad adquirida por el usuario.
                $sqlStock = "UPDATE productos 
                             SET stock = stock - :cantidad 
                             WHERE id = :producto_id";

                // Se prepara la consulta de actualización de stock.
                $stmtStock = $conexion->prepare($sqlStock);

                // Se enlazan la cantidad comprada y el ID del producto.
                $stmtStock->bindParam(':cantidad', $item['cantidad']);
                $stmtStock->bindParam(':producto_id', $item['id']);

                // Se ejecuta la actualización del stock.
                $stmtStock->execute();
            }

            // Si todas las consultas se realizaron correctamente,
            // se confirma la transacción en la base de datos.
            $conexion->commit();

            // Se retorna el ID del pedido creado.
            return $pedido_id;

        } catch (Exception $e) {

            // Si ocurre cualquier error durante el proceso,
            // se revierten todos los cambios realizados en la transacción.
            $conexion->rollBack();

            // Se retorna false para indicar que el pedido no pudo crearse.
            return false;
        }
    }
}
?>
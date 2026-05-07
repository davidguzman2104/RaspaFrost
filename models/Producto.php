<?php
// Se incluye el archivo de conexión a la base de datos.
// Este archivo contiene la variable $conexion, la cual permite realizar consultas mediante PDO.
require_once __DIR__ . '/../config/db.php';

// Modelo Producto.
// Esta clase se encarga de gestionar las operaciones relacionadas con los productos
// dentro de la base de datos, como consultar productos activos,
// obtener un producto específico y actualizar su stock.
class Producto {

    // Método estático encargado de obtener todos los productos activos.
    public static function obtenerTodos() {

        // Se utiliza la conexión global definida en el archivo db.php.
        global $conexion;

        // Consulta SQL para seleccionar únicamente los productos que están activos.
        // Esto evita mostrar productos inactivos dentro del catálogo.
        $sql = "SELECT * FROM productos WHERE estado = 'activo'";

        // Se prepara la consulta para ejecutarla de forma segura.
        $stmt = $conexion->prepare($sql);

        // Se ejecuta la consulta.
        $stmt->execute();

        // Se devuelven todos los registros encontrados en forma de arreglo asociativo.
        // Cada producto será representado con sus campos, como id, nombre, precio,
        // stock, imagen, categoría y estado.
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método estático encargado de obtener un producto específico por su ID.
    public static function obtenerPorId($id) {

        // Se utiliza la conexión global a la base de datos.
        global $conexion;

        // Consulta SQL para buscar un producto por su ID.
        // También se valida que el producto esté activo para evitar agregar
        // al carrito productos inactivos o no disponibles.
        $sql = "SELECT * FROM productos WHERE id = :id AND estado = 'activo'";

        // Se prepara la consulta SQL.
        $stmt = $conexion->prepare($sql);

        // Se enlaza el parámetro :id con el valor recibido.
        // PDO::PARAM_INT indica que el valor debe tratarse como número entero.
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Se ejecuta la consulta.
        $stmt->execute();

        // Se devuelve un solo producto en formato de arreglo asociativo.
        // Si no existe un producto con ese ID, el resultado será false.
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método estático encargado de actualizar el stock de un producto.
    // Se utiliza cuando el usuario finaliza una compra y se necesita descontar
    // del inventario la cantidad comprada.
    public static function actualizarStock($id, $cantidad) {

        // Se utiliza la conexión global a la base de datos.
        global $conexion;

        // Consulta SQL para descontar la cantidad comprada del stock actual.
        // La condición "stock >= :cantidad" evita que el stock quede en números negativos.
        $sql = "UPDATE productos 
                SET stock = stock - :cantidad 
                WHERE id = :id AND stock >= :cantidad";

        // Se prepara la consulta para ejecutarla de forma segura.
        $stmt = $conexion->prepare($sql);

        // Se enlaza la cantidad que se va a descontar del stock.
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);

        // Se enlaza el ID del producto que será actualizado.
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Se ejecuta la actualización del stock.
        $stmt->execute();

        // rowCount() devuelve el número de filas afectadas.
        // Si es mayor a 0, significa que el stock se actualizó correctamente.
        // Si es 0, puede significar que el producto no existe o que no había stock suficiente.
        return $stmt->rowCount() > 0;
    }
}
?>
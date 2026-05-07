<?php
// Se incluye el modelo Producto, ya que este controlador necesita
// obtener la información de los productos desde la base de datos.
require_once __DIR__ . '/../models/Producto.php';

// Controlador encargado de gestionar la visualización del catálogo de productos.
class ProductoController {

    // Método encargado de mostrar el catálogo de productos.
    public function mostrarCatalogo() {

        // Se obtiene la lista completa de productos desde el modelo Producto.
        // El método obtenerTodos() consulta la base de datos y devuelve
        // los productos que serán mostrados en la vista del catálogo.
        $productos = Producto::obtenerTodos();

        // Se carga la vista catalogo.php.
        // Esta vista recibe la variable $productos y se encarga de
        // recorrerla para mostrar los productos dinámicamente en pantalla.
        require __DIR__ . '/../views/catalogo.php';
    }
}
?>
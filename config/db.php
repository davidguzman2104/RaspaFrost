<?php
$conexion = new PDO("mysql:host=localhost;dbname=ecommerce", "root", "1234");
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
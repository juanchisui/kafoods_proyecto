<?php

//importar la conexion
require "includes/config/database.php";
$db= conectarBD();


//email y usuario
$usuario = 'admin';
$clave = "123456";

$claveHash= password_hash($clave, PASSWORD_DEFAULT);

//quey para crear el usuario
$query ="INSERT INTO usuarios(usuario,clave) VALUES ('{$usuario}','{$claveHash}');" ;
echo $query;


//agregar a la base datos;
mysqli_query($db,$query);
<?php

//importar la conexion
require "includes/config/database.php";
$db= conectarBD();


//email y usuario
$email = 'correo@correo.com';
$clave = "123456";

$claveHash= password_hash($clave, PASSWORD_DEFAULT);

//quey para crear el usuario
$query ="INSERT INTO usuarios(email,clave) VALUES ('{$email}','{$claveHash}');" ;
echo $query;


//agregar a la base datos;
mysqli_query($db,$query);
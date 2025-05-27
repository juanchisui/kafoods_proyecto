<?php

function conectarBD(): mysqli{
    $db = mysqli_connect('localhost','root','root','kfoods');

    if(!$db){
        echo 'No se puedo conecta la base de datos';
        exit;
    }

    return $db;
} 

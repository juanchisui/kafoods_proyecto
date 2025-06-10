<?php
    //importar la conexion
    require "includes/config/database.php";
    $db= conectarBD();

    $errores = [];

    //autenticar usuario
    if($_SERVER['REQUEST_METHOD'] === 'POST' ){
      
        $usuario = mysqli_real_escape_string($db,$_POST['usuario'] );
        $clave = mysqli_real_escape_string($db,$_POST['clave']) ;

        if(!$usuario){
            $errores[]= 'usuario es obligatorio';
        }

         if(!$clave){
            $errores[]= 'la clave es obligatoria';
        }

        if(empty($errores)){
            //revisar si usuario existe
            $query = "SELECT * FROM usuarios WHERE usuario='{$usuario}'";
            $resultado= mysqli_query($db,$query);

            

            if($resultado -> num_rows){
                //verificar la clave
                $usuario = mysqli_fetch_assoc($resultado);

                
                $auth = password_verify($clave,$usuario['clave']);


                if($auth){
                    //vericiar su la clave correcta
                    session_start();

                    //llenar arreglo de la sesion
                    $_SESSION['usuario'] = $usuario['usuario'];
                    $_SESSION['index'] = true;

                    header('Location: /admin');

                
                }else{
                    $errores[] = 'la clave es incorrecta';
                }

            }else{
                //verificar si el usuario existe
                $errores[]='El usuario no existe';
            }
        }



    }

       


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&    display=swap" rel="stylesheet">
    <link rel="stylesheet" href="normalize.css">
    <link rel="stylesheet" href="style.css">
    <title>K-foods</title>
</head>
<body>
    <section class="main ">
        <div class="main-contenedor contenedor">


            <div class="main-imagen">          
            </div>


            <div class="main-login">
                <img class="main-logo" src="img/Logo.svg" alt="">
                <?php foreach($errores as $error):?>
                    <div class="alerta error">
                        <?php echo $error; ?>
                    </div>

                <?php endforeach;?>

                <form action="" method="POST">
                    <div class="campo">
                        <label class="campo__label" for="text">Usuario</label>
                        <input class="campo__input" type="text" name="usuario" placeholder="Ingresa usuario" id="text" require>
                    </div>


                    <div class="campo">
                        <label class="campo__label" for="clave">Clave</label>
                        <input class="campo__input" type="password" name="clave" placeholder="Ingresa la clave" id="clave" require>
                    </div>


                    <div class="campo">
                        <input class="boton" type="submit"  id="" value="Iniciar sesion">
                    </div>
                </form>
            </div>


        </div>
       
    </section>
</body>
</html>

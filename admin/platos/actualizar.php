<?php
    //valida que en el navegador no sobrreescriban el id
    $id = $_GET['id'];
    $id = filter_var($id,FILTER_VALIDATE_INT);

    if(!$id){
        header('location: /admin');
    }

    
    //Conexion base de datos
    require "../../includes/config/database.php";
    $db= conectarBD();

    //arreglo para mensajes con errores
    $errores = [];

    //consulta para obtener los datos de los platos
    $consulta = "SELECT * FROM plato WHERE idplato= {$id}";
    $resultado = mysqli_query($db, $consulta);
    $plato = mysqli_fetch_assoc($resultado);



    
    $nombre = $plato['nombre'];
    $descripcion = $plato['descripcion'];
    $precio =  $plato['precio'];

    if($_SERVER['REQUEST_METHOD'] === 'POST'){ //AQUI VALIDAMOS LOS ERRORES E INSERTAMOS EN LA BASE DE DATOS
        

        $nombre = mysqli_real_escape_string($db, $_POST['nombre']);
        $descripcion = mysqli_real_escape_string($db, $_POST['descripcion']);
        $precio = mysqli_real_escape_string($db, $_POST['precio']);

        if(!$nombre){
            $errores[] = 'Nombre es obligatorio';
        }

        if(strlen(trim($descripcion)) === 0) {
        $errores[] = 'Descripcion es obligatorio';
        }

        if(!$precio){
            $errores[] = 'Precio es obligatorio';
        }

        //revisar que el arreglo de erroes este vacio para insertar los datos en la base de datos
        if(empty($errores)){
            //insertar en la base de datos

            $query = "UPDATE plato SET nombre = '$nombre',descripcion = '$descripcion', precio=$precio WHERE idplato = $id";

            //echo $query;

            $resultado = mysqli_query($db, $query);

            if($resultado){
                header('Location: /admin?resultado=2');
            }
        }


    }

    require '../../includes/funciones.php';   
    incluirTemplate ('header');   
?>

<main>
    <div class="contenedor">
        <h1 class="titulo-main">Actualizar Plato</h1>

        <a href="/admin" class='boton boton-plato'>volver</a>
    </div>

    <!--vamos a iterar para que cuando haya un error se muestre en pantalla-->


    <?php foreach($errores as $error):?>
        <div class="alerta error centrar">
            <?php echo $error ?>
        </div>
        
    <?php endforeach;?>

    <form  class="contenedor formulario" method="POST" enctype="multipart/form-data">
        <legend>Datos del plato</legend>

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" placaholder="nombre del plato" class="campo__input" value=<?php echo $nombre; ?>>

        <label for="descripcion">Descripcion:</label>
        <textarea  id="descripcion" name="descripcion" class="campo__input textarea"><?php echo $descripcion; ?> </textarea>

        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" class="campo__input" value=<?php echo $precio; ?>>

        <legend>Mesero</legend>

        <select name="mesero" id="" class="campo__input">
            <option value="1">juan riveros</option>
            <option value="2">Karen quintero</option>
        </select>

        <input type='submit' value="Actualizar plato" class="boton crear-plato">

    </form>


</main>

<?php
   
incluirTemplate ('footer');
?>

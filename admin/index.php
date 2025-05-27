<?php
//VAMOS A MOSTAR LOS PLATOS INGRESADOS EN LA BASE DATOS EN EL MENU
//COMIENZA AQUI CONSULTANDO LA BASE DE DATOS

//importa la base datos
 require "../includes/config/database.php";
$db= conectarBD();

//escribir el query
$query = "SELECT * FROM plato";

//consultar la base datos
$resultadoConsulta = mysqli_query($db, $query);

//TERMINAR AQUI 


//llamamos la alerta cuando agregamos un plato
$resultado = $_GET['resultado'] ?? null; 


//eliminar plato
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = $_POST['idplato'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if($id){
        $query = "DELETE FROM plato WHERE idplato = $id";

        $resultado = mysqli_query($db, $query);

        if($resultado){
            header('location: /admin');
        }
    }
}


//llama el template del header
require '../includes/funciones.php';   
incluirTemplate ('header');

?>


<main class="Main contenedor">
    <div class="contenedor">
        <h1 class="titulo-main">Menu kfoods</h1>
        <?php if(intval($resultado) ===1): ?>
            <p class="alerta exito" id="alerta-exito">Plato Agregado correctamente</p>
        <?php elseif(intval($resultado) ===2): ?>
            <p class="alerta exito" id="alerta-exito">Plato Actualizado Correctamente</p>
        <?php endif; ?>

        <a href="/admin/platos/crear.php" class='boton boton-plato'>Agregar plato</a>
        <a href="/admin/ordenes/index.php" class='boton boton-plato'>Ordenes</a>

    </div>

    <table class="menu contenedor">
        <thead>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripcion</th>
            <th>Precio</th>
            <th>Acciones</th>
        </thead>

        <tbody><!--mostas los platos desde la base datos-->
            <?php while($plato = mysqli_fetch_assoc($resultadoConsulta)): ?>
            <tr>
                <td><?php echo $plato['idplato'] ?></td>
                <td><?php echo $plato['nombre'] ?></td>
                <td><?php echo $plato['descripcion'] ?></td>
                <td>$ <?php echo $plato['precio'] ?></td>
                <td class="boton-accion">


                    <a href="/admin/platos/actualizar.php?id=<?php echo $plato['idplato']; ?>" class="boton boton-actualizar" >Actualizar</a>
                    
                    <form action="" method="POST">

                            <input type="hidden" name="idplato" value='<?php echo $plato['idplato'];?>'>

                            <input type="submit" class="boton boton-eliminar" value="Eliminar">
                    </form>
                    
                    
                    
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>

    </table>
    <script src="/admin/js/app.js"></script>
</main>








<?php
   
incluirTemplate ('footer');
?>

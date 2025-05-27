<?php
//VAMOS A MOSTAR LOS PLATOS INGRESADOS EN LA BASE DATOS EN EL MENU
//COMIENZA AQUI CONSULTANDO LA BASE DE DATOS

//importa la base datos
require __DIR__ . '/../../includes/config/database.php';
$db= conectarBD();


// consulta para obtener las órdenes con información relacionada
$query = "SELECT 
            o.idorden,
            o.total,
            o.fecha,
            CONCAT(m.nombre, ' ', m.apellido) AS mesero,
            COUNT(d.iddetalle_orden) AS total_platos
          FROM orden o
          JOIN mesero m ON o.mesero_idmesero = m.idmesero
          LEFT JOIN detalle_orden d ON o.idorden = d.orden_idorden
          GROUP BY o.idorden
          ORDER BY o.fecha DESC";

$resultadoOrden = mysqli_query($db, $query);

//llamamos la alerta cuando agregamos un plato
$resultado = $_GET['resultado'] ?? null; 

//eliminar orden

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idorden'])) {
    $id = filter_var($_POST['idorden'], FILTER_VALIDATE_INT);
    
    if($id) {
        // Primero eliminar los detalles relacionados
        $queryDetalles = "DELETE FROM detalle_orden WHERE orden_idorden = $id";
        mysqli_query($db, $queryDetalles);
        
        // Luego eliminar la orden principal
        $queryOrden = "DELETE FROM orden WHERE idorden = $id";
        $resultado = mysqli_query($db, $queryOrden);
        
        if($resultado) {
            header('Location: /admin/ordenes'); 
            
        }
    }
}

//llama el template del header
require __DIR__ . '/../../includes/funciones.php';
incluirTemplate ('header');

?>


<main class="Main contenedor">
    <div class="contenedor">
        <h1 class="titulo-main">Ordenes kfoods</h1>
        

        <?php if(intval($resultado) ===1): ?>
            <p class="alerta exito" id="alerta-exito">Orden Agregada correctamente</p>
        <?php endif; ?>

        <a href="/admin/ordenes/crear.php" class='boton boton-plato'>Agregar orden</a>
        <a href="/admin/admin/" class='boton boton-plato'>Menu</a>

    </div>


    <table class="menu contenedor ">
        <thead>
        <tr class="tabla-tr">
            <th>ID</th>
            <th>Fecha</th>
            <th>Mesero</th>
            <th>Total Platos</th>
            <th>Monto Total</th>
            <th>Acciones</th>
        </tr>
        
        <tbody class="tbody">
            <?php while($orden = mysqli_fetch_assoc($resultadoOrden)): ?>
            <tr>
                <td class="tobdy-form"><?php echo $orden['idorden']; ?></td>
                <td class="tobdy-form"><?php echo date('d/m/Y H:i', strtotime($orden['fecha'])); ?></td>
                <td class="tobdy-form"><?php echo $orden['mesero']; ?></td>
                <td class="tobdy-form"><?php echo $orden['total_platos']; ?></td>
                <td class="tobdy-form">$<?php echo number_format($orden['total']); ?></td>
                <td class="tobdy-form">
                    <a href="/admin/ordenes/ver.php?id=<?php echo $orden['idorden']; ?>" class="boton boton-ver" >Ver</a>
                    
                    <form action="" method="POST">

                            <input type="hidden" name="idorden" value='<?php echo $orden['idorden'];?>'>

                            <input type="submit" class="boton boton-eliminar" value="Eliminar">
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>

    </thead>
    </table>

    
    <script src="/admin/js/app.js"></script>
</main>








<?php
   
incluirTemplate ('footer');
?>

<?php
    //Conexion base de datos
    require "../../includes/config/database.php";
    $db= conectarBD();

   //obtener el id cuando haamo click en ver
   $id_orden =$_GET['id'] ?? null;
   $id_orden = filter_var($id_orden, FILTER_VALIDATE_INT);

   //si no hay id lo devuelce
   if(!$id_orden){
        header('location: /admin/ordenes');
   }

   //consulta para los datos de la orden
   $query_orden = "SELECT o.*, CONCAT(m.nombre, ' ', m.apellido) AS mesero FROM orden o JOIN mesero m ON o.mesero_idmesero = m.idmesero WHERE o.idorden = $id_orden";

   $resultado_orden= mysqli_query($db,$query_orden);
   $orden = mysqli_fetch_assoc($resultado_orden);

   //consula para traer los platos
    $query_platos = "SELECT p.nombre, p.descripcion, p.precio, d.cantidad, (d.cantidad * p.precio) AS subtotal FROM detalle_orden d JOIN plato p ON d.plato_idplato = p.idplato WHERE d.orden_idorden = $id_orden";
    $resultado_platos = mysqli_query($db, $query_platos);


    require '../../includes/funciones.php';   
    incluirTemplate ('header');   
?>

<main>
    <div class="contenedor">
        <h1 class="titulo-main">Detalle pedido</h1>



        <h1 titulo-pedido>Orden #<?php echo $orden['idorden'] ?> </h1>

        <div class="detalle">
            <div class="campo-detalle">
                <span class="campo__label">Fecha</span>
                <span class="campo__valor"><?php echo date('d/m/y',strtotime($orden['fecha'])) ?></span>
            </div>

            <div class="campo-detalle">
                <span class="campo__label">Mesero</span>
                <span class="campo__valor"><?php echo $orden['mesero']?></span>
            </div>

            <div class="campo-detalle">
                <span class="campo__label">Total</span>
                <span class="campo__valor"><?php echo number_format($orden['total'],2)?></span>
            </div>
        </div>


        <!--platos lista-->

        <table class="tabla-orden">
            <thead>
                <tr>
                    <th>Plato</th>
                    <th>Descripcio</th>
                    <th>Precio Unitario</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
                <?php while($plato = mysqli_fetch_assoc($resultado_platos)): ?>
                <tr>
                    <td><?php echo $plato['nombre'] ?></td>
                    <td><?php echo $plato['descripcion'] ?></td>
                    <td>$<?php echo number_format($plato['precio'], ); ?></td>
                    <td><?php echo $plato['cantidad']; ?></td>
                    <td>$<?php echo number_format($plato['subtotal'], ); ?></td>
                </tr>
                <?php endwhile ?>
            </tbody>

            <tfoot>
                <tr>
                    <td>
                       <td class="text-right">Total:</td>
                        <td>$<?php echo number_format($orden['total'], 2); ?></td> 
                    </td>
                </tr>
            </tfoot>
        </table>

        <a href="/admin/ordenes" class='boton boton-plato obton-orden'>Ordenes</a>
        <a href="/admin/ordenes/generar_recibo.php?id=<?php echo $orden['idorden']; ?>" target="_blank" class="boton obton-orden">Generar Recibo </a>
    </div>







</main>









<?php
   
incluirTemplate ('footer');
?>

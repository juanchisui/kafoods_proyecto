<?php
    //Conexion base de datos
    require "../../includes/config/database.php";
    $db= conectarBD();

    

    function obtenerPrecioPlato($db, $idPlato) {
        $query = "SELECT precio FROM plato WHERE idplato = " . intval($idPlato);
        $resultado = mysqli_query($db, $query);
        return ($resultado && mysqli_num_rows($resultado) > 0) 
            ? mysqli_fetch_assoc($resultado)['precio'] 
            : 0;
    }

    //consultar mesero
    $consulta = "SELECT * FROM mesero";
    $resultado = mysqli_query($db,$consulta);

    //consultar plato
    $consultaPlato = "SELECT * FROM plato";
    $resultadoPlato = mysqli_query($db,$consultaPlato);

    //arreglo para mensajes con errores
    $errores = [];


    

    if($_SERVER['REQUEST_METHOD'] === 'POST'){ //AQUI VALIDAMOS LOS ERRORES E INSERTAMOS EN LA BASE DE DATOS
        
        //Validar mesero
        $meseroId = $_POST['mesero'] ?? '';
        if (empty($meseroId)) {
        $errores[] = 'Selecciona un mesero';
        }

        //Validar platos y cantidades
        $platos = $_POST['plato'] ?? [];
        $cantidades = $_POST['cantidades'] ?? [];
        $totalEnviado = floatval($_POST['total'] ?? 0);
        $totalCalculado = 0;

        if (empty($platos)) {
            $errores[] = 'Agrega al menos un plato';
        } else {
            foreach ($platos as $index => $platoId) {
                // Validar que el plato no esté vacío
                if (empty($platoId)) {
                    $errores[] = 'Selecciona un plato válido';
                    break;
                }

                // Validar cantidad
                $cantidad = intval($cantidades[$index] ?? 0);
                if ($cantidad < 1) {
                    $errores[] = 'La cantidad debe ser mayor a 0';
                    break;
            }

                // Calcular total real (consultando precio en DB)
                $precioPlato = obtenerPrecioPlato($db, $platoId); 
                $totalCalculado += $precioPlato * $cantidad;
            }
        }

        // Validar coincidencia del total 
        if (abs($totalEnviado - $totalCalculado) > 0.01) { 
        $errores[] = 'El total no coincide con los platos seleccionados';
        }

        //revisar que el arreglo de erroes este vacio para insertar los datos en la base de datos
        if(empty($errores)) {
            // 1. Insertar la orden principal
            $queryOrden = "INSERT INTO orden (total, fecha, mesero_idmesero) 
                          VALUES (?, NOW(), ?)";
            $stmtOrden = $db->prepare($queryOrden);
            $stmtOrden->bind_param("di", $totalCalculado, $meseroId);
            $stmtOrden->execute();
    
            // Obtener el ID de la orden recién creada
            $idOrden = $stmtOrden->insert_id;
    
            // 2. Insertar los detalles de la orden (platos)
            foreach ($platos as $index => $platoId) {
                $cantidad = intval($cantidades[$index]);

                $queryDetalle = "INSERT INTO detalle_orden (cantidad, plato_idplato, orden_idorden) 
                                VALUES (?, ?, ?)";
                $stmtDetalle = $db->prepare($queryDetalle);
                $stmtDetalle->bind_param("iii", $cantidad, $platoId, $idOrden);
                $stmtDetalle->execute();
            }
    
            // 3. Redirigir con mensaje de éxito
            header('Location: /admin/ordenes?resultado=1');
        
        }

    }

    require '../../includes/funciones.php';   
    incluirTemplate ('header');   
?>

<main>
    <div class="contenedor">
        <h1 class="titulo-main">Crear orden</h1>

        <a href="/admin/ordenes/index.php" class='boton boton-plato'>volver</a>
    </div>

    <!--vamos a iterar para que cuando haya un error se muestre en pantalla-->


    <?php foreach($errores as $error):?>
        <div class="alerta error centrar">
            <?php echo $error ?>
        </div>
        
    <?php endforeach;?>

    <form action="/admin/ordenes/crear.php" class="contenedor formulario" method="POST" enctype="multipart/form-data">

        <!---iteramos para mostrar meseros-->  

        <legend>Mesero</legend>

        <select name="mesero" id="" class="campo__input">
            <option value="">>--seleccione--<</option>
            <?php while($mesero = mysqli_fetch_assoc($resultado)): ?>
                <option value="<?php echo $mesero['idmesero']?>"><?php echo $mesero['nombre']." ".$mesero['apellido'];?></option>       
            <?php endwhile;?>
        </select>


    

        <!---iteramos para mostrar los platos y los precios-->           

        <legend>Plato</legend>

    <div class="contenedor-platos">

        <div class="grupo-plato">
            <div class="campos-plato">    
                <select name="plato[]" class="campo__input descripcion-plato" onchange="mostrarDescripcion(this)">
                    <option value="">>--seleccione plato--<</option>
                    <?php 
                    mysqli_data_seek($resultadoPlato, 0);
                    while($plato = mysqli_fetch_assoc($resultadoPlato)): ?>
                        <option value="<?php echo $plato['idplato']?>"
                    data-descripcion="<?php echo $plato['descripcion'] ?>"
                    data-precio="<?php echo $plato['precio'] ?>">

                        <?php echo $plato['nombre'] ?>;
                        ($<?php echo $plato['precio']?>)

                        </option>       
                    <?php endwhile;?>
                </select>


                <!---campo para cantidades-->
                <label for="cantidades">Cantidad</label>
                <input type="number" id="cantidades" name="cantidades[]" min="1" value="1" class="campo__input"     placeholer="cantidad">

                <!---campo para descripcion-->
                <h3>Descripcion</h3>
                <div data-descripcion-container style="margin: 10px 0; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                </div>

                

            </div>  

                <button type="button" class="boton boton-eliminar-plato boton-elimina" >🗑️</button>  
        </div> 

    </div>

             
    <!-- Botón para agregar más platos -->
    <button type="button" id="agregar-plato" class="boton boton-secundario boton-agrega">
            ➕ Agregar otro plato
    </button>

    <!--SUMAR TOTAL-->
    
    <div class="grupo-total">
                <h3>Total: <span id="total">$0</span></h3>
                <input type="hidden" name="total" id="input-total" value="0">
    </div>

    <input type='submit' value="Crear Orden" class="boton crear-plato">
    </form>

<script src="/admin/js/ordenes.js"></script>

</main>

<?php
   
incluirTemplate ('footer');
?>

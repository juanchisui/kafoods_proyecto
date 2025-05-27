<?php
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
<?php
    ob_start();
    require __DIR__ . '/../../includes/config/database.php';
    $db = conectarBD();
    require __DIR__ . '/../libs/fpdf/fpdf.php';

    //validar el id de la orden
    $id_orden =$_GET['id'] ?? null;
    $id_orden = filter_var($id_orden, FILTER_VALIDATE_INT);
    
    //traemos los datos de la orden
    $query_orden = "SELECT o.*, CONCAT(m.nombre, ' ', m.apellido) AS mesero FROM orden o JOIN mesero m ON o.mesero_idmesero = m.idmesero WHERE o.idorden = $id_orden";

    $resultado_orden= mysqli_query($db,$query_orden);
    $orden = mysqli_fetch_assoc($resultado_orden);

    //configuarion del pdf
    $pdf = new FPDF ('P','mm', array(110,150));
    $pdf ->AddPage();
    $pdf->SetMargins(10, 10, 10);

    //el logo
    $pdf->Image(__DIR__ . '/../../img/logo.png', 40, 5, 30);
    $pdf-> Ln(20);

    //titulo
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 6, 'KFOODS - Recibo #'.$orden['idorden'], 0, 1, 'C');
    $pdf->Ln(5);

    // Datos básicos
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y H:i', strtotime($orden['fecha'])), 0, 1);
    $pdf->Cell(0, 6, 'Mesero: ' . $orden['mesero'], 0, 1);
    $pdf->Ln(5);

    // Tabla de platos

    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(40, 6, 'Plato', 1, 0);
    $pdf->Cell(15, 6, 'Precio', 1, 0, 'C');
    $pdf->Cell(15, 6, 'Cant.', 1, 0, 'C');
    $pdf->Cell(20, 6, 'Subtotal', 1, 1, 'C');

    //Contenido de la tabla
    $pdf->SetFont('Arial', '', 8);
    $query_platos = "SELECT p.nombre, p.precio, d.cantidad, 
                    (d.cantidad * p.precio) AS subtotal 
                    FROM detalle_orden d 
                    JOIN plato p ON d.plato_idplato = p.idplato 
                    WHERE d.orden_idorden = $id_orden";
    $platos = mysqli_query($db, $query_platos);

    while ($plato = mysqli_fetch_assoc($platos)) {
        $pdf->Cell(40, 6, $plato['nombre'], 1);
        $pdf->Cell(15, 6, '$' . number_format($plato['precio'], 2), 1, 0, 'C');
        $pdf->Cell(15, 6, $plato['cantidad'], 1, 0, 'C');
        $pdf->Cell(20, 6, '$' . number_format($plato['subtotal'], 2), 1, 1, 'C');
    }

    // Total
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Ln(5);
    $pdf->Cell(0, 8, 'TOTAL: $' . number_format($orden['total'], 2), 0, 1, 'R');

    // Pie de página
    //$pdf->SetY(-15);
    //$pdf->SetFont('Arial', 'I', 8);
    //$pdf->Cell(0, 5, 'Gracias por su compra!', 0, 1, 'C');

    // 10. Generar PDF
    $pdf->Output('I', 'Recibo_KFoods_' . $id_orden . '.pdf');
    ob_end_flush();
?>
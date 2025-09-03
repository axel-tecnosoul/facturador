<?php
require('../vendor/fpdf/fpdf.php');

$id = $_GET['id'];

class PDF extends FPDF
{

   // Cabecera de página
   function Header()
   {
      global $id;
      include '../database.php';
      $pdo = Database::connect();
	   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      /** Venta */
      $sql = "SELECT v.id, v.punto_venta, v.nombre_cliente,v.direccion, v.cae, v.fecha_vencimiento_cae, v.numero_comprobante, v.tipo_comprobante, v.id_descuento_aplicado, fp.forma_pago,d.porcentaje, v.total, v.total_con_descuento FROM ventas v INNER JOIN forma_pago fp ON fp.id LEFT JOIN descuentos d ON d.id = v.id_descuento_aplicado = v.id_forma_pago WHERE v.id = ?";
      $q = $pdo->prepare($sql);
      $q->execute(array($id));
		$data = $q->fetch(PDO::FETCH_ASSOC);

      /* Variables*/
      $punto_venta = $data['punto_venta'];
      $cuit = "30-71775420-0";
      $fecha_inicio_actividad = "01/09/2017";
      $fecha_vto_pago = "05/04/2023";
      $ingresos_brutos = "27-27032771-6";
      $observaciones = "";
      $obs=$observaciones;
      if(strlen($obs)>100){
      $obs=substr($observaciones,0,100)."[...]";
      }
      /* LINEAS HORIZONTALES*/
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(8, 8,201,8);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(8, 14,201,14);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(98, 30,114,30);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(8, 50,201,50);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(8, 58,201,58);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(8, 59,201,59);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(8, 77,201,77);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(8, 265,201,265);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(8, 283,201,283);
      //$this->SetDrawColor(0, 0, 255, 0);
      
      

      /* LINEAS VERTICALES*/
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(8, 8,8,58);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(98, 14,98,30);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(114, 14,114,30);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(106, 30,106,50);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(201, 8,201,58);
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(201, 59,201,77);
      
      //$this->SetDrawColor(0, 0, 255, 0);
      $this->Line(8, 59,8,77);

      /* ORIGINAL */
      $this->Cell(88);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(0, 2, utf8_decode("ORIGINAL"));
      $this->Ln(5);

      /* Filas */
      
      /* TIPO COMPROBANTE*/
      $this->Cell(92);  // mover a la derecha
      $this->SetFont('Arial', 'B', 25);
      $this->Cell(0, 15, utf8_decode('B'));
      $this->Ln(5);
      /* Tipo de Factura */
      $this->SetFont('Arial', 'B', 16); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
      $this->Cell(110); // Movernos a la derecha
      //creamos una celda o fila
      if($data['tipo_comprobante'] == 'B'){
         $this->Cell(110, 0, utf8_decode('FACTURA'));
      }else{
         $this->Cell(110, 0, utf8_decode('NOTA DE CREDITO'));
      }
      $this->Ln(8); // Salto de línea
      /* NOMBRE */
      $this->Cell(1);  // mover a la derecha
      $this->Image('../assets/images/logoBackend.png',35,16,36);
      $this->Ln(1);
      
      /* Domicilio */
      $this->Cell(1);  // mover a la derecha
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, 4, utf8_decode("Riobamba 2751 - 1653 - VILLA BALLESTER - BS.AS"), 0, 0, '', 0);
      $this->Ln(1);

      /* Correo */
      $this->Cell(1);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(0, 10, utf8_decode("Email: "), 0, 0, '', 0);
      $this->Ln(1);
      $this->Cell(10);  // mover a la derecha
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, 8, utf8_decode("miroperitooficial@gmail.com"), 0, 0, '', 0);
      $this->Ln(1);

      /* TEL */
      $this->Cell(1);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(0, 13, utf8_decode("Tel: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(7);  // mover a la derecha
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, 13, utf8_decode("1140467012"), 0, 0, '', 0);
      $this->Ln(1);
      

      /* Condición IVA */
      $this->Cell(1);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(0, 18, utf8_decode("Condición frente al IVA: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(34);
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, 18, utf8_decode("Monotributista"), 0, 0, '', 0);
      $this->Ln(6);

      /* Comp Nº */
      $this->Cell(160);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(59, -24, utf8_decode("Comp Nº: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(174);
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, -24, utf8_decode(str_pad($data['numero_comprobante'],8,"0",STR_PAD_LEFT)), 0, 0, '', 0);
      $this->Ln(2);
      

      /* Punto de Venta */
      $this->Cell(110);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(59, -28, utf8_decode("Punto de Venta:"), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(132);
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, -28, utf8_decode(str_pad($punto_venta,4,"0",STR_PAD_LEFT)), 0, 0, '', 0);
      $this->Ln(0);

      /* Fecha de emisión */
      $this->Cell(110);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(85, -15, utf8_decode("Fecha de Emisión: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(136);
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, -15, utf8_decode(date('d/m/Y')), 0, 0, '', 0);
      $this->Ln(5);

      /* CUIT */
      $this->Cell(110);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(85, -17, utf8_decode("CUIT: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(119);
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, -17, utf8_decode($cuit), 0, 0, '', 0);
      $this->Ln(5);


      /* Ingresos Brutos */
      $this->Cell(110);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(85, -19, utf8_decode("Ingresos Brutos: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(134);  // mover a la derecha
      $this->SetFont('Arial', '', 8);
      $this->Cell(85, -19, utf8_decode($ingresos_brutos), 0, 0, '', 0);
      $this->Ln(5);

      /* Fecha de Inicio de Actividades */
      $this->Cell(110);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(85, -20, utf8_decode("Fecha de Inicio de Actividades: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(153);
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, -20, utf8_decode($fecha_inicio_actividad), 0, 0, '', 0);
      $this->Ln(1);


      /* Fecha de Vencimiento */
      $this->Cell(110);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(85, -6, utf8_decode("Fecha de Vto. para el pago: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(148);
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, -6, utf8_decode($fecha_vto_pago), 0, 0, '', 0);
      $this->Ln(10);

      /* Apellido y Nombre / Razon Social */
      $this->Cell(1);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(85, -5, utf8_decode("Apellido y Nombre/Razon Social: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(47);
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, -5, utf8_decode($data['nombre_cliente']), 0, 0, '', 0);
      $this->Ln(5);

      /* Condición IVA */
      $this->Cell(1);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(85, 0, utf8_decode("Condición IVA: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(22);
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, 0, utf8_decode("Consumidor Final"), 0, 0, '', 0);
      $this->Ln(5);

      /* CUIT */
      $this->Cell(110);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(85, -25, utf8_decode("CUIT: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(118);
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, -25, utf8_decode("-"), 0, 0, '', 0);
      $this->Ln(5);

      /* Domicilio Comercial */
      $this->Cell(110);  // mover a la derecha
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(85, -20, utf8_decode("Domicilio Comercial: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(139);
      $this->SetFont('Arial', '', 8);
      $this->Cell(0, -20, utf8_decode($data['direccion']), 0, 0, '', 0);
      $this->Ln(-3);

      /* CAMPOS DE LA TABLA */
      $this->Cell(-2);
      $this->SetFillColor(160, 160, 160); //colorFondo
      $this->SetTextColor(0, 0, 0); //colorTexto
      $this->SetDrawColor(0, 0, 0); //colorBorde
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(13, 7, utf8_decode('Código'), 1, 0, 'C', 1);
      $this->Cell(105, 7, utf8_decode('Descripción'), 1, 0, 'C', 1);
      $this->Cell(15, 7, utf8_decode('Cant.'), 1, 0, 'C', 1);
      $this->Cell(24, 7, utf8_decode('Precio Unit.'), 1, 0, 'C', 1);
      $this->Cell(18, 7, utf8_decode('Bonif'), 1, 0, 'C', 1);
      $this->Cell(18, 7, utf8_decode('Subtotal'), 1, 1, 'C', 1);

      $this->SetFillColor(255, 255, 255); //colorFondo
      $this->SetTextColor(0, 0, 0); //colorTexto
      $this->SetDrawColor(0, 0, 0); //colorBorde
      $this->SetFont('Arial', '', 6);
   
      /* Detalle  Venta*/
      $sql2 = "SELECT vd.id_venta, vd.id_producto, p.codigo, p.descripcion, p.precio, vd.cantidad,vd.precio, vd.subtotal FROM ventas_detalle vd INNER JOIN productos p ON p.id = vd.id_producto WHERE vd.id_venta = $id";

      $subtotal = 0;
      $ln = 0;
      $cant = 0;
      $descripcion= "";
      $observaciones = "";
      $porcentaje = [];
      foreach ($pdo->query($sql2) as $row){
         $this->Cell(-2);
         if($cant <= 20){
               $this->Cell(13, 7, utf8_decode('CDL'.$row['codigo']), 1, 0, 'C', 0);
               if(strlen($row['descripcion'])>77){
                  $descripcion=substr($row['descripcion'],0,77)."[...]";
               }else{
                  $descripcion = $row['descripcion'];
               }
               $this->Cell(105, 7, utf8_decode($descripcion), 1, 0, 'L', 0);
               $this->Cell(15, 7, utf8_decode($row['cantidad']), 1, 0, 'C', 0);
               $this->Cell(24, 7, utf8_decode("$".number_format($row['precio'], 2,',', '.')), 1, 0, 'R', 0);
               $this->Cell(18, 7, utf8_decode("$".number_format(($row['precio'] - $row['subtotal']), 2,',', '.')), 1, 0, 'R', 0);
               $this->Cell(18, 7, utf8_decode("$".number_format($row['subtotal'], 2,',', '.')), 1, 1, 'R', 0);
               $subtotal= $row['subtotal'] + $subtotal;
               $ln = $ln + 7;//Salto de linea que resta del total
               $cant = $cant + 1;
               $observaciones = "";
         }
         
         
      }
      $ln = 144 - $ln;
      $this->Ln($ln);//Con 1 solo dato en la tabla el valor seria 144
      $this->Cell(111);
      $this->SetFillColor(160, 160, 160); //colorFondo
      $this->SetTextColor(0, 0, 0); //colorTexto
      $this->SetDrawColor(0, 0, 0); //colorBorde
      $this->SetFont('Arial', '', 8); 
      $this->Cell(80, 8, utf8_decode("  Subtotal"), 0, 5, '', 1);
      $this->Ln(-4);
      $this->Cell(173);
      $this->Cell(0, 0, utf8_decode("$".number_format($subtotal,2, ',', '.')));
      $this->Ln(1);
      $this->Cell(111);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(80, 8, utf8_decode("  Total Venta"), 0, 0, '', 1);
      $this->Ln(0);
      $this->Cell(170);
      $this->Cell(0, 8, utf8_decode("$".number_format($subtotal,2, ',', '.')));
      /* Lineas Horizontales */
      $this->Line(121, 230,201,230);
      $this->Line(121, 243,201,243);
      /* Lineas Verticales */
      $this->Line(121, 230,121,243);
      $this->Line(201, 230,201,243);
      $this->Ln(10);
      $this->Cell(-2);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(193, 12, utf8_decode("Observaciones: "), 1, 0, '', 0);
      $this->Ln(0);
      $this->Cell(25);
      $this->SetFont('Arial', '', 10);
      $this->Cell(190, 12, utf8_decode($observaciones), 0, 0, '', 0);
      $this->Ln(20);
      $this->Cell(1);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(190, 12, utf8_decode("CAE Nº: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(15);
      $this->SetFont('Arial', '', 10);
      $this->Cell(190, 12, utf8_decode($data['cae']), 0, 0, '', 0);
      $this->Ln(5);
      $this->Cell(1);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(190, 12, utf8_decode("Fecha de Vto CAE: "), 0, 0, '', 0);
      $this->Ln(0);
      $this->Cell(34);
      $this->SetFont('Arial', '', 10);
      $fecha_vencimiento_cae= strtotime($data['fecha_vencimiento_cae']);
      $fecha_vencimiento_cae= date("d/m/Y", $fecha_vencimiento_cae);
      $this->Cell(190, 12, utf8_decode($fecha_vencimiento_cae), 0, 0, '', 0);
   }

   // Pie de página
   function Footer()
   {
      $this->SetY(-15); // Posición: a 1,5 cm del final
      $this->SetFont('Arial', 'I', 8); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
      $this->Cell(355, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C'); //pie de pagina(numero de pagina)

      $this->SetY(-15); // Posición: a 1,5 cm del final
      $this->SetFont('Arial', 'I', 8); //tipo fuente, cursiva, tamañoTexto
      $this->Cell(0, 10, utf8_decode('2023 @ Desarrollado por Misiones Software'), 0, 0, 'C'); // pie de pagina(fecha de pagina)
   }
}


$pdf = new PDF();
$pdf->AddPage(); /* aqui entran dos para parametros (horientazion,tamaño)V->portrait H->landscape tamaño (A3.A4.A5.letter.legal) */
$pdf->AliasNbPages(); //muestra la pagina / y total de paginas

$i = 0;
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetDrawColor(0, 0, 0); //colorBorde


$i = $i + 1;
/* TABLA */



$pdf->Output('reporteVentasCliente.pdf', 'I');//nombreDescarga, Visor(I->visualizar - D->descargar)
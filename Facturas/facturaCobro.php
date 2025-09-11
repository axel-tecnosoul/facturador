<?php
require('../vendor/fpdf/fpdf.php');
require_once '../database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('ID inválido');
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT f.id, f.fecha, f.total, f.cae, f.fecha_vencimiento_cae, f.numero_comprobante, c.razon_social, c.direccion, c.cuit, c.email FROM facturas f INNER JOIN clientes c ON f.id_cliente = c.id WHERE f.id = ?";
$q = $pdo->prepare($sql);
$q->execute([$id]);
$factura = $q->fetch(PDO::FETCH_ASSOC);
if (!$factura) {
    Database::disconnect();
    die('Factura no encontrada');
}

$sql = "SELECT descripcion, precio, cantidad FROM factura_detalle WHERE id_factura = ?";
$q = $pdo->prepare($sql);
$q->execute([$id]);
$items = $q->fetchAll(PDO::FETCH_ASSOC);
Database::disconnect();

$pdf = new FPDF();
$pdf->AddPage();

// Logo y datos de la empresa
$pdf->Image('../assets/images/logoBackend.png', 10, 8, 40);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,utf8_decode('Factura'),0,1,'R');
$pdf->SetFont('Arial','',9);
$pdf->Cell(0,5,utf8_decode('Riobamba 2751 - 1653 - VILLA BALLESTER - BS.AS'),0,1);
$pdf->Cell(0,5,utf8_decode('CUIT: 30-71775420-0'),0,1);
$pdf->Cell(0,5,utf8_decode('Email: miroperitooficial@gmail.com'),0,1);
$pdf->Ln(5);

// Datos del cliente
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,6,utf8_decode('Cliente'),0,1);
$pdf->SetFont('Arial','',9);
$pdf->Cell(0,5,utf8_decode($factura['razon_social']),0,1);
$pdf->Cell(0,5,utf8_decode($factura['direccion']),0,1);
$pdf->Cell(0,5,utf8_decode('CUIT: '.$factura['cuit']),0,1);
$pdf->Ln(5);

// Detalle de items
$pdf->SetFont('Arial','B',9);
$pdf->Cell(100,7,'Descripcion',1);
$pdf->Cell(30,7,'Cantidad',1,0,'R');
$pdf->Cell(30,7,'Precio',1,0,'R');
$pdf->Cell(30,7,'Subtotal',1,1,'R');

$pdf->SetFont('Arial','',9);
$total = 0;
foreach ($items as $item) {
    $subtotal = $item['precio'] * $item['cantidad'];
    $total += $subtotal;
    $pdf->Cell(100,7,utf8_decode($item['descripcion']),1);
    $pdf->Cell(30,7,$item['cantidad'],1,0,'R');
    $pdf->Cell(30,7,number_format($item['precio'],2,',','.'),1,0,'R');
    $pdf->Cell(30,7,number_format($subtotal,2,',','.'),1,1,'R');
}

$pdf->SetFont('Arial','B',9);
$pdf->Cell(160,7,'Total',1);
$pdf->Cell(30,7,number_format($total,2,',','.'),1,1,'R');
$pdf->Ln(5);

// CAE
$pdf->SetFont('Arial','',8);
$pdf->Cell(0,5,'CAE: '.$factura['cae'],0,1);
$pdf->Cell(0,5,'Vto CAE: '.$factura['fecha_vencimiento_cae'],0,1);

$pdfContent = $pdf->Output('S');

// Enviar por correo opcional
if (isset($_GET['enviar']) && $factura['email']) {
    require_once '../PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer();
    $mail->setFrom('no-reply@example.com', 'Mi Empresa');
    $mail->addAddress($factura['email'], $factura['razon_social']);
    $mail->Subject = 'Factura';
    $mail->Body = 'Adjuntamos su factura.';
    $mail->addStringAttachment($pdfContent, 'factura.pdf');
    @$mail->send();
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="factura_'.$id.'.pdf"');
echo $pdfContent;


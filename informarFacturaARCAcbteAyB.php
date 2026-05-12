<?php
ini_set("display_errors",1);
ini_set("display_startup_errors",1);
error_reporting(E_ALL);
require("config.php");
if(empty($_SESSION['user']['id_perfil'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
require 'funciones.php';

if ( !empty($_GET)) {
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $modoDebug=0;
  if ($modoDebug==1) {
    $pdo->beginTransaction();
    var_dump($_GET);
  }
  
  $sql = "SELECT * FROM ventas WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($_GET["id"]));
  $data = $q->fetch(PDO::FETCH_ASSOC);

  $sql4 = "SELECT punto_venta FROM almacenes WHERE id = ? ";
  $q4 = $pdo->prepare($sql4);
  $q4->execute(array($data['id_almacen']));
  $data4 = $q4->fetch(PDO::FETCH_ASSOC);

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";
  }

  include './../external/afip/Afip.php';
  include 'config_facturacion_electronica.php';//poner $homologacion=1 para facturar en modo homologacion. Retorna $aInitializeAFIP.

  if($modoDebug==1){
    var_dump($aInitializeAFIP);
  }
  $afip = new Afip($aInitializeAFIP);

  $punto_venta=$data4["punto_venta"];

  $server_status = $afip->ElectronicBilling->GetServerStatus();
  /*echo 'Este es el estado del servidor:';
  var_dump($server_status);*/

  $ImpTotal=$data["total_con_descuento"];
  $tipo_comprobante=$data["tipo_comprobante"];
  //$total=121;
  if($tipo_comprobante=="A"){
    $tipo_comprobante=1;//1 -> Factura A
  }elseif($tipo_comprobante=="B"){
    $tipo_comprobante=6;//6 -> Factura B
    
  }
  $ImpNeto=$ImpTotal/1.21;
  $ImpIVA=$ImpTotal-$ImpNeto;

  $DocNro=$data["dni"];
  $DocTipo=$data["tipo_doc"];;
  
  $ImpNeto=number_format($ImpNeto,2,".","");
  $ImpIVA=number_format($ImpIVA,2,".","");
  

  $data = array(
    'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
    'PtoVta' 	=> $punto_venta,  // Punto de venta
    'CbteTipo' 	=> $tipo_comprobante,  // Tipo de comprobante (ver tipos disponibles) 
    'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
    'DocTipo' 	=> $DocTipo, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles). Para comprobantes clase A y M el campo DocTipo debe ser igual a 80 (CUIT)
    'DocNro' 	=> $DocNro,  // Número de documento del comprador (0 consumidor final)
    'CbteDesde' 	=> 2,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
    'CbteHasta' 	=> 2,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
    'CbteFch' 	=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
    'ImpTotal' 	=> $ImpTotal,//121, // Importe total del comprobante
    'ImpTotConc' 	=> 0,   // Importe neto no gravado
    'ImpNeto' 	=> $ImpNeto,//100, // Importe neto gravado
    'ImpOpEx' 	=> 0,   // Importe exento de IVA
    'ImpIVA' 	=> $ImpIVA,//21,  //Importe total de IVA
    'ImpTrib' 	=> 0,   //Importe total de tributos
    'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
    'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
    'Iva' 		=> array( // (Opcional) Alícuotas asociadas al comprobante
      array(
        'Id' 		=> 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
        'BaseImp' 	=> $ImpNeto,//100, // Base imponible -> ES IGUAL A ImpNeto?
        'Importe' 	=> $ImpIVA,//21 // Importe -> ES IGUAL A ImpIVA?
      )
    ), 
  );

  if ($modoDebug==1) {
    var_dump($data);
  }

  //$res = $afip->ElectronicBilling->CreateVoucher($data);
  //$res = $afip->ElectronicBilling->CreateNextVoucher($data);

  $res="";
  try {
    //$res = $afip->ElectronicBilling->CreateVoucher($data);
    $res = $afip->ElectronicBilling->CreateNextVoucher($data);
  } catch (Exception $e) {
      echo 'Error: ',  $e->getMessage(), "\n";
      var_dump($data);
      echo "<a href='verVenta.php?id=".$_GET["id"]."'>Volver a Ver venta</a>";
      die();
  }
  
  if(isset($res['CAE'])){
    $estado="A";
    $CAE=$res['CAE'];//CAE asignado el comprobante
    $CAEFchVto=$res['CAEFchVto'];//Fecha de vencimiento del CAE (yyyy-mm-dd)
    $voucher_number=$res['voucher_number'];//Número asignado al comprobante

    if ($modoDebug==1) {
      var_dump($res);
      var_dump($CAE);
      var_dump($CAEFchVto);
      var_dump($voucher_number);
    }

    $sql = "UPDATE ventas SET tipo_doc = ?, estado = ?, punto_venta = ?, numero_comprobante = ?, cae = ?, fecha_vencimiento_cae = ? WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($DocTipo,$estado,$punto_venta,$voucher_number,$CAE,$CAEFchVto,$_GET["id"]));

    if ($modoDebug==1) {
      $q->debugDumpParams();
      echo "<br><br>Afe: ".$q->rowCount();
      echo "<br><br>";
    }

    if($q->rowCount()==1){
      echo "Venta informada a AFIP correctamente";
    }

  }

  if ($modoDebug==1) {
    $pdo->rollBack();
    die();
  }
  Database::disconnect();
  
  header("Location: verVenta.php?id=".$_GET["id"]);
}
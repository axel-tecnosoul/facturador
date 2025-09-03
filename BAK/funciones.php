<?php

function get_nombre_comprobante($tipo_comprobante){
  switch ($tipo_comprobante) {
    case 'R':
      $tipo_cbte="Recibo";
      break;
    case 'A':
      $tipo_cbte="Factura A";
      break;
    case 'B':
      $tipo_cbte="Factura B";
      break;
    case 'NCA':
      $tipo_cbte="Nota de Crédito A";
      break;
    case 'NCB':
      $tipo_cbte="Nota de Crédito B";
      break;
    default:
      $tipo_cbte="";
      break;
  }
  return $tipo_cbte;
}

function get_estado_comprobante($estado_abreviado){
  switch ($estado_abreviado) {
    case 'R':
      $estado_completo="Rechazado";
      break;
    case 'A':
      $estado_completo="Aprobado";
      break;
    case 'E':
      $estado_completo="ERROR";
      break;
    default:
      $estado_completo="";
      break;
  }
  return $estado_completo;
}

function format_numero_comprobante($punto_venta,$numero_comprobante){
  return str_pad($punto_venta,4,"0",STR_PAD_LEFT)."-".str_pad($numero_comprobante,8,"0",STR_PAD_LEFT);
}

function calcularDeudaProveedor($id_forma_pago,$id_modalidad,$precio_final){
  require_once "config.php";
  require_once 'database.php';

  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $sql4 = "SELECT valor FROM parametros WHERE id = 8 ";
  $q4 = $pdo->prepare($sql4);
  $q4->execute();
  $data4 = $q4->fetch(PDO::FETCH_ASSOC);
  $porcentaje_pagar_no_efectivo=(100-$data4["valor"])/100;
  //var_dump($fp);
  $pdo = Database::disconnect();

  $fp = 1;
  //si el pago no es en efectivo se le hace un descuento a la proveedora
  if ($id_forma_pago != 1) {
    //$fp = 0.85;
    //$fp = 0.80;
    $fp = $porcentaje_pagar_no_efectivo;
  }

  $porcentaje_modalidad = 0;
  if ($id_modalidad == 1) {//COMPRA DIRECTA

  } else if ($id_modalidad == 40) {//CONSIGNACION POR PORCENTAJE
    $porcentaje_modalidad = 0.4;
  } else if ($id_modalidad == 50) {//CONSIGNACION POR CREDITO
    $porcentaje_modalidad = 0.5;
  }
  
  $deuda_proveedor = number_format($precio_final*$porcentaje_modalidad*$fp,2,".","");
  return $deuda_proveedor;
}

function calcularDeudaProveedorViejo($id_forma_pago,$id_modalidad,$precio_final){
  $fp = 1;
  //si el pago no es en efectivo se le hace un descuento a la proveedora
  if ($id_forma_pago != 1) {
    $fp = 0.85;
    //$fp = 0.80;
  }

  $porcentaje_modalidad = 0;
  if ($id_modalidad == 1) {//COMPRA DIRECTA

  } else if ($id_modalidad == 40) {//CONSIGNACION POR PORCENTAJE
    $porcentaje_modalidad = 0.4;
  } else if ($id_modalidad == 50) {//CONSIGNACION POR CREDITO
    $porcentaje_modalidad = 0.5;
  }
  
  $deuda_proveedor = number_format($precio_final*$porcentaje_modalidad*$fp,2,".","");
  return $deuda_proveedor;
}

function ultimosDoceMeses($mesAnio=0){
  if($mesAnio==0){
      $mesAnio=date("m-Y");
  }
  $label=[$mesAnio=>0];
  $c=0;
  while($c<11){
    $mesAnio=date("m-Y",strtotime("01-".$mesAnio." - 1 month"));
    $label[$mesAnio]=0;
    $c++;
  }
  $label=array_reverse($label);
  return $label;
}

function formatFechaGraficoLineasPorMeses($array){
  $newArray=[];
  foreach ($array as $key => $value) {
      $newArray[]=date("Y M", strtotime("01-".$key));
  }
  return $newArray;
}

function randomColor(){
  $str = "#";
  for ($i = 0 ; $i < 6 ; $i++) {
      $randNum = rand(0, 15);
      switch ($randNum) {
          case 10: $randNum = "A";
          break;
          case 11: $randNum = "B";
          break;
          case 12: $randNum = "C";
          break;
          case 13: $randNum = "D";
          break;
          case 14: $randNum = "E";
          break;
          case 15: $randNum = "F";
          break;
      }
      $str .= $randNum;
  }
  return $str;
}
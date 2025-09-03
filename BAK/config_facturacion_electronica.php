<?php
/*
archivo de configuracion para los datos de facturacion electronica, espera la variable $homologacion para poder facturar en modo homolagcion
*/

$homologacion=0;

$cuit=30717754200;
$produccion=true;
$aInitializeAFIP=array('CUIT' => $cuit,'production'=>$produccion);
if ($homologacion==1) {
  $cuit=20351290340;
  $produccion=false;
  $ruta="crt_axel_homo/";

  /*$cert=$ruta."homo_axel3.crt";
  $key=$ruta."homo_axel3.key";*/
  $cert=$ruta."crt";
  $key=$ruta."key";
  //$aInitializeAFIP=array('CUIT' => $cuit);
  $aInitializeAFIP=array('CUIT' => $cuit,'production'=>$produccion,'cert'=>$cert,'key'=>$key);
}
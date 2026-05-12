<?php
/*
Archivo de configuración para facturación electrónica ARCA (ex-AFIP)
Espera la variable $homologacion para facturar en modo homologación
*/

$homologacion=0;

//$cuit=2035129034;
$produccion=true;
//$aInitializeAFIP=array('CUIT' => $cuit,'production'=>$produccion);
$aInitializeAFIP=array('production'=>$produccion);
if ($homologacion==1) {
  //$cuit=20351290340;
  $produccion=false;
  $ruta="crt_axel_homo/";

  /*$cert=$ruta."homo_axel3.crt";
  $key=$ruta."homo_axel3.key";*/
  $cert=$ruta."crt";
  $key=$ruta."key";
  //$aInitializeAFIP=array('CUIT' => $cuit);
  //$aInitializeAFIP=array('CUIT' => $cuit,'production'=>$produccion,'cert'=>$cert,'key'=>$key);
  $aInitializeAFIP=array('production'=>$produccion,'cert'=>$cert,'key'=>$key);
}
<?php
include 'database.php';
$aCobros=[];

$tipo_fecha="fecha_cobro";
if(!empty($_GET["tipo_fecha"])){
  if($_GET["tipo_fecha"]=="cobro") {
    $tipo_fecha="fecha_cobro";
  }elseif($_GET["tipo_fecha"]=="factura") {
    $tipo_fecha="fecha_factura";
  }
}

$filtroDesde="";
if(!empty($_GET["desde"]) && $_GET["desde"] != 0) {
  $filtroDesde=" AND co.$tipo_fecha>='".$_GET["desde"]."'";
}
$filtroHasta="";
if(!empty($_GET["hasta"]) && $_GET["hasta"] != 0) {
  $filtroHasta=" AND co.$tipo_fecha<='".$_GET["hasta"]."'";
}
$filtroCliente="";
if(!empty($_GET["id_cliente"]) && $_GET["id_cliente"] != 0) {
  $filtroCliente=" AND co.id_cliente IN (".$_GET["id_cliente"].")";
}
$filtroEmisor="";
if(!empty($_GET["id_emisor"]) && $_GET["id_emisor"] != 0) {
  $filtroEmisor=" AND co.id_emisor IN (".$_GET["id_emisor"].")";
}

//Ventas
$pdo = Database::connect();
$sql = "SELECT co.id AS id_cobro,co.id_factura,co.fecha_factura,date_format(co.fecha_factura,'%d/%m/%Y') AS fecha_factura_formatted,date_format(co.fecha_cobro,'%d/%m/%Y') AS fecha_cobro_formatted,fecha_cobro,date_format(co.fecha_hora_alta,'%d/%m/%Y') AS fecha_hora_alta,co.detalle,cl.nombre AS nombre_cliente,co.monto_pesos,SUM(pc.monto) AS total_pagado,co.monto_dolares,co.cotizacion_usd,COALESCE(em.razon_social, 'Sin emisor') AS emisor_razon_social FROM cobros co INNER JOIN clientes cl ON co.id_cliente=cl.id LEFT JOIN pagos_colaboradores pc ON pc.id_cobro=co.id LEFT JOIN emisores em ON co.id_emisor=em.id WHERE 1 $filtroDesde $filtroHasta $filtroCliente $filtroEmisor GROUP BY co.id";
//echo $sql;
foreach ($pdo->query($sql) as $row) {

  $btnModificar="";
  $btnModificar='<a href="modificarCobro.php?id='.$row["id_cobro"].'"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar" title="Modificar"></a>';
  $btnEliminar="";
  //$btnEliminar='<a href="#" class="btnEliminar" data-id="'.$row["id_cobro"].'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar" title="Eliminar"></a>';
  $btnVer="";
  //$btnVer='<a href="verCobro.php?id='.$row["id_cobro"].'"><img src="img/eye.png" width="30" border="0" alt="Ver Cobro" title="Ver Operaciones"></a>';
  $btnNuevoPagoColaborador=' <a href="nuevoPagoColaborador.php?id='.$row["id_cobro"].'"><img src="img/dolar.png" width="30" border="0" alt="Ver Cobro" title="Nuevo Pago a Colaborador"></a>';
  $btnFactura="";
  if(!empty($row["id_factura"])){
    $hrefFactura='Facturas/facturaCobro.php?id='.$row["id_factura"];
    $btnFactura=' 
      <a href="'.$hrefFactura.'" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;margin-left:4px;width:40px;height:40px;color:#dc3545;font-size:28px;line-height:1;" title="Ver factura PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
  }
  
  $monto_pesos=$row["monto_pesos"];
  $total_pagado=$row["total_pagado"];
  $rentabilidad_pesos=$monto_pesos-$total_pagado;

  $monto_dolares=$row["monto_dolares"];
  $cotizacion_usd=$row["cotizacion_usd"];
  $rentabilidad_usd="";

  /*if($monto_dolares>0){
    $cotizacion_usd=$monto_pesos/$monto_dolares;
    
    $rentabilidad_usd=$rentabilidad_pesos/$cotizacion_usd;
  }*/
  if($cotizacion_usd>0){
    //$cotizacion_usd=$monto_pesos/$monto_dolares;
    
    $rentabilidad_usd=$rentabilidad_pesos/$cotizacion_usd;
  }

  $aCobros[]=[
    "id"=>$row["id_cobro"],
    "fecha_factura"=>[$row["fecha_factura"],$row["fecha_factura_formatted"]],
    "fecha_cobro"=>[$row["fecha_cobro"],$row["fecha_cobro_formatted"]],
    "fecha_hora_alta"=>$row["fecha_hora_alta"],
    "detalle"=>$row["detalle"],
    "razon_social"=>$row["nombre_cliente"],
    "emisor"=>$row["emisor_razon_social"],
    "monto_pesos"=>$monto_pesos,
    "total_pagado"=>$total_pagado,
    "rentabilidad_pesos"=>$rentabilidad_pesos,
    "monto_dolares"=>$monto_dolares,
    "cotizacion_usd"=>$cotizacion_usd,
    "rentabilidad_usd"=>$rentabilidad_usd,
    "acciones" => $btnModificar.$btnEliminar.$btnVer.$btnNuevoPagoColaborador.$btnFactura
  ];
}

Database::disconnect();
echo json_encode($aCobros);
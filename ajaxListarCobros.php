<?php
include_once 'database.php';
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

// Solo una NC autorizada anula el cobro; una pendiente sigue entre los activos.
$estadoCobro = $_GET['estado'] ?? 'activos';
$filtroEstado = ' AND nc.id IS NULL';
if ($estadoCobro === 'anulados') {
  $filtroEstado = ' AND nc.id IS NOT NULL';
} elseif ($estadoCobro === 'todos') {
  $filtroEstado = '';
}

//Ventas
$pdo = Database::connect();
$sql = "SELECT f.punto_venta,f.numero_comprobante,f.homologacion,f.id AS factura_existente,f.estado AS estado_factura,f.cae,f.error_arca,f.id_nota_credito,nc.id AS id_nc,co.id AS id_cobro,co.id_factura,co.fecha_factura,date_format(co.fecha_factura,'%d/%m/%Y') AS fecha_factura_formatted,date_format(co.fecha_cobro,'%d/%m/%Y') AS fecha_cobro_formatted,fecha_cobro,date_format(co.fecha_hora_alta,'%d/%m/%Y') AS fecha_hora_alta,co.detalle,cl.nombre AS nombre_cliente,co.monto_pesos,SUM(pc.monto) AS total_pagado,co.monto_dolares,co.cotizacion_usd,COALESCE(em.razon_social, 'Sin emisor') AS emisor_razon_social FROM cobros co INNER JOIN clientes cl ON co.id_cliente=cl.id LEFT JOIN pagos_colaboradores pc ON pc.id_cobro=co.id LEFT JOIN emisores em ON co.id_emisor=em.id LEFT JOIN facturas f ON f.id=co.id_factura LEFT JOIN facturas nc ON nc.id_factura_asociada=co.id_factura AND nc.tipo_comprobante IN (3,8,13) AND nc.estado='A' AND nc.cae IS NOT NULL AND nc.cae <> '' WHERE 1 $filtroEstado $filtroDesde $filtroHasta $filtroCliente $filtroEmisor GROUP BY co.id";
//echo $sql;
$cobros = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$detalles = array();
$idsFacturas = array_values(array_unique(array_filter(array_column($cobros, 'id_factura'))));
if (!empty($idsFacturas)) {
  $marcadores = implode(',', array_fill(0, count($idsFacturas), '?'));
  $q = $pdo->prepare("SELECT id_factura,descripcion,precio,cantidad FROM factura_detalle WHERE id_factura IN ($marcadores) ORDER BY id");
  $q->execute($idsFacturas);
  foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $item) {
    $detalles[$item['id_factura']][] = $item;
  }
}
$detallesCobros = array();
$idsCobros = array_column($cobros, 'id_cobro');
if (!empty($idsCobros)) {
  $marcadores = implode(',', array_fill(0, count($idsCobros), '?'));
  $q = $pdo->prepare("SELECT id_cobro,descripcion,precio,cantidad FROM cobro_detalle WHERE id_cobro IN ($marcadores) ORDER BY id");
  $q->execute($idsCobros);
  foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $item) $detallesCobros[$item['id_cobro']][] = $item;
}
foreach ($cobros as $row) {

  $btnModificar="";
  $btnModificar='<a href="modificarCobro.php?id='.$row["id_cobro"].'"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar" title="Modificar"></a>';
  $btnEliminar="";
  //$btnEliminar='<a href="#" class="btnEliminar" data-id="'.$row["id_cobro"].'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar" title="Eliminar"></a>';
  $btnVer="";
  //$btnVer='<a href="verCobro.php?id='.$row["id_cobro"].'"><img src="img/eye.png" width="30" border="0" alt="Ver Cobro" title="Ver Operaciones"></a>';
  $btnNuevoPagoColaborador=' <a href="nuevoPagoColaborador.php?id='.$row["id_cobro"].'"><img src="img/dolar.png" width="30" border="0" alt="Ver Cobro" title="Nuevo Pago a Colaborador"></a>';
  $anulado = !empty($row['id_nc']);
  $estadoBadge = $anulado ? '<span class="badge badge-danger mr-2">Anulado por NC</span> ' : '';
  if ($anulado) $btnNuevoPagoColaborador = '';
  $btnFactura="";
  if($row["estado_factura"] === "A" && !empty($row["cae"])){
    $hrefFactura='Facturas/facturaCobro.php?id='.$row["id_factura"];
    $btnFactura=' 
      <a href="'.$hrefFactura.'" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;margin-left:4px;width:40px;height:40px;color:#dc3545;font-size:28px;line-height:1;" title="Ver factura PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
  }
  
  if ($anulado) {
    $btnModificar = $btnNuevoPagoColaborador = $btnFactura = '';
    $btnVer = '<a href="verCobro.php?id='.(int)$row['id_cobro'].'"><img src="img/eye.png" width="30" border="0" alt="Ver cobro" title="Ver cobro"></a>';
  }
  if (!$anulado && !empty($row['id_factura'])) {
    if (empty($row['factura_existente'])) {
      $estadoBadge = '<span class="badge badge-danger d-block mb-2">Factura vinculada no encontrada</span>';
    } elseif ($row['estado_factura'] !== 'A' || empty($row['cae'])) {
      $estadoBadge = '<span class="badge badge-warning d-block mb-2">Factura sin autorizaci&oacute;n registrada</span>';
      if (!empty($row['error_arca'])) {
        $estadoBadge .= '<span class="badge badge-danger d-block mb-2">Error de facturaci&oacute;n</span>';
      }
    }
    if (!empty($row['id_nota_credito'])) {
      $estadoBadge .= '<span class="badge badge-warning d-block mb-2">NC pendiente de conciliaci&oacute;n</span>';
    }
  }
  $homologacionBadge = (int)$row['homologacion'] === 1 ? '<span class="badge badge-warning p-2 d-block mb-2" style="font-size:13px;border:2px solid #b45309;">HOMOLOGACI&Oacute;N - SIN VALIDEZ FISCAL</span>' : '';
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

  $items = $detalles[$row['id_factura']] ?? array(array('descripcion'=>$row['detalle'], 'precio'=>$monto_pesos, 'cantidad'=>1));
  if (empty($row['id_factura']) && !empty($detallesCobros[$row['id_cobro']])) $items = $detallesCobros[$row['id_cobro']];
  $detalle = '';
  if (!empty($row['numero_comprobante'])) {
    $detalle .= '<div class="text-nowrap mb-2">Comprobante: '.sprintf('%04d-%08d', $row['punto_venta'], $row['numero_comprobante']).'</div>';
  }
  $detalle .= 'Detalle:<br><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Descripci&oacute;n</th><th>Cantidad</th><th>Precio unitario</th><th>Subtotal</th></tr></thead><tbody>';
  foreach ($items as $item) {
    $descripcion = htmlspecialchars(html_entity_decode((string)$item['descripcion'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
    $detalle .= '<tr><td style="white-space:normal;">'.$descripcion.'</td><td>'.number_format((float)$item['cantidad'], 2, ',', '.').'</td><td class="text-nowrap">$ '.number_format((float)$item['precio'], 2, ',', '.').'</td><td class="text-nowrap">$ '.number_format((float)$item['precio'] * (float)$item['cantidad'], 2, ',', '.').'</td></tr>';
  }
  $detalle .= '</tbody></table></div>';

  $aCobros[]=[
    "id"=>$row["id_cobro"],
    "anulado"=>$anulado,
    "fecha_factura"=>[$row["fecha_factura"],$row["fecha_factura_formatted"]],
    "fecha_cobro"=>[$row["fecha_cobro"],$row["fecha_cobro_formatted"]],
    "fecha_hora_alta"=>$row["fecha_hora_alta"],
    "detalle"=>$detalle,
    "razon_social"=>$row["nombre_cliente"],
    "emisor"=>$row["emisor_razon_social"],
    "monto_pesos"=>$monto_pesos,
    "total_pagado"=>$total_pagado,
    "rentabilidad_pesos"=>$rentabilidad_pesos,
    "monto_dolares"=>$monto_dolares,
    "cotizacion_usd"=>$cotizacion_usd,
    "rentabilidad_usd"=>$rentabilidad_usd,
    "acciones" => $homologacionBadge.$estadoBadge.$btnModificar.$btnEliminar.$btnVer.$btnNuevoPagoColaborador.$btnFactura
  ];
}

Database::disconnect();
echo json_encode($aCobros);
<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}
include_once 'database.php';
$pdo = Database::connect();

$desde=$_GET["desde"];
$filtroDesde="";
if($desde!=""){
  $filtroDesde="AND DATE(fecha_hora)>='$desde'";
  $filtroDesdePagoProv="AND DATE(fecha_hora_pago)>='$desde'";
}
$hasta=$_GET["hasta"];
$filtroHasta="";
if($hasta!=""){
  $filtroHasta="AND DATE(fecha_hora)<='$hasta'";
  $filtroHastaPagoProv="AND DATE(fecha_hora_pago)<='$hasta'";
}
$forma_pago=$_GET["forma_pago"];
$filtroFormaPago="";
if($forma_pago!=""){
  $filtroFormaPago="AND fp.id IN ($forma_pago)";
}
$id_almacen=$_GET["id_almacen"];
$filtroAlmacen="";
if($id_almacen!=0){
  $filtroAlmacen="AND a.id IN ($id_almacen)";
}
$id_empleado=$_GET["id_empleado"];
$filtroEmpleado="";
if($id_empleado!=0){
  $filtroEmpleado="AND e.id IN ($id_empleado)";
}

function encerrar_entre_comillas($valor) {
  return '"' . addslashes($valor) . '"';
}

$id_pagos_proveedores_desde_pagos_pendientes="pago_realizado";
$motivo=$_GET["motivo"];
$aMotivos=explode(",",$motivo);

$mostrarPagoProveedoresDesdePagosPendientes=0;
//var_dump($motivo);
$filtroMotivo="";
if($motivo!=""){
  if(in_array($id_pagos_proveedores_desde_pagos_pendientes,$aMotivos)){
    $mostrarPagoProveedoresDesdePagosPendientes=1;
  }
  // aplicar la función a cada valor del array
  $aMotivos = array_map('encerrar_entre_comillas', $aMotivos);
  $motivos=implode(",",$aMotivos);
  $filtroMotivo="AND id_motivo IN ($motivos)";
}else{
  $mostrarPagoProveedoresDesdePagosPendientes=1;
}
//$tipo_comprobante=$_GET["tipo_comprobante"];

//como los pagos a proveedores se hacen solamente en efectivo por el momento debemos mostrar esta consulta solamente si se elije "efectivo" entre las formas de pago
$id_forma_pago_efectivo=1;
//$id_motivo_pago_proveedoras=22;
//var_dump($motivo);
//var_dump($mostrarPagoProveedoresDesdePagosPendientes);

$aCaja=[];
//if($desde<=$hasta and $id_almacen!=0){
if($desde<=$hasta){

  $wherePagoProv=" $filtroFormaPago $filtroAlmacen";

  //$where="$wherePagoProv $filtroFormaPago $filtroAlmacen";
  $where="$filtroFormaPago $filtroAlmacen";

  $whereVentas=$where." AND v.anulada = 0";
  $whereCanjes=$where." AND c.anulado = 0";

  //INICIO SALDO ANTERIOR

  $filtroHastaSaldoAnterior="AND DATE(fecha_hora)<'$desde'";
  $filtroHastaSaldoAnteriorPagoProv="AND DATE(fecha_hora_pago)<'$desde'";

  $total_facturas_recibos=0;
  if($filtroMotivo=="" and $filtroEmpleado==""){
    //obtenemos las facturas y ventas para el saldo anterior
    $sql = "SELECT SUM(v.total_con_descuento) AS total_ventas FROM ventas v inner join almacenes a on a.id = v.id_almacen inner join forma_pago fp on fp.id = v.id_forma_pago WHERE tipo_comprobante IN ('R','A','B') $whereVentas $filtroHastaSaldoAnterior";
    $q = $pdo->prepare($sql);
    //echo $sql;
    $q->execute(array());
    $data = $q->fetch(PDO::FETCH_ASSOC);
    /*$total_facturas_recibos=$data["total_ventas"];
    if(is_null($total_facturas_recibos)){
      $total_facturas_recibos=0;
    }*/
    $total_facturas_recibos=0;
    if($data){
      $total_facturas_recibos=$data["total_ventas"];
    }
  }

  $total_notas_credito=0;
  if($filtroMotivo=="" and $filtroEmpleado==""){
    //obtenemos las notas de credito para el saldo anterior
    $sql = "SELECT SUM(v.total_con_descuento) AS total_notas_credito FROM ventas v inner join almacenes a on a.id = v.id_almacen inner join forma_pago fp on fp.id = v.id_forma_pago WHERE tipo_comprobante IN ('NCA','NCB') $whereVentas $filtroHastaSaldoAnterior";
    $q = $pdo->prepare($sql);
    //echo $sql;
    $q->execute(array());
    $data = $q->fetch(PDO::FETCH_ASSOC);
    /*$total_notas_credito=$data["total_notas_credito"];
    if(is_null($total_notas_credito)){
      $total_notas_credito=0;
    }*/
    $total_notas_credito=0;
    if($data){
      $total_notas_credito=$data["total_notas_credito"];
    }
  }

  //obtenemos los movimientos registrados como ingresos a caja chica para el saldo anterior
  $sql2 = "SELECT SUM(mc.monto) AS ingresos_externos FROM movimientos_caja mc inner join almacenes a on a.id = mc.id_almacen_egreso inner join forma_pago fp on fp.id = mc.id_forma_pago LEFT JOIN empleados e ON mc.id_empleado=e.id WHERE anulado=0 AND tipo_caja='Chica' AND  tipo_movimiento='Ingreso' $where $filtroHastaSaldoAnterior $filtroMotivo $filtroEmpleado";
  $q2 = $pdo->prepare($sql2);
  //echo $sql2;
  $q2->execute(array());
  $data2 = $q2->fetch(PDO::FETCH_ASSOC);
  /*$ingresos_externos=$data2["ingresos_externos"];
  if(is_null($ingresos_externos)){
    $ingresos_externos=0;
  }*/
  $ingresos_externos=0;
  if($data2){
    $ingresos_externos=$data2["ingresos_externos"];
  }

  //obtenemos los movimientos registrados como egresos de caja chica para el saldo anterior
  $sql3 = "SELECT SUM(mc.monto) AS egresos_caja_chica FROM movimientos_caja mc inner join almacenes a on a.id = mc.id_almacen_egreso inner join forma_pago fp on fp.id = mc.id_forma_pago LEFT JOIN empleados e ON mc.id_empleado=e.id WHERE anulado=0 AND tipo_caja='Chica' AND tipo_movimiento='Egreso' $where $filtroHastaSaldoAnterior $filtroMotivo $filtroEmpleado";
  $q3 = $pdo->prepare($sql3);
  //echo $sql;
  $q3->execute(array());
  $data3 = $q3->fetch(PDO::FETCH_ASSOC);
  /*$egresos_caja_chica=$data3["egresos_caja_chica"];
  if(is_null($egresos_caja_chica)){
    $egresos_caja_chica=0;
  }*/
  $egresos_caja_chica=0;
  if($data3){
    $egresos_caja_chica=$data3["egresos_caja_chica"];
  }

  $total_pago_proveedores=0;
  //if($filtroMotivo==""){
  if($mostrarPagoProveedoresDesdePagosPendientes==1 and $filtroEmpleado==""){
    //obtenemos los pagos a proveedores que se hicieron desde esta caja para el saldo anterior
    $sql4 = " SELECT SUM(deuda_proveedor) AS total_pago_proveedores FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id INNER JOIN almacenes a ON vd.id_almacen=a.id INNER JOIN forma_pago fp ON vd.id_forma_pago=fp.id WHERE pagado=1 AND caja_egreso='Chica' $whereVentas $filtroHastaSaldoAnteriorPagoProv";
    $q4 = $pdo->prepare($sql4);
    //echo $sql4;
    $q4->execute(array());
    $data4 = $q4->fetch(PDO::FETCH_ASSOC);
    $total_pago_proveedores=0;
    if($data4){
      $total_pago_proveedores=$data4["total_pago_proveedores"];
    }

    //obtenemos los pagos a proveedores que se hicieron desde esta caja para el saldo anterior
    $sql4 = " SELECT SUM(deuda_proveedor) AS total_pago_proveedores_canjes FROM canjes_detalle cd INNER JOIN canjes c ON cd.id_canje=c.id INNER JOIN almacenes a ON cd.id_almacen=a.id INNER JOIN forma_pago fp ON cd.id_forma_pago=fp.id WHERE pagado=1 AND caja_egreso='Chica' $whereCanjes $filtroHastaSaldoAnteriorPagoProv";
    $q4 = $pdo->prepare($sql4);
    //echo $sql4;
    $q4->execute(array());
    $data4 = $q4->fetch(PDO::FETCH_ASSOC);
    $total_pago_proveedores_canjes=0;
    if($data4){
      $total_pago_proveedores_canjes=$data4["total_pago_proveedores_canjes"];
    }
    
    $total_pago_proveedores+=$total_pago_proveedores_canjes;
  }

  $saldo_anterior=$ingresos_externos+$total_facturas_recibos-$total_notas_credito-$egresos_caja_chica-$total_pago_proveedores;

  $aCaja[]=[
    "id_venta"=>0,
    "id"=>"Saldo anterior",
    "fecha_hora"=>date("d-m-Y H:i",strtotime($desde)),
    "motivo"=>"",
    "detalle"=>"",
    "forma_pago"=>"",
    "credito"=>0,
    "debito"=>0,
    "saldo"=>$saldo_anterior,
    "detalle_productos"=>"",
  ];

  $modo_debug=0;
  //PARA PODER DEBUGUEAR MOSTRAMOS VARIABLES EN LA COLUMNA DETALLE
  if($modo_debug==1){
    $detalle="total_ventas: $total_ventas<br>ingresos_externos: $ingresos_externos<br>egresos_caja_chica: $egresos_caja_chica<br>total_pago_proveedores: $total_pago_proveedores<br>";
    $detalle.="data[total_ventas]: $data[total_ventas]<br>data2[ingresos_externos]: $data2[ingresos_externos]<br>data3[egresos_caja_chica]: $data3[egresos_caja_chica]<br>data4[total_pago_proveedores]: $data4[total_pago_proveedores]<br>";
    $aCaja[]=[
      "id_venta"=>0,
      "id"=>"",
      "fecha_hora"=>date("d-m-Y H:i",strtotime($desde)),
      "motivo"=>"",
      "detalle"=>$detalle,
      "forma_pago"=>"",
      "credito"=>0,
      "debito"=>0,
      "saldo"=>0,
      "detalle_productos"=>"",
    ];
  }

  //FIN SALDO ANTERIOR

  //INICIO OBTENCION DE REGISTROS A MOSTRAR EN LA TABLA

  if($filtroMotivo=="" and $filtroEmpleado==""){
    //obtenemos las ventas
    $sql = " SELECT v.id AS id_venta, a.almacen, date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted,v.fecha_hora, fp.forma_pago,(SELECT GROUP_CONCAT('+',vd.cantidad,' ',p.descripcion,': $',FORMAT(vd.subtotal,2,'de_DE') SEPARATOR '<br>') FROM ventas_detalle vd inner join productos p on p.id = vd.id_producto WHERE vd.id_venta=v.id) AS detalle_productos,(SELECT COUNT(vd.id) FROM ventas_detalle vd WHERE vd.id_venta=v.id) AS cant_productos,v.total_con_descuento,v.id_cierre_caja,tipo_comprobante FROM ventas v inner join almacenes a on a.id = v.id_almacen inner join forma_pago fp on fp.id = v.id_forma_pago WHERE 1 $whereVentas $filtroDesde $filtroHasta";
    //echo $sql;
    foreach ($pdo->query($sql) as $row) {
      $cerrado="<i class='fa fa-lock' aria-hidden='true'></i> ";
      if($row["id_cierre_caja"]==0){
        $cerrado="<i class='fa fa-unlock' aria-hidden='true'></i> ";
      }
      $credito=$row["total_con_descuento"];
      $debito=0;
      $tipo_comprobante="Venta";
      if(in_array($row["tipo_comprobante"],["NCA","NCB"])){
        $credito=0;
        $debito=$row["total_con_descuento"];
        $tipo_comprobante="Nota de Crédito";
      }
      $aCaja[]=[
        "id_venta"=>$row["id_venta"],
        "id"=>$cerrado."<span data-id='".$row["id_venta"]."' data-tipo='venta' class='ver badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></span> V#".$row["id_venta"]."",
        "fecha_hora"=>date("d-m-Y H:i",strtotime($row["fecha_hora"])),
        //"detalle"=>$cerrado."<a href='verVenta.php?id=".$row["id_venta"]."' target='_blank' class='badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></a> ".$tipo_comprobante." ID ".$row["id_venta"]."",
        "motivo"=>$tipo_comprobante,
        "detalle"=>$row["cant_productos"]." producto/os",
        "forma_pago"=>$row["forma_pago"],
        "credito"=>$credito,
        "debito"=>$debito,
        "saldo"=>0,
        "detalle_productos"=>$row["detalle_productos"],
      ];
    }

  }

  if($mostrarPagoProveedoresDesdePagosPendientes==1 and $filtroEmpleado==""){
    //obtenemos las pagos a proveedoras
    $sql = " SELECT p.id_proveedor,fecha_hora_pago,CONCAT(apellido,' ',nombre) AS proveedor,SUM(deuda_proveedor) AS suma_deuda_proveedor,GROUP_CONCAT('+',vd.cantidad,' ',p.descripcion,': $',FORMAT(vd.deuda_proveedor,2,'de_DE') SEPARATOR '<br>') AS detalle_productos,fp.forma_pago FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id INNER JOIN productos p ON vd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id INNER JOIN almacenes a ON vd.id_almacen=a.id INNER JOIN forma_pago fp ON vd.id_forma_pago=fp.id WHERE pagado=1 AND caja_egreso='Chica' $whereVentas $filtroDesdePagoProv $filtroHastaPagoProv GROUP BY p.id_proveedor, DATE(vd.fecha_hora_pago)";
    //echo $sql;
    foreach ($pdo->query($sql) as $row) {
      $aCaja[]=[
        "id_proveedor"=>$row["id_proveedor"],
        "id"=>$row["id_proveedor"],
        "fecha_hora"=>date("d-m-Y H:i",strtotime($row["fecha_hora_pago"])),
        //"detalle"=>"Pago a proveedores: ".$row["proveedor"]."",
        "motivo"=>"Pago a proveedores",
        "detalle"=>$row["proveedor"],
        //"forma_pago"=>"Efectivo",
        "forma_pago"=>$row["forma_pago"],
        "credito"=>0,
        "debito"=>$row["suma_deuda_proveedor"],
        "saldo"=>0,
        "detalle_productos"=>$row["detalle_productos"],
      ];
    }

    //obtenemos las pagos a proveedoras de productos vendidos por medio de canjes
    $sql = " SELECT p.id_proveedor,fecha_hora_pago,CONCAT(apellido,' ',nombre) AS proveedor,SUM(deuda_proveedor) AS suma_deuda_proveedor,GROUP_CONCAT('+',cd.cantidad,' ',p.descripcion,': $',FORMAT(cd.deuda_proveedor,2,'de_DE') SEPARATOR '<br>') AS detalle_productos,fp.forma_pago FROM canjes_detalle cd INNER JOIN canjes c ON cd.id_canje=c.id INNER JOIN productos p ON cd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id INNER JOIN almacenes a ON cd.id_almacen=a.id INNER JOIN forma_pago fp ON cd.id_forma_pago=fp.id WHERE pagado=1 AND caja_egreso='Chica' $whereCanjes $filtroDesdePagoProv $filtroHastaPagoProv GROUP BY p.id_proveedor, DATE(cd.fecha_hora_pago)";
    //echo $sql;
    foreach ($pdo->query($sql) as $row) {
      $fecha_hora_pago=date("d-m-Y H:i",strtotime($row["fecha_hora_pago"]));
      $fecha_pago=date("d-m-Y",strtotime($fecha_hora_pago));
      $motivo="Pago a proveedores";
      $detalle=$row["proveedor"];
      $indice = null;
      //buscamos el indice del array en caso de que ya haya un pago a proveedor realizado con la misma forma de pago y en la misma fecha
      foreach ($aCaja as $key => $value) {
        if ($detalle==$value["detalle"] and $motivo==$value["motivo"] and $fecha_pago==date("d-m-Y",strtotime($value["fecha_hora"])) and $row["forma_pago"]==$value["forma_pago"]) {
          $indice = $key;
          break;
        }
      }

      if($indice){
        //si se encuentra el indice sumamos la deuda del proveedor y concatenamos el detalle del producto
        $aCaja[$indice]["debito"]+=$row["suma_deuda_proveedor"];
        $aCaja[$indice]["detalle_productos"].="<br>".$row["detalle_productos"];
      }else{
        //si no hay un indice agregamos todos los datos al array
        $aCaja[]=[
          "id_proveedor"=>$row["id_proveedor"],
          "id"=>$row["id_proveedor"],
          "fecha_hora"=>$fecha_hora_pago,
          //"detalle"=>"Pago a proveedores: ".$row["proveedor"]."",
          "motivo"=>$motivo,
          "detalle"=>$detalle,
          //"forma_pago"=>"Efectivo",
          "forma_pago"=>$row["forma_pago"],
          "credito"=>0,
          "debito"=>$row["suma_deuda_proveedor"],
          "saldo"=>0,
          "detalle_productos"=>$row["detalle_productos"],
        ];
      }
    }
  }

  //obtenemos los movimientos de la caja chica y segun si son ingresos o egresos los mostramos en crédio o débito
  $sql = " SELECT mc.id AS id_movimiento, a.almacen, date_format(mc.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted,mc.fecha_hora, fp.forma_pago,mc.monto AS total,msc.motivo,mc.detalle,mc.id_cierre_caja,mc.tipo_movimiento FROM movimientos_caja mc inner join almacenes a on a.id = mc.id_almacen_egreso inner join forma_pago fp on fp.id = mc.id_forma_pago INNER JOIN motivos_salidas_caja msc ON mc.id_motivo=msc.id LEFT JOIN empleados e ON mc.id_empleado=e.id WHERE anulado=0 AND tipo_caja='Chica' $where $filtroDesde $filtroHasta $filtroMotivo $filtroEmpleado";
  //echo $sql;
  foreach ($pdo->query($sql) as $row) {
    
    //$iconVer="<a href='verMovimientoCajaChica.php?id=".$row["id_movimiento"]."' target='_blank' class='badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></a>";
    $iconVer="<span data-id='".$row["id_movimiento"]."' data-tipo='movimiento' class='ver badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></span>";

    $iconEdit="";
    $cerrado="<i class='fa fa-lock' aria-hidden='true'></i> ";
    if($row["id_cierre_caja"]==0){
      $iconEdit="<a href='modificarMovimientoCajaChica.php?id=".$row["id_movimiento"]."' target='_blank' class='badge badge-secondary'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
      $cerrado="<i class='fa fa-unlock' aria-hidden='true'></i> ";
    }

    if($row["tipo_movimiento"]=="Ingreso"){
      $credito=$row["total"];
      $debito=0;
      $saldo=0;
    }else{
      $credito=0;
      $debito=$row["total"];
      $saldo=0;
    }
    $aCaja[]=[
      "id_movimiento"=>$row["id_movimiento"],
      "id"=>$cerrado.$iconVer.$iconEdit." M#".$row["id_movimiento"]."",
      "fecha_hora"=>date("d-m-Y H:i",strtotime($row["fecha_hora"])),
      //"detalle"=>$cerrado.$iconVer.$iconEdit." Mov. ID ".$row["id_movimiento"]."",
      "motivo"=>$row["motivo"],
      "detalle"=>$row["detalle"],
      "forma_pago"=>$row["forma_pago"],
      "credito"=>$credito,
      "debito"=>$debito,
      "saldo"=>$saldo,
      "detalle_productos"=>$row["motivo"].": ".$row["detalle"],
    ];
  }
  Database::disconnect();

  function date_compare($a, $b){
      $t1 = strtotime($a['fecha_hora']);
      $t2 = strtotime($b['fecha_hora']);
      return $t1 - $t2;
  }

  usort($aCaja, 'date_compare');

  foreach ($aCaja as $key => $value) {
    $aCaja[$key]['fecha_hora']=date("d/m/Y H:i",strtotime($value['fecha_hora']));
  }
}

echo json_encode($aCaja);
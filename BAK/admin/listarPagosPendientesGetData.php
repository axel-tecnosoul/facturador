<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}
include 'database.php';
$pdo = Database::connect();

$desde=$_GET["desde"];
$filtroDesde="";
if($desde!=""){
  $filtroDesde="AND DATE(fecha_hora)>='$desde'";
}
$hasta=$_GET["hasta"];
$filtroHasta="";
if($hasta!=""){
  $filtroHasta="AND DATE(fecha_hora)<='$hasta'";
}
$forma_pago=$_GET["forma_pago"];
$filtroFormaPago="";
if($forma_pago!=0){
  $filtroFormaPago="AND id_forma_pago IN ($forma_pago)";
}
$id_almacen=$_GET["id_almacen"];
$filtroAlmacen="";
if($id_almacen!=0){
  $filtroAlmacen="AND id_almacen IN ($id_almacen)";
}
//$tipo_comprobante=$_GET["tipo_comprobante"];

$aCaja=[];
if($desde<=$hasta and $id_almacen!=0){

  $where=" $filtroFormaPago $filtroAlmacen";

  //obtenemos los ingresos registrados por los usuarios para el saldo anterior
  //$sql = "SELECT SUM(mcg.monto) AS ingresos_externos FROM movimientos_caja_grande mcg inner join almacenes a on a.id = mcg.id_almacen inner join forma_pago fp on fp.id = mcg.id_forma_pago WHERE tipo_movimiento='Ingreso' $where AND DATE(fecha_hora)<'$desde'";
  $sql = "SELECT SUM(mc.monto) AS ingresos_externos FROM movimientos_caja mc inner join almacenes a on a.id = mc.id_almacen inner join forma_pago fp on fp.id = mc.id_forma_pago WHERE tipo_caja='Grande' AND  tipo_movimiento='Ingreso' $where AND DATE(fecha_hora)<'$desde'";
  $q = $pdo->prepare($sql);
  //echo $sql;
  $q->execute(array());
  $data = $q->fetch(PDO::FETCH_ASSOC);
  $ingresos_externos=$data["ingresos_externos"];
  if(is_null($ingresos_externos)){
    $ingresos_externos=0;
  }

  //obtenemos las salidas de caja chica a caja grande para el saldo anterior
  //$sql2 = "SELECT SUM(monto) AS ingresos_caja_chica FROM egresos_caja_chica inner join forma_pago fp on fp.id = id_forma_pago WHERE id_motivo=1 $where AND DATE(fecha_hora)<'$desde'";
  $sql2 = "SELECT SUM(mc.monto) AS ingresos_caja_chica FROM movimientos_caja mc inner join almacenes a on a.id = mc.id_almacen inner join forma_pago fp on fp.id = mc.id_forma_pago WHERE tipo_caja='Chica' AND tipo_movimiento='Egreso' AND id_motivo=1 $where AND DATE(fecha_hora)<'$desde'";
  $q2 = $pdo->prepare($sql2);
  //echo $sql2;
  $q2->execute(array());
  $data2 = $q2->fetch(PDO::FETCH_ASSOC);
  $ingresos_caja_chica=$data2["ingresos_caja_chica"];
  if(is_null($ingresos_caja_chica)){
    $ingresos_caja_chica=0;
  }

  //obtenemos los egresos de caja grande para el saldo anterior
  //$sql3 = "SELECT SUM(mcg.monto) AS egresos_caja_grande FROM movimientos_caja_grande mcg inner join almacenes a on a.id = mcg.id_almacen inner join forma_pago fp on fp.id = mcg.id_forma_pago WHERE tipo_movimiento='Egreso' $where AND DATE(fecha_hora)<'$desde'";
  $sql3 = "SELECT SUM(mc.monto) AS egresos_caja_grande FROM movimientos_caja mc inner join almacenes a on a.id = mc.id_almacen inner join forma_pago fp on fp.id = mc.id_forma_pago WHERE tipo_caja='Grande' AND tipo_movimiento='Egreso' $where AND DATE(fecha_hora)<'$desde'";
  $q3 = $pdo->prepare($sql3);
  //echo $sql;
  $q3->execute(array());
  $data3 = $q3->fetch(PDO::FETCH_ASSOC);
  $egresos_caja_grande=$data3["egresos_caja_grande"];
  if(is_null($egresos_caja_grande)){
    $egresos_caja_grande=0;
  }

  $saldo_anterior=0;
  //$ingresos_caja_chica=0;

  $saldo_anterior+=$ingresos_externos+$ingresos_caja_chica-$egresos_caja_grande;

  $modo_debug=0;
  if($modo_debug==1){
    $detalle="ingresos_externos: $ingresos_externos<br>ingresos_caja_chica: $egresos_caja_grande<br>egresos_caja_grande: $ingresos_caja_chica<br>";
    $detalle.="data[ingresos_externos]: $data[ingresos_externos]<br>data2[ingresos_caja_chica]: $data2[ingresos_caja_chica]<br>data3[egresos_caja_grande]: $data3[egresos_caja_grande]<br>";
    $aCaja[]=[
      "id_venta"=>0,
      "fecha_hora"=>date("d-m-Y H:i",strtotime($desde)),
      "detalle"=>$detalle,
      "forma_pago"=>"",
      "credito"=>0,
      "debito"=>0,
      "saldo"=>0,
      "detalle_productos"=>"",
    ];
  }

  //echo $sql;
  $aCaja[]=[
    "id_venta"=>0,
    "fecha_hora"=>date("d-m-Y H:i",strtotime($desde)),
    "detalle"=>"Saldo anterior",
    "forma_pago"=>"",
    "credito"=>0,
    "debito"=>0,
    "saldo"=>$saldo_anterior,
    "detalle_productos"=>"",
  ];

  //obtenemos los egresos de caja chica a caja grande
  //$sql = " SELECT ech.id AS id_egreso, a.almacen, date_format(ech.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted,ech.fecha_hora, fp.forma_pago,ech.monto AS total,msc.motivo,ech.detalle,ech.id_cierre_caja FROM egresos_caja_chica ech inner join almacenes a on a.id = ech.id_almacen inner join forma_pago fp on fp.id = ech.id_forma_pago INNER JOIN motivos_salidas_caja msc ON ech.id_motivo=msc.id WHERE id_motivo=1 $where $filtroDesde $filtroHasta";
  $sql = " SELECT mc.id AS id_egreso, a.almacen, date_format(mc.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted,mc.fecha_hora, fp.forma_pago,mc.monto AS total,msc.motivo,mc.detalle,mc.id_cierre_caja,mc.tipo_movimiento FROM movimientos_caja mc inner join almacenes a on a.id = mc.id_almacen inner join forma_pago fp on fp.id = mc.id_forma_pago INNER JOIN motivos_salidas_caja msc ON mc.id_motivo=msc.id WHERE tipo_caja='Chica' AND tipo_movimiento='Egreso' AND id_motivo=1 $where $filtroDesde $filtroHasta";
  foreach ($pdo->query($sql) as $row) {
    $iconVer="<a href='verMovimientoCajaChica.php?id=".$row["id_egreso"]."' target='_blank' class='badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></a>";
    $iconEdit="";

    $cerrado="<i class='fa fa-lock' aria-hidden='true'></i> ";
    if($row["id_cierre_caja"]==0){
      $iconEdit="<a href='modificarMovimientoCajaChica.php?id=".$row["id_egreso"]."' target='_blank' class='badge badge-secondary'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
      $cerrado="<i class='fa fa-unlock' aria-hidden='true'></i> ";
    }
    
    $aCaja[]=[
      "id_egreso"=>$row["id_egreso"],
      "fecha_hora"=>date("d-m-Y H:i",strtotime($row["fecha_hora"])),
      "detalle"=>$cerrado.$iconVer.$iconEdit." Ingreso ID ".$row["id_egreso"],
      "forma_pago"=>$row["forma_pago"],
      "credito"=>$row["total"],
      "debito"=>0,
      "saldo"=>0,
      "detalle_productos"=>$row["motivo"].": ".$row["detalle"],
    ];
  }

  //obtenemos los movimientos de caja grande registrados por los usuarios
  //$sql = " SELECT mcg.id AS id_movimiento, a.almacen, date_format(mcg.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted,mcg.fecha_hora, fp.forma_pago,mcg.monto AS total,msc.motivo,mcg.detalle,mcg.id_cierre_caja,mcg.tipo_movimiento FROM movimientos_caja_grande mcg inner join almacenes a on a.id = mcg.id_almacen inner join forma_pago fp on fp.id = mcg.id_forma_pago INNER JOIN motivos_salidas_caja msc ON mcg.id_motivo=msc.id WHERE 1 $where $filtroDesde $filtroHasta";
  $sql = " SELECT mc.id AS id_movimiento, a.almacen, date_format(mc.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted,mc.fecha_hora, fp.forma_pago,mc.monto AS total,msc.motivo,mc.detalle,mc.id_cierre_caja,mc.tipo_movimiento FROM movimientos_caja mc inner join almacenes a on a.id = mc.id_almacen inner join forma_pago fp on fp.id = mc.id_forma_pago INNER JOIN motivos_salidas_caja msc ON mc.id_motivo=msc.id WHERE tipo_caja='Grande' $where $filtroDesde $filtroHasta";
  //echo $sql;
  foreach ($pdo->query($sql) as $row) {
    
    $iconVer="<a href='verMovimientoCajaGrande.php?id=".$row["id_movimiento"]."' target='_blank' class='badge badge-primary'><i class='fa fa-eye' aria-hidden='true'></i></a>";
    $iconEdit="";
    $cerrado="<i class='fa fa-lock' aria-hidden='true'></i> ";
    if($row["id_cierre_caja"]==0){
      $iconEdit="<a href='modificarMovimientoCajaGrande.php?id=".$row["id_movimiento"]."' target='_blank' class='badge badge-secondary'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
      $cerrado="<i class='fa fa-unlock' aria-hidden='true'></i> ";
    }

    if($row["tipo_movimiento"]=="Ingreso"){
      $aCaja[]=[
        "id_movimiento"=>$row["id_movimiento"],
        "fecha_hora"=>date("d-m-Y H:i",strtotime($row["fecha_hora"])),
        "detalle"=>$cerrado.$iconVer.$iconEdit." Mov. ID ".$row["id_movimiento"]."",
        "forma_pago"=>$row["forma_pago"],
        "credito"=>$row["total"],
        "debito"=>0,
        "saldo"=>0,
        "detalle_productos"=>$row["motivo"].": ".$row["detalle"],
      ];
    }else{
      $aCaja[]=[
        "id_movimiento"=>$row["id_movimiento"],
        "fecha_hora"=>date("d-m-Y H:i",strtotime($row["fecha_hora"])),
        "detalle"=>$cerrado.$iconVer.$iconEdit." Mov. ID ".$row["id_movimiento"]."",
        "forma_pago"=>$row["forma_pago"],
        "credito"=>0,
        "debito"=>$row["total"],
        "saldo"=>0,
        "detalle_productos"=>$row["motivo"].": ".$row["detalle"],
      ];
    }
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
<?php
require("config.php");
require 'database.php';
require 'funciones.php';

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$modoDebug=0;
$pdo->beginTransaction();

//3,9,51,73,74,83,84,92,94,102

//VENTAS
//$sql = "SELECT vd.id AS id_venta_detalle,vd.deuda_proveedor,p.id_proveedor FROM ventas v INNER JOIN ventas_detalle vd ON vd.id_venta=v.id INNER JOIN productos p ON vd.id_producto=p.id LEFT JOIN devoluciones_detalle de ON de.id_venta_detalle=vd.id WHERE vd.id_modalidad=50 AND vd.pagado=0 AND v.anulada=0 AND v.id_venta_cbte_relacionado IS NULL AND de.id_devolucion IS NULL and v.fecha_hora<DATE_SUB(NOW(), INTERVAL 1 MONTH)";//WHERE p.id_proveedor=667;
$sql = "SELECT vd.id AS id_venta_detalle,vd.deuda_proveedor,p.id_proveedor,v.tipo_comprobante FROM ventas v INNER JOIN ventas_detalle vd ON vd.id_venta=v.id INNER JOIN productos p ON vd.id_producto=p.id LEFT JOIN devoluciones_detalle de ON de.id_venta_detalle=vd.id WHERE vd.id_modalidad=50 AND vd.pagado=0 AND v.anulada=0 AND de.id_devolucion IS NULL and v.fecha_hora<DATE_SUB(NOW(), INTERVAL 1 MONTH)";//WHERE p.id_proveedor=667; AND v.id_venta_cbte_relacionado IS NULL
$q = $pdo->prepare($sql);
$q->execute();
$ok=$ok2=0;
foreach($q->fetchAll(PDO::FETCH_ASSOC) as $row){
  $ok2++;

  $operacion="+";
  if($row["tipo_comprobante"]=="NCB"){
    $operacion="-";
  }

  $sql = "UPDATE proveedores set credito = credito $operacion ? where id = ?";
  $q2 = $pdo->prepare($sql);
  $q2->execute(array($row["deuda_proveedor"],$row["id_proveedor"]));
  $afe=$q2->rowCount();

  if ($modoDebug==1) {
    $q2->debugDumpParams();
    echo "<br><br>Afe: ".$afe;
    echo "<br><br>";
  }

  if($afe==1){
    $sql = "UPDATE ventas_detalle set pagado = 1, fecha_hora_pago = NOW() where id = ?";
    $q2 = $pdo->prepare($sql);
    $q2->execute(array($row["id_venta_detalle"]));
    $afe=$q2->rowCount();

    if($afe==1){
      $ok++;
    }

    if ($modoDebug==1) {
      $q2->debugDumpParams();
      echo "<br><br>Afe: ".$q2->rowCount();
      echo "<br><br>";
    }
  }
}

//CANJES
$sql = "SELECT cd.id AS id_canje_detalle,cd.deuda_proveedor,p.id_proveedor FROM canjes c INNER JOIN canjes_detalle cd ON cd.id_canje=c.id INNER JOIN productos p ON cd.id_producto=p.id LEFT JOIN devoluciones_detalle de ON de.id_canje_detalle=cd.id WHERE cd.id_modalidad=50 AND cd.pagado=0 AND c.anulado=0 AND de.id_devolucion IS NULL AND c.fecha_hora<DATE_SUB(NOW(), INTERVAL 1 MONTH)";//WHERE p.id_proveedor=667;
$q = $pdo->prepare($sql);
$q->execute();
//$ok=$ok2=0;
foreach($q->fetchAll(PDO::FETCH_ASSOC) as $row){
  $ok2++;
  $sql = "UPDATE proveedores set credito = credito + ? where id = ?";
  $q2 = $pdo->prepare($sql);
  $q2->execute(array($row["deuda_proveedor"],$row["id_proveedor"]));
  $afe=$q2->rowCount();

  if ($modoDebug==1) {
    $q2->debugDumpParams();
    echo "<br><br>Afe: ".$afe;
    echo "<br><br>";
  }

  if($afe==1){
    $sql = "UPDATE canjes_detalle set pagado = 1, fecha_hora_pago = NOW() where id = ?";
    $q2 = $pdo->prepare($sql);
    $q2->execute(array($row["id_canje_detalle"]));
    $afe=$q2->rowCount();

    if($afe==1){
      $ok++;
    }

    if ($modoDebug==1) {
      $q2->debugDumpParams();
      echo "<br><br>Afe: ".$q2->rowCount();
      echo "<br><br>";
    }
  }
}

//$pdo->rollBack();
if ($modoDebug==1) {
  //$pdo->rollBack();
  echo "ok==ok2<br>";
  echo "$ok==$ok2<br>";
}
if($ok==$ok2){
  if ($modoDebug==1) {
    $pdo->rollBack();
    echo "Todo bien. Hacemos COMMIT";
  }else{
    $pdo->commit();
    //echo "Hacemos COMMIT";
  }
}else{
  $pdo->rollBack();
  if ($modoDebug==1) {
    echo "Hacemos rollback";
  }
}

$sql = "UPDATE parametros set valor = valor + 1 where id = 7";
$q2 = $pdo->prepare($sql);
$q2->execute();

$pdo = Database::disconnect();
?>
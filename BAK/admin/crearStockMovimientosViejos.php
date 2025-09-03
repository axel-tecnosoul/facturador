<?php
require("config.php");
require 'database.php';

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$modoDebug=0;

if($modoDebug==1){
  $pdo->beginTransaction();
}

//$sql = "SELECT DATE(fecha_hora) AS fecha_hora,id_usuario,count(*) AS cant,GROUP_CONCAT(id SEPARATOR ',') AS id_stock_movimiento_detalle FROM stock_movimientos_detalle GROUP BY DATE(fecha_hora),id_usuario";
$sql = "SELECT fecha_hora,id_usuario,count(*) AS cant,id_almacen_origen,id_almacen_destino,GROUP_CONCAT(id SEPARATOR ',') AS id_stock_movimiento_detalle FROM stock_movimientos_detalle GROUP BY DATE(fecha_hora),id_usuario,id_almacen_origen,id_almacen_destino";
echo $sql."<br>";
foreach ($pdo->query($sql) as $row) {

  $sql = "INSERT INTO stock_movimientos (fecha_hora,id_almacen_origen, id_almacen_destino, id_usuario) VALUES (?,?,?,?)";
  $q = $pdo->prepare($sql);
  $q->execute([$row['fecha_hora'],$row['id_almacen_origen'],$row['id_almacen_destino'],$row['id_usuario']]);
  
  $id_stock_movimiento = $pdo->lastInsertId();

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";
    echo "id_stock_movimiento: ".$id_stock_movimiento."<br><br>";
  }

  $sql = "UPDATE stock_movimientos_detalle set id_stock_movimiento = ? where id IN ($row[id_stock_movimiento_detalle])";
  $q = $pdo->prepare($sql);
  $q->execute([$id_stock_movimiento]);

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";
  }

  echo $row["fecha_hora"]." - ";
  echo $row["id_usuario"]." - ";
  echo $row["cant"]." - ";
  echo $row["id_stock_movimiento_detalle"]."<br><br>";
  
}

if ($modoDebug==1) {
  $pdo->rollBack();
} else {
  Database::disconnect();
  header("Location: ".$redirect);
}

?>
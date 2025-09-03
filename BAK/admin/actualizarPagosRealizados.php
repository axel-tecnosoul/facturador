<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/*$sql = "UPDATE ventas_detalle vd SET id_almacen=(SELECT id_almacen FROM proveedores pr INNER JOIN productos p ON p.id_proveedor=pr.id WHERE vd.id_producto=p.id) WHERE pagado=1 AND id_modalidad=40 AND id_almacen IS NULL";
$q = $pdo->prepare($sql);
$q->execute();
$q->debugDumpParams();
echo "<br><br>Afe: ".$q->rowCount()."<br><br>";*/

$sql = "UPDATE ventas_detalle vd SET id_forma_pago=1 WHERE pagado=1 AND id_modalidad=40 AND id_forma_pago IS NULL";
$q = $pdo->prepare($sql);
$q->execute();
$q->debugDumpParams();
echo "<br><br>Afe: ".$q->rowCount()."<br><br>";

$sql = "UPDATE ventas_detalle vd SET caja_egreso='Grande' WHERE pagado=1 AND id_modalidad=40 AND caja_egreso IS NULL AND id_almacen=4";//id_almacen 4 -> Villa Ballester
$q = $pdo->prepare($sql);
$q->execute();
$q->debugDumpParams();
echo "<br><br>Afe: ".$q->rowCount()."<br><br>";

$sql = "UPDATE ventas_detalle vd SET caja_egreso='Chica' WHERE pagado=1 AND id_modalidad=40 AND caja_egreso IS NULL AND id_almacen!=4";//id_almacen 4 -> Villa Ballester
$q = $pdo->prepare($sql);
$q->execute();
$q->debugDumpParams();
echo "<br><br>Afe: ".$q->rowCount()."<br><br>";

//die();
  
Database::disconnect();

?>
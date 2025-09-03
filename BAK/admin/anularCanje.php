<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';

$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if ( null==$id ) {
  header("Location: listarCanjes.php");
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
$sql = "UPDATE canjes set anulado = 1 where id = ?";
$q = $pdo->prepare($sql);
$q->execute(array($id));

$sql = " SELECT cd.id_producto, cd.cantidad, cd.subtotal, cd.id_modalidad, p.id_proveedor, v.id_almacen from canjes_detalle cd inner join canjes v on v.id = cd.id_canje inner join productos p on p.id = cd.id_producto where cd.id_canje = ".$id;
foreach ($pdo->query($sql) as $row) {

  $sql2 = "SELECT id FROM stock WHERE id_producto = ? and id_almacen = ?";
  $q2 = $pdo->prepare($sql2);
  $q2->execute(array($row[0],$row[5]));
  $data = $q2->fetch(PDO::FETCH_ASSOC);
  if (!empty($data)) {
    $sql3 = "UPDATE stock set cantidad = cantidad + ? where id = ?";
    $q3 = $pdo->prepare($sql3);
    $q3->execute(array($row[1],$data['id']));
  } else {
    $sql3 = "INSERT INTO stock (id_producto, id_almacen, cantidad, id_modalidad) VALUES (?,?,?,?)";
    $q3 = $pdo->prepare($sql3);
    $q3->execute(array($row[0],$row[5],$row[1],$row[3]));
  }

  if ($row[3] == 50) {
    $credito = $row[2]/2;
    $sql = "UPDATE proveedores set credito = credito - ? where id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($credito,$row[4]));
    
    /*$sql2 = "SELECT `id` FROM `stock` WHERE id_producto = ? and id_almacen = ?";
    $q2 = $pdo->prepare($sql2);
    $q2->execute(array($row[0],$row[5]));
    $data = $q2->fetch(PDO::FETCH_ASSOC);
    if (!empty($data)) {
      $sql3 = "UPDATE `stock` set cantidad = cantidad + ? where id = ?";
      $q3 = $pdo->prepare($sql3);
      $q3->execute(array($row[1],$data['id']));
    } else {
      $sql3 = "INSERT INTO `stock`(`id_producto`, `id_almacen`, `cantidad`, `id_modalidad`) VALUES (?,?,?,?)";
      $q3 = $pdo->prepare($sql3);
      $q3->execute(array($row[0],$row[5],$row[1],$row[3]));
    }*/
  }

}

$sql2 = "SELECT id_proveedor,total FROM canjes WHERE id = ?";
$q2 = $pdo->prepare($sql2);
$q2->execute(array($id));
$data = $q2->fetch(PDO::FETCH_ASSOC);
if (!empty($data)) {
  $sql = "UPDATE proveedores set credito = credito + ? where id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($data["total"],$data["id_proveedor"]));
}

Database::disconnect();
  
header("Location: listarCanjes.php");?>
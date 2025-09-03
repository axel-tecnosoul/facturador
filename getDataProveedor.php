<?php
include 'database.php';
$aProductos=[];

$data=[];
if(!empty($_POST["id_proveedor"])) {
  $pdo = Database::connect();

  $sql = " SELECT id_almacen,id_modalidad FROM proveedores WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST["id_proveedor"]));
  $data = $q->fetch(PDO::FETCH_ASSOC);
      
  Database::disconnect();
}
echo json_encode($data);
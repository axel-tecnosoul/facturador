<?php
require("config.php");
require 'database.php';
require 'funciones.php';

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT cd.id, cd.subtotal, cd.id_modalidad, cd.deuda_proveedor, p.id_proveedor FROM canjes_detalle cd LEFT JOIN productos p ON cd.id_producto=p.id LEFT JOIN canjes c ON cd.id_canje=c.id";
$q = $pdo->prepare($sql);
$q->execute();
$canje_detalle = $q->fetchAll(PDO::FETCH_ASSOC);
foreach ($canje_detalle as $data){
    $forma_pago = 1;
    $modalidad = $data['id_modalidad'];
    $subtotal = $data['subtotal'];
    $idProveedor = $data['id_proveedor'];
    $deuda_proveedor=calcularDeudaProveedor($forma_pago,$modalidad,$subtotal);

    $id = $data['id'];
    $sql = "UPDATE canjes_detalle set deuda_proveedor = ? WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($deuda_proveedor, $id));

    if($modalidad==50){

      $sql = "UPDATE canjes_detalle set pagado = 1 WHERE id = ?";
      $q = $pdo->prepare($sql);
      $q->execute(array($id));

      $sql = "UPDATE proveedores set credito = credito + ? where id = ?";
      $q = $pdo->prepare($sql);
      $q->execute(array($deuda_proveedor,$idProveedor));
    }
}
$pdo = Database::disconnect();
?>
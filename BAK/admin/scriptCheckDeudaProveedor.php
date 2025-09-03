<?php
require("config.php");
require 'database.php';
require 'funciones.php';

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT v.id_forma_pago,d.porcentaje,vd.precio,vd.subtotal,vd.id_modalidad,vd.deuda_proveedor,vd.pagado,p.id_proveedor FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id LEFT JOIN productos p ON vd.id_producto=p.id LEFT JOIN descuentos d ON v.id_descuento_aplicado=d.id";//WHERE p.id_proveedor=667;
$q = $pdo->prepare($sql);
$q->execute();
$canje_detalle = $q->fetchAll(PDO::FETCH_ASSOC);
echo $q->rowCount();
foreach ($canje_detalle as $data){
  
  $forma_pago = $data['id_forma_pago'];
  $modalidad = $data['id_modalidad'];
  $subtotal = $data['subtotal'];
  $idProveedor = $data['id_proveedor'];
  $deuda_proveedor = calcularDeudaProveedor($forma_pago,$modalidad,$subtotal);
  $deuda_proveedor_viejo = calcularDeudaProveedorViejo($forma_pago,$modalidad,$subtotal);

  if(!$idProveedor){
    var_dump($data);
  }

  //$data["deuda_proveedor"]=(float) $data["deuda_proveedor"];

  //echo $data["deuda_proveedor"]." - ".$deuda_proveedor." - ".$deuda_proveedor_viejo."<br>";
  if($deuda_proveedor==$data["deuda_proveedor"]){
    //la deuda del proveedor se calculo con el 80%
    //var_dump("deuda proveedor es igual");
  }else{
    if($deuda_proveedor_viejo==$data["deuda_proveedor"]){
      //la deuda del proveedor se calculo con el 85%
      //var_dump("deuda proveedor viejo es igual");
    }else{
      var_dump($deuda_proveedor);
      var_dump($data["deuda_proveedor"]);
      var_dump($deuda_proveedor_viejo);
      var_dump($data);
      var_dump($deuda_proveedor==$data["deuda_proveedor"]);
    }
  }
}
$pdo = Database::disconnect();
?>
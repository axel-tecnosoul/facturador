<?php
include 'database.php';
$data = [];
if(!empty($_GET['id_cliente']) && !empty($_GET['fecha_factura'])) {
  $pdo = Database::connect();
  $sql = "SELECT precio_base, fecha_base FROM clientes WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($_GET['id_cliente']));
  $cliente = $q->fetch(PDO::FETCH_ASSOC);
  if($cliente){
    $precio_base = (float)$cliente['precio_base'];
    $fecha_base = $cliente['fecha_base'];
    $factor = 1;
    $fecha_inicio = new DateTime($fecha_base);
    $fecha_fin = new DateTime($_GET['fecha_factura']);
    if($fecha_fin > $fecha_inicio){
      $fecha_inicio->modify('first day of next month');
      while($fecha_inicio <= $fecha_fin){
        $periodo = $fecha_inicio->format('Y-m-01');
        $q2 = $pdo->prepare("SELECT porcentaje FROM ipc_historial WHERE periodo = ?");
        $q2->execute(array($periodo));
        $ipc = $q2->fetch(PDO::FETCH_ASSOC);
        if($ipc){
          $factor *= (1 + $ipc['porcentaje']/100);
        }
        $fecha_inicio->modify('first day of next month');
      }
    }
    $precio_unitario = round($precio_base * $factor, 2);
    $data = [
      'precio_unitario' => $precio_unitario,
      'precio_base' => $precio_base,
      'fecha_base' => $fecha_base
    ];
  }
  Database::disconnect();
}
echo json_encode($data);

<?php
include 'database.php';
$aCobros=[];

$filtroDesde="";
if(!empty($_GET["desde"]&& $_GET["desde"] != 0)) {
  $filtroDesde=" AND pc.fecha_pago>='".$_GET["desde"]."'";
}
$filtroHasta="";
if(!empty($_GET["hasta"]&& $_GET["hasta"] != 0)) {
  $filtroHasta=" AND pc.fecha_pago<='".$_GET["hasta"]."'";
}
$filtroCliente="";
if(!empty($_GET["id_cliente"]&& $_GET["id_cliente"] != 0)) {
  $filtroCliente=" AND co.id_cliente IN (".$_GET["id_cliente"].")";
}
$filtroColaborador="";
if(!empty($_GET["id_colaborador"]&& $_GET["id_colaborador"] != 0)) {
  $filtroColaborador=" AND pc.id_colaborador IN (".$_GET["id_colaborador"].")";
}

//Ventas
$pdo = Database::connect();
$sql = "SELECT pc.id AS id_pago,c.nombre,co.id AS id_cobro,date_format(pc.fecha_pago,'%d/%m/%Y') AS fecha_pago,date_format(co.fecha_hora_alta,'%d/%m/%Y') AS fecha_hora_alta,cl.razon_social,pc.monto,pc.observaciones FROM pagos_colaboradores pc INNER JOIN colaboradores c ON pc.id_colaborador=c.id INNER JOIN cobros co ON pc.id_cobro=co.id INNER JOIN clientes cl ON co.id_cliente=cl.id WHERE 1 $filtroDesde $filtroHasta $filtroCliente $filtroColaborador";
//echo $sql;
foreach ($pdo->query($sql) as $row) {

  $btnModificar="";
  $btnModificar='<a href="modificarPagoColaborador.php?id='.$row["id_pago"].'"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar" title="Modificar"></a>';
  $btnEliminar="";
  //$btnEliminar='<a href="#" class="btnEliminar" data-id="'.$row["id_pago"].'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar" title="Eliminar"></a>';
  $btnVer="";
  //$btnVer='<a href="verCobro.php?id='.$row["id_pago"].'"><img src="img/eye.png" width="30" border="0" alt="Ver Cobro" title="Ver Operaciones"></a>';
  
  $aCobros[]=[
    "id"=>$row["id_pago"],
    "id_cobro"=>$row["id_cobro"],
    "colaborador"=>$row["nombre"],
    "fecha_pago"=>$row["fecha_pago"],
    "fecha_hora_alta"=>$row["fecha_hora_alta"],
    "razon_social"=>$row["razon_social"],
    "monto"=>$row["monto"],
    "observaciones"=>$row["observaciones"],
    "acciones" => $btnModificar.$btnEliminar.$btnVer
  ];
}

Database::disconnect();
echo json_encode($aCobros);
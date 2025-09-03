<?php
include 'database.php';
$aClientes=[];

/*$filtroAlmacen="";
if(!empty($_GET["id_almacen"]&& $_GET["id_almacen"] != 0)) {
  $filtroAlmacen=" AND a.id=".$_GET["id_almacen"];
}
$filtroModalidad="";
if(!empty($_GET["id_modalidad"]&& $_GET["id_modalidad"] != 0)) {
  $filtroModalidad=" AND m.id IN (".$_GET["id_modalidad"].")";
}*/

//Ventas
$pdo = Database::connect();
$sql = "SELECT id AS id_cliente, razon_social, cuit, direccion, telefono, email FROM clientes WHERE 1";
//echo $sql;
foreach ($pdo->query($sql) as $row) {

  $btnModificar='<a href="modificarCliente.php?id='.$row["id_cliente"].'"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar" title="Modificar"></a>';
  $btnEliminar='<a href="#" class="btnEliminar" data-id="'.$row["id_cliente"].'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar" title="Eliminar"></a>';
  $btnVer='<a href="verCliente.php?id='.$row["id_cliente"].'"><img src="img/eye.png" width="30" border="0" alt="Ver Cliente" title="Ver Operaciones"></a>';
  
  $aClientes[]=[
    "id"=>$row["id_cliente"],
    "razon_social"=>$row["razon_social"],
    "cuit"=>$row["cuit"],
    "direccion"=>$row["direccion"],
    "email"=>$row["email"],
    "telefono"=>$row["telefono"],
    "acciones" => $btnModificar.$btnEliminar.$btnVer
  ];
}

Database::disconnect();
echo json_encode($aClientes);
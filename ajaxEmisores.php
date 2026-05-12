<?php
include 'database.php';
$aEmisores=[];

//Emisores
$pdo = Database::connect();
$sql = "SELECT e.id, e.razon_social, e.cuit, e.activo, 
               DATE_FORMAT(e.fecha_hora_alta, '%d/%m/%Y %H:%i') AS fecha_alta_formatted
        FROM emisores e 
        ORDER BY e.razon_social";

foreach ($pdo->query($sql) as $row) {
  $btnModificar = '<a href="modificarEmisor.php?id='.$row["id"].'"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar" title="Modificar"></a>';
  
  $btnEliminar = '';
  if($row["activo"] == 1) {
    $btnEliminar = '<a href="#" class="btnEliminar" data-id="'.$row["id"].'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Desactivar" title="Desactivar"></a>';
  } else {
    $btnEliminar = '<a href="#" class="btnActivar" data-id="'.$row["id"].'"><img src="img/icon_alta.png" width="24" height="25" border="0" alt="Activar" title="Activar"></a>';
  }
  
  $estado = $row["activo"] == 1 ? 'Activo' : 'Inactivo';
  
  $aEmisores[]=[
    "id"=>$row["id"],
    "razon_social"=>$row["razon_social"],
    "cuit"=>$row["cuit"],
    "activo"=>$estado,
    "fecha_hora_alta"=>$row["fecha_alta_formatted"],
    "acciones" => $btnModificar." ".$btnEliminar
  ];
}

Database::disconnect();
echo json_encode($aEmisores);
?>
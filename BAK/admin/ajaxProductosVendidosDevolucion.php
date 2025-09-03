<?php
include 'database.php';
$aProductos=[];

$filtroDesde="";
if(!empty($_GET["desde"])) {
  $filtroDesde=" AND DATE(fecha_hora)>='".$_GET["desde"]."'";
}
$filtroHasta="";
if(!empty($_GET["hasta"])){
  $filtroHasta=" AND DATE(fecha_hora)<='".$_GET["hasta"]."'";
}
$filtroProveedor="";
if(!empty($_GET["proveedor"] && $_GET["proveedor"] != 0)) {
  $filtroProveedor=" AND pr.id=".$_GET["proveedor"];
}
$filtroAlmacen="";
if(!empty($_GET["id_almacen"]&& $_GET["id_almacen"] != 0)) {
  $filtroAlmacen=" AND a.id=".$_GET["id_almacen"];
}
$filtroCategoria="";
if(!empty($_GET["id_categoria"]&& $_GET["id_categoria"] != 0)) {
  $filtroCategoria=" AND c.id=".$_GET["id_categoria"];
}

$mostrarVentas=0;
if(!empty($_GET["ventas"] && $_GET["ventas"]=="true")) {
  $mostrarVentas=1;
}
$mostrarCanjes=0;
if(!empty($_GET["canjes"] && $_GET["canjes"]=="true")) {
  $mostrarCanjes=1;
}
$mostrarAmbos=0;
if($mostrarCanjes==0 and $mostrarVentas==0){
  $mostrarAmbos=1;
}

$haceUnMes=date("Y-m-d",strtotime(date("Y-m-d")." -1 month"));

$where=" AND DATE(fecha_hora)>='$haceUnMes' AND ((de.id_modalidad!=1 AND pagado=0) OR (de.id_modalidad=1 AND pagado=1)) $filtroDesde $filtroHasta $filtroProveedor $filtroAlmacen $filtroCategoria";

$pdo = Database::connect();

//Ventas
if($mostrarVentas==1 or $mostrarAmbos==1){
  $sql = "SELECT v.id as id_venta,de.id AS id_venta_detalle, v.total_con_descuento, de.pagado, a.almacen, p.codigo, c.categoria, p.descripcion, de.cantidad, de.precio, de.subtotal, m.modalidad, de.pagado, pr.nombre, pr.apellido, de.id_forma_pago, fp.forma_pago, de.id_venta,de.deuda_proveedor,date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted, fecha_hora, caja_egreso, forma_pago FROM ventas_detalle de INNER JOIN ventas v ON de.id_venta=v.id inner join productos p on p.id = de.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = de.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor LEFT join almacenes a on a.id = v.id_almacen LEFT join forma_pago fp on fp.id = de.id_forma_pago WHERE v.anulada = 0 AND v.id_venta_cbte_relacionado IS NULL $where";
  //echo $sql;
  foreach ($pdo->query($sql) as $row) {
    $pagado = "";
    $total_deuda = 0;
    if($row["pagado"] == 1){
      $pagado = "Si";
    }else{
      $pagado = "No";
    }
    if($row["forma_pago"] == NULL || $row["forma_pago"] == ""){
      $forma_pago = "";
    }else{
      $forma_pago = $row["forma_pago"];
    }
    if($row["caja_egreso"] == NULL || $row["caja_egreso"] == ""){
      $caja_egreso = "";
    }else{
      $caja_egreso = $row["caja_egreso"];
    }
    $aProductos[]=[
        "tipo"=>"Venta",
        "id"=>"V#".$row["id_venta"],
        "fecha_hora"=>$row["fecha_hora"],
        "fecha_hora_formatted"=>$row["fecha_hora_formatted"]."hs",
        "descripcion"=>$row["descripcion"],
        "proveedor"=>$row["nombre"]." ".$row["apellido"],
        "caja_egreso"=>$caja_egreso,
        "forma_pago"=>$forma_pago,
        "almacen"=>$row["almacen"],
        "pagado"=>$pagado,
        //"input" => '<a href="verVenta.php?id='. $row["id_venta"]. '"><img src="img/eye.png" width="24" height="15" border="0" alt="Ver Venta" title="Ver Venta"></a>',
        "input" => '<input type="checkbox" class="no-sort customer-selector check-row" data-subtotal="'.$row["subtotal"].'" value="v/'.$row["id_venta_detalle"].'" /><input type="hidden" class="subtotal" value="'.$row["subtotal"].'">',
        "precio"=>number_format($row["precio"],2),
        "subtotal"=>$row["subtotal"],
        "cantidad"=>$row["cantidad"],
        "codigo"=>$row["codigo"],
        "categoria"=>$row["categoria"]
    ];
  }
}

//Canjes
if($mostrarCanjes==1 or $mostrarAmbos==1){
  $sql = "SELECT cj.id AS id_canje, de.id AS id_canje_detalle, cj.total_con_descuento, de.pagado, a.almacen, p.codigo, c.categoria, p.descripcion, de.cantidad, de.precio, de.subtotal, m.modalidad, de.pagado, pr.nombre, pr.apellido, de.id_forma_pago, fp.forma_pago, de.id_canje,de.deuda_proveedor,date_format(cj.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted, fecha_hora,caja_egreso,forma_pago FROM canjes_detalle de INNER JOIN canjes cj ON de.id_canje=cj.id inner join productos p on p.id = de.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = de.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor LEFT join almacenes a on a.id = cj.id_almacen LEFT join forma_pago fp on fp.id = de.id_forma_pago WHERE cj.anulado = 0 $where";
      
  foreach ($pdo->query($sql) as $row) {
    $pagado = "";
    if($row["pagado"] == 1){
      $pagado = "Si";
    }else{
      $pagado = "No";
    }
    if($row["forma_pago"] == NULL || $row["forma_pago"] == ""){
      $forma_pago = "";
    }else{
      $forma_pago = $row["forma_pago"];
    }
    if($row["caja_egreso"] == NULL || $row["caja_egreso"] == ""){
      $caja_egreso = "";
    }else{
      $caja_egreso = $row["caja_egreso"];
    }
    $aProductos[]=[
        "tipo"=>"Canje",
        "id"=>"C#".$row["id_canje"],
        "fecha_hora"=>$row["fecha_hora"],
        "fecha_hora_formatted"=>$row["fecha_hora_formatted"]."hs",
        "descripcion"=>$row["descripcion"],
        "proveedor"=>$row["nombre"]." ".$row["apellido"],
        "caja_egreso"=>$caja_egreso,
        "forma_pago"=>$forma_pago,
        "almacen"=>$row["almacen"],
        "pagado"=>$pagado,
        //"input" => '<a href="verCanje.php?id='. $row["id_canje"]. '"><img src="img/eye.png" width="24" height="15" border="0" alt="Ver Canje" title="Ver Canje"></a>',
        "input" => '<input type="checkbox" class="no-sort customer-selector check-row" data-subtotal="'.$row["subtotal"].'" value="c/'.$row["id_canje_detalle"].'" />',
        "precio"=>number_format($row["precio"],2),
        "subtotal"=>$row["subtotal"],
        "cantidad"=>$row["cantidad"],
        "codigo"=>$row["codigo"],
        "categoria"=>$row["categoria"]
    ];	
  }
}
Database::disconnect();

if (empty($aProductos)) {
  $aProductos = []; // Asignar un array vacío si no hay resultados
}

echo json_encode($aProductos);
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
  $filtroProveedor=" AND pr.id IN (".$_GET["proveedor"].")";
}
$filtroAlmacen="";
if(!empty($_GET["id_almacen"] && $_GET["id_almacen"] != 0)) {
  $filtroAlmacen=" AND a.id=".$_GET["id_almacen"];
}
$filtroCategoria="";
if(!empty($_GET["id_categoria"] && $_GET["id_categoria"] != 0)) {
  $filtroCategoria=" AND c.id IN (".$_GET["id_categoria"].")";
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
$pdo = Database::connect();

//Ventas
if($mostrarVentas==1 or $mostrarAmbos==1){
  
  $sql = "SELECT v.id as id_venta,vd.id AS id_detalle_venta, v.total_con_descuento, vd.pagado, a.almacen, p.codigo, c.categoria, p.descripcion, vd.cantidad, vd.precio, vd.subtotal, m.modalidad, vd.pagado, pr.nombre, pr.apellido, vd.id_forma_pago, fp.forma_pago, vd.id_venta,vd.deuda_proveedor,date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted, fecha_hora, caja_egreso, forma_pago FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id inner join productos p on p.id = vd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = vd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor LEFT join almacenes a on a.id = v.id_almacen LEFT join forma_pago fp on fp.id = vd.id_forma_pago LEFT JOIN devoluciones_detalle de ON de.id_venta_detalle=vd.id WHERE v.anulada = 0 AND v.id_venta_cbte_relacionado IS NULL AND de.id_devolucion IS NULL $filtroDesde $filtroHasta $filtroProveedor $filtroAlmacen $filtroCategoria";
      
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
        "input" => '<a href="verVenta.php?id='. $row["id_venta"]. '"><img src="img/eye.png" width="24" height="15" border="0" alt="Ver Venta" title="Ver Venta"></a>',
        "precio"=>number_format($row["precio"],2),
        "subtotal"=>$row["subtotal"],
        "cantidad"=>$row["cantidad"],
        "codigo"=>$row["codigo"],
        "categoria"=>$row["categoria"]
    ];
  }
}

if($mostrarCanjes==1 or $mostrarAmbos==1){
  //Canjes
  $sql = "SELECT cj.id AS id_canje, cd.id AS id_detalle_canje, cj.total_con_descuento, cd.pagado, a.almacen, p.codigo, c.categoria, p.descripcion, cd.cantidad, cd.precio, cd.subtotal, m.modalidad, cd.pagado, pr.nombre, pr.apellido, cd.id_forma_pago, fp.forma_pago, cd.id_canje,cd.deuda_proveedor,date_format(cj.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora_formatted, fecha_hora,caja_egreso,forma_pago FROM canjes_detalle cd INNER JOIN canjes cj ON cd.id_canje=cj.id inner join productos p on p.id = cd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = cd.id_modalidad inner join proveedores pr on pr.id = p.id_proveedor LEFT join almacenes a on a.id = cj.id_almacen LEFT join forma_pago fp on fp.id = cd.id_forma_pago LEFT JOIN devoluciones_detalle de ON de.id_canje_detalle=cd.id WHERE cj.anulado = 0 AND de.id_devolucion IS NULL $filtroDesde $filtroHasta $filtroProveedor $filtroAlmacen $filtroCategoria";
      
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
        "input" => '<a href="verCanje.php?id='. $row["id_canje"]. '"><img src="img/eye.png" width="24" height="15" border="0" alt="Ver Canje" title="Ver Canje"></a>',
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
<?php
/*include 'database.php';
$aProveedores=[];

$filtroAlmacen="";
if(!empty($_GET["id_almacen"]&& $_GET["id_almacen"] != 0)) {
  $filtroAlmacen=" AND a.id=".$_GET["id_almacen"];
}
$filtroModalidad="";
if(!empty($_GET["id_modalidad"]&& $_GET["id_modalidad"] != 0)) {
  $filtroModalidad=" AND m.id IN (".$_GET["id_modalidad"].")";
}

//Ventas
$pdo = Database::connect();
$sql = "SELECT p.id AS id_proveedor, p.dni, CONCAT(p.nombre,' ',p.apellido) AS proveedor, p.email, IF(p.activo=1,'Si','No') AS activo, date_format(fecha_alta,'%d/%m/%Y') AS fecha_alta_formatted,fecha_alta, p.telefono, p.credito, a.almacen, m.modalidad, 
  (SELECT COUNT(vd.id) 
    FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id INNER JOIN productos p2 ON p2.id=vd.id_producto LEFT JOIN devoluciones_detalle de ON de.id_venta_detalle=vd.id 
    WHERE v.anulada = 0 AND v.id_venta_cbte_relacionado IS NULL AND de.id_devolucion IS NULL AND p2.id_proveedor=p.id
  ) AS ventasPesos,
  (SELECT COUNT(cd.id) 
    FROM canjes_detalle cd INNER JOIN canjes c ON cd.id_canje=c.id INNER JOIN productos p2 ON p2.id=cd.id_producto LEFT JOIN devoluciones_detalle de ON de.id_canje_detalle=cd.id 
    WHERE c.anulado = 0 AND de.id_devolucion IS NULL AND p2.id_proveedor=p.id
  ) AS ventasCanjes 
FROM proveedores p left join almacenes a on a.id = id_almacen left join modalidades m on m.id = id_modalidad WHERE 1 $filtroAlmacen $filtroModalidad";
//echo $sql;
foreach ($pdo->query($sql) as $row) {

  $btnModificar='<a href="modificarProveedor.php?id='.$row["id_proveedor"].'"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar" title="Modificar"></a>';
  $btnEliminar='<a href="#" class="btnEliminar" data-id="'.$row["id_proveedor"].'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar" title="Eliminar"></a>';
  $btnVer='<a href="verProveedor.php?id='.$row["id_proveedor"].'"><img src="img/eye.png" width="30" border="0" alt="Ver Proveedor" title="Ver Operaciones"></a>';
  
  $aProveedores[]=[
    "id"=>$row["id_proveedor"],
    "proveedor"=>$row["proveedor"],
    "dni"=>$row["dni"],
    "almacen"=>$row["almacen"],
    "ventasPesos"=>$row["ventasPesos"],
    "ventasCanjes"=>$row["ventasCanjes"],
    "enStock"=>0,
    "modalidad"=>$row["modalidad"],
    "email"=>$row["email"],
    "activo"=>$row["activo"],
    "telefono"=>$row["telefono"],
    "credito"=>number_format($row["credito"],2),
    "fecha_alta"=>$row["fecha_alta"],
    "fecha_alta_formatted"=>$row["fecha_alta_formatted"],
    "acciones" => $btnModificar.$btnEliminar.$btnVer
  ];
}

Database::disconnect();
echo json_encode($aProveedores);*/




include 'database.php';
include 'funciones.php';
session_start();
$aProveedores=[];

$pdo = Database::connect();
$columns = $_GET['columns'];

//$data_columns = ["","p.cb","p.codigo","c.categoria","p.descripcion","CONCAT(pr.nombre,' ',pr.apellido)","p.precio","p.activo"];//PARA EL ORDENAMIENTO

//$data_columns = $fields = ['p.id AS id_proveedor','p.dni','CONCAT(p.nombre," ",p.apellido) AS proveedor','(SELECT COUNT(vd.id) FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id INNER JOIN productos p2 ON p2.id=vd.id_producto LEFT JOIN devoluciones_detalle de ON de.id_venta_detalle=vd.id WHERE v.anulada = 0 AND v.id_venta_cbte_relacionado IS NULL AND de.id_devolucion IS NULL AND p2.id_proveedor=p.id) AS ventasPesos','(SELECT COUNT(cd.id) FROM canjes_detalle cd INNER JOIN canjes c ON cd.id_canje=c.id INNER JOIN productos p2 ON p2.id=cd.id_producto LEFT JOIN devoluciones_detalle de ON de.id_canje_detalle=cd.id WHERE c.anulado = 0 AND de.id_devolucion IS NULL AND p2.id_proveedor=p.id) AS ventasCanjes','(SELECT COUNT(s.id) FROM stock s INNER JOIN productos p2 ON p2.id=s.id_producto WHERE p2.id_proveedor=p.id AND s.cantidad > 0) AS enStock','IF(p.activo=1,"Si","No") AS activo','p.email','date_format(p.fecha_alta,"%d/%m/%Y") AS fecha_alta_formatted','p.fecha_alta','p.telefono','p.credito','a.almacen','m.modalidad'];
$data_columns = $fields = ['p.id AS id_proveedor','p.dni','p.nombre','p.apellido','IF(p.activo=1,"Si","No") AS activo','p.email','date_format(p.fecha_alta,"%d/%m/%Y") AS fecha_alta_formatted','p.fecha_alta','p.telefono','p.credito','a.almacen','m.modalidad'];

$from="FROM proveedores p left join almacenes a on a.id = id_almacen left join modalidades m on m.id = id_modalidad";

$orderBy = " ORDER BY ";
foreach ($_GET['order'] as $order) {
  $campo=$data_columns[$order['column']];
  $ex=explode(" AS ",$campo);
  $campo=$ex[0];
  $orderBy .= $campo . " {$order['dir']}, ";
}

$id_modalidad=$_GET["id_modalidad"];
$filtroModalidad="";
if($id_modalidad!="" and $id_modalidad>0){
  $filtroModalidad=" AND p.id_modalidad IN ($id_modalidad)";
}

$id_almacen=$_GET["id_almacen"];
$filtroAlmacen="";
if($id_almacen!=0){
  $filtroAlmacen=" AND p.id_almacen IN ($id_almacen)";
}
$activo=$_GET["activo"];
$filtroActivo="";
if($activo!=""){
  $filtroActivo=" AND p.activo IN ($activo)";
}

//var_dump($orderBy);
$orderBy = substr($orderBy, 0, -2);
//var_dump($orderBy);
$where = " 1 ";
//$whereFiltered=$where.$filtroDesde.$filtroHasta.$filtroAlmacen.$filtroModalidad.$filtroTipoComprobante;

foreach ($columns as $k => $column) {
    if ($search = $column['search']['value']) {
        $where .= ' AND '.$fields[$k].' = '.$search;
    }
}

//$where = substr($where, 0, -5);

$globalSearch = $_GET['search'];
//var_dump($globalSearch);

/*if ( $globalSearchValue = $globalSearch['value'] ) {
	$where .= ($where ? $where.' AND ' : '' )."name LIKE '%$globalSearchValue%'";
}*/
if ( $globalSearchValue = $globalSearch['value'] ) {
  $aWhere=[];
  foreach ($fields as $k => $field) {
    //$campo=$field;
    $ex=explode(" AS ",$field);
    $field=$ex[0];

    $aWhere[]=$field.' LIKE "%'.$globalSearchValue.'%"';
    //$where .= ($where ? $where.' AND ' : '' )."name LIKE '%$globalSearchValue%'";
  }
  $where .= ' AND ('.implode(' OR ', $aWhere).')';
}

$whereFiltered=$where.$filtroAlmacen.$filtroModalidad.$filtroActivo;

$length = $_GET['length'];
$start = $_GET['start'];

//OBTENEMOS EL TOTAL DE REGISTROS
//$countSql = "SELECT count(v.id) as Total $from WHERE $where";
$countSql = "SELECT count(p.id) as Total $from WHERE 1";
//echo $countSql;
$countSt = $pdo->query($countSql);
//var_dump($countSql);
$total = $countSt->fetch()['Total'];


//OBTENEMOS EL TOTAL DE REGISTROS CON FILTRO APLICADO
// Data set length after filtering
//$resFilterLength = self::sql_exec( $db, $bindings,"SELECT COUNT(`id`) FROM productos ".($where ? "WHERE $where " : ''));
$queryFiltered="SELECT COUNT(p.id) AS recordsFiltered $from ".($whereFiltered ? "WHERE $whereFiltered " : '');
//var_dump($queryFiltered);
//echo $queryFiltered;
$resFilterLength = $pdo->query($queryFiltered);
$recordsFiltered = $resFilterLength->fetch()['recordsFiltered'];

$campos=implode(",", $fields);
//$fields = ['cb','codigo','categoria','descripcion','nombre','apellido','precio','p.activo','p.id'];

//$sql = "SELECT * FROM productos ".($where ? "WHERE $where " : '')."$orderBy LIMIT $length OFFSET $start";
$sql = "SELECT $campos $from ".($whereFiltered ? "WHERE $whereFiltered " : '')."$orderBy LIMIT $length OFFSET $start";
error_log($sql);
//echo $sql;
$st = $pdo->query($sql);
$queryInfo="";
if ($st) {
    //$rs = $st->fetchAll(PDO::FETCH_FUNC, fn($id, $codigo, $categoria) => [$id, $codigo, $categoria] );
    foreach ($pdo->query($sql) as $row) {

      $btnModificar='<a href="modificarProveedor.php?id='.$row["id_proveedor"].'"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar" title="Modificar"></a> ';
      $btnEliminar='<a href="#" class="btnEliminar" data-id="'.$row["id_proveedor"].'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar" title="Eliminar"></a> ';
      $btnVer='<a href="verProveedor.php?id='.$row["id_proveedor"].'"><img src="img/eye.png" width="30" border="0" alt="Ver Proveedor" title="Ver Operaciones"></a>';
      
      $aProveedores[]=[
        "id"=>$row["id_proveedor"],
        "proveedor"=>$row["nombre"]." ".$row["apellido"],
        "nombre"=>$row["nombre"],
        "apellido"=>$row["apellido"],
        "dni"=>$row["dni"],
        "almacen"=>$row["almacen"],
        /*"ventasPesos"=>$row["ventasPesos"],
        "ventasCanjes"=>$row["ventasCanjes"],
        "enStock"=>$row["enStock"],*/
        "modalidad"=>$row["modalidad"],
        "email"=>$row["email"],
        "activo"=>$row["activo"],
        "telefono"=>$row["telefono"],
        "credito"=>$row["credito"],
        "fecha_alta"=>$row["fecha_alta"],
        "fecha_alta_formatted"=>$row["fecha_alta_formatted"],
        "acciones" => $btnModificar.$btnEliminar.$btnVer
      ];
    }

    $queryInfo=[
      'campos' => $campos,
      'from' => $from,
      'where' => $whereFiltered,
      'orderBy' => $orderBy,
      'length' => $length,
      'start' => $start,
      'query' => $sql,
    ];
} else {
    var_dump($pdo->errorInfo());
    die;
}

echo json_encode([
  'data' => $aProveedores,
  'recordsTotal' => $total,
  'recordsFiltered' => $recordsFiltered,//count($aProveedores),
  'queryInfo'=>$queryInfo,
]);

<?php
include 'database.php';
$aProductos=[];

$pdo = Database::connect();
$columns = $_GET['columns'];

//$data_columns = ["","p.cb","p.codigo","c.categoria","p.descripcion","CONCAT(pr.nombre,' ',pr.apellido)","p.precio","p.activo"];//PARA EL ORDENAMIENTO

$data_columns = $fields = ['CONCAT(pr.nombre," ",pr.apellido)','p.descripcion','a.almacen','p.precio','p.precio_costo','p.precio-p.precio_costo','p.codigo','c.categoria','v.id'];

$from="FROM ventas v INNER JOIN ventas_detalle vd ON vd.id_venta=v.id inner join productos p on p.id = vd.id_producto inner join almacenes a on a.id = v.id_almacen inner join categorias c on c.id = p.id_categoria inner join proveedores pr on pr.id = p.id_proveedor";

$orderBy = " ORDER BY ";
foreach ($_GET['order'] as $order) {
  $orderBy .= $data_columns[$order['column']] . " {$order['dir']}, ";
}

$id_proveedor=$_GET["id_proveedor"];
$filtroProveedor="";
if($id_proveedor!=""){
  $filtroProveedor=" AND p.id_proveedor IN ($id_proveedor)";
}

$id_almacen=$_GET["id_almacen"];
$filtroAlmacen="";
if($id_almacen!=""){
  $filtroAlmacen=" AND v.id_almacen IN ($id_almacen)";
}

$id_categoria=$_GET["id_categoria"];
$filtroCategoria="";
if($id_categoria!=""){
  $filtroCategoria=" AND p.id_categoria IN ($id_categoria)";
}

//var_dump($orderBy);
$orderBy = substr($orderBy, 0, -2);
//var_dump($orderBy);
$where = "vd.id_modalidad = 1 ";
$whereFiltered=$where.$filtroProveedor.$filtroAlmacen.$filtroCategoria;

foreach ($columns as $k => $column) {
    if ($search = $column['search']['value']) {
        $where .= ' AND '.$fields[$k].' = '.$search;
    }
}

//$where = substr($where, 0, -5);

$globalSearch = $_GET['search'];
/*if ( $globalSearchValue = $globalSearch['value'] ) {
	$where .= ($where ? $where.' AND ' : '' )."name LIKE '%$globalSearchValue%'";
}*/
if ( $globalSearchValue = $globalSearch['value'] ) {
  $aWhere=[];
  foreach ($fields as $k => $field) {
    $aWhere[]=$field.' LIKE "%'.$globalSearchValue.'%"';
    //$where .= ($where ? $where.' AND ' : '' )."name LIKE '%$globalSearchValue%'";
  }
  $where .= '('.implode(' OR ', $aWhere).')';
}

$length = $_GET['length'];
$start = $_GET['start'];

//OBTENEMOS EL TOTAL DE REGISTROS
$countSql = "SELECT count(p.id) as Total $from WHERE $where";
$countSt = $pdo->query($countSql);

$total = $countSt->fetch()['Total'];


//OBTENEMOS EL TOTAL DE REGISTROS CON FILTRO APLICADO
// Data set length after filtering
//$resFilterLength = self::sql_exec( $db, $bindings,"SELECT COUNT(`id`) FROM productos ".($where ? "WHERE $where " : ''));
$queryFiltered="SELECT COUNT(p.id) AS recordsFiltered $from ".($whereFiltered ? "WHERE $whereFiltered " : '');
//var_dump($queryFiltered);

$resFilterLength = $pdo->query($queryFiltered);
$recordsFiltered = $resFilterLength->fetch()['recordsFiltered'];

$campos=implode(",", $fields);
//$fields = ['cb','codigo','categoria','descripcion','nombre','apellido','precio','p.activo','p.id'];

$sql2 = "SELECT SUM(vd.precio) AS total_precio_venta,SUM(precio_costo) AS total_precio_costo,SUM(vd.precio)-SUM(precio_costo) AS total_ganancia $from ".($whereFiltered ? "WHERE $whereFiltered " : '');
//echo $sql2;
$row2 = $pdo->query($sql2)->fetch();

$total_precio_venta = $row2['total_precio_venta'];
$total_precio_costo = $row2['total_precio_costo'];
$total_ganancia = $row2['total_ganancia'];

//$sql = "SELECT * FROM productos ".($where ? "WHERE $where " : '')."$orderBy LIMIT $length OFFSET $start";
$sql = "SELECT $campos $from ".($whereFiltered ? "WHERE $whereFiltered " : '')."$orderBy LIMIT $length OFFSET $start";
error_log($sql);
//var_dump($sql);
$st = $pdo->query($sql);
$queryInfo="";
if ($st) {
    //$rs = $st->fetchAll(PDO::FETCH_FUNC, fn($id, $codigo, $categoria) => [$id, $codigo, $categoria] );
    foreach ($pdo->query($sql) as $row) {

      $aProductos[]=[
        "codigo"=>$row['codigo'],
        "categoria"=>$row['categoria'],
        "descripcion"=>$row['descripcion'],
        "almacen"=>$row['almacen'],
        //"proveedor"=>$row['nombre']." ".$row['apellido'],
        "proveedor"=>$row[0],// AS proveedor
        "precio"=>$row['precio'],
        "precio_costo"=>$row['precio_costo'],
        "ganancia"=>$row['precio']-$row['precio_costo'],
        "id_venta"=>$row['id'],// AS id_venta
      ];
    }

    $queryInfo=[
      'campos' => $campos,
      'from' => $from,
      'where' => $where,
      'orderBy' => $orderBy,
      'length' => $length,
      'start' => $start,
      'query' => $sql,
      'total_precio_venta'=>$total_precio_venta,
      'total_precio_costo'=>$total_precio_costo,
      'total_ganancia'=>$total_ganancia,
    ];
} else {
    var_dump($pdo->errorInfo());
    die;
}

echo json_encode([
  'data' => $aProductos,
  'recordsTotal' => $total,
  'recordsFiltered' => $recordsFiltered,//count($aProductos),
  'queryInfo'=>$queryInfo,
]);
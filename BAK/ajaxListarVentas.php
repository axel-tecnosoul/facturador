<?php
include 'database.php';
include 'funciones.php';
session_start();
$aProductos=[];

$pdo = Database::connect();
$columns = $_GET['columns'];

//$data_columns = ["","p.cb","p.codigo","c.categoria","p.descripcion","CONCAT(pr.nombre,' ',pr.apellido)","p.precio","p.activo"];//PARA EL ORDENAMIENTO

$data_columns = $fields = ['v.id','date_format(v.fecha_hora,"%d/%m/%Y %H:%i")','v.tipo_comprobante','a.almacen','fp.forma_pago','v.total_con_descuento','v.modalidad_venta','v.nombre_cliente','v.dni','v.direccion','v.email','v.telefono','v.total','d.descripcion','v.id_cierre_caja','v.estado'];

$from="FROM ventas v inner join almacenes a on a.id = v.id_almacen left join descuentos d on d.id = v.id_descuento_aplicado INNER JOIN forma_pago fp ON v.id_forma_pago=fp.id";

$orderBy = " ORDER BY ";
foreach ($_GET['order'] as $order) {
  $orderBy .= $data_columns[$order['column']] . " {$order['dir']}, ";
}

$desde=$_GET["desde"];
$filtroDesde="";
if($desde!=""){
  $filtroDesde=" AND DATE(v.fecha_hora)>='$desde'";
}

$hasta=$_GET["hasta"];
$filtroHasta="";
if($hasta!=""){
  $filtroHasta=" AND DATE(v.fecha_hora)<='$hasta'";
}

$forma_pago=$_GET["forma_pago"];
$filtroFormaPago="";
if($forma_pago!=""){
  $filtroFormaPago=" AND v.id_forma_pago IN ($forma_pago)";
}

$id_almacen=$_GET["id_almacen"];
$filtroAlmacen="";
if($id_almacen!=0){
  $filtroAlmacen=" AND v.id_almacen IN ($id_almacen)";
}

$tipo_comprobante=$_GET["tipo_comprobante"];
$filtroTipoComprobante="";
if($tipo_comprobante!=""){
  $ex=explode(",",$tipo_comprobante);
  $tipo_comprobante="'".implode("','",$ex)."'";
  $filtroTipoComprobante=" AND v.tipo_comprobante IN ($tipo_comprobante)";
}

//var_dump($orderBy);
$orderBy = substr($orderBy, 0, -2);
//var_dump($orderBy);
$where = "v.anulada = 0";
if ($_SESSION['user']['id_perfil'] != 1) {
  $where.=" and a.id = ".$_SESSION['user']['id_almacen']; 
}
//$whereFiltered=$where.$filtroDesde.$filtroHasta.$filtroAlmacen.$filtroFormaPago.$filtroTipoComprobante;

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
    $aWhere[]=$field.' LIKE "%'.$globalSearchValue.'%"';
    //$where .= ($where ? $where.' AND ' : '' )."name LIKE '%$globalSearchValue%'";
  }
  $where .= ' AND ('.implode(' OR ', $aWhere).')';
}

$whereFiltered=$where.$filtroDesde.$filtroHasta.$filtroAlmacen.$filtroFormaPago.$filtroTipoComprobante;

$length = $_GET['length'];
$start = $_GET['start'];

//OBTENEMOS EL TOTAL DE REGISTROS
//$countSql = "SELECT count(v.id) as Total $from WHERE $where";
$countSql = "SELECT count(v.id) as Total $from WHERE v.anulada=0";
//echo $countSql;
$countSt = $pdo->query($countSql);
//var_dump($countSql);
$total = $countSt->fetch()['Total'];


//OBTENEMOS EL TOTAL DE REGISTROS CON FILTRO APLICADO
// Data set length after filtering
//$resFilterLength = self::sql_exec( $db, $bindings,"SELECT COUNT(`id`) FROM productos ".($where ? "WHERE $where " : ''));
$queryFiltered="SELECT COUNT(v.id) AS recordsFiltered $from ".($whereFiltered ? "WHERE $whereFiltered " : '');
//var_dump($queryFiltered);

$resFilterLength = $pdo->query($queryFiltered);
$recordsFiltered = $resFilterLength->fetch()['recordsFiltered'];

$campos=implode(",", $fields);
//$fields = ['cb','codigo','categoria','descripcion','nombre','apellido','precio','p.activo','p.id'];

$sql2 = "SELECT SUM(CASE WHEN v.tipo_comprobante IN ('NCA','NCB') THEN total_con_descuento*-1 ELSE total_con_descuento END) AS total_facturas_recibos $from WHERE $whereFiltered ";
//echo $sql2;
$row2 = $pdo->query($sql2)->fetch();

$total_facturas_recibos = ($row2['total_facturas_recibos'] ?: 0);

//$sql = "SELECT * FROM productos ".($where ? "WHERE $where " : '')."$orderBy LIMIT $length OFFSET $start";
$sql = "SELECT $campos $from ".($whereFiltered ? "WHERE $whereFiltered " : '')."$orderBy LIMIT $length OFFSET $start";
error_log($sql);
//echo $sql;
$st = $pdo->query($sql);
$queryInfo="";
if ($st) {
    //$rs = $st->fetchAll(PDO::FETCH_FUNC, fn($id, $codigo, $categoria) => [$id, $codigo, $categoria] );
    foreach ($pdo->query($sql) as $row) {

      $aProductos[]=[
        "id_venta"=>$row['id'],
        "fecha_hora"=>$row[1],// AS fecha_hora
        "tipo_comprobante"=>$tipo_cbte=get_nombre_comprobante($row["tipo_comprobante"]),
        "almacen"=>$row['almacen'],
        "forma_pago"=>$row['forma_pago'],
        "total_con_descuento"=>$row["total_con_descuento"],// AS proveedor
        "nombre_cliente"=>$row['nombre_cliente'],
        "dni"=>$row['dni'],
        "direccion"=>$row['direccion'],
        "email"=>$row['email'],
        "telefono"=>$row['telefono'],
        "total"=>$row['total'],
        "descuento"=>$row['descripcion'],
        "id_cierre_caja"=>$row['id_cierre_caja'],
        "modalidad_venta"=>$row["modalidad_venta"],
        "estado"=>$row['estado']
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
      'query_total_facturas_recibos' => $sql2,
      'total_facturas_recibos'=>$total_facturas_recibos,
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

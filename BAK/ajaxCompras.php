<?php
include 'database.php';
$aProductos=[];
$total=0;
$recordsFiltered=0;
$debug="";
$queryInfo="";
if(!empty($_GET["proveedor"]) and $_GET["proveedor"]>0) {

  $id_proveedor=$_GET["proveedor"];

  $pdo = Database::connect();
  $columns = $_GET['columns'];
  //var_dump($columns);

  $data_columns = ["p.cb","p.codigo","c.categoria","p.descripcion","p.precio"];
  
  $fields = ["p.id","p.codigo","c.categoria","p.descripcion","p.precio","p.cb"];

  $from="FROM productos p inner join categorias c on c.id = p.id_categoria";

  $orderBy = " ORDER BY ";
  foreach ($_GET['order'] as $order) {
    //var_dump($order);
    //$orderBy .= $order['column'] + 1 . " {$order['dir']}, ";
    $orderBy .= $data_columns[$order['column']] . " {$order['dir']}, ";
  }

  $orderBy = substr($orderBy, 0, -2);

  $where = " p.id_proveedor = $id_proveedor AND activo=1 AND ";
  foreach ($columns as $k => $column) {
    if ($search = $column['search']['value']) {
      $search=trim($search,"$");
      $search=trim($search,"^");
      $where .= $fields[$k].' LIKE "'.$search.'" AND ';
    }
  }

  $globalSearch = $_GET['search'];
  if ( $globalSearchValue = $globalSearch['value'] ) {
    $aWhere=[];
    foreach ($fields as $k => $field) {
      $aWhere[]=$field.' LIKE "%'.$globalSearchValue.'%"';
    }
    $where .= '('.implode(' OR ', $aWhere).')';
  }

  if(substr($where, -5)==" AND ") $where = substr($where, 0, -5);

  $length = $_GET['length'];
  $start = $_GET['start'];

  //OBTENEMOS EL TOTAL DE REGISTROS
  $countSql = "SELECT count($fields[0]) as Total $from";
  $countSt = $pdo->query($countSql);
  $total = $countSt->fetch()['Total'];


  //OBTENEMOS EL TOTAL DE REGISTROS CON FILTRO APLICADO
  $queryFiltered="SELECT COUNT($fields[0]) AS recordsFiltered $from ".($where ? "WHERE $where " : '');
  //var_dump($queryFiltered);
  $resFilterLength = $pdo->query($queryFiltered);
  $recordsFiltered = $resFilterLength->fetch()['recordsFiltered'];

  $campos=implode(",", $fields);

  $sql = "SELECT $campos $from ".($where ? "WHERE $where " : '')."$orderBy LIMIT $length OFFSET $start";

  error_log($sql);
  //var_dump($sql);
  $debug.=$sql;
  $st = $pdo->query($sql);

  if ($st) {
    //$rs = $st->fetchAll(PDO::FETCH_FUNC, fn($id, $name, $price) => [$id, $name, $price] );
    foreach ($pdo->query($sql) as $row) {
      //var_dump($row);
      
      $fields = ["p.id","p.codigo","c.categoria","p.descripcion","p.precio","p.cb"];
      $aProductos[]=[
        "id_producto"=>$row["id"],
        "codigo"=>$row["codigo"],
        "categoria"=>$row["categoria"],
        "descripcion"=>$row["descripcion"],
        "precio"=>$row["precio"],
        "cb"=>$row["cb"],
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
    ];

  } else {
    var_dump($pdo->errorInfo());
    die;
  }
  Database::disconnect();
}
echo json_encode([
  'data' => $aProductos,
  'recordsTotal' => $total,
  'recordsFiltered' => $recordsFiltered,//count($aProductos),
  'debug'=>$debug,
  'queryInfo'=>$queryInfo,
]);


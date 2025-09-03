<?php
require("config.php");
/*if (empty($_SESSION['user'])) {
    header("Location: index.php");
    die("Redirecting to index.php");
}*/
    
require 'database.php';
require 'funciones.php';
    
$id_proveedor = null;
if (isset($_POST['id_proveedor'])) {
    $id_proveedor = $_POST['id_proveedor'];
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//$periodo="diario";
//$periodo="mensual";

$mysqlHoy="curdate()";
$mysqlHoyHaceUnAnio="DATE_SUB(curdate(), INTERVAL 1 YEAR)";

$whereEsteAnio="YEAR(fecha_hora_alta)=YEAR($mysqlHoy)";
$whereAnioAnterior="YEAR(fecha_hora_alta)=YEAR($mysqlHoyHaceUnAnio)";

$aLabel=[1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0];
//$aLabel=ultimosDoceMeses();

$aLabel=[];
$dataEsteAnio=[];
$dataAnioAnterior=[];
$primerDiaMes=date("Y-m-01");
//$ultimoDiaMes=date("Y-m-t");//con el parametro "t" obtenemos el ultimo dia del mes
for ($i=0; $i < 12; $i++) { 
  $label=date("Y-m", strtotime($primerDiaMes));
  $aLabel[]=$label;
  $dataEsteAnio[$label]=0;
  $dataAnioAnterior[$label]=0;
  $primerDiaMes=date("Y-m-01", strtotime($primerDiaMes." -1 month"));
}
//var_dump($aLabel);
$aLabel=array_reverse($aLabel);
$dataEsteAnio=array_reverse($dataEsteAnio);
$dataAnioAnterior=array_reverse($dataAnioAnterior);
//var_dump($aLabel);

$sql = "SELECT DATE_FORMAT(fecha_hora_alta, '%Y-%m') AS label, COUNT(id) AS cant, (
  SELECT COUNT(id) FROM productos WHERE id_proveedor IN ($id_proveedor) AND YEAR(fecha_hora_alta) = YEAR(CURDATE()) - 1 AND MONTH(fecha_hora_alta) = MONTH(p.fecha_hora_alta)
) AS cant_anio_anterior FROM productos p WHERE id_proveedor IN ($id_proveedor) AND fecha_hora_alta >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(fecha_hora_alta, '%Y-%m') ORDER BY fecha_hora_alta;";
$q = $pdo->prepare($sql);
$q->execute();
//$dataEsteAnio=$aLabel;
while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
  //var_dump($fila);
  $dataEsteAnio[$fila["label"]]=$fila["cant"];
  $dataAnioAnterior[$fila["label"]]=$fila["cant_anio_anterior"];
}
//var_dump($dataEsteAnio);
//var_dump($dataAnioAnterior);

function formatFecha($array){
    //return formatFechaGraficoLineasPorMeses($array);
    $newArray=[];
    foreach ($array as $key => $value) {
      //var_dump($value);
        //$numeroMesCon2Decimales=str_pad($value, 2, 0, STR_PAD_LEFT);
        $newArray[]=date("Y-m-d", strtotime($value."-01"));
    }
    return $newArray;
}

Database::disconnect();
//var_dump($aLabel);

$datasets=[
    [
        "label"=> "Ultimo año",
        //"fillColor"=> "rgba(68, 102, 242, 0.3)",
        "fillColor"=> "rgba(68, 102, 242, 0.1)",
        "backgroundColor"=> "#0B3B0B",
        "borderColor"=> "#0B3B0B",
        "borderWidth"=>1,
        "pointHitRadius"=>5,
        "lineTension"=> 0,
        "fill"=> false,
        //"data"=> [10, 59, 80, 81, 56, 55, 40]
        "data"=> array_values($dataEsteAnio)
    ],[
        "label"=> "Año anterior",
        //"fillColor"=> "rgba(30, 166, 236, 0.3)",
        "fillColor"=> "rgba(30, 166, 236, 0.1)",
        "backgroundColor"=> "#1ea6ec",
        "borderColor"=> "#1ea6ec",
        "borderWidth"=>1,
        "pointHitRadius"=>5,
        "lineTension"=> 0,
        "fill"=> false,
        //"data"=> [28, 48, 40, 19, 86, 27, 90]
        "data"=> array_values($dataAnioAnterior)
    ]
    /*,[
      "label"=> $fila["producto"],//no hace nada, se debe agregar la leyenda manualmente
      "backgroundColor"=> $color,
      "borderColor"=> $color,
      //"spanGaps"=>false,//para no mostrar los que estan en 0
      "fill"=> false,
      "data"=> array_values($data)
    ]*/
];

$datosGraficoLienas=[
    "labels"=>formatFecha($aLabel),//array_keys
    //"labels"=>$aLabel,//array_keys
    "datasets"=>$datasets,
];

echo json_encode($datosGraficoLienas);

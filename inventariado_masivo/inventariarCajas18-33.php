<style>
  .text-center{
    text-align: center;
  }
  /* Estilo para todas las celdas vacías en la tabla */
  table td:empty {
    background-color: #ffcccc; /* Cambia el color de fondo a tu preferencia */
  }
</style>
<?php
ini_set('max_execution_time', '0');
//$archivo=$_FILES["archivo"]['name'];
//$ruta="E:/TRABAJO/00 - Nelson Murstein/Newine/importaciones modulo produccion/";
$ruta="";

//$archivo=$ruta."CATEGORIAS ordenamiento nuevas.xlsx";
$archivo=$ruta."LISTADO PARA INVENTARIAR MASIVAMENTE.xlsx";
$hoja_usar=0;//LISTADO 18-33

//$destino=$archivo;
//copy($_FILES['archivo']['tmp_name'],$destino);
//var_dump($archivo);
$archivo=strval(str_replace("\0", "", $archivo));
//var_dump($archivo);

include_once("../vendor/PHPExcel/IOFactory.php");
$objPHPExcel = PHPExcel_IOFactory::load($archivo);
//var_dump($objPHPExcel);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

$ar=explode('.',$archivo);
$file=$ar[0].".xlsx";
//$objWriter->save($file);

//if (file_exists ("bak_".$file)){
if (file_exists ($file)){//validacion para saber si el archivo ya existe previamente
  /*INVOCACION DE CLASES Y CONEXION A BASE DE DATOS*/
  /** Invocacion de Clases necesarias */
  require_once('../vendor/PHPExcel.php');
  require_once('../vendor/PHPExcel/Reader/Excel2007.php');
  //DATOS DE CONEXION A LA BASE DE DATOS
  //include '../database.php';
  include "../database.php";
  $pdo = Database::connect();
  $pdo->beginTransaction();

  function pdo_debugStrParams($stmt) {
    ob_start();
    $stmt->debugDumpParams();
    $r = ob_get_contents();
    ob_end_clean();
    //echo $r;
    $ex=explode("Sent SQL:",$r);
    //var_dump($ex);
    $ex2=explode("Params:",$ex[1]);
    //var_dump($ex2);
    return trim($ex2[0]);
    return $r;
  }

  $modoDebug=0;
        
  /*var_dump($archivo);
  echo "<br>";
  echo "<br>";*/

  // Cargando la hoja de calculo
  $objReader=new PHPExcel_Reader_Excel2007();//instancio un objeto como PHPExcelReader(objeto de captura de datos de excel)
  //$objPHPExcel=$objReader->load("bak_".$file);//carga en objphpExcel por medio de objReader,el nombre del archivo
  $objPHPExcel=$objReader->load($file);//carga en objphpExcel por medio de objReader,el nombre del archivo
  $objFecha=new PHPExcel_Shared_Date();// Asignar hoja de excel activa
  $objPHPExcel->setActiveSheetIndex($hoja_usar);//objPHPExcel tomara la posicion de hoja (en esta caso 0 o 1) con el setActiveSheetIndex(numeroHoja)
  // Llenamos un arreglo con los datos del archivo xlsx
  $i=2; //celda inicial en la cual empezara a realizar el barrido de la grilla de excel
  $f=0;

  $param=$contador=$ok=$inventariado_ok=0;
  $filas=$objPHPExcel->getActiveSheet()->getHighestRow()-1;
  $errores="";
  while($param==0){//mientras el parametro siga en 0, no ha encontrado un NULL, entonces sige metiendo datos
    if($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue()==NULL or $i==$f){//pregunta si ha encontrado un valor null en la columna
      $param=1;//Si ha encontrado un valor en NULL pone $param en 1 y finaliza eh while
    }else{
      $contador++;

      $caja=str_replace("'"," ",$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue());
      $id_proveedor=str_replace("'"," ",$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue());
      $id_proveedor=str_replace("#","",$id_proveedor);
      $codigo=str_replace("'"," ",$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue());

      //echo $categoria." - ".$accion."<br>";
      $id_almacen=9;
      $cantidad=1;

      //if($id_proveedor!="BYRO" and $id_proveedor!="BYRO"){
        //$sql2 = " SELECT * FROM productos p INNER JOIN stock s ON s.id_producto=p.id WHERE p.codigo = '$codigo' AND p.id_proveedor=$id_proveedor AND s.id_almacen=$id_almacen AND s.cantidad=$cantidad";
        $sql2 = " SELECT s.id,p.id_proveedor,s.id_almacen,s.cantidad, inventario_ok FROM productos p LEFT JOIN stock s ON s.id_producto=p.id WHERE p.codigo = ?";
        $q2 = $pdo->prepare($sql2);
        $q2->execute([$codigo]);

        $afe=$q2->rowCount();
        //echo "<strong>Se han encontrado $afe registros</strong><br>";
        while($data = $q2->fetch(PDO::FETCH_ASSOC)){
          if($data["id_proveedor"]==$id_proveedor and $data["id_almacen"]==$id_almacen and $data["cantidad"]==$cantidad){
            if($afe==1){
              $ok++;
              //echo "<strong>ENCONTRADO</strong><br>";
              $sql = "UPDATE stock SET inventario_ok = 1 WHERE id = ".$data["id"];
              $q = $pdo->prepare($sql);
              $q->execute();
              $afeUpdate=$q->rowCount();
              if($afeUpdate==1 or $afeUpdate==0){
                $inventariado_ok++;
              }else{
                var_dump($sql);
                $err = $q->errorInfo();
                var_dump($err);
              }

            }else{
              $errores.="<tr><td>$caja</td><td>$codigo</td><td>$id_proveedor</td><td>$data[id_proveedor]</td><td class='text-center'>$id_almacen</td><td class='text-center'>$data[id_almacen]</td><td class='text-center'>$cantidad</td><td class='text-center'>$data[cantidad]</td><td class='text-center'>$data[inventario_ok]</td><td>Se repite</td></tr>";
            }
          }else{
            if($afe==1){
              $error="No coincide";
            }else{
              $error="Aparece $afe veces";
            }
            $errores.="<tr><td>$caja</td><td>$codigo</td><td>$id_proveedor</td><td>$data[id_proveedor]</td><td class='text-center'>$id_almacen</td><td class='text-center'>$data[id_almacen]</td><td class='text-center'>$cantidad</td><td class='text-center'>$data[cantidad]</td><td class='text-center'>$data[inventario_ok]</td><td>$error</td></tr>";
          }
          /*else{
            echo $codigo." - ".$id_proveedor."<br>";
            echo "<strong>NO ENCONTRADO</strong><br>";
            echo "<hr>";
            $errores;
          }*/

          /*if ($modoDebug==1) {
            $q2->debugDumpParams();
            echo "<br><br>Afe: ".$afe;
            $err = $q2->errorInfo();
            if($err[1]){
              var_dump($err);
            }
            echo "<br><br>";
          }*/
        }
      //}

    }
    $i++;
  }

  echo "Total: ".$contador."<br>";
  echo "Correctos: ".$ok."<br>";
  echo "Inventariados OK: ".$inventariado_ok;

  if($errores!=""){
    echo '<table border="1"><tr><td>Caja</td><td>Codigo</td><td>Proveedor Excel</td><td>Proveedor BBDD</td><td>Almacen Excel</td><td>Almacen BBDD</td><td>Cantidad Excel</td><td>Cantidad BBDD</td><td>Inventariado</td><td>Error</td></tr>'.$errores.'</table>';
  }
  
  //unlink($archivo);
  
  if ($modoDebug==1) {
    $pdo->rollBack();
  }else{
    $pdo->commit();
  }

}?>
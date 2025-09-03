<?php
ini_set('max_execution_time', '0');
//$archivo=$_FILES["archivo"]['name'];
//$ruta="E:/TRABAJO/00 - Nelson Murstein/Newine/importaciones modulo produccion/";
$ruta="";

$archivo=$ruta."CATEGORIAS a eliminar.xlsx";

//$destino=$archivo;
//copy($_FILES['archivo']['tmp_name'],$destino);
$archivo=strval(str_replace("\0", "", $archivo));

include_once("../vendor/PHPExcel/IOFactory.php");
$objPHPExcel = PHPExcel_IOFactory::load($archivo);
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

$ar=explode('.',$archivo);
$file=$ar[0].".xlsx";
$objWriter->save($file);

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

  $modoDebug=1;
        
  var_dump($archivo);
  echo "<br>";
  echo "<br>";

  // Cargando la hoja de calculo
  $objReader=new PHPExcel_Reader_Excel2007();//instancio un objeto como PHPExcelReader(objeto de captura de datos de excel)
  //$objPHPExcel=$objReader->load("bak_".$file);//carga en objphpExcel por medio de objReader,el nombre del archivo
  $objPHPExcel=$objReader->load($file);//carga en objphpExcel por medio de objReader,el nombre del archivo
  $objFecha=new PHPExcel_Shared_Date();// Asignar hoja de excel activa
  $objPHPExcel->setActiveSheetIndex(0);//objPHPExcel tomara la posicion de hoja (en esta caso 0 o 1) con el setActiveSheetIndex(numeroHoja)
  // Llenamos un arreglo con los datos del archivo xlsx
  $i=2; //celda inicial en la cual empezara a realizar el barrido de la grilla de excel
  $f=0;

  $param=$contador=$ok=0;
  $filas=$objPHPExcel->getActiveSheet()->getHighestRow()-1;
  
  while($param==0){//mientras el parametro siga en 0, no ha encontrado un NULL, entonces sige metiendo datos
    if($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue()==NULL or $i==$f){//pregunta si ha encontrado un valor null en la columna
      $param=1;//Si ha encontrado un valor en NULL pone $param en 1 y finaliza eh while
    }else{
      $id_categoria=str_replace("'"," ",$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue());
      $categoria=str_replace("'"," ",$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue());
      $accion=str_replace("'"," ",$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue());

      /*echo $id_categoria." - ".$categoria." - ".$accion."<br>";
      var_dump($accion);
      if($accion==""){
        echo "DEJAR<BR>";
      }*/

      if($accion!=""){
        $sql = " SELECT id, categoria, activa FROM categorias WHERE id = ? ";
        $sql = $pdo->prepare($sql);
        $sql->execute([$id_categoria]);
        $row=$sql->fetch(PDO::FETCH_ASSOC);
        $afe=$sql->rowCount();
        $contador++;
        if($afe==1){

          echo "<strong>ENCONTRADO</strong>";
          var_dump($row);

          $sql2 = " UPDATE categorias SET activa = 0 WHERE id = ? ";
          $sql2 = $pdo->prepare($sql2);
          $sql2->execute([$row["id"]]);
          $afe2=$sql2->rowCount();
          if($afe2==1){
            $ok++;
          }
        }else{
          echo $categoria." - ".$accion."<br>";
          echo "<strong>NO ENCONTRADO</strong>";
          echo "<hr>";
        }

        if ($modoDebug==1) {
          $sql->debugDumpParams();
          echo "<br><br>Afe: ".$afe;
          $err = $sql->errorInfo();
          if($err[1]){
            var_dump($err);
          }
          echo "<br><br>";
        }
      }

    }
    $i++;
  }

  $anduvo=1;
  if($contador==$ok and $ok>0){
    echo "TODO BIEN<br>";
    echo "Total: ".($i-3)."<br>";
    echo "contador ($contador) == ok ($ok)";
    if ($modoDebug==0) {
      $pdo->commit();
    }
  }else{
    echo "NO ANDUVO<br>";
    echo "Total: ".($i-3)."<br>";
    echo "contador: ".$contador."<br>";
    echo "ok: ".$ok;
    $anduvo=0;
  }
  
  //unlink($archivo);
  
  if ($modoDebug==1 or $anduvo==0) {
    $pdo->rollBack();
  }

}?>
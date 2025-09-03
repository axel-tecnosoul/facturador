<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
extract($_REQUEST);
/*$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}*/

if ( null==$id ) {
  $fileVolver="listarProductos.php";
  if(isset($volver)){
    if($volver=="stock"){
      $fileVolver="listarStock.php";
    }
  }
  header("Location: $fileVolver");
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$array = explode(',', $id);?>
<style>
  body{
    margin: 2px;
    margin-top: 0;
  }
  .borde_etiqueta{
    border: solid 1px black;
    border-radius: 10px;
    width:175px;
    height:82.5px;
    float: left;
    /*margin: 16.5px;*/
    margin: 8.5px;
    margin-bottom: 0;
    margin-top: 5px;
  }
  .img_etiqueta{
    margin:10 auto; max-width:100%; max-height:100%; display: block; height: 66px; align-items: center;
  }
  .container_img{
    max-height: 55px;
    /*max-width: 160px;*/
    margin: 0 auto;
  }
  .lbl_etiqueta{
    text-align: center; margin: 0; font-size: 14px; margin-bottom: 0px;
  }
  .contenedor{
    width:100%;
    height:100%;
    /*margin: 0;*/
    /*margin-left: -12px;
    margin-right: -12px;*/
    margin-bottom: 0;
    margin-top: 0;
  }
  .loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .loading-text {
    color: #fff;
    font-size: 24px;
  }
</style>

<!-- margin-top: -15px; -->
<div id="loading" class="loading-overlay">
  <div class="loading-text">Cargando...</div>
</div>
<div style="display: none;"><?=count($array)?></div>
<div class='contenedor'><?php
  foreach ($array as $value){
    
    $sql = "SELECT id_proveedor, codigo, descripcion, precio, cb FROM productos WHERE id = ? ";
    $q = $pdo->prepare($sql);
    $q->execute(array($value));
    $data = $q->fetch(PDO::FETCH_ASSOC);
    
    $nombre = $data['descripcion'];
    $proveedor = $data['id_proveedor'];
    $codigo = $data['codigo'];
    $precio = $data['precio'];
    $cb = $data['cb'];?>
    <div class="borde_etiqueta">
      <div>
        <div>
          <div class="container_img">
            <img class="img_etiqueta" alt='testing' src='barcode.php?codetype=Code128&size=50&text=<?=$cb?>&print=true'/>
          </div>
        </div>
        <p class="lbl_etiqueta"><?=$proveedor." "?><?=$codigo?> $<?=number_format($precio,2)?></p>
      </div>
    </div><?php

  }?>
</div><?php

Database::disconnect();?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var loadingOverlay = document.getElementById('loading');
    loadingOverlay.style.display = 'block';
  });

  window.addEventListener('load', function() {
    var loadingOverlay = document.getElementById('loading');
    loadingOverlay.style.display = 'none';
  });
</script>

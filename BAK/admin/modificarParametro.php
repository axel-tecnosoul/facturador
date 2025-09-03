<?php
require("config.php");
if (empty($_SESSION['user'])) {
  header("Location: index.php");
  die("Redirecting to index.php");
}
require 'database.php';

$id = null;
if (!empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if (null==$id) {
  header("Location: listarParametros.php");
}

if (!empty($_POST)) {

  $modo_debug=0;
  
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $id=$_GET['id'];

  $sql = "UPDATE parametros set valor = ? where id = ?";
  $q = $pdo->prepare($sql);
  $q->execute([$_POST['valor'],$id]);

  if($id==8 and isset($_POST["fecha_desde"]) and $_POST["fecha_desde"]!=""){
    
    require 'funciones.php';

    $fecha_desde=$_POST["fecha_desde"];

    $sql = "SELECT vd.id AS id_venta_detalle,v.id_forma_pago,d.porcentaje,vd.precio,vd.subtotal,vd.id_modalidad,vd.deuda_proveedor,vd.pagado,p.id_proveedor FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id LEFT JOIN productos p ON vd.id_producto=p.id LEFT JOIN descuentos d ON v.id_descuento_aplicado=d.id WHERE v.id_forma_pago != 1 AND pagado = 0 and date(v.fecha_hora)>='$fecha_desde'";//WHERE p.id_proveedor=667;
    $q = $pdo->prepare($sql);
    $q->execute();
    $venta_detalle = $q->fetchAll(PDO::FETCH_ASSOC);
    echo $q->rowCount()."<br>";
    foreach ($venta_detalle as $data){
      
      $forma_pago = $data['id_forma_pago'];
      $modalidad = $data['id_modalidad'];
      $subtotal = $data['subtotal'];
      $idProveedor = $data['id_proveedor'];
      $deuda_proveedor = calcularDeudaProveedor($forma_pago,$modalidad,$subtotal);

      if(!$idProveedor and $modo_debug==1){
        var_dump($data);
      }

      //$data["deuda_proveedor"]=(float) $data["deuda_proveedor"];

      //echo $data["deuda_proveedor"]." - ".$deuda_proveedor." - ".$deuda_proveedor_viejo."<br>";
      if($deuda_proveedor==$data["deuda_proveedor"]){
        //la deuda del proveedor se calculo con el 80%
        if($modo_debug==1){
          var_dump("deuda proveedor es igual");
        }
      }else{
        if($modo_debug==1){
          echo "Deuda proveedor nueva: ".$deuda_proveedor."<br>";
          echo "Deuda proveedor actual: ".$data["deuda_proveedor"]."<br>";
          var_dump($data);
          var_dump($deuda_proveedor==$data["deuda_proveedor"]);
        }

        $sql = "UPDATE ventas_detalle set deuda_proveedor = ? where id = ?";
        $q = $pdo->prepare($sql);
        $q->execute([$deuda_proveedor,$data["id_venta_detalle"]]);
      }
    }

    $sql = "SELECT cd.id AS id_canje_detalle,d.porcentaje,cd.precio,cd.subtotal,cd.id_modalidad,cd.deuda_proveedor,cd.pagado,p.id_proveedor,c.id_venta,IF(c.id_venta IS NULL,1,v.id_forma_pago) AS id_forma_pago FROM canjes_detalle cd INNER JOIN canjes c ON cd.id_canje=c.id LEFT JOIN productos p ON cd.id_producto=p.id LEFT JOIN descuentos d ON c.id_descuento_aplicado=d.id LEFT JOIN ventas v ON c.id_venta=v.id WHERE pagado = 0 and date(c.fecha_hora)>='$fecha_desde' HAVING id_forma_pago!=1";//WHERE p.id_proveedor=667;
    $q = $pdo->prepare($sql);
    $q->execute();
    $canje_detalle = $q->fetchAll(PDO::FETCH_ASSOC);
    echo $q->rowCount()."<br>";
    foreach ($canje_detalle as $data){
      
      $forma_pago = $data['id_forma_pago'];
      $modalidad = $data['id_modalidad'];
      $subtotal = $data['subtotal'];
      $idProveedor = $data['id_proveedor'];
      $deuda_proveedor = calcularDeudaProveedor($forma_pago,$modalidad,$subtotal);

      if(!$idProveedor and $modo_debug==1){
        var_dump($data);
      }

      //$data["deuda_proveedor"]=(float) $data["deuda_proveedor"];

      //echo $data["deuda_proveedor"]." - ".$deuda_proveedor." - ".$deuda_proveedor_viejo."<br>";
      if($deuda_proveedor==$data["deuda_proveedor"]){
        //la deuda del proveedor se calculo con el 80%
        if($modo_debug==1){
          var_dump("deuda proveedor es igual");
        }
      }else{
        if($modo_debug==1){
          echo "Deuda proveedor nueva: ".$deuda_proveedor."<br>";
          echo "Deuda proveedor actual: ".$data["deuda_proveedor"]."<br>";
          var_dump($data);
          var_dump($deuda_proveedor==$data["deuda_proveedor"]);
        }

        $sql = "UPDATE canjes_detalle set deuda_proveedor = ? where id = ?";
        $q = $pdo->prepare($sql);
        $q->execute([$deuda_proveedor,$data["id_canje_detalle"]]);
      }
    }
  }
  
  if($modo_debug==1){
    die();
  }
  
  Database::disconnect();
  
  header("Location: listarParametros.php");
} else {
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT id,parametro,valor FROM parametros WHERE id = ? ";
  $q = $pdo->prepare($sql);
  $q->execute([$id]);
  $data = $q->fetch(PDO::FETCH_ASSOC);

  Database::disconnect();
}?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
    <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
  </head>
  <body class="light-only">
    <!-- Loader ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
      <?php include('header.php');?>
    
      <!-- Page Header Start-->
      <div class="page-body-wrapper">
        <?php include('menu.php');?>
        <!-- Page Sidebar Start-->
        <!-- Right sidebar Ends-->
        <div class="page-body">
        <div class="container-fluid">
            <div class="page-header">
              <div class="row">
                <div class="col-10">
                  <div class="page-header-left">
                    <h3><?php include("title.php"); ?></h3>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="#"><i data-feather="home"></i></a></li>
                      <li class="breadcrumb-item">Modificar Parametros</li>
                    </ol>
                  </div>
                </div>
                <!-- Bookmark Start-->
                <div class="col-2">
                  <div class="bookmark pull-right">
                    <ul>
                      <li><a  target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?php echo date('d-m-Y');?>"><i data-feather="calendar"></i></a></li>
                    </ul>
                  </div>
                </div>
                <!-- Bookmark Ends-->
              </div>
            </div>
          </div>
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Modificar Parametros</h5>
                  </div>
                  <form class="form theme-form" role="form" method="post" action="modificarParametro.php?id=<?php echo $id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
            
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Parámetro</label>
                            <div class="col-sm-9"><input name="parametro" type="text" class="form-control" value="<?php echo $data['parametro']; ?>" readonly></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Valor</label>
                            <div class="col-sm-9"><input name="valor" type="text" maxlength="99" class="form-control" value="<?php echo $data['valor']; ?>" required="required"></div>
                          </div><?php

                          if($id==8){
                            $fecha_desde=date("Y-m-d",strtotime(date("Y-m-01")." -1 month"))?>
                            <div class="form-group row d-flex align-items-center">
                              <label class="col-sm-3 col-form-label">Fecha desde la cual se actualizará el porcentaje de las prendas vendidas que aún no fueron pagadas </label>
                              <div class="col-sm-9"><input name="fecha_desde" type="date" class="form-control" value="<?=$fecha_desde?>" max="<?=date("Y-m-d")?>" required="required"></div>
                          </div><?php
                          }?>
              
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Modificar</button>
                        <a href='listarParametros.php' class="btn btn-light">Volver</a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
        <?php include("footer.php"); ?>
      </div>
    </div>
    <!-- latest jquery-->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap js-->
    <script src="assets/js/bootstrap/popper.min.js"></script>
    <script src="assets/js/bootstrap/bootstrap.js"></script>
    <!-- feather icon js-->
    <script src="assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="assets/js/icons/feather-icon/feather-icon.js"></script>
    <!-- Sidebar jquery-->
    <script src="assets/js/sidebar-menu.js"></script>
    <script src="assets/js/config.js"></script>
    <!-- Plugins JS start-->
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
    <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    <script>
      $(document).ready(function () {
        $("form").on("submit",function(e){
          //e.preventDefault();
          $("button[type='submit']").addClass("disabled").attr("disabled",true);
        })
      });
    </script>
  </body>
</html>
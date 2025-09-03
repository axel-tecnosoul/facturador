<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
if (!empty($_GET['id'])) {
  $id = $_GET['id'];
}
if ( null==$id ) {
  header("Location: listarCobros.php");
}

if ( !empty($_POST)) {
  
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $cotizacion_usd=$_POST['cotizacion_usd'];
  $monto_pesos=$_POST['monto_pesos'];
  $fecha_cobro=$_POST['fecha_cobro'];
  if($fecha_cobro==""){
    $fecha_cobro=null;
  }
  
  /*$sql = "UPDATE cobros set id_cliente = ?, fecha_factura = ?, fecha_cobro = ?, monto_pesos = ?, monto_dolares = ?, detalle = ? where id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['id_cliente'],$_POST['fecha_factura'],$fecha_cobro,$_POST['monto_pesos'],$_POST['monto_dolares'],$_POST['detalle'],$_GET['id']));*/

  $monto_dolares=0;
  if($cotizacion_usd>0){
    $monto_dolares=$monto_pesos/$cotizacion_usd;
  }

  $sql = "UPDATE cobros set id_cliente = ?, fecha_factura = ?, fecha_cobro = ?, monto_pesos = ?, cotizacion_usd = ?, monto_dolares = ?, detalle = ? where id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['id_cliente'],$_POST['fecha_factura'],$fecha_cobro,$_POST['monto_pesos'],$_POST['cotizacion_usd'],$monto_dolares,$_POST['detalle'],$_GET['id']));
  
  Database::disconnect();
  
  header("Location: listarCobros.php");

} else {
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $sql = "SELECT co.id AS id_cobro,co.fecha_factura,co.fecha_cobro,co.detalle,co.monto_pesos,co.cotizacion_usd,co.id_cliente FROM cobros co WHERE co.id = ? ";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  $data = $q->fetch(PDO::FETCH_ASSOC);
  
  Database::disconnect();
}?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
	  <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select-1.13.14/dist/css/bootstrap-select.min.css">
    <style>
      .multiselect{
        color:#212529 !important;
        background-color:#fff;
        border-color:#ccc;
      }
    </style>
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
                      <li class="breadcrumb-item">Modificar Cobro</li>
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
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Modificar Cobro</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="modificarCobro.php?id=<?=$id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-9">
                              <select name="id_cliente" class="form-control form-control-sm filtraTabla selectpicker multiselect w-100" data-live-search="true">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $sql = "SELECT c.id AS id_cliente,c.razon_social FROM clientes c";
                                foreach ($pdo->query($sql) as $row) {
                                  $selected="";
                                  if($data["id_cliente"]==$row["id_cliente"]){
                                    $selected=" selected";
                                  }?>
                                  <option value="<?=$row["id_cliente"]?>" <?=$selected?>><?=$row["razon_social"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha factura</label>
                            <div class="col-sm-9"><input name="fecha_factura" type="date" class="form-control" value="<?=$data["fecha_factura"]?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha de cobro</label>
                            <div class="col-sm-9"><input name="fecha_cobro" type="date" class="form-control" value="<?=$data["fecha_cobro"]?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Monto en pesos</label>
                            <div class="col-sm-9"><input name="monto_pesos" type="number" maxlength="99" class="form-control" value="<?=$data["monto_pesos"]?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cotizacion USD</label>
                            <div class="col-sm-9"><input name="cotizacion_usd" type="number" maxlength="99" class="form-control" value="<?=$data["cotizacion_usd"]?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Detalle</label>
                            <div class="col-sm-9"><input name="detalle" type="text" maxlength="99" class="form-control" value="<?=$data["detalle"]?>"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Modificar</button>
						            <a href='listarCobros.php' class="btn btn-light">Volver</a>
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
    <script src="vendor/bootstrap-select-1.13.14/dist/js/bootstrap-select.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/js/i18n/defaults-es_ES.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
	  <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
  </body>
</html>
<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';

$ventas = [];
$canjes = [];

if (!empty($_GET['id'])) {
  $id = $_GET['id'];
  $items = explode(',', $id);

  foreach ($items as $item) {
    if (strpos($item, 'v/') === 0) {
      $ventas[] = substr($item, 2);
    } elseif (strpos($item, 'c/') === 0) {
      $canjes[] = substr($item, 2);
    }
  }
}

if ( null==$id ) {
  header("Location: listarPagosRealizados.php");
}

if ( !empty($_POST)) {
  
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  if($_POST['condicion'] == 1){
    $sql = "UPDATE ventas_detalle set caja_egreso = ?, id_almacen = ?, id_forma_pago = ? where id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($_POST['tipo_caja'],$_POST['id_almacen'],$_POST['id_forma_pago'],$_POST['id']));
  }else{
    $sql= "UPDATE canjes_detalle set caja_egreso = ?, id_almacen = ?, id_forma_pago = ? where id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($_POST['tipo_caja'],$_POST['id_almacen'],$_POST['id_forma_pago'],$_POST['id']));
  }
  Database::disconnect();
  
  header("Location: listarPagosRealizados.php");

} else {
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $v = 0;
  if (!empty($ventas)) {
    foreach ($ventas as $value)	{
      $sql = "SELECT vd.caja_egreso, vd.id_almacen, vd.id_forma_pago, p.descripcion, p.codigo, pr.apellido, pr.nombre, pr.id,date_format(vd.fecha_hora_pago,'%d/%m/%Y %H:%i') AS fecha_hora_pago FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id INNER JOIN productos p ON vd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id WHERE vd.id = ? ";
      $q = $pdo->prepare($sql);
      $q->execute(array($value));
      $data = $q->fetch(PDO::FETCH_ASSOC);

      $checkedCajaChica="";
      if ($data["caja_egreso"]=="Chica") {
        $checkedCajaChica="checked='checked'";
      }

      $checkedCajaGrande="";
      if ($data["caja_egreso"]=="Grande") {
        $checkedCajaGrande="checked='checked'";
      }
      $v = 1;
    }
  }
  
  if (!empty($canjes)) {
    foreach ($canjes as $value)	{
      $sql2 = "SELECT cd.caja_egreso, cd.id_almacen, cd.id_forma_pago, p.descripcion, p.codigo, pr.apellido, pr.nombre, pr.id,date_format(cd.fecha_hora_pago,'%d/%m/%Y %H:%i') AS fecha_hora_pago FROM canjes_detalle cd INNER JOIN canjes v ON cd.id_canje=v.id INNER JOIN productos p ON cd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id WHERE cd.id = ? ";
      $q2 = $pdo->prepare($sql2);
      $q2->execute(array($value));
      $data2 = $q2->fetch(PDO::FETCH_ASSOC);

      $checkedCajaChica="";
      if ($data2["caja_egreso"]=="Chica") {
        $checkedCajaChica="checked='checked'";
      }

      $checkedCajaGrande="";
      if ($data2["caja_egreso"]=="Grande") {
        $checkedCajaGrande="checked='checked'";
      }
    }
  }      
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
                      <li class="breadcrumb-item">Modificar Pago Realizado</li>
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
                    <h5>Modificar Pago Realizado</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="modificarPagoRealizado.php?id=<?php echo $id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">

                          <div class="form-group row">
                            <div class="col-sm-3">Fecha y hora:</div>
                            <div class="col-sm-9"><?php echo ($v == 1) ?  $data["fecha_hora_pago"] : $data2["fecha_hora_pago"] ?>hs.</div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Proveedor:</div>
                            <div class="col-sm-9"><?php echo($v == 1) ?  "(".$data["id"].") ".$data["nombre"]." ".$data["apellido"] :  "(".$data2["id"].") ".$data2["nombre"]." ".$data2["apellido"]?></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Producto:</div>
                            <div class="col-sm-9"><?php echo($v == 1) ?  "(".$data["codigo"].") ".$data["descripcion"] :  "(".$data2["codigo"].") ".$data2["descripcion"]?></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Caja: </div>
                            <div class="col-sm-9">
                              <label class="d-block" for="edo-ani1">
                                <input class="radio_animated" value="Grande" required <?=$checkedCajaGrande?> id="edo-ani1" type="radio" name="tipo_caja"><label for="edo-ani1">Grande</label>
                              </label>
                              <label class="d-block" for="edo-ani">
                                <input class="radio_animated" value="Chica" required <?=$checkedCajaChica?> id="edo-ani" type="radio" name="tipo_caja"><label for="edo-ani">Chica</label>
                              </label>
                            </div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Almacen: </div>
                            <div class="col-sm-9">
                              <select name="id_almacen" style="width: 100%;" class="js-example-basic-single" required="required">
                                <option value="">Seleccione</option><?php
                                $pdo = Database::connect();
                                $sql = " SELECT id, almacen FROM almacenes";
                                foreach ($pdo->query($sql) as $row) {
                                  $selected="";
                                  $almacen = "";
                                  if ($v == 1){
                                    $almacen = $data["id_almacen"];
                                  }else{
                                    $almacen = $data2["id_almacen"];
                                  }
                                  if($almacen==$row["id"]){
                                    $selected="selected";
                                  }?>
                                  <option value="<?=$row["id"]?>" <?=$selected?>><?=$row["almacen"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Forma de Pago: </div>
                            <div class="col-sm-9">
                              <select name="id_forma_pago" id="id_forma_pago" style="width: 100%;" class="js-example-basic-single" required="required">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT id, forma_pago FROM forma_pago WHERE 1";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  $selected="";
                                  $forma_pago = "";
                                  if ($v == 1){
                                    $forma_pago = $data["id_forma_pago"];
                                  }else{
                                    $forma_pago = $data2["id_forma_pago"];
                                  }
                                  if($forma_pago==$fila["id"]){
                                    $selected="selected";
                                  }?>
                                  <option value='<?=$fila['id']?>' <?=$selected?>><?=$fila['forma_pago']?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                              <input type="hidden" name='condicion' id='condicion' value=<?= $v ?>>
                              <input type="hidden" name='id' id='id' value=<?php echo($v == 1) ? implode(',', $ventas) : implode(',', $canjes); ?>>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Modificar</button>
						            <a href='listarPagosRealizados.php' class="btn btn-light">Volver</a>
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
    <script src="assets/js/typeahead/handlebars.js"></script>
    <script src="assets/js/typeahead/typeahead.bundle.js"></script>
    <script src="assets/js/typeahead/typeahead.custom.js"></script>
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <script src="assets/js/typeahead-search/handlebars.js"></script>
    <script src="assets/js/typeahead-search/typeahead-custom.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
	<script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
  </body>
</html>
<?php
    require("config.php");
    if(empty($_SESSION['user']))
    {
        header("Location: index.php");
        die("Redirecting to index.php"); 
    }
	
	require 'database.php';
	
	if ( !empty($_POST)) {
		
		// insert data
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$sql = "INSERT INTO `descuentos`(`descripcion`, `vigencia_desde`, `vigencia_hasta`, `minimo_compra`, `minimo_cantidad_prendas`, `monto_fijo`, `porcentaje`, `activo`) VALUES (?,?,?,?,?,?,?,1)";
		$q = $pdo->prepare($sql);
		$q->execute(array($_POST['descripcion'],$_POST['vigencia_desde'],$_POST['vigencia_hasta'],$_POST['minimo_compra'],$_POST['minimo_cantidad_prendas'],$_POST['monto_fijo'],$_POST['porcentaje']));
    $id_descuento = $pdo->lastInsertId();
		
    $formas_pago = $_POST['forma_pago'];
    foreach($formas_pago as $valor) {
      $sql = "INSERT INTO `descuentos_x_formapago`(`id_descuento`, `id_forma_pago`, `fecha_hora`) VALUES (?,?, NOW())";
      $q = $pdo->prepare($sql);
      $q->execute(array($id_descuento, $valor));

    }
    
		Database::disconnect();
		
		header("Location: listarDescuentos.php");
	}
	
?>
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
                      <li class="breadcrumb-item">Nuevo Descuento</li>
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
                    <h5>Nuevo Descuento</h5>
                  </div>
				  <form class="form theme-form" role="form" method="post" action="nuevoDescuento.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
						
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Detalle del Descuento</label>
								<div class="col-sm-9"><input name="descripcion" type="text" maxlength="199" class="form-control" value="" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Vigencia Desde</label>
								<div class="col-sm-9"><input name="vigencia_desde" type="date" maxlength="99" class="form-control" value="" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Vigencia Hasta</label>
								<div class="col-sm-9"><input name="vigencia_hasta" type="date" maxlength="99" class="form-control" value="" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Monto de Compra Mínimo</label>
								<div class="col-sm-9"><input name="minimo_compra" type="number" step="0.01" maxlength="99" class="form-control" value="0" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Cantidad Mínima de Ítems</label>
								<div class="col-sm-9"><input name="minimo_cantidad_prendas" type="number" step="0" maxlength="99" class="form-control" value="0" required="required"></div>
							</div>
							<div class="form-group row d-none">
								<label class="col-sm-3 col-form-label">Monto Descuento Fijo</label>
								<div class="col-sm-9"><input name="monto_fijo" type="number" step="0.01" maxlength="99" class="form-control" value="0" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Porcentaje Descuento</label>
								<div class="col-sm-9"><input name="porcentaje" type="number" step="0.1" maxlength="99" class="form-control" value="0" required="required"></div>
							</div>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Forma de Pago</label>
                <div class="col-sm-9">
                <select name="forma_pago[]" id="forma_pago" class="form-control form-control-sm forma_pago selectpicker" data-style="multiselect" data-selected-text-format="count > 1" multiple><?php
                  $pdo = Database::connect();
                  $sql = " SELECT id, forma_pago FROM forma_pago WHERE activo = 1";
                  foreach ($pdo->query($sql) as $row) {?>
                    <option value="<?=$row["id"]?>" <?php //if($row["id"]==1) echo "selected"?>><?=$row["forma_pago"]?></option><?php
                  }
                  Database::disconnect();?>
                </select>
                </div>
              </div>
							
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Crear</button>
						<a href="listarDescuentos.php" class="btn btn-light">Volver</a>
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
    <script>
      $(document).ready(function() {
          $('.forma_pago').select2();
      });
    </script>
  </body>
</html>
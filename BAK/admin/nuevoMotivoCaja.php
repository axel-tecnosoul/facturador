<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
if ( !empty($_POST)) {
  // insert data

  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $sql = "INSERT INTO motivos_salidas_caja (motivo,id_tipo_motivo, tipo_gasto, aparece_balance) VALUES (?,?,?,?)";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['motivo'],$_POST["id_tipo_motivo"],$_POST['tipo_gasto'],$_POST['aparece_balance']));
  
  Database::disconnect();
  
  header("Location: listarMotivosCaja.php");
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
                      <li class="breadcrumb-item">Nuevo Motivo egreso de caja</li>
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
                    <h5>Nueva Motivo egreso de caja</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="nuevoMotivoCaja.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tipo de motivo</label>
                            <div class="col-sm-9">
                              <select name="id_tipo_motivo" id="id_tipo_motivo" class="js-example-basic-single col-sm-12" required>
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT id, nombre FROM tipos_motivos ORDER BY nombre";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  echo ">".$fila['nombre']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Motivo egreso de caja</label>
                            <div class="col-sm-9"><input name="motivo" type="text" maxlength="99" class="form-control" value="" required="required"></div>
                          </div>
                          
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tipo de Gasto</label>
                            <div class="col-sm-9">
                              <label class="d-block" for="edo-ani">
                                <input name="tipo_gasto" type="radio" class="radio_animated" id="edo-ani" value="Fijo" checked required>
                                <label for="edo-ani1">Fijo</label>
                              </label>
                              <label class="d-block" for="edo-ani1">
                                <input name="tipo_gasto" type="radio" class="radio_animated" id="edo-ani1" value="Variable" required>
                                <label for="edo-ani1">Variable</label>
                              </label>
                            </div>
                          </div>

                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Aparece Balance</label>
                            <div class="col-sm-9">
                              <label class="d-block" for="edo-ani">
                                <input name="aparece_balance" type="radio" class="radio_animated"id="edo-ani" value="1" checked required>
                                <label for="edo-ani1">SI</label>
                              </label>
                              <label class="d-block" for="edo-ani1">
                                <input name="aparece_balance" type="radio" class="radio_animated" id="edo-ani1" value="0" required>
                                <label for="edo-ani1">NO</label>
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Crear</button>
						            <a href="listarMotivosCaja.php" class="btn btn-light">Volver</a>
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
  </body>
</html>
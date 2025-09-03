<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';

$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if ( null==$id ) {
  header("Location: listarStock.php");
}

if ( !empty($_POST)) {

  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if($_POST["nueva_cantidad"]>0){
    $sql = "UPDATE stock set cantidad = ? where id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($_POST['nueva_cantidad'],$_GET['id']));
  }
  
  $sql = "UPDATE stock set id_modalidad = ?, inventario_ok = ?, id_estado = ? where id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['id_modalidad'],$_POST["inventario_ok"],$_POST["id_estado"],$_GET['id']));
  
  Database::disconnect();
  
  header("Location: listarStock.php");

} else {
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT s.id, s.id_producto, s.id_almacen, s.cantidad, s.id_modalidad, s.inventario_ok, s.id_estado, p.descripcion FROM stock s INNER JOIN productos p ON s.id_producto=p.id WHERE s.id = ? ";
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
                      <li class="breadcrumb-item">Ajustar Cantidad Stock</li>
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
                    <h5>Modificar Stock</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="modificarStock.php?id=<?php echo $id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
								            <label class="col-sm-3 col-form-label">Producto</label>
								            <div class="col-sm-9"><label><?php echo $data['descripcion'];?></label></div>
							            </div>
                          <div class="form-group row">
								            <label class="col-sm-3 col-form-label">Cantidad Anterior</label>
								            <div class="col-sm-9"><input name="cantidad_anterior" type="number" class="form-control" value="<?php echo $data['cantidad']; ?>" readonly="readonly"></div>
							            </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Nueva Cantidad</label>
                            <div class="col-sm-9"><input name="nueva_cantidad" type="number" step="1" min="0" class="form-control" value=""></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Modalidad</label>
                            <div class="col-sm-9">
                            <select name="id_modalidad" id="id_modalidad" class="js-example-basic-single col-sm-12" required="required">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT `id`, `modalidad` FROM `modalidades` WHERE 1";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  if ($fila['id'] == $data['id_modalidad']) {
                                    echo " selected ";
                                  }
                                  echo ">".$fila['modalidad']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Inventario OK</label>
                            <div class="col-sm-9">
                              <label class="d-block" for="inventario_ok_si">
                                <input class="radio_animated" value="1" <?php if($data["inventario_ok"]==1) echo "checked"?> required id="inventario_ok_si" type="radio" name="inventario_ok"><label for="inventario_ok_si">Si</label>
                              </label>
                              <label class="d-block" for="inventario_ok_no">
                                <input class="radio_animated" value="0" <?php if($data["inventario_ok"]==0) echo "checked"?> required id="inventario_ok_no" type="radio" name="inventario_ok"><label for="inventario_ok_no">No</label>
                              </label>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Estado</label>
                            <div class="col-sm-9">
                              <select name="id_estado" id="id_estado" class="js-example-basic-single col-sm-12" required="required">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT id, estado FROM estados_stock";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  $selected="";
                                  if($data["id_estado"]==$fila['id']){
                                    $selected="selected";
                                  }
                                  echo "<option value='".$fila['id']."' $selected>".$fila['estado']."</option>";
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
                        <button class="btn btn-primary" type="submit">Ajustar</button>
						              <a onclick="document.location.href='listarStock.php'" class="btn btn-light">Volver</a>
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
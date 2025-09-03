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
  header("Location: listarProductos.php");
}

if ( !empty($_POST)) {
  
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $actualizar_precio_viejo="";
  if($_POST["precio_anterior"]!=$_POST['precio']){
    $actualizar_precio_viejo=" precio_anterior=precio,";
  }
  
  $sql = "UPDATE productos set codigo = ?, id_categoria = ?, descripcion = ?, id_proveedor = ?, $actualizar_precio_viejo precio = ?, precio_costo = ?, activo = ? where id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['codigo'],$_POST['id_categoria'],$_POST['descripcion'],$_POST['id_proveedor'],$_POST['precio'],$_POST['precio_costo'],$_POST['activo'],$_GET['id']));
  
  $sql = "SELECT `cb` FROM `productos` WHERE id = ? ";
  $q = $pdo->prepare($sql);
  $q->execute(array($_GET['id']));
  $data = $q->fetch(PDO::FETCH_ASSOC);
  if (empty($data['cb'])) {
    $cb = microtime(true)*10000;	
    $sql = "update `productos` set `cb` = ? where id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($cb,$_GET['id']));
  }
  
  Database::disconnect();
  
  header("Location: listarProductos.php");

} else {
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT id, codigo, id_categoria, descripcion, id_proveedor, precio, precio_costo, activo FROM productos WHERE id = ? ";
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
                      <li class="breadcrumb-item">Modificar Producto</li>
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
                    <h5>Modificar Producto</h5>
                  </div>
				  <form class="form theme-form" role="form" method="post" action="modificarProducto.php?id=<?php echo $id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
						
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Código</label>
								<div class="col-sm-9"><input name="codigo" type="text" maxlength="99" class="form-control" value="<?php echo $data['codigo']; ?>" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Categoría</label>
								<div class="col-sm-9">
								<select name="id_categoria" id="id_categoria" class="js-example-basic-single col-sm-12" required="required">
								<option value="">Seleccione...</option>
								<?php 
								$pdo = Database::connect();
								$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
								$sqlZon = "SELECT `id`, `categoria` FROM `categorias` WHERE activa = 1";
								$q = $pdo->prepare($sqlZon);
								$q->execute();
								while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
									echo "<option value='".$fila['id']."'";
									if ($fila['id'] == $data['id_categoria']) {
										echo " selected ";
									}
									echo ">".$fila['categoria']."</option>";
								}
								Database::disconnect();
								?>
								</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Descripción</label>
								<div class="col-sm-9"><input name="descripcion" type="text" maxlength="99" class="form-control" value="<?php echo htmlentities($data['descripcion']); ?>" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Proveedor</label>
								<div class="col-sm-9">
								<select name="id_proveedor" id="id_proveedor" class="js-example-basic-single col-sm-12" required="required">
								<option value="">Seleccione...</option>
								<?php 
								$pdo = Database::connect();
								$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
								$sqlZon = "SELECT `id`, `nombre`, `apellido` FROM `proveedores` WHERE activo = 1";
								$q = $pdo->prepare($sqlZon);
								$q->execute();
								while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
									echo "<option value='".$fila['id']."'";
									if ($fila['id'] == $data['id_proveedor']) {
										echo " selected ";
									}
									echo ">".$fila['nombre'].' '.$fila['apellido']."</option>";
								}
								Database::disconnect();?>
								</select>
								</div>
							</div>
							<div class="form-group row"><?php
                $readonly_precio="";
                if($_SESSION["user"]["id_perfil"]!=1){
                  //$precio_costo=0;
                  $readonly_precio="readonly";
                }?>
								<label class="col-sm-3 col-form-label">Precio</label>
								<div class="col-sm-9">
                  <input name="precio" type="number" step="0.01" min="0" class="form-control" value="<?php echo $data['precio']; ?>" required="required" <?=$readonly_precio?>>
                  <input type="hidden" name="precio_anterior" value="<?php echo $data['precio']; ?>">
                </div>
							</div><?php
              $precio_costo=$data['precio_costo'];
              //$readonly_precio="";
              if($_SESSION["user"]["id_perfil"]!=1 and $data['id_proveedor']==1091){
                $precio_costo=0;
                //$readonly_precio="readonly";
              }?>
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Precio de costo</label>
                <div class="col-sm-9"><input name="precio_costo" type="number" step="0.01" min="0" class="form-control" value="<?=$precio_costo?>" required="required" <?=$readonly_precio?>></div>
              </div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Activo</label>
								<div class="col-sm-9">
								<select name="activo" id="activo" class="js-example-basic-single col-sm-12" required="required">
								<option value="">Seleccione...</option>
								<option value="1" <?php if ($data['activo']==1) echo " selected ";?>>Si</option>
								<option value="0" <?php if ($data['activo']==0) echo " selected ";?>>No</option>
								</select>
								</div>
							</div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Modificar</button>
						<a onclick="document.location.href='listarProductos.php'" class="btn btn-light">Volver</a>
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
<?php
    require("config.php");
    if(empty($_SESSION['proveedor']))
    {
        header("Location: loginProveedores.php");
        die("Redirecting to loginProveedores.php"); 
    }
	
	require 'database.php';

	$id = $_SESSION['proveedor']['id'];
	
	if ( null==$id ) {
		header("Location: loginProveedores.php");
	}
	
	if ( !empty($_POST)) {
		
		// insert data
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$sql = "update `proveedores` set `email` = ?, `dni` = ?, `nombre` = ?, `apellido` = ?, `telefono` = ? where id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($_POST['email'],$_POST['dni'],$_POST['nombre'],$_POST['apellido'],$_POST['telefono'],$_GET['id']));
		
		if (!empty($_POST['clave'])) {
			$sql = "update `proveedores` set `clave`=? where id =?";
			$q = $pdo->prepare($sql);
			$q->execute(array($_POST['clave'],$_GET['id']));
		}
		
		Database::disconnect();
		
		header("Location: logout_proveedor.php");
	
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
	  <?php include('header_proveedor.php');?>
	  
      <!-- Page Header Start-->
      <div class="page-body-wrapper">
		<?php include('menu_proveedor.php');?>
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
                      <li class="breadcrumb-item">Modificar Datos Personales</li>
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
                    <h5>Modificar Datos Personales</h5>
                  </div>
				  <form class="form theme-form" role="form" method="post" action="listarDatosProveedor.php?id=<?php echo $id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">E-Mail</label>
								<div class="col-sm-9"><input name="email" type="email" maxlength="99" class="form-control" value="<?php echo $_SESSION['proveedor']['email']; ?>" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Teléfono</label>
								<div class="col-sm-9"><input name="telefono" type="text" maxlength="99" class="form-control" value="<?php echo $_SESSION['proveedor']['telefono']; ?>" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">DNI</label>
								<div class="col-sm-9"><input name="dni" type="text" maxlength="99" class="form-control" value="<?php echo $_SESSION['proveedor']['dni']; ?>" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Nombre</label>
								<div class="col-sm-9"><input name="nombre" type="text" maxlength="99" class="form-control" value="<?php echo $_SESSION['proveedor']['nombre']; ?>" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Apellido</label>
								<div class="col-sm-9"><input name="apellido" type="text" maxlength="99" class="form-control" value="<?php echo $_SESSION['proveedor']['apellido']; ?>" required="required"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Contraseña</label>
								<div class="col-sm-9"><input class="form-control" name="clave" id="password" type="password"></div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Repetir Contraseña</label>
								<div class="col-sm-9"><input class="form-control" name="clave2" id="confirm_password" type="password"></div>
							</div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Modificar</button>
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
	var password = document.getElementById("password")
	  , confirm_password = document.getElementById("confirm_password");

	function validatePassword(){
	  if(password.value != confirm_password.value) {
		confirm_password.setCustomValidity("Las claves no coinciden");
	  } else {
		confirm_password.setCustomValidity('');
	  }
	}

	password.onchange = validatePassword;
	confirm_password.onkeyup = validatePassword;
	</script>
  </body>
</html>
<?php 
    require("config.php"); 
	require 'database.php';
    if ( !empty($_POST)) {
		
		// insert data
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$sql = "INSERT INTO `proveedores`(`email`, `clave`, `dni`, `nombre`, `apellido`, `activo`, `fecha_alta`, `telefono`) VALUES (?,?,?,?,?,1,now(),?)";
		$q = $pdo->prepare($sql);
		$q->execute(array($_POST['email'],$_POST['clave'],$_POST['dni'],$_POST['nombre'],$_POST['apellido'],$_POST['telefono']));
		
		Database::disconnect();
		
		header("Location: loginProveedores.php");
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="endless admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, endless admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="pixelstrap">
    <link rel="icon" href="assets/images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <title><?php include ("title.php");?></title>
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Font Awesome-->
    <link rel="stylesheet" type="text/css" href="assets/css/fontawesome.css">
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="assets/css/icofont.css">
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="assets/css/themify.css">
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="assets/css/flag-icon.css">
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="assets/css/feather-icon.css">
    <!-- Plugins css start-->
    <!-- Plugins css Ends-->
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link id="color" rel="stylesheet" href="assets/css/light-1.css" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
  </head>
  <body>
    <!-- Loader starts-->
    <div class="loader-wrapper">
      <div class="loader bg-white">
        <div class="whirly-loader"> </div>
      </div>
    </div>
    <!-- Loader ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
      <div class="container-fluid p-0">
        <!-- login page start-->
        <div class="authentication-main">
          <div class="row">
            <div class="col-md-12">
              <div class="auth-innerright">
                <div class="authentication-box">
                  <div class="text-center"><img src="assets/images/logoLogin.png" width="250px" alt=""></div>
                  <div class="card mt-4">
                    <div class="card-body">
                      <div class="text-center">
                        <h4>Registro de Nuevo Proveedor</h4>
                        <h6>Ingrese los datos que se solicitan a continuación </h6>
                      </div>
					  <form class="theme-form" role="form" action="nuevoProveedor.php" method="post">
                        <div class="form-group row">
							<label class="col-sm-3 col-form-label">E-Mail</label>
							<div class="col-sm-9"><input name="email" type="email" maxlength="99" class="form-control" required="required"></div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Teléfono</label>
							<div class="col-sm-9"><input name="telefono" type="text" maxlength="99" class="form-control" required="required"></div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">DNI</label>
							<div class="col-sm-9"><input name="dni" type="text" maxlength="99" class="form-control" required="required"></div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Nombre</label>
							<div class="col-sm-9"><input name="nombre" type="text" maxlength="99" class="form-control" required="required"></div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Apellido</label>
							<div class="col-sm-9"><input name="apellido" type="text" maxlength="99" class="form-control" required="required"></div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Contraseña</label>
							<div class="col-sm-9"><input class="form-control" name="clave" id="password" type="password"></div>
						</div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label">Repetir Contraseña</label>
							<div class="col-sm-9"><input class="form-control" name="clave2" id="confirm_password" type="password"></div>
						</div>
						<div class="form-group form-row mt-3 mb-0">
						  <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- login page end-->
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
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
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
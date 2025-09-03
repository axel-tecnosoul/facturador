<?php
require("admin/config.php");
require 'admin/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include("head.php"); ?>
</head>
<body>
<div id="loader-wrapper">
	<div id="loader">
		<div class="dot"></div>
		<div class="dot"></div>
		<div class="dot"></div>
		<div class="dot"></div>
		<div class="dot"></div>
		<div class="dot"></div>
		<div class="dot"></div>
	</div>
</div>
<header id="tt-header">
	<?php include("header.php"); ?>
</header>
<br>

<!-- Section: Contact v.1 -->
<section class="my-5">

  <!-- Section heading -->
  <h2 class="h1-responsive font-weight-bold text-center my-5">Sacá tu turno para vender</h2>
  <!-- Section description -->
  

  <!-- Grid row -->
  <div class="row">

    <!-- Grid column -->
    <div class="col-lg-6 mb-lg-4 mb-4 centrar-div2">

      <!-- Form with header -->
      <div class="card">
        <div class="card-body">
          <!-- Header -->
          
          <form id="" class="" method="post" action="emitirTurno.php">
									
          <!-- Body -->
		  <div class="md-form">
            <label for="form-suc">Sucursal</label>
            <select name="id_almacen" id="id_almacen" class="form-control" required="required">
			<option value="">Seleccione...</option>
			<?php 
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sqlZon = "SELECT `id`, `almacen` FROM `almacenes` WHERE activo = 1";
			$q = $pdo->prepare($sqlZon);
			$q->execute();
			while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
				echo "<option value='".$fila['id']."'";
				echo ">".$fila['almacen']."</option>";
			}
			Database::disconnect();
			?>
			</select>
          </div>
		  
		  <div class="md-form">
            <label for="form-cant">Cantidad</label>
            <select name="cantidad" id="form-cant" class="form-control" required="required">
				<option value="">Seleccione...</option>
				<option value="1 a 15 prendas">1 a 15 prendas</option>
				<option value="16 a 35 prendas">16 a 35 prendas</option>
				<option value="36 a 50 prendas">36 a 50 prendas</option>
				<option value="51 a 70 prendas">51 a 70 prendas</option>
				<option value="71 a 85 prendas">71 a 85 prendas</option>
				<option value="Más de 86 prendas">Más de 86 prendas</option>

			</select>
            
          </div>
		  <div class="md-form">
            <label for="form-fec">Fecha</label>
            <input type="date" name="fecha" id="form-fec" class="form-control" required="required">
            
          </div>
		  <div class="md-form">
            <label for="form-hora">Hora</label>
            <input type="time" name="hora" id="form-hora" class="form-control" required="required">
            
          </div>
		  <div class="md-form">
            <label for="form-dni">DNI</label>
            <input type="text" name="dni" id="form-dni" class="form-control" required="required">
            
          </div>
          <div class="md-form">
            <label for="form-name">Nombre y Apellido</label>
            <input type="text" name="nombre" id="form-name" class="form-control" required="required">
            
          </div>
          <div class="md-form">
            <label for="form-email">E-mail</label>
            <input type="email" name="email" id="form-email" class="form-control" required="required">
            
          </div>
          <div class="md-form">
            <label for="form-tel">Teléfono</label>
            <input type="text" name="telefono" id="form-tel" class="form-control" required="required">
            
          </div>
		  <br>
          <div class="text-center">
            <button type="submit" class="btn btn-light-blue">Solicitar Turno</button>
          </div>
		  </form>
        </div>
      </div>
      <!-- Form with header -->

    </div>
    <!-- Grid column -->

    
  </div>
  <!-- Grid row -->

</section>
<!-- Section: Contact v.1 -->
			<br>
			<br>
			<br>
			<br>
			<?php include("testimonios.php"); ?>
				
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
				
			<hr class="style13">
			<br>
			<br>
			<br>
			<br>
			<?php include("suscribite.php"); ?>
<br>
<br>
<br>
<footer id="">
	<?php include("footer.php"); ?>
</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="external/jquery/jquery.min.js"><\/script>')</script>
<script defer src="js/bundle.js"></script>

<script defer src="separate-include/single-product/single-product.js"></script>
<script src="separate-include/portfolio/portfolio.js"></script>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>
<a href="#" class="tt-back-to-top">Volver al inicio</a>
</body>
</html>
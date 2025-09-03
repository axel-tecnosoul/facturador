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
  <h2 class="h1-responsive font-weight-bold text-center my-5">CONTACTANOS</h2>
  <!-- Section description -->
  

  <!-- Grid row -->
  <div class="row">

    <!-- Grid column -->
    <div class="col-lg-5 mb-lg-0 mb-4 centrar-div3">

      <!-- Form with header -->
      <div class="card">
        <div class="card-body">
          <!-- Header -->
          <div class="form-header blue accent-1">
            <h3 class="mt-2"><i class="fas fa-envelope"></i> Escribinos!</h3>
          </div>
          <form id="" class="" method="post" action="contactar.php">
									
          <!-- Body -->
          <div class="md-form">
            
            <input type="text" name="nombre" id="form-name" class="form-control" required="required">
            <label for="form-name">Nombre</label>
          </div>
          <div class="md-form">
            
            <input type="email" name="email" id="form-email" class="form-control" required="required">
            <label for="form-email">E-mail</label>
          </div>
          <div class="md-form">
            
            <input type="text" name="asunto" id="form-Subject" class="form-control" required="required">
            <label for="form-Subject">Asunto</label>
          </div>
          <div class="md-form">
           
            <textarea id="form-text" name="mensaje" class="form-control md-textarea" rows="3" required="required"></textarea>
            <label for="form-text">Mensaje</label>
          </div>
		  <br>
          <div class="text-center">
            <button type="submit" class="btn btn-light-blue">Envíar</button>
          </div>
		  </form>
        </div>
      </div>
      <!-- Form with header -->

    </div>
    <!-- Grid column -->

    <!-- Grid column -->
    <div class="col-lg-6">

      <!--Google map-->
      <div id="map-container-section" class="z-depth-1-half map-container-section mb-4" style="height: 400px">
		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3286.103930128673!2d-58.55610208423821!3d-34.550923862214546!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95bcb77f5d865585%3A0x8b26729ac6873fed!2sMi%20Roperito!5e0!3m2!1ses!2sar!4v1653423717406!5m2!1ses!2sar" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
      <!-- Buttons-->
      <div class="row text-center">
        <div class="col">
          <a class="btn-floating blue accent-1">
            <i class="fas fa-map-marker-alt"></i>
          </a>
          <p><a target="_blank" href="https://goo.gl/maps/Sk3SvYbBrGsAB4kS6">Villa Ballester</a></p>
          <p class="mb-md-0">Independencia 4701</p>
        </div>
		<div class="col">
			<a class="btn-floating blue accent-1">
			  <i class="fas fa-map-marker-alt"></i>
			</a>
			<p><a target="_blank" href="https://goo.gl/maps/QxwvCPqcKCkMEGVm8">San Isidro</a></p>
			<p class="mb-md-0">Av. Fondo de la Legua 425</p>
		  </div>
		<div class="col">
			<a class="btn-floating blue accent-1">
			  <i class="fas fa-map-marker-alt"></i>
			</a>
			<p><a target="_blank" href="https://goo.gl/maps/8b3dKr9Vce7Ve9Lk8">Martinez</a></p>
			<p class="mb-md-0">Parana 3922</p>
		  </div>
        <div class="col">
          <a class="btn-floating blue accent-1">
            <i class="fas fa-phone"></i>
          </a>
          <p>Horarios</p>
          <p class="mb-md-0">Lunes a Sábados de 10:00 - 19:30 hs </p>
        </div>
        <div class="col">
          <a class="btn-floating blue accent-1">
            <i class="fas fa-envelope"></i>
          </a>
          <p>ballester@miroperito.ar</p>
          <p class="mb-0">sanisidro@miroperito.ar</p>
		  <p class="mb-0">martinez@miroperito.ar</p>
		  <p class="mb-0">proveedoras@miroperito.ar</p>
		  <p class="mb-0">vende@miroperito.ar</p>
		  <p class="mb-0">devoluciones@miroperito.ar</p>
        </div>
      </div>

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
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

  $modoDebug=0;

  if ($modoDebug==1) {
    $pdo->beginTransaction();
    var_dump($_POST);
  }

  $porcentaje=$_POST["porcentaje"];
  $redondeo=$_POST["redondeo"];

  $accion="(p.precio * (1+($porcentaje/100)))";
  if($redondeo>0){
    $accion="CEIL($accion / $redondeo) * $redondeo";
  }

  $sql = " UPDATE productos p INNER JOIN stock s ON p.id = s.id_producto SET p.precio = $accion";// WHERE p.id = 1
  //$sql = " UPDATE productos p INNER JOIN stock s ON p.id = s.id_producto SET p.precio = $accion WHERE p.id = 12539";//
  $q = $pdo->prepare($sql);
  $q->execute();

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";  

    $sql4 = "SELECT p.id,precio FROM productos p INNER JOIN stock s ON p.id = s.id_producto";// WHERE id = 12539 
    /*$q4 = $pdo->prepare($sql4);
    $q4->execute();
    $data4 = $q4->fetch(PDO::FETCH_ASSOC);
    var_dump($data4);*/
    foreach ($pdo->query($sql4) as $data4) {
      echo $data4["id"]." - ".$data4["precio"]."<br>";
    }

  }

  /*if($_POST["nueva_cantidad"]>0){
    $sql = "UPDATE stock set cantidad = ? where id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($_POST['nueva_cantidad'],$_GET['id']));
  }
  
  $sql = "UPDATE stock set id_modalidad = ? where id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['id_modalidad'],$_GET['id']));*/
  

  if ($modoDebug==1) {
    $pdo->rollBack();
    die();
  }
  Database::disconnect();
  
  header("Location: listarStock.php");

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
                      <li class="breadcrumb-item">Actualizar precio masivo</li>
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
                    <h5>Actualizar precio masivo</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="actualizarPrecioMasivo.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Ejemplo</label>
                            <div class="col-sm-3"><input type="number" id="valor_probar" class="form-control" value="<?=$ej=rand(1000,20000)?>" step="1" min="0"></div>
                          </div>
							            <div class="form-group row">
								            <label class="col-sm-3 col-form-label">Porcentaje ajustar</label>
								            <div class="col-sm-3"><input name="porcentaje" id="porcentaje" type="number" class="form-control" required value=""></div>
                            <label class="col-sm-6 col-form-label" id="ejemplo_porcentaje">$ <?=number_format($ej,2,",",".")?></label>
							            </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Valor de redondeo superior</label>
                            <div class="col-sm-3"><input name="redondeo" id="redondeo" type="number" class="form-control" required value="100" step="1" min="1"></div>
                            <label class="col-sm-6 col-form-label" id="ejemplo_redondeo">$ <?=number_format((ceil($ej / 100) * 100),2,",",".")?></label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Actualizar</button>
						            <a href='listarStock.php' class="btn btn-light">Volver</a>
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
    <script>
      $(document).ready(function () {
        $("input[type='number']").on("input",function(){
        //$("input[type='number']").keypress(function(){
          let porcentaje=$("#porcentaje").val()
          if(isNaN(porcentaje) || porcentaje==""){
            porcentaje=0;
          }
          porcentaje=parseFloat(porcentaje);
          console.log(porcentaje)
          let redondeo=$("#redondeo").val()
          if(isNaN(redondeo) || redondeo==""){
            redondeo=0;
          }
          redondeo=parseFloat(redondeo);
          console.log(redondeo)
          let valor_probar=$("#valor_probar").val()
          if(isNaN(valor_probar) || valor_probar==""){
            valor_probar=0;
          }
          valor_probar=parseFloat(valor_probar);
          let ejemplo_porcentaje=$("#ejemplo_porcentaje")
          let nuevo_valor=valor_probar+(porcentaje*valor_probar/100)
          ejemplo_porcentaje.html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(nuevo_valor))
          if(redondeo>0){
            nuevo_valor=Math.ceil(nuevo_valor / redondeo) * redondeo;
          }
          let ejemplo_redondeo=$("#ejemplo_redondeo")
          ejemplo_redondeo.html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(nuevo_valor))
        })
      });
    </script>
  </body>
</html>
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

  $cotizacion_usd=$_POST['cotizacion_usd'];
  $horas=$_POST['horas'];
  $precio_unitario=$_POST['precio_unitario'];
  $monto_pesos=$horas*$precio_unitario;
  $fecha_cobro=$_POST['fecha_cobro'];
  if($fecha_cobro==""){
    $fecha_cobro=null;
  }


  $monto_dolares=0;
  if($cotizacion_usd>0){
    $monto_dolares=$monto_pesos/$cotizacion_usd;
  }

$sql = "INSERT INTO cobros (id_cliente,fecha_factura,fecha_cobro,horas,precio_unitario,monto_pesos,cotizacion_usd,monto_dolares,detalle,id_usuario) VALUES (?,?,?,?,?,?,?,?,?,?)";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['id_cliente'],$_POST['fecha_factura'],$fecha_cobro,$horas,$precio_unitario,$monto_pesos,$_POST['cotizacion_usd'],$monto_dolares,$_POST['detalle'],$_SESSION["user"]['id']));
  
  Database::disconnect();
  
  header("Location: listarCobros.php");
}
$hoy=date("Y-m-d")?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
    <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select-1.13.14/dist/css/bootstrap-select.min.css">
    <style>
      .multiselect{
        color:#212529 !important;
        background-color:#fff;
        border-color:#ccc;
      }
    </style>
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
                      <li class="breadcrumb-item">Nuevo Cobro</li>
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
                    <h5>Nuevo Cobro</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="nuevoCobro.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-9">
                              <select name="id_cliente" class="form-control form-control-sm filtraTabla selectpicker multiselect w-100" data-live-search="true" required>
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $sql = "SELECT c.id AS id_cliente,c.razon_social FROM clientes c";
                                foreach ($pdo->query($sql) as $row) {?>
                                  <option value="<?=$row["id_cliente"]?>"><?=$row["razon_social"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha factura</label>
                            <div class="col-sm-9"><input name="fecha_factura" type="date" class="form-control" value="<?=$hoy?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha de cobro</label>
                            <div class="col-sm-9"><input name="fecha_cobro" type="date" class="form-control"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Horas</label>
                            <div class="col-sm-9"><input name="horas" id="horas" type="number" step="0.01" class="form-control" value="" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Precio unitario</label>
                            <div class="col-sm-9"><input name="precio_unitario" id="precio_unitario" type="number" step="0.01" class="form-control" value="" readonly></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Monto en pesos</label>
                            <div class="col-sm-9"><input name="monto_pesos" id="monto_pesos" type="number" step="0.01" class="form-control" value="" readonly></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cotizacion USD</label>
                            <div class="col-sm-9"><input name="cotizacion_usd" type="number" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Detalle</label>
                            <div class="col-sm-9"><input name="detalle" type="text" maxlength="99" class="form-control" value=""></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Crear</button>
						            <a href="listarCobros.php" class="btn btn-light">Volver</a>
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
    <script src="vendor/bootstrap-select-1.13.14/dist/js/bootstrap-select.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/js/i18n/defaults-es_ES.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
	  <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    <script>
      function actualizarPrecio(){
        var id_cliente = $('select[name=id_cliente]').val();
        var fecha_factura = $('input[name=fecha_factura]').val();
        if(id_cliente && fecha_factura){
          $.getJSON('getPrecioUnitario.php',{id_cliente:id_cliente,fecha_factura:fecha_factura},function(data){
            if(data && data.precio_unitario){
              $('#precio_unitario').val(data.precio_unitario);
              calcularMonto();
            }
          });
        }
      }
      function calcularMonto(){
        var horas = parseFloat($('#horas').val());
        var precio = parseFloat($('#precio_unitario').val());
        if(!isNaN(horas) && !isNaN(precio)){
          $('#monto_pesos').val((horas*precio).toFixed(2));
        }
      }
      $(document).ready(function(){
        $('select[name=id_cliente], input[name=fecha_factura]').change(actualizarPrecio);
        $('#horas').on('input', calcularMonto);
      });
    </script>
  </body>
</html>
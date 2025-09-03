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

  $sql = "INSERT INTO pagos_colaboradores (id_colaborador,id_cobro,fecha_pago,monto,observaciones,id_usuario) VALUES (?,?,?,?,?,?)";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['id_colaborador'],$_POST['id_cobro'],$_POST['fecha_pago'],$_POST['monto_pesos'],$_POST['observaciones'],$_SESSION["user"]['id']));
  
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
				          <form class="form theme-form" role="form" method="post" action="nuevoPagoColaborador.php">
                    <input type="hidden" name="id_cobro" value="<?=$_GET["id"]?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-9"><?php
                              $pdo = Database::connect();
                              $sql = "SELECT razon_social, monto_pesos FROM cobros co INNER JOIN clientes cl ON co.id_cliente=cl.id WHERE co.id = ?";
                              $q = $pdo->prepare($sql);
                              $q->execute(array($_GET["id"]));
                              $data = $q->fetch(PDO::FETCH_ASSOC);
                              Database::disconnect();
                              echo $data["razon_social"]." - $".number_format($data["monto_pesos"],0,",",".");?>
                              <input type="hidden" id="hidden_monto_pesos" value="<?=$data["monto_pesos"]?>">
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Colaborador</label>
                            <div class="col-sm-9">
                              <select name="id_colaborador" id="id_colaborador" class="form-control form-control-sm filtraTabla selectpicker multiselect w-100">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $sql = "SELECT id,nombre FROM colaboradores";
                                foreach ($pdo->query($sql) as $row) {?>
                                  <option value="<?=$row["id"]?>"><?=$row["nombre"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha de pago</label>
                            <div class="col-sm-9"><input name="fecha_pago" type="date" class="form-control" value="<?=$hoy?>"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Monto en pesos</label>
                            <div class="col-sm-9"><input name="monto_pesos" id="monto_pesos" type="number" maxlength="99" class="form-control" value=""></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Observaciones</label>
                            <div class="col-sm-9"><input name="observaciones" type="text" maxlength="199" class="form-control" value=""></div>
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
  </body>
  <script type="text/javascript">
    $(document).ready(function() {
      $("#id_colaborador").on("change", function(){
        console.log(this);
        console.log(this.value);
        let monto_a_pagar
        if(this.value==3){
          let monto_pesos=$("#hidden_monto_pesos").val();
          let porcetaje=4;
          //alert(monto_pesos);
          let monto_neto=monto_pesos-(monto_pesos*porcetaje/100);
          monto_a_pagar=monto_neto/2;
          //alert(monto_a_pagar);
        }else{
          monto_a_pagar="";
        }
        $("#monto_pesos").val(monto_a_pagar);
      });
    });
  </script>
</html>
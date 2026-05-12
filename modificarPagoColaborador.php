<?php
require("config.php");
if (empty($_SESSION['user'])) {
  header("Location: index.php");
  die("Redirecting to index.php");
}

require 'database.php';

$id = null;
if (!empty($_GET['id'])) {
  $id = (int)$_GET['id'];
}

if ($id === null || $id <= 0) {
  header("Location: listarPagos.php");
  exit;
}

if (!empty($_POST)) {
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $sql = "UPDATE pagos_colaboradores SET id_colaborador = ?, fecha_pago = ?, monto = ?, observaciones = ? WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array(
    $_POST['id_colaborador'],
    $_POST['fecha_pago'],
    $_POST['monto_pesos'],
    $_POST['observaciones'],
    $id
  ));

  Database::disconnect();

  header("Location: listarPagos.php");
  exit;
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT pc.id AS id_pago, pc.id_colaborador, pc.id_cobro, pc.fecha_pago, pc.monto, pc.observaciones, cl.nombre AS nombre_cliente, co.monto_pesos
        FROM pagos_colaboradores pc
        INNER JOIN cobros co ON pc.id_cobro = co.id
        INNER JOIN clientes cl ON co.id_cliente = cl.id
        WHERE pc.id = ?";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);

if (!$data) {
  Database::disconnect();
  header("Location: listarPagos.php");
  exit;
}

$sql = "SELECT id, nombre FROM colaboradores ORDER BY nombre";
$colaboradores = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

Database::disconnect();
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
                      <li class="breadcrumb-item">Modificar Pago a Colaborador</li>
                    </ol>
                  </div>
                </div>
                <!-- Bookmark Start-->
                <div class="col-2">
                  <div class="bookmark pull-right">
                    <ul>
                      <li><a  target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?=date('d-m-Y');?>"><i data-feather="calendar"></i></a></li>
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
                    <h5>Modificar Pago a Colaborador</h5>
                  </div>
			          <form class="form theme-form" role="form" method="post" action="modificarPagoColaborador.php?id=<?=$id; ?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-9"><?=$data['nombre_cliente'] . " - $" . number_format((float)$data['monto_pesos'], 0, ",", "."); ?></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Colaborador</label>
                            <div class="col-sm-9">
                              <select name="id_colaborador" id="id_colaborador" class="form-control form-control-sm filtraTabla selectpicker multiselect w-100" required>
                                <option value="">Seleccione...</option><?php
                                foreach ($colaboradores as $colaborador) {
                                  $selected = ((int)$colaborador['id'] === (int)$data['id_colaborador']) ? 'selected' : '';?>
                                  <option value="<?=$colaborador['id']; ?>" <?=$selected; ?>><?=$colaborador['nombre']; ?></option><?php
                                }?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha de pago</label>
                            <div class="col-sm-9"><input name="fecha_pago" type="date" class="form-control" value="<?=$data['fecha_pago']; ?>" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Monto en pesos</label>
                            <div class="col-sm-9"><input name="monto_pesos" id="monto_pesos" type="number" step="0.01" maxlength="99" class="form-control" value="<?=$data['monto']; ?>" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Observaciones</label>
                            <div class="col-sm-9"><input name="observaciones" type="text" maxlength="199" class="form-control" value="<?=htmlspecialchars((string)$data['observaciones'], ENT_QUOTES, 'UTF-8'); ?>"></div>
                          </div>
                          <input type="hidden" id="hidden_monto_pesos" value="<?=$data['monto_pesos']; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Modificar</button>
			                  <a href='listarPagos.php' class="btn btn-light">Volver</a>
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
      $(document).ready(function() {
        $("#id_colaborador").on("change", function() {
          let montoAPagar = "";
          if (this.value == 3) {
            let montoPesos = parseFloat($("#hidden_monto_pesos").val() || 0);
            let porcentaje = 4;
            let montoNeto = montoPesos - (montoPesos * porcentaje / 100);
            montoAPagar = (montoNeto / 2).toFixed(2);
          }
          if (montoAPagar !== "") {
            $("#monto_pesos").val(montoAPagar);
          }
        });
      });
    </script>
  </body>
</html>
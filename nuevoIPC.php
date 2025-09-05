<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php");
}
require 'database.php';

$id = null;
if (!empty($_GET['id'])) {
  $id = $_GET['id'];
}

if (!empty($_POST)) {
  $periodo = $_POST['periodo'];
  // Guardar como primer día del mes
  if ($periodo && strlen($periodo) == 7) {
    $periodo .= '-01';
  }
  $porcentaje = $_POST['porcentaje'];

  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ($id) {
    $sql = "UPDATE ipc_historial SET periodo=?, porcentaje=? WHERE id=?";
    $q = $pdo->prepare($sql);
    $q->execute(array($periodo, $porcentaje, $id));
  } else {
    $sql = "INSERT INTO ipc_historial (periodo,porcentaje) VALUES (?,?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($periodo, $porcentaje));
  }
  Database::disconnect();
  header("Location: listarIPC.php");
}

if ($id) {
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT * FROM ipc_historial WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  $data = $q->fetch(PDO::FETCH_ASSOC);
  $periodo = substr($data['periodo'],0,7);
  $porcentaje = $data['porcentaje'];
  Database::disconnect();
} else {
  $periodo = '';
  $porcentaje = '';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
          <?php include('head_forms.php');?>
  </head>
  <body class="light-only">
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
      <!-- Page Header Start-->
      <?php include('header.php');?>
      <!-- Page Header Ends                              -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <?php include('menu.php');?>
        <!-- Page Sidebar Ends-->
        <div class="page-body">
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5><?=($id?'Editar':'Nuevo');?> IPC</h5>
                  </div>
                  <form class="form theme-form" role="form" method="post" action="nuevoIPC.php<?=($id?'?id='.$id:'');?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Periodo</label>
                            <div class="col-sm-9"><input name="periodo" type="month" class="form-control" value="<?=$periodo;?>" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Porcentaje</label>
                            <div class="col-sm-9"><input name="porcentaje" type="number" step="0.01" class="form-control" value="<?=$porcentaje;?>" required></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Guardar</button>
                        <a href="listarIPC.php" class="btn btn-light">Volver</a>
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
  </body>
</html>

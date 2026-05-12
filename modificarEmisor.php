<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require_once 'funciones.php';
require 'database.php';

$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if ( null==$id ) {
  header("Location: listarEmisores.php");
}

if ( !empty($_POST)) {
  
  // update data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  $sql = "UPDATE emisores SET razon_social = ?, cuit = ?, punto_venta = ?, direccion = ?, email = ?, telefono = ?, condicion_iva = ?, ingresos_brutos = ?, fecha_inicio_actividades = ?, activo = ? WHERE id = ?";
  $q = $pdo->prepare($sql);
  $activo = (isset($_POST['activo']) && $_POST['activo'] === '1') ? 1 : 0;
  $q->execute(array(
    trim($_POST['razon_social']),
    trim($_POST['cuit']),
    (int)$_POST['punto_venta'],
    trim($_POST['direccion']),
    trim($_POST['email']),
    trim($_POST['telefono']),
    trim($_POST['condicion_iva']),
    trim($_POST['ingresos_brutos']),
    $_POST['fecha_inicio_actividades'],
    $activo,
    $_GET['id']
  ));
  
  Database::disconnect();
  
  header("Location: listarEmisores.php");

} else {
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT id, razon_social, cuit, punto_venta, direccion, email, telefono, condicion_iva, ingresos_brutos, fecha_inicio_actividades, activo FROM emisores WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  $data = $q->fetch(PDO::FETCH_ASSOC);
  
  Database::disconnect();
}

$razon_social_value = htmlspecialchars((string)($data['razon_social'] ?? ''), ENT_QUOTES, 'UTF-8');
$cuit_value = htmlspecialchars((string)($data['cuit'] ?? ''), ENT_QUOTES, 'UTF-8');
$punto_venta_value = htmlspecialchars((string)($data['punto_venta'] ?? ''), ENT_QUOTES, 'UTF-8');
$direccion_value = htmlspecialchars((string)($data['direccion'] ?? ''), ENT_QUOTES, 'UTF-8');
$email_value = htmlspecialchars((string)($data['email'] ?? ''), ENT_QUOTES, 'UTF-8');
$telefono_value = htmlspecialchars((string)($data['telefono'] ?? ''), ENT_QUOTES, 'UTF-8');
$ingresos_brutos_value = htmlspecialchars((string)($data['ingresos_brutos'] ?? ''), ENT_QUOTES, 'UTF-8');
$fecha_inicio_actividades_value = htmlspecialchars((string)($data['fecha_inicio_actividades'] ?? ''), ENT_QUOTES, 'UTF-8');

$condicion_iva_actual = (string)($data['condicion_iva'] ?? '');

$activo_si_checked = '';
$activo_no_checked = '';
if (isset($data['activo']) && (int)$data['activo'] === 1) {
  $activo_si_checked = 'checked';
}
if (isset($data['activo']) && (int)$data['activo'] === 0) {
  $activo_no_checked = 'checked';
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
                      <li class="breadcrumb-item">Modificar Emisor</li>
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
              <div class="col-12">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="card">
                      <div class="card-header pb-0">
                        <h5>Modificar Emisor</h5>
                      </div>
                      <form class="form theme-form" method="post" action="modificarEmisor.php?id=<?=$id;?>">
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="mb-3">
                                <label class="form-label" for="razon_social">Razón Social *</label>
                                <input class="form-control" id="razon_social" name="razon_social" type="text" placeholder="Ingrese la razón social" value="<?=$razon_social_value; ?>" required>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="mb-3">
                                <label class="form-label" for="cuit">CUIT *</label>
                                <input class="form-control" id="cuit" name="cuit" type="text" placeholder="XX-XXXXXXXX-X" value="<?=$cuit_value; ?>" required>
                              </div>
                            </div>
                          </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label" for="punto_venta">Punto de Venta *</label>
                                    <input class="form-control" id="punto_venta" name="punto_venta" type="number" placeholder="Ej: 1" min="0" value="<?=$punto_venta_value; ?>" required>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label" for="direccion">Dirección *</label>
                                    <input class="form-control" id="direccion" name="direccion" type="text" placeholder="Ingrese la dirección" value="<?=$direccion_value; ?>" required>
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label" for="email">Email *</label>
                                    <input class="form-control" id="email" name="email" type="email" placeholder="correo@ejemplo.com" value="<?=$email_value; ?>" required>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label" for="telefono">Teléfono *</label>
                                    <input class="form-control" id="telefono" name="telefono" type="text" placeholder="Ingrese teléfono" value="<?=$telefono_value; ?>" required>
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label" for="condicion_iva">Condición frente al IVA *</label>
                                    <select class="form-control" id="condicion_iva" name="condicion_iva" required>
                                      <option value="">-- Seleccionar --</option><?php
                                      foreach (getTiposCondicionesIvaReceptor() as $condicion) {
                                        $cond_iva=htmlspecialchars($condicion->Desc, ENT_QUOTES, 'UTF-8');
                                        $selected="";
                                        if($condicion_iva_actual==$cond_iva){
                                          $selected="selected";
                                        }?>
                                        <option value="<?=$cond_iva;?>" <?=$selected; ?>><?=$cond_iva;?></option><?php
                                      } ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label" for="ingresos_brutos">Ingresos Brutos *</label>
                                    <input class="form-control" id="ingresos_brutos" name="ingresos_brutos" type="text" placeholder="XXXXXX-X" value="<?=$ingresos_brutos_value; ?>" required>
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label" for="fecha_inicio_actividades">Fecha Inicio de Actividades *</label>
                                    <input class="form-control" id="fecha_inicio_actividades" name="fecha_inicio_actividades" type="date" value="<?=$fecha_inicio_actividades_value; ?>" required>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label d-block">Activo *</label>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="activo" id="activo_si" value="1" <?=$activo_si_checked; ?> required>
                                      <label class="form-check-label" for="activo_si">Sí</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="activo" id="activo_no" value="0" <?=$activo_no_checked; ?> required>
                                      <label class="form-check-label" for="activo_no">No</label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                        </div>
                        <div class="card-footer text-end">
                          <div class="col-sm-9 offset-sm-3">
                            <button class="btn btn-primary" type="submit">Actualizar</button>
                            <a class="btn btn-light" href="listarEmisores.php">Cancelar</a>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
        <?php include("footer.php"); ?>
        <!-- footer end-->
      </div>
    </div>
    <!-- latest jquery-->
    <script src="assets/js/jquery-3.5.1.min.js"></script>
    <!-- feather icon js-->
    <script src="assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="assets/js/icons/feather-icon/feather-icon.js"></script>
    <!-- Sidebar jquery-->
    <script src="assets/js/sidebar-menu.js"></script>
    <!-- Bootstrap js-->
    <script src="assets/js/bootstrap/popper.min.js"></script>
    <script src="assets/js/bootstrap/bootstrap.min.js"></script>
    <!-- Plugins JS start-->
    <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    
    <script>
      // Formatear CUIT mientras se escribe
      document.getElementById('cuit').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 11) {
          if (value.length > 2) {
            value = value.substring(0, 2) + '-' + value.substring(2);
          }
          if (value.length > 11) {
            value = value.substring(0, 11) + '-' + value.substring(11);
          }
          e.target.value = value;
        }
      });
    </script>
  </body>
</html>
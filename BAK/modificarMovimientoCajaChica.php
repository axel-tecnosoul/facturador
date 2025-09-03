<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';

$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if ( null==$id ) {
  header("Location: listarCajaChica.php");
}

if ( !empty($_POST)) {

  // update data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $id_usuario=$_SESSION['user']["id"];
  $fecha_hora=$_POST['fecha']." ".$_POST['hora'];
  $id_empleado=$_POST["id_empleado"] ?: NULL;

  //$sql = "UPDATE egresos_caja_chica SET fecha_hora = ?, monto = ?, id_forma_pago = ?, id_usuario = ?, id_motivo = ?, detalle = ?, id_almacen = ? WHERE id = ?";
  $sql = "UPDATE movimientos_caja SET fecha_hora = ?, monto = ?, id_forma_pago = ?, id_usuario = ?, id_motivo = ?, detalle = ?, id_almacen_egreso = ?, id_almacen_corresponde = ?, tipo_movimiento = ?, id_empleado = ? WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($fecha_hora,$_POST['monto'],$_POST['forma_pago'],$id_usuario,$_POST['id_motivo'],$_POST['detalle'],$_POST["id_almacen_egreso"],$_POST["id_almacen_corresponde"],$_POST["tipo_movimiento"],$id_empleado,$_GET["id"]));
  
  Database::disconnect();
  
  header("Location: listarCajaChica.php");
} else {
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT fecha_hora,monto,id_forma_pago,id_usuario,id_motivo,id_tipo_motivo,detalle,id_almacen_egreso,id_almacen_corresponde,tipo_movimiento,id_empleado FROM movimientos_caja mc LEFT JOIN motivos_salidas_caja msc ON mc.id_motivo=msc.id WHERE mc.id = ? ";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  $data = $q->fetch(PDO::FETCH_ASSOC);

  $checkedTipoMovimientoIngreso="";
  $checkedTipoMovimientoEgreso="";
  if($data["tipo_movimiento"]=="Ingreso"){
    $checkedTipoMovimientoIngreso="checked";
  }
  if($data["tipo_movimiento"]=="Egreso"){
    $checkedTipoMovimientoEgreso="checked";
  }
  
  Database::disconnect();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_forms.php');?>
	  <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
    <style>
      .select2-container{
        border: 1px solid #ccc;
        border-radius: 5px;
      }
      .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
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
                      <li class="breadcrumb-item">Modificar movimiento de caja chica</li>
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
                    <h5>Modificar movimiento de caja chica</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="modificarMovimientoCajaChica.php?id=<?php echo $id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha</label>
                            <div class="col-sm-9"><input name="fecha" type="date" value="<?=date("Y-m-d",strtotime($data["fecha_hora"]))?>" class="form-control" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Hora</label>
                            <div class="col-sm-9"><input name="hora" type="time" value="<?=date("H:i",strtotime($data["fecha_hora"]))?>" class="form-control" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Caja del Almacen</label>
                            <div class="col-sm-9">
                              <select name="id_almacen_egreso" id="id_almacen_egreso" class="form-control" required>
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT id, almacen FROM almacenes WHERE activo = 1";
                                if ($_SESSION['user']['id_perfil'] != 1) {
                                  $sqlZon .= " and id = ".$_SESSION['user']['id_almacen']; 
                                }
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  $selected="";
                                  if($fila['id']==$data["id_almacen_egreso"]){
                                    $selected="selected";
                                  }
                                  echo "<option value='".$fila['id']."' $selected>".$fila['almacen']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Almacen correspondiente</label>
                            <div class="col-sm-9">
                              <select name="id_almacen_corresponde" id="id_almacen_corresponde" class="form-control" required>
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT id, almacen FROM almacenes WHERE activo = 1";
                                if ($_SESSION['user']['id_perfil'] != 1) {
                                  $sqlZon .= " and id = ".$_SESSION['user']['id_almacen']; 
                                }
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  $selected="";
                                  if($fila['id']==$data["id_almacen_corresponde"]){
                                    $selected="selected";
                                  }
                                  echo "<option value='".$fila['id']."' $selected>".$fila['almacen']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Forma de pago</label>
                            <div class="col-sm-9">
                              <select name="forma_pago" id="forma_pago" class="form-control" required>
                                <option value="">- Seleccione -</option><?php
                                $pdo = Database::connect();
                                $sql = " SELECT id, forma_pago FROM forma_pago";
                                foreach ($pdo->query($sql) as $row) {
                                  $selected="";
                                  if($row['id']==$data["id_forma_pago"]){
                                    $selected="selected";
                                  }?>
                                  <option value="<?=$row["id"]?>" <?=$selected?>><?=$row["forma_pago"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tipo de movimiento</label>
                            <div class="col-sm-9">
                              <label class="d-block" for="edo-ani">
                                <input class="radio_animated" value="Ingreso" required id="edo-ani" type="radio" name="tipo_movimiento" <?=$checkedTipoMovimientoIngreso?>>Ingreso
                              </label>
                              <label class="d-block" for="edo-ani1">
                                <input class="radio_animated" value="Egreso" required id="edo-ani1" type="radio" name="tipo_movimiento" <?=$checkedTipoMovimientoEgreso?>>Egreso
                              </label>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Monto</label>
                            <div class="col-sm-9"><input name="monto" type="number" maxlength="99" class="form-control" value="<?=$data["monto"]?>" required></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tipo de Motivo</label>
                            <div class="col-sm-9">
                              <select name="id_tipo_motivo" id="id_tipo_motivo" class="form-control js-example-basic-single">
                                <option value="">- Seleccione -</option><?php
                                $pdo = Database::connect();
                                $sql = " SELECT id, nombre FROM tipos_motivos";
                                foreach ($pdo->query($sql) as $row) {$selected="";
                                  if($row['id']==$data["id_tipo_motivo"]){
                                    $selected="selected";
                                  }?>
                                  <option value="<?=$row["id"]?>" <?=$selected?>><?=$row["nombre"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Motivo</label>
                            <div class="col-sm-9">
                              <select name="id_motivo" id="id_motivo" class="form-control js-example-basic-single" required>
                                <option value="">- Seleccione -</option><?php
                                $pdo = Database::connect();
                                $sql = " SELECT id, motivo, id_tipo_motivo FROM motivos_salidas_caja WHERE id!=2";
                                foreach ($pdo->query($sql) as $row) {
                                  $selected="";
                                  if($row['id']==$data["id_motivo"]){
                                    $selected="selected";
                                  }?>
                                  <option value="<?=$row["id"]?>" data-id-tipo-motivo="<?=$row["id_tipo_motivo"]?>" <?=$selected?>><?=$row["motivo"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div id="row_select_empleados" class="form-group row">
                            <label class="col-sm-3 col-form-label">Empleado</label>
                            <div class="col-sm-9">
                              <select name="id_empleado" id="id_empleado" class="form-control js-example-basic-single">
                                <option value="">- Seleccione -</option><?php
                                $pdo = Database::connect();
                                $sql = " SELECT id, CONCAT(nombre,' ',apellido) AS empleado FROM empleados WHERE 1";
                                foreach ($pdo->query($sql) as $row) {
                                  $selected="";
                                  if($row['id']==$data["id_empleado"]){
                                    $selected="selected";
                                  }?>
                                  <option value="<?=$row["id"]?>" <?=$selected?>><?=$row["empleado"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Detalle</label>
                            <div class="col-sm-9"><input name="detalle" type="text" class="form-control" value="<?=$data["detalle"]?>" required></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Modificar</button>
						            <a href="listarCajaChica.php" class="btn btn-light">Volver</a>
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
      $(document).ready(function () {

        $("input[name='tipo_movimiento']").on("change",function(){
          let selectMotivo=$("#id_motivo");
          selectMotivo.val("");
          let optionSalidaCajaGrande=selectMotivo.find("option[value='1']");
          if(this.value=="Ingreso"){
            optionSalidaCajaGrande.attr("disabled",true);
          }else{
            optionSalidaCajaGrande.attr("disabled",false);
          }
          selectMotivo.select2()
        })

        $("#id_tipo_motivo").on("change",function(){
          let id_tipo_motivo=this.value;
          let selectMotivo=$("#id_motivo")
          selectMotivo.find("option").each(function(){
            let disabled=true;
            if(this.value=="" || id_tipo_motivo=="" || id_tipo_motivo==this.dataset.idTipoMotivo){
              console.log(this.value);
              disabled=false;
            }
            this.disabled=disabled;
          })
          selectMotivo.val("")
          selectMotivo.select2()

          let id_empleado=$("#id_empleado");
          if(id_tipo_motivo==12){//12 -> Sueldos
            id_empleado.prop("required",true)
          }else{
            id_empleado.prop("required",false)
          }
          
        })

        $("#id_motivo").on("change",function(){
          let selectedOption=$(this).find("option[value='"+this.value+"']");
          selectedOption=selectedOption[0];
          let id_empleado=$("#id_empleado");
          if(selectedOption.dataset.idTipoMotivo==12){//12 -> Sueldos
            id_empleado.prop("required",true)
          }else{
            id_empleado.prop("required",false)
          }
        })
      });
    </script>
  </body>
</html>
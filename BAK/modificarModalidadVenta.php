<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
require 'funciones.php';

$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if ( null==$id ) {
  header("Location: listarPagosRealizados.php");
}

if ( !empty($_POST)) {
  
  // insert data
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $modoDebug=0;

  if ($modoDebug==1) {
    $pdo->beginTransaction();
    var_dump($_GET);
    var_dump($_POST);
  }

  $sql = "SELECT vd.id_modalidad AS id_modalidad_venta, p.id_proveedor, v.id_forma_pago, vd.subtotal FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id INNER JOIN productos p ON vd.id_producto=p.id WHERE vd.id = ? ";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  $data = $q->fetch(PDO::FETCH_ASSOC);

  if ($modoDebug==1) {
    $q->debugDumpParams();
    var_dump($data);
  }
  
  if($data["id_modalidad_venta"]!=$_POST["id_modalidad"]){

    $deuda_proveedor=calcularDeudaProveedor($data['id_forma_pago'],$_POST["id_modalidad"],$data["subtotal"]);

    $pagado=0;
    if($_POST["id_modalidad"]==1){
      $pagado=1;//seteamos la variable para que muestre como pagada (sera mas facil identifcar en caso de error)
    }

    //SI LA MODALIDAD NUEVA ES A CREDITO, AUMENTAMOS EL CREDITO DE LA PROVEEDORA
    if($_POST["id_modalidad"]==50){

      $sql = "UPDATE proveedores set credito = credito + ? where id = ?";
      $q = $pdo->prepare($sql);
      $q->execute(array($deuda_proveedor,$data["id_proveedor"]));

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }
    }

    //SI LA MODALIDAD VIEJA ES POR PORCENTAJE, RESTAMOS EL CREDITO DE LA PROVEEDORA
    if($data["id_modalidad_venta"]==50){

      $sql = "UPDATE proveedores set credito = credito - ? where id = ?";
      $q = $pdo->prepare($sql);
      $q->execute(array($deuda_proveedor,$data["id_proveedor"]));

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }
    }

    $sql = "UPDATE ventas_detalle set id_modalidad = ?, deuda_proveedor = ?, pagado = $pagado where id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($_POST['id_modalidad'],$deuda_proveedor,$id));

    if ($modoDebug==1) {
      $q->debugDumpParams();
      echo "<br><br>Afe: ".$q->rowCount();
      echo "<br><br>";
    }

  }

  if ($modoDebug==1) {
    $pdo->rollBack();
    die();
  }

  
  Database::disconnect();

  header("Location: verProveedor.php?id=".$data["id_proveedor"]);

} else {
  
  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $sql = "SELECT vd.caja_egreso, vd.id_almacen, vd.id_forma_pago,vd.id_modalidad AS id_modalidad_venta, m.modalidad AS modalidad_proveedor, vd.deuda_proveedor, p.descripcion, p.codigo, pr.apellido, pr.nombre, pr.id,date_format(vd.fecha_hora_pago,'%d/%m/%Y %H:%i') AS fecha_hora_pago, v.id_forma_pago, fp.forma_pago, d.descripcion AS descuento, d.porcentaje,vd.precio,vd.cantidad,vd.subtotal FROM ventas_detalle vd INNER JOIN ventas v ON vd.id_venta=v.id INNER JOIN productos p ON vd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id INNER JOIN modalidades m ON pr.id_modalidad=m.id INNER JOIN forma_pago fp ON v.id_forma_pago=fp.id LEFT JOIN descuentos d ON v.id_descuento_aplicado=d.id WHERE vd.id = ? ";
  $q = $pdo->prepare($sql);
  $q->execute(array($id));
  $data = $q->fetch(PDO::FETCH_ASSOC);
  
  Database::disconnect();
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
                      <li class="breadcrumb-item">Modificar Modalidad de Venta</li>
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
                    <h5>Modificar Modalidad de Venta</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="modificarModalidadVenta.php?id=<?=$id?>">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">

                          <div class="form-group row">
                            <div class="col-sm-3">Proveedor:</div>
                            <div class="col-sm-9"><?="(".$data["id"].") ".$data["nombre"]." ".$data["apellido"]?></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Modalidad por defecto:</div>
                            <div class="col-sm-9"><?=$data["modalidad_proveedor"]?></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Forma de pago:</div>
                            <div class="col-sm-9"><?php
                              $porcentaje_comision=100;
                              if($data['id_forma_pago']!=1){
                                $porcentaje_comision=80;
                                $deuda_proveedor=calcularDeudaProveedor($data['id_forma_pago'],$data["id_modalidad_venta"],$data["subtotal"]);
                                if($deuda_proveedor!=$data["deuda_proveedor"]){
                                  $porcentaje_comision=85;
                                }
                              }
                              echo $data["forma_pago"]." (".$porcentaje_comision."%)"?>
                            </div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Descuento:</div>
                            <div class="col-sm-9"><?php
                              if($data["descuento"]){
                                echo $data["descuento"]." (".$data["porcentaje"]."%)";
                              }?></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Producto:</div>
                            <div class="col-sm-9"><?="(".$data["codigo"].") ".$data["descripcion"]?></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Precio:</div>
                            <div class="col-sm-9">$<?=number_format($data["precio"],2,",",".")?></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Cantidad:</div>
                            <div class="col-sm-9"><?=$data["cantidad"]?></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Subtotal:</div>
                            <div class="col-sm-9">$<?=number_format($data["subtotal"],2,",",".")?></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Deuda proveedor:</div>
                            <div class="col-sm-9">$<?=number_format($data["deuda_proveedor"],2,",",".")?></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3" style="align-self: center;">Modalidad de Venta: </div>
                            <div class="col-sm-9">
                              <input type="hidden" id="id_modalidad_actual" value="<?=$data["id_modalidad_venta"]?>">
                              <select name="id_modalidad" id="id_modalidad" style="width: 100%;" class="js-example-basic-single" required="required">
                                <option value="">Seleccione</option><?php
                                $pdo = Database::connect();
                                $sql = " SELECT id, modalidad FROM modalidades";
                                foreach ($pdo->query($sql) as $row) {
                                  $deuda_proveedor=calcularDeudaProveedor($data['id_forma_pago'],$row["id"],$data["subtotal"]);

                                  $selected="";
                                  if($data["id_modalidad_venta"]==$row["id"]){
                                    $selected="selected";
                                  }?>
                                  <option value="<?=$row["id"]?>" <?=$selected?> data-deuda-proveedor="<?=$deuda_proveedor?>"><?=$row["modalidad"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row d-none" id="fila_nueva_deuda_proveedor">
                            <div class="col-sm-3">Nueva deuda proveedor:</div>
                            <div class="col-sm-9" id="nueva_deuda_proveedor"></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-3">Fecha y hora de pago:</div>
                            <div class="col-sm-9"><?php
                            if($data["fecha_hora_pago"]){
                              echo $data["fecha_hora_pago"]."hs.";
                            }?></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Modificar</button>
						            <a href='listarPagosRealizados.php' class="btn btn-light">Volver</a>
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
        let id_modalidad_actual=$("#id_modalidad_actual").val();
        $("#id_modalidad").on("change",function(){
          let fila_nueva_deuda_proveedor=$("#fila_nueva_deuda_proveedor")
          if(id_modalidad_actual==this.value){
            fila_nueva_deuda_proveedor.addClass("d-none")
          }else{
            fila_nueva_deuda_proveedor.removeClass("d-none")
            let selected_option=$(this).find("option[value='"+this.value+"']")
            let nueva_deuda_proveedor=selected_option.data("deudaProveedor");
            nueva_deuda_proveedor=new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(nueva_deuda_proveedor)
            $("#nueva_deuda_proveedor").html(nueva_deuda_proveedor)
            //console.log(selected_option);
            console.log(nueva_deuda_proveedor);
          }
        })
      });
    </script>
  </body>
</html>
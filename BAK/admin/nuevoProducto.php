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

  $sql2 = " SELECT id FROM productos WHERE codigo = ? ";
  $q2 = $pdo->prepare($sql2);
  $q2->execute(array($_POST['codigo']));
  $data2 = $q2->fetch(PDO::FETCH_ASSOC);

  if ($modoDebug==1) {
    $q2->debugDumpParams();
    echo "<br><br>Afe: ".$q2->rowCount();
    echo "<br><br>";
    var_dump($data2);
  }

  if (empty($data2)) {
  
    $cb = microtime(true)*10000;
    
    $sql = "INSERT INTO productos(codigo, id_categoria, descripcion, id_proveedor, precio, precio_costo, activo, cb) VALUES (?,?,?,?,?,?,1,?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($_POST['codigo'],$_POST['id_categoria'],$_POST['descripcion'],$_POST['id_proveedor'],$_POST['precio'],$_POST["precio_costo"],$cb));
    $idProducto = $pdo->lastInsertId();

    $sql3 = "INSERT INTO stock(id_producto, id_almacen, cantidad, id_modalidad) VALUES (?,?,?,?)";
    $q3 = $pdo->prepare($sql3);
    $q3->execute(array($idProducto,$_POST['id_almacen'],$_POST['cantidad'],$_POST['id_modalidad']));
  
  }
  
  Database::disconnect();
  
  header("Location: listarProductos.php");
}?>
<!DOCTYPE html>
<html lang="en">
  <head><?php
    include('head_forms.php');?>
	  <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
  </head>
  <body class="light-only">
    <!-- Loader ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper"><?php
      include('header.php');?>
	  
      <!-- Page Header Start-->
      <div class="page-body-wrapper"><?php
        include('menu.php');?>
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
                      <li class="breadcrumb-item">Nuevo Producto</li>
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
                    <h5>Nuevo Producto</h5>
                  </div>
                  <form class="form theme-form" role="form" method="post" action="nuevoProducto.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">

                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Código</label>
                            <div class="col-sm-9"><input name="codigo" type="text" maxlength="99" class="form-control" value="" required="required"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Categoría</label>
                            <div class="col-sm-9">
                              <select name="id_categoria" id="id_categoria" class="js-example-basic-single col-sm-12" required="required">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT `id`, `categoria` FROM `categorias` WHERE activa = 1";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  echo ">".$fila['categoria']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Descripción</label>
                            <div class="col-sm-9"><input name="descripcion" type="text" maxlength="99" class="form-control" value="" required="required"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Proveedor</label>
                            <div class="col-sm-9">
                              <select name="id_proveedor" id="id_proveedor" class="js-example-basic-single col-sm-12" required="required">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT `id`, `nombre`, `apellido` FROM `proveedores` WHERE activo = 1";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  echo ">(".$fila['id'].") ".$fila['nombre'].' '.$fila['apellido']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Precio</label>
                            <div class="col-sm-9"><input name="precio" type="number" step="0.01" min="0" class="form-control" value="" required="required"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Precio de costo</label>
                            <div class="col-sm-9"><input name="precio_costo" type="number" step="0.01" min="0" class="form-control" value="0" required="required"></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Almacen</label>
                            <div class="col-sm-9">
                              <select name="id_almacen" id="id_almacen" class="js-example-basic-single col-sm-12" required="required">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT `id`, `almacen` FROM `almacenes` WHERE activo = 1 ";
                                if ($_SESSION['user']['id_perfil'] == 2) {
                                  $sqlZon .= " and id = ".$_SESSION['user']['id_almacen'];
                                }
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  echo ">".$fila['almacen']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Modalidad</label>
                            <div class="col-sm-9">
                              <select name="id_modalidad" id="id_modalidad" class="js-example-basic-single col-sm-12" required="required">
                                <option value="">Seleccione...</option><?php
                                $pdo = Database::connect();
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $sqlZon = "SELECT `id`, `modalidad` FROM `modalidades` WHERE 1";
                                $q = $pdo->prepare($sqlZon);
                                $q->execute();
                                while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                                  echo "<option value='".$fila['id']."'";
                                  echo ">".$fila['modalidad']."</option>";
                                }
                                Database::disconnect();?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Cantidad</label>
                            <div class="col-sm-9"><input name="cantidad" type="number" step="1" min="1" class="form-control" value="" required="required"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Crear</button>
                        <a href="listarProductos.php" class="btn btn-light">Volver</a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start--><?php
        include("footer.php"); ?>
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
        $("#id_proveedor").on("change",function(){
          let id_proveedor=this.value;
          console.log(id_proveedor);
          $.ajax({
            type: "POST",
            url: "getDataProveedor.php",
            data: "id_proveedor="+id_proveedor,
            dataType: "json",
            success: function (response) {
              console.log(response);
              $("#id_modalidad").val(response.id_modalidad).trigger('change');
              $("#id_almacen").val(response.id_almacen).trigger('change');
            }
          });
        })
      });
    </script>
  </body>
</html>
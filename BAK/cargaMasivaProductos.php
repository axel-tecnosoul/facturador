<?php
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';
$modoDebug=0;
if ( !empty($_POST)) {

  $pdo = Database::connect();
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $activo=1;
  if ($modoDebug==1) {
    var_dump($_POST);
    $pdo->beginTransaction();
  }
  foreach ($_POST["codigo"] as $key => $codigo) {
    if($key==0){
      continue;
    }
    $cb = microtime(true)*10000;

    $sql2 = " SELECT id FROM productos WHERE codigo = ? ";
    $q2 = $pdo->prepare($sql2);
    $q2->execute(array($codigo));
    $data2 = $q2->fetch(PDO::FETCH_ASSOC);

    if ($modoDebug==1) {
      $q2->debugDumpParams();
      echo "<br><br>Afe: ".$q2->rowCount();
      echo "<br><br>";
      var_dump($data2);
    }

    $descripcion=$_POST["descripcion"][$key];
    
    if (empty($data2)) {
      $sql = "INSERT INTO productos (codigo, id_categoria, descripcion, id_proveedor, precio, precio_costo, activo, cb) VALUES (?,?,?,?,?,?,?,?) ";
      $q = $pdo->prepare($sql);
      $q->execute(array($codigo,$_POST["id_categoria"][$key],$descripcion,$_POST["id_proveedor"],$_POST["precio"][$key],$_POST["precio_costo"][$key],$activo,$cb));

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }
      
      $idProducto = $pdo->lastInsertId();
      $sql3 = "INSERT INTO stock (id_producto, id_almacen, cantidad, id_modalidad) VALUES (?,?,?,?)";
      $q3 = $pdo->prepare($sql3);
      $q3->execute(array($idProducto,$_POST["id_almacen"],$_POST["cantidad"][$key],$_POST["id_modalidad"]));

      if ($modoDebug==1) {
        $q3->debugDumpParams();
        echo "<br><br>Afe: ".$q3->rowCount();
        echo "<br><br>";
      }
    } else {
      $sql = "UPDATE productos SET id_categoria=?, descripcion=?, id_proveedor=?, precio=?, precio_costo=?, activo=? WHERE codigo=? ";
      $q = $pdo->prepare($sql);
      $q->execute(array($_POST["id_categoria"][$key],$descripcion,$_POST["id_proveedor"],$_POST["precio"][$key],$_POST["precio_costo"][$key],$activo,$codigo));

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }

      /*$sql = "UPDATE stock SET id_producto=?, id_almacen=?, cantidad=?, id_modalidad=? WHERE codigo=? ";
      $q = $pdo->prepare($sql);
      $q->execute(array($$data2["id"],$_POST["id_almacen"],$_POST["cantidad"][$key],$_POST["id_modalidad"],$codigo));

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }*/
    }
  }
  if ($modoDebug==1) {
    $pdo->rollBack();
    die();
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
                      <li class="breadcrumb-item">Carga masiva de Productos</li>
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
                    <h5>Carga masiva de Productos</h5>
                  </div>
                  <form class="form theme-form" role="form" method="post" action="cargaMasivaProductos.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">

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
                            <label class="col-sm-3 col-form-label">Productos</label>
                            <div class="col-sm-9"></div>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-12">
                              <table class="table-detalle table table-bordered table-hover text-center" id="tableEmail">
                                <thead>
                                  <tr>
                                    <th>Código</th>
                                    <th>Categoria</th>
                                    <th>Descripcion</th>
                                    <th>Precio</th>
                                    <th>Precio de costo</th>
                                    <th>Cantidad</th>
                                    <th>Eliminar</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr id='addr0' data-id="0" style="display: none;">
                                    <td data-name="codigo">
                                      <input type="text" class="form-control" placeholder="Código" name="codigo[]" id="codigo-0"/>
                                    </td>
                                    <td data-name="id_categoria">
                                      <select name="id_categoria[]" id="id_categoria-0" class="js-example-basic-single" style="width: 100%">
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
                                    </td>
                                    <td data-name="descripcion">
                                      <input type="text" class="form-control" placeholder="Descripcion" name="descripcion[]" id="descripcion-0"/>
                                    </td>
                                    <td data-name="precio">
                                      <input type="number" class="form-control" placeholder="Precio" name="precio[]" id="precio-0"/>
                                    </td>
                                    <td data-name="precio_costo">
                                      <input type="number" class="form-control" placeholder="Precio de costo" name="precio_costo[]" id="precio_costo-0"/>
                                    </td>
                                    <td data-name="cantidad">
                                      <input type="number" class="form-control" placeholder="Cantidad" name="cantidad[]" id="cantidad-0"/>
                                    </td>
                                    <td data-name="eliminar">
                                      <span name="eliminar[]" title="Eliminar" class="btn btn-sm row-remove text-center" onClick="eliminarFila(this);">
                                        <img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar" title="Eliminar">
                                      </span>
                                    </td>
                                  </tr>
                              </tbody>
                              <tfoot>
                                <tr>
                                  <td colspan="4"></td>
                                  <td colspan="2" align='center'>
                                    <input type="button" class="btn btn-dark" id="addRowEmail" value="Agregar Producto">
                                    <input type="hidden" name="emailEliminados" id="emailEliminados">
                                  </td>
                                </tr>
                              </tfoot>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Cargar</button>
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
    <script type="text/javascript">
      
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

      $(document).ready(function(){
          $("#addRowEmail").on('click', function(event) {
            event.preventDefault();
            addRowEmail();
          }).click();
          //addRowEmail();//añadimos automaticamente una fila en email
      });

      function eliminarFila(t){
        var fila=$(t).closest("tr");
        fila.remove();
      }

      /**SCRIPT DE DETALLE PRODUCTO*/
      function addRowEmail(){
        //alert("hola");
        var newid = 0;
        var primero="";
        var ultimoRegistro=0;
        $.each($("#tableEmail tr"), function() {
          if (parseInt($(this).data("id")) > newid) {
            newid = parseInt($(this).data("id"));
          }
        });
        //debugger;
        newid++;
        //console.log(newid);
        var tr = $("<tr></tr>", {
          "id": "addr"+newid,
          "data-id": newid
        });
        //console.log(newid);
        var p=0;
        $.each($("#tableEmail tbody tr:nth(0) td"),function(){//loop through each td and create new elements with name of newid
          var cur_td = $(this); 
          var children = cur_td.children();
          if($(this).data("name")!=undefined){// add new td and element if it has a name
            var td = $("<td></td>", {
              "data-name": $(cur_td).data("name"),
              "class": this.className
            });
            var c = $(cur_td).find($(children[0]).prop('tagName')).clone();//.val("")
            
            var id=$(c).attr("id");
            $(c).attr("required",true);
            ultimoRegistro=id;
            if(id!=undefined){
              //console.log("id1: ");
              //console.log(id);
              id=id.split("-");
              c.attr("id", id[0]+"-"+newid);//modificamos el id de cada input
              if(p==0){
                primero=c;
                p++;
              }
            }
            c.appendTo($(td));
            td.appendTo($(tr));
            
          }else {
            //console.log("<td></td>",{'text':$('#tab_logic tr').length})
            var td = $("<td></td>", {
              'text': $('#tableEmail tr').length
            }).appendTo($(tr));
          }
        });
        //console.log($(tr).find($("input[name='detalledni[]']")));
        //console.log(tr);//.find($("input"))
        $(tr).appendTo($('#tableEmail'));// add the new row
        if(newid>0){
          primero.focus();
          var sel2=$("#id_categoria-"+newid)
          //console.log(sel2);
          
          sel2.select2();//llamamos para inicializar select2
          sel2.select2('destroy');//como no se iniciliaza bien lo destruimos para que elimine las clases que arrastra de la clonacion
          sel2.select2();//volvemos a inicializar y ahora si se inicializa bien
          
        }
        return tr.attr("id");
      }

    </script>
  </body>
</html>
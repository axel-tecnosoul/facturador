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
  
  $sql = "INSERT INTO compras(fecha, id_almacen, id_proveedor, id_usuario, total, id_modalidad) VALUES (now(),?,?,?,0,?)";
  $q = $pdo->prepare($sql);
  $q->execute(array($_POST['id_almacen'],$_POST['id_proveedor'],$_SESSION['user']['id'],$_POST['id_modalidad']));
  $idCompra = $pdo->lastInsertId();

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";
  }
  
  $total = 0;
  //$sql = " SELECT p.id, p.codigo, c.categoria, p.descripcion, p.precio FROM productos p inner join categorias c on c.id = p.id_categoria WHERE p.activo = 1 and p.id_proveedor = ".$_POST["id_proveedor"];
  //foreach ($pdo->query($sql) as $row) {
  foreach ($_POST['id_producto'] as $key => $id_producto) {

    $cantidad = $_POST['cantidad'][$key];
    //$cantidadAnterior = $_POST['stock'][$key];
    $precio = $_POST['precio'][$key];
    //$modalidad = $_POST["id_modalidad"][$key];
    //$idProveedor = $_POST["id_proveedor"][$key];
    $idProducto = $_POST["id_producto"][$key];

    //if ($_POST['cantidad_'.$row[0]] > 0) {
      //$idProducto = $row[0];
      //$cantidad = $_POST['cantidad_'.$row[0]];
      //$precio = $row[4];
      $subtotal = $cantidad * $precio;
      $total += $subtotal;
      
      $sql = "INSERT INTO compras_detalle(id_compra, id_producto, precio, cantidad) VALUES (?,?,?,?)";
      $q = $pdo->prepare($sql);
      $q->execute(array($idCompra,$idProducto,$precio,$cantidad));

      if ($modoDebug==1) {
        $q->debugDumpParams();
        echo "<br><br>Afe: ".$q->rowCount();
        echo "<br><br>";
      }
      
      $sql2 = "SELECT id FROM stock WHERE id_producto = ? and id_almacen = ? and id_modalidad = ?";
      $q2 = $pdo->prepare($sql2);
      $q2->execute(array($idProducto,$_POST['id_almacen'],$_POST['id_modalidad']));
      $data = $q2->fetch(PDO::FETCH_ASSOC);

      if ($modoDebug==1) {
        echo $sql2;
        echo "<br><br>";
        var_dump($data);
        echo "<br><br>";
      }

      if (!empty($data)) {
        $sql3 = "UPDATE stock set cantidad = cantidad + ? where id = ?";
        $q3 = $pdo->prepare($sql3);
        $q3->execute(array($cantidad,$data['id']));

        if ($modoDebug==1) {
          $q3->debugDumpParams();
          echo "<br><br>Afe: ".$q3->rowCount();
          echo "<br><br>";
        }

      } else {
        $sql3 = "INSERT INTO stock(id_producto, id_almacen, cantidad, id_modalidad) VALUES (?,?,?,?)";
        $q3 = $pdo->prepare($sql3);
        $q3->execute(array($idProducto,$_POST['id_almacen'],$cantidad,$_POST['id_modalidad']));

        if ($modoDebug==1) {
          $q3->debugDumpParams();
          echo "<br><br>Afe: ".$q3->rowCount();
          echo "<br><br>";
        }
      }
          
    //}
  }
  
  $sql = "UPDATE compras set total = ? WHERE id = ?";
  $q = $pdo->prepare($sql);
  $q->execute(array($total,$idCompra));

  if ($modoDebug==1) {
    $q->debugDumpParams();
    echo "<br><br>Afe: ".$q->rowCount();
    echo "<br><br>";
  }

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
	<link rel="stylesheet" type="text/css" href="assets/css/datatables.css">
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
                      <li class="breadcrumb-item">Nueva Compra</li>
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
                    <h5>Ingreso de Stock</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="nuevaCompra.php">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Almacen</label>
                            <div class="col-sm-9">
                            <select name="id_almacen" id="id_almacen" class="js-example-basic-single col-sm-12" required="required">
                            <option value="">Seleccione...</option>
                            <?php 
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
                            Database::disconnect();
                            ?>
                            </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Modalidad</label>
                            <div class="col-sm-9">
                            <select name="id_modalidad" id="id_modalidad" class="js-example-basic-single col-sm-12" required="required">
                            <option value="">Seleccione...</option>
                            <?php 
                            $pdo = Database::connect();
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $sqlZon = "SELECT `id`, `modalidad` FROM `modalidades` WHERE 1";
                            $q = $pdo->prepare($sqlZon);
                            $q->execute();
                            while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                              echo "<option value='".$fila['id']."'";
                              echo ">".$fila['modalidad']."</option>";
                            }
                            Database::disconnect();
                            ?>
                            </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Proveedor</label>
                            <div class="col-sm-9">
                            <select name="id_proveedor" id="id_proveedor" class="js-example-basic-single col-sm-12" required="required" onChange="jsListarProductos(this.value);">
                            <option value="">Seleccione...</option>
                            <?php 
                            $pdo = Database::connect();
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $sqlZon = "SELECT `id`, `nombre`, `apellido` FROM `proveedores` WHERE activo = 1";
                            $q = $pdo->prepare($sqlZon);
                            $q->execute();
                            while ($fila = $q->fetch(PDO::FETCH_ASSOC)) {
                              echo "<option value='".$fila['id']."'";
                              echo ">".$fila['nombre'].' '.$fila['apellido']."</option>";
                            }
                            Database::disconnect();
                            ?>
                            </select>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Productos en stock</label>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-12">
                            <table class="display" id="dataTables-example666">
                              <thead>
                                <tr>
                                  <th>ID</th>
                                  <th>Código</th>
                                  <th>Categoría</th>
                                  <th>Descripción</th>
                                  <th>Precio</th>
                                  <th>Accion</th>
                                </tr>
                              </thead>
                              <tbody>
                              </tbody>
                            </table>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Productos a comprar</label>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-12">
                              <table class="table" id="productos_comprar">
                                <thead>
                                  <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Categoría</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Eliminar</th>
                                  </tr>
                                </thead>
                                <tbody></tbody>
                              </table>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Total</label>
                            <div class="col-sm-9"><label id="total_compra">$ 0</label></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary" type="submit">Crear</button>
						            <a href="listarStock.php" class="btn btn-light">Volver</a>
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
    <!-- <script src="assets/js/typeahead/handlebars.js"></script>
    <script src="assets/js/typeahead/typeahead.bundle.js"></script>
    <script src="assets/js/typeahead/typeahead.custom.js"></script> -->
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <!-- <script src="assets/js/typeahead-search/handlebars.js"></script>
    <script src="assets/js/typeahead-search/typeahead-custom.js"></script> -->
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
    <!-- Plugin used-->
	<script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
	
	<script src="assets/js/datatable/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.buttons.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/jszip.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.colVis.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/pdfmake.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/vfs_fonts.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.autoFill.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.select.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.html5.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.print.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.responsive.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/responsive.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.keyTable.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.colReorder.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.fixedHeader.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.rowReorder.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.scroller.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/custom.js"></script>
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Plugins JS Ends-->
    <!-- Theme js-->

	  <script>
      $("form").on("submit",function(e){
        e.preventDefault();
        if($('#productos_comprar tbody tr').length){
          //console.log("submit");
          this.submit();
        }else{
          alert("Añada algún producto")
        }
      });
      
      function jsListarProductos(val) {
        $("#dataTables-example666").dataTable().fnDestroy();
        $('#dataTables-example666').DataTable({
          "ajax" : "ajaxCompras.php?proveedor="+val,//&id_vehiculo="+id_vehiculo+"
          stateSave: true,
          responsive: true,
          serverSide: true,
          processing: true,
          scrollY: false,
          "columns":[
            {"data": "cb"},//"fecha_mostrar"},
            {"data": "codigo"},//"vehiculo.marca"},
            {"data": "categoria"},//"vehiculo.modelo"},
            {"data": "descripcion"},//"vehiculo.patente"},
            {
              render: function(data, type, row, meta) {
                return `
                  <input type="hidden" disabled name="id_producto[]" class="enviar_form id_producto" value="${row.id_producto}">
                  <input type="hidden" disabled name="precio[]" class="enviar_form precio" value="${row.precio}">`+
                  new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.precio);
              },
              className: 'dt-body-right text-right',
              orderDataType: "num-fmt"
            },{
              render: function(data, type, row, meta) {
                let disabled=""
                if(row.cantidad<1){
                  disabled="disabled"
                }
                return `<button type="button" class="btn btn-success btn-sm btnAnadir" ${disabled} data-id_producto="${row.id_producto}">Añadir</button>`;
              }
            }/*,{
              "data": "precio",
              className: 'd-none precio'
            }*/
          ],
          language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
            "infoEmpty": "Mostrando 0 to 0 of 0 Registros",
            "infoFiltered": "(Filtrado de _MAX_ total registros)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No hay resultados",
            "paginate": {
              "first": "Primero",
              "last": "Ultimo",
              "next": "Siguiente",
              "previous": "Anterior"
            }
          },
          initComplete: function(){
            $('[title]').tooltip();
          },
        })
      }
    
      $(document).ready(function() {
        jsListarProductos(0)
      });

      $(document).on("click",".btnAnadir",function(){
        let prod_anadido=$("#productos_comprar input[name='id_producto[]'][value='"+this.dataset.id_producto+"']");
        //console.log(prod_anadido)
        //console.log("cantidad encontrada");
        if(prod_anadido.length==0){
          let fila=$(this).parent().parent();
          let clon=fila.clone();
          let btn=clon.find("button");
          //console.log(btn);
          btn.parent().html(`
            <input type='number' name='cantidad[]' class='form-control form-control-sm cantidad mx-auto' min='1' style="width: 70px;" value="1" required></input>
          `);
          clon.append(`
            <td class='text-center'>
              <img src='img/icon_baja.png' class='btnEliminar' width='24' height='25' border='0' alt='Eliminar' title='Eliminar'>
            </td>
          `);
          /*clon.find("input[name='precio[]']").attr("disabled",false);
          clon.find("input[name='stock[]']").attr("disabled",false);*/
          clon.find(".enviar_form").attr("disabled",false);

          $("#productos_comprar tbody").append(clon[0]);

          actualizarMontoTotal();
        }else{
          alert("El producto ya fue añadido")
        }
      })

      function actualizarMontoTotal(){
        let total=0;
        $("#productos_comprar tbody tr").each(function(){
          total+=parseInt($(this).find(".precio").val())*parseInt($(this).find(".cantidad").val());
        })
        if(isNaN(total)){total=0;}
        $("#total_compra").html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total))
      }

      $(document).on("keyup change",".cantidad",function(){
        actualizarMontoTotal()
      })

      $(document).on("click",".btnEliminar",function(){
        $(this).parent().parent().remove();
        actualizarMontoTotal()
      })
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
  </body>
</html>
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
  header("Location: listarDevoluciones.php");
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT d.id, date_format(d.fecha_hora,'%d/%m/%Y %H:%i') fecha_hora, d.total, u.usuario FROM devoluciones d LEFT JOIN usuarios u ON d.id_usuario=u.id WHERE d.id = ? ";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);
  
$aProductosDevolver=[];

$sql = "SELECT vd.cantidad as cantidad_producto, vd.subtotal, p.codigo, p.descripcion, p.precio, v.fecha_hora, v.id_almacen, v.id_descuento_aplicado, d.descripcion AS descuento_aplicado, fp.forma_pago FROM ventas_detalle vd INNER JOIN productos p ON p.id = vd.id_producto INNER JOIN ventas v ON vd.id_venta=v.id LEFT JOIN descuentos d ON d.id = v.id_descuento_aplicado LEFT JOIN forma_pago fp ON v.id_forma_pago = fp.id INNER JOIN usuarios u ON v.id_usuario=u.id INNER JOIN devoluciones_detalle dd ON dd.id_venta_detalle=vd.id WHERE dd.id_devolucion=$id ";
/*$q = $pdo->prepare($sql);
$q->execute();
foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {*/
foreach ($pdo->query($sql) as $row) { 
  $descuentos_aplicados = '';
  if ($row["id_descuento_aplicado"] == NULL || $row["id_descuento_aplicado"] == 0) {
    //$descuentos_aplicados = 'Sin descuentos aplicados';
  } else {
    $descuentos_aplicados = $row["descuento_aplicado"];
  }
  
  $aProductosDevolver[]=[
    "fecha_hora" => $row["fecha_hora"],
    "producto" => "(" . $row["codigo"] . ") " . $row["descripcion"],
    "precio" => $row["precio"],
    "subtotal" => $row["subtotal"],
    "cantidad_producto" => $row["cantidad_producto"],
    "forma_pago" => $row["forma_pago"],
    "descuentos_aplicados" => $descuentos_aplicados,
  ];
}

$sql = "SELECT cd.cantidad as cantidad_producto, cd.subtotal, p.codigo, p.descripcion, p.precio, c.fecha_hora, c.id_almacen, c.id_descuento_aplicado, d.descripcion AS descuento_aplicado FROM canjes_detalle cd INNER JOIN productos p ON p.id = cd.id_producto INNER JOIN canjes c ON cd.id_canje=c.id LEFT JOIN descuentos d ON d.id = c.id_descuento_aplicado INNER JOIN usuarios u ON c.id_usuario=u.id INNER JOIN devoluciones_detalle dd ON dd.id_canje_detalle=cd.id WHERE dd.id_devolucion=$id";
/*$q = $pdo->prepare($sql);
$q->execute();
//$devoluciones = $q->fetchAll(PDO::FETCH_ASSOC);
//var_dump($devoluciones);
foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {*/
foreach ($pdo->query($sql) as $row) { 
  $descuentos_aplicados = '';
  if ($row["id_descuento_aplicado"] == NULL || $row["id_descuento_aplicado"] == 0) {
    //$descuentos_aplicados = 'Sin descuentos aplicados';
  } else {
    $descuentos_aplicados = $row["descuento_aplicado"];
  }
  
  $aProductosDevolver[]=[
    "fecha_hora" => $row["fecha_hora"],
    "producto" => "(" . $row["codigo"] . ") " . $row["descripcion"],
    "precio" => $row["precio"],
    "subtotal" => $row["subtotal"],
    "cantidad_producto" => $row["cantidad_producto"],
    "forma_pago" => "Canje",
    "descuentos_aplicados" => $descuentos_aplicados,
  ];
}

Database::disconnect();?>
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
                      <li class="breadcrumb-item">Ver Canje</li>
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
                    <h5>Ver Devolucion</h5>
                  </div>
				          <form class="form theme-form" role="form" method="post" action="#">
                    <div class="card-body">
                      <div class="row">
                        <div class="col">
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Fecha Hora Devolucion</label>
                            <div class="col-sm-9"><?php echo $data['fecha_hora']; ?>hs</div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Usuario</label>
                            <div class="col-sm-9"><?php echo $data['usuario']; ?></div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Productos</label>
                          </div>
                          <div class="form-group row">
                            <div class="col-sm-12">
                              <table class="display" id="dataTables-example666">
                                <thead>
                                  <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                    <th>Cantidad</th>
                                    <th>Forma de Pago</th>
                                  </tr>
                                </thead>
                                <tbody><?php
                                  $precio_total = 0;
                                  foreach ($aProductosDevolver as $data) { 
                                    $precio_total += $data["subtotal"];?>
                                    <tr>
                                      <td><?=date("d/m/Y H:i", strtotime($data["fecha_hora"]))?></td>
                                      <td><?=$data["producto"]?></td>
                                      <td>$<?=number_format($data["precio"], 2, ",", ".");?></td>
                                      <td>$<?=number_format($data["subtotal"], 2, ",", ".");?></td>
                                      <td><?=$data["cantidad_producto"]?></td>
                                      <td><?=$data["forma_pago"]?></td>
                                      <!-- <td><?=$data["descuentos_aplicados"];?></td> -->
                                    </tr><?php
                                  }?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Total devolucion</label>
                            <div class="col-sm-9">$<?php echo number_format($precio_total,2); ?></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <div class="col-sm-9 offset-sm-3">
                        <a href='listarDevoluciones.php' class="btn btn-light">Volver</a>
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
	
    <script>
      $(document).ready(function() {
        $('#dataTables-example666').DataTable({
          stateSave: true,
          responsive: true,
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
          }}
        });
      });
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
  </body>
</html>
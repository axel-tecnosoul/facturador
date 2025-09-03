<?php 
require("config.php");
if(empty($_SESSION['user'])){
    header("Location: index.php");
    die("Redirecting to index.php"); 
}
require 'funciones.php';

$id = null;
if ( !empty($_GET['id'])) {
  $id = $_REQUEST['id'];
}

if ( null==$id ) {
  header("Location: listarMovimientoStock.php");
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<?php include('head_tables.php');?>
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
        <!-- Right sidebar Start-->
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
                      <li class="breadcrumb-item">Movimientos de Stock</li>
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
              <!-- Zero Configuration  Starts-->
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Detalle Movimiento Stock ID #<?php echo $id; ?> &nbsp;<a href="imprimirMovimientoStockTotal.php?id=<?php echo $id; ?>"><img src="img/print.png" width="40" height="30" border="0" alt="imprimir Movimiento Stock Total" title="Imrpimir Total de Prendas"></a></h5>
                  </div><?php
                  /*$ar=["facebook_user_info"=>["info1"=>"value1","info2"=>"value2"],"form_info"=>["id"=>1,"campo1"=>"valor1","campo2"=>"valor2"]];
                  var_dump($ar);
                  var_dump(json_encode($ar));*/
                  
                  //var_dump($_SESSION);?>
                  
                  <div class="card-body">
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th>Movimiento</th>
                            <th>Producto</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Cantidad</th>
                            <th>Usuario</th>
                            <th>Fecha y hora</th>
                            <th>Opciones</th>
                          </tr>
                        </thead>
                        <tbody><?php 
                          include 'database.php';
                          $pdo = Database::connect();
                          $sql = " SELECT smd.id,p.descripcion,(SELECT almacen FROM almacenes a WHERE a.id=smd.id_almacen_origen) AS almacen_origen,(SELECT almacen FROM almacenes a WHERE a.id=smd.id_almacen_destino) AS almacen_destino,cantidad,u.usuario,smd.fecha_hora,(SELECT sm.id FROM stock_movimientos sm WHERE sm.id=smd.id_stock_movimiento) AS Movimiento_Stock FROM stock_movimientos_detalle smd INNER JOIN stock_movimientos sm ON smd.id_stock_movimiento = sm.id INNER JOIN productos p ON smd.id_producto=p.id INNER JOIN usuarios u ON smd.id_usuario=u.id WHERE smd.id_stock_movimiento = $id ORDER BY smd.fecha_hora ASC";
                          /*if ($_SESSION['user']['id_perfil'] == 2) {
                            $sql .= " and a.id = ".$_SESSION['user']['id_almacen'];
                          }*/
                          foreach ($pdo->query($sql) as $row) {
                            echo '<tr>';
                            echo '<td>'. $row["Movimiento_Stock"] . '</td>';
                            echo '<td>'. $row["descripcion"] . '</td>';
                            echo '<td>'. $row["almacen_origen"] . '</td>';
                            echo '<td>'. $row["almacen_destino"] . '</td>';
                            echo '<td>'. $row["cantidad"] . '</td>';
                            echo '<td>'. $row["usuario"].'</td>';
                            echo '<td>'. date("d M Y H:i",strtotime($row["fecha_hora"])) . '</td>';
                            echo '<td>';
                            echo '<a target="_blank" href="imprimirMovimientoStock.php?id='.$row["id"].'"><img src="img/print.png" width="24" height="25" border="0" alt="Imprimir" title="Imprimir"></a>';
                            echo '</td>';
                            echo '</tr>';
                          }
                          Database::disconnect();?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="card-footer">
                    
                      <a href='listarMovimientoStock.php' class="btn btn-light">Volver</a>
                    
                  </div>
                </div>
              </div>
              <!-- Zero Configuration  Ends-->
              <!-- Feature Unable /Disable Order Starts-->
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
    <script src="assets/js/script.js"></script>
	<script>
		$(document).ready(function() {
			$('#dataTables-example666').DataTable({
				stateSave: true,
				responsive: true,
        "order": [[ 5, "asc" ]], //or asc 
        "columnDefs" : [{"targets":5, "type":"date-es"}],
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
    <!-- Plugin used-->
  </body>
</html>
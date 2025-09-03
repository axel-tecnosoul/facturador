<?php 
ini_set( "session.gc_maxlifetime", 600 );
session_start(); 
if(empty($_SESSION['user']))
{
	header("Location: index.php");
	die("Redirecting to index.php"); 
}

?>
<!DOCTYPE html>
<html lang="en">
  <head><?php
    include('head_tables.php');?>
  </head>
  <body>
    <div class="page-wrapper">
      <div class="page-body-wrapper">
        <div class="page-body">
          <div class="container-fluid">
            <a href="listarStock.php" class="btn btn-secondary">VOLVER</a>
            <div class="row"><?php
              include 'database.php';

              $stringMovimientoStock=$_GET["id"];
              $aMovimientoStock=explode("i",$stringMovimientoStock);
              foreach ($aMovimientoStock as $id) {?>
                <div class="col-6">
                  <div class="card">
                    <div class="card-header text-center" style="padding: 20px;">
                      <h5>Movimiento de Stock</h5>
                    </div>
                    <div class="card-body" style="padding: 20px;">
                      <div class="dt-ext table-responsive"><?php 
                        $pdo = Database::connect();
                        $sql = " SELECT sm.id,p.descripcion,(SELECT almacen FROM almacenes a WHERE a.id=sm.id_almacen_origen) AS almacen_origen,(SELECT almacen FROM almacenes a WHERE a.id=sm.id_almacen_destino) AS almacen_destino,cantidad,u.usuario,fecha_hora FROM stock_movimientos sm INNER JOIN productos p ON sm.id_producto=p.id INNER JOIN usuarios u ON sm.id_usuario=u.id WHERE sm.id = ?";
                        $q = $pdo->prepare($sql);
                        $q->execute(array($id));
                        $data = $q->fetch(PDO::FETCH_ASSOC);
                        
                        Database::disconnect();?>
                        <table class="table table-bordered display" id="dataTables-example666">
                          <tbody>
                            <tr>
                              <th style="width: 30%;">Producto</th>
                              <td style="width: 70%;"><?=$data["descripcion"]?></td>
                            </tr>
                            <tr>
                              <th style="width: 30%;">Origen</th>
                              <td style="width: 70%;"><?=$data["almacen_origen"]?></td>
                            </tr>
                            <tr>
                              <th style="width: 30%;">Destino</th>
                              <td style="width: 70%;"><?=$data["almacen_destino"]?></td>
                            </tr>
                            <tr>
                              <th style="width: 30%;">Cantidad</th>
                              <td style="width: 70%;"><?=$data["cantidad"]?></td>
                            </tr>
                            <tr>
                              <th style="width: 30%;">Usuario</th>
                              <td style="width: 70%;"><?=$data["usuario"]?></td>
                            </tr>
                            <tr>
                              <th style="width: 30%;">Fecha y Hora</th>
                              <td style="width: 70%;"><?=date("d M Y H:i",strtotime($data["fecha_hora"]))?></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="card-footer" style="padding: 20px;">
                      <div class="row">
                        <div class="col-6">
                          <h5>Mi Roperito</h5>
                        </div>
                        <div class="col-6 text-right">
                          <?=date("d M Y H:i",strtotime(date("Y-m-d H:i")))?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><?php
              }?>
            </div>
          </div>
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
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

      window.print();
			$('#dataTables-example667').DataTable({
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
	  <script src="assets/js/chart/chartist/chartist.js"></script>
    <script src="assets/js/chart/morris-chart/raphael.js"></script>
    <script src="assets/js/chart/morris-chart/morris.js"></script>
    <script src="assets/js/chart/morris-chart/prettify.min.js"></script>
    <script src="assets/js/chart/chartjs/chart.min.js"></script>
    <script src="assets/js/chart/flot-chart/excanvas.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.time.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.categories.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.stack.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.pie.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.symbol.js"></script>
    <script src="assets/js/chart/google/google-chart-loader.js"></script>
    <script src="assets/js/chart/peity-chart/peity.jquery.js"></script>
    <script src="assets/js/prism/prism.min.js"></script>
	<script src="assets/js/script.js"></script>
    <!-- Plugin used-->
  </body>
</html>
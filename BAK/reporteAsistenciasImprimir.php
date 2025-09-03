<?php 
ini_set( "session.gc_maxlifetime", 600 );
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}?>
<!DOCTYPE html>
<html lang="en">
  <head>
	  <?php include('head_tables.php');?>
  </head>
  <body>
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
      <!-- Page Header Start-->
      
      <!-- Page Header Ends                              -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <!-- Page Sidebar Ends-->
        <!-- Right sidebar Start-->
        <!-- Right sidebar Ends-->
        <div class="page-body">
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <form class="form-inline theme-form mt-3" name="form1" method="post" action="reporteAsistencias.php">
                      <div class="form-group mb-0">
                        Fecha Desde:&nbsp;<input class="form-control" type="date" name="fechaDesde" value="<?=$_POST['fechaDesde']; ?>" readonly="readonly">
                      </div>
                      <div class="form-group mb-0">
                        Fecha Hasta:&nbsp;<input class="form-control" type="date" name="fechaHasta" value="<?=$_POST['fechaHasta']; ?>" readonly="readonly">
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div><?php
			      include 'database.php';?>
            <div class="row">
              <!-- Zero Configuration  Starts-->
              <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Reporte de Asistencias/Puntualidad de Empleados&nbsp;</h5>
                  </div>
                  <div class="card-body">
                    <div class="dt-ext table-responsive">
                      <table class="display" border="1" cellpadding="10" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>Ingreso</th>
                            <th>Egreso</th>
                            <th>Hs Trabajadas</th>
                            <th>IP</th>
                          </tr>
                        </thead>
                        <tbody><?php
                          $pdo = Database::connect();
                          $sql = " SELECT ua.`id`, u.`usuario`, date_format(ua.`fecha`,'%d/%m/%Y'), date_format(ua.`registro_ingreso`,'%H:%i'), date_format(ua.`registro_salida`,'%H:%i'), `ip` FROM `usuarios_asistencia` ua inner join usuarios u on u.id = ua.id_usuario WHERE 1 ";
                          if (!empty($_POST['fechaDesde'])) {
                            $sql .= " AND ua.`fecha` >= '".$_POST['fechaDesde']."'";
                          }
                          if (!empty($_POST['fechaHasta'])) {
                            $sql .= " AND ua.`fecha` <= '".$_POST['fechaHasta']."'";
                          }
                          if (!empty($_POST['id_usuario'])) {
                            $sql .= " AND ua.`id_usuario` = ".$_POST['id_usuario'];
                          }
                          $horas_totales=0;
                          $minutos_totales=0;
                          foreach ($pdo->query($sql) as $row) {
                            echo '<tr>';
                            echo '<td>'. $row[1] . '</td>';
                            echo '<td>'. $row[2] . '</td>';
                            echo '<td>'. $row[3] . 'hs</td>';
                            echo '<td>'. $row[4] . 'hs</td>';
                            echo '<td>';
                            if(!empty($row[3]) and !empty($row[4])){
                              $segundos_entrada = strtotime($row[3]);
                              $segundos_salida = strtotime($row[4]);
                              $diferencia = $segundos_salida - $segundos_entrada;

                              echo gmdate("H:i", $diferencia)."hs";
                              
                              //$segundos_totales += $diferencia;
                              $horas_totales+=gmdate("H", $diferencia);
                              $minutos_totales+=gmdate("i", $diferencia);;
                            }
                            echo '</td>';
                            echo '<td>'. $row[5] . '</td>';
                            echo '</tr>';
                          }
                          Database::disconnect();
                          $horas=intdiv($minutos_totales,60);
                          $minutos_restantes=$minutos_totales-($horas*60);
                          $total=($horas_totales+$horas).":".$minutos_restantes;?>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th colspan="4" style="text-align: right;">Total: </th>
                            <th><?=$total?>hs</th>
                            <th></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
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
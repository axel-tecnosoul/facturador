<?php 
require("config.php");
if(empty($_SESSION['user'])){
  header("Location: index.php");
  die("Redirecting to index.php"); 
}
require 'database.php';?>
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
        <div class="page-body">
          <div class="container-fluid">
            <div class="page-header">
              <div class="row">
                <div class="col-10">
                  <div class="page-header-left">
                    <h3>Panel General de hoy</h3>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="index.php"><i data-feather="home"></i></a></li>
                      <li class="breadcrumb-item active">Dashboard</li>
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
                <div class="col-md-2">
                    <div class="card">
                      <div class="card-body">
                        <div class="chart-widget-dashboard">
                          <div class="media"><?php
                            $contactosRecibidos = 0;?>
                            <div class="media-body">
                              <h5 class="mt-0 mb-0 f-w-600"><span class="counter"><?php echo $contactosRecibidos; ?></span></h5>
                              <p><a href="listarContactos.php">Contactos recibidos</a></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <div class="chart-widget-dashboard">
                          <div class="media"><?php
                            $suscripcionesRecibidas = 0;?>
                            <div class="media-body">
                              <h5 class="mt-0 mb-0 f-w-600"><span class="counter"><?php echo $suscripcionesRecibidas; ?></span></h5>
                              <p><a href="listarSuscripciones.php">Suscripciones recibidas</a></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <div class="card">
                    <div class="card-body">
                      <div class="chart-widget-dashboard">
                        <div class="media"><?php
                          $proveedoresActivos = 0;?>
                          <div class="media-body">
                            <h5 class="mt-0 mb-0 f-w-600"><span class="counter"><?php echo $proveedoresActivos; ?></span></h5>
                            <p><a href="listarProveedores.php">Proveedores activos</a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-body">
                      <div class="chart-widget-dashboard">
                        <div class="media"><?php
                          $canjesPendientes = 0;?>
                          <div class="media-body">
                            <h5 class="mt-0 mb-0 f-w-600"><i data-feather="dollar-sign"></i><span class="counter"><?php echo number_format($canjesPendientes,2,",","."); ?></span></h5>
                            <p><a href="listarVentas.php">Total en canjes pendientes</a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="card">
                    <div class="card-body">
                      <div class="chart-widget-dashboard">
                        <div class="media"><?php
                          $canjesRealizados = 0;?>
                          <div class="media-body">
                            <h5 class="mt-0 mb-0 f-w-600"><span class="counter"><?php echo $canjesRealizados; ?></span></h5>
                            <p><a href="listarCanjes.php">Canjes realizados</a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-body">
                      <div class="chart-widget-dashboard">
                        <div class="media"><?php
                          $pendientePago = 0;?>
                          <div class="media-body">
                            <h5 class="mt-0 mb-0 f-w-600"><i data-feather="dollar-sign"></i><span class="counter"><?php echo number_format($pendientePago,2,",","."); ?></span></h5>
                            <p><a href="listarPagosPendientes.php">Pendiente a pagar</a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-body">
                      <div class="chart-widget-dashboard">
                        <div class="media"><?php
                          $montoPagado = 0;?>
                          <div class="media-body">
                            <h5 class="mt-0 mb-0 f-w-600"><i data-feather="dollar-sign"></i><span class="counter"><?php echo number_format($montoPagado,2,",","."); ?></span></h5>
                            <p><a href="listarVentas.php">Monto pagado</a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-body">
                      <div class="chart-widget-dashboard">
                        <div class="media"><?php
                          $prendasIngresadas = 0;?>
                          <div class="media-body">
                            <h5 class="mt-0 mb-0 f-w-600"><span class="counter"><?php echo $prendasIngresadas; ?></span></h5>
                            <p><a href="listarStock.php">Prendas ingresadas</a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="card">
                    <div class="card-body">
                      <div class="chart-widget-dashboard">
                        <div class="media"><?php
                          $montoStockIngresado = 0;?>
                          <div class="media-body">
                            <h5 class="mt-0 mb-0 f-w-600"><i data-feather="dollar-sign"></i><span class="counter"><?php echo number_format($montoStockIngresado,2,",","."); ?></span></h5>
                            <p><a href="listarStock.php">Monto de stock ingresado</a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-body">
                      <div class="chart-widget-dashboard">
                        <div class="media"><?php
                          $montoVendido = 0;?>
                          <div class="media-body">
                            <h5 class="mt-0 mb-0 f-w-600"><i data-feather="dollar-sign"></i><span class="counter"><?php echo number_format($montoVendido,2,",","."); ?></span></h5>
                            <p><a href="listarVentas.php">Monto total por ventas</a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-body">
                      <div class="chart-widget-dashboard">
                        <div class="media"><?php
                          $prendasVendidas = 0;?>
                          <div class="media-body">
                            <h5 class="mt-0 mb-0 f-w-600"><span class="counter"><?php echo $prendasVendidas; ?></span></h5>
                            <p><a href="listarVentas.php">Prendas vendidas</a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-body">
                      <div class="chart-widget-dashboard">
                        <div class="media"><?php
                          $valuacion = 0;?>
                          <div class="media-body">
                            <h5 class="mt-0 mb-0 f-w-600"><i data-feather="dollar-sign"></i><span class="counter"><?php echo number_format($valuacion,2,",","."); ?></span></h5>
                            <p><a href="listarStock.php">Valuación stock</a></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="card height-equal">
                    <div class="card-header card-header-border">
                      <div class="row">
                        <div class="col-sm-7">
                          <h5><a href="listarTurnos.php">Turnos del día</a></h5>
                        </div>
                        <div class="col-sm-5">
                          <div class="pull-right right-header">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-body recent-notification"><?php
                      $pdo = Database::connect();
                      $sql = " SELECT cl.razon_social,monto_pesos,monto_dolares FROM cobros co inner join clientes cl ON co.id_cliente=cl.id WHERE co.fecha_factura IS NULL AND co.fecha_cobro IS NULL";
                      foreach ($pdo->query($sql) as $row) {?>
                        <div class="media">
                          <h6><?php echo $row[5]; ?></h6>
                          <div class="media-body"><span><?=$row["razon_social"]?> (<?=$row["monto_pesos"]?>) - <?=$row["monto_dolares"]?></span>
                            <p class="f-12"><?=$row[2]?></p>
                          </div>
                        </div><?php 
                      }
                      Database::disconnect();?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <!-- Container-fluid Ends-->
        </div>
          <!-- Container-fluid Ends-->
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
    <script src="assets/js/bootstrap/tableExport.js"></script>
    <script src="assets/js/bootstrap/jquery.base64.js"></script>
    <!-- <script src="assets/js/chart/chartist/chartist.js"></script> -->
    <!-- <script src="assets/js/chart/morris-chart/raphael.js"></script>
    <script src="assets/js/chart/morris-chart/morris.js"></script>
    <script src="assets/js/chart/morris-chart/prettify.min.js"></script>
    <script src="assets/js/chart/morris-chart/morris-script.js"></script>
    <script src="assets/js/chart/chartjs/chart.min.js"></script>
    <script src="assets/js/chart/flot-chart/excanvas.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.time.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.categories.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.stack.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.pie.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.symbol.js"></script>
    <script src="assets/js/chart/google/google-chart-loader.js"></script> -->
    <script src="assets/js/chart/peity-chart/peity.jquery.js"></script>
    <script src="assets/js/prism/prism.min.js"></script>
    <script src="assets/js/clipboard/clipboard.min.js"></script>
    <!-- <script src="assets/js/counter/jquery.waypoints.min.js"></script>
    <script src="assets/js/counter/jquery.counterup.min.js"></script>
    <script src="assets/js/counter/counter-custom.js"></script> -->
    <script src="assets/js/custom-card/custom-card.js"></script>
    <!-- <script src="assets/js/dashboard/project-custom.js"></script> -->
    <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    
    <!-- Plugin used-->
  </body>
</html>
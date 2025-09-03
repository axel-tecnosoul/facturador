<?php 
require("config.php");
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
          <!-- Container-fluid starts--><?php
		      if ($_SESSION['user']['id_perfil'] != 3) {?>
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-2"><?php
                  if ($_SESSION['user']['id_perfil'] == 1) {?>
                    <div class="card">
                      <div class="card-body">
                        <div class="chart-widget-dashboard">
                          <div class="media"><?php
                            $contactosRecibidos = 0;
                            $pdo = Database::connect();
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $sql = "SELECT count(*) cant FROM `contactos` WHERE date_format(fecha_hora,'%d/%m/%Y') = date_format(now(),'%d/%m/%Y') ";
                            $q = $pdo->prepare($sql);
                            $q->execute();
                            $data = $q->fetch(PDO::FETCH_ASSOC);
                            $contactosRecibidos = $data['cant'];
                            Database::disconnect();?>
                            <div class="media-body">
                              <h5 class="mt-0 mb-0 f-w-600"><span class="counter"><?php echo $contactosRecibidos; ?></span></h5>
                              <p><a href="listarContactos.php">Contactos recibidos</a></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div><?php
                  }
                  if ($_SESSION['user']['id_perfil'] == 1) {?>
                    <div class="card">
                      <div class="card-body">
                        <div class="chart-widget-dashboard">
                          <div class="media"><?php
                            $suscripcionesRecibidas = 0;
                            $pdo = Database::connect();
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $sql = "SELECT count(*) cant FROM `suscripciones` WHERE date_format(fecha_hora,'%d/%m/%Y') = date_format(now(),'%d/%m/%Y') ";
                            $q = $pdo->prepare($sql);
                            $q->execute();
                            $data = $q->fetch(PDO::FETCH_ASSOC);
                            $suscripcionesRecibidas = $data['cant'];
                            Database::disconnect();?>
                            <div class="media-body">
                              <h5 class="mt-0 mb-0 f-w-600"><span class="counter"><?php echo $suscripcionesRecibidas; ?></span></h5>
                              <p><a href="listarSuscripciones.php">Suscripciones recibidas</a></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div><?php
                  }?>
                  <div class="card">
                    <div class="card-body">
                      <div class="chart-widget-dashboard">
                        <div class="media"><?php
                          $proveedoresActivos = 0;
                          $pdo = Database::connect();
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                          $sql = "SELECT count(*) cant FROM `proveedores` WHERE activo = 1 ";
                          $q = $pdo->prepare($sql);
                          $q->execute();
                          $data = $q->fetch(PDO::FETCH_ASSOC);
                          $proveedoresActivos = $data['cant'];
                          Database::disconnect();?>
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
                          $canjesPendientes = 0;
                          $pdo = Database::connect();
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                          $sql = "SELECT sum(vd.`subtotal`) total FROM `ventas_detalle` vd inner join ventas v on v.id = vd.id_venta WHERE v.anulada = 0 and vd.`id_modalidad` = 50 and vd.`pagado` = 0 ";
                          $q = $pdo->prepare($sql);
                          $q->execute();
                          $data = $q->fetch(PDO::FETCH_ASSOC);
                          $canjesPendientes = $data['total']/2;
                          Database::disconnect();?>
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
                          $canjesRealizados = 0;
                          $pdo = Database::connect();
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                          $sql = "SELECT count(*) cant FROM `canjes` WHERE date_format(fecha_hora,'%d/%m/%Y') = date_format(now(),'%d/%m/%Y') ";
                          if ($_SESSION['user']['id_perfil'] == 2) {
                            $sql .= " and id_almacen = ".$_SESSION['user']['id_almacen']; 
                          }
                          $q = $pdo->prepare($sql);
                          $q->execute();
                          $data = $q->fetch(PDO::FETCH_ASSOC);
                          $canjesRealizados = $data['cant'];
                          Database::disconnect();?>
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
                          $pendientePago = 0;
                          $pdo = Database::connect();
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                          $sql = "SELECT sum(vd.`subtotal`) total FROM `ventas_detalle` vd inner join ventas v on v.id = vd.id_venta WHERE v.anulada = 0 and vd.`id_modalidad` = 40 and vd.`pagado` = 0 ";
                          $q = $pdo->prepare($sql);
                          $q->execute();
                          $data = $q->fetch(PDO::FETCH_ASSOC);
                          $pendientePago = $data['total']*0.4;
                          Database::disconnect();?>
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
                          $montoPagado = 0;
                          $pdo = Database::connect();
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                          $sql = "SELECT sum(vd.`subtotal`) total FROM `ventas_detalle` vd inner join ventas v on v.id = vd.id_venta WHERE v.anulada = 0 and vd.`id_modalidad` = 40 and vd.`pagado` = 1 ";
                          $q = $pdo->prepare($sql);
                          $q->execute();
                          $data = $q->fetch(PDO::FETCH_ASSOC);
                          $montoPagado = $data['total']*0.4;
                          Database::disconnect();?>
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
                          $prendasIngresadas = 0;
                          $pdo = Database::connect();
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                          $sql = "SELECT sum(cd.`cantidad`) cant FROM `compras_detalle` cd inner join compras c on c.id = cd.id_compra WHERE date_format(c.fecha,'%d/%m/%Y') = date_format(now(),'%d/%m/%Y') ";
                          if ($_SESSION['user']['id_perfil'] == 2) {
                            $sql .= " and c.id_almacen = ".$_SESSION['user']['id_almacen']; 
                          }
                          $q = $pdo->prepare($sql);
                          $q->execute();
                          $data = $q->fetch(PDO::FETCH_ASSOC);
                          $prendasIngresadas = $data['cant'];
                          if(!$prendasIngresadas) $prendasIngresadas=0;
                          Database::disconnect();?>
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
                          $montoStockIngresado = 0;
                          $pdo = Database::connect();
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                          $sql = "SELECT sum(total) total FROM `compras` WHERE date_format(fecha,'%d/%m/%Y') = date_format(now(),'%d/%m/%Y') ";
                          if ($_SESSION['user']['id_perfil'] == 2) {
                            $sql .= " and id_almacen = ".$_SESSION['user']['id_almacen']; 
                          }
                          $q = $pdo->prepare($sql);
                          $q->execute();
                          $data = $q->fetch(PDO::FETCH_ASSOC);
                          $montoStockIngresado = $data['total'];
                          Database::disconnect();?>
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
                          $montoVendido = 0;
                          $pdo = Database::connect();
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                          $sql = "SELECT sum(total_con_descuento) total FROM `ventas` WHERE anulada = 0 and date_format(fecha_hora,'%d/%m/%Y') = date_format(now(),'%d/%m/%Y') ";
                          if ($_SESSION['user']['id_perfil'] == 2) {
                            $sql .= " and id_almacen = ".$_SESSION['user']['id_almacen']; 
                          }
                          $q = $pdo->prepare($sql);
                          $q->execute();
                          $data = $q->fetch(PDO::FETCH_ASSOC);
                          $montoVendido = $data['total'];
                          Database::disconnect();?>
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
                          $prendasVendidas = 0;
                          $pdo = Database::connect();
                          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                          $sql = "SELECT sum(vd.`cantidad`) cant FROM `ventas_detalle` vd inner join ventas v on v.id = vd.id_venta WHERE v.anulada = 0 and date_format(v.fecha_hora,'%d/%m/%Y') = date_format(now(),'%d/%m/%Y') ";
                          if ($_SESSION['user']['id_perfil'] == 2) {
                            $sql .= " and v.id_almacen = ".$_SESSION['user']['id_almacen']; 
                          }
                          $q = $pdo->prepare($sql);
                          $q->execute();
                          $data = $q->fetch(PDO::FETCH_ASSOC);
                          $prendasVendidas = $data['cant'];
                          Database::disconnect();?>
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
                          $valuacion = 0;
                          $pdo = Database::connect();
                          $sql = "SELECT s.cantidad, p.precio FROM stock s inner join productos p on p.id = s.id_producto where s.cantidad > 0 ";
                          if ($_SESSION['user']['id_perfil'] == 2) {
                            $sql .= " and s.id_almacen = ".$_SESSION['user']['id_almacen']; 
                          }
                          //echo $sql;
                          foreach ($pdo->query($sql) as $row) {
                            $valuacion += $row[0]*$row[1];
                          }
                          Database::disconnect();?>
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
                      $sql = " SELECT t.`id`, date_format(t.`fecha_hora`,'%d/%m/%Y %H:%i'), a.`almacen`, t.`cantidad`, date_format(t.`fecha`,'%d/%m/%Y'), date_format(t.`hora`,'%H:%i'), t.`dni`, t.`nombre`, t.`email`, t.`telefono`, e.estado, t.id_estado FROM `turnos` t inner join estados_turno e on e.id = t.`id_estado` inner join almacenes a on a.id = t.id_almacen WHERE date_format(t.`fecha`,'%d/%m/%Y') = date_format(now(),'%d/%m/%Y') ";
                      if ($_SESSION['user']['id_perfil'] == 2) {
                        $sql .= " and a.id = ".$_SESSION['user']['id_almacen']; 
                      }
                      $sql .= " order by a.`almacen`, date_format(t.`hora`,'%H:%i') ";
                      foreach ($pdo->query($sql) as $row) {?>
                        <div class="media">
                          <h6><?php echo $row[5]; ?></h6>
                          <div class="media-body"><span><?php echo $row[7]; ?> (<?php echo $row[3]; ?>) - <?php echo $row[10]; ?></span>
                            <p class="f-12"><?php echo $row[2]; ?></p>
                          </div>
                        </div><?php 
                      }
                      Database::disconnect();?>
                    </div>
                  </div>
                </div>
              </div>
            </div><?php
          }?>
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
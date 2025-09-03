<?php 
session_start(); 
include 'database.php';
if(empty($_SESSION['user']))
{
	header("Location: index.php");
	die("Redirecting to index.php"); 
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
        <div class="page-body">
          <div class="container-fluid">
            <div class="page-header">
              <div class="row">
                <div class="col-10">
                  <div class="page-header-left">
                    <h3><?php include("title.php"); ?></h3>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="index.php"><i data-feather="home"></i></a></li>
                      <li class="breadcrumb-item active">Evolutivo Ventas</li>
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
			        <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h5>Cantidad de Pedidos</h5>
                  </div>
                  <div class="card-body chart-block">
                    <canvas id="myLineCharts"></canvas>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h5>Total Vendido</h5>
                  </div>
                  <div class="card-body chart-block">
                    <canvas id="profitchart"></canvas>
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
    <script src="assets/js/clipboard/clipboard.min.js"></script>
    <script src="assets/js/counter/jquery.waypoints.min.js"></script>
    <script src="assets/js/counter/jquery.counterup.min.js"></script>
    <script src="assets/js/counter/counter-custom.js"></script>
    <script src="assets/js/custom-card/custom-card.js"></script>
    <script src="assets/js/dashboard/project-custom.js"></script>
    <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    <script src="assets/js/typeahead/handlebars.js"></script>
    <script src="assets/js/typeahead/typeahead.bundle.js"></script>
    <script src="assets/js/typeahead/typeahead.custom.js"></script>
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <script src="assets/js/typeahead-search/handlebars.js"></script>
    <script src="assets/js/typeahead-search/typeahead-custom.js"></script>
	<script src="assets/js/chart/morris-chart/raphael.js"></script>
    <script src="assets/js/chart/morris-chart/morris.js"></script>
    <script src="assets/js/chart/morris-chart/prettify.min.js"></script>
    <script src="assets/js/chart/morris-chart/morris-script.js"></script>
    <script src="assets/js/dashboard/dashboard-ecommerce/morris-script.js"></script>
    <script src="assets/js/dashboard/dashboard-ecommerce/owl-carousel.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script>
      var myLineChart = {
        labels: [<?php 
          $pdo = Database::connect();
          $sql = " SELECT date_format(fecha_hora,'%m-%Y') fecha,count(*) cant FROM ventas where anulada = 0 group by fecha order by fecha_hora asc limit 0,12 ";
          foreach ($pdo->query($sql) as $row) {
            echo "'".$row[0]."',";
          }
          Database::disconnect();?>
        ],
        datasets: [{
          fillColor: "transparent",
          strokeColor: endlessAdminConfig.primary,
          pointColor: endlessAdminConfig.primary,
          data: [<?php 
            $pdo = Database::connect();
            $sql = " SELECT date_format(fecha_hora,'%m-%Y') fecha,count(*) cant FROM ventas where anulada = 0 group by fecha order by fecha_hora asc limit 0,12 ";
            foreach ($pdo->query($sql) as $row) {
              echo $row[1].",";
            }
            Database::disconnect();?>
          ]
        }]
      }
      var ctx = document.getElementById("myLineCharts").getContext("2d");
      var LineChartDemo = new Chart(ctx).Line(myLineChart, {
        pointDotRadius: 2,
        pointDotStrokeWidth: 5,
        pointDotStrokeColor: "#ffffff",
        bezierCurve: false,
        scaleShowVerticalLines: false,
        scaleGridLineColor: "#eeeeee"
      });

      var myLineChart1 = {
        labels: [<?php 
          $pdo = Database::connect();
          $sql = " SELECT date_format(fecha_hora,'%m-%Y') fecha,sum(total_con_descuento) total FROM ventas where anulada = 0  group by fecha order by fecha_hora asc limit 0,12 ";
          foreach ($pdo->query($sql) as $row) {
            echo "'".$row[0]."',";
          }
          Database::disconnect();?>
        ],
        datasets: [{
          fillColor: "transparent",
          strokeColor: endlessAdminConfig.primary,
          pointColor: endlessAdminConfig.primary,
          data: [<?php
            $pdo = Database::connect();
            $sql = " SELECT date_format(fecha_hora,'%m-%Y') fecha,sum(total_con_descuento) total FROM ventas where anulada = 0  group by fecha order by fecha_hora asc limit 0,12 ";
            foreach ($pdo->query($sql) as $row) {
              echo $row[1].",";
            }
            Database::disconnect();?>
          ]
        }]
      }
      var ctx = document.getElementById("profitchart").getContext("2d");
      var LineChartDemo = new Chart(ctx).Line(myLineChart1, {
        pointDotRadius: 2,
        pointDotStrokeWidth: 5,
        pointDotStrokeColor: "#ffffff",
        bezierCurve: false,
        scaleShowVerticalLines: false,
        scaleGridLineColor: "#eeeeee"
      });
	  </script>
    <!-- Plugin used-->
  </body>
</html>
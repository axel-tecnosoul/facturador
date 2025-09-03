<?php 
session_start(); 
include 'database.php';
include 'funciones.php';
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_tables.php');?>
    <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
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
                      <li class="breadcrumb-item active">Ranking Proveedores</li>
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
              <div class="col-6">
                <div class="card">
                  <div class="card-header">
                    <h5>Cantidad de proveedoras</h5>
                    <div class="card-header-right d-none">
                      <div class="select2-drpdwn-project select-options">
                        <select class="form-control form-control-primary btn-square" id="periodo">
                          <option value="mensual">Mensual</option>
                          <option value="diario">Anual</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div id="contenedorMyGraph" class="card-body chart-block">
                    <canvas id="myGraph"></canvas>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="card">
                  <div class="card-header">
                    <h5>Cantidad de prendas entregadas</h5>
                    <div class="card-header-right d-none">
                      <div class="select2-drpdwn-project select-options">
                        <select class="form-control form-control-primary btn-square" id="periodo">
                          <option value="mensual">Mensual</option>
                          <option value="diario">Anual</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div id="contenedorMyGraph2" class="card-body chart-block">
                    <canvas id="myGraph2"></canvas>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h5>Cantidad por proveedor</h5>
                    <div class="card-header-right">
                      <div class="select2-drpdwn-project select-options">
                        <select class="form-control form-control-primary btn-square js-example-basic-single w-auto" id="id_proveedor">
                          <option value="0">- Seleccione -</option><?php
                          $pdo = Database::connect();
                          $sql3 = "SELECT id, CONCAT(apellido, ' ', nombre) AS proveedor FROM proveedores";
                          //echo $sql3;
                          foreach ($pdo->query($sql3) as $row) {?>
                            <option value="<?=$row["id"]?>"><?="(".$row["id"].") ".$row["proveedor"]?></option><?php
                          }
                          Database::disconnect();?>
                        </select>
                        <a href="#" target="_blank" id="verProveedorSeleccionado" class="disabled"><img src="img/eye.png" width="24" height="15" border="0" alt="Ver" title="Ver proveedor seleccionado"></a>
                      </div>
                    </div>
                  </div>
                  <div id="contenedorMyGraph3" class="card-body chart-block">
                    <canvas id="myGraph3"></canvas>
                  </div>
                </div>
              </div>
              <div class="col-md-6 d-none">
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

    <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
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
    <!-- <script src="assets/js/chart/chartjs/chart.min.js"></script> -->
    <script src="assets/js/chart/chartjs/chart2.5.0.min.js"></script>
    <script src="assets/js/chart/chartjs/Chart.PieceLabel.js"></script><!-- Plugin viejo encontrado en internet para mostrar los valores en el grafico de dona -->
    
    <!-- <script src="assets/js/chart/chartist/chartist.js"></script>
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
    <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script> -->
    
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
  
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script>
      const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

      $(document).ready(function () {

        obtnerValoresGrafico1();
        obtnerValoresGrafico2();
        obtnerValoresGrafico3();

        $("#id_proveedor").on('change', function(event) {
          var canvasContainer=$("#contenedorMyGraph3");
          canvasContainer.html('');
          canvasContainer.html('<canvas id="myGraph3"></canvas>');
          obtnerValoresGrafico3();
          let href="#";
          let clase="disabled";
          if(this.value>0){
            href="verProveedor.php?id="+this.value;
            clase="";
          }
          $("#verProveedorSeleccionado").attr("href",href).attr("class",clase);
        });
      });

      function obtnerValoresGrafico1(){
        var periodo=$("#periodo").val();
        //console.log(periodo);
        $.ajax({
          type: "POST",
          url: "obtenerDatosGraficoLineasCantProveedoresPrendas.php",
          data: "periodo="+periodo,
          success: function(data) {
            //console.log(data);
            lineGraphData=JSON.parse(data);
            console.log(lineGraphData);
            let id_grafico="myGraph";
            let options={
              tooltips: {
                enabled: true, // Habilitar los tooltips
                mode: 'nearest', // Modo de interacción (puedes ajustarlo según tus necesidades)
                intersect: false, // Permite que el tooltip se muestre en varios elementos cuando se superpongan
                callbacks: {
                  title: function(tooltipItem, data) {
                    // Personalizar el título del tooltip
                    tooltipItem=tooltipItem[0]
                    //console.log("title");
                    //console.log(tooltipItem);
                    //console.log(data);
                    var label = data.labels[tooltipItem.index];
                    var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                    return value+' proveedores han entregado prendas';
                  },
                  label: function(tooltipItem, data) {
                    //console.log("label");
                    //console.log(tooltipItem);
                    //console.log(data);
                    // Personalizar el contenido del tooltip para cada punto
                    var label = data.labels[tooltipItem.index];
                    var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];

                    const fecha=label.split("-")
                    const mes = meses[parseInt(fecha[1])-1];
                    let año = fecha[0];
                    if(tooltipItem.datasetIndex==1){
                      año-=1;
                    }
                    const formatoPersonalizado = mes + " " + año;
                    console.log(formatoPersonalizado); // Resultado: "dic '22" (formato depende de la localización del navegador)
                    //return label + ': ' + value;
                    return formatoPersonalizado;
                  }
                }
              },
              scales: {
                xAxes: [{
                  ticks: {
                    callback: function(value, index, values) {
                      // Formatear el label según tus necesidades
                      const fecha=value.split("-")
                      const mes = meses[parseInt(fecha[1])-1].substring(0, 3);
                      const añoCorto = fecha[0].slice(-2);
                      //const formatoPersonalizado = mes + " '" + añoCorto;
                      const formatoPersonalizado = mes;
                      console.log(formatoPersonalizado); // Resultado: "dic '22" (formato depende de la localización del navegador)
                      return formatoPersonalizado;
                    }
                  }
                }]
              }
            }
            armarGraficoLineas(id_grafico,lineGraphData,options);
          }
        });
      }

      function obtnerValoresGrafico2(){
        var periodo=$("#periodo").val();
        //console.log(periodo);
        $.ajax({
          type: "POST",
          url: "obtenerDatosGraficoLineasCantPrendasEntregadas.php",
          data: "periodo="+periodo,
          success: function(data) {
            //console.log(data);
            lineGraphData=JSON.parse(data);
            console.log(lineGraphData);
            let id_grafico="myGraph2";
            let options={
              tooltips: {
                enabled: true, // Habilitar los tooltips
                mode: 'nearest', // Modo de interacción (puedes ajustarlo según tus necesidades)
                intersect: false, // Permite que el tooltip se muestre en varios elementos cuando se superpongan
                callbacks: {
                  title: function(tooltipItem, data) {
                    // Personalizar el título del tooltip
                    tooltipItem=tooltipItem[0]
                    //console.log("title");
                    //console.log(tooltipItem);
                    //console.log(data);
                    var label = data.labels[tooltipItem.index];
                    var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                    return value+' prendas entregadas';
                  },
                  label: function(tooltipItem, data) {
                    //console.log("label");
                    //console.log(tooltipItem);
                    //console.log(data);
                    // Personalizar el contenido del tooltip para cada punto
                    var label = data.labels[tooltipItem.index];
                    var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];

                    const fecha=label.split("-")
                    const mes = meses[parseInt(fecha[1])-1];
                    let año = fecha[0];
                    if(tooltipItem.datasetIndex==1){
                      año-=1;
                    }
                    const formatoPersonalizado = mes + " " + año;
                    console.log(formatoPersonalizado); // Resultado: "dic '22" (formato depende de la localización del navegador)
                    //return label + ': ' + value;
                    return formatoPersonalizado;
                  }
                }
              },
              scales: {
                xAxes: [{
                  ticks: {
                    callback: function(value, index, values) {
                      // Formatear el label según tus necesidades
                      const fecha=value.split("-")
                      const mes = meses[parseInt(fecha[1])-1].substring(0, 3);
                      const añoCorto = fecha[0].slice(-2);
                      //const formatoPersonalizado = mes + " '" + añoCorto;
                      const formatoPersonalizado = mes;
                      console.log(formatoPersonalizado); // Resultado: "dic '22" (formato depende de la localización del navegador)
                      return formatoPersonalizado;
                    }
                  }
                }]
              }
            }
            armarGraficoLineas(id_grafico,lineGraphData,options);
          }
        });
      }

      function obtnerValoresGrafico3(){
        var id_proveedor=$("#id_proveedor").val();
        //console.log(id_proveedor);
        $.ajax({
          type: "POST",
          url: "obtenerDatosGraficoLineasCantPrendasPorProveedor.php",
          data: "id_proveedor="+id_proveedor,
          success: function(data) {
            //console.log(data);
            lineGraphData=JSON.parse(data);
            console.log(lineGraphData);
            let id_grafico="myGraph3";
            let options={
              tooltips: {
                enabled: true, // Habilitar los tooltips
                mode: 'nearest', // Modo de interacción (puedes ajustarlo según tus necesidades)
                intersect: false, // Permite que el tooltip se muestre en varios elementos cuando se superpongan
                callbacks: {
                  title: function(tooltipItem, data) {
                    // Personalizar el título del tooltip
                    tooltipItem=tooltipItem[0]
                    //console.log("title");
                    //console.log(tooltipItem);
                    //console.log(data);
                    var label = data.labels[tooltipItem.index];
                    var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                    return value+' prendas entregadas';
                  },
                  label: function(tooltipItem, data) {
                    //console.log("label");
                    //console.log(tooltipItem);
                    //console.log(data);
                    // Personalizar el contenido del tooltip para cada punto
                    var label = data.labels[tooltipItem.index];
                    var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];

                    const fecha=label.split("-")
                    const mes = meses[parseInt(fecha[1])-1];
                    let año = fecha[0];
                    if(tooltipItem.datasetIndex==1){
                      año-=1;
                    }
                    const formatoPersonalizado = mes + " " + año;
                    console.log(formatoPersonalizado); // Resultado: "dic '22" (formato depende de la localización del navegador)
                    //return label + ': ' + value;
                    return formatoPersonalizado;
                  }
                }
              },
              scales: {
                xAxes: [{
                  ticks: {
                    callback: function(value, index, values) {
                      // Formatear el label según tus necesidades
                      const fecha=value.split("-")
                      const mes = meses[parseInt(fecha[1])-1].substring(0, 3);
                      const añoCorto = fecha[0].slice(-2);
                      //const formatoPersonalizado = mes + " '" + añoCorto;
                      const formatoPersonalizado = mes;
                      console.log(formatoPersonalizado); // Resultado: "dic '22" (formato depende de la localización del navegador)
                      return formatoPersonalizado;
                    }
                  }
                }],
                yAxes: [{
                  ticks: {
                    beginAtZero: true, // Establecer el valor mínimo del eje y en 0
                    min: 0 // Establecer el tamaño del paso en 1 para mostrar solo números enteros
                  }
                }]
              }
            }
            armarGraficoLineas(id_grafico,lineGraphData,options);
          }
        });
      }

      function armarGraficoLineas(id_grafico,lineGraphData,options=undefined){
        //doughnutData=JSON.parse(etapas);
        if(lineGraphData==undefined){
          var lineGraphData2 = getValoresDefectoGraficoLineas();
          console.log(lineGraphData2);
        }
        
        var lineGraphOptions = {
            /*scaleShowGridLines: true,
            scaleGridLineColor: "rgba(0,0,0,.05)",
            scaleGridLineWidth: 1,
            scaleShowHorizontalLines: true,
            scaleShowVerticalLines: true,
            bezierCurve: true,
            bezierCurveTension: 0.4,
            pointDot: true,
            pointDotRadius: 4,
            pointDotStrokeWidth: 1,
            pointHitDetectionRadius: 20,
            datasetStroke: true,
            datasetStrokeWidth: 2,
            datasetFill: true,
            legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",*/
            /*tooltips: {
              enabled: true, // Habilitar los tooltips
              mode: 'nearest', // Modo de interacción (puedes ajustarlo según tus necesidades)
              intersect: false, // Permite que el tooltip se muestre en varios elementos cuando se superpongan
              callbacks: {
                title: function(tooltipItem, data) {
                  // Personalizar el título del tooltip
                  tooltipItem=tooltipItem[0]
                  //console.log("title");
                  //console.log(tooltipItem);
                  //console.log(data);
                  var label = data.labels[tooltipItem.index];
                  var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                  return value+' proveedores han entregado prendas';
                },
                label: function(tooltipItem, data) {
                  console.log("label");
                  console.log(tooltipItem);
                  console.log(data);
                  // Personalizar el contenido del tooltip para cada punto
                  var label = data.labels[tooltipItem.index];
                  var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];

                  const fecha=label.split("-")
                  const mes = meses[parseInt(fecha[1])-1];
                  let año = fecha[0];
                  if(tooltipItem.datasetIndex==1){
                    año-=1;
                  }
                  const formatoPersonalizado = mes + " " + año;
                  console.log(formatoPersonalizado); // Resultado: "dic '22" (formato depende de la localización del navegador)
                  //return label + ': ' + value;
                  return formatoPersonalizado;
                }
              }
            },
            scales: {
              xAxes: [{
                ticks: {
                  callback: function(value, index, values) {
                    // Formatear el label según tus necesidades
                    const fecha=value.split("-")
                    const mes = meses[parseInt(fecha[1])-1].substring(0, 3);
                    const añoCorto = fecha[0].slice(-2);
                    //const formatoPersonalizado = mes + " '" + añoCorto;
                    const formatoPersonalizado = mes;
                    console.log(formatoPersonalizado); // Resultado: "dic '22" (formato depende de la localización del navegador)
                    return formatoPersonalizado;
                  }
                }
              }]
            }*/
        };

        if(options!=undefined){
          lineGraphOptions=options
        }
        //console.log(lineGraphData);
        var lineCtx = document.getElementById(id_grafico).getContext("2d");
        //var myLineCharts = new Chart(lineCtx).Line(lineGraphData, lineGraphOptions);
        var myLineCharts = new Chart(lineCtx,{
          type:"line",
          data: lineGraphData,
          options: lineGraphOptions
        })
        
      }

      function getValoresDefectoGraficoLineas(){
        return lineGraphData = {
          labels: ["January", "February", "March", "April", "May", "June", "July"],
          datasets: [{
              label: "My First dataset",
              fillColor: "rgba(68, 102, 242, 0.3)",
              strokeColor: "#0B3B0B",
              pointColor: "#0B3B0B",
              pointStrokeColor: "#fff",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "#000",
              data: [10, 59, 80, 81, 56, 55, 40]
          }, {
              label: "My Second dataset",
              fillColor: "rgba(30, 166, 236, 0.3)",
              strokeColor: "#1ea6ec",
              pointColor: "#1ea6ec",
              pointStrokeColor: "#fff",
              pointHighlightFill: "#000",
              pointHighlightStroke: "rgba(30, 166, 236, 1)",
              data: [28, 48, 40, 19, 86, 27, 90]
          }]
        };
      }

      /*var myLineChart = {
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
      });*/
	  </script>
    <!-- Plugin used-->
  </body>
</html>
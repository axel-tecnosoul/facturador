<?php
include_once("config.php");
//session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}
include_once("funciones.php");
include_once("database.php");
$hoy=date("Y-m-d");
//$desde="2024-01-01";
$desde=date("Y-m-01",strtotime($hoy." -6 month"));
$hasta=$hoy;?>
<!DOCTYPE html>
<html lang="en">
  <head>
	  <?php include('head_tables.php');?>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select-1.13.14/dist/css/bootstrap-select.min.css">
  </head>
  <style>
    td.child {
      background-color: beige;
    }
    .multiselect{
      color:#212529 !important;
      background-color:#fff;
      border-color:#ccc;
    }
  </style>
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
                      <li class="breadcrumb-item">Rentabilidad</li>
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
                    <h5>Rentabilidad</h5>
                  </div>
                  <div class="card-body">
                    <div class="row"><?php
                      $pdo = Database::connect();
                      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                      
                      //$label=[1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0];
                      //generamos un array con los ultimos 12 meses
                      $ultimosDoceMeses=ultimosDoceMeses();

                      $datasets=[];
                      $aLabels=[];
                      $aColoresProductos=[];
                      $formatoFechaFin="DATE_FORMAT(fecha_cobro,'%m-%Y')";

                      $modoDebug=0;

                      $aMontos=["Pesos"=>"monto_pesos","USD"=>"monto_dolares"];
                      foreach ($aMontos as $key => $monto) {
                          $color=randomColor();
                          $aColoresProductos[$key]=$color;

                          //$sql2 = "SELECT $formatoFechaFin AS mes,SUM($monto) AS monto FROM cobros WHERE fecha_cobro IS NOT NULL GROUP BY $formatoFechaFin";//MONTH(fecha_inicio)
                          $sql2 = "SELECT $formatoFechaFin AS mes, SUM($monto) AS monto FROM cobros WHERE fecha_cobro IS NOT NULL AND fecha_cobro >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) GROUP BY $formatoFechaFin";
                          $q2 = $pdo->prepare($sql2);
                          $q2->execute();

                          if ($modoDebug==1) {
                              $q2->debugDumpParams();
                              echo "<br><br>Afe: ".$q2->rowCount();
                              echo "<br><br>";
                          }

                          if($q2->rowCount()>0){
                              $data=$ultimosDoceMeses;
                              while ($fila2 = $q2->fetch(PDO::FETCH_ASSOC)) {
                                $data[$fila2["mes"]]+=$fila2["monto"];
                              }
                              $datasets[$key][]=[
                                  "label"=> $key,//no hace nada, se debe agregar la leyenda manualmente
                                  "backgroundColor"=> $color,
                                  "borderColor"=> $color,
                                  //"spanGaps"=>false,//para no mostrar los que estan en 0
                                  "fill"=> false,
                                  "data"=> array_values($data)
                              ];
                              $aLabels[$key]=$color;
                          }
                      }
                      
                      $rentabilidadPesos=[
                          "labels"=>formatFechaGraficoLineasPorMeses($ultimosDoceMeses),//array_keys
                          "datasets"=>$datasets["Pesos"]
                      ];

                      $rentabilidadDolares=[
                        "labels"=>formatFechaGraficoLineasPorMeses($ultimosDoceMeses),//array_keys
                        "datasets"=>$datasets["USD"]
                      ];
                      
                      Database::disconnect();?>
                      <div class="card-header col-12"><h5>Rentabilidad</h5></div><br>
                      <div id="contenedorMyGraph" class="card-body chart-block col-6">
                        <canvas id="rentabilidadPesos"></canvas>
                      </div>
                      <div id="contenedorMyGraph" class="card-body chart-block col-6">
                        <canvas id="rentabilidadDolares"></canvas>
                      </div>
                    </div>
                    <div class="row">
                      <table class="table">
                        <tr>
                          <td class="text-right border-0 p-1">Desde: </td>
                          <td class="border-0 p-1">
                            <input type="date" id="desde" value="<?=$desde?>" class="form-control form-control-sm filtraTabla">
                          </td>
                          <!-- <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Forma de pago:</td> -->
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                            <label for="id_cliente">Cliente:</label><br>
                            <select id="id_cliente" class="form-control form-control-sm filtraTabla selectpicker w-100" data-style="multiselect" data-selected-text-format="count > 1" data-actions-box="true" multiple><?php
                                $pdo = Database::connect();
                                $sql = "SELECT c.id AS id_cliente,c.razon_social FROM clientes c";
                                foreach ($pdo->query($sql) as $row) {?>
                                  <option value="<?=$row["id_cliente"]?>"><?=$row["razon_social"]?></option><?php
                                }
                                Database::disconnect();?>
                            </select>
                          </td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                            <label class="d-block" for="checkbox-agrupar-x-mes">
                              <input class="checkbox_animated filtraTabla" value="si" checked required id="checkbox-agrupar-x-mes" type="checkbox" name="agrupar_x_mes[]"> Agrupar por mes
                            </label>
                          </td>
                          <!-- <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                            <label for="tipo_comprobante">Tipo Cbte:</label><br>
                            <select id="tipo_comprobante" class="form-control form-control-sm filtraTabla selectpicker w-100" data-style="multiselect" data-selected-text-format="count > 1" multiple>
                              <option value="R">Recibo</option>
                              <option value="B">Factura B</option>
                              <option value="NCB">Nota de Credito B</option>
                            </select>
                          </td> -->
                        </tr>
                        <tr>
                          <td class="text-right border-0 p-1">Hasta: </td>
                          <td class="border-0 p-1">
                            <input type="date" id="hasta" value="<?=$hasta?>" class="form-control form-control-sm filtraTabla">
                          </td>
                        </tr>
                      </table>
                    </div>
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Cliente</th>
                            <!-- <th class="text-center">Fecha y hora alta</th> -->
                            <th class="text-center">Fecha cobro</th>
                            <th class="text-center">Rentabilidad Pesos</th>
                            <th class="text-center">Rentabilidad USD</th>
                            <th class="none">Monto Pesos:</th>
                            <th class="none">Monto USD:</th>
                            <th class="none">Total pagado:</th>
                            <th class="none">Detalle:</th>
                            <th class="none">Fecha factura:</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th></th>
                            <th></th>
                            <!-- <th></th> -->
                            <th>Totales</th>
                            <th class="text-right"></th>
                            <th class="text-right"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                          </tr>
                        </tfoot>
                        <tbody></tbody>
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
        <?php include("footer.php"); ?>
      </div>
    </div>

    <div class="modal fade" id="eliminarModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Confirmación</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          </div>
          <div class="modal-body">¿Está seguro que desea eliminar la venta?</div>
          <div class="modal-footer">
            <a id="btnEliminarVenta" class="btn btn-primary">Eliminar</a>
            <button class="btn btn-light" type="button" data-dismiss="modal" aria-label="Close">Volver</button>
          </div>
        </div>
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

    <script src="vendor/bootstrap-select-1.13.14/dist/js/bootstrap-select.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/js/i18n/defaults-es_ES.js"></script>
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

    <script src="assets/js/chart/chartjs/chart2.5.0.min.js"></script>
    <script src="assets/js/chart/chartjs/Chart.PieceLabel.js"></script><!-- Plugin viejo encontrado en internet para mostrar los valores en el grafico de dona -->
    <!-- Plugins JS Ends-->
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
	<script>

    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    function openModalEliminarVenta(idVenta){
      $('#eliminarModal').modal("show");
      document.getElementById("btnEliminarVenta").href="anularVenta.php?id="+idVenta;
    }

		$(document).ready(function() {

      armarGraficoLineas("rentabilidadPesos","ARS",<?=json_encode($rentabilidadPesos)?>);
      armarGraficoLineas("rentabilidadDolares","USD",<?=json_encode($rentabilidadDolares)?>);

      getCobros();
      $(".filtraTabla").on("change",getCobros);

		});

    function getCobros(){
      let desde=$("#desde").val();
      let hasta=$("#hasta").val();
      let id_cliente=$("#id_cliente").val();
      let agrupar_x_mes=$("input[name='agrupar_x_mes[]']:checked").val();

      let table=$('#dataTables-example666')
      table.DataTable().destroy();
      table.DataTable({
        //dom: 'rtip',
        //serverSide: true,
        processing: true,
        ajax:{
          url:'ajaxListarCobros.php?desde='+desde+'&hasta='+hasta+'&id_cliente='+id_cliente+"&agrupar_x_mes="+agrupar_x_mes,dataSrc:""
        },
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
          }
        },
        "columns":[
          {"data": "id"},
          {"data": "razon_social"},
          //{"data": "fecha_cobro"},
          {
            render: function(data, type, row, meta) {
              //console.log(row.fecha_cobro);
              //let fecha_cobro=JSON.parse(row.fecha_cobro);
              let fecha_cobro=row.fecha_cobro;
              //console.log(fecha_cobro);
              if (type === 'display') {
                //return moment(data).format('YYYY-MM-DD HH:mm:ss');
                return fecha_cobro[1];
              }
              return fecha_cobro[0];
            }
          },
          //{"data": "fecha_hora_alta"},
          {
            render: function(data, type, row, meta) {
              return Intl.NumberFormat('es-AR', {style: 'currency', currency: 'ARS', minimumFractionDigits: 0}).format(row.rentabilidad_pesos)
            },
            className: "dt-body-right",
          },
          {
            render: function(data, type, row, meta) {
              return Intl.NumberFormat('es-AR', {style: 'currency', currency: 'USD', minimumFractionDigits: 2}).format(row.rentabilidad_usd)
            },
            className: "dt-body-right",
          },
          {
            render: function(data, type, row, meta) {
              return Intl.NumberFormat('es-AR', {style: 'currency', currency: 'ARS', minimumFractionDigits: 0}).format(row.monto_pesos)
            },
            className: "dt-body-right",
          },
          {
            render: function(data, type, row, meta) {
              return Intl.NumberFormat('es-AR', {style: 'currency', currency: 'USD', minimumFractionDigits: 0}).format(row.monto_dolares)
            },
            className: "dt-body-right",
          },
          {
            render: function(data, type, row, meta) {
              return Intl.NumberFormat('es-AR', {style: 'currency', currency: 'ARS', minimumFractionDigits: 0}).format(row.total_pagado)
            },
            className: "dt-body-right",
          },
          {"data": "detalle"},
          {"data": "fecha_factura"},
        ],
        initComplete: function(settings, json){
          var api = this.api();
          let rentabilidadPesos=rentabilidadDolares=0;
          
          json.forEach(item => {
            if (item.rentabilidad_pesos !== "") {
              rentabilidadPesos += parseInt(item.rentabilidad_pesos);
            }
            if (item.rentabilidad_usd !== "") {
              rentabilidadDolares += parseFloat(item.rentabilidad_usd);
            }
            
          });
          
          rentabilidadPesos = Intl.NumberFormat('es-AR', {style: 'currency', currency: 'ARS', minimumFractionDigits: 0}).format(rentabilidadPesos)
          rentabilidadDolares = Intl.NumberFormat('es-AR', {style: 'currency', currency: 'USD', minimumFractionDigits: 2}).format(rentabilidadDolares)

          $(api.column(3).footer()).html(rentabilidadPesos);
          $(api.column(4).footer()).html(rentabilidadDolares);

          $('[title]').tooltip();
        }
			})

      table.on('processing.dt', function (e, settings, processing) {
        let firstCell = table.find("td:first");
        if (processing) {
          // Si está en proceso, establecer colspan en 2 (o el valor deseado)
          //firstCell.attr('colspan', 1);
          firstCell.removeAttr('colspan');
        } else {
          // Si el procesamiento ha terminado, eliminar el colspan
          //firstCell.removeAttr('colspan');
        }
      });

    }

    function armarGraficoLineas(id,moneda,lineGraphData){
      if(lineGraphData==undefined){
        lineGraphData=getValoresDefectoGraficoLineas();
        console.log(lineGraphData);
      }
      var lineGraphOptions = {
        scales: {
          yAxes: [{
            ticks: {
              callback: function(value, index, values) {
                return new Intl.NumberFormat('es-ES', { style: 'currency', currency: moneda, minimumFractionDigits: 0 }).format(value);
              }
            }
          }],
        },
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
              if(moneda=="USD"){
                console.log(value);
                console.log(typeof(value));
              }
              return new Intl.NumberFormat('es-ES', { style: 'currency', currency: moneda, minimumFractionDigits: 0 }).format(value);
            },
            label: function(tooltipItem, data) {
              // Personalizar el contenido del tooltip para cada punto
              var label = data.labels[tooltipItem.index];
              var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
              return label;
            }
          }
        },
        /*interaction: {
          intersect: false,
        },
        scales: {
          x: {
            display: true,
            title: {
              display: true
            }
          },
          y: {
            display: true,
            title: {
              display: true,
              text: 'Value'
            },
            suggestedMin: -10,
            suggestedMax: 200
          }
        }*/
      };
      //console.log(lineGraphData);
      var lineCtx = document.getElementById(id).getContext("2d");
      var myLineCharts = new Chart(lineCtx,{
        type:"line",
        data: lineGraphData,
        options: lineGraphOptions
      })
    }

		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>
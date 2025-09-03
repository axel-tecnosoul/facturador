<?php
include_once("config.php");
//session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}
include_once("funciones.php");
include_once("database.php");?>
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
                      <li class="breadcrumb-item">Pagos</li>
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
                    <h5>Pagos
                      &nbsp;<a href="nuevoCobro.php"><img src="img/icon_alta.png" width="24" height="25" border="0" alt="Nuevo Cobro" title="Nuevo Cobro"></a>
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <table class="table">
                        <tr>
                          <td class="text-right border-0 p-1">Desde: </td>
                          <td class="border-0 p-1">
                            <input type="date" id="desde" value="<?=date("Y-m-d")?>" class="form-control form-control-sm filtraTabla">
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
                            <label for="id_colaborador">Colaborador:</label><br>
                            <select id="id_colaborador" class="form-control form-control-sm filtraTabla selectpicker w-100" data-style="multiselect" data-selected-text-format="count > 1" data-actions-box="true" multiple><?php
                                $pdo = Database::connect();
                                $sql = "SELECT id,nombre FROM colaboradores";
                                foreach ($pdo->query($sql) as $row) {?>
                                  <option value="<?=$row["id"]?>"><?=$row["nombre"]?></option><?php
                                }
                                Database::disconnect();?>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td class="text-right border-0 p-1">Hasta: </td>
                          <td class="border-0 p-1">
                            <input type="date" id="hasta" value="<?=date("Y-m-d")?>" class="form-control form-control-sm filtraTabla">
                          </td>
                        </tr>
                      </table>
                    </div>
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Colaborador</th>
                            <th class="text-center">Cliente</th>
                            <!-- <th class="text-center">Fecha y hora alta</th> -->
                            <th class="text-center">Fecha pago</th>
                            <th class="text-center">Monto Pesos</th>
                            <th class="text-center">Opciones</th>
                            <th class="none">Observaciones:</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <!-- <th></th> -->
                            <th>Totales</th>
                            <th class="text-right"></th>
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
    <!-- Plugins JS Ends-->
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="assets/js/script.js"></script>
	<script>

    function openModalEliminarVenta(idVenta){
      $('#eliminarModal').modal("show");
      document.getElementById("btnEliminarVenta").href="anularVenta.php?id="+idVenta;
    }

		$(document).ready(function() {

      getPagos();
      $(".filtraTabla").on("change",getPagos);

		});

    function getPagos(){
      let desde=$("#desde").val();
      let hasta=$("#hasta").val();
      let id_cliente=$("#id_cliente").val();
      let id_colaborador=$("#id_colaborador").val();

      let table=$('#dataTables-example666')
      table.DataTable().destroy();
      table.DataTable({
        //dom: 'rtip',
        //serverSide: true,
        processing: true,
        ajax:{
          //url:'ajaxListarCobros.php?desde='+desde+'&hasta='+hasta+'&tipo_fecha='+tipo_fecha+'&forma_pago='+forma_pago+'&tipo_comprobante='+tipo_comprobante+'&id_almacen='+id_almacen
          url:'ajaxListarPagos.php?desde='+desde+'&hasta='+hasta+'&id_cliente='+id_cliente+'&id_colaborador='+id_colaborador,dataSrc:""
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
          {"data": "colaborador"},
          {"data": "razon_social"},
          {"data": "fecha_pago"},
          //{"data": "fecha_hora_alta"},
          {
            render: function(data, type, row, meta) {
              return Intl.NumberFormat('es-AR', {style: 'currency', currency: 'ARS', minimumFractionDigits: 0}).format(row.monto)
            },
            className: "dt-body-right",
          },
          {"data": "acciones"},
          {"data": "observaciones"},
        ],
        initComplete: function(settings, json){
          var api = this.api();
          let totalPesos=0;
          let totalDolares=0;
          console.log(json);
          json.forEach(item => {
            if (item.monto_pesos !== "") {
              totalPesos += parseInt(item.monto);
            }
          });
          console.log(totalPesos,totalDolares);
          $(api.column(4).footer()).html(Intl.NumberFormat('es-AR', {style: 'currency', currency: 'ARS', minimumFractionDigits: 0}).format(totalPesos));
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

		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>
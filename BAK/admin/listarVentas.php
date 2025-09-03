<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}
include_once("funciones.php");?>
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
                      <li class="breadcrumb-item">Ventas</li>
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
                    <h5>Ventas
                      &nbsp;<a href="nuevaVenta.php"><img src="img/icon_alta.png" width="24" height="25" border="0" alt="Nueva Venta" title="Nueva Venta"></a>
                      &nbsp;<a href="exportVentas.php"><img src="img/xls.png" width="24" height="25" border="0" alt="Exportar Ventas" title="Exportar Ventas"></a>
                      &nbsp;<a href="listarVentasAnuladas.php"><img src="img/canceled.png" width="24" height="25" border="0" alt="Ventas Eliminadas" title="Ventas Eliminadas"></a>
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <table class="table">
                        <tr>
                          <td class="text-right border-0 p-1">Desde: </td>
                          <td class="border-0 p-1"><input type="date" id="desde" value="<?=date("Y-m-d")?>" class="form-control form-control-sm filtraTabla"></td>
                          <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Forma de pago:</td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                            <select id="forma_pago" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-selected-text-format="count > 1" data-actions-box="true" multiple><?php
                              include 'database.php';
                              $pdo = Database::connect();
                              $sql = " SELECT id, forma_pago FROM forma_pago";
                              foreach ($pdo->query($sql) as $row) {?>
                                <option value="<?=$row["id"]?>"><?=$row["forma_pago"]?></option><?php
                              }
                              Database::disconnect();?>
                            </select>
                          </td>
                          <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1">Tipo Cbte:</td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                            <select id="tipo_comprobante" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-selected-text-format="count > 1" multiple>
                              <option value="R">Recibo</option>
                              <!-- <option value="A">Factura A</option> -->
                              <option value="B">Factura B</option>
                              <!-- <option value="NCA">Nota de Credito A</option> -->
                              <option value="NCB">Nota de Credito B</option>
                            </select>
                          </td>
                          <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1"><?php
                            if ($_SESSION['user']['id_perfil'] == 1) {
                              echo "Almacen: ";
                            }?>
                            <!-- Tipo comprobante: -->
                          </td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1"><?php
                            if ($_SESSION['user']['id_perfil'] == 1) {?>
                              <select id="id_almacen" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect">
                                <option value="0">- Todos -</option><?php
                                $pdo = Database::connect();
                                $sql = " SELECT id, almacen FROM almacenes";
                                foreach ($pdo->query($sql) as $row) {?>
                                  <option value="<?=$row["id"]?>"><?=$row["almacen"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select><?php
                            }else{?>
                              <input type="hidden" id="id_almacen" value="<?=$_SESSION['user']['id_almacen']?>"><?php
                            }?>
                          </td>
                        </tr>
                        <tr>
                          <td class="text-right border-0 p-1">Hasta: </td>
                          <td class="border-0 p-1"><input type="date" id="hasta" value="<?=date("Y-m-d")?>" class="form-control form-control-sm filtraTabla"></td>
                        </tr>
                      </table>
                    </div>
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Fecha/Hora</th>
                            <th>Tipo Cbte.</th>
                            <th>Almacen</th>
                            <th>Forma de pago</th>
                            <th>Total</th>
                            <th>Opciones</th>
                            <th class="none">Subtotal</th>
                            <th class="none">Descuento</th>
                            <th class="none">Modalidad venta</th>
                            <th class="none">Cliente</th>
                            <th class="none">DNI</th>
                            <th class="none">Dirección</th>
                            <th class="none">E-Mail</th>
                            <th class="none">Teléfono</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th colspan="5">Total</th>
                            <th>Total</th>
                            <th colspan="9">Opciones</th>
                          </tr>
                        </tfoot>
                        <tbody><?php
                          //include 'database.php';
                          /*$pdo = Database::connect();
                          $sql = " SELECT v.id, date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora, a.almacen, v.nombre_cliente, v.dni, v.direccion, v.email, v.telefono, v.total, d.descripcion, v.total_con_descuento, v.id_cierre_caja,v.tipo_comprobante,fp.forma_pago,v.estado FROM ventas v inner join almacenes a on a.id = v.id_almacen left join descuentos d on d.id = v.id_descuento_aplicado INNER JOIN forma_pago fp ON v.id_forma_pago=fp.id WHERE v.anulada = 0 ";
                          if ($_SESSION['user']['id_perfil'] != 1) {
                            $sql .= " and a.id = ".$_SESSION['user']['id_almacen']; 
                          }
                          foreach ($pdo->query($sql) as $row) {
                            echo '<tr>';
                            echo '<td>'. $row["id"] . '</td>';
                            echo '<td>'. $row["fecha_hora"] . 'hs</td>';
                            $tipo_cbte=get_nombre_comprobante($row["tipo_comprobante"]);
                            $estado=$row["estado"];
                            $class="";
                            if($estado=="A"){
                              $class="badge badge-success";
                            }
                            if($estado=="R" or $estado=="E"){
                              $class="badge badge-danger";
                            }
                            echo '<td><span class="'.$class.'">'. $tipo_cbte . '</span></td>';
                            echo '<td>'. $row["almacen"] . '</td>';
                            echo '<td>'. $row["forma_pago"] . '</td>';
                            //echo '<td>$'. number_format($row["total_con_descuento"],2,",",".") . '</td>';
                            echo '<td>$'. number_format($row["total_con_descuento"],2) . '</td>';
                            echo '<td>';
                            echo '<a href="verVenta.php?id='.$row["id"].'"><img src="img/eye.png" width="24" height="15" border="0" alt="Ver Venta" title="Ver Venta"></a>';
                            echo '&nbsp;&nbsp;';
                            if(($_SESSION["user"]["id_perfil"]==1 or $row["id_cierre_caja"]==0) and $row["tipo_comprobante"]=="R"){
                              echo '<a href="#" data-toggle="modal" data-original-title="Confirmación" data-target="#eliminarModal_'.$row["id"].'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Anular" title="Anular"></a>';
                              echo '&nbsp;&nbsp;';
                            }
                            echo '</td>';
                            echo '<td>$'. number_format($row["total"],2) . '</td>';
                            echo '<td>'. $row["descripcion"] . '</td>';
                            echo '<td>'. $row["nombre_cliente"] . '</td>';
                            echo '<td>'. $row["dni"] . '</td>';
                            echo '<td>'. $row["direccion"] . '</td>';
                            echo '<td>'. $row["email"] . '</td>';
                            echo '<td>'. $row["telefono"] . '</td>';
                            echo '</tr>';
                          }
                          Database::disconnect();*/?>
                        </tbody>
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

      getVentas();
      $(".filtraTabla").on("change",getVentas);

		});

    function getVentas(){
      let desde=$("#desde").val();
      let hasta=$("#hasta").val();
      let forma_pago=$("#forma_pago").val();
      let tipo_comprobante=$("#tipo_comprobante").val();
      let id_almacen=$("#id_almacen").val();

      let id_perfil="<?=$_SESSION["user"]["id_perfil"]?>";

      let table=$('#dataTables-example666')
      table.DataTable().destroy();
      table.DataTable({
        //dom: 'rtip',
        serverSide: true,
        processing: true,
        ajax:{url:'ajaxListarVentas.php?desde='+desde+'&hasta='+hasta+'&forma_pago='+forma_pago+'&tipo_comprobante='+tipo_comprobante+'&id_almacen='+id_almacen},
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
          {"data": "id_venta"},
          {render: function(data, type, row, meta) {
            return row.fecha_hora+"hs";
          }},
          {render: function(data, type, row, meta) {
            let estado=row.estado;
            let clase="";
            if(estado=="A"){
              clase="badge badge-success";
            }
            if(estado=="R" || estado=="E"){
              clase="badge badge-danger";
            }
            return '<span class="'+clase+'">'+row.tipo_comprobante+'</span>';
          }},
          {"data": "almacen"},
          {"data": "forma_pago"},
          {
            render: function(data, type, row, meta) {
              return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.total_con_descuento);
            },
            className: 'dt-body-right text-right',
          },
          {render: function(data, type, row, meta) {
            let btnVer='<a href="verVenta.php?id='+row.id_venta+'"><img src="img/eye.png" width="24" height="15" border="0" alt="Ver Venta" title="Ver Venta"></a>&nbsp;&nbsp;'
            let btnAnular="";
            console.log(id_perfil);
            console.log(row.id_cierre_caja);
            console.log(row.tipo_comprobante);
            if((id_perfil=="1" || row.id_cierre_caja==0) && row.tipo_comprobante=="Recibo"){
              //btnAnular='<a href="#" data-toggle="modal" data-original-title="Confirmación" data-target="#eliminarModal_'+row["id"]+'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Anular" title="Anular"></a>&nbsp;&nbsp;'
              btnAnular='<a href="#" title="Eliminar" onclick="openModalEliminarVenta('+row.id_venta+')"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar"></a>&nbsp;&nbsp;'
            }
            return btnVer+btnAnular;
          }},
          { render: function(data, type, row, meta) {
              return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.total);
            },
            className: 'dt-body-right text-right',
          },
          {"data": "descuento"},
          {"data": "modalidad_venta"},
          {"data": "nombre_cliente"},
          {"data": "dni"},
          {"data": "direccion"},
          {"data": "email"},
          {"data": "telefono"},
        ],
        /*initComplete: function(settings, json){
          let total_facturas_recibos=json.queryInfo.total_facturas_recibos

          var api = this.api();
          // Update footer
          $(api.column(5).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_facturas_recibos));

          $('[title]').tooltip();
        }*/
        drawCallback: function(settings, json){
          console.log(settings);
          console.log(settings.json.queryInfo.total_facturas_recibos);
          let total_facturas_recibos=settings.json.queryInfo.total_facturas_recibos

          var api = this.api();
          // Update footer
          $(api.column(5).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_facturas_recibos));

          $('[title]').tooltip();
        }
			})
    }
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>
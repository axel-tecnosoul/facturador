<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}?>
<!DOCTYPE html>
<html lang="en">
  <head>
	  <?php include('head_tables.php');?>
    <link rel="stylesheet" type="text/css" href="assets/css/select2.css">
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
    .ver{
      cursor: pointer;
    }
    .modal-dialog{
      overflow-y: initial !important
    }
    .modal-body{
      max-height: 80vh;
      overflow-y: auto;
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
                      <li class="breadcrumb-item">Caja chica</li>
                    </ol>
                  </div>
                </div>
                <!-- Bookmark Start-->
                <div class="col-2">
                  <div class="bookmark pull-right">
                    <ul>
                      <li><a target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?php echo date('d-m-Y');?>"><i data-feather="calendar"></i></a></li>
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
                    <h5>Caja chica
                      &nbsp;<a href="nuevoMovimientoCajaChica.php"><img src="img/import.png" width="24" height="25" border="0" alt="Registrar movimiento de dinero" title="Registrar movimiento de dinero"></a>
                      &nbsp;<button title="Cerrar Caja" class="btn btn-link btn-lg p-0 px-2 border" style="color:#05093f" id="btnCerrarCaja"><i class='fa fa-lock' aria-hidden='true'></i></button>
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <table class="table">
                        <tr>
                          <td class="text-right border-0 p-1">Desde: </td>
                          <td class="border-0 p-1"><input type="date" name="desde" id="desde" value="<?=date("Y-m-d")?>" class="form-control form-control-sm filtraTabla"></td>
                          <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1"></td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                            <label for="forma_pago">Forma de pago:</label>
                            <select name="forma_pago" id="forma_pago" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-selected-text-format="count > 1" multiple><?php
                              include 'database.php';
                              $pdo = Database::connect();
                              $sql = " SELECT id, forma_pago FROM forma_pago";
                              foreach ($pdo->query($sql) as $row) {?>
                                <option value="<?=$row["id"]?>" <?php //if($row["id"]==1) echo "selected"?>><?=$row["forma_pago"]?></option><?php
                              }
                              Database::disconnect();?>
                            </select>
                          </td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                            <label for="id_tipo_motivo">Tipo de motivo:</label>
                            <select name="id_tipo_motivo" id="id_tipo_motivo" class="form-control form-control-sm selectpicker" data-style="multiselect" data-selected-text-format="count > 1" data-actions-box="true" data-live-search="true" multiple><?php
                              $pdo = Database::connect();
                              $sql = " SELECT id, nombre FROM tipos_motivos";
                              foreach ($pdo->query($sql) as $row) {?>
                                <option value="<?=$row["id"]?>"><?=$row["nombre"]?></option><?php
                              }
                              Database::disconnect();?>
                            </select>
                          </td>
                          <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1"></td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                            <label for="motivo">Motivo:</label>
                            <select name="motivo" id="motivo" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-selected-text-format="count > 1" data-actions-box="true" data-live-search="true" multiple><?php
                              $pdo = Database::connect();
                              $sql = " SELECT id, motivo FROM motivos_salidas_caja";
                              foreach ($pdo->query($sql) as $row) {?>
                                <option value="<?=$row["id"]?>"><?=$row["motivo"]?></option><?php
                              }
                              Database::disconnect();?>
                            </select>
                          </td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1">
                            <label for="motivo">Empleado:</label>
                            <select name="id_empleado" id="id_empleado" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-selected-text-format="count > 1" data-actions-box="true" data-live-search="true" multiple>
                              <option value="">- Seleccione -</option><?php
                              $pdo = Database::connect();
                              $sql = " SELECT id, CONCAT(nombre,' ',apellido) AS empleado FROM empleados WHERE 1";
                              foreach ($pdo->query($sql) as $row) {?>
                                <option value="<?=$row["id"]?>"><?=$row["empleado"]?></option><?php
                              }
                              Database::disconnect();?>
                            </select>
                          </td>
                          <td rowspan="2" style="vertical-align: middle;" class="text-right border-0 p-1"><?php
                            if ($_SESSION['user']['id_perfil'] == 1) {
                              echo "";
                            }?>
                            <!-- Tipo comprobante: -->
                          </td>
                          <td rowspan="2" style="vertical-align: middle;" class="border-0 p-1"><?php
                            if ($_SESSION['user']['id_perfil'] == 1) {?>
                              <label for="id_almacen">Almacen: </label>
                              <select name="id_almacen" id="id_almacen" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect">
                                <option value="0">- Todos -</option><?php
                                $pdo = Database::connect();
                                $sql = " SELECT id, almacen FROM almacenes";
                                foreach ($pdo->query($sql) as $row) {?>
                                  <option value="<?=$row["id"]?>"<?php //if($row["id"]==6) echo "selected"?>><?=$row["almacen"]?></option><?php
                                }
                                Database::disconnect();?>
                              </select><?php
                            }else{?>
                              <input type="hidden" id="id_almacen" value="<?=$_SESSION['user']['id_almacen']?>"><?php
                            }?>
                            <!-- <select name="tipo_comprobante" id="tipo_comprobante" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect" data-selected-text-format="count > 1" multiple>
                              <option value="A" selected disabled>Factura A</option>
                              <option value="B" selected>Factura B</option>
                              <option value="R" selected>Recibo</option>
                            </select> -->
                          </td>
                        </tr>
                        <tr>
                          <td class="text-right border-0 p-1">Hasta: </td>
                          <td class="border-0 p-1"><input type="date" name="hasta" id="hasta" value="<?=date("Y-m-d")?>" class="form-control form-control-sm filtraTabla"></td>
                        </tr>
                      </table>
                    </div>
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th>Fecha/Hora</th>
                            <th>Detalle</th>
                            <th>Forma de Pago</th>
                            <th>Credito</th>
                            <th>Débito</th>
                            <th>Saldo</th>
                            <th class="none"></th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                          <tr>
                            <td colspan="3" class="font-weight-bold">Totales</td>
                            <td class="font-weight-bold"></td>
                            <td class="font-weight-bold"></td>
                            <td class="font-weight-bold"></td>
                            <td class="none"></td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>

                  <!-- MODAL VER -->
                  <div class="modal fade" id="modalVer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable" role="document" style="max-width: 800px;">
                      <div class="modal-content">
                        <!-- <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel"></h5>
                          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        </div> -->
                        <div class="modal-body">Ya no podrá anular ventas ni modificar los egresos</div>
                        <!-- <div class="modal-footer">
                          <a href="#" id="btnConfirmCerrarCaja" class="btn btn-primary">Cerrar Caja</a>
                          <button data-dismiss="modal" class="btn btn-light">Volver</button>
                        </div> -->
                      </div>
                    </div>
                  </div>
                  <!-- FIN MODAL VER -->

                  <!-- MODAL CERRAR CAJA -->
                  <div class="modal fade" id="modalCerrarCaja" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel2">¿Está seguro que desea cerrar la caja?</h5>
                          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        </div>
                        <div class="modal-body">Ya no podrá anular ventas ni modificar los egresos</div>
                        <div class="modal-footer">
                          <a href="#" id="btnConfirmCerrarCaja" class="btn btn-primary">Cerrar Caja</a>
                          <button data-dismiss="modal" class="btn btn-light">Volver</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- FIN MODAL CERRAR CAJA -->

                  <!-- MODAL ELEJIR ALMACEN PARA CERRAR CAJA -->
                  <div class="modal fade" id="modalElijaAlmacen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel3" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel3">Atención</h5>
                          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        </div>
                        <div class="modal-body">Por favor seleccione un almacen para cerrar la caja</div>
                        <div class="modal-footer">
                          <button data-dismiss="modal" class="btn btn-primary">OK</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- FIN MODAL ELEJIR ALMACEN PARA CERRAR CAJA -->

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
    <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/dist/js/bootstrap-select.js"></script>
    <script src="vendor/bootstrap-select-1.13.14/js/i18n/defaults-es_ES.js"></script>

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

        getCaja();
        $(".filtraTabla").on("change",getCaja);

        $("#id_tipo_motivo").on("change",function(){
          let id_tipo_motivo=this.value;
          let selectMotivo=$("#id_motivo")
          selectMotivo.find("option").each(function(){
            let disabled=true;
            if(this.value=="" || id_tipo_motivo==this.dataset.idTipoMotivo){
              console.log(this.value);
              disabled=false;
            }
            this.disabled=disabled;
          })
          selectMotivo.val("")
          selectMotivo.select2()

          let id_empleado=$("#id_empleado");
          console.log(id_tipo_motivo);
          console.log(id_empleado);
          if(id_tipo_motivo==12){//12 -> Sueldos
            id_empleado.prop("required",true)
          }else{
            id_empleado.prop("required",false)
          }
          //id_empleado.select2()

          /*let row_select_empleados=$("#row_select_empleados");
          row_select_empleados.find("#id_empleado").val(0).trigger("change")
          if(id_tipo_motivo==12){//12 -> Sueldos
            row_select_empleados.removeClass("d-none")
          }else{
            row_select_empleados.addClass("d-none")
          }*/
        })

        $("#btnCerrarCaja").on("click",function(){
          let id_almacen=$("#id_almacen").val();
          if(id_almacen==0){
            $("#modalElijaAlmacen").modal("show");
          }else{
            $("#modalCerrarCaja").modal("show");
            $("#btnConfirmCerrarCaja").attr("href","cerrarCajaChica.php?id_almacen="+id_almacen)
          }
        })

        $(document).on("click",".ver",function(){
          let id=this.dataset.id;
          let tipo=this.dataset.tipo;
          let url="cardVerMovimientoCajaChica.php?id="+id;
          if(tipo=="venta"){
            url="cardVerVenta.php?id="+id;
          }
          $.ajax({
            type: "POST",
            url: url,
            data: "modal=1",
            //dataType: "json",
            success: function (response) {
              console.log(response);
              let modal=$("#modalVer")
              modal.find(".modal-body").html(response)
              modal.modal("show")

              if(tipo=="venta"){
                $('#tableVentaProductos').DataTable({
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
                  }
                });
              }
            }
          });
        })

      });

      function getCaja(){
        let desde=$("#desde").val();
        let hasta=$("#hasta").val();
        let forma_pago=$("#forma_pago").val();
        let motivo=$("#motivo").val();
        //let tipo_comprobante=$("#tipo_comprobante").val();
        let id_almacen=$("#id_almacen").val();
        let bandera_saldo=saldo=credito=debito=0;
        $('#dataTables-example666').DataTable().destroy();
        $('#dataTables-example666').DataTable({
          stateSave: true,
          dom: 'rtip',
          responsive: true,
          ordering: false,
          //ajax: "listarCajaGetData.php",
          ajax:{url:"listarCajaChicaOLDGetData.php?desde="+desde+"&hasta="+hasta+"&forma_pago="+forma_pago+"&id_almacen="+id_almacen+"&motivo="+motivo,dataSrc:""},
          paginate: false,
          scrollY: '100vh',
          scrollCollapse: true,
          "columns":[
            {"data": "fecha_hora"},
            {"data": "detalle","width": "20%"},
            {"data": "forma_pago"},
            {
              render: function(data, type, row, meta) {
                if(type=="display"){
                  credito+=parseFloat(row.credito);
                }
                if(parseFloat(row.credito)>0){
                  return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.credito);
                }else{
                  return "";
                }
              },
              className: 'dt-body-right text-right',
            },{
              render: function(data, type, row, meta) {
                if(type=="display"){
                  debito+=parseFloat(row.debito);
                }
                if(parseFloat(row.debito)>0){
                  return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.debito);
                }else{
                  return "";
                }
              },
              className: 'dt-body-right text-right',
            },{
              render: function(data, type, row, meta) {
                if(type=="display"){
                  if(bandera_saldo==0){
                    bandera_saldo=1;
                    saldo=parseFloat(row.saldo)
                  }else{
                    saldo+=parseFloat(row.credito)-parseFloat(row.debito);
                    //console.log("saldo: "+saldo);
                  }
                }
                return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(saldo);
              },
              className: 'dt-body-right text-right',
              orderDataType: "num-fmt"
            },{
              "data": "detalle_productos",
              className: 'bg-secondary',
            },
          ],
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
          footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            let saldoFinal=sumaDebito=sumaCredito=0;
            data.forEach(function(reg){
              if(reg.saldo>0){
                saldoFinal=reg.saldo;
              }
              let credito=parseFloat(reg.credito)
              let debito=parseFloat(reg.debito)
              sumaDebito+=debito
              sumaCredito+=credito
              saldoFinal+=credito-debito;
              
            })
            // Update footer
            //console.log($(api.column(3).footer()))
            $(api.column(3).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(sumaCredito));
            $(api.column(4).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(sumaDebito));
            $(api.column(5).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(saldoFinal));
            //console.log(saldo);
          }
        });
      }
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>
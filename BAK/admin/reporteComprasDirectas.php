<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}
include 'database.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_tables.php');?>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select-1.13.14/dist/css/bootstrap-select.min.css">
  </head>
  <style>
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
                      <li class="breadcrumb-item">Compras Directas</li>
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
                    <h5>Compras Directas</h5>
                  </div>
                  <div class="card-body">

                    <!-- VENDIDO -->
                    <div class="row">
                      <div class="col-12">
                        <div class="card">
                          <div class="card-header">
                            <h5>Vendido</h5>
                          </div>
                          <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-4">
                                  <label for="id_proveedor_vendidos">Proveedor: </label>
                                  <select id="id_proveedor_vendidos" class="form-control form-control-sm d-inline w-auto filtraTabla2 selectpicker" data-actions-box="true" data-selected-text-format="count > 1" multiple data-style="multiselect" data-live-search="true"><?php
                                    $pdo = Database::connect();
                                    $sql = " SELECT id, apellido, nombre FROM proveedores";
                                    foreach ($pdo->query($sql) as $row) {
                                      $option="(".$row["id"].") ".$row["apellido"]." ".$row["nombre"]?>
                                      <option value="<?=$row["id"]?>"><?=$option?></option><?php
                                    }
                                    Database::disconnect();?>
                                  </select>
                                </div>
                                <div class="col-4">
                                  <label for="id_almacen_vendidos">Almacen: </label>
                                  <select id="id_almacen_vendidos" class="form-control form-control-sm d-inline w-auto filtraTabla2 selectpicker" data-selected-text-format="count > 1" multiple data-style="multiselect"><?php
                                    $pdo = Database::connect();
                                    $sql = " SELECT id, almacen FROM almacenes";
                                    foreach ($pdo->query($sql) as $row) {?>
                                      <option value="<?=$row["id"]?>"><?=$row["almacen"]?></option><?php
                                    }
                                    Database::disconnect();?>
                                  </select>
                                </div>
                                <div class="col-4">
                                  <label for="id_categoria_vendidos">Categorias: </label>
                                  <select id="id_categoria_vendidos" class="form-control form-control-sm d-inline w-auto filtraTabla2 selectpicker" data-actions-box="true" data-selected-text-format="count > 1" multiple data-style="multiselect" data-live-search="true"><?php
                                    $pdo = Database::connect();
                                    $sql = " SELECT id, categoria FROM categorias";
                                    foreach ($pdo->query($sql) as $row) {?>
                                      <option value="<?=$row["id"]?>"><?=$row["categoria"]?></option><?php
                                    }
                                    Database::disconnect();?>
                                  </select>
                                </div>
                            </div>
                            <div class="dt-ext table-responsive">
                              <table class="display" id="dataTables-example667">
                                <thead>
                                  <tr>
                                    <th>Proveedor</th>
                                    <th>Descripción</th>
                                    <th>Almacen</th>
                                    <th>Precio Venta</th>
                                    <th>Precio Costo</th>
                                    <th>Ganancia</th>
                                    <th class="none">Código</th>
                                    <th class="none">Categoría</th>
                                    <th class="none">ID Venta</th>
                                  </tr>
                                </thead>
                                <tfoot>
                                  <tr>
                                    <th colspan="3">Totales</th>
                                    <th>Precio Venta</th>
                                    <th>Precio Costo</th>
                                    <th>Ganancia</th>
                                    <th colspan="3"></th>
                                  </tr>
                                </tfoot>
                                <tbody></tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- EN STOCK -->
                    <div class="row">
                      <div class="col-12">
                        <div class="card">
                          <div class="card-header">
                            <h5>En stock</h5>
                          </div>
                          <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-4">
                                  <label for="id_proveedor">Proveedor: </label>
                                  <select id="id_proveedor" class="form-control form-control-sm d-inline w-auto filtraTabla selectpicker" data-actions-box="true" data-selected-text-format="count > 1" multiple data-style="multiselect" data-live-search="true"><?php
                                    $pdo = Database::connect();
                                    $sql = " SELECT id, apellido, nombre FROM proveedores";
                                    foreach ($pdo->query($sql) as $row) {
                                      $option="(".$row["id"].") ".$row["apellido"]." ".$row["nombre"]?>
                                      <option value="<?=$row["id"]?>"><?=$option?></option><?php
                                    }
                                    Database::disconnect();?>
                                  </select>
                                </div>
                                <div class="col-4">
                                  <label for="id_almacen">Almacen: </label>
                                  <select id="id_almacen" class="form-control form-control-sm d-inline w-auto filtraTabla selectpicker" data-selected-text-format="count > 1" multiple data-style="multiselect"><?php
                                    $pdo = Database::connect();
                                    $sql = " SELECT id, almacen FROM almacenes";
                                    foreach ($pdo->query($sql) as $row) {?>
                                      <option value="<?=$row["id"]?>"><?=$row["almacen"]?></option><?php
                                    }
                                    Database::disconnect();?>
                                  </select>
                                </div>
                                <div class="col-4">
                                  <label for="id_categoria">Categorias: </label>
                                  <select id="id_categoria" class="form-control form-control-sm d-inline w-auto filtraTabla selectpicker" data-actions-box="true" data-selected-text-format="count > 1" multiple data-style="multiselect" data-live-search="true"><?php
                                    $pdo = Database::connect();
                                    $sql = " SELECT id, categoria FROM categorias";
                                    foreach ($pdo->query($sql) as $row) {?>
                                      <option value="<?=$row["id"]?>"><?=$row["categoria"]?></option><?php
                                    }
                                    Database::disconnect();?>
                                  </select>
                                </div>
                            </div>
                            <div class="dt-ext table-responsive">
                              <table class="display" id="dataTables-example666">
                                <thead>
                                  <tr>
                                    <th>Proveedor</th>
                                    <th>Descripción</th>
                                    <th>Almacen</th>
                                    <th>Precio Venta</th>
                                    <th>Precio Costo</th>
                                    <th>Ganancia</th>
                                    <th class="none">Código</th>
                                    <th class="none">Categoría</th>
                                    <th class="none">Cantidad</th>
                                  </tr>
                                </thead>
                                <tfoot>
                                  <tr>
                                    <th colspan="3">Totales</th>
                                    <th>Precio Venta</th>
                                    <th>Precio Costo</th>
                                    <th>Ganancia</th>
                                    <th colspan="3"></th>
                                  </tr>
                                </tfoot>
                                <tbody></tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
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
		$(document).ready(function() {
      get_compras_directas_stock()
      get_compras_directas_vendidas()

      $(".filtraTabla").on("change",get_compras_directas_stock);
      $(".filtraTabla2").on("change",get_compras_directas_vendidas);
		});

    function get_compras_directas_stock(){
      let id_proveedor=$("#id_proveedor").val();
      let id_almacen=$("#id_almacen").val();
      let id_categoria=$("#id_categoria").val();
      let total_precio=total_precio_costo=total_ganancia=0
      let table=$('#dataTables-example666')
      table.DataTable().destroy();
      table.DataTable({
        'ajax': 'ajaxListarComprasDirectasStock.php?id_proveedor='+id_proveedor+'&id_almacen='+id_almacen+'&id_categoria='+id_categoria,
        dom: 'rtip',
        stateSave: true,
        responsive: true,
        serverSide: true,
        processing: true,
        scrollY: false,
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
            {"data": "proveedor"},
            {"data": "descripcion"},
            {"data": "almacen"},
            {render: function(data, type, row, meta) {
                return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.precio);
              },
              className: 'dt-body-right text-right',
            },{
              render: function(data, type, row, meta) {
                return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.precio_costo);
              },
              className: 'dt-body-right text-right',
            },{
              render: function(data, type, row, meta) {
                //let ganancia=parseFloat(row.precio)-parseFloat(row.precio_costo)
                return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.ganancia);
              },
              className: 'dt-body-right text-right',
            },
            {"data": "codigo"},
            {"data": "categoria"},//,"width": "20%"
            {"data": "cantidad"},
          ],
        initComplete: function(settings, json){
          let total_precio_venta=json.queryInfo.total_precio_venta
          let total_precio_costo=json.queryInfo.total_precio_costo
          let total_ganancia=json.queryInfo.total_ganancia

          var api = this.api();
          // Update footer
          $(api.column(3).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_precio_venta));
          $(api.column(4).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_precio_costo));
          $(api.column(5).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_ganancia));

          $('[title]').tooltip();

        },
      });
    }

    function get_compras_directas_vendidas(){
      let id_proveedor=$("#id_proveedor_vendidos").val();
      let id_almacen=$("#id_almacen_vendidos").val();
      let id_categoria=$("#id_categoria_vendidos").val();
      let total_precio=total_precio_costo=total_ganancia=0
      let table=$('#dataTables-example667')
      table.DataTable().destroy();
      table.DataTable({
        'ajax': 'ajaxListarComprasDirectasVendidas.php?id_proveedor='+id_proveedor+'&id_almacen='+id_almacen+'&id_categoria='+id_categoria,
        dom: 'rtip',
        stateSave: true,
        responsive: true,
        serverSide: true,
        processing: true,
        scrollY: false,
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
          {"data": "proveedor"},
          {"data": "descripcion"},
          {"data": "almacen"},
          {render: function(data, type, row, meta) {
              return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.precio);
            },
            className: 'dt-body-right text-right',
          },{
            render: function(data, type, row, meta) {
              return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.precio_costo);
            },
            className: 'dt-body-right text-right',
          },{
            render: function(data, type, row, meta) {
              //let ganancia=parseFloat(row.precio)-parseFloat(row.precio_costo)
              return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.ganancia);
            },
            className: 'dt-body-right text-right',
          },
          {"data": "codigo"},
          {"data": "categoria"},//,"width": "20%"
          {"data": "id_venta"},
        ],
        initComplete: function(settings, json){
          let total_precio_venta=json.queryInfo.total_precio_venta
          let total_precio_costo=json.queryInfo.total_precio_costo
          let total_ganancia=json.queryInfo.total_ganancia

          var api = this.api();
          // Update footer
          $(api.column(3).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_precio_venta));
          $(api.column(4).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_precio_costo));
          $(api.column(5).footer()).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total_ganancia));

          $('[title]').tooltip();

        },
      });
    }
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>
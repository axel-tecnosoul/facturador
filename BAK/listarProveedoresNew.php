<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}
include("database.php");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	  <?php include('head_tables.php');?>
    <style>
      #overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Color semitransparente de fondo */
        z-index: 9999; /* Asegura que el overlay esté en el frente */
        display: none; /* Inicialmente oculto */
      }
    </style>
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
                      <li class="breadcrumb-item">Proveedores</li>
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
                    <h5>Proveedores&nbsp;<a href="nuevoProveedorAdmin.php"><img src="img/icon_alta.png" width="24" height="25" border="0" alt="Nuevo" title="Nuevo"></a>&nbsp;<a href="exportProveedores.php"><img src="img/xls.png" width="24" height="25" border="0" alt="Exportar" title="Exportar"></a></h5><span>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <table class="table">
                        <tr>
                          <td class="border-0 p-1" style="text-align: right;"><label for="id_almacen">Almacen:</label></td>
                          <td class="border-0 p-1">
                            <select id="id_almacen" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect">
                              <option value="0">- Todos -</option><?php
                              $pdo = Database::connect();
                              $sql = " SELECT id, almacen FROM almacenes";
                              foreach ($pdo->query($sql) as $row) {?>
                                <option value="<?=$row["id"]?>"><?=$row["almacen"]?></option><?php
                              }
                              Database::disconnect();?>
                            </select>
                          </td>
                          <td class="border-0 p-1" style="text-align: right;"><label for="id_modalidad">Modalidad:</label></td>
                          <td class="border-0 p-1">
                            <select id="id_modalidad" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect">
                              <option value="0">- Todos -</option><?php
                              $pdo = Database::connect();
                              $sql = " SELECT id, modalidad FROM modalidades";
                              foreach ($pdo->query($sql) as $row) {?>
                                <option value="<?=$row["id"]?>"><?=$row["modalidad"]?></option><?php
                              }
                              Database::disconnect();?>
                            </select>
                          </td>
                        </tr>
                      </table>
                    </div>
                    <div class="dt-ext table-responsive">
                      <div id="overlay"></div>
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>DNI</th>
                            <th>Nombre y apellido</th>
                            <!-- <th>Apellido</th> -->
                            <th>Ventas por pesos</th>
                            <th>Ventas por canje</th>
                            <th>En stock</th>
                            <th>Activo</th>
                            <th>Opciones</th>
                            <th class="none">Almacén</th>
                            <th class="none">Modalidad</th>
                            <th class="none">E-Mail</th>
                            <th class="none">Teléfono</th>
                            <th class="none">Crédito</th>
                            <th class="none">Fecha Alta</th>
                          </tr>
                        </thead>
                        <tbody><?php
                          /*$pdo = Database::connect();
                          $sql = " SELECT p.id, p.dni, CONCAT(p.nombre,' ',p.apellido) AS proveedor, p.email, p.activo, date_format(fecha_alta,'%d/%m/%Y') AS fecha_alta, p.telefono, p.credito, a.almacen, m.modalidad FROM proveedores p left join almacenes a on a.id = id_almacen left join modalidades m on m.id = id_modalidad WHERE 1 ";
                          
                          foreach ($pdo->query($sql) as $row) {
                            echo '<tr>';
                            echo '<td>'. $row["id"] . '</td>';
                            echo '<td>'. $row["dni"] . '</td>';
                            echo '<td>'. $row["proveedor"] . '</td>';
                            //echo '<td>'. $row["apellido"] . '</td>';
                            echo '<td>'. 0 . '</td>';
                            echo '<td>'. 0 . '</td>';
                            echo '<td>'. 0 . '</td>';
                            if ($row["activo"] == 1) {
                              echo '<td>Si</td>';
                            } else {
                              echo '<td>No</td>';
                            }
                            echo '<td>';
                            echo '<a href="modificarProveedor.php?id='.$row["id"].'"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar" title="Modificar"></a>';
                            echo '&nbsp;&nbsp;';
                            echo '<a href="#" class="btnEliminar" data-id="'.$row["id"].'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar" title="Eliminar"></a>';
                            echo '&nbsp;&nbsp;';
                            echo '<a href="verProveedor.php?id='.$row["id"].'"><img src="img/eye.png" width="30" border="0" alt="Ver Proveedor" title="Ver Operaciones"></a>';
                            echo '&nbsp;&nbsp;';
                            echo '</td>';
                            echo '<td class="none">'. $row["almacen"] . '</td>';
                            echo '<td class="none">'. $row["modalidad"] . '</td>';
                            echo '<td class="none">'. $row["email"] . '</td>';
                            echo '<td class="none">'. $row["telefono"] . '</td>';
                            echo '<td class="none">$'. number_format($row["credito"],2) . '</td>';
                            echo '<td class="none">'. $row["fecha_alta"] . '</td>';
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
          <div class="modal-body">¿Está seguro que desea eliminar el proveedor?</div>
          <div class="modal-footer">
            <a id="btnEliminarProveedor" class="btn btn-primary">Eliminar</a>
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
        $(".btnEliminar").on("click",function(){
          $("#btnEliminarProveedor").attr("href","eliminarProveedor.php?id="+this.dataset.id);
          $("#eliminarModal").modal("show")
        })

        $(document).ready(function() {
        getProveedores();
        $(".filtraTabla").on("change",getProveedores);
      });

      function getProveedores(){
        let id_almacen=$("#id_almacen").val();
        let id_modalidad=$("#id_modalidad").val();
        let id_perfil="<?=$_SESSION["user"]["id_perfil"]?>";

        //document.getElementById("overlay").style.display = "block";
        let table=$('#dataTables-example666')
        table.DataTable().destroy();
        table.DataTable({ 
          ajax:{url:'ajaxProveedores.php?id_almacen='+id_almacen+'&id_modalidad='+id_modalidad},
				  //stateSave: true,
          serverSide: true,
          processing: true,
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
            {"data": "dni"},
            {"data": "proveedor"},
            {
              render: function(data, type, row, meta) {
                if(type=="display"){
                  return new Intl.NumberFormat('es-AR', {useGrouping: true, minimumFractionDigits: 0, maximumFractionDigits: 0}).format(row.ventasPesos);
                }else{
                  return row.ventasPesos;
                  //return moment(full.fecha_hora_subida).format('DD MMM YYYY HH:mm');
                }
              },
              className: 'dt-body-right text-right',
            },
            {
              render: function(data, type, row, meta) {
                if(type=="display"){
                  return new Intl.NumberFormat('es-AR', {useGrouping: true, minimumFractionDigits: 0, maximumFractionDigits: 0}).format(row.ventasCanjes);
                }else{
                  return row.ventasCanjes;
                  //return moment(full.fecha_hora_subida).format('DD MMM YYYY HH:mm');
                }
              },
              className: 'dt-body-right text-right',
            },
            {
              render: function(data, type, row, meta) {
                if(type=="display"){
                  //return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.enStock);
                  return new Intl.NumberFormat('es-AR', {useGrouping: true, minimumFractionDigits: 0, maximumFractionDigits: 0}).format(row.enStock);

                }else{
                  return row.enStock;
                  //return moment(full.fecha_hora_subida).format('DD MMM YYYY HH:mm');
                }
              },
              className: 'dt-body-right text-right',
            },
            {"data": "activo"},
            {"data": "acciones"},
            {"data": "almacen"},
            {"data": "modalidad"},
            {"data": "email"},
            {"data": "telefono"},
            {
              render: function(data, type, row, meta) {
                if(type=="display"){
                  return new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(row.credito);
                }else{
                  return row.credito;
                  //return moment(full.fecha_hora_subida).format('DD MMM YYYY HH:mm');
                }
              },
              className: 'dt-body-right text-right',
            },
            {render: function(data, type, row, meta) {
              if(type=="display"){
                return row.fecha_alta_formatted;
              }else{
                return row.fecha_alta;
                //return moment(full.fecha_hora_subida).format('DD MMM YYYY HH:mm');
              }
            }}
          ],
          columnDefs: [
            //{ targets: [0], visible: false},
            { targets: [1], type: 'datetime'},
          ],
          drawCallback: function(settings, json){
            $('[title]').tooltip();
            //document.getElementById("overlay").style.display = "none";
          }
        })
      };

        $('#dataTables-example666').DataTable({
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
    <!-- Plugin used-->
  </body>
</html>
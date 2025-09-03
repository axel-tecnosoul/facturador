<?php 
session_start(); 
if(empty($_SESSION['user'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}
include("database.php");?>
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
                      <li class="breadcrumb-item">Clientes</li>
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
                    <h5>Clientes&nbsp;<a href="nuevoCliente.php"><img src="img/icon_alta.png" width="24" height="25" border="0" alt="Nuevo" title="Nuevo"></a></h5><span>
                  </div>
                  <div class="card-body">
                    <div class="row d-none">
                      <table class="table">
                        <tr>
                          <td class="border-0 p-1" style="text-align: right;vertical-align: middle;"><label for="id_almacen">Almacen:</label></td>
                          <td class="border-0 p-1" style="vertical-align: middle;">
                            <select id="id_almacen" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect">
                              <option value="0">- Todos -</option>
                            </select>
                          </td>
                          <td class="border-0 p-1" style="text-align: right;vertical-align: middle;"><label for="id_modalidad">Modalidad:</label></td>
                          <td class="border-0 p-1" style="vertical-align: middle;">
                            <select id="id_modalidad" class="form-control form-control-sm filtraTabla selectpicker" data-style="multiselect">
                              <option value="0">- Todos -</option>
                            </select>
                          </td>
                          <td style="vertical-align: middle;" class="border-0 p-1">
                            <label class="d-block" for="checkbox-activas">
                              <input class="checkbox_animated filtraTabla" value="1" checked required id="checkbox-activas" type="checkbox" name="activa[]">
                              <label for="checkbox-activas">Activas</label>
                            </label>

                            <label class="d-block" for="checkbox-inactivas">
                              <input class="checkbox_animated filtraTabla" value="0" required id="checkbox-inactivas" type="checkbox" name="activa[]">
                              <label for="checkbox-inactivas">Inactivas</label>
                            </label>
                          </td>
                        </tr>
                      </table>
                      <p id="aclaracion" class="font-italic small">Para buscar por ID introduzca "id:" Ej: id:1144</p>
                    </div>
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Razon Socil</th>
                            <th>CUIT</th>
                            <th>E-Mail</th>
                            <th>Teléfono</th>
                            <th>Direccion</th>
                            <th>Precio Base</th>
                            <th>Opciones</th>
                          </tr>
                        </thead>
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
          <div class="modal-body">¿Está seguro que desea eliminar el cliente?</div>
          <div class="modal-footer">
            <a id="btnEliminarCliente" class="btn btn-primary">Eliminar</a>
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
      var table=$('#dataTables-example666')

      $(document).ready(function() {

        $(document).on("click",".btnEliminar",function(){
          console.log(this);
          $("#btnEliminarCliente").attr("href","eliminarCliente.php?id="+this.dataset.id);
          console.log($("#btnEliminarCliente"));
          $("#eliminarModal").modal("show")
        })

        //getClientees();
        $(".filtraTabla").on("change",function(){
          table.DataTable().ajax.reload(); // Se vuelve a cargar los datos del servidor sin recargar la página
        });

        table.DataTable({
          ajax:{
            //url:'ajaxClientees.php?id_almacen='+id_almacen+'&id_modalidad='+id_modalidad+'&activo='+activo
            url:'ajaxClientes.php',dataSrc:""
          },
          //stateSave: true,
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
            {"data": "razon_social"},
            {"data": "cuit"},
            {"data": "email"},
            {"data": "telefono"},
            {"data": "direccion"},
            {"data": "precio_base"},
            {"data": "acciones"},
          ],
          drawCallback: function(settings, json){
            $('[title]').tooltip();
          }
        });
      })
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>
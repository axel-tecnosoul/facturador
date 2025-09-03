<?php 
session_start(); 
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
                      <li class="breadcrumb-item">Productos</li>
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
                    <h5>Productos
                      &nbsp;
                      <a href="nuevoProducto.php"><img src="img/icon_alta.png" width="24" height="25" border="0" alt="Nuevo" title="Nuevo"></a>
                      &nbsp;
                      <a href="#"><img src="img/pdf.png" width="24" height="25" border="0" alt="Etiquetar Productos Seleccionados" id="etiquetado-masivo" title="Etiquetar Productos Seleccionados"></a>
                      &nbsp;
                      <a href="importProductos.php"><img src="img/import.png" width="24" height="25" border="0" alt="Importar Productos" title="Importar Productos"></a>
                      &nbsp;
                      <a href="exportProductos.php"><img src="img/xls.png" width="24" height="25" border="0" alt="Exportar" title="Exportar"></a>
                      &nbsp;
                      <a href="cargaMasivaProductos.php" title="Carga masiva"><img src="img/table_plus_icon.png" width="24" height="25" border="0" alt="Carga masiva"></a>
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Proveedor</th>
                            <th>Precio</th>
                            <th>Activo</th>
                            <th>Opciones</th>
                          </tr>
                        </thead>
                        <tbody><?php 
                          include 'database.php';
                          /*$pdo = Database::connect();
                          $sql = " SELECT p.`id`, p.`codigo`, c.`categoria`, p.`descripcion`, pr.`nombre`, pr.`apellido`, p.`precio`, p.`activo`,p.cb FROM `productos` p inner join categorias c on c.id = p.id_categoria inner join proveedores pr on pr.id = p.id_proveedor WHERE 1 ";
                          
                          foreach ($pdo->query($sql) as $row) {
                            echo '<tr>';
                            echo '<td>'. $row[8] . '</td>';
                            echo '<td>'. $row[1] . '</td>';
                            echo '<td>'. $row[2] . '</td>';
                            echo '<td>'. $row[3] . '</td>';
                            echo '<td>'. $row[4] .' '.$row[5]. '</td>';
                            echo '<td>$'. number_format($row[6],2) . '</td>';
                            if ($row[7] == 1) {
                              echo '<td>Si</td>';
                            } else {
                              echo '<td>No</td>';
                            }
                            echo '<td>';
                              echo '<a href="modificarProducto.php?id='.$row[0].'"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Modificar" title="Modificar"></a>';
                              echo '&nbsp;&nbsp;';
                              echo '<a href="etiquetarProducto.php?cb='.$row[8].'"><img src="img/pdf.png" width="24" height="25" border="0" alt="Etiquetar" title="Etiquetar"></a>';
                              echo '&nbsp;&nbsp;';
                              echo '<a href="#" data-toggle="modal" data-original-title="Confirmación" data-target="#eliminarModal_'.$row[0].'"><img src="img/icon_baja.png" width="24" height="25" border="0" alt="Eliminar" title="Eliminar"></a>';
                              echo '&nbsp;&nbsp;';
                            echo '</td>';
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
          <div class="modal-body">¿Está seguro que desea eliminar el producto?</div>
          <div class="modal-footer">
            <a id="btnEliminarProducto" class="btn btn-primary">Eliminar</a>
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
        $('#dataTables-example666').DataTable({
          'ajax': 'ajaxListarProductos.php',
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
          "columnDefs": [
            {
              "targets": [0],
              "searchable": false,
              "orderable": false,
            }
          ],
          order: [[1, 'desc']],
          initComplete: function(){
            $('[title]').tooltip();
          }
        });
      });

      function openModalEliminarContacto(idProducto){
        $('#eliminarModal').modal("show");
        document.getElementById("btnEliminarProducto").href="eliminarProducto.php?id="+idProducto;
      }

      $('.customer-selector').on('click', function () {
        $('.toggle-checkboxes').prop('checked', false);
      });

      $('#etiquetado-masivo').on('click', function (e) {
        e.preventDefault();
        if ($('.customer-selector:checked').length < 1) {
          alert("Debe seleccionar un producto como mínimo");
        } else {
          var arr = [];
          $('.customer-selector:checked').each(function (i,o) { arr.push($(o).val()); });
          //window.location.href=window.location.href.replace("listarProductos.php", "etiquetarMasivo.php?id=" + arr.join(",") );
          window.open("etiquetarMasivo.php?id=" + arr.join(","), '_blank');
        }
      });

      var toggle = true;
      $('.toggle-checkboxes').on('click', function (e) {
        e.preventDefault();
        $('.customer-selector').prop('checked', toggle);
        toggle = !toggle;
      })
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>
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
                      <li class="breadcrumb-item">Stock</li>
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
                    <h5>Stock
					&nbsp;<a href="nuevaCompra.php"><img src="img/icon_alta.png" width="24" height="25" border="0" alt="Ingresar Stock" title="Ingresar Stock"></a>
					&nbsp;<a href="nuevoMovimientoStock.php"><img src="img/import.png" width="24" height="25" border="0" alt="Movimientos Entre Almacenes" title="Movimientos Entre Almacenes"></a>
					&nbsp;<a href="exportStock.php"><img src="img/xls.png" width="24" height="25" border="0" alt="Exportar Stock" title="Exportar Stock"></a>
					</h5>
                  </div>
                  <div class="card-body">
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Proveedor</th>
                            <th>Almacen</th>
                            <th>Activo</th>
                            <th>Modalidad</th>
                            <th>Cantidad</th>
                            <th>Opciones</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Proveedor</th>
                            <th>Almacen</th>
                            <th>Activo</th>
                            <th>Modalidad</th>
                            <th>Cantidad</th>
                            <th>Opciones</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          <?php 
							include 'database.php';
							$pdo = Database::connect();
							$sql = " SELECT s.id, p.codigo, c.categoria, p.descripcion, pr.nombre, pr.apellido, a.almacen, s.cantidad, m.modalidad, p.precio,p.activo FROM stock s inner join productos p on p.id = s.id_producto inner join almacenes a on a.id = s.id_almacen left join modalidades m on m.id = s.id_modalidad left join categorias c on c.id = p.id_categoria left join proveedores pr on pr.id = p.id_proveedor WHERE s.cantidad > 0 ";
							if ($_SESSION['user']['id_perfil'] == 2) {
								$sql .= " and a.id = ".$_SESSION['user']['id_almacen'];
							}
              //echo $sql;
							foreach ($pdo->query($sql) as $row) {
								echo '<tr>';
								echo '<td>'. $row["id"] . '</td>';
								echo '<td>'. $row["codigo"] . '</td>';
								echo '<td>'. $row["categoria"] . '</td>';
								echo '<td>'. $row["descripcion"] . '</td>';
                echo '<td>$'. number_format($row["precio"],2). '</td>';
								echo '<td>'. $row["nombre"] .' '.$row["apellido"]. '</td>';
								echo '<td>'. $row["almacen"] . '</td>';
                if ($row["activo"] == 1) {
                  echo '<td>Si</td>';
                } else {
                  echo '<td>No</td>';
                }
								echo '<td>'. $row["modalidad"] . '</td>';
								echo '<td>'. $row["cantidad"] . '</td>';
								echo '<td>';
								echo '<a href="modificarStock.php?id='.$row["id"].'"><img src="img/icon_modificar.png" width="24" height="25" border="0" alt="Ajustar Cantidad" title="Ajustar Cantidad"></a>';
								echo '</td>';
								echo '</tr>';
						   }
						   Database::disconnect();
						  ?>
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
			let table=$('#dataTables-example666')
      table.DataTable({
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
        initComplete: function(){
          this.api().columns.adjust().draw();//Columns sin parentesis
          this.api().columns().every(function(){//Columns() con parentesis
            var column=this;
            if(column.footer().innerHTML!="Precio"){
              var select=$("<select class=' form-control form-control-sm'><option value=''>Todos</option></select>")
                .appendTo($(column.footer()).empty())
                .on("change",function(){
                  var val=$.fn.dataTable.util.escapeRegex(
                    $(this).val()
                  );
                  column.search(val ? '^'+val+'$':'',true,false).draw();
                });
              column.data().unique().sort().each(function(d,j){
                var val=$("<div/>").html(d).text();
                if(column.search()==='^'+val+'$'){
                  select.append("<option value='"+val+"' selected='selected'>"+val+"</option>");
                }else{
                  select.append("<option value='"+val+"'>"+val+"</option>");
                }
              })
            }else{
              getTotalStock(table)
            }
          })
        }
			}).on( 'search.dt', function () {
        getTotalStock(table)
      } );
		});

    function getTotalStock(table){
      let total=0;
      table=table.DataTable()
      table.rows( {order:'index', search:'applied'} ).nodes().each(function(d){
        
        var val=$(d).find(":nth-child(5)").html();
        let precio=Number(val.replace(/[^0-9.-]+/g,""));

        var val2=$(d).find(":nth-child(10)").html();
        let cantidad=Number(val2.replace(/[^0-9.-]+/g,""));
        total+=(precio*cantidad);
      })
      let column_a_cobrar=table.columns(4).footer()
      $(column_a_cobrar).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total));
    }
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>
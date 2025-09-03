<?php 
session_start(); 
if(empty($_SESSION['proveedor'])){
	header("Location: index.php");
	die("Redirecting to index.php"); 
}?>
<!DOCTYPE html>
<html lang="en">
  <head>
	  <?php include('head_tables.php');?>
  </head>
  <body class="light-only">
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
      <!-- Page Header Start-->
      <?php include('header_proveedor.php');?>
     
      <!-- Page Header Ends                              -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <?php include('menu_proveedor.php');?>
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
                      <li class="breadcrumb-item">Mis Ventas</li>
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
                    <h5>Mis Ventas
					            &nbsp;<a href="exportOperacionesProveedor.php"><img src="img/xls.png" width="24" height="25" border="0" alt="Exportar Mis Ventas" title="Exportar Mis Ventas"></a>
					          </h5>
                  </div>
                  <div class="card-body">
                    <div class="dt-ext table-responsive">
                      <table class="display" id="dataTables-example666">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Almacen</th>
                            <th>Fecha/Hora</th>
                            <th>Código</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <!-- <th>Precio</th>
                            <th>Subtotal</th> -->
                            <th>A cobrar</th>
                            <th>Pagado</th>
                            <th>Modalidad</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th>ID</th>
                            <th>Almacen</th>
                            <th>Fecha/Hora</th>
                            <th>Código</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <!-- <th>Precio</th>
                            <th>Subtotal</th> -->
                            <th>A cobrar</th>
                            <th>Pagado</th>
                            <th>Modalidad</th>
                          </tr>
                        </tfoot>
                        <tbody><?php 
                          include 'database.php';
                          $pdo = Database::connect();
                          $sql = " SELECT v.id, a.almacen, date_format(v.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora, p.codigo, c.categoria, p.descripcion, vd.cantidad, vd.precio, vd.subtotal, m.modalidad, IF(vd.pagado=1,'Si','NO') AS pagado,vd.deuda_proveedor FROM ventas_detalle vd inner join ventas v on v.id = vd.id_venta inner join almacenes a on a.id = v.id_almacen inner join productos p on p.id = vd.id_producto inner join categorias c on c.id = p.id_categoria inner join modalidades m on m.id = vd.id_modalidad LEFT JOIN devoluciones_detalle de ON de.id_venta_detalle=vd.id WHERE v.anulada = 0 AND id_venta_cbte_relacionado IS NULL AND de.id_devolucion IS NULL and p.id_proveedor = ".$_SESSION['proveedor']['id'];
                          
                          foreach ($pdo->query($sql) as $row) {
                            //$row["deuda_proveedor"]=1120.50;
                            echo '<tr>';
                            echo '<td>V# '.$row["id"].'</td>';
                            echo '<td>'.$row["almacen"].'</td>';
                            echo '<td>'.$row["fecha_hora"].'hs</td>';
                            echo '<td>'.$row["codigo"].'</td>';
                            echo '<td>'.$row["categoria"].'</td>';
                            echo '<td>'.$row["descripcion"].'</td>';
                            echo '<td>'.$row["cantidad"].'</td>';
                            /*echo '<td>$'.number_format($row["precio"],2).'</td>';
                            echo '<td>$'.number_format($row["subtotal"],2).'</td>';*/
                            echo '<td>$'.number_format($row["deuda_proveedor"],2).'</td>';
                            echo '<td>'.$row["pagado"].'</td>';
                            echo '<td>'.$row["modalidad"].'</td>';
                            /*if ($row["pagado"] == 1) {
                              echo '<td>Si</td>';	
                            } else {
                              echo '<td>No</td>';	
                            }*/
                            echo '</tr>';
                          }

                          $sql = " SELECT c.id, date_format(c.fecha_hora,'%d/%m/%Y %H:%i') AS fecha_hora, a.almacen, c.total,cd.cantidad,cd.subtotal, cd.deuda_proveedor, IF(cd.pagado=1,'Si','NO') AS pagado ,c2.categoria,p.codigo,p.descripcion, m.modalidad FROM canjes c INNER JOIN canjes_detalle cd ON cd.id_canje=c.id INNER JOIN productos p ON cd.id_producto=p.id INNER JOIN proveedores pr ON p.id_proveedor=pr.id INNER JOIN categorias c2 ON p.id_categoria=c2.id INNER JOIN almacenes a on a.id = c.id_almacen inner join modalidades m on m.id = cd.id_modalidad LEFT JOIN devoluciones_detalle de ON de.id_canje_detalle=cd.id WHERE anulado = 0 AND de.id_devolucion IS NULL AND p.id_proveedor = ".$_SESSION['proveedor']['id'];
                          //echo $sql;
                        
                          foreach ($pdo->query($sql) as $row) {
                            echo '<tr>';
                            echo '<td>C# '.$row["id"].'</td>';
                            echo '<td>'.$row["almacen"].'</td>';
                            echo '<td>'.$row["fecha_hora"].'hs</td>';
                            echo '<td>'.$row["codigo"].'</td>';
                            echo '<td>'.$row["categoria"].'</td>';
                            echo '<td>'.$row["descripcion"].'</td>';
                            echo '<td>'.$row["cantidad"].'</td>';
                            /*echo '<td>$'.number_format($row["precio"],2).'</td>';
                            echo '<td>$'.number_format($row["subtotal"],2).'</td>';*/
                            echo '<td>$'.number_format($row["deuda_proveedor"],2).'</td>';
                            echo '<td>'.$row["pagado"].'</td>';
                            echo '<td>'.$row["modalidad"].'</td>';
                            //echo '<td>';
                            //echo '</td>';
                            echo '</tr>';
                          }
                          Database::disconnect();?>
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
        filter:function(){
          console.log("filtrando");
          this.api().columns().every(function(){//Columns() con parentesis
            var column=this;
            if(column.footer().innerHTML=="A cobrar"){
              getTotalACobrar(column)
            }
          });
        },
        initComplete: function(){
          this.api().columns.adjust().draw();//Columns sin parentesis
          this.api().columns().every(function(){//Columns() con parentesis
            var column=this;
            if(column.footer().innerHTML!="A cobrar"){
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
              getTotalACobrar(table)
            }
          })
        }
			}).on( 'search.dt', function () {
        getTotalACobrar(table)
      } );

      function getTotalACobrar(table){
        let total=0;
        table=table.DataTable()
        table.rows( {order:'index', search:'applied'} ).nodes().each(function(d){
          var val=$(d).find(":nth-child(8)").html();
          let number=Number(val.replace(/[^0-9.-]+/g,""));
          console.log(number);
          total+=number;
        })
        let column_a_cobrar=table.columns(7).footer()
        $(column_a_cobrar).html(new Intl.NumberFormat('es-AR', {currency: 'ARS', style: 'currency'}).format(total));
      }
		});
		
		</script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"></script>
    <!-- Plugin used-->
  </body>
</html>